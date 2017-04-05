<?php

include_once "connect.php";

$uHash = $conn->real_escape_string($_GET['u']); // the user's Hash
$pHash = $conn->real_escape_string($_GET['p']); // the post's unique identifier
$link = $conn->real_escape_string($_GET['l']); // the external link to the file
$name = $conn->real_escape_string($_GET['n']); // the file's name
$type = $conn->real_escape_string($_GET['t']); // the type of file

// if all the fields are provided
if($uHash && $pHash && $link && $name && $type) {

  // if the user exists with the inputted hash
  $query = $conn->query("SELECT userID FROM userInfo WHERE link='{$uHash}' LIMIT 1;");
  if($query->num_rows == 1) {

    // save the user's ID
    $uID = $query->fetch_assoc()['userID'];

    // query to put the information into the database
    $putFileSQL = "INSERT INTO tempFiles (userID, tempPostHash, tempFileLink, tempFileName, tempFileType) VALUES ({$uID}, '{$pHash}', '{$link}', '{$name}', '{$type}');";

    $request = $conn->query($putFileSQL);
    echo 1;
    return;
    
  } // else: invalid user Hash
} // else: insufficient information provided

echo 0;

?>
