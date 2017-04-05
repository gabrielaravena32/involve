<?php

// include the connection script for database
include_once "connect.php";

// store all the variables as mysql safe strings
$userHash = $conn->real_escape_string($_GET['u']);
$classCode = $conn->real_escape_string($_GET['c']);

// find the user account trying to join a class
$userSearchQuery = $conn->query("SELECT userID FROM userInfo WHERE link = '{$userHash}' LIMIT 1;");

// if there is a user
if($userSearchQuery->num_rows == 1) {
  $userID = $userSearchQuery->fetch_assoc()['userID'];

  // select the groupID where the user is not the teacher or a student already in that class
  $groupSearchQuery = $conn->query("SELECT groupID FROM groups
                                      WHERE
                                        groupAccessCode = '{$classCode}'
                                        AND
                                        teacherID != {$userID}
                                        AND
                                        groupID NOT IN (SELECT groupID FROM userGroups WHERE userID = {$userID})
                                      LIMIT 1;");

  // if there is a group
  if($groupSearchQuery->num_rows == 1) {
    $groupID = $groupSearchQuery->fetch_assoc()['groupID'];

    $addUserToClass = $conn->query("INSERT INTO userGroups (userID, groupID) VALUES ({$userID}, {$groupID});");

    echo 0;

  // no group with that group access code
  } else {
    echo 2;
  }

// no user with that userHash
} else {
  echo 1;
}

?>
