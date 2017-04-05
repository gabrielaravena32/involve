<?php

// include the connection to database script
include_once "connect.php";

$uHash = $conn->real_escape_string($_GET['uh']);

// get the current date (with no hours or minutes or seconds) as an integer
$timestamp = new Datetime('today', new Datetimezone('Australia/Sydney'));
$timestamp = strtotime($timestamp->format('Y-m-d H:i:s') . PHP_EOL);

// get the current time as seconds
$now = new Datetime('now', new Datetimezone('Australia/Sydney'));
$now = strtotime($now->format('Y-m-d H:i:s') . PHP_EOL);

// get the time of the day (by removing $timestamp which is the date portion calculated above)
$time = $now - $timestamp;

// SQL query to select all the classes that the user has that day
// returns the className, time class starts, length of class, and the prefix and last name of teacher
$nextClass = $conn->query("SELECT g.groupName, gt.length, ui.prefix, ui.lastName, ({$time} - gt.time) AS progress FROM
                            (groups AS g RIGHT JOIN groupsTimetable AS gt ON g.groupID=gt.groupID)
                            RIGHT JOIN userInfo AS ui ON g.teacherID=ui.userID
                            WHERE
                              ((gt.startDate - {$timestamp}) % gt.repeatInterval = 0)
                              AND gt.time + gt.length > {$time}
                              AND gt.time < {$time}
                              AND g.groupID IN
                                (SELECT ug.groupID FROM userGroups AS ug
                                  WHERE ug.userID=
                                    (SELECT userID FROM userInfo
                                      WHERE link='{$uHash}'))
                            LIMIT 1;");

if($nextClass->num_rows > 0) {
  $output = $nextClass->fetch_assoc();
  $json = json_encode($output);
} else {
  $json = json_encode(0);
}
echo $json;

?>
