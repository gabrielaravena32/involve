<?php

// start the session (keep user logged in)
session_start();

// include the connect script
include_once "php/connect.php";

// create an empty array to hold the current user's information
$userInfo = [];

// if the session doesn't have a token set (not logged in)
if (!$_SESSION['token']) {

  // destroy the session created at the top of the page and send them to home.php
  session_destroy();
  header("Location: .");

// else: if the user is logged in
} else {
  // select the user's information from the database where the user's token is correct
  $sql = "SELECT * FROM userInfo WHERE userID=(SELECT userID FROM users WHERE token='{$_SESSION['token']}');";
  $result = $conn->query($sql);

  // if there is a user with that token
  if ($result->num_rows == 1) {
    // set the array $userInfo to hold the information (not stored in $_SESSION making it more secure)
    $userInfo = $result->fetch_assoc();

  // else if the user's stored token doesn't match any in the database
  } else {
    // destroy session data (token and any other information) and send them to home.php
    session_destroy();
    header("Location: .");
  }
}

// --- get the information for the group to be displayed on the page
// set up a variable for the groups's information
$groupInfo = [];
// get the inputted group link from the page URL
$groupLink = $conn->real_escape_string($_GET['gid']);

// get the inputted group type of page to show
$groupPageType = $_GET['t'];

// Get the group name, the school name, the teachers name (prefix + last name),
//    the teacher's profile photo, the teacher's link and the number of students
//    that are signed up to the class (using a mySQL COUNT() function)
$groupSQL = "SELECT
        	g.groupID, g.groupName, g.groupColour, g.groupSubject,
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





// else: one row was returned - user exists
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

  // get what the user typed into the URL
  $url = $_SERVER['REQUEST_URI'];
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
                <div class="group-banner class-colour-'.$groupInfo['groupColour'].'">
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
  $output .= '<div class="group-nav">
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
      $output .= '<p id="not-member">You are not a member of this group.
                  <br>If you believe this to be an error or want to join, please
                  <a onclick="requestToJoinGroup(\''.$groupLink.'\', \''.$userInfo['link'].'\');">request to join this group</a>.</p>';
    // else: they have a pending request
    } else {
      $output .= '<p id="not-member">You are not a member of this group.
                  <br>However you currently have a pending request to join this group.</p>';
    }

  // else: user IS in the group
  } else {
    // students was selected
    if ($groupPageType === 's') {

      $output .= 'students page';

    // else: files was selected
    } else if($groupPageType === 'fi') {

      $output .= 'files page';

    // else: feed was selected (or the default)
    } else {

      $output .= 'feed page';

    }
  }

  $output .= '</div></div></body></html>';

  // print this to the page
  echo $output;

}

?>




<script>
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
</script>
