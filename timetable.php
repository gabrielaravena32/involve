<?php

// include the connect script
include_once "php/connect.php";


$path = '.';
// include the redirect script
include_once "php/redirect.php";

$navPageSel = 'timetable';

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Involve | Timetable</title>

    <link rel="stylesheet" href="css/timetable.css">
  </head>
  <body>

    <?php include_once "includes/sidebar-left.php"; ?>

    <!-- Page content -->
    <div class="content">

      <div id="timetable">
        <div class="timetable-day">Monday</div>
        <div class="timetable-day">Tuesday</div>
        <div class="timetable-day">Wednesday</div>
        <div class="timetable-day">Thursday</div>
        <div class="timetable-day">Friday</div>

<?php

// prepare an output to be echoed at the end
$output = '';

// function returns the timestamp of the start of a day given the day as a string
// e.g. "6 Jan 2017" --> 1483657200
function getDayAsTimestamp($day) {
  $timestamp = new Datetime($day, new Datetimezone('Australia/Sydney'));
  $timestamp = strtotime($timestamp->format('Y-m-d H:i:s') . PHP_EOL);
  return $timestamp;
}

// get the current day as a timestamp (e.g. midnight of the previous day or 0am of today)
$currentDay = getDayAsTimestamp('today');

// get the day of the week currently from Sunday (0) - Saturday (6)
$numDaysToLoad = date('w', $currentDay);

$numDaysPastMonday = 0;

// maps the following:
//    6    0    1    2    3    4    5
//    ↓    ↓    ↓    ↓    ↓    ↓    ↓
//    14   13   12   11   10   9    8
// which is the number of days that the SQL query needs to look ahead for to
// catch all the classes that will be displayed
if ($numDaysToLoad < 6) {
  $numDaysPastMonday = $numDaysToLoad - 1;
  $numDaysToLoad = 13 - $numDaysToLoad;
} else {
  $numDaysPastMonday = -2;
  $numDaysToLoad = 14;
}

// prepare the SQL to get all the classes within the time period shown on the timetable
$classesSQL =  "SELECT gt.startDate, gt.repeatInterval, gt.time, gt.length, g.groupSubject, g.groupColour, g.groupLink FROM groupsTimetable AS gt
                RIGHT JOIN groups AS g ON g.groupID = gt.groupID
                WHERE
                (g.teacherID = {$userInfo['userID']}
                	OR g.groupID IN
                    (SELECT groupID FROM userGroups WHERE userID = {$userInfo['userID']}))
                AND ((startDate - {$currentDay}) % repeatInterval = 0
                    OR (startDate - 3600 - {$currentDay}) % repeatInterval = 0
                    OR (startDate + 3600 - {$currentDay}) % repeatInterval = 0
                    OR (startDate - {$currentDay}) % repeatInterval + repeatInterval < 86400*{$numDaysToLoad}
                    OR (startDate - 3600 - {$currentDay}) % repeatInterval + repeatInterval < 86400*{$numDaysToLoad}
                    OR (startDate + 3600 - {$currentDay}) % repeatInterval + repeatInterval < 86400*{$numDaysToLoad})
                ORDER BY gt.time;";

// query database with above SQL
$classesQuery = $conn->query($classesSQL);

// array of classes that will fill timetable
$classes = [];

// add all the clases from the database to the array
if($classesQuery->num_rows > 0) {
  while($class = $classesQuery->fetch_assoc()) {
    $classes[] = $class;
  }
}

$firstMonday = date('j', $currentDay) - $numDaysPastMonday;

// get the first day of the month as a DateTime object
$d = new DateTime('first day of this month', new Datetimezone('Australia/Sydney'));

// convert the DateTime Object into a string "Month Year"
$month = $d->format('F');
$year = $d->format('Y');

// convert the DateTime Object into a string "Month" in three character form
$monthText = $d->format('M');

// get the number of days in this month
$numDaysInMonth = $d->format('t');

// prepare the day (so that on first loop it makes the right date)
$day = $firstMonday - 1;

for ($i = 0; $i < 12; $i++) {
  // add one to the day
  $day = $day + 1;
  // get the timestamp for the date to be displayed
  $ts = getDayAsTimestamp($day.' '.$month.' '.$year);
  // get the day of the week
  $dayOfWeek = date('N',$ts);

  // day is not a sunday or saturday
  if($dayOfWeek < 6) {

    if ($ts == $currentDay) {
      $output .= '<div class="timetable-date selected">
                    <span>'.$day.'<span>'.$monthText.'</span></span><div>';
    } else {
      $output .= '<div class="timetable-date">
                    <span>'.$day.'<span>'.$monthText.'</span></span><div>';
    }

    foreach($classes as $class) {
      if(($class['startDate'] - $ts) % $class['repeatInterval'] == 0 || ($class['startDate'] - 3600 - $ts) % $class['repeatInterval'] == 0 || ($class['startDate'] + 3600 - $ts) % $class['repeatInterval'] == 0) {
        $startTime = ($class['time'] / 3600) % 12;
        if($class['time'] / 3600 == 12) {
          $startTime = 12;
        }
        $endTime = ($startTime + ($class['length'] / 3600))% 12;
        if($startTime + ($class['length'] / 3600) == 12) {
          $endTime = 12;
        }

        $output .= '<a href="group/'.$class['groupLink'].'" class="timetable-periods class-colour-'.$class['groupColour'].'"><span>'.$startTime.' - '.$endTime.'</span><span>'.$class['groupSubject'].'</span></a>';
      }
    }

    if (substr($output, -4) != "</a>") {
      $output .= '</div><span class="day-empty">Your day is empty.</span></div>';
    } else {
      $output .= '</div></div>';
    }

  }

  // if the day is the last day of the month
  if($day == $numDaysInMonth) {
    // get the next month name
    $monthText = date('M',strtotime($month.' next month'));
    // change the month
    $month = date('F',strtotime($month.' next month'));
    // set the day to zero so when it is incremented next it will become 1st
    $day = 0;
  }

}

echo $output;

?>
      </div>
    </div>

  </body>
</html>
