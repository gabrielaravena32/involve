<?php

$navPageSel = 'classes';

// include the connect script
include_once "php/connect.php";

$path = '.';
// include the redirect script
include_once "php/redirect.php";

// convert date greater then or today to a readable format
function timeToWords($d) {
  // convert the date into a unix timestamp (seconds)
  $d = strtotime($d);

  // create a time for 'today' at midnight (e.g. beginning of the day within no hours and seconds)
  $currentDay = new Datetime('today', new Datetimezone('Australia/Sydney'));
  $currentDay = strtotime($currentDay->format('Y-m-d H:i:s') . PHP_EOL);

  // if the date is more than two days from the current morning (e.g. not today or tomorrow)
  if($d > $currentDay + (2*24*60*60)){
    return date("j M",$d);

    // if the day has the same date as today
  } else if(date("Y-m-d", $d) == date("Y-m-d", $currentDay)) {
    // the date is today
    return "Today";
  }
  // the date is tomorrow
  return "Tomorrow";
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Involve | Classes</title>

    <link rel="stylesheet" href="css/classes.css">
  </head>
  <body>

    <?php include_once "includes/sidebar-left.php"; ?>

    <!-- Page content -->
    <div class="content">
<?php

$classesSQL =  "SELECT g.groupID, g.groupName, g.groupColour, g.groupLink,
                g.teacherID, CONCAT(ui.prefix, ' ', ui.lastName) AS teacherName,
                ui.photo AS teacherPhoto,
                (SELECT COUNT(userID) FROM userGroups AS ug WHERE ug.groupID = g.groupID) AS numGroupMembers
                FROM groups AS g
                RIGHT JOIN userInfo AS ui ON ui.userID = g.teacherID
                WHERE
                  g.groupID IN (SELECT groupID FROM userGroups AS ug WHERE ug.userID = {$userInfo['userID']})
                  OR g.teacherID = {$userInfo['userID']};";

$classesQuery = $conn->query($classesSQL);

$assessmentSQL = "SELECT p.groupID, p.aName, p.due
                  FROM posts AS p
                  WHERE
                    (p.groupID IN (SELECT groupID FROM userGroups AS ug WHERE ug.userID = {$userInfo['userID']})
                      OR (SELECT g.teacherID FROM groups AS g WHERE g.groupID = p.groupID LIMIT 1) = {$userInfo['userID']})
                    AND p.type = 'a'
                    AND p.due > CURRENT_TIMESTAMP
                    ORDER BY p.due;";

$assessmentQuery = $conn->query($assessmentSQL);

if($assessmentQuery->num_rows > 0) {
  $assessments = [];
  while($ass = $assessmentQuery->fetch_assoc()) {
    $assessments[$ass['groupID']][] = [$ass['aName'], $ass['due']];
  }
}

if($classesQuery->num_rows > 0) {
  while($class = $classesQuery->fetch_assoc()) {

    // turn the number of students into a string with correct grammar
    if($class['numGroupMembers'] > 1) {
      $numMembers = $class['numGroupMembers'].' students';
    } else if($class['numGroupMembers'] == 1){
      $numMembers = '1 student';
    } else {
      $numMembers = 'No students';
    }

    // prepare the output
    $output .= "<a class='group class-colour-{$class['groupColour']}' href='group/{$class['groupLink']}'>
                  <div>
                    <h3>{$class['groupName']}</h3>
                    <h4>With {$class['teacherName']}&nbsp;&middot;&nbsp;{$numMembers}</h4>
                  </div>
                  <div>";

    if($assessments[$class['groupID']]) {
      foreach($assessments[$class['groupID']] as $ass) {
        $output .= '<div>
                      <span><span>Due '.timeToWords($ass[1]).'</span>
                        '.$ass[0].'
                      </span>
                    </div>';
      }
    } else {
      $output .= "No assessment tasks.";
    }

    $output .= "</div><img src='{$class['teacherPhoto']}' alt='Profile photo of {$class['teacherName']}'></a>";
  }
} else {
  $output = '<p>You currently are not in any classes.<br>You can join a class by clicking the button on the left.</p>';
}

echo $output;

?>
    </div>

  </body>
</html>
