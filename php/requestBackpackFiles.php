<?php

// include the connect script
include_once "connect.php";

// get the user's Hash
$uHash = $conn->real_escape_string($_GET['uh']);
$o = $conn->real_escape_string($_GET['o']);

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

// order by most recent
if($o == 1) {
  $query = $conn->query(" SELECT
                          	CASE
                          		WHEN bi.fileName = '' THEN f.fileName
                          			ELSE bi.fileName
                          		END AS fileName,
                          	f.fileExtUrl,
                          	f.createdDate,
                            f.fileType
                          FROM backpackItems AS bi
                          RIGHT JOIN files AS f ON f.fileID = bi.fileID
                          WHERE bi.userID = (SELECT userID FROM userInfo WHERE link = '{$uHash}' LIMIT 1)
                          ORDER BY f.createdDate DESC;");
// order by file name
} else if ($o == 2) {
  $query = $conn->query(" SELECT
                          	CASE
                          		WHEN bi.fileName = '' THEN f.fileName
                          			ELSE bi.fileName
                          		END AS fileName,
                          	f.fileExtUrl,
                          	f.createdDate,
                            f.fileType
                          FROM backpackItems AS bi
                          RIGHT JOIN files AS f ON f.fileID = bi.fileID
                          WHERE bi.userID = (SELECT userID FROM userInfo WHERE link = '{$uHash}' LIMIT 1)
                          ORDER BY(CASE WHEN bi.fileName = '' THEN f.fileName ELSE bi.fileName END);");
// order by favourites then file name
} else if ($o == 3) {
  $query = $conn->query(" SELECT
                          	CASE
                          		WHEN bi.fileName = '' THEN f.fileName
                          			ELSE bi.fileName
                          		END AS fileName,
                          	f.fileExtUrl,
                          	f.createdDate,
                            f.fileType,
                            bi.favourite
                          FROM backpackItems AS bi
                          RIGHT JOIN files AS f ON f.fileID = bi.fileID
                          WHERE bi.userID = (SELECT userID FROM userInfo WHERE link = '{$uHash}' LIMIT 1)
                          ORDER BY bi.favourite DESC, (CASE WHEN bi.fileName = '' THEN f.fileName ELSE bi.fileName END);");
}

$output = '';
if($query) {
  if ($query->num_rows > 0) {
    while($row = $query->fetch_assoc()) {
      $output .= '<div class="backpack-item">';

      switch($row['fileType']) {
        case 'img':
          // jpeg, gif, png, tiff
          $output .= '<div style=\'background-image: url('.$row['fileExtUrl'].')\'></div>';
          break;

        case 'url':
          // urls
          $output .= '<iframe src="'.$row['fileExtUrl'].'" sandbox></iframe>';
          break;

        case 'word':
        case 'ppt':
        case 'excel':
        case 'pdf':
        case 'code':
        case 'zip':
        case 'text':
        case 'r-text':
          // word document, powerpoint, excel, pdf, html, css, js, zip file, plain and rich text file
          $output .= '<div class="backpack-image-'.$row['fileType'].'"></div>';
          break;

        case 'undef':
          // undefined file type
          break;
      }

      $output .= '<div class="backpack-item-hover">
                      <span>';

      if($row['fileName'] == '') {
        $output .= get_title($row['fileExtUrl']);
      } else {
        $output .= $row['fileName'];
      }
      
      $output .= '</span>
                  <span>'.date('j M Y',strtotime($row['createdDate'])).'</span>
                </div>';

      if($row['favourite']) {
        if($row['favourite'] == 1) {
          $output .= '<div class="favourite">&#9733;</div>';
        }
      }

      $output .= '</div>';
    }
  }
}
echo $output;
// add the showing of the file (currently just defaults to flatuicolors.com)

?>
