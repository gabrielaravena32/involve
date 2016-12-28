<?php
session_start();
include_once "php/connect.php";

$userInfo = [];
if (!$_SESSION['token']) {
  session_destroy();
  header("Location: .");
} else {
  $sql = "SELECT * FROM userInfo WHERE userID=(SELECT userID FROM users WHERE token='".$_SESSION['token']."');";
  $result = $conn->query($sql);
  if ($result->num_rows == 1) {
    $userInfo = $result->fetch_assoc();
  } else {
    session_destroy();
    header("Location: .");
  }
}

// log out automatically
// $_SESSION['token'] = null;


function timeToWords($d) {
  $d = strtotime($d);
  if($d - (2*24*60*60) < time() && date("Y-m-d",$d) != date("Y-m-d",time())) {
    if($d - (24*60*60) < time()) {
      return "Today ".date("g:ia",$d);
    }
    return "Tomorrow ".date("g:ia",$d);
  }
  return date("d M",$d);
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Involve | Home</title>

    <link rel="stylesheet" href="css/home.css">
    <!-- Load in the home.js file found in /js folder -->
    <script src="js/home.js"></script>
  </head>
  <body>

    <!-- Navbar -->
    <?php include_once "includes/navbar.php"; ?>

    <div class="page">

      <?php include_once "includes/sidebar-left.php"; ?>

      <!-- Page content -->
      <div class="content">

        <!-- Posts -->
        <?php
        $postSQL = '';
        if ($_GET['v'] == 'tasks') {
          // prepare SQL to get only assessment tasks
          $postSQL = "SELECT * FROM
                        (posts AS p RIGHT JOIN userInfo AS ui ON p.`userID` = ui.`userID`)
                        RIGHT JOIN groups AS g ON p.`groupID` = g.`groupID`
                      WHERE
                      	p.`groupID` = (SELECT groupID FROM userGroups WHERE userID='".$userInfo['userID']."')
                        AND p.`type` = 'a'
                      ORDER BY p.date DESC;";
        } else {
          // prepareSQL to get all posts from relevant groups
          $postSQL = "SELECT * FROM
                        (posts AS p RIGHT JOIN userInfo AS ui ON p.`userID` = ui.`userID`)
                        RIGHT JOIN groups AS g ON p.`groupID` = g.`groupID`
                      WHERE
                      	p.`groupID` = (SELECT groupID FROM userGroups WHERE userID='".$userInfo['userID']."')
                      ORDER BY p.date DESC;";
        }
        // query the database with relevant query
        $postQuery = $conn->query($postSQL);

        // prepare an output
        $output = '';

        // loop through all posts
        if ($postQuery->num_rows > 0) {
          // for each post
          while($post = $postQuery->fetch_assoc()) {
            $type = $post['type'];
            if($type === 't') {
              // output of regular text post
              $output .= '<div class="post"><div class="post-header"><div class="pull-left">
                        <img src="'.$post["photo"].'" alt="Profile photo of '.$post["firstName"].'">
                        <div class="post-image-container"><span class="post-title">'.$post["prefix"].' '.$post["lastName"].'
                        <span> to <a href="groups?n='.$post["groupID"].'">'.$post["groupName"].'</a></span></span>
                        <span class="post-date">'.timeToWords($post['date']).'
                        </span></div></div></div><div class="post-content"><p>'.$post['text'].'</p><a href="" id="post-comment-'
                        .$post['postID'].'" class="post-content-comment">Add Comment</a></div></div>';
            } else if($type === 'a'){
              // output of an assignment post
              $output .= '<div class="post post-assignment"><div class="post-header"><div class="pull-left">
                        <img src="'.$post["photo"].'" alt="Profile photo of '.$post["firstName"].'">
                        <div class="post-image-container"><span class="post-title">'.$post["prefix"].' '.$post["lastName"].'
                        <span> to <a href="groups?n='.$post["groupID"].'">'.$post["groupName"].'</a></span></span>
                        <span class="post-date">'.timeToWords($post['date']).'
                        </span></div></div><div class="pull-right"><div class="assignment-indicator ai-0">Due
                        '.timeToWords($post['due']).'</div></div></div>
                        <div class="post-content"><p>'.$post['text'].'</p><a href="" id="post-handin-
                        '.$post['postID'].'" class="post-assignment-handin">Hand In</a><a href="" id="post-comment-'
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
  </body>
</html>
