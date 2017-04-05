<?php

session_start();
if($_SESSION['token']) {
  header('Location: home');
}

include_once "php/connect.php";

// sanitise any post inputs
$email = $conn->real_escape_string($_POST['email']);
$pwd = $conn->real_escape_string($_POST['password']);

// if the user has tried to log in
if($email && $pwd) {
  // select the rows in the database with the inputted email address
  $sql = "SELECT userID,password FROM users WHERE email='{$email}';";
  $result = $conn->query($sql);

  // if an account with that email exists
  if ($result->num_rows == 1) {
    while($row = $result->fetch_assoc()) {
      // if the password is correct
      if (password_verify($pwd, $row['password'])) {

        // generate a randomised 60-digit token (to be used to verify user without storing password or easily modified user id)
        $token = password_hash(bin2hex(openssl_random_pseudo_bytes(10)), PASSWORD_BCRYPT, ['cost'=>10]);
        $_SESSION['token'] = $token;

        // add the token to the mysql db
        $sql2 = "UPDATE users SET token='".$token."' WHERE userID={$row['userID']};";
        $query = $conn->query($sql2);

        // send the user to home.php (feed)
        header('Location: home');
      } else {
        // Password wrong
        echo "<script>document.addEventListener('DOMContentLoaded', function(event){ passwordWrong(); });</script>";
      }
    }
  } else {
    // No email found with that email address
    echo "<script>document.addEventListener('DOMContentLoaded', function(event){ emailWrong(); });</script>";
  }
} else if ($email){
  // the password inputed was incorrect or not filled in
  echo "<script>document.addEventListener('DOMContentLoaded', function(event){ passwordWrong(); });</script>";
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Involve For Education</title>

    <link rel="stylesheet" href="css/index.css" />
  </head>
  <body>
    <div id="blur-zone">

      <!-- navigation -->
      <div id="navigation" class="bg">
        <h1><span class="bold">Involve</span> for Education</h1>

        <div class="pull-right">
          <a href="info">Information</a>
          <a href="download">Download</a>
          <a href="" id="nav-signin">Sign In</a>
        </div>
      </div>

      <div id="content">

        <!-- banner (with hero image) -->
        <section id="hero">
          <div id="hero-modal">
            <div class="subheading-line">Connect at School</div>
            <p>Involve aims to connect students and teachers both within and outside the classroom.</p>
            <a href="createaccount" class="btn btn-blue">Create Account</a>
          </div>
        </section>

        <!-- teacher information section -->
        <section id="info-teacher">
          <div class="info-decoration">
            <div class="info-colour"></div>
            <img id="teacher-img" src="images/index/laptop-teacher.png" alt="" />
          </div>
          <div class="info-content">
            <div class="info-wrap">
              <div class="subheading">Involve simplifies teaching.</div>
              <ul>
                <li>Create an account in just seconds</li>
                <li>Make a class or use your school code</li>
                <li>Give students class code to facilitate learning</li>
                <li>Write class plans and organise resources</li>
              </ul>
              <p>From there you can start teaching - doing what you do best. Involve will faciliate learning, and help you and your students communicate. For more information <a href="info">click here</a>.</p>
              <a href="start-teaching" class="btn btn-red">Start Teaching</a>
            </div>
          </div>
        </section>

        <div id="img-banner"></div>

        <!-- student information section -->
        <section id="info-student">
          <div class="info-content">
            <div class="info-wrap">
              <div class="subheading">Involve is made for students.</div>
              <ul>
                <li>Create an account in a few clicks</li>
                <li>Sign up to classes using a class code or student code</li>
                <li>Organise your learning into one place</li>
              </ul>
              <p>The rest is up to you, but Involve will try it's best to keep you on track. Worried about security? Or want to find out more information <a href="info">click here</a>.</p>
              <a href="start-learning" class="btn btn-yellow">Start Learning</a>
            </div>
          </div>
          <div class="info-decoration">
            <div class="info-colour"></div>
            <img id="student-img" src="images/index/ipad-student.png" alt="" />
          </div>
        </section>

        <!-- download section -->
        <section id="download">
          <div id="download-top">
            <div id="download-subheading">Use <span>Involve</span> on your desktop.</div>
            <img id="download-image" src="images/index/application.png" alt="" />
          </div>
          <div id="download-bottom">
            <p>Select your platform.</p>
            <a href="download/mac" class="btn btn-blue">
              <svg width="15" height="15" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#ffffff">
                <path d="M 256.50,384.00c-66.156,0.00-130.837-9.686-193.05-28.849l 10.124-30.371C 132.526,342.859, 193.812,352.00, 256.50,352.00 c 8.172,0.00, 16.319-0.164, 24.441-0.474C 283.054,312.746, 288.00,288.00, 288.00,288.00s-32.00,0.00-96.00,0.00c0.00-87.505, 29.909-165.027, 62.465-223.50L0.00,64.50 l0.00,383.00 l 283.088,0.00 c-2.336-22.842-3.152-44.43-3.077-63.92C 272.195,383.854, 264.358,384.00, 256.50,384.00z M 96.00,128.00l 32.00,0.00 l0.00,64.00 L 96.00,192.00 L 96.00,128.00 zM 512.00,64.50L 298.435,64.50 c-0.974,1.598-1.947,3.207-2.92,4.843c-19.208,32.286-34.468,65.379-45.358,98.36 c-9.103,27.566-15.151,55.116-18.086,82.297l 102.429,0.00 l-9.229,45.417c-0.157,0.808-4.085,21.422-6.10,53.471 c 40.932-4.063, 81.111-12.103, 120.257-24.107l 10.124,30.371c-42.783,13.178-86.732,21.874-131.526,26.021 c-0.018,2.188-0.028,4.395-0.021,6.64c 0.062,20.315, 1.173,40.285, 3.301,59.688L 512.00,447.501 L 512.00,64.50 z M 416.00,192.00l-32.00,0.00 l0.00-64.00 l 32.00,0.00 L 416.00,192.00 zM 293.632,512.00l 39.084,0.00 c-2.73-10.907-5.094-22.096-7.069-33.543c-1.747-10.126-3.191-20.46-4.343-30.957l-38.216,0.00 C 285.204,468.189, 288.563,489.901, 293.632,512.00zM 344.224,0.00l-48.198,0.00 c-12.875,17.435-27.466,39.185-41.56,64.50l 43.969,0.00 C 315.272,36.844, 332.017,14.799, 344.224,0.00z" ></path>
              </svg>
              Download for Mac
            </a>
            <a href="download/window" class="btn btn-grey">
              <svg width="15" height="15" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" fill="#95a5a6">
                <path d="M 0.175,256.00 L 0.00,99.963 L 192.00,73.891 L 192.00,256.00 ZM 224.00,69.241 L 479.936,32.00 L 479.936,256.00 L 224.00,256.00 ZM 479.999,288.00 L 479.936,512.00 L 224.00,475.992 L 224.00,288.00 ZM 192.00,471.918 L 0.156,445.621 L 0.146,288.00 L 192.00,288.00 Z" ></path>
              </svg>
              Download for Windows
            </a>
          </div>
        </section>

      </div>
    </div>


    <div id="sign-in-modal">
      <a href="" id="sign-in-close">
        <div class="elem-1"></div>
        <div class="elem-2"></div>
      </a>
      <div class="subheading-line">Sign In</div>
      <div class="subtext">To access Involve.</div>
      <form action="index.php" method="post">
        <input type="text" name="email" placeholder="Email Address" id="signin-email" value="<?php if($email) {echo $email;}?>">
        <input type="password" name="password" placeholder="Password" id="signin-pwd" value="<?php if($pwd) {echo $pwd;}?>">
        <button type="submit" name="submit">Sign In</button>
      </form>
      <div class="a-container">
        <a href="createaccount">Create Account</a>
      </div>
    </div>

    <script src="js/index.js"></script>
  </body>
</html>
