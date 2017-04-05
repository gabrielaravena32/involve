<?php

$output = '';

include_once "connect.php";

$ph = $conn->real_escape_string($_GET['ph']); // the post's hash
$uh = $conn->real_escape_string($_GET['uh']); // the users's hash


// function to return the the title of a webpage given a URL
// needed because link's can have dynamic titles
// Credit: http://stackoverflow.com/questions/4348912/get-title-of-website-via-link
function get_title($url){
  $str = file_get_contents($url);
  if(strlen($str)>0){
    $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
    preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
    return $title[1];
  }
}

// Function to convert a mysql Date object into a more different format (given the date)
// Result: 2016-12-29 18:13:01 --> Tomorrow at 6:13pm or 2016-12-30 02:30:00 --> Today at 2:30am
function timeToWords($d) {
  // convert the date into a unix timestamp (seconds)
  $d = strtotime($d);

  // create a time for 'today' at midnight (e.g. beginning of the day within no hours and seconds)
  $currentDay = new Datetime('today', new Datetimezone('Australia/Sydney'));
  $currentDay = strtotime($currentDay->format('Y-m-d H:i:s') . PHP_EOL);

  // if the date is more than two days from the current morning (e.g. not today or tomorrow)
  // or if the date is reverse of that (not yesterday)
  if($d > $currentDay + (2*24*60*60) || $d < $currentDay - (24*60*60)) {
    // if the year is not the same
    if(date("Y",$d) != date("Y", $currentDay)) {
      return date("d M Y",$d);
    }
    return date("d M",$d);

    // the date is either yesterday, today or tomorrow
    // if the day has the same date as today
  } else if(date("Y-m-d", $d) == date("Y-m-d", $currentDay)) {
    return "Today ".date("g:ia",$d);

  // the date is either yesterday or today
  // if the date is greater than today (tomorrow)
  } else if($d > $currentDay) {
    return "Tomorrow ".date("g:ia", $d);
  }
  // the date can only be yesterday
  return "Yesterday ".date("g:ia", $d);
}


if($uh && $ph) {

  $userSQL = "SELECT userID, photo FROM userInfo WHERE link='{$uh}' LIMIT 1;";
  $userInfo = $conn->query($userSQL)->fetch_assoc();
  $userID = $userInfo['userID'];
  $userPhoto = $userInfo['photo'];

  $postsSQL =   "SELECT p.date, p.due, p.text, p.type, p.postID, p.postHash, ui.userID, ui.prefix, ui.firstName, ui.lastName, ui.photo, ui.link, g.groupName, g.groupLink FROM
                  (posts AS p RIGHT JOIN userInfo AS ui ON p.`userID` = ui.`userID`)
                  RIGHT JOIN groups AS g ON p.`groupID` = g.`groupID`
                WHERE p.`postHash` = '{$ph}'
                LIMIT 1;";

  // find any attachments to add to these posts
  $attachmentsSQL = "SELECT f.fileName, f.fileIntUrl, f.fileExtUrl, f.fileType
                      FROM
                        attachments AS a
                        RIGHT JOIN files AS f ON f.fileID = a.fileID
                      WHERE a.postID = (SELECT p.postID FROM posts AS p
                                        WHERE p.postHash = '{$ph}'
                                        LIMIT 1);";

  // query the database with the sql created above
  $postsQuery = $conn->query($postsSQL);

  if($postsQuery->num_rows > 0) {

    // query the database with the sql created above
    $attachmentsQuery = $conn->query($attachmentsSQL);

    // query the database for comments
    $commentQuery = $conn->query($commentSQL);

    $output = 'TODO: getPost.php';

  }
}

echo $output;

?>
