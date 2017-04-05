<?php

// include the connection script for database
include_once "connect.php";

// get the group hash and the user hash
$gh = $conn->real_escape_string($_GET['gh']);
$uh = $conn->real_escape_string($_GET['uh']);

// check whether the user hash is valid
$checkUH = $conn->query("SELECT userID FROM userInfo WHERE link='{$uh}' LIMIT 1;");
if($checkUH->num_rows === 1) {

  // get the user's ID
  $uid = $checkUH->fetch_assoc()['userID'];

  // check whether the group hash is valid
  $checkGH = $conn->query("SELECT groupID FROM groups WHERE groupLink='{$gh}' LIMIT 1;");
  if($checkGH->num_rows === 1) {

    //get the group's ID
    $gid = $checkGH->fetch_assoc()['groupID'];

    // check whether the user is already in the group or has requested to join already
    $checkUIG = $conn->query("SELECT
                                (SELECT COUNT(relationID) FROM userGroups
                                  WHERE userID=1 AND groupID=4)
                              +
                                (SELECT COUNT(id) FROM requestToJoinGroup
                                  WHERE userID=1 AND groupID=4)
                              > 0 AS numEntries;");
    // if the user was found in or requesting the group already
    if($checkUIG->fetch_assoc()['numEntries'] > 0) {
      echo 3;

    // else: add the user to the requestToJoinGroup database
    } else {

      // add the request
      $addRequest = $conn->query("INSERT INTO requestToJoinGroup (userID, groupID) VALUES ({$uid}, {$gid});");
      echo 0;

    }

  // else: invalid group hash
  } else {
    echo 2;
  }
// else: invalid user hash
} else {
  echo 1;
}


?>
