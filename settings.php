<?php

// include the connect script
include_once "php/connect.php";

// create blank path
$path = '.';

// check where the url is pointing to
if(substr($_SERVER['REQUEST_URI'],-8) != 'settings') {
  $path = '../';
}

// include the redirect script
include_once "php/redirect.php";

// settings page type
$type = $_GET['p'];

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Involve | Home</title>

    <?php echo '<base href="'.$path.'" />'; ?>

    <link rel="stylesheet" href="css/settings.css">
  </head>
  <body>

    <?php include_once "includes/sidebar-left.php"; ?>

    <!-- Page content -->
    <div class="content" id="content">
      <div class="settings-sidebar">
        <div class="settings-sidebar-heading">
          <img src="<?php echo $userInfo['photo']; ?>">
          <div class="settings-sidebar-title">
            <h2><?php echo $userInfo['firstName'].' '.$userInfo['lastName']; ?></h2>
            <h3>Settings</h3>
          </div>
        </div>
      <?php

      // output the navbar
      if($type == "ac") {
        echo '<a class="selected">Account</a>
              <a href="settings/security">Security</a>
              <a href="settings/notifications">Notifications</a>
              </div>';
      } else if ($type == "se") {
        echo '<a href="settings/account">Account</a>
              <a class="selected">Security</a>
              <a href="settings/notifications">Notifications</a>
              </div>';
      } else if ($type == "no"){
        echo '<a href="settings/account">Account</a>
              <a href="settings/security">Security</a>
              <a class="selected">Notifications</a>
              </div>';
      }

      // output the screen
      if($type == "ac") {

        // prepare the school section
        $schoolQuery = $conn->query("SELECT s.schoolName FROM schools AS s WHERE s.id = (SELECT ui.school FROM userInfo AS ui WHERE ui.userID = {$userInfo['userID']}) LIMIT 1;");
        if($schoolQuery->num_rows > 0) {
          $schoolText = '<div class="settings-input-school"><span>You are currently at:</span> <a href="#">'.$schoolQuery->fetch_assoc()['schoolName'].'</a></div>';
        } else {
          $schoolText = '<div class="settings-input-school">You are currently not listed as part of a school.</div>';
        }

        // prepare the banner photo section
        if($userInfo['bannerPhoto'] != '') {
          $bannerText = '<img src="'.$userInfo['bannerPhoto'].'">';
        }

        echo '<div id="settings-account" class="settings-window">
                <h4>Your personal information</h4>
                <h5>Manage the basics - such as name, photo and school - to help your teachers and peers.</h5>

                <div class="setting-input-group">
                  <label>Name</label>
                  <div class="input-flex">
                    <input type="text" id="input-firstName" placeholder="First Name" value="'.$userInfo['firstName'].'">
                    <input type="text" id="input-lastName" placeholder="Last Name" value="'.$userInfo['lastName'].'">
                    <button id="setting-updateName-button" class="disabled" onclick="settingsUpdateName()">Update name</button>
                  </div>
                </div>

                <div class="setting-input-group">
                  <label>School</label>
                  <input type="text" id="input-school" placeholder="Search for a new school...">'.$schoolText.'
                </div>

                <div class="setting-input-group input-disabled">
                  <label>Email</label>
                  <input type="text" value="'.$userInfo['email'].'" disabled class="disabled">
                  <p><i>Note</i>: To change your email address you have to <a href="#">verify a change of email</a> using your current email address.</p>
                </div>

                <div class="setting-input-group input-profile-image">
                  <label>Profile Image</label>
                  <div class="input-flex">
                    <img src="'.$userInfo['photo'].'" alt="Your profile image">
                    <div class="input-flex-right">
                      <button class="change-profile" onclick="settingsChangeProfile()">Change profile image</button>
                      <p><i>Note</i>: Valid file types are png, jpg and gif.</p>
                    </div>
                  </div>
                </div>

                <div class="setting-input-group input-banner-image">
                  <label>Banner Image</label>
                  <div class="input-flex">
                    '.$bannerText.'
                    <div class="input-flex-right">
                      <button class="change-banner" onclick="settingsChangeBanner()">Change banner image</button>
                      <p><i>Note</i>: Valid file types are png, jpg and gif.</p>
                    </div>
                  </div>
                </div>
              </div>
              <div id="changeProfile">
                <div class="changeProfile-modal">
                  <div onclick="closeChangeProfile()" class="changeProfile-close-button">&#215;</div>

                  <div class="changeProfile-top">
                    <h4>Change profile image</h4>
                    <div class="changeProfile-top-headings">
                      <a id="changeProfile-upload-heading" onclick="changeProfileSelectUpload()" class="selected">Upload file</a>
                      <a id="changeProfile-default-heading" onclick="changeProfileSelectDefault()">Default images</a>
                    </div>
                  </div>

                  <div id="changeProfile-content-upload" class="changeProfile-content selected t1">
                    <input type="file" id="changeProfile-file" accept=".gif,.jpg,.png"">
                    <div class="text-box">
                      <span>Drag profile image here</span>
                      <span>- or -</span>
                      <label for="changeProfile-file">Click here to select an image from your computer</label>
                    </div>
                    <div class="text-box">
                      <span>Drop it here!</span>
                    </div>
                    <div class="text-box">
                      <span>Loading...</span>
                    </div>
                    <div class="text-box">
                      <span>Your profile image will look like:</span>
                      <img alt="potential profile image" id="changeProfile-image">
                      <span>To confirm change, click the button below.</span>
                    </div>
                    <div class="text-box">
                      <span>Invalid file type.</span>
                      <span>Please use a png, jpg or gif.</span>
                      <label for="changeProfile-file">Drag and drop or click here to select a file from your computer</label>
                    </div>
                  </div>

                  <div id="changeProfile-content-default" class="changeProfile-content">
                    <p>Choose from one of our default profile pictures.</p>
                    <img id="default-profile-img-1" onclick="selectDefaultProfile(1)" src="https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fboy1.png?alt=media&token=75f31d22-eda5-4e98-a4c2-78f20f8150e5">
                    <img id="default-profile-img-2" onclick="selectDefaultProfile(2)" src="https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fboy2.png?alt=media&token=c04772cb-d3c3-4802-bec7-6f25ad750ed3">
                    <img id="default-profile-img-3" onclick="selectDefaultProfile(3)" src="https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fboy3.png?alt=media&token=4b0dd789-5712-4f88-bc0c-ba0bfbbdd141">
                    <img id="default-profile-img-4" onclick="selectDefaultProfile(4)" src="https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fgirl1.png?alt=media&token=c3a936a1-f712-42f7-98e8-d330d7cd982a">
                    <img id="default-profile-img-5" onclick="selectDefaultProfile(5)" src="https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fgirl2.png?alt=media&token=03d61835-82a4-4efb-bec0-ac5627d14ee5">
                    <img id="default-profile-img-6" onclick="selectDefaultProfile(6)" src="https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fgirl3.png?alt=media&token=36624058-8570-4859-92a7-c8b55cc67d93">
                  </div>

                  <div class="changeProfile-bottom">
                    <a onclick="updateProfileImage()" id="changeProfile-button" class="disabled">Change Profile Image</a>
                    <a onclick="closeChangeProfile()">Cancel</a>
                  </div>
                </div>
              </div>
              <div id="changeBanner">
                <div class="changeBanner-modal">
                  <div onclick="closeChangeBanner()" class="changeBanner-close-button">&#215;</div>

                  <div class="changeBanner-top">
                    <h4>Change banner image</h4>
                    <div class="changeBanner-top-headings">
                      <a id="changeBanner-upload-heading" onclick="changeBannerSelectUpload()" class="selected">Upload file</a>
                      <a id="changeBanner-default-heading" onclick="changeBannerSelectDefault()">Default banners</a>
                    </div>
                  </div>

                  <div id="changeBanner-content-upload" class="changeBanner-content selected t1">
                    <input type="file" id="changeBanner-file" accept=".gif,.jpg,.png"">
                    <div class="text-box">
                      <span>Drag banner image here</span>
                      <span>- or -</span>
                      <label for="changeBanner-file">Click here to select an image from your computer</label>
                    </div>
                    <div class="text-box">
                      <span>Drop it here!</span>
                    </div>
                    <div class="text-box">
                      <span>Loading...</span>
                    </div>
                    <div class="text-box">
                      <span>Your banner image will look like:</span>
                      <img alt="potential banner image" id="changeBanner-image">
                      <span>To confirm change, click the button below.</span>
                    </div>
                    <div class="text-box">
                      <span>Invalid file type.</span>
                      <span>Please use a png, jpg or gif.</span>
                      <label for="changeBanner-file">Drag and drop or click here to select a file from your computer</label>
                    </div>
                  </div>

                  <div id="changeBanner-content-default" class="changeBanner-content">
                    <p>Choose from one of our default banner images.</p>
                    <div id="default-banner-img-1" onclick="selectDefaultBanner(1)" style="background-image: url(https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fbg-1.jpg?alt=media&token=42b445b8-154a-497e-8b1f-5afb64a7dc94);"></div>
                    <div id="default-banner-img-2" onclick="selectDefaultBanner(2)" style="background-image: url(https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fbg-5.jpg?alt=media&token=b84f7a38-15ee-4fe3-9917-dd07774de989);"></div>
                    <div id="default-banner-img-3" onclick="selectDefaultBanner(3)" style="background-image: url(https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fbg-3.jpg?alt=media&token=93c1b15b-1a3f-4048-a5e6-24e10dc51d60);"></div>
                    <div id="default-banner-img-4" onclick="selectDefaultBanner(4)" style="background-image: url(https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fbg-4.jpg?alt=media&token=57be9cc1-7163-4b30-b25d-8d4721472fb4);"></div>
                    <div id="default-banner-img-5" onclick="selectDefaultBanner(5)" style="background-image: url(https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fbg-2.jpg?alt=media&token=992ceeb7-4ef7-457e-88e5-39d712a30bcc);"></div>
                    <div id="default-banner-img-6" onclick="selectDefaultBanner(6)" style="background-image: url(https://firebasestorage.googleapis.com/v0/b/involve-a4e62.appspot.com/o/profile-img%2Fbg-6.jpg?alt=media&token=dce2356d-1e27-4bec-ad33-7ce55c43d410);"></div>
                  </div>

                  <div class="changeBanner-bottom">
                    <a onclick="updateBannerImage()" id="changeBanner-button" class="disabled">Change Banner Image</a>
                    <a onclick="closeChangeBanner()">Cancel</a>
                  </div>
                </div>
              </div>';
      } else if ($type == "se") {
        echo '<div id="settings-security" class="settings-window">
                <h4>Keep your account safe</h4>
                <h5>You control your personal information. With these settings you can control who can interact with you. Or maybe you just want to change your password?</h5>

                <div class="setting-input-group">
                  <label>Change password</label>
                  <input type="password" placeholder="Current Password">
                  <div class="input-flex">
                    <input type="password" placeholder="New Password">
                    <input type="password" placeholder="Confirm New Password">
                  </div>
                  <button class="disabled">Yes, I wish to change my password</button>
                </div>

                <div class="setting-input-group">
                  <label>Privacy settings</label>
                  <p>These settings can be changed at any time to control who can see your information and where. Please also note that only peers and your teachers can search for you by default.</p>
                  <div class="setting-input-checkbox">
                    <div class="checkbox-disabled">
                      <input type="checkbox" id="s-p-c-1" checked disabled>
                      <label for="s-p-c-1">Allow account to be searched for by other users.</label>
                    </div>
                    <div>
                      <input type="checkbox" id="s-p-c-2">
                      <label for="s-p-c-2">Allow account to be viewed by peers.</label>
                    </div>
                    <div>
                      <input type="checkbox" id="s-p-c-3" checked>
                      <label for="s-p-c-3">Show which school I am at on my profile.</label>
                    </div>
                    <div>
                      <input type="checkbox" id="s-p-c-4" checked>
                      <label for="s-p-c-4">Allow peers to view my email address.</label>
                    </div>
                    <div>
                      <input type="checkbox" id="s-p-c-5" checked>
                      <label for="s-p-c-5">Allow others to request schedule.</label>
                    </div>
                  </div>
                </div>

                <div class="setting-input-group">
                  <label>Blocked Users</label>
                  <p>Users that you block cannot send you messages or see your profile. However they can see messages and comments made in groups they are in.</p>
                  <div class="setting-blocked-users">
                    <div class="blocked-user">Aidan Hunt <a onclick="">Unblock</a></div>
                    <div class="blocked-user">Gabriel Aravena Brogan <a onclick="">Unblock</a></div>
                  </div>
                  <br>
                  <input type="text" id="input-block-user" placeholder="Search for a user to block them...">
                </div>
              </div>';
      } else if ($type == "no"){
        echo '<div id="settings-notifications" class="settings-window">
                <h4>Control when and how you\'re notified</h4>
                <h5>How and when do you want to be notified? You let us know with these settings, and we will tailor your notifications.</h5>

                <div class="setting-input-group">
                  <label>Email notifications</label>
                  <p>Emails will be sent to your email address when:</p>
                  <div class="setting-input-checkbox">
                    <div>
                      <input type="checkbox" id="s-e-c-1" checked>
                      <label for="s-e-c-1">User sends a private message</label>
                    </div>
                    <div>
                      <input type="checkbox" id="s-e-c-2" checked>
                      <label for="s-e-c-2">User creates a post in a group you\'re in</label>
                    </div>
                    <div>
                      <input type="checkbox" id="s-e-c-3" checked>
                      <label for="s-e-c-3">Assessment due soon</label>
                    </div>
                    <div>
                      <input type="checkbox" id="s-e-c-4" checked>
                      <label for="s-e-c-4">User requests schedule</label>
                    </div>
                  </div>
                </div>

                <div class="setting-input-group">
                  <label>Application updates</label>
                  <p>Involve would like to keep you informed about everything relevant to you.</p>
                  <div class="setting-input-checkbox">
                    <div>
                      <input type="checkbox" id="s-a-c-1" checked>
                      <label for="s-a-c-1">Send emails with updates to the Involve application (don\'t worry we won\'t spam your inbox)</label>
                    </div>
                    <div>
                      <input type="checkbox" id="s-a-c-2" checked>
                      <label for="s-a-c-2">Inform me when there is a new version of desktop application</label>
                    </div>
                  </div>
                </div>

                <div class="setting-input-group">
                  <label>In-Application notifications</label>
                  <p>Customise how you are informed of notifications while you are using the application.</p>
                  <div class="setting-input-checkbox">
                    <div>
                      <input type="checkbox" id="s-i-c-1" checked>
                      <label for="s-i-c-1">Beep when message is recieved</label>
                    </div>
                    <div>
                      <input type="checkbox" id="s-i-c-2" checked>
                      <label for="s-i-c-2">Notify me when new post is made</label>
                    </div>
                    <div>
                      <input type="checkbox" id="s-i-c-2" checked>
                      <label for="s-i-c-2">Notify me when a user replies to any of my posts</label>
                    </div>
                  </div>
                </div>

              </div>';
      }
      ?>
    </div>

    <script src="https://cdn.filesizejs.com/filesize.min.js"></script>
    <script src="https://www.gstatic.com/firebasejs/3.6.4/firebase.js"></script>
    <script>
      // change of name variables
      var settingDefaultFirstName = '<?php echo $userInfo['firstName'] ?>',
          settingDefaultLastName = '<?php echo $userInfo['lastName'] ?>',
          inputFirstName = document.getElementById("input-firstName"),
          inputLastName = document.getElementById("input-lastName"),
          settingUpdateNameButton = document.getElementById("setting-updateName-button");

      // check whether the values in the input are equal to the current (default) values
      // based on this change the button from disabled to not (or vise versa)
      var settingsCheckNames = function() {
        if(inputFirstName.value.trim() != settingDefaultFirstName || inputLastName.value.trim() != settingDefaultLastName) {
          settingUpdateNameButton.className = '';
          return true;
        } else {
          settingUpdateNameButton.className = 'disabled';
          return false;
        }
      };

      // when any key is pressed in the input fields
      inputFirstName.onkeyup = settingsCheckNames;
      inputLastName.onkeyup = settingsCheckNames;

      // user has requested to change their name
      var settingsUpdateName = function() {
        // if the button is not disabled
        if(settingUpdateNameButton.className == '') {
          // if there has been changes to the inputs
          if(settingsCheckNames()) {
            alert('TODO');
          }
        }
      };

      // user has started searching for a school
      var inputSchoolSearch = document.getElementById("input-school");
      inputSchoolSearch.onkeyup = function(e) {
        alert('TODO');
      }

      // FIREBASE initialisation process
      var config = {
        apiKey: "AIzaSyBhl6sYMC2vY-6khkDD-61zIJ3GY5UTqyk",
        storageBucket: "involve-a4e62.appspot.com"
      };
      firebase.initializeApp(config);
      var storageRef = firebase.storage().ref();

      // change profile image variables
      var cpWindow = document.getElementById("changeProfile"),
          cpUploadHeading = document.getElementById("changeProfile-upload-heading"),
          cpDefaultHeading = document.getElementById("changeProfile-default-heading"),
          cpUploadContent = document.getElementById("changeProfile-content-upload"),
          cpDefaultContent = document.getElementById("changeProfile-content-default"),
          cpDraggable = document.getElementById("changeProfile-content-upload"), // elements needed for drag and drop
          cpFileInput = document.getElementById("changeProfile-file"),
          cpButton = document.getElementById("changeProfile-button"),
          newProfileImageURL = '',
          imageUploaded = '';

      // user has requested to change their profile picture
      var settingsChangeProfile = function() {
        cpWindow.className = 'active';
      };

      // user has requested to close the 'change profile' window
      var closeChangeProfile = function() {
        cpWindow.className = '';
      };

      // user wishes to upload photo
      var changeProfileSelectUpload = function() {
        cpUploadHeading.className = 'selected';
        cpDefaultHeading.className = '';
        cpDefaultContent.className = 'changeProfile-content';

        // return to the view of potential image if there was one uploaded
        if(imageUploaded) {
          cpUploadContent.className = 'changeProfile-content selected t4';
        } else {
          cpUploadContent.className = 'changeProfile-content selected t1';
        }
      };

      // user wishes to use a default photo
      var changeProfileSelectDefault = function() {
        cpUploadHeading.className = '';
        cpDefaultHeading.className = 'selected';
        cpUploadContent.className = 'changeProfile-content';
        cpDefaultContent.className = 'changeProfile-content selected';
      };

      // upload any files added by the user
      var uploadProfileFile = function(evt) {
        // stop the adding of a file from doing anything by default
        evt.stopPropagation();
        evt.preventDefault();

        // show a 'loading...' message on the screen
        cpDraggable.className = 'changeProfile-content selected t3';

        // get all the information needed from the upload to create the file
        var file = evt.target.files[0],
            metadata = { 'contentType': file.type };

        // if file type is not valid
        if(file.type != 'image/jpeg' && file.type != 'image/png' && file.type != 'image/gif') {

          cpDraggable.className = 'changeProfile-content selected t5';
          newProfileImageURL = '';
          imageUploaded = '';
          cpButton.className = 'disabled';

        } else {

          // create the file on the external server
          storageRef.child('profile-img/<?php echo $userInfo['link'].md5(uniqid('', true)); ?>').put(file, metadata).then(function(snapshot) {
            // get the external URL of the newly created file
            newProfileImageURL = snapshot.metadata.downloadURLs[0];
            imageUploaded = snapshot.metadata.downloadURLs[0];
            document.getElementById('changeProfile-image').src = newProfileImageURL;
            cpDraggable.className = 'changeProfile-content selected t4';
            cpButton.className = '';
          });

        }
      };

      // if the files change (dragged or manually inputted)
      cpFileInput.addEventListener('change', uploadProfileFile, false);

      // show that that a file is being dragged over
      ['dragover','dragenter'].forEach(function(event) {
        cpFileInput.addEventListener(event, function() {
          // add some effect saying to realease file
          cpDraggable.className = 'changeProfile-content selected t2';
        });
      });

      // remove any effect left behind by a file being dragged over the box
      ['dragleave','dragend'].forEach( function(event) {
        cpFileInput.addEventListener(event, function() {
          // go back to the first text box
          cpDraggable.className = 'changeProfile-content selected t1';
        });
      });

      // user has selected one of the default images
      var selectDefaultProfile = function(num) {
        // go through each image and either remove any classes
        for(i = 1; i <=6; i++) {
          // if the image is the one the user selected add the 'selected' class
          // also set the profile image url to the URL of the selected image
          if(i == num) {
            var profileImgElem = document.getElementById("default-profile-img-"+i);
            profileImgElem.className = 'selected';
            newProfileImageURL = profileImgElem.src;
            cpButton.className = '';
          } else {
            document.getElementById("default-profile-img-"+i).className = '';
          }
        }
      }

      // actually change the profile image in the database
      var updateProfileImage = function() {
        if(newProfileImageURL) {
          alert('todo');
        } else {
          alert('todo: case cancel');
        }
      };

      // change profile image variables
      var biWindow = document.getElementById("changeBanner"),
          biUploadHeading = document.getElementById("changeBanner-upload-heading"),
          biDefaultHeading = document.getElementById("changeBanner-default-heading"),
          biUploadContent = document.getElementById("changeBanner-content-upload"),
          biDefaultContent = document.getElementById("changeBanner-content-default"),
          biDraggable = document.getElementById("changeBanner-content-upload"), // elements needed for drag and drop
          biFileInput = document.getElementById("changeBanner-file"),
          biButton = document.getElementById("changeBanner-button"),
          newBannerImageURL = '',
          imageUploaded = '';

      // user has requested to change their banner photo
      var settingsChangeBanner = function() {
        biWindow.className = 'active';
      };

      // user has requested to close the 'change banner' window
      var closeChangeBanner = function() {
        biWindow.className = '';
      };

      // user wishes to upload photo
      var changeBannerSelectUpload = function() {
        biUploadHeading.className = 'selected';
        biDefaultHeading.className = '';
        biDefaultContent.className = 'changeBanner-content';

        // return to the view of potential image if there was one uploaded
        if(imageUploaded) {
          biUploadContent.className = 'changeBanner-content selected t4';
        } else {
          biUploadContent.className = 'changeBanner-content selected t1';
        }
      };

      // user wishes to use a default photo
      var changeBannerSelectDefault = function() {
        biUploadHeading.className = '';
        biDefaultHeading.className = 'selected';
        biUploadContent.className = 'changeBanner-content';
        biDefaultContent.className = 'changeBanner-content selected';
      };

      // upload any files added by the user
      var uploadBannerFile = function(evt) {
        // stop the adding of a file from doing anything by default
        evt.stopPropagation();
        evt.preventDefault();

        // show a 'loading...' message on the screen
        biDraggable.className = 'changeBanner-content selected t3';

        // get all the information needed from the upload to create the file
        var file = evt.target.files[0],
            metadata = { 'contentType': file.type };

        // if file type is not valid
        if(file.type != 'image/jpeg' && file.type != 'image/png' && file.type != 'image/gif') {

          biDraggable.className = 'changeBanner-content selected t5';
          newBannerImageURL = '';
          imageUploaded = '';
          biButton.className = 'disabled';

        } else {

          // create the file on the external server
          storageRef.child('banner-img/<?php echo $userInfo['link'].md5(uniqid('', true)); ?>').put(file, metadata).then(function(snapshot) {
            // get the external URL of the newly created file
            newBannerImageURL = snapshot.metadata.downloadURLs[0];
            imageUploaded = snapshot.metadata.downloadURLs[0];
            document.getElementById('changeBanner-image').src = newBannerImageURL;
            biDraggable.className = 'changeBanner-content selected t4';
            biButton.className = '';
          });

        }
      };

      // if the files change (dragged or manually inputted)
      biFileInput.addEventListener('change', uploadBannerFile, false);

      // show that that a file is being dragged over
      ['dragover','dragenter'].forEach(function(event) {
        biFileInput.addEventListener(event, function() {
          // add some effect saying to realease file
          biDraggable.className = 'changeBanner-content selected t2';
        });
      });

      // remove any effect left behind by a file being dragged over the box
      ['dragleave','dragend'].forEach( function(event) {
        biFileInput.addEventListener(event, function() {
          // go back to the first text box
          biDraggable.className = 'changeBanner-content selected t1';
        });
      });

      // user has selected one of the default images
      var selectDefaultBanner = function(num) {
        // go through each image and either remove any classes
        for(i = 1; i <=6; i++) {
          // if the image is the one the user selected add the 'selected' class
          // also set the profile image url to the URL of the selected image
          if(i == num) {
            var bannerImgElem = document.getElementById("default-banner-img-"+i);
            bannerImgElem.className = 'selected';
            newBannerImageURL = bannerImgElem.style.backgroundImage.slice(5, -2);
            biButton.className = '';
          } else {
            document.getElementById("default-banner-img-"+i).className = '';
          }
        }
      }

      // actually change the profile image in the database
      var updateBannerImage = function() {
        if(newBannerImageURL) {
          alert('todo');
        } else {
          alert('todo: case cancel');
        }
      };
    </script>
  </body>
</html>
