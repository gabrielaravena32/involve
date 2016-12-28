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
  $sql = "SELECT userID,password FROM users WHERE email='".$email."';";
  $result = $conn->query($sql);

  // if an account with that email exists
  if ($result->num_rows == 1) {
    while($row = $result->fetch_assoc()) {
      // if the password is correct
      if (password_verify($pwd, $row['password'])) {

        // generate a randomised 60-digit token (to be used to varify user without storing password or easily modified user id)
        $token = password_hash(bin2hex(openssl_random_pseudo_bytes(10)), PASSWORD_BCRYPT, ['cost'=>4]);
        $_SESSION['token'] = $token;

        // add the token to the mysql db
        $sql2 = "UPDATE users SET token='".$token."' WHERE userID=".$row['userID'].";";
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
          <a href="" id="nav-info">Information</a>
          <a href="" id="nav-download">Download</a>
          <a href="" id="nav-signin">Sign In</a>
        </div>
      </div>

      <div id="content">

        <!-- banner (with hero image) -->
        <section id="hero">
          <div id="hero-modal">
            <div class="subheading-line">Connect at School</div>
            <p>Involve aims to connect students and teachers through with and outside the classroom.</p>
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
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin pharetra eros ac dolor consectetur semper. Ut vel odio nibh. Nunc porta quis justo ac ullamcorper. Donec condimentum risus eu pharetra dictum. Cras euismod, turpis sed congue tempus, purus augue egestas odio, sed porta elit arcu in est. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              <a href="" class="btn btn-red">Start Teaching</a>
            </div>
          </div>
        </section>

        <div id="img-banner"></div>

        <!-- student information section -->
        <section id="info-student">
          <div class="info-content">
            <div class="info-wrap">
              <div class="subheading">Involve is made for students.</div>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin pharetra eros ac dolor consectetur semper. Ut vel odio nibh. Nunc porta quis justo ac ullamcorper. Donec condimentum risus eu pharetra dictum. Cras euismod, turpis sed congue tempus, purus augue egestas odio, sed porta elit arcu in est. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
              <a href="" class="btn btn-yellow">Start Learning</a>
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
            <a href="" class="btn btn-blue">Download for Mac</a>
            <a href="" class="btn btn-grey">Download for Windows</a>
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

    <script src="js/min/index-min.js"></script>
  </body>
</html>
