<?php

// include the connect script
include_once "php/connect.php";

// get the inputted group link from the page URL
$groupLink = $conn->real_escape_string($_GET['gid']);

// get what the user typed into the URL
$url = $_SERVER['REQUEST_URI'];

// check whether the last character is a '/'
if(substr($url,-1) === '/') {
  if(substr($url,(-strlen($groupLink) - 1)) === $groupLink.'/') {
    // group/groupCode/
    $path = '../../';
  } else {
    // group/groupCode/feed/
    // group/groupCode/students/
    // group/groupCode/backpack/
    $path = '../../../';
  }

} else {

  if(substr($url,-strlen($groupLink)) === $groupLink) {
    // group/groupCode
    $path = '../';
  } else {
    if (substr($url,-5) == 'group'){
      $path = 'home';
    } else {
      // group/groupCode/feed
      // group/groupCode/students
      // group/groupCode/backpack
      $path = '../../';
    }
  }
}

// include the redirect script
include_once "php/redirect.php";

// --- get the information for the group to be displayed on the page
// set up a variable for the groups's information
$groupInfo = [];

// get the inputted group type of page to show
$groupPageType = $_GET['t'];

// Get the group name, the school name, the teachers name (prefix + last name),
//    the teacher's profile photo, the teacher's link and the number of students
//    that are signed up to the class (using a mySQL COUNT() function)
$groupSQL =   "SELECT
              	g.groupID, g.groupName, g.groupColour, g.groupSubject, g.groupAccessCode,
              	(SELECT schoolName FROM schools WHERE id=ui.school) AS groupSchool,
              	CONCAT(ui.prefix, ' ', ui.LastName) AS teacherName,
              	ui.photo AS teacherPhoto,
              	ui.link AS teacherLink,
              	(SELECT COUNT(userID) FROM userGroups WHERE groupID=g.groupID) AS numStudents
              FROM groups AS g
              JOIN userInfo AS ui
              	ON g.teacherID=ui.userID
              WHERE g.groupLink = '{$groupLink}';";

// run the SQL above to get the results from the database
$groupResult = $conn->query($groupSQL);

// if there wasn't one row returned, either:
//    - user doesn't exist (no rows returned)
//    - more than one user exists with that userLink (more than one row returned)
//      :: however this is unlikely as I need more than 80,000 users for the
//         userLinks to start repeating (I would have to solve this as the problem
//         arose --> much further down the line)
if($groupResult->num_rows != 1) {

  // presumably the user does not exist

  echo 'group doesn\'t exist';





// else: one row was returned - group exists
} else {

  // put the information in the groupInfo array
  $groupInfo = $groupResult->fetch_assoc();


  $output = '<!DOCTYPE html>
            <html>
              <head>
                <meta charset="utf-8">
                <title>Involve | '.$groupInfo['groupName'].'</title>';


  // For more information on what happens in the PHP below go to the
  // user.php file in the home directory and read through a very similar
  // solution implemented there

  // check whether the last character is a '/'
  if(substr($url,-1) === '/') {
    if(substr($url,(-strlen($groupLink) - 1)) === $groupLink.'/') {
      // group/groupCode/
      $output .= '<base href="../../">';
    } else {
      // group/groupCode/feed/
      // group/groupCode/students/
      // group/groupCode/backpack/
      $output .= '<base href="../../../">';
    }

  // else: last character is not '/'
  } else {

    if(substr($url,-strlen($groupLink)) === $groupLink) {
      // group/groupCode
      $output .= '<base href="../">';
    } else {
      // group/groupCode/feed
      // group/groupCode/students
      // group/groupCode/backpack
      $output .= '<base href="../../">';
    }
  }

  // add the CSS, end the head element and start the body element
  $output .= '<link rel="stylesheet" href="css/group.css">
              </head><body>';

  // print the output to the page before the include of sidebar is called
  echo $output;

  // include the sidebar from another php file
  include_once "includes/sidebar-left.php";

  // add the content of the group page
  $output = '<div class="content" id="content">
                <div id="group-banner" class="class-colour-'.$groupInfo['groupColour'].'">
                  <div class="group-banner-flex">
                    <div class="group-information">
                      <h2>'.$groupInfo['groupName'].'</h2>
                      <h3>'.$groupInfo['groupSubject'].' - '.$groupInfo['groupSchool'].'</h3>
                      <h3>';

  // if there is more than one student in the class
  if ($groupInfo['numStudents'] > 1) {
    $output .= $groupInfo['numStudents'].' students';
  // else: only one
  } else if ($groupInfo['numStudents'] == 1){
    $output .= '1 student';
  // else: none
  } else {
    $output .= 'No students';
  }

  // keep adding to the ouput (finish the banner)
  $output .=        '</div>
                    <div class="group-teacher">
                      <img src="'.$groupInfo['teacherPhoto'].'">
                    </div>
                  </div>
                </div>';

  // add the navigation bar (below banner) to the HTML
  $output .= '<div id="group-nav">
                <div class="group-nav-flex">
                  <div class="group-nav-left nav-select-feed">';
  if($groupPageType === 'fe') {
    $output .= '<a href="group/'.$groupLink.'/feed" id="nav-selected-'.$groupInfo['groupColour'].'">Class Feed</a>
                <a href="group/'.$groupLink.'/students">Students</a>
                <a href="group/'.$groupLink.'/files">Files</a>';
  } else if($groupPageType === 's') {
    $output .= '<a href="group/'.$groupLink.'/feed">Class Feed</a>
                <a href="group/'.$groupLink.'/students" id="nav-selected-'.$groupInfo['groupColour'].'">Students</a>
                <a href="group/'.$groupLink.'/files">Files</a>';
  } else if($groupPageType == 'fi'){
    $output .= '<a href="group/'.$groupLink.'/feed">Class Feed</a>
                <a href="group/'.$groupLink.'/students">Students</a>
                <a href="group/'.$groupLink.'/files" id="nav-selected-'.$groupInfo['groupColour'].'">Files</a>';
  }
  $output .=       '</div>
                  <div class="group-nav-right">Teacher: <a href="user/'.$groupInfo['teacherLink'].'">'.$groupInfo['teacherName'].'</a></div>
                </div>
              </div>
              <div class="group-content">';

  // query to return whether the user is in the group or the teacher of the group
  $userInGroupResult = $conn->query("SELECT 1 FROM userGroups WHERE
                                      (userID = {$userInfo['userID']} AND groupID = {$groupInfo['groupID']})
                                    OR
                                      (groupID = (SELECT groupID FROM groups WHERE teacherID = {$userInfo['userID']}))
                                    LIMIT 1;");

  // check whether the user is NOT in the group
  if($userInGroupResult->num_rows === 0) {
    // check whether the user has already requested to join the group
    $alreadyRequested = $conn->query("SELECT * FROM requestToJoinGroup WHERE userID={$userInfo['userID']} AND groupID = {$groupInfo['groupID']};");

    // if they have not
    if($alreadyRequested->num_rows === 0) {
      $output .= '<p class="text-message" id="not-member">You are not a member of this group.
                  <br>If you believe this to be an error or want to join, please
                  <a onclick="requestToJoinGroup(\''.$groupLink.'\', \''.$userInfo['link'].'\');">request to join this group</a>.</p>';
    // else: they have a pending request
    } else {
      $output .= '<p class="text-message" id="not-member">You are not a member of this group.
                  <br>However you currently have a pending request to join this group.</p>';
    }

  // else: user IS in the group
  } else {
    // students was selected
    if ($groupPageType === 's') {

      $studentsSQL = "SELECT CONCAT(ui.firstName, ' ', ui.lastName) AS name, ui.photo, ui.link
                      FROM userInfo AS ui
                      WHERE
                      	ui.userID IN
                      		(SELECT userID FROM userGroups WHERE groupID = {$groupInfo['groupID']})
                      ORDER BY ui.lastName;";
      $studentsResult = $conn->query($studentsSQL);

      $numStudents = $studentsResult->num_rows;
      if($numStudents > 0) {
        if($numStudents == 1) {
          $output .= '<div class="student-number">There is 1 student in this class.</div><div class="students-flex">';
        } else {
          $output .= '<div class="student-number">There are '.$numStudents.' students in this class.</div><div class="students-flex">';
        }
        while($stu = $studentsResult->fetch_assoc()) {
          $output .= '<a href="user/'.$stu['link'].'"><img src="'.$stu['photo'].'"><span>'.$stu['name'].'</span></a>';
        }
        $output .= '</div>';
      } else {
        $output .= '<div class="text-message">No students in this class yet.<br>The group code is <span style="">'.$groupInfo['groupAccessCode'].'</span> if you want to add students.</div>';
      }

    // else: files was selected
    } else if($groupPageType === 'fi') {

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

      $filesSQL = "SELECT f.fileName, f.fileIntUrl,
                          f.fileExtUrl, f.fileType
                    FROM
                    	attachments AS a
                    	RIGHT JOIN files AS f ON f.fileID = a.fileID
                    WHERE a.postID IN
                      (SELECT * FROM (SELECT postID FROM posts
                        WHERE
                          groupID = {$groupInfo['groupID']}
                      ORDER BY date DESC
                      LIMIT 20) temp_tab);";
      $filesResult = $conn->query($filesSQL);

      if($filesResult->num_rows > 0) {
        $output .= '<div class="files-flex">';
        while($file = $filesResult->fetch_assoc()) {
          $p = 1;
          switch($file['fileType']) {
            case 'img':
              $p = 0;
              $output .= '<a href="file/'.$file['fileIntUrl'].'">
                            <div class="post-att-img" style="background-image: url('.$file['fileExtUrl'].');"></div>
                            <div class="post-att-info">
                              <span>'.$file['fileName'].'</span>
                              <span>Image</span>
                            </div>
                          </a>';
              break;
            case 'url':
              $p = 0;
              $output .= '<a href="'.$file['fileExtUrl'].'" target="_blank">
                            <iframe src="'.$file['fileExtUrl'].'" sandbox></iframe>
                            <div class="post-att-info">
                              <span>'.get_title($file['fileExtUrl']).'</span>
                              <span>'.$file['fileExtUrl'].'</span>
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
            default:
              break;
          }

          if($p == 1) {
            $output .= '<a class="post-attachment" href="file/'.$file['fileIntUrl'].'">
                          <div class="post-att-'.$file['fileType'].'"></div>
                          <div class="post-att-info">
                            <span>'.$file['fileName'].'</span>
                            <span>'.$docType.'</span>
                          </div>
                        </a>';
          }
        }
        $output .= '</div>';
      } else {
        $output .= '<div class="text-message">No files are associated with this group yet.</div>';
      }

    // else: feed was selected (or the default)
    } else {

      // function returns the timestamp of the start of a day given the day as a string
      // e.g. "6 Jan 2017" --> 1483657200
      function getDayAsTimestamp($day) {
        $timestamp = new Datetime($day, new Datetimezone('Australia/Sydney'));
        $timestamp = strtotime($timestamp->format('Y-m-d H:i:s') . PHP_EOL);
        return $timestamp;
      }

      // get the current day as a timestamp (e.g. midnight of the previous day or 0am of today)
      $currentDay = getDayAsTimestamp('today');

      // number of days in the current month
      $numDaysInThisMonth = date('t', $currentDay);

      // day of the month
      $dayOfTheMonth = date('j', $currentDay);

      // number of days left = number of days in the month minus the day we are up to
      $numDaysLeftInMonth = $numDaysInThisMonth - $dayOfTheMonth;

      // get the first day of the month as a DateTime object
      $d = new DateTime('first day of this month', new Datetimezone('Australia/Sydney'));

      // convert the DateTime Object into a string "Month Year"
      $monthText = $d->format('F Y');

      // get the weekday of the first day of the month where:
      // Sunday = 0 ... Sat = 6
      $dayOffset = $d->format('w');

      // first saturday of the month
      $firstSaturdayOfMonth = date('j',strtotime('First Saturday of '.$monthText));

      // first sunday of the month
      $firstSundayOfMonth = date('j',strtotime('First Sunday of '.$monthText));

      // sql query to get the start date of all corresponding classes and the repeat
      //     interval of said classes
      $timetableItemsSQL = "SELECT gt.startDate, gt.repeatInterval
                            	FROM groupsTimetable AS gt
                              WHERE groupID = {$groupInfo['groupID']}
                              AND ((startDate - {$currentDay}) % repeatInterval = 0
                                  OR (startDate - {$currentDay}) % repeatInterval + repeatInterval < 86400*{$numDaysLeftInMonth});";

      // run the sql query above
      $timetableItemsQuery = $conn->query($timetableItemsSQL);

      // create an empty array of classes for the group
      $classes = [];

      // if there are any rows in the database found go through each as $row
      if ($timetableItemsQuery->num_rows > 0) {
        while($row = $timetableItemsQuery->fetch_assoc()) {

          // for each row append it to the end of the classes array so that it becomes
          // a two dimensional array of all the classes for the group
          $classes[] = $row;
        }
      }

      // create an output with the first part of the calendar
      // where $dayOffset is being used is a means of moving the first day of the month
      // to the correct day of the week (Sunday does not need to be offset which works
      // because 0 * 30px is 0px and Saturday will be offset by 6*30px)
      $output .= '<div class="group-content-flex"><div class="group-content-sidebar"><div class="group-timetable">
          <h4>'.$monthText.'</h4>
          <div class="group-timetable-dates">
            <div class="group-timetable-weekdays">
              <span>Sun</span>
              <span>Mon</span>
              <span>Tue</span>
              <span>Wed</span>
              <span>Thu</span>
              <span>Fri</span>
              <span>Sat</span>
            </div>
            <div id="timetable-dates-flex">
              <div class="timetable-offset" style="width: calc('.$dayOffset.' * 30px);"></div>';

      // for each day in the month
      for ($i = 1; $i <= $numDaysInThisMonth; $i++) {

        // set the class to be inputted to nothing (if weekend)
        $dayClass = '';

        // if day is NOT a weekend (saturday or sunday)
        if (($i - $firstSaturdayOfMonth) % 7 != 0 && ($i - $firstSundayOfMonth) % 7 != 0) {
          // get the timestamp for the day
          $ts = getDayAsTimestamp($i.' '.$monthText);

          // for all the classes the teacher takes part in
          for ($j = 0; $j < count($classes); $j++) {
            // if the class is on that day
            if(($classes[$j]['startDate'] - $ts) % $classes[$j]['repeatInterval'] == 0) {
              // add one to the counter of classes today
              $dayClass = 'free';
              break;
            }
          }
        }

        // if the day of the month is the current day add a selected class
        if ($i == $dayOfTheMonth) {
          $output .= '<div class="timetable-date selected '.$dayClass.'"><span>'.$i.'</span></div>';

        // else do a regular day of the calendar
        } else {
          $output .= '<div class="timetable-date '.$dayClass.'"><span>'.$i.'</span></div>';
        }
      }

      // get the number of extra days needed to fill in the calendar
      $numberOfExtraDays = 7 - (($dayOffset + $numDaysInThisMonth) % 7);

      // for each of those days add a disabled number
      for ($i = 1; $i <= $numberOfExtraDays; $i++) {
        $output .= '<div class="timetable-date disabled"><span>'.$i.'</span></div>';
      }

      // close off any unfinished divs
      $output .= '</div><div class="group-timetable-key">
                    <span>= there is a class on</span>
                  </div></div></div></div>';


      $output .= '<div id="group-content-feed"><p>Loading...</p></div></div>';

      // the number of posts to load initially (there is a load more button)
      $numPostsToLoad = 20;

      // the number of posts available to the user
      $numPostsQuery = $conn->query("SELECT COUNT(p.postID) AS num
                                      FROM posts AS p
                                      WHERE p.`groupID` = {$groupInfo['groupID']};");
      $numPosts = $numPostsQuery->fetch_assoc()['num'];

    }
  }

  $output .= '</div></div></body></html>';

  // print this to the page
  echo $output;
}

?>

<!-- User Action -->
<?php
if($userInGroupResult->num_rows != 0) {
  include_once "includes/user-action.php";
}

?>

<script>

// Create a dynamic textbox
// I modified the code a bit to work for my case but this should be good
// Credit: http://stackoverflow.com/questions/454202/creating-a-textarea-with-auto-resize
var observe;
if (window.attachEvent) {
    observe = function (element, event, handler) {
        element.attachEvent('on'+event, handler);
    };
}
else {
    observe = function (element, event, handler) {
        element.addEventListener(event, handler, false);
    };
}

function init (elem) {
  function resize (e) {
    elem.className = '';
    elem.style.height = 'auto';
    if(elem.scrollHeight === 0) {
      elem.style.height = '28px';
    } else if(elem.scrollHeight > window.innerHeight/2) {
      elem.style.height = (window.innerHeight/2)+'px';
    } else {
      elem.style.height = elem.scrollHeight+'px';
    }
  }
  function delayedResize (e) {
    window.setTimeout(function() {
      resize(e);
    }, 0);
  }
  observe(elem, 'change',  function() { resize(elem); });
  observe(elem, 'cut',     function() { delayedResize(elem); });
  observe(elem, 'paste',   function() { delayedResize(elem); });
  observe(elem, 'drop',    function() { delayedResize(elem); });
  observe(elem, 'keydown', function() { delayedResize(elem); });
  resize(elem);
}

// make the textarea dynamic
var text = document.getElementById('create-post-input-text');
init(text);

// create an array of all the textareas already initialised
var alreadyInit = [];

// function called every time a comment input is clicked
var setInit = function(code) {
  // if the comment input has NOT already been initialised
  if(alreadyInit.indexOf(code) === -1) {

    // initialise it
    init(document.getElementById('comment-text-'+code));

    // add it to the array
    alreadyInit.push(code);

    // change the display of the element
    document.getElementById('comment-input-'+code).className = 'comment-input in-use';
  } else {
    // change the display of the element
    document.getElementById('comment-input-'+code).className = 'comment-input in-use';
  }
};

// clear the comment post button
var clearAppearance = function(code) {
  // if there is no text in the comment
  if (document.getElementById('comment-text-'+code).value == '') {
    // remove styling
    document.getElementById('comment-input-'+code).className = 'comment-input';
  }
};

// show comments
var showComments = function(code) {
  document.getElementById('post-comment-show-'+code).className = 'hidden';
  document.getElementById('post-comment-section-'+code).className = 'post-comment-section shown';
};

// hide comments
var hideComments = function(code) {
  document.getElementById('post-comment-show-'+code).className = '';
  document.getElementById('post-comment-section-'+code).className = 'post-comment-section';
};

// make a comment
var commentOnPost = function(code) {
  // get the comment text
  var text = document.getElementById('comment-text-'+code).value;

  // set up a request
  var request;
  if (window.XMLHttpRequest) {
    request = new XMLHttpRequest();
  } else {
    request = new ActiveXObject("Microsoft.XMLHTTP");
  }

  // on return of information from AJAX request
  request.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      // the comment was successful
      if(this.responseText != 0) {
        // get the element that the comment will move into
        var commentSection = document.getElementById('post-comments-html-'+code);
        // add the comment into the element
        commentSection.innerHTML = commentSection.innerHTML + '<div class="comment"><img src="<?php echo $userInfo['photo']; ?>" /><div><span><a href="user"><?php echo $userInfo['firstName'].' '.$userInfo['lastName']; ?></a> Right Now</span><p>'+this.responseText+'</p></div></div>';

        // clear the comment input
        document.getElementById('comment-text-'+code).value = '';
        // show the comment section (if not already shown)
        showComments(code);
        // remove the comment input styling
        clearAppearance(code);
      }
    }
  };

  // send the request to the php file: testEmail.php with the inputted email
  request.open('GET', 'php/addComment.php?p='+code+'&u=<?php echo $userInfo['link']; ?>&t='+ encodeURIComponent(text), true);
  request.send();
};

var requestToJoinGroup = function(groupHash, userHash) {
  // set up an AJAX request
  var request;
  if (window.XMLHttpRequest) {
    request = new XMLHttpRequest();
  } else {
    request = new ActiveXObject("Microsoft.XMLHTTP");
  }

  // on return of information from AJAX request
  request.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {

      var notMemElem = document.getElementById('not-member');

      // if the username is not found in the database (email is free)
      if(this.responseText == 0) {

        notMemElem.className = 'success';
        notMemElem.innerText = 'Your request is now pending teacher approval.';
        return;

      // else the email has been found in the database
      } else {

        notMemElem.className = 'failed';
        notMemElem.innerText = 'An error occured, please try again later.';
        return;
      }
    }
  };

  // send the request to the php file: testEmail.php with the inputted email
  request.open('GET', 'php/requestToJoinGroup.php?gh=' + groupHash + '&uh=' + userHash, true);
  request.send();
};


document.onscroll = function() {
  var scrollX = document.body.scrollTop,
      groupNav = document.getElementById('group-nav'),
      groupContent = document.getElementById('group-nav'),
      groupBanner = document.getElementById('group-banner');

  if (scrollX >= 225) {
    groupNav.className = 'top';
    groupBanner.style.marginBottom = '50px';
  } else {
    groupNav.className = '';
    groupBanner.style.marginBottom = '';
  }
}


// Remove an HTML element
// Credit: http://stackoverflow.com/questions/3387427/remove-element-by-id
Element.prototype.remove = function() {
  this.parentElement.removeChild(this);
}
NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
  for(var i = this.length - 1; i >= 0; i--) {
    if(this[i] && this[i].parentElement) {
      this[i].parentElement.removeChild(this[i]);
    }
  }
}

// variables for the posts loading
var numberOfPostsAvailableToUser = <?php echo $numPosts; ?>,
    numCurrentPosts = 0,
    contentElem = document.getElementById('group-content-feed');

var loadPosts = function() {
  if (numCurrentPosts < numberOfPostsAvailableToUser) {
    var request;

    // set the correct request type (AJAX)
    if (window.XMLHttpRequest) {
      request = new XMLHttpRequest();
    } else {
      request = new ActiveXObject("Microsoft.XMLHTTP");
    }

    // on return of information from AJAX request
    request.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {

        if(document.getElementById('load-more-posts')) {
          document.getElementById('load-more-posts').remove();
        }

        if(contentElem.innerHTML == '<p>Loading...</p>') {
          contentElem.innerHTML = '';
        }

        contentElem.innerHTML = contentElem.innerHTML + this.responseText;

        // add to the number of current posts the number of most number of posts added
        numCurrentPosts = numCurrentPosts + <?php echo $numPostsToLoad; ?>;

        if (numCurrentPosts < numberOfPostsAvailableToUser) {
          contentElem.innerHTML = contentElem.innerHTML + '<a id="load-more-posts" onclick="loadPosts()">Load More Posts</a>';
        } else {
          contentElem.innerHTML = contentElem.innerHTML + '<p>That\'s it, you\'ve seen every post.</p>';
        }
      }
    };

    var url = 'php/requestLoadPosts.php?uh=<?php echo $userInfo['link']; ?>&ntl=<?php echo $numPostsToLoad; ?>&np=' + numCurrentPosts + '&gh=<?php echo $groupLink; ?>';

    if(numCurrentPosts === 0) {
      url = 'php/requestLoadPosts.php?uh=<?php echo $userInfo['link']; ?>&ntl=<?php echo $numPostsToLoad; ?>&f=1&gh=<?php echo $groupLink; ?>';
    }

    // send the request to the php file: testEmail.php with the inputted email
    request.open('GET', url, true);
    request.send();

  // else: there are no posts to load
  } else {
    // if there is a button (for some reason)
    if (document.getElementById('load-more-posts')) {
      // remove the button
      document.getElementById('load-more-posts').remove();
    }

    contentElem.innerHTML = '<p>There are no posts for this group, you can make one by clicking the plus below.</p>';
  }
}

loadPosts();

</script>
