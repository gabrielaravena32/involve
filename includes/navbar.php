
<!-- Navbar accross the top of most pages -->
<nav>

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
    <img src='<?php echo $userInfo["photo"]."' alt='Profile photo of ".$userInfo['firstName']; ?>' id="navbar-userIMG">
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

  nav * {
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
    width: calc(100vw - 635.172px);
    margin-left: 60px;
  }

  nav .navbar-search-wrapper * {
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
