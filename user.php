<?php

// include the connect script
include_once "php/connect.php";

// create a blank path string
$path = '';

// get the requested URL ending
// e.g. if user typed website.com/user/userCode/
//      it would be 'user/userCode/'
$url = $_SERVER['REQUEST_URI'];

// get the page link
$pageLink = $conn->real_escape_string($_GET['uid']);

// check whether the last character is a '/'
if(substr($url,-1) === '/') {

  // check whether the 8 characters before the / are the user code
  if(substr($url,(strlen($pageLink)*-1)-1,-1) === $pageLink) {
    // user/userCode/
    // so send the user to home.php which is two directories back
    $path = '../../';
  } else {
    // user/
    // so sen the user to home.php which is one directory back
    $path = '../';
  }
} else {

  // check whether the last 9 characters are /userCode
  if(substr($url,(strlen($pageLink)*-1)-1) === "/".$pageLink) {
    // user/userCode
    // so send the user to home.php which is one directory back
    $path = '../';
  } else {
    // user.php?uid=userCode and user
    // both cases the home.php file is in the same directory (send them there)
    $path = '.';
  }
}

// include the redirect script
include_once "php/redirect.php";

// --- get the information for the profile to be displayed on the page
// set up a variable for the page user's information
$pageInfo = [];

// get the inputted user link from the page URL
$pageLink = $conn->real_escape_string($_GET['uid']);

// boolean determining whether the page is the user's
$selfPage = false;

// if the page link is equal to self (e.g. the URL is 'user/' or 'user')
if($pageLink === 'self') {
  // set the page link to the current user's link
  $pageLink = $userInfo['link'];
  $selfPage = true;
}

// search database for user with the correct link
$sql = "SELECT u.type, ui.*,
        CASE
          WHEN ui.school > 0 THEN (SELECT s.schoolName FROM schools AS s WHERE s.id = ui.school)
          ELSE ''
        END AS schoolName
        FROM userInfo AS ui RIGHT JOIN users AS u ON ui.userID=u.userID
        WHERE ui.link='{$pageLink}';";
$result = $conn->query($sql);

// if there is not one row returned - the user with that link does not exist
if ($result->num_rows != 1) {

  echo 'user doesn\'t exist';

// else: a user was found with that userID
} else {

  // get the information associated with the user of this page
  $pageInfo = $result->fetch_assoc();

  // if the user is a teacher
  if($pageInfo['type'] == 't') {
    // create a teacher name (Prefix + Last name)
    $pageInfo['name'] = $pageInfo['prefix'].' '.$pageInfo['lastName'];
  // else the user is a student
  } else {
    // create a student name (First + Last name)
    $pageInfo['name'] = $pageInfo['firstName'].' '.$pageInfo['lastName'];
  }

  // start the output for the HTML
  $output = '<!DOCTYPE html>
            <html>
              <head>
                <meta charset="utf-8">
                <title>Involve | '.$pageInfo['name'].'</title>';

  // INTERESTING READ ABOUT URLS
  // All files are called relative to the URL, this
  // means that all CSS, images, scripts and other
  // files will be called relative to the URL.
  //
  // E.g.
  //   calling css/index.css
  //   from index.php
  //   would go to the URL css/index.css
  //   however...
  //   calling css/index.css
  //   from user/gabriel.php
  //   would go to the URL user/css/index.css
  //
  // This is a problem because the user.php file can
  // be accessed through many different URLs:
  //   - user/userCode/
  //   - user/userCode
  //   - user/         -> defaults to own profile
  //   - user          -> defaults to own profile
  //   - user.php?uid=userCode
  //
  // To solve this I need to determine which way
  // the user is accessing this page and then add
  // a <base href=""> value depending.

  // get what the user typed into the URL
  // e.g. if user typed website.com/user/userCode/
  //      it would be 'user/userCode/'
  $output = '<base href="'.$path.'" />';

  // add to the output the end of the head and body element
  $output .= '<link rel="stylesheet" href="css/user.css"></head><body>';

  // print the output before the include function is called
  echo $output;

  // ----- SIDEBAR

  // include the sidebar (from different php file)
  include_once "includes/sidebar-left.php";

  // ----- USER BANNER

  // start adding to output again (starting with the content)
  $output = '<div class="content" id="content">';

  if($pageInfo['bannerPhoto']) {
    // display the banner image
    $output .= '<div class="user-banner" style="background-image: url('.$pageInfo['bannerPhoto'].')"></div>';

  // else: the user does not have a banner image
  } else {
    // make a random colour from one of the 16 choices available based on
    // the userID (modulous 16 means that it will map to 0-15 which we add one to)
    $consistentRandomNum = ($pageInfo['userID'] % 16) + 1;
    // display the banner with a 'random' background colour (consistent across reloads)
    $output .= '<div class="user-banner class-colour-'.$consistentRandomNum.'"></div>';
  }

  // ----- USER INFORMATION

  $output .= '<div class="user-information">';

  // output the person's profile picture
  $output .= '<img src="'.$pageInfo['photo'].'" alt="'.$pageInfo['name'].'\'s profile photo">';

  // output the person's first and last name
  $output .= '<h2>'.$pageInfo['name'].'</h2>';


  // if the person has a registered school
  if ($pageInfo['schoolName'] != '') {
    // if the person is a student
    if($pageInfo['type'] === 's') {

      // output to screen 'student at ...'
      $output .= '<h3>Student at '.$pageInfo['schoolName'].'</h3>';

    // else the person is a teacher
    } else {

      // output to screen 'teacher at ...'
      $output .= '<h3>Teacher at '.$pageInfo['schoolName'].'</h3>';
    }

  // else the person does not have a registered school
  } else {
    // if the person is a student
    if($pageInfo['type'] === 's') {

      // output to the screen 'student'
      $output .= '<h3>Student</h3>';

    // else the person is a teacher
    } else {

      // output to the screen 'teacher'
      $output .= '<h3>Teacher</h3>';
    }
  }

  // if the page is the user logged ins
  if($pageLink === $userInfo['link'] || $selfPage) {

    // add a edit profile button
    $output .= '<a href="settings/account">Edit Profile</a>';

  // else another user's profile
  } else {

    // add a button to message and a button to request schedule
    $output .= '<a href="messages/'.$pageInfo['link'].'">Send Message</a><a href="">Request Schedule</a>';
  }

  // end the user information div
  $output .= '</div>';

  // ------ USER TIMETABLE / CALENDAR

  // function returns the timestamp of the start of a day given the day as a string
  // e.g. "6 Jan 2017" --> 1483657200
  function getDayAsTimestamp($day) {
    $timestamp = new Datetime($day, new Datetimezone('Australia/Sydney'));
    $timestamp = strtotime($timestamp->format('Y-m-d H:i:s') . PHP_EOL);
    return $timestamp;
  }

  // get the current day as a timestamp (e.g. midnight of the previous day or 0am of today)
  $currentDay = getDayAsTimestamp('today');

  // number of days in the current month
  $numDaysInThisMonth = date('t', $currentDay);

  // day of the month
  $dayOfTheMonth = date('j', $currentDay);

  // number of days left = number of days in the month minus the day we are up to
  $numDaysLeftInMonth = $numDaysInThisMonth - $dayOfTheMonth;

  // get the first day of the month as a DateTime object
  $d = new DateTime('first day of this month', new Datetimezone('Australia/Sydney'));

  // convert the DateTime Object into a string "Month Year"
  $monthText = $d->format('F Y');

  // get the weekday of the first day of the month where:
  // Sunday = 0 ... Sat = 6
  $dayOffset = $d->format('w');

  // first saturday of the month
  $firstSaturdayOfMonth = date('j',strtotime('First Saturday of '.$monthText));

  // first sunday of the month
  $firstSundayOfMonth = date('j',strtotime('First Sunday of '.$monthText));

  // sql query to get the start date of all corresponding classes, repeat interval
  //     of said classes, and the number of periods in a day as expected by the
  //     school of the user who's profile it is
  $timetableItemsSQL = "SELECT gt.startDate, gt.repeatInterval, s.numPeriodsNormalDay FROM groupsTimetable AS gt
                        RIGHT JOIN schools AS s ON s.id = {$pageInfo['school']}
                        WHERE groupID IN
                          (SELECT groupID FROM groups
                            WHERE teacherID = {$pageInfo['userID']} OR groupID IN
                            (SELECT groupID FROM userGroups
                              WHERE userID = {$pageInfo['userID']}))
                        AND ((startDate - {$currentDay}) % repeatInterval = 0
                            OR (startDate - 3600 - {$currentDay}) % repeatInterval = 0
                            OR (startDate + 3600 - {$currentDay}) % repeatInterval = 0
                            OR (startDate - {$currentDay}) % repeatInterval + repeatInterval < 86400*{$numDaysLeftInMonth}
                            OR (startDate - 3600 - {$currentDay}) % repeatInterval + repeatInterval < 86400*{$numDaysLeftInMonth}
                            OR (startDate + 3600 - {$currentDay}) % repeatInterval + repeatInterval < 86400*{$numDaysLeftInMonth});";

  // run the sql query above
  $timetableItemsQuery = $conn->query($timetableItemsSQL);

  // create an empty array of classes for the user
  $classes = [];

  // if there are any rows in the database found go through each as $row
  if ($timetableItemsQuery->num_rows > 0) {
    while($row = $timetableItemsQuery->fetch_assoc()) {

      // for each row append it to the end of the classes array so that it becomes
      // a two dimensional array of all the classes for the user
      $classes[] = $row;
    }
  }

  // create an output with the first part of the calendar
  // where $dayOffset is being used is a means of moving the first day of the month
  // to the correct day of the week (Sunday does not need to be offset which works
  // because 0 * 30px is 0px and Saturday will be offset by 6*30px)
  $output .= '<div class="user-sidebar">
    <div class="user-timetable">
      <h4>'.$monthText.'</h4>
      <div class="user-timetable-dates">
        <div class="user-timetable-weekdays">
          <span>Sun</span>
          <span>Mon</span>
          <span>Tue</span>
          <span>Wed</span>
          <span>Thu</span>
          <span>Fri</span>
          <span>Sat</span>
        </div>
        <div id="timetable-dates-flex">
          <div class="timetable-offset" style="width: calc('.$dayOffset.' * 30px);"></div>';

  // for each day in the month
  for ($i = 1; $i <= $numDaysInThisMonth; $i++) {

    // set the class to be inputted to nothing (if weekend)
    $dayClass = '';

    // if day is NOT a weekend (saturday or sunday)
    if (($i - $firstSaturdayOfMonth) % 7 != 0 && ($i - $firstSundayOfMonth) % 7 != 0) {
      // get the timestamp for the day
      $ts = getDayAsTimestamp($i.' '.$monthText);

      // number of classes found on each day
      $classToday = 0;

      // value to place into calendar - default to free if weekday
      $dayClass = 'free';

      // for all the classes the teacher takes part in
      for ($j = 0; $j < count($classes); $j++) {
        // if the class is on that day
        if(($classes[$j]['startDate'] - $ts) % $classes[$j]['repeatInterval'] == 0 || ($classes[$j]['startDate'] - 3600 - $ts) % $classes[$j]['repeatInterval'] == 0 || ($classes[$j]['startDate'] + 3600 - $ts) % $classes[$j]['repeatInterval'] == 0) {

          // add one to the counter of classes today
          $classToday += 1;
        }
        // if the for loop is up to the last class (just finished checking it)
        if($j == count($classes) -1) {
          // if the classToday counter is greater than or equal to the normal amount
          // of periods prescribed by the school then the teacher is 'busy' otherwise
          // they are shown to be 'free', meaning they have a free period
          if($classToday >= $classes[$j]['numPeriodsNormalDay']) {
            // change the day class to busy
            $dayClass = 'busy';
          }
        }
      }
    }

    // if the day of the month is the current day add a selected class
    if ($i == $dayOfTheMonth) {
      $output .= '<div class="timetable-date selected '.$dayClass.'"><span>'.$i.'</span></div>';

    // else do a regular day of the calendar
    } else {
      $output .= '<div class="timetable-date '.$dayClass.'"><span>'.$i.'</span></div>';
    }
  }

  // get the number of extra days needed to fill in the calendar
  $numberOfExtraDays = 7 - (($dayOffset + $numDaysInThisMonth) % 7);

  // for each of those days add a disabled number
  for ($i = 1; $i <= $numberOfExtraDays; $i++) {
    $output .= '<div class="timetable-date disabled"><span>'.$i.'</span></div>';
  }

  // close off any unfinished divs
  $output .= '</div><div class="user-timetable-key">
                <span>= has a free period</span>
                <span>= busy all periods</span>
              </div></div></div></div>
              <div class="user-content">';

  // ----- TEACHER'S PANEL

  // create an array of the teacher's already in the table (identified by their link)
  // e.g. ['c4ca4238', 'c81e728d']
  $teachers = [];

  // create an array of the subject's already in the table (identified by their name)
  // e.g. ['SDD', 'English Advanced']
  $subjects = [];

  // SQL to get the group's name, colour, link, the teacher's name (as prefix +
  // last name), the teacher's photo and the number of members in the group
  $userInfoSQL =   "SELECT g.groupName, g.groupColour, g.groupLink, g.groupSubject,
                  g.teacherID, CONCAT(ui.prefix, ' ', ui.lastName) AS teacherName,
                  ui.photo AS teacherPhoto,
                  ui.link AS teacherLink,
                  (SELECT COUNT(userID) FROM userGroups AS ug WHERE ug.groupID = g.groupID) AS numGroupMembers
                  FROM groups AS g
                  RIGHT JOIN userInfo AS ui ON ui.userID = g.teacherID
                  WHERE
                    g.groupID IN (SELECT groupID FROM userGroups AS ug WHERE ug.userID = {$pageInfo['userID']})
                    OR g.teacherID = {$pageInfo['userID']};";

  // run the SQL query above
  $userInfoQuery = $conn->query($userInfoSQL);

  // integer value for the number of rows return (how many classes)
  $numberOfRows = $userInfoQuery->num_rows;

  // if there are any rows (meaning the user has a class)
  if($numberOfRows > 0) {

    // setup the HTML container for the classes to be displayed in
    $output .= '<div class="user-content-classes"><h3>Classes</h3>
                <p>'.$pageInfo['name'].' has '.$numberOfRows.' class';

    // if there is more than one class (add an 'es' to the end of class - to make
    // the sentence make grammatical sense)
    if($numberOfRows > 1) { $output .= 'es'; }

    // finish the starting HTML container
    $output .= ' at the moment.</p>
                <div class="classes-wrapper">';

    // for each class
    while($class = $userInfoQuery->fetch_assoc()) {
      // add the follow output (customised per class)
      $output .= '<a href="group/'.$class['groupLink'].'" class="class-colour-'.$class['groupColour'].'">
                    <h5>'.$class['groupName'].'</h5>
                    <span><span>Teacher:</span>'.$class['teacherName'].'</span>
                    <span><span>Students:</span>'.$class['numGroupMembers'].'</span>
                  </a>';

      // if the page belongs to a teacher (so look for subjects)
      if ($pageInfo['type'] == 't') {
        if($class['teacherID'] == $pageInfo['userID']) {
          if($subjects[$class['groupSubject']]) {
            $subjects[$class['groupSubject']]['numGroupMembers'] += $class['numGroupMembers'];
          } else {
            $subjects[$class['groupSubject']] = $class;
          }
        }

      // it is a students page (so look for teachers)
      } else {

        if($teachers[$class['teacherLink']]) {
          $teachers[$class['teacherLink']]['groupSubject'] .= ' and '.$class['groupSubject'];
        } else {
          $teachers[$class['teacherLink']] = $class;
        }
      }
    }
    // finish the HTML element
    $output .= '</div></div>';

  // else: there were no classes for the user
  } else {
    // output a statment saying such
    $output .= '<div class="user-content-classes"><h3>Classes</h3>
                <p>'.$pageInfo['name'].' currently does not have any classes.
                </p>
              </div>';
  }

  // if the user is a teacher
  if ($pageInfo['type'] == 't') {
    // if the teacher has subject's they have taught (retrieved from the database
    // in the previous SQL query)
    if($subjects) {
    // the number of unique subjects
      $numSubjects = count($subjects);

      // add to the output the start of the HTML element for subjects
      $output .= '<div class="user-content-subjects">
              <h3>Students</h3>
              <p>'.$pageInfo['name'].' currently teaches '.$numSubjects;

      // add an 'es' to the end of class to make it grammatically correct if needed
      if ($numSubjects > 1) {
        $output .= ' different subjects.';
      } else {
        $output .= ' subject.';
      }

      // finish the start of the HTML element for subjects
      $output .= '</p>
                  <div class="subjects-wrapper">';

      foreach($subjects as $subject) {
        $output .= '<div>
                      <h5>'.$subject['numGroupMembers'].'</h5>
                      <span>students for <span>'.$subject['groupSubject'].'</span></span>
                    </div>';
      }

      $output .= '</div></div>';

    // else: the teacher does not teach any subjects
    } else {
      // reflect this in the HTML
      $output .= '<div class="user-content-subjects">
                    <h3>Subjects</h3>
                    <p>'.$pageInfo['name'].' teaches no subjects.</p>
                  </div>';
    }

  // else: the user is a student
  // if the user has teachers (retrieved from database in previous SQL query)
  } else if ($teachers) {
    // the number of unique teachers
    $numTeachers = count($teachers);

    // add to the output the start of the HTML element for teachers
    $output .= '<div class="user-content-teachers">
            <h3>Teachers</h3>
            <p>'.$pageInfo['name'].' currently has '.$numTeachers.' teacher';

    // add an 's' to the end of teacher to make it grammatically correct if needed
    if ($numTeachers > 1) { $output .= 's'; }

    // finish the start of the HTML element for teachers
    $output .= '.</p>
                <div class="teachers-wrapper">';

    // add the HTML elements for teachers created above in the while loop
    foreach($teachers as $teacher) {
      // prepare the output for the teacher's section
      $output .= '<a href="user/'.$teacher['teacherLink'].'">
                    <img src="'.$teacher['teacherPhoto'].'" alt="'.$teacher['teacherName'].'\'s photo">
                    <span>'.$teacher['teacherName'].'</span>
                    <span>Teaches '.$teacher['groupSubject'].'</span>
                  </a>';
    }

    // end the HTML element for teachers
    $output .= '</div></div>';

  // else: the user is a student and doesn't have any teachers
  } else {
    // reflect this in the output
    $output .= '<div class="user-content-teachers">
                  <h3>Teachers</h3>
                  <p>'.$pageInfo['name'].' has no teachers.</p>
                </div>';
  }

  // finish the HTML
  $output .= '</div></div></body></html>';

  // print the output to the screen
  echo $output;
}
?>
