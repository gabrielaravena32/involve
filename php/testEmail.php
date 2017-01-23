<?php

// include the connection script for database
include_once "connect.php";

// store the email as a mysql safe string
$email = $conn->real_escape_string($_REQUEST['e']);

// prepare an sql query to determine if there are any other accounts registered with the email
$sql = "SELECT userID FROM users WHERE email='{$email}';";

// query the database
$emailQuery = $conn->query($sql);

// if there are any rows return (meaning there is an account)
if ($emailQuery->num_rows > 0) {

  // echo 0 (meaning invalid email)
  echo 0;
  return 0;

// else no rows
} else {

  // echo 1 (meaning valid email)
  echo 1;
  return 1;
}

?>
