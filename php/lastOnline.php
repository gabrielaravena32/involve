<?php

// start the session (keep user logged in)
session_start();

// include the connect script
include_once "connect.php";

// if the user has a token set
if($_SESSION['token']) {
  // update the user's last online where the token matches the user
  $sql = "UPDATE users SET lastOnline=CURRENT_TIMESTAMP WHERE token='{$_SESSION['token']}';";
  $result = $conn->query($sql);
}

?>
