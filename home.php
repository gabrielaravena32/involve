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
  </head>
  <body>

    <!-- Navbar -->
    <?php include_once "includes/navbar.php"; ?>

    <div class="page">

      <?php include_once "sidebar-left"; ?>

      <!-- Page content -->
      <div class="content">

        <div id="post-view-selector">
          <a href="?v=all" id="post-view-selector-all">All Posts</a>
          <a href="?v=tasks" id="post-view-selector-tasks">Upcoming Tasks</a>
          <a href="" id="post-view-selector-subjects">Classes</a>
          <a href="" id="post-view-selector-topics">Subject Topics</a>
          <!-- can add more later -->
        </div>

        <!-- Posts -->
        <?php
        // query to get all the posts relevant to the user
        $postSQL = '';
        if ($_GET['v'] == 'tasks') {
          $postSQL = "SELECT * FROM
                        (posts AS p RIGHT JOIN userInfo AS ui ON p.`userID` = ui.`userID`)
                        RIGHT JOIN groups AS g ON p.`groupID` = g.`groupID`
                      WHERE
                      	p.`groupID` = (SELECT groupID FROM userGroups WHERE userID='".$userInfo['userID']."')
                        AND p.`type` = 'a'
                      ORDER BY p.date DESC;";
        } else {
          $postSQL = "SELECT * FROM
                        (posts AS p RIGHT JOIN userInfo AS ui ON p.`userID` = ui.`userID`)
                        RIGHT JOIN groups AS g ON p.`groupID` = g.`groupID`
                      WHERE
                      	p.`groupID` = (SELECT groupID FROM userGroups WHERE userID='".$userInfo['userID']."')
                      ORDER BY p.date DESC;";
        }
        $postQuery = $conn->query($postSQL);

        // prepare an output
        $output = '';

        // loop through all posts
        if ($postQuery->num_rows > 0) {
          // for each post
          while($post = $postQuery->fetch_assoc()) {
            $type = $post['type'];
            if($type === 't') {
              $output .= '<div class="post"><div class="post-header"><div class="pull-left">
                        <img src="'.$post["photo"].'" alt="Profile photo of '.$post["firstName"].'">
                        <div class="post-image-container"><span class="post-title">'.$post["prefix"].' '.$post["lastName"].'
                        <span> to <a href="groups?n='.$post["groupID"].'">'.$post["groupName"].'</a></span></span>
                        <span class="post-date">'.timeToWords($post['date']).'
                        </span></div></div></div><div class="post-content"><p>'.$post['text'].'</p><a href="" id="post-comment-'
                        .$post['postID'].'" class="post-content-comment">Add Comment</a></div></div>';
            } else if($type === 'a'){
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
        <!-- <div class="post">
          <div class="post-header">
            <div class="pull-left">
              <img src="images/default.png">
              <div class="post-image-container">
                <span class="post-title">Gabriel Aravena <span>to Physics</span></span>
                <span class="post-date">14th Dec</span>
              </div>
            </div>
          </div>
          <div class="post-content">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin pharetra eros ac dolor consectetur semper. Ut vel odio nibh. Nunc porta quis justo ac ullamcorper. Donec condimentum risus eu pharetra dictum. Cras euismod, turpis sed congue tempus, purus augue egestas odio, sed porta elit arcu in est. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <a href="#" class="post-content-comment">Add Comment</a>
          </div>
        </div>

        <div class="post post-assignment">
          <div class="post-header">
            <div class="pull-left">
              <img src="images/default.png">
              <div class="post-image-container">
                <span class="post-title">Mr Coombes <span>to English Extension 1</span></span>
                <span class="post-date">3 Dec</span>
              </div>
            </div>
            <div class="pull-right">
              <div class="assignment-indicator ai-0">Due 19 Nov</div>
            </div>
          </div>
          <div class="post-content">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin pharetra eros ac dolor consectetur semper. Ut vel odio nibh. Nunc porta quis justo ac ullamcorper. Donec condimentum risus eu pharetra dictum. Cras euismod, turpis sed congue tempus, purus augue egestas odio, sed porta elit arcu in est. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <a href="#" class="post-assignment-handin">Hand In</a>
          </div>
        </div> -->

        <!-- End with a load more -->

      </div>

      <!-- sidebar on right -->
      <?php include_once "includes/sidebar-right.php"; ?>

    </div>

  </body>
</html>
