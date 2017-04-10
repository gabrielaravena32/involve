<?php

// start the session (keep user logged in)
session_start();

// create an empty array to hold the current user's information
$userInfo = [];

// if the session doesn't have a token set (not logged in)
if (!$_SESSION['token']) {

  // destroy the session created at the top of the page and send them to home.php
  session_destroy();
  header("Location: ".$path);

// else: if the user is logged in
} else {
  // select the user's information from the database where the user's token is correct
  $sql = "SELECT ui.*, u.type, u.email FROM userInfo AS ui RIGHT JOIN users AS u ON u.userID = ui.userID WHERE u.token='{$_SESSION['token']}';";
  $result = $conn->query($sql);

  // if there is a user with that token
  if ($result->num_rows == 1) {
    // set the array $userInfo to hold the information (not stored in $_SESSION making it more secure)
    $userInfo = $result->fetch_assoc();

  // else if the user's stored token doesn't match any in the database
  } else {
    // destroy session data (token and any other information) and send them to home.php
    session_destroy();
    header("Location: ".$path);
  }
}

?>
