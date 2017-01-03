<?php

// start the session (keep the user logged in)
session_start();

// if the user has a token stored in session (logged in)
if ($_SESSION['token']) {
  // send the user to the home page
  header("Location: home");
}

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Involve | Create Account</title>

    <link rel="stylesheet" href="css/createaccount.css" />
  </head>

  <?php
  if($_GET['t'] == 1) {
    echo "<body onload='activateWindow(3)'>";
  } else if($_GET['t'] == 2) {
    echo "<body onload='activateWindow(2)'>";
  } else {
    echo "<body onload='activateWindow(1)'>";
  }
  ?>

    <div class="window" id="createaccount-1">

      <div class="window-flex">
        <h2><span>Involve</span> for Education</h2>

        <p>To create an Involve Account we need to know some information about you, to personalise your service.<br /><br />First, are you a teacher or a student?</p>
        <a class="btn btn-yellow" onclick="activateWindow(2)" id="createaccount-first-o1">Student</a>
        <a class="btn btn-red" onclick="activateWindow(3)" id="createaccount-first-o2">Teacher</a>
      </div>
    </div>

    <div class="window" id="createaccount-2">

      <div class="window-flex">
        <h2><span>Involve</span> for Education</h2>

        <p>Okay time for all the boring stuff. It only takes a minute though.</p>
        <input type="email" id="create-account-s-email" placeholder="Email Address" onblur="checkEmail(this.value)"/>
        <input type="password" id="create-account-s-password" placeholder="Password" onkeyup="checkPassword(this.value)" onblur="checkPassword(this.value)">
        <input type="text" id="create-account-s-first" placeholder="First Name" onblur="checkName('s-first', this.value)">
        <input type="text" id="create-account-s-last" placeholder="Last Name" onblur="checkName('s-last', this.value)">

        <a class="btn btn-yellow" onclick="createAccount('s')">Create Account</a>
      </div>
    </div>

    <div class="window" id="createaccount-3">

      <div class="window-flex">
        <h2><span>Involve</span> for Education</h2>

        <p>Please fill in the following to complete your account.</p>
        <input type="email" id="create-account-t-email" placeholder="Email Address" onblur="checkEmail(this.value)">
        <input type="password" id="create-account-t-password" placeholder="Password" onkeyup="checkPassword(this.value)" onblur="checkPassword(this.value)">
        <select id="create-account-t-prefix" onblur="checkPrefix(this.value)">
          <option disabled selected>Prefix</option>
          <option value="Mr">Mr</option>
          <option value="Mrs">Mrs</option>
          <option value="Ms">Ms</option>
          <option value="Miss">Miss</option>
          <option value="Dr">Dr</option>
          <option value="Professor">Professor</option>
        </select>
        <div class="select-arrow" id="create-account-t-prefix-arrow"></div>
        <input type="text" id="create-account-t-first" placeholder="First Name" onblur="checkName('t-first', this.value)">
        <input type="text" id="create-account-t-last" placeholder="Last Name" onblur="checkName('t-last', this.value)">

        <a class="btn btn-red" onclick="createAccount('t')">Create Account</a>
      </div>
    </div>

    <div class="window" id="createaccount-4">
      <div class="window-flex">
        <h2><span>Involve</span> for Education</h2>

        <p>Your account has been created!<br /><br />All that is left to do is to join classes. This can either be done with individual class codes or an sCode.</p>

        <input class="input-code" type="text" id="student-code" placeholder="Class Code or sCode">

        <a href="home" class="regular-link">Remind me later.</a>
      </div>
    </div>

    <div class="window" id="createaccount-5">
      <div class="window-flex">
        <h2><span>Involve</span> for Education</h2>

        <p>Your account has been created.<br /><br />All that is left is to join a school using an sCode. If you do not know what this is please visit our <a href="info">information page</a>.</p>

        <input class="input-code" type="text" id="teacher-code" placeholder="School Code">

        <a href="home" class="regular-link">Remind me later.</a>
      </div>
    </div>

  <script src="js/createaccount.js"></script>

  </body>
</html>
