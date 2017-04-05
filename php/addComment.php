<?php

include_once "connect.php";

// get all the information passed to the php file
$pHash = $conn->real_escape_string($_GET['p']);
$uHash = $conn->real_escape_string($_GET['u']);
$text = $conn->real_escape_string($_GET['t']);

// if all the information is correct
if($pHash && $uHash && $text) {
  // determine the userID of the user with corresponding hash
  $userQuery = $conn->query("SELECT userID FROM userInfo WHERE link='{$uHash}' LIMIT 1;");

  // if there is a user with the hash
  if($userQuery->num_rows > 0) {
    // get the user's ID
    $userID = $userQuery->fetch_assoc()['userID'];

    $addCommentQuery = $conn->query("INSERT INTO comments (postID, userID, commentText) VALUES ((SELECT postID FROM posts WHERE postHash='{$pHash}' LIMIT 1), {$userID}, '{$text}');");

    echo $text;
    return;
  }
}

echo 0;
?>
