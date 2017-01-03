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
  $sql = "SELECT * FROM userInfo WHERE userID=(SELECT userID FROM users WHERE token='".$_SESSION['token']."');";
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
  <body onload="progressBar()">

    <!-- Navbar -->
    <?php include_once "includes/navbar.php"; ?>

    <div class="page">

      <?php include_once "includes/sidebar-left.php"; ?>

      <!-- Page content -->
      <div class="content" id="content">

        <!-- Posts -->
        <?php

        $postSQL = '';
        if ($_GET['v'] == 'tasks') {
          // prepare SQL to get only assessment tasks
          // returns the date of post, due date of assignments, type of post, teachers name (prefix and lastName e.g. Mr, Smith),
          //     the teachers photo, the classes' ID and the class's name
          $postSQL = "SELECT p.date, p.due, p.text, p.type, p.postID, ui.prefix, ui.lastName, ui.photo, ui.link, g.groupID, g.groupName FROM
                        (posts AS p RIGHT JOIN userInfo AS ui ON p.`userID` = ui.`userID`)
                        RIGHT JOIN groups AS g ON p.`groupID` = g.`groupID`
                      WHERE
                      	p.`groupID` IN (SELECT groupID FROM userGroups WHERE userID='".$userInfo['userID']."')
                        AND p.`type` = 'a'
                      ORDER BY p.date DESC;";
        } else {
          // prepareSQL to get all posts from relevant groups
          // returns the date of post, due date of assignments, type of post, teachers name (prefix and lastName e.g. Mr, Smith),
          //     the teachers photo, the classes' ID and the class's name
          $postSQL = "SELECT p.date, p.due, p.text, p.type, p.postID, ui.prefix, ui.lastName, ui.photo, ui.link, g.groupName, g.groupLink FROM
                        (posts AS p RIGHT JOIN userInfo AS ui ON p.`userID` = ui.`userID`)
                        RIGHT JOIN groups AS g ON p.`groupID` = g.`groupID`
                      WHERE
                      	p.`groupID` IN (SELECT groupID FROM userGroups WHERE userID='".$userInfo['userID']."')
                      ORDER BY p.date DESC;";
        }

        // query the database with relevant query
        $postQuery = $conn->query($postSQL);

        // prepare an output
        $output = '';

        // if there are posts --> loop through all posts individually
        if ($postQuery->num_rows > 0) {
          while($post = $postQuery->fetch_assoc()) {

            // get the type of the post (e.g. assignment or text)
            $type = $post['type'];
            if($type === 't') {
              // output of regular text post
              $output .= '<div class="post"><div class="post-header"><div class="pull-left">
                        <img src="'.$post["photo"].'" alt="Profile photo of '.$post["prefix"].' '.$post["lastName"].'">
                        <div class="post-image-container"><span class="post-title"><a class="post-title-teacher" href="user/'.$post['link'].'">'.$post["prefix"]
                        .' '.$post["lastName"].'</a> to <a href="group/'.$post["groupLink"].'">'.$post["groupName"]
                        .'</a></span><span class="post-date">'.timeToWords($post['date']).'
                        </span></div></div></div><div class="post-content"><p>'.$post['text'].'</p><a href="" id="post-comment-'
                        .$post['postID'].'" class="post-content-comment">Add Comment</a></div></div>';
            } else if($type === 'a'){
              // output of an assignment post
              $output .= '<div class="post post-assignment"><div class="post-header"><div class="pull-left">
                        <img src="'.$post["photo"].'" alt="Profile photo of '.$post["prefix"].' '.$post["lastName"].'">
                        <div class="post-image-container"><span class="post-title"><a class="post-title-teacher" href="user/'.$post['link'].'">'
                        .$post["prefix"].' '.$post["lastName"].'</a> to <a href="group/'.$post["groupLink"].'">'.$post["groupName"]
                        .'</a></span><span class="post-date">'.timeToWords($post['date']).'
                        </span></div></div><div class="pull-right"><div class="assignment-indicator ai-0">Due
                        '.timeToWords($post['due']).'</div></div></div>
                        <div class="post-content"><p>'.$post['text'].'</p><a href="" id="post-handin-'
                        .$post['postID'].'" class="post-assignment-handin">Hand In</a><a href="" id="post-comment-'
                        .$post['postID'].'" class="post-content-comment">Add Comment</a></div></div>';
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

    </div>

    <script type="text/javascript">

      function updateLastOnline() {
        var request;
        if (window.XMLHttpRequest) {
          request = new XMLHttpRequest();
        } else {
          request = new ActiveXObject("Microsoft.XMLHTTP");
        }
        request.open('GET', 'php/lastOnline.php', true);
        request.send();

        setTimeout(updateLastOnline, 58000);
      }
      updateLastOnline();

    </script>
  </body>
</html>
