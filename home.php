<?php

// start the session (keep user logged in)
session_start();

// include the connect script
include_once "php/connect.php";

// create an empty array to hold the current user's information
$userInfo = [];

// if the session doesn't have a token set (not logged in)
if (!$_SESSION['token']) {

  // destroy the session created at the top of the page
  session_destroy();

  // send the user to the home page
  header("Location: .");

  // if the user is logged in
} else {
  // select the user information from the database where the user's token is correct
  $sql = "SELECT * FROM userInfo WHERE userID=(SELECT userID FROM users WHERE token='{$_SESSION['token']}');";
  $result = $conn->query($sql);

  // if there is a user with that token
  if ($result->num_rows == 1) {
    // set the array $userInfo to hold the information (not stored in $_SESSION making it more secure)
    $userInfo = $result->fetch_assoc();

  // else if the user's stored token doesn't match any in the database
  } else {
    // destroy session data (token and any other information)
    session_destroy();

    // send the user to the homepage
    header("Location: .");
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
    return date("d M",$d);

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

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Involve | Home</title>

    <link rel="stylesheet" href="css/home.css">
    <script src="js/home.js"></script>
  </head>
  <body>

    <?php include_once "includes/sidebar-left.php"; ?>

    <!-- Page content -->
    <div class="content" id="content">

      <!-- Posts -->
      <?php

      // prepareSQL to get all posts from relevant groups
      // returns the date of post, due date of assignments, type of post, teachers name (prefix and lastName e.g. Mr, Smith),
      //     the teachers photo, the classes' ID and the class's name
      $postSQL = "SELECT p.date, p.due, p.text, p.type, p.postID, p.postHash, ui.userID, ui.prefix, ui.firstName, ui.lastName, ui.photo, ui.link, g.groupName, g.groupLink FROM
                    (posts AS p RIGHT JOIN userInfo AS ui ON p.`userID` = ui.`userID`)
                    RIGHT JOIN groups AS g ON p.`groupID` = g.`groupID`
                  WHERE
                  	p.`groupID` IN (SELECT groupID FROM userGroups WHERE userID={$userInfo['userID']})
                    OR
                    p.`groupID` IN (SELECT groupID FROM groups WHERE teacherID={$userInfo['userID']})
                  ORDER BY p.date DESC
                  LIMIT 20;";

      // query the database with relevant query
      $postQuery = $conn->query($postSQL);

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

      $attachmentSQL = "SELECT a.postID, f.fileName, f.fileIntUrl, f.fileExtUrl, f.fileType, f.internalBlob
                        FROM
                        	attachments AS a
                        	RIGHT JOIN files AS f ON f.fileID = a.fileID
                        WHERE a.postID IN
                          (SELECT * FROM (SELECT postID FROM posts
                            WHERE
                              groupID IN (SELECT groupID FROM userGroups WHERE userID={$userInfo['userID']})
                              OR
                              groupID IN (SELECT groupID FROM groups WHERE teacherID={$userInfo['userID']})
                          ORDER BY date DESC
                          LIMIT 20) temp_tab);";

      $attachmentResult = $conn->query($attachmentSQL);
      $attachments = [];

      if($attachmentResult->num_rows > 0) {
        while($aRow = $attachmentResult->fetch_assoc()) {
          if(!$attachments[$aRow['postID']]) {
            $attachments[$aRow['postID']] = [];
          }
          $attachments[$aRow['postID']][] = [$aRow['fileName'], $aRow['fileIntUrl'], $aRow['fileExtUrl'], $aRow['fileType'], $aRow['internalBlob']];
        }
      }

      // prepare an output
      $output = '';

      // if there are posts --> loop through all posts individually
      if ($postQuery->num_rows > 0) {
        while($post = $postQuery->fetch_assoc()) {

          $attachmentOutput = '';
          $atts = $attachments[$post['postID']];
          if($atts) {
            foreach($atts as $att) {
              $attachmentOutput .= '';
              switch($att[3]) {
                case 'img':
                  $attachmentOutput .= '<a class="post-attachment" href="file/'.$att[1].'">
                                          <div class="post-att-img" style="background-image: url('.$att[2].');"></div>
                                          <div class="post-att-info">
                                            <span>'.$att[0].'</span>
                                            <span>Image</span>
                                          </div>
                                        </a>';
                  break;

                case 'url':
                  $attachmentOutput .= '<a class="post-attachment" href="'.$att[2].'" target="_blank">
                                          <iframe src="'.$att[2].'" sandbox></iframe>
                                          <div class="post-att-info">
                                            <span>'.get_title($att[2]).'</span>
                                            <span>'.$att[2].'</span>
                                          </div>
                                        </a>';
                  break;

                case 'pdf':
                  $attachmentOutput .= '<a class="post-attachment" href="file/'.$att[1].'">
                                          <div class="post-att-img" style="background-image: url(data:image/jpeg;base64,' .  base64_encode($att[4]).');"></div>
                                          <div class="post-att-info">
                                            <span>'.$att[0].'</span>
                                            <span>PDF</span>
                                          </div>
                                        </a>';
                  break;

                case 'html':
                  $attachmentOutput .= '<a class="post-attachment" href="file/'.$att[1].'"></a>';
                  break;

                case 'word':
                  $attachmentOutput .= '<a class="post-attachment" href="file/'.$att[1].'">
                                          <div class="post-att-word"></div>
                                          <div class="post-att-info">
                                            <span>'.$att[0].'</span>
                                            <span>Word</span>
                                          </div>
                                        </a>';
                  break;

                case 'ppt':
                  $attachmentOutput .= '<a class="post-attachment" href="file/'.$att[1].'">
                                          <div class="post-att-word"></div>
                                          <div class="post-att-info">
                                            <span>'.$att[0].'</span>
                                            <span>Powerpoint</span>
                                          </div>
                                        </a>';
                  break;

                case 'excel':
                  $attachmentOutput .= '<a class="post-attachment" href="file/'.$att[1].'">
                                          <div class="post-att-word"></div>
                                          <div class="post-att-info">
                                            <span>'.$att[0].'</span>
                                            <span>Excel</span>
                                          </div>
                                        </a>';
                  break;

                default:
                  $attachmentOutput .= '<a class="post-attachment" href="file/'.$att[1].'"></a>';
                  break;
              }
            }
            $attachmentOutput .= '<br>';
          }

          // get the type of the post (e.g. assignment or text)
          $type = $post['type'];
          // if the post is text
          if($type === 't') {
            // output of regular text post
            $output .= '<div class="post"><div class="post-header"><div class="pull-left">';

            // If it is your post
            if($post['userID'] === $userInfo['userID']) {
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
                        '</span></div></div></div><div class="post-content"><p>'.$post['text'].'</p>'.$attachmentOutput.'<a href="" id="post-comment-'
                        .$post['postHash'].'" class="post-content-comment">Add Comment</a></div></div>';

          // else: if the post is an assignment
          } else if($type === 'a'){
            // output of an assignment post
            $output .= '<div class="post post-assignment"><div class="post-header"><div class="pull-left">';

            // if this is your post
            if ($post['userID'] === $userInfo['userID']) {
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
            // add the rest of the assignment text
            $output .= ' to <a href="group/'.$post["groupLink"].'">'.$post["groupName"]
                      .'</a></span><span class="post-date">'.timeToWords($post['date']).'
                      </span></div></div><div class="pull-right"><div class="assignment-indicator ai-0">Due
                      '.timeToWords($post['due']).'</div></div></div>
                      <div class="post-content"><p>'.$post['text'].'</p>'.$attachmentOutput.'<a href="" id="post-handin-'
                      .$post['postHash'].'" class="post-assignment-handin">Hand In</a><a href="" id="post-comment-'
                      .$post['postHash'].'" class="post-content-comment">Add Comment</a></div></div>';
          }
        }
      }
      // print the output to the screen
      echo $output;
      ?>

      <!-- End with a load more -->
    </div>

    <!-- sidebar on right -->
    <?php include_once "includes/sidebar-right.php"; ?>
  </body>
</html>
