<?php

$output = '';

include_once "connect.php";

$query = $conn->real_escape_string($_GET['q']);
$userHash = $conn->real_escape_string($_GET['u']);

if($query != '' && $userHash) {
  // get the userID
  $userIDQuery = $conn->query("SELECT userID FROM userInfo WHERE link='{$userHash}' LIMIT 1;");
  if($userIDQuery->num_rows > 0) {
    $userID = $userIDQuery->fetch_assoc()['userID'];

    // search for users with the query
    $userSearchQuery = $conn->query("SELECT ui.userID, ui.photo, ui.firstName, ui.lastName, ui.prefix, ui.link, (SELECT schoolName FROM schools WHERE id = ui.school) AS schoolName,
                                      CASE
                                        WHEN UNIX_TIMESTAMP(CURRENT_TIMESTAMP()) - UNIX_TIMESTAMP(u.lastOnline) < 30 THEN 'available'
                                        ELSE 'unavailable'
                                      END as 'lastOnline'
                                      FROM userInfo AS ui
                                      RIGHT JOIN users AS u ON u.userID = ui.userID
                                      WHERE
                                      	(ui.userID IN (SELECT userID FROM userGroups WHERE groupID IN (SELECT groupID FROM userGroups WHERE userID = {$userID}))
                                      	  OR ui.userID IN (SELECT teacherID FROM groups WHERE groupID IN (SELECT groupID FROM userGroups WHERE userID = {$userID})))
                                      	AND
                                      	(ui.firstName LIKE '%{$query}%'
                                      	  OR ui.lastName LIKE '%{$query}%'
                                      	  OR CONCAT(ui.firstName,' ',ui.lastName) LIKE '%{$query}%'
                                    	    OR CONCAT(ui.prefix,' ',ui.lastName) LIKE '%{$query}%');");

    // get the number of users returned by the database
    $numUsersFound = $userSearchQuery->num_rows;
    if ($numUsersFound > 0) {

      // if only one user was found (more in depth search results)
      if($numUsersFound == 1) {
        // get the user information
        $searchUser = $userSearchQuery->fetch_assoc();

        $searchUserID = $searchUser['userID'];

        // get the classes the user shares with them
        $userShareClassesQuery = $conn->query("SELECT groupLink, groupSubject, groupColour
                                                FROM groups AS g
                                                WHERE
                                                	(g.groupID IN (SELECT groupID FROM userGroups WHERE userID = {$userID})
                                                	  AND g.teacherID = {$searchUserID})
                                                  	OR
                                                  	(g.groupID IN (SELECT groupID FROM userGroups WHERE userID = {$searchUserID})
                                                  	  AND g.teacherID = {$userID})
                                                  	OR
                                                  	(g.groupID IN (SELECT groupID FROM userGroups WHERE userID = {$userID})
                                                  	  AND g.groupID IN (SELECT groupID FROM userGroups WHERE userID = {$searchUserID}));");


        if($userShareClassesQuery->num_rows > 0) {
          while($class = $userShareClassesQuery->fetch_assoc()) {
            $groupOutput .= '<a href="group/'.$class['groupLink'].'" class="search-group-colour-'.$class['groupColour'].'">'.$class['groupSubject'].'</a>';
          }
        }

        if($searchUser['prefix'] != '') {
          $name = $searchUser['prefix'].' '.$searchUser['lastName'];
          $sName = $searchUser['prefix'].' '.$searchUser['lastName'];
        } else {
          $name = $searchUser['firstName'].' '.$searchUser['lastName'];
          $sName = $searchUser['firstName'];
        }

        $output .= '<div class="search-single-user">
                    <div class="search-single-user-top">
                      <div class="user-top-image">
                        <img src="'.$searchUser['photo'].'" alt="'.$searchUser['firstName'].'\'s photo">
                      </div>
                      <div class="user-top-information">
                        <h3>'.$name.'</h3>
                        <h4>Studies at '.$searchUser['schoolName'].' <span class="'.$searchUser['lastOnline'].'">'.$searchUser['lastOnline'].'</span></h4>
                        <a href="user/'.$searchUser['link'].'">See '.$sName.'\'s profile &raquo;</a>
                      </div>
                    </div>
                    <div class="search-single-user-bottom">
                      <div class="user-bottom-groups">
                        <div class="user-bottom-groups-flex">
                          <span>Classes:</span>
                          '.$groupOutput.'
                        </div>
                      </div>
                      <a href="messages/'.$searchUser['link'].'" class="user-bottom-message">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 26 26" fill="#666666">
                          <path d="M 13 0.1875 C 5.924 0.1875 0.1875 5.252 0.1875 11.5 C 0.1875 14.676732 1.67466 17.538895 4.0625 19.59375 C 3.5416445 22.603047 0.17600428 23.827728 0.40625 24.65625 C 3.4151463 25.900544 9.377016 23.010935 10.28125 22.5625 C 11.155019 22.728689 12.06995 22.8125 13 22.8125 C 20.076 22.8125 25.8125 17.748 25.8125 11.5 C 25.8125 5.252 20.076 0.1875 13 0.1875 z"></path>
                        </svg>
                      </a>
                    </div>
                  </div>';

      // else: many users returned
      } else {

        $i = 1;

        $output .= '<h3>Users</h3><div class="search-users-flex">';
        while($searchUser = $userSearchQuery->fetch_assoc()) {
          if($i > 3) {
            break;
          }
          $i = $i + 1;

          if($searchUser['prefix'] != '') {
            $name = $searchUser['prefix'].' '.$searchUser['lastName'];
          } else {
            $name = $searchUser['firstName'].' '.$searchUser['lastName'];
          }

          $output .= '<a href="user/'.$searchUser['link'].'" class="result-user">
                        <h3>'.$name.'</h3>
                        <h4>'.$searchUser['schoolName'].'</h4>
                        <span class="'.$searchUser['lastOnline'].'">'.$searchUser['lastOnline'].'</span>
                        <div class="user-result-image">
                          <img src="'.$searchUser['photo'].'" alt="'.$searchUser['firstName'].'\'s photo">
                        </div>
                      </a>';
        }

        if ($userSearchQuery->num_rows == 2) {
          $output .= '<div class="result-user-disabled">
                        <span>No one else found. You must be in a class with them to find them.</span>
                        <div class="user-result-image">
                          <img src="images/search/no-user.gif">
                        </div>
                      </div>';
        }

        $output .= '</div>';

      }
    } // can add else statment

    // group search query
    $groupSearchQuery = $conn->query("SELECT g.groupID, g.groupColour, g.groupName, g.groupSubject, g.groupLink, ui.photo, CONCAT(ui.prefix, ' ', ui.lastName) AS teacherName, (SELECT COUNT(userID) FROM userGroups AS ug WHERE ug.groupID = g.groupID) AS numGroupMembers
                                        FROM groups AS g
                                        RIGHT JOIN userInfo AS ui ON g.teacherID = ui.userID
                                        WHERE
                                          (ui.firstName LIKE '%{$query}%'
                                          OR
                                          ui.lastName LIKE '%{$query}%'
                                          OR
                                          CONCAT(ui.prefix, ' ', ui.lastName) LIKE '%{$query}%'
                                          OR
                                          CONCAT(ui.firstName, ' ', ui.lastName) LIKE '%{$query}%'
                                          OR
                                          g.groupName LIKE '%{$query}%'
                                          OR
                                          g.groupSubject LIKE '%{$query}%')
                                        AND
                                          (g.groupID IN (SELECT groupID FROM userGroups WHERE userID = {$userID})
                                          OR
                                          g.teacherID = {$userID});");

    // get the number of groups found
    $numGroups = $groupSearchQuery->num_rows;

    // if there is one group
    if($numGroups == 1) {
      $group = $groupSearchQuery->fetch_assoc();

      // query to get the most recent post
      $mostRecentPostQuery = $conn->query("SELECT ui.photo, p.text, ui.prefix, ui.firstName, ui.lastName
                                          	FROM posts AS p RIGHT JOIN userInfo AS ui ON ui.userID = p.userID
                                          	WHERE p.groupID = {$group['groupID']}
                                          	ORDER BY date DESC LIMIT 1;");

      // handle most recent post output
      $mrpo = '<h6 class="latest-post-info">No posts have been made yet.</h6>';
      if($mostRecentPostQuery->num_rows == 1) {
        $mrp = $mostRecentPostQuery->fetch_assoc();

        $name = $mrp['firstName'].' '.$mrp['lastName'];
        if($mrp['prefix']) {
          $name = $mrp['prefix'].' '.$mrp['lastName'];
        }

        $mrpo = '<div class="latest-post">
                  <h6>Latest post</h6>
                  <div class="post-message">
                    <img src="'.$mrp['photo'].'">
                    <p><span>'.$name.'</span>'.$mrp['text'].'</p>
                  </div>
                </div>';
      }

      // query to get the upcoming assignments
      $upcomingAssignmentsQuery = $conn->query("SELECT p.aName, p.due
                                              	FROM posts AS p
                                              	WHERE
                                              		p.groupID = {$group['groupID']}
                                              		AND
                                              		p.type = 'a'
                                              		AND
                                              		p.due > CURRENT_TIMESTAMP
                                              	ORDER BY due DESC;");

      // handle upcoming assignments output
      $uao = '<h5>No upcoming assignments</h5>';
      if($upcomingAssignmentsQuery->num_rows > 0) {
        $uao = '<h5>Upcoming Assignments</h5>';
        while($ass = $upcomingAssignmentsQuery->fetch_assoc()) {
          $due = strtotime($ass['due']);
          $uao .= '<div class="upcoming-assignment">'.$ass['aName'].'<span>'.date("j M", $due).'</span></div>';
        }
      }

      // the output for a single group
      $output .= '<a href="group/'.$group['groupLink'].'" class="search-single-group search-group-colour-'.$group['groupColour'].'">
                    <div class="search-group-top">
                      <div class="search-group-top-left">
                        <h4>'.$group['groupName'].'</h4>
                        <h5><span>'.$group['numGroupMembers'].' students for '.$group['groupSubject'].'</span>&nbsp;&nbsp;//&nbsp;&nbsp;<span>'.$group['teacherName'].'</span></h5>
                      </div>
                      <img src="'.$group['photo'].'" alt="teacher\'s photo">
                    </div>
                    <div class="search-group-bottom">
                      '.$uao.$mrpo.'
                    </div>
                  </a>';

    // more than one group found --> go through each group
    } else if ($numGroups > 0){

      $output .= '<h3>Groups</h3><div class="search-group-flex">';

      $a = 0;
      while($group = $groupSearchQuery->fetch_assoc()) {
        // increment counter
        $a = $a + 1;

        // output to the screen
        $output .= '<a href="group/'.$group['groupLink'].'" class="search-group-colour-'.$group['groupColour'].'">
                      <div class="search-group-ind-flex">
                        <div class="search-group-top">
                          <h4>'.$group['groupName'].'</h4>
                          <span>'.$group['numGroupMembers'].' students ('.$group['groupSubject'].')</span>
                          <span>'.$group['teacherName'].'</span>
                        </div>
                        <img src="'.$group['photo'].'" alt="teacher\'s photo">
                      </div>
                    </a>';

        // stop at three groups
        if($a == 3) {
          break;
        }
      }

      // if there were only two groups found add a blank find more groups filler
      if ($a == 2) {
        $output .= '<div class="disabled-group">
                      <h4>No other groups found.</h4>
                      <p>To join more groups you need to use the code given by your teacher.</p>
                      <button class="join-class-btn" onclick="toggleJoinClass()">+ Join A Class</button>
                    </div>';
      }

      $output .= '</div>';
    }

    // search for files with the query
    $fileSearchQuery = $conn->query("SELECT f.fileIntUrl, f.fileExtUrl, f.fileType, f.fileName, ui.firstName, ui.lastName, ui.prefix, ui.photo, g.groupSubject, g.groupName
                                      FROM (((attachments AS a RIGHT JOIN files AS f ON f.fileID = a.fileID)
                                    	RIGHT JOIN posts AS p ON p.postID = a.postID)
                                    	RIGHT JOIN groups AS g ON p.groupID = g.groupID)
                                    	RIGHT JOIN userInfo AS ui ON p.userID = ui.userID
                                    	WHERE
                                    		(f.fileName LIKE '%{$query}%'
                                    		OR ui.firstName LIKE '%{$query}%'
                                    		OR ui.lastName LIKE '%{$query}%'
                                        OR CONCAT(ui.firstName,' ',ui.lastName) LIKE '%{$query}%'
                                        OR CONCAT(ui.prefix,' ',ui.lastName) LIKE '%{$query}%'
                                    		OR p.aName LIKE '%{$query}%')
                                    	AND f.fileID IN
                                    		(SELECT a.fileID
                                    	    	FROM attachments AS a
                                    	    	WHERE a.postID IN
                                    	    		(SELECT p.postID FROM posts AS p WHERE
                                        				p.groupID IN (SELECT groupID FROM userGroups WHERE userID={$userID})
                                        				OR
                                        				p.groupID IN (SELECT groupID FROM groups WHERE teacherID={$userID})));");

    $numFilesFound = $fileSearchQuery->num_rows;
    if($numFilesFound > 0) {

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

      function formatFileType($fileType) {
        switch($fileType) {
          case 'pdf':
            $ft = 'PDF';
            break;

          case 'word':
            $ft = 'Word';
            break;

          case 'ppt':
            $ft = 'Powerpoint';
            break;

          case 'excel':
            $ft = 'Excel';
            break;

          case 'code':
            $ft = 'Code File';
            break;

          case 'zip':
            $ft = 'ZIP Folder';
            break;

          case 'text':
            $ft = 'Text File';
            break;

          case 'r-text':
            $ft = 'Rich Text File';
            break;

          default:
            $ft = 'Unknown File Type';
            break;
        }
        return $ft;
      }

      // if only one file was found
      if($numFilesFound == 1) {

        $searchFile = $fileSearchQuery->fetch_assoc();
        $fileType = $searchFile['fileType'];

        if ($fileType == 'url') {
          $output .= '<a href="'.$searchFile['fileExtUrl'].'" class="search-single-file">
                        <iframe src="'.$searchFile['fileExtUrl'].'" sandbox="true"></iframe>
                        <div class="file-information">
                          <div class="file-title">
                            <h4>'.get_title($searchFile['fileExtUrl']).'</h4>
                            <h5>Website</h5>
                          </div>

                          <div class="file-reference">
                            <img src="'.$searchFile['photo'].'" alt="'.$searchFile['firstName']." ".$searchFile['lastName'].'\'s photo">
                            <span>&#10095;&#10095;</span>
                            <span>'.$searchFile['groupName'].'</span>
                          </div>
                        </div>
                      </a>';
        } else if($fileType == 'img') {
          $output .= '<a href="file/'.$searchFile['fileIntUrl'].'" class="search-single-file">
                        <div class="file-img" style="background-image: url('.$searchFile['fileExtUrl'].');"></div>
                        <div class="file-information">
                          <div class="file-title">
                            <h4>'.$searchFile['fileName'].'</h4>
                            <h5>Image</h5>
                          </div>

                          <div class="file-reference">
                            <img src="'.$searchFile['photo'].'" alt="'.$searchFile['firstName']." ".$searchFile['lastName'].'\'s photo">
                            <span>&#10095;&#10095;</span>
                            <span>'.$searchFile['groupName'].'</span>
                          </div>
                        </div>
                      </a>';
        } else {
          $output .= '<a href="file/'.$searchFile['fileIntUrl'].'" class="search-single-file">
                        <img src="images/attachments/'.$fileType.'.gif" alt="Powerpoint image">
                        <div class="file-information">
                          <div class="file-title">
                            <h4>'.$searchFile['fileName'].'</h4>
                            <h5>'.formatFileType($fileType).'</h5>
                          </div>

                          <div class="file-reference">
                            <img src="'.$searchFile['photo'].'" alt="'.$searchFile['firstName']." ".$searchFile['lastName'].'\'s photo">
                            <span>&#10095;&#10095;</span>
                            <span>'.$searchFile['groupName'].'</span>
                          </div>
                        </div>
                      </a>';
        }

      // else: many files found
      } else {

        $i = 1;

        $output .= '<h3>Files</h3><div class="search-files-flex">';

        while($searchFile = $fileSearchQuery->fetch_assoc()) {
          if($i > 3) {
            break;
          }
          $i = $i + 1;

          $fileType = $searchFile['fileType'];

          if ($fileType == 'url') {
            $output .= '<a href="'.$searchFile['fileExtUrl'].'" class="search-single-file">
                          <iframe src="'.$searchFile['fileExtUrl'].'" sandbox="true" scrolling="no"></iframe>
                          <div class="file-information">
                            <h4>'.get_title($searchFile['fileExtUrl']).'</h4>
                            <h5>Website</h5>

                            <div class="file-reference">
                              <img src="'.$searchFile['photo'].'" alt="'.$searchFile['firstName']." ".$searchFile['lastName'].'\'s photo">
                              <span>&#10095;&#10095;</span>
                              <span>'.$searchFile['groupSubject'].'</span>
                            </div>
                          </div>
                        </a>';
          } else if($fileType == 'img') {
            $output .= '<a href="file/'.$searchFile['fileIntUrl'].'" class="search-single-file">
                          <div class="file-img" style="background-image: url('.$searchFile['fileExtUrl'].');"></div>
                          <div class="file-information">
                            <h4>'.$searchFile['fileName'].'</h4>
                            <h5>Image</h5>

                            <div class="file-reference">
                              <img src="'.$searchFile['photo'].'" alt="'.$searchFile['firstName']." ".$searchFile['lastName'].'\'s photo">
                              <span>&#10095;&#10095;</span>
                              <span>'.$searchFile['groupSubject'].'</span>
                            </div>
                          </div>
                        </a>';
          } else {
            $output .= '<a href="file/'.$searchFile['fileIntUrl'].'" class="search-single-file">
                          <img src="images/attachments/'.$fileType.'.gif" alt="Powerpoint image">
                          <div class="file-information">
                            <h4>'.$searchFile['fileName'].'</h4>
                            <h5>'.formatFileType($fileType).'</h5>

                            <div class="file-reference">
                              <img src="'.$searchFile['photo'].'" alt="'.$searchFile['firstName']." ".$searchFile['lastName'].'\'s photo">
                              <span>&#10095;&#10095;</span>
                              <span>'.$searchFile['groupSubject'].'</span>
                            </div>
                          </div>
                        </a>';
          }

        }

        $output .= '</div>';

      }

    }

    // post query
    $postsSearchQuery = $conn->query("SELECT ui.photo, ui.firstName, ui.lastName, ui.prefix, p.text, p.date, g.groupLink, g.groupName, g.groupColour FROM
                                      	(posts AS p RIGHT JOIN userInfo AS ui ON ui.userID = p.userID)
                                      	RIGHT JOIN groups AS g ON g.groupID = p.groupID
                                      	WHERE
                                      		(p.groupID IN (SELECT groupID FROM userGroups WHERE userID = {$userID})
                                          OR
                                          g.teacherID = {$userID})
                                      	AND (
                                          g.groupName LIKE '%{$query}%'
                                      		OR ui.firstName LIKE '%{$query}%'
                                      		OR ui.lastName LIKE '%{$query}%'
                                       		OR CONCAT(ui.firstName, ' ', ui.lastName) LIKE '%{$query}%'
                                       		OR CONCAT(ui.prefix,' ',ui.lastName) LIKE '%{$query}%'
                                      		OR p.text LIKE '%{$query}%'
                                          OR p.aName LIKE '%{$query}%'
                                        ) ORDER BY p.date LIMIT 4;");

    // if there are posts found
    if($postsSearchQuery->num_rows > 0) {

      // prepare the output
      $output .= '<h3>Posts</h3><div class="search-posts-flex">';
      while($post = $postsSearchQuery->fetch_assoc()) {

        // prepare the post creator's name
        $name = $post['firstName'].' '.$post['lastName'];
        if($post['prefix'] != '') {
          $name = $post['prefix'].' '.$post['lastName'];
        }

        // prepare the date
        $date = date("j M", strtotime($post['date']));

        // output the information for the post
        $output .= '<div class="result-post">
                      <img src="'.$post['photo'].'">
                      <div class="post-right">
                        <h5>'.$name.' <span>'.$date.'</span></h5>
                        <h6 class="search-group-colour-'.$post['groupColour'].'">'.$post['groupName'].'</h6>
                        <p>'.$post['text'].'</p>
                      </div>
                    </div>';
      }

      // end the post output
      $output .= '</div>';
    }

  }

  // show the end of the search result
  if($output) {
    $output .= '<span>End of results for \''.$query.'\'.</span><hr>';

  // nothing found
  } else {
    $output .= 'TODO: nothing found.';
  }

// nothing searched
} else {
  $output .= '<img src="images/search/options.jpg">';
}

echo $output;

?>



<!--

----- POSTS -----

SELECT ui.photo, ui.firstName, ui.lastName, ui.prefix, p.text, p.due, p.postID, g.groupName FROM
	(posts AS p RIGHT JOIN userInfo AS ui ON ui.userID = p.userID)
	RIGHT JOIN groups AS g ON g.groupID = p.groupID
	WHERE
		(p.groupID IN (SELECT groupID FROM userGroups WHERE userID = {$userID})
    OR
    g.teacherID = {$userID})
	AND
		(g.groupName LIKE "%{$query}%"
		OR
		ui.firstName LIKE "%{$query}%"
		OR
		ui.lastName LIKE "%{$query}%"
   		OR
 		CONCAT(ui.firstName, ' ', ui.lastName) LIKE "%{$query}%"
 		OR
 		CONCAT(ui.prefix,' ',ui.lastName) LIKE "%{$query}%"
		OR
		p.text LIKE "%{$query}%");

-->
