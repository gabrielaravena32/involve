<?php

// destroy the session of the user creating the new account
session_start();
session_destroy();

// include the connection script for database
include_once "connect.php";

// store all the variables as mysql safe strings
$email = $conn->real_escape_string($_REQUEST['e']);
$pwd = $conn->real_escape_string($_REQUEST['p']);
$first = $conn->real_escape_string($_REQUEST['f']);
$last = $conn->real_escape_string($_REQUEST['l']);
$type = $conn->real_escape_string($_REQUEST['t']);
$prefix = $conn->real_escape_string($_REQUEST['pr']);


// --- Email verification
// determine whether the email works (also protects against no email entered)
// Credit: http://stackoverflow.com/questions/5855811/how-to-validate-an-email-in-php
if(!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match('/@.+\./', $email)){
  // return the php file with 0 (meaning error)
  echo 0;
  return;
}
// prepare and run an sql query to determine if there are any other accounts registered with the email
$emailSQL = "SELECT userID FROM users WHERE email='".$email."';";
$emailQuery = $conn->query($emailSQL);
// if there are any rows return (meaning there is an account)
if ($emailQuery->num_rows > 0) {
  // return the php file with 0 (meaning error)
  echo 0;
  return;
}


// --- Password verification
// check if the password is too short or too long (protects against no password entered)
if (strlen($pwd) < 5 || strlen($pwd) > 18) {
  // return the php file with 0 (meaning error)
  echo 0;
  return;
}
// secure the password by hashing it
$pwdHash = password_hash($pwd, PASSWORD_BCRYPT, ['cost'=>10]);


// --- First and Last Name verification
// Make the first letter of every word capitalised
// Credit: http://php.net/manual/en/function.ucwords.php
function ucname($string) {
  $string =ucwords(strtolower($string));

  foreach (array("-", "\'", "'") as $delimiter) {
    if (strpos($string, $delimiter)!==false) {
      $string =implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
    }
  }
  return $string;
}
// e.g. john michael smith --> John Michael Smith
$first = ucname($first);
$last = ucname($last);
// create a regular expression to determine whether the variables are in the form of names
$re = "/^[a-z ,.'-]+$/i";
// test whether either of them is not in the correct form (also works if both are not)
// also protects against no name entered
if(!preg_match($re, $first) || !preg_match($re, $last)) {
  // return the php file with 0 (meaning error)
  echo 0;
  return;
}


// --- Type verification
// determine if the value of type is not t or s (protects against no type entered)
if (($type == 't') == 0 && ($type == 's') == 0) {
  // return the php file with 0 (meaning error)
  echo 0;
  return;
}


// --- Prefix verification
// determine whether prefix was entered if the user is a teacher
if($type == 't' && $prefix == "") {
  // return the php file with 0 (meaning error)
  echo 0;
  return;
}


// --- Create a token
// generate a randomised 60-digit token (to be used to verify user without storing password or easily modified user id)
$token = password_hash(bin2hex(openssl_random_pseudo_bytes(10)), PASSWORD_BCRYPT, ['cost'=>4]);


// --- Add account to database
$sql = "INSERT INTO users (email, password, type, token) VALUES ('{$email}', '{$pwdHash}', '{$type}', '{$token}');";
$query = $conn->query($sql);

// --- Get the userID of the new user
$sql2 = "SELECT userID FROM users WHERE token='{$token}';";
$result = $conn->query($sql2);
// set a placeholder
$id = 0;
// go through the results if there is one row
if ($result->num_rows == 1) {
  while($row = $result->fetch_assoc()) {
    // get the id of the new user
    $id = $row['userID'];
  }
}

$uiSQL = '';

// if the user is a teacher
if($type == 't') {
  // add the information (including prefix)
  $uiSQL = "INSERT INTO userInfo (userID, firstName, lastName, prefix, link) VALUES ({$id}, '{$first}', '{$last}', '{$prefix}', substring(MD5({$id}), 1, 8));";
// else: the user is a student
} else {
  // add the information (with a null prefix)
  $uiSQL = "INSERT INTO userInfo (userID, firstName, lastName, link) VALUES ({$id}, '{$first}', '{$last}', substring(MD5({$id}), 1, 8));";
}
$query = $conn->query($uiSQL);

// --- Log the user in
// start the session and set token (indicating user is logged in)
session_start();
$_SESSION['token'] = $token;
// return the php file with 1 (meaning the account has been created and user has been logged in)
echo 1;

?>
