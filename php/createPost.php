<?php

include_once "connect.php";

// get all the information passed to the php file
$hash = $conn->real_escape_string($_GET['h']);
$text = $conn->real_escape_string($_GET['t']);
$groupHash = $conn->real_escape_string($_GET['g']);
$type = $conn->real_escape_string($_GET['a']);
$due = $conn->real_escape_string($_GET['d']);
$userHash = $conn->real_escape_string($_GET['u']);
$title = $conn->real_escape_string($_GET['ti']);

// if there is a hash (for determining all the uploaded files) and a groupHash
if ($hash && $groupHash) {
  // get any attachments that have been added with the above hash
  $getAttachments = $conn->query("SELECT tempFileID, userID, tempFileLink, tempFileName, tempFileType
                                    FROM tempFiles
                                    WHERE tempPostHash = '{$hash}';");

  $attachments = [];
  $atd = '';
  // if there are attachments
  if($getAttachments->num_rows > 0) {
    // for each value add it to the attachments array
    while($att = $getAttachments->fetch_assoc()) {
      $userID = $att['userID']; // also get the user's ID
      $atd .= $att['tempFileID'].','; // add its id to a list seperated by commas
      $attachments[] = [$att['tempFileLink'], $att['tempFileName'], $att['tempFileType']];
    }
    $atd = substr($atd, 0, -1); // remove the final hanging comma

  // else: no attachments
  } else {
    // get the user's ID or return false
    $getUserID = $conn->query("SELECT userID FROM userInfo WHERE link='{$userHash}' LIMIT 1;");
    if($getUserID->num_rows > 0) {
      $userID = $getUserID->fetch_assoc()['userID'];
    } else {
      echo 0;
      return;
    }
  }

  // the post is an assignment
  if ($type) {
    // assignements need a due date
    if ($due && $title) {
      // get the due date in the format yyyy-mm-dd hh:mm:ss
      $dueDate = substr($due, 0, 4).'-'.substr($due, 5, 2).'-'.substr($due, 8, 2).' 23:59:59';

      // sql to add the assignment to the posts table
      $sql = "INSERT INTO posts (userID, groupID, type, aName, due, text) VALUES ({$userID}, (SELECT groupID FROM groups WHERE groupLink = '{$groupHash}' LIMIT 1), 'a', '{$title}', timestamp'{$dueDate}', '{$text}');";
    }
  // else: the post is just text
  } else {
    // sql to add the text post to the posts table
    $sql = "INSERT INTO posts (userID, groupID, text) VALUES ({$userID}, (SELECT groupID FROM groups WHERE groupLink = '{$groupHash}' LIMIT 1), '{$text}');";
  }

  // run the sql created above
  $query = $conn->query($sql);

  // get the postID for the post created
  $getPostID = $conn->query("SELECT postID, postHash FROM posts WHERE userID={$userID} AND groupID = (SELECT groupID FROM groups WHERE groupLink = '{$groupHash}' LIMIT 1) AND text = '{$text}' ORDER BY date DESC LIMIT 1;");
  if($getPostID->num_rows > 0) {
    $post = $getPostID->fetch_assoc();
    $postID = $post['postID'];
    $postHash = $post['postHash'];
  }

  // if there are any attachments
  if(count($attachments) > 0) {

    // add all the files to the files database with the correct postID
    $attachFilesSql = "INSERT INTO files (fileName, fileExtUrl, fileType, originalOwner) VALUES";

    $linkPostandFileSql = [];

    // append to the sql each attachments information
    // set up an sql to get the attachment's id
    foreach($attachments as $att) {
      $attachFilesSql .= " ('{$att[1]}', '{$att[0]}', '{$att[2]}', {$userID}),";

      // the sql to get the file ID
      $linkPostandFileSql[] = "SELECT fileID FROM files WHERE fileName = '{$att[1]}' AND fileExtUrl = '{$att[0]}' AND fileType = '{$att[2]}' ORDER BY createdDate DESC LIMIT 1";
    }

    // remove the final ','
    $attachFilesSql = substr($attachFilesSql, 0, -1);

    // finish the query
    $attachFilesSql .= ";";

    // run the query
    $addAttachments = $conn->query($attachFilesSql);

    // remove the files from the temp files table
    $removeFiles = $conn->query("DELETE FROM tempFiles WHERE tempFileID IN ({$atd});");

    // set up the connection query
    $connectThem = "INSERT INTO attachments (postID, fileID) VALUES";

    // link each file to the post
    foreach($linkPostandFileSql AS $sql) {
      $connectThem .= " ({$postID}, ({$sql})),";
    }

    // remove the trailing ','
    $connectThem = substr($connectThem, 0, -1);

    // run the sql
    $query = $conn->query($connectThem.";");

    echo $postHash;
    return;

  } else {
    echo $postHash;
    return;
  }

}

echo 0;

?>
