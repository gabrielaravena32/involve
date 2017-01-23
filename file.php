
<?php

// start the session (keep user logged in)
session_start();

// include the connect script
include_once "php/connect.php";

// create an empty array to hold the current user's information
$userInfo = [];

// if the session doesn't have a token set (not logged in)
if (!$_SESSION['token']) {

  // destroy the session created at the top of the page
  session_destroy();

  // send the user to the home page
  header("Location: .");

  // if the user is logged in
} else {
  // select the user information from the database where the user's token is correct
  $sql = "SELECT * FROM userInfo WHERE userID=(SELECT userID FROM users WHERE token='{$_SESSION['token']}');";
  $result = $conn->query($sql);

  // if there is a user with that token
  if ($result->num_rows == 1) {
    // set the array $userInfo to hold the information (not stored in $_SESSION making it more secure)
    $userInfo = $result->fetch_assoc();

  // else if the user's stored token doesn't match any in the database
  } else {
    // destroy session data (token and any other information)
    session_destroy();

    // send the user to the homepage
    header("Location: .");
  }
}

// get the file URL
$file = $_GET['f'];

// if there was no file included send the user to the home or index page
if(!$file) {
  header('Location: .');
}

?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">

<?

$url = $_SERVER['REQUEST_URI'];

// check whether the last character is a '/'
if(substr($url,-1) === '/') {
  echo '<base href="../../"><link rel="stylesheet" href="css/file.css">';
} else {
  echo '<base href="../"><link rel="stylesheet" href="css/file.css">';
}

// set up array to hold the file information
$fileInfo = [];

// set up the output string
$output = '';

// sql to get the file name, file type (image, pdf, etc), and the external URL
$fileSQL = "SELECT f.fileName AS name, f.fileType AS type, f.fileExtUrl AS url
            FROM files AS f
            WHERE
            	f.fileIntUrl = '{$file}'
            	AND (f.fileID IN
            			(SELECT a.fileID FROM attachments AS a WHERE a.postID IN
            				(SELECT p.postID FROM posts AS p WHERE
            					p.groupID IN (SELECT ug.groupID FROM userGroups AS ug WHERE ug.userID = {$userInfo['userID']})
            					OR
            					p.groupID IN (SELECT g.groupID FROM groups AS g WHERE g.teacherID = {$userInfo['userID']})
            				))
            		OR f.fileID IN
            			(SELECT b.fileID FROM backpackItems AS b WHERE b.userID = 1))
            LIMIT 1;";

// run the sql query and collect the results in fileResult
$fileResult = $conn->query($fileSQL);

// if there is one row returned --> meaning there is a file the user can access
if ($fileResult->num_rows == 1) {
  // add the information collected to the fileInfo array
  $fileInfo = $fileResult->fetch_assoc();

  // add to the output the title and the styling
  $output .= '<title>Involve | '.$fileInfo['name'].'</title></head>
              <body>

                <div id="navbar">
                  <a onclick="window.history.back();">Back to <span>Involve</span></a>

                  <div class="pull-right">
                    <a onclick="addToBackpack(\''.$file.'\', \''.$userInfo['link'].'\');">Add to Backpack</a>
                    <a onclick="getLink(\''.$fileInfo['url'].'\', this);">Get Link</a>
                  </div>
                </div>';

  switch($fileInfo['type']) {
    case 'pdf':
      $output .= '<div id="content" class="content-pdf">
                    <iframe src="'.$fileInfo['url'].'"></iframe>
                  </div>';
      break;

    case 'img':
      $output .= '<div id="content" class="content-img">
                    <img src="'.$fileInfo['url'].'" alt="Photo of '.$fileInfo['name'].'">
                  </div>';
      break;

    case 'word':
      $output .= '<div id="content" class="content-download">
                    <div class="download download-word">
                      <div class="download-top">
                        <div class="download-image"></div>
                        <div class="download-info">
                          <span>'.$fileInfo['name'].'</span>
                          <span>Word Document</span>
                        </div>
                      </div>
                      <a href="'.$fileInfo['url'].'" download>Download</a>
                    </div>
                  </div>';
      break;

    case 'ppt':
      $output .= '<div id="content" class="content-download">
                    <div class="download download-ppt">
                      <div class="download-top">
                        <div class="download-image"></div>
                        <div class="download-info">
                          <span>'.$fileInfo['name'].'</span>
                          <span>Powerpoint Document</span>
                        </div>
                      </div>
                      <a href="'.$fileInfo['url'].'" download>Download</a>
                    </div>
                  </div>';
      break;

    case 'excel':
      $output .= '<div id="content" class="content-download">
                    <div class="download download-excel">
                      <div class="download-top">
                        <div class="download-image"></div>
                        <div class="download-info">
                          <span>'.$fileInfo['name'].'</span>
                          <span>Excel Document</span>
                        </div>
                      </div>
                      <a href="'.$fileInfo['url'].'" download>Download</a>
                    </div>
                  </div>';
      break;

    default:
      $output .= 'havent added support for this file type yet';
      break;
  }

// else: either no file, or invalid access
} else {
  // select 1 if the file exists
  $result = $conn->query("SELECT 1 FROM files WHERE fileIntUrl='{$file}';");
  // if there is a result from the sql query - there is a file but they dont have access
  if($result->num_rows == 1) {
    echo 'no access';
  // else: the file does not exist
  } else {
    echo 'doesnt exist';
  }
}

// print the output to the screen
echo $output;

?>


    <script type="text/javascript">

    var getLink = function (text, elem) {

      copyTextToClipboard(text);

      elem.innerHTML = 'Link copied';
      elem.style.color = '#3498db';

      setTimeout(function() {
        elem.innerHTML = 'Get Link';
        elem.style.color = '#000';
      }, 3000);

    };

    // Credit: http://stackoverflow.com/questions/400212/how-do-i-copy-to-the-clipboard-in-javascript
    var copyTextToClipboard = function(text) {
      var textArea = document.createElement("textarea");
      textArea.style.position = 'fixed';
      textArea.style.top = 0;
      textArea.style.left = 0;
      textArea.style.width = '2em';
      textArea.style.height = '2em';
      textArea.style.padding = 0;
      textArea.style.border = 'none';
      textArea.style.outline = 'none';
      textArea.style.boxShadow = 'none';
      textArea.style.background = 'transparent';
      textArea.value = text;
      document.body.appendChild(textArea);
      textArea.select();
      try {
        document.execCommand('copy');
      } catch (err) {
        //failed
      }
      document.body.removeChild(textArea);
    };


    var addToBackpack = function(item, userHash) {
      alert(userHash+' has requested '+item);
    };
    </script>
  </body>
</html>
