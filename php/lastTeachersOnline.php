<?php

include_once "connect.php";

// get the current date (with no hours or minutes or seconds) as an integer
$timestamp = new Datetime('today', new Datetimezone('Australia/Sydney'));
$timestamp = strtotime($timestamp->format('Y-m-d H:i:s') . PHP_EOL);

// get the user ID
$userID = $_GET['uid'];

if($userID) {

  $output = '';

  // SQL query to find all the teachers (that user has a class with) that are currently in class
  // Returns the id of the teacher
  $teacherClassSQL = "SELECT g.teacherID
                        FROM groupsTimetable AS gt
                        RIGHT JOIN groups AS g ON g.groupID=gt.groupID
                        WHERE
                          ((gt.startDate - {$timestamp}) % gt.repeatInterval = 0)
                          AND gt.time - (HOUR(CURRENT_TIME)*60*60 + MINUTE(CURRENT_TIME)*60) < 0
                          AND ABS(gt.time - (HOUR(CURRENT_TIME)*60*60 + MINUTE(CURRENT_TIME)*60)) < gt.length
                          AND gt.groupID IN
                            (SELECT g.groupID FROM groups AS g WHERE g.groupID IN
                              (SELECT ug.groupID FROM userGroups AS ug
                                        WHERE ug.userID={$userID})
                          );";

  // query the database with the above SQL
  $teacherClassQuery = $conn->query($teacherClassSQL);

  // create an empty array to be populated by the teacher's IDs who are in class
  $teachersInClass = [];

  // if any teachers were found to be in class run through all rows retrieved
  if ($teacherClassQuery->num_rows > 0) {
    while($activeTeacher = $teacherClassQuery->fetch_assoc()) {

      // add the teacher's ID to the array
      $teachersInClass[] = $activeTeacher['teacherID'];
    }
  }

  // SQL query to determine the active status of relevant teachers to user (e.g. online, offline, in class)
  // Returns the teachers id, name (prefix + lastname e.g. Mr, Smith), the url to a profile photo, the last
  //    time the teacher was online and the current sever timestamp (used to calculate time since last online)
  $activeTeachersSQL = "SELECT ui.userID, ui.prefix, ui.lastName, ui.photo, u.lastOnline, ui.link,  CURRENT_TIMESTAMP 'currentTimestamp'
                          FROM users AS u
                          RIGHT JOIN userInfo AS ui
                              ON u.userID=ui.userID
                          WHERE u.userID IN
                              (SELECT g.teacherID
                              FROM groups AS g
                              WHERE g.groupID IN
                                  (SELECT ug.groupID
                                  FROM userGroups AS ug
                                  WHERE ug.userID = {$userID}))
                          ORDER BY u.lastOnline DESC
                          LIMIT 4;";

  // query the database with SQL query above
  $activeTeachersQuery = $conn->query($activeTeachersSQL);

  // if any rows were found in database
  if ($activeTeachersQuery->num_rows > 0) {

    // prepare the start of the HTML element
    $output .= '<div id="active-teachers">
                  <h3>Teachers</h3>
                  <div id="active-teachers-list">';

    // run through each row returned
    while($activeTeacher = $activeTeachersQuery->fetch_assoc()) {

      // create the element for each teacher
      $output .= '<div class="active-user">
                    <img src="'.$activeTeacher['photo'].'"/>
                    <div class="active-user-right">
                      <a href="user/'.$activeTeacher['link'].'">'.$activeTeacher['prefix'].' '.$activeTeacher['lastName'].'</a>';

      // if the teacher should currently be in a class (teacher's ID found in the array created earlier)
      if (in_array($activeTeacher['userID'], $teachersInClass)) {
        $output .= '<span>Currently in Class</span><div class="bubble bubble-yellow"></div>';

      // if the teacher was last seen online within 60 seconds --> teacher is online but not in class (available)
      } else if(strtotime($activeTeacher['currentTimestamp']) - strtotime($activeTeacher['lastOnline']) < 30) {
        $output .= '<span>Currently online</span><div class="bubble bubble-green"></div>';

      // else the teacher is not online (unavailable as far as application is concerned)
      } else {
        $output .= '<span>Unavailable</span><div class="bubble bubble-red"></div>';
      }

      // end the teacher's element
      $output .= '</div></div>';
    }

    // end the active teacher element
    $output .= '</div></div>';
  }
  echo $output;
}
?>
