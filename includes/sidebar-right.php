<div id="sidebar-right">

  <?php
  // get the current date (with no hours or minutes or seconds) as an integer
  $timestamp = new Datetime('today', new Datetimezone('Australia/Sydney'));
  $timestamp = strtotime($timestamp->format('Y-m-d H:i:s') . PHP_EOL);

  echo "TODO: Fix the current class / time feature";

  // SQL query to select all the classes that the user has that day
  // returns the className, time class starts, length of class, and the prefix and last name of teacher (e.g. Mr, Smith)
  $currentClassSQL = "SELECT g.groupName, gt.time, gt.length, ui.prefix, ui.lastName FROM
                        (groups AS g RIGHT JOIN groupsTimetable AS gt ON g.groupID=gt.groupID)
                        RIGHT JOIN userInfo AS ui ON g.teacherID=ui.userID
                        WHERE
                          ((gt.startDate - {$timestamp}) % gt.repeatInterval = 0)
                          AND g.groupID IN
                            (SELECT ug.groupID FROM userGroups AS ug
                                      WHERE ug.userID={$userInfo['userID']})
                        ORDER BY gt.time;";

  // query the database with the query
  $currentClassQuery = $conn->query($currentClassSQL);

  // get the current time as seconds
  $now = new Datetime('now', new Datetimezone('Australia/Sydney'));
  $now = strtotime($now->format('Y-m-d H:i:s') . PHP_EOL);

  // get the time of the day (by removing $timestamp which is the date portion calculated above)
  $time = $now - $timestamp;

  // prepare an empty output
  $output = '';

  // if the query for classes returned a value run through all the rows in database
  if ($currentClassQuery->num_rows > 0) {
    while($currentClass = $currentClassQuery->fetch_assoc()) {

      // if the current time is less than the time in the database (e.g. class hasn't started)
      if ($time < $currentClass['time']) {

        // if there is no current class in the "current class" field
        if($output == "") {

          // show the user that they do not have a current class
          $output = '<div id="current-class">
                      <div id="current-class-text">Currently you do not have a class on.</div>
                    </div>
                    <div id="next-classes">
                      <h3>Coming Up</h3>
                      <div id="next-classes-list">';
        }

        // add the item to the display
        $output .= '<div class="next-classes-item">
                      <div>'.gmdate("H:i", $currentClass['time']).'</div>
                      <div class="next-class-name">'.$currentClass['groupName'].'</div>
                    </div>';

      // else if: the current time is less than the start time plus the length (e.g. class is going on now)
      } else if ($time < $currentClass['time'] + $currentClass['length']){

        // display that the class is currently happening
        $output .= '<div id="current-class">
                      <div id="current-class-text">Currently you have '.$currentClass['groupName'].' with '.$currentClass['prefix'].' '.$currentClass['lastName'].'.</div>
                      <div id="current-class-bar" data-load="true">
                        <div id="current-class-progress" data-progress="'.($time-$currentClass['time']).'" data-length="'.$currentClass['length'].'">
                        </div>
                      </div>
                    </div>
                    <div id="next-classes"><h3>Coming Up</h3><div id="next-classes-list">';
      }
    }

    // if there were no classes left in the day to add to "coming up"
    if ($output != '' && substr($output, -28) === '<div id="next-classes-list">') {

      // remove the HTML element
      $output = substr($output, 0, -69);

    // otherwise there were classes add
    } else if($output != '') {

      // finish the HTML element
      $output .= '</div></div>';
    }
  }

  $output .= '<div id="active-teacher-javascript-container"></div>';

  // return the output to the screen
  echo $output;
  ?>

</div>

<!-- HTML style sheet -->
<style>
  #sidebar-right {
    position: fixed;
    right: 50px;
    top: 30px;
    width: 215px;
    color: #95a5a6;
  }

  #sidebar-right > * {
    position: relative;
    width: 100%;
    box-sizing: border-box;
    padding: 10px 15px;
    margin-bottom: 20px;
    box-shadow: 2px 2px 6px 3px rgba(0,0,0,0.2);
    background: white;
  }

  #sidebar-right #current-class {
    padding: 20px 15px 15px 15px;
    font-size: 17px;
    font-weight: 300;
    background: #34495e;
    color: white;
    line-height: 1.5;
  }

  #current-class-bar {
    position: relative;
    width: 100%;
    height: 3px;
    margin: 10px 0;
    background: #2c3e50;
  }

  #current-class-progress {
    position: relative;
    display: block;
    width: 0%;
    height: 100%;
    background: white;
  }

  #current-class-progress::after {
    content: " ";
    display: block;
    position: absolute;
    width: 9px;
    height: 9px;
    background: white;
    border-radius: 50%;
    right: 0;
    top: -3px;
  }

  #sidebar-right h3 {
    margin: 0;
    text-transform: uppercase;
    font-size: 13px;
    font-weight: 500;
    margin: 10px 0px;
  }

  .next-classes-item {
    height: 30px;
    margin-bottom: 5px;
    padding: 3px 10px;
    box-sizing: border-box;
    border-radius: 2px;
    background: #ecf0f1;
    box-shadow: 1px 1px 3px 0px rgba(0,0,0,0.2);
  }

  .next-classes-item > * {
    position: relative;
    float: left;
    display: inline-block;
    height: 24px;
    line-height: 24px;
    font-size: 13px;
    color: #7f8c8d;
    font-weight: 500;
  }

  .next-classes-item .next-class-name {
    margin-left: 6px;
    font-weight: 400;
  }

  #active-user * {
    position: relative;
  }

  #active-teacher-javascript-container p {
    font-size: 12px;
    margin: 0;
  }

  #active-teacher-javascript-container .active-user {
    width: 100%;
    padding: 2px 0;
    height: 40px;
    line-height: 15px;
    font-size: 13px;
  }

  #active-teacher-javascript-container .active-user img {
    height: 25px;
    width: 25px;
    border-radius: 50%;
    float: left;
    margin-right: 6px;
    margin-top: 10px;
  }

  #active-teacher-javascript-container .active-user .active-user-right {
    position: relative;
    padding: 10px 0;
  }

  #active-teacher-javascript-container .active-user .active-user-right a {
    text-decoration: none;
    font-weight: 500;
    color: #7f8c8d;
    display: block;
  }

  #active-teacher-javascript-container .active-user .active-user-right .bubble {
    position: absolute;
    right: 0;
    top: 20px;
    height: 10px;
    width: 10px;
    border-radius: 50%;
  }

  #active-teacher-javascript-container .active-user .active-user-right .bubble-green {
    background: #27ae60;
  }
  #active-teacher-javascript-container .active-user .active-user-right .bubble-yellow {
    background: #f1c40f;
  }
  #active-teacher-javascript-container .active-user .active-user-right .bubble-red {
    background: #c0392b;
  }

</style>

<script>

var currentClassBarElem;
var progress, length, nextProgressBarUpdate;
var newClass = false;

// create the progress bar for current class
// function(progress, total) both variables as seconds
function progressBar() {
  progress = progress + 60;

  if(newClass) {
    newClass = false;

    // clear the nextProgressBarUpdate variable timeout
    clearTimeout(nextProgressBarUpdate);

    // ajax request to get the new class information
    // set up an AJAX request
    var request;
    if (window.XMLHttpRequest) {
      request = new XMLHttpRequest();
    } else {
      request = new ActiveXObject("Microsoft.XMLHTTP");
    }

    // on return of information from AJAX request
    request.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {

        var currentClass = document.getElementById('current-class');

        var classInfo = JSON.parse(this.responseText);

        // if there is no next class
        if(classInfo === 0) {

          // change the information to reflect this
          currentClass.innerHTML = '<div id="current-class-text">Currently you do not have a class on.</div>';


        // else there is a next class
        } else {

          var groupName = classInfo.groupName,
              prefix = classInfo.prefix,
              lastName = classInfo.lastName,
              classProgress = parseInt(classInfo.progress);
              classLength = parseInt(classInfo.length);

          // change the information to reflect this
          currentClass.innerHTML = '<div id="current-class-text">Currently you have '+groupName+' with '+prefix+' '+lastName+'.</div><div id="current-class-bar" data-load="true"><div id="current-class-progress" data-progress="'+classProgress+'" data-length="'+length+'"></div></div>';

          progress = classProgress;
          length = classLength;

          currentClassBarElem.style.width = (progress/length)*100 + "%";

          // call this function once a minute
          nextProgressBarUpdate = setInterval(progressBar, 60000);
        }

      }
    };

    // send the request to the php file: testEmail.php with the inputted email
    request.open('GET', 'php/requestNextClass.php?uh=<?php echo $userInfo['link'] ?>');
    request.send();

  } else {
    // if the class ends within the next 60 seconds
    if(length-progress < 60) {
      // set the newClass variable to true
      newClass = true;
      // clear the interval of updating progress bar
      clearInterval(nextProgressBarUpdate);

      // set the next update to be on the class switch
      nextProgressBarUpdate = setTimeout(progressBar, (length-progress) * 1000);
    }

    // set the width of the progress bar
    currentClassBarElem.style.width = (progress/length)*100 + "%";
  }
};


// if the class bar has the load atribute
if(document.getElementById('current-class-bar')) {

  currentClassBarElem = document.getElementById('current-class-progress');

  // get the initial progress and length as dictated by database
  progress = parseInt(currentClassBarElem.getAttribute("data-progress"));
  length = parseInt(currentClassBarElem.getAttribute("data-length"));

  progress = progress - 60;

  // call progressBar function
  progressBar();

  // call this function once for every minute remaining
  nextProgressBarUpdate = setInterval(progressBar, 60000);
}

var lastTeacherElem = document.getElementById('active-teacher-javascript-container');
var nextLastOnline;

var getLastOnline = function() {

  // set up an AJAX request
  var request;
  if (window.XMLHttpRequest) {
    request = new XMLHttpRequest();
  } else {
    request = new ActiveXObject("Microsoft.XMLHTTP");
  }

  // on return of information from AJAX request
  request.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      if(this.responseText === '') {
        lastTeacherElem.style.display = 'none';
      } else {
        lastTeacherElem.innerHTML = this.responseText;
      }
    }
  };

  // send the request to the php file: testEmail.php with the inputted email
  request.open('GET', 'php/lastTeachersOnline.php?uh=<?php echo $userInfo['link']; ?>');
  request.send();

  nextLastOnline = setTimeout(getLastOnline, 29000);
};

lastTeacherElem.onmouseenter = function() {
  clearTimeout(nextLastOnline);
  getLastOnline();
}

getLastOnline();
</script>
