
<!-- Navbar accross the top of most pages -->
<nav id="navbar">

  <!-- Title -->
  <h1><span>Involve&nbsp;</span>for Education</h1>

  <!-- Search bar -->
  <div class="navbar-search-wrapper">
    <div class="navbar-search-icon">
      <svg xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:cc="http://creativecommons.org/ns#" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:svg="http://www.w3.org/2000/svg" xmlns="http://www.w3.org/2000/svg" xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd" xmlns:inkscape="http://www.inkscape.org/namespaces/inkscape" viewBox="0 -256 1792 1792" width="40px" height="40px" style="border: none; position: relative; transform: translate(0,-14px)">
        <g transform="matrix(1,0,0,-1,60.745763,1201.8983)">
          <path d="m 1152,704 q 0,185 -131.5,316.5 Q 889,1152 704,1152 519,1152 387.5,1020.5 256,889 256,704 256,519 387.5,387.5 519,256 704,256 889,256 1020.5,387.5 1152,519 1152,704 z m 512,-832 q 0,-52 -38,-90 -38,-38 -90,-38 -54,0 -90,38 L 1103,124 Q 924,0 704,0 561,0 430.5,55.5 300,111 205.5,205.5 111,300 55.5,430.5 0,561 0,704 q 0,143 55.5,273.5 55.5,130.5 150,225 94.5,94.5 225,150 130.5,55.5 273.5,55.5 143,0 273.5,-55.5 130.5,-55.5 225,-150 94.5,-94.5 150,-225 Q 1408,847 1408,704 1408,484 1284,305 l 343,-343 q 37,-37 37,-90 z" id="path3029" inkscape:connector-curvature="0" style="fill:currentColor"/>
        </g>
      </svg>
    </div>
    <input id="navbar-search" type="text" placeholder="Search for posts, people, topics..." />

    <div id="navbar-search-results"></div>
  </div>

  <!-- Menu icon for smaller screens -->
  <div id="navbar-menu-icon">&#9776;</div>

  <!-- Pull to the right -->
  <div class="pull-right">
    <img src='<?php echo $userInfo['photo']."' alt='Profile photo of ".$userInfo['firstName']; ?>' id="navbar-userIMG" onclick="accountDropDownToggle()">
    <div id="account-dropdown">
      <span>Logged in as:</span>
      <span><?php echo $userInfo['firstName'].' '.$userInfo['lastName']; ?></span>
      <a href="user/<?php echo $userInfo['link']; ?>">
        <svg width="15px" height="15px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1000 1000" >
          <g><path d="M602.4,558.7H397.6C222.2,558.7,80,708.5,80,892.9v21.5c0,75.5,142.1,75.6,317.6,75.6h204.9c175.3,0,317.6-2.8,317.6-75.6v-21.5C920,708.5,777.8,558.7,602.4,558.7L602.4,558.7z M251.9,264.8c0-33.4,6.4-66.7,18.9-97.5c12.4-30.8,30.8-59.1,53.8-82.6C347.5,61,375,42.1,405,29.4c30-12.8,62.5-19.4,95-19.4c32.5,0,64.9,6.6,95,19.4c30,12.7,57.5,31.6,80.5,55.2c23,23.6,41.3,51.8,53.8,82.6c12.4,30.8,18.9,64.2,18.9,97.5s-6.4,66.7-18.9,97.5c-12.4,30.8-30.8,59.1-53.8,82.6c-23,23.6-50.5,42.5-80.5,55.2c-30,12.8-62.5,19.4-95,19.4c-32.5,0-64.9-6.7-95-19.4c-30-12.8-57.5-31.6-80.5-55.2c-23-23.5-41.3-51.8-53.8-82.6C258.3,331.4,251.9,298.1,251.9,264.8L251.9,264.8z"></path></g>
        </svg>
        Profile
      </a>
      <a href="settings">
        <svg width="15px" height="15px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1000 1000">
          <g><path d="M864.2,548c2-15.7,3.4-31.4,3.4-48c0-16.7-1.5-32.4-3.4-48l103.4-80.9c9.3-7.4,11.7-20.6,5.9-31.4l-98-169.5c-5.9-10.8-19.1-14.7-29.9-10.8l-122,49c-25.5-19.6-53-35.7-82.8-48L622.1,30.6c-1.5-11.7-11.7-20.6-24-20.6h-196c-12.3,0-22.5,8.8-24,20.6l-18.6,129.9c-29.9,12.3-57.3,28.9-82.8,48l-122.1-49c-11.3-4.4-24,0-29.9,10.8l-98,169.5c-6.3,10.8-3.4,24,5.9,31.4L136,452c-2,15.7-3.4,31.8-3.4,48c0,16.2,1.5,32.4,3.4,48L32.6,628.9c-9.3,7.3-11.7,20.6-5.9,31.4l98,169.5c5.9,10.8,19.1,14.7,29.9,10.8l122-49c25.5,19.6,53,35.7,82.8,48L378,969.4c1.5,11.7,11.7,20.6,24,20.6h196.1c12.3,0,22.5-8.8,24-20.6l18.6-129.9c29.9-12.3,57.3-28.9,82.8-48l122,49c11.3,4.4,24,0,29.9-10.8l98-169.5c5.9-10.8,3.4-24-5.9-31.4L864.2,548z M500.1,671.5c-94.5,0-171.5-77-171.5-171.5s77-171.5,171.5-171.5s171.5,77,171.5,171.5S594.6,671.5,500.1,671.5z"></path></g>
        </svg>
        Settings
      </a>
      <a onclick="logOut()">
        <svg xmlns="http://www.w3.org/2000/svg" width="15px" height="15px" viewBox="0 0 32 32" enable-background="new 0 0 32 32">
          <path id="polygon3027" style="" d="m 18,24 0,4 -14,0 0,-24 14,0 0,4 4,0 0,-8 -22,0 0,32 22,0 0,-8 z m -6,-4.003 0,-8 12,0 0,-4 8,8 -8,8 0,-4 z"/>
        </svg>
        Log Out
      </a>
    </div>
  </div>

</nav>

<style>
  nav {
    position: fixed;
    box-sizing: border-box;
    width: 100%;
    height: 68px;
    background: white;
    box-shadow: 0 0 3px 3px rgba(0,0,0,0.2);
    padding: 0 50px;
    z-index: 1;
  }

  nav > * {
    position: relative;
    float: left;
    height: 40px;
    padding: 14px 0;
    margin: 0;
  }

  nav h1 {
    line-height: 40px;
    font-weight: 300;
    margin-right: 30px;
  }

  nav h1 span {
    font-weight: 500;
    margin: 0;
    padding: 0;
  }

  nav .navbar-search-wrapper {
    width: calc(100vw - 645.172px);
    margin-left: 60px;
  }

  nav .navbar-search-wrapper * {
    position: relative;
    float: left;
    height: 40px;
    padding: 14px 0;
    margin: 0;
    position: relative;
    box-sizing: border-box;
    height: 40px;
    float: left;
    border: 1px solid rgb(217,217,217);
  }

  nav .navbar-search-icon {
    width: 40px;
    border-right: none;
    border-radius: 3px 0 0 3px;
  }

  nav .navbar-search-wrapper input {
    font-size: 13px;
    width: calc(100% - 40px);
    padding: 6px;
    line-height: 0;
    border-radius: 0 3px 3px 0;
  }

  nav .navbar-search-wrapper input:hover, nav .navbar-search-wrapper input:active, nav .navbar-search-wrapper input:focus {
    outline: none;
  }

  nav .navbar-search-wrapper #navbar-search-results {
    display: none;
    width: calc(100% - 40px);
    margin: -1px 0 0 40px;
    background: #fdfdfd;
  }

  nav .navbar-search-wrapper #navbar-search-results.active {
    display: block;
  }

  nav .pull-right {
    width: 200px;
    float: right;
  }

  nav .pull-right #navbar-userIMG {
    display: block;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    float: right;
    margin: 5px 0;
    padding: 0;
  }

  nav .pull-right #account-dropdown {
    display: none;
    position: absolute;
    width: 150px;
    right: 0;
    top: 55px;
    padding: 10px 20px;
    background: white;
    box-shadow: 2px 2px 5px 2px rgba(0,0,0,0.2);
  }

  nav .pull-right #account-dropdown.active {
    display: block;
  }

  #account-dropdown span {
    font-size: 14px;
    display: block;
  }

  #account-dropdown span:nth-child(1) {
    margin-top: 10px;
    color: #555;
  }

  #account-dropdown span:nth-child(2) {
    padding-bottom: 10px;
    border-bottom: 1px solid #333;
  }

  #account-dropdown a {
    display: block;
    padding: 4px 0;
    text-decoration: none;
    color: inherit;
    font-size: 15px;
    line-height: 15px;
    margin: 10px 0;
    cursor: pointer;
  }

  #account-dropdown a svg {
    float: left;
    margin-right: 5px;
  }

  nav #navbar-menu-icon {
    display: none;
  }

  @media (max-width: 1100px) {
    nav .pull-right {
      display: none;
    }

    nav .navbar-search-wrapper  {
      width: calc(100% - 350px);
    }

    nav #navbar-menu-icon {
      font-size: 18px;
      display: block;
      height: 20px;
      line-height: 20px;
      float: right;
      padding: 0;
      margin-top: 24px;
      cursor: pointer;
    }
  }
</style>


<script type="text/javascript">

var dropdown = false;
var accountDropDownToggle = function(a = false) {
  // if the dropdown menu is already down or the function explicitly calls for it to be removed
  if(dropdown === true || a) {
    // set the dropdown variable to show that it is now closed
    dropdown = false;

    // remove the active class (hiding the dropdown)
    document.getElementById('account-dropdown').className = '';

    // remove all the event listeners
    document.getElementById('content').onmouseover = null;
  } else {
    // set the dropdown variable to show that it is now open
    dropdown = true;

    // show the dropdown menu (with active class)
    document.getElementById('account-dropdown').className = 'active';

    // create an event listener for hovering over the body of the application (not on the drop down)
    document.getElementById('content').onmouseover = function() {
      // hide the dropdown
      accountDropDownToggle(true);
    };
  }
};


var logOut = function() {
  var request;
  if (window.XMLHttpRequest) {
    request = new XMLHttpRequest();
  } else {
    request = new ActiveXObject("Microsoft.XMLHTTP");
  }
  request.open('GET', 'php/logout.php', true);
  request.send();

  location.href="home";
}

</script>
