<?php

$output = '';

include_once "connect.php";

$uh = $conn->real_escape_string($_GET['uh']); // the user's link
$gh = $conn->real_escape_string($_GET['gh']); // the group's link

// number of posts to already loaded (default = 20)
$np = 20;
if ($_GET['np']) { $np = $_GET['np']; }

if($_GET['f']) { $np = 0; }

// number of posts to return (default=20)
$ntl = 20;
if($_GET['ntl']) { $ntl = $_GET['ntl']; }

// function to return the the title of a webpage given a URL
// needed because link's can have dynamic titles
// Credit: http://stackoverflow.com/questions/4348912/get-title-of-website-via-link
function get_title($url){
  $str = file_get_contents($url);
  if(strlen($str)>0){
    $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
    preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
    return $title[1];
  }
}

// Function to convert a mysql Date object into a more different format (given the date)
// Result: 2016-12-29 18:13:01 --> Tomorrow at 6:13pm or 2016-12-30 02:30:00 --> Today at 2:30am
function timeToWords($d) {
  // convert the date into a unix timestamp (seconds)
  $d = strtotime($d);

  // create a time for 'today' at midnight (e.g. beginning of the day within no hours and seconds)
  $currentDay = new Datetime('today', new Datetimezone('Australia/Sydney'));
  $currentDay = strtotime($currentDay->format('Y-m-d H:i:s') . PHP_EOL);

  // if the date is more than two days from the current morning (e.g. not today or tomorrow)
  // or if the date is reverse of that (not yesterday)
  if($d > $currentDay + (2*24*60*60) || $d < $currentDay - (24*60*60)) {
    // if the year is not the same
    if(date("Y",$d) != date("Y", $currentDay)) {
      return date("d M Y",$d);
    }
    return date("j M",$d);

    // the date is either yesterday, today or tomorrow
    // if the day has the same date as today
  } else if(date("Y-m-d", $d) == date("Y-m-d", $currentDay)) {
    return "Today ".date("g:ia",$d);

  // the date is either yesterday or today
  // if the date is greater than today (tomorrow)
  } else if($d > $currentDay) {
    return "Tomorrow ".date("g:ia", $d);
  }
  // the date can only be yesterday
  return "Yesterday ".date("g:ia", $d);
}


// Function to return the time left before assignment
function assignmentTime($d) {
  // convert the date into a unix timestamp (seconds)
  $d = strtotime($d);

  // create a time for 'today' at midnight (e.g. beginning of the day within no hours and seconds)
  $currentDay = new Datetime('today', new Datetimezone('Australia/Sydney'));
  $currentDay = strtotime($currentDay->format('Y-m-d H:i:s') . PHP_EOL);

  // if the assignment is late
  if($d < $currentDay) {
    return "Assignment Late";

  // not late
  } else {
    // if the date is today or tomorrow
    if($d > $currentDay + (2*24*60*60)) {
      // if the year is not the same
      if(date("Y",$d) != date("Y", $currentDay)) {
        return "Due ".date("d M Y",$d);
      }
      return "Due ".date("j M",$d);

    // if the day has the same date as today
    } else if(date("Y-m-d", $d) == date("Y-m-d", $currentDay)) {

      // create a time that has more detail
      $currentDay = new Datetime('now', new Datetimezone('Australia/Sydney'));
      $currentDay = strtotime($currentDay->format('Y-m-d H:i:s') . PHP_EOL);

      // get the hours they were due
      $curH = date('G',$currentDay);
      $assH = date('G',$d);

      // assignment is more than an hour away
      if($assH - $curH > 0) {
        return "Due in ".($assH-$curH)." hours";
      }

      // assignment is minutes away
      $curM = intval(date('i',$currentDay));
      $assM = intval(date('i',$d));

      // return minutes
      return "Due in ".($assM-$curM)." minutes";
    }
    return "Due Tomorrow";
  }
}


if($uh) {

  $userIDSQL = "SELECT userID, photo FROM userInfo WHERE link='{$uh}' LIMIT 1;";
  $userInfo = $conn->query($userIDSQL)->fetch_assoc();
  $userID = $userInfo['userID'];
  $userPhoto = $userInfo['photo'];

  $postsSQL = '';
  $attachmentsSQL = '';

  // the request is coming from to collect from only a specific group
  if($gh) {
    // get any posts for the user
    $postsSQL = "SELECT * FROM
                  (SELECT p.date, p.due, p.text, p.type, p.aName, p.postID, p.postHash, ui.userID, ui.prefix, ui.firstName, ui.lastName, ui.photo, ui.link, g.groupName, g.groupLink FROM
                    (posts AS p RIGHT JOIN userInfo AS ui ON p.`userID` = ui.`userID`)
                    RIGHT JOIN groups AS g ON p.`groupID` = g.`groupID`
                  WHERE p.`groupID` = (SELECT groupID FROM groups WHERE groupLink='{$gh}')
                  ORDER BY p.date DESC
                  LIMIT {$ntl} OFFSET {$np})
                AS t;";

    // find any attachments to add to these posts
    $attachmentsSQL = "SELECT a.postID, f.fileName, f.fileIntUrl, f.fileExtUrl, f.fileType
                        FROM
                          attachments AS a
                          RIGHT JOIN files AS f ON f.fileID = a.fileID
                        WHERE a.postID IN
                          (SELECT * FROM
                            (SELECT p.postID FROM posts AS p
                              WHERE p.groupID = (SELECT groupID FROM groups WHERE groupLink='{$gh}')
                            ORDER BY p.date DESC
                            LIMIT {$ntl} OFFSET {$np})
                          AS t)
                        ORDER BY a.postID;";

    // find the comments attached to each post shown
    $commentSQL = "SELECT c.postID, c.commentText, c.date, ui.firstName, ui.lastName, ui.prefix,
                          ui.link AS userLink, ui.photo AS userPhoto, u.type
                    FROM
                    	(comments AS c RIGHT JOIN userInfo AS ui ON c.userID=ui.userID)
                    	RIGHT JOIN users As u ON ui.userID = u.userID
                    WHERE c.postID IN
                    	(SELECT * FROM
                    		(SELECT p.postID FROM posts AS p
                    			WHERE p.groupID = (SELECT groupID FROM groups WHERE groupLink='{$gh}')
                    			ORDER BY p.date DESC
                    			LIMIT {$ntl} OFFSET {$np})
                            AS t)
                    ORDER BY c.postID, c.date;";

  // else: the request is for all posts for a user
  } else {

    // get any posts for the user
    $postsSQL = "SELECT * FROM
                  (SELECT p.date, p.due, p.text, p.type, p.aName, p.postID, p.postHash, ui.userID, ui.prefix, ui.firstName, ui.lastName, ui.photo, ui.link, g.groupName, g.groupLink FROM
                    (posts AS p RIGHT JOIN userInfo AS ui ON p.`userID` = ui.`userID`)
                    RIGHT JOIN groups AS g ON p.`groupID` = g.`groupID`
                  WHERE
                    p.`groupID` IN (SELECT groupID FROM userGroups WHERE userID={$userID})
                    OR
                    p.`groupID` IN (SELECT groupID FROM groups WHERE teacherID={$userID})
                  ORDER BY p.date DESC
                  LIMIT {$ntl} OFFSET {$np})
                AS t;";

    // find any attachments to add to these posts
    $attachmentsSQL = "SELECT a.postID, f.fileName, f.fileIntUrl, f.fileExtUrl, f.fileType
                        FROM
                          attachments AS a
                          RIGHT JOIN files AS f ON f.fileID = a.fileID
                        WHERE a.postID IN
                          (SELECT * FROM
                            (SELECT p.postID FROM posts AS p
                              WHERE
                                p.groupID IN (SELECT groupID FROM userGroups WHERE userID={$userID})
                                OR
                                p.groupID IN (SELECT groupID FROM groups WHERE teacherID={$userID})
                            ORDER BY p.date DESC
                            LIMIT {$ntl} OFFSET {$np})
                          AS t)
                        ORDER BY a.postID;";

    // find the comments attached to each post shown
    $commentSQL = "SELECT c.postID, c.commentText, c.date, ui.firstName, ui.lastName, ui.prefix,
                          ui.link AS userLink, ui.photo AS userPhoto, u.type AS userType
                    FROM
                    	(comments AS c RIGHT JOIN userInfo AS ui ON c.userID=ui.userID)
                    	RIGHT JOIN users As u ON ui.userID = u.userID
                    WHERE c.postID IN
                    	(SELECT * FROM
                    		(SELECT p.postID FROM posts AS p
                          WHERE
                            p.groupID IN (SELECT groupID FROM userGroups WHERE userID={$userID})
                            OR
                            p.groupID IN (SELECT groupID FROM groups WHERE teacherID={$userID})
                    			ORDER BY p.date DESC
                    			LIMIT {$ntl} OFFSET {$np})
                            AS t)
                    ORDER BY c.postID, c.date;";

  }

  // query the database with the sql created above
  $postsQuery = $conn->query($postsSQL);

  // if there are any rows (meaning more posts to load)
  if($postsQuery->num_rows > 0) {

    // query the database with the sql created above
    $attachmentsQuery = $conn->query($attachmentsSQL);

    // create an empty array of 'attachments'
    $attachments = [];

    // if there were any in the database
    if($attachmentsQuery->num_rows > 0) {
      while($aRow = $attachmentsQuery->fetch_assoc()) {
        // add the attachment as an array of values to the attachments array
        // at the index of the post
        $attachments[$aRow['postID']][] = [$aRow['fileName'], $aRow['fileIntUrl'], $aRow['fileExtUrl'], $aRow['fileType']];
      }
    }

    // query the database for comments
    $commentQuery = $conn->query($commentSQL);

    // create an empty array of 'comments' for each post
    $commentOutput = [];

    if($commentQuery->num_rows > 0) {
      while($cRow = $commentQuery->fetch_assoc()) {
        // prepare the name of the user who made the comment
        if($cRow['userType'] == 't') {
          $username = $cRow['prefix'].' '.$cRow['lastName'];
        } else {
          $username = $cRow['firstName'].' '.$cRow['lastName'];
        }

        // add the comment as an array of values to the comment array
        // at the index of the post
        $commentOutput[$cRow['postID']] .= '<div class="comment">
                                              <img src="'.$cRow['userPhoto'].'" />
                                              <div>
                                                <span><a href="user/'.$cRow['userLink'].'">'.$username.'</a> '.timeToWords($cRow['date']).'</span>
                                                <p>'.$cRow['commentText'].'</p>
                                              </div>
                                            </div>';
      }
    }

    // for each post
    while($post = $postsQuery->fetch_assoc()) {

      // add the attachments
      $attachmentOutput = '';
      $atts = $attachments[$post['postID']];
      if($atts) {
        foreach($atts as $att) {
          $p = 1;
          switch($att[3]) {
            case 'img':
              $p = 0;
              $attachmentOutput .= '<a class="post-attachment" href="file/'.$att[1].'">
                                      <div class="post-att-img" style="background-image: url('.$att[2].');"></div>
                                      <div class="post-att-info">
                                        <span>'.$att[0].'</span>
                                        <span>Image</span>
                                      </div>
                                    </a>';
              break;

            case 'url':
              $p = 0;
              $attachmentOutput .= '<a class="post-attachment" href="'.$att[2].'" target="_blank">
                                      <iframe src="'.$att[2].'" sandbox></iframe>
                                      <div class="post-att-info">
                                        <span>'.get_title($att[2]).'</span>
                                        <span>'.$att[2].'</span>
                                      </div>
                                    </a>';
              break;

            case 'word':
              $docType = 'Word';
              break;
            case 'ppt':
              $docType = 'Powerpoint';
              break;
            case 'excel':
              $docType = 'Excel';
              break;
            case 'pdf':
              $docType = 'PDF';
              break;
            case 'code':
              $docType = 'Code File';
              break;
            case 'zip':
              $docType = 'ZIP Folder';
              break;
            case 'text':
              $docType = 'Text File';
              break;
            case 'r-text':
              $docType = 'Rich Text File';
              break;
            case 'undef':
              $docType = 'Unknown file type';
              break;
          }

          if ($p == 1) {
            $attachmentOutput .= '<a class="post-attachment" href="file/'.$att[1].'">
                                    <div class="post-att-'.$att[3].'"></div>
                                    <div class="post-att-info">
                                      <span>'.$att[0].'</span>
                                      <span>'.$docType.'</span>
                                    </div>
                                  </a>';
          }
        }
      }

      if($commentOutput[$post['postID']]) {
        // prepare the comment output and an input for the user
        $comments = '<div class="post-comment" id="post-comments-'.$post['postHash'].'">
                    <a id="post-comment-show-'.$post['postHash'].'" onclick="showComments(\''.$post['postHash'].'\')">Show Comments&nbsp;<b>&or;</b></a>
                    <div class="post-comment-section" id="post-comment-section-'.$post['postHash'].'">
                      <span id="post-comments-html-'.$post['postHash'].'">'.$commentOutput[$post['postID']].'</span>
                      <a onclick="hideComments(\''.$post['postHash'].'\')">Hide Comments&and;</a>
                    </div>
                    <div class="comment">
                      <img src="'.$userPhoto.'" alt="Your profile photo">
                      <div class="comment-input" id="comment-input-'.$post['postHash'].'">
                        <textarea id="comment-text-'.$post['postHash'].'" placeholder="Add class comment..."/ rows="1" onclick="setInit(\''.$post['postHash'].'\')" onblur="clearAppearance(\''.$post['postHash'].'\')"></textarea>
                        <a onclick="commentOnPost(\''.$post['postHash'].'\')">Post</a>
                      </div>
                    </div></div>';
      } else {
        // prepare an input for the user
        $comments = '<div class="post-comment" id="post-comments-'.$post['postHash'].'">
                    <a id="post-comment-show-'.$post['postHash'].'" onclick="showComments(\''.$post['postHash'].'\')" class="hidden">Show Comments&nbsp;<b>&or;</b></a>
                    <div class="post-comment-section" id="post-comment-section-'.$post['postHash'].'">
                      <span id="post-comments-html-'.$post['postHash'].'"></span>
                      <a onclick="hideComments(\''.$post['postHash'].'\')">Hide Comments&and;</a>
                    </div>
                    <div class="comment">
                      <img src="'.$userPhoto.'" alt="Your profile photo">
                      <div class="comment-input" id="comment-input-'.$post['postHash'].'">
                        <textarea id="comment-text-'.$post['postHash'].'" placeholder="Add class comment..."/ rows="1" onclick="setInit(\''.$post['postHash'].'\')" onblur="clearAppearance(\''.$post['postHash'].'\')"></textarea>
                        <a onclick="commentOnPost(\''.$post['postHash'].'\')">Post</a>
                      </div>
                    </div></div>';
      }

      // get the type of the post (e.g. assignment or text)
      $type = $post['type'];
      // if the post is text
      if($type === 't') {
        // output of regular text post
        $output .= '<div class="post"><div class="post-header"><div class="pull-left">';

        // If it is your post
        if($post['userID'] === $userID) {
          // say 'You' instead of your name
          $output .= '<img src="'.$post["photo"].'" alt="Your profile photo">
                    <div class="post-image-container"><span class="post-title"><a class="post-title-teacher" href="user/'.$post['link'].'">You</a>';
        // else: not your post
        } else {
          // if a teacher's post
          if($post['prefix']) {
            $output .= '<img src="'.$post["photo"].'" alt="Profile photo of '.$post["prefix"].' '.$post["lastName"].'">
                      <div class="post-image-container"><span class="post-title"><a class="post-title-teacher" href="user/'.$post['link'].'">'.$post["prefix"]
                      .' '.$post["lastName"].'</a>';
          // else: student's post
          } else {
            $output .= '<img src="'.$post["photo"].'" alt="Profile photo of '.$post["firstName"].' '.$post["lastName"].'">
                      <div class="post-image-container"><span class="post-title"><a class="post-title-teacher" href="user/'.$post['link'].'">'.$post["firstName"]
                      .' '.$post["lastName"].'</a>';
          }
        }
        // add the rest of the text
        $output .= ' to <a href="group/'.$post["groupLink"].'">'.$post["groupName"].'</a></span><span class="post-date">'.timeToWords($post['date']).
                    '</span></div></div></div><div class="post-content"><p>'.$post['text'].'</p>'.$attachmentOutput.'</div>'.$comments.'</div>';

      // else: if the post is an assignment
      } else if($type === 'a'){
        // output of an assignment post
        $output .= '<div class="post post-assignment"><div class="post-header"><div class="pull-left">';

        // if this is your post
        if ($post['userID'] == $userID) {
          // say 'You' instead of name
          $output .= '<img src="'.$post["photo"].'" alt="Your profile photo"><div class="post-image-container">'.
                      '<span class="post-title"><a class="post-title-teacher" href="user/'.$post['link'].'">You</a>';
        // else: not your post
        } else {
          // if teacher post
          if($post['prefix']) {
            $output .= '<img src="'.$post["photo"].'" alt="Profile photo of '.$post["prefix"].' '.$post["lastName"].'">
            <div class="post-image-container"><span class="post-title"><a class="post-title-teacher" href="user/'.$post['link'].'">'
            .$post["prefix"].' '.$post["lastName"].'</a>';
          // else: student
          } else {
            $output .= '<img src="'.$post["photo"].'" alt="Profile photo of '.$post["firstName"].' '.$post["lastName"].'">
            <div class="post-image-container"><span class="post-title"><a class="post-title-teacher" href="user/'.$post['link'].'">'
            .$post["firstName"].' '.$post["lastName"].'</a>';
          }

        }

        $d = strtotime($post['due']);

        // create a time for 'today' at midnight (e.g. beginning of the day within no hours and seconds)
        $currentDay = new Datetime('now', new Datetimezone('Australia/Sydney'));
        $currentDay = strtotime($currentDay->format('Y-m-d H:i:s') . PHP_EOL);

        if ($d <= $currentDay) {
          $ai = 2;
        } else if ($d > $currentDay + (24*60*60)) {
          $ai = 0;
        } else {
          $ai = 1;
        }

        // add the rest of the assignment text
        $output .= ' to <a href="group/'.$post["groupLink"].'">'.$post["groupName"]
                  .'</a></span><span class="post-date">'.timeToWords($post['date']).'
                  </span></div></div><div class="pull-right"><div class="assignment-indicator ai-'.$ai.'">
                  '.assignmentTime($post['due']).'</div></div></div>
                  <div class="post-content"><h3>'.$post['aName'].'</h3><p>'.$post['text'].'</p>'.$attachmentOutput.'<br><a href="" id="post-handin-'
                  .$post['postHash'].'" class="post-assignment-handin">Hand In</a><br><br></div>'.$comments.'</div>';
      }
    }
  }
}

echo $output;
?>
