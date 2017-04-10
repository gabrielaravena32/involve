<div id="search-sidebar">
  <span id="search-sidebar-close">&#10005;</span>

  <div>
    <h4>Search for...</h4>
    <input type="text" id="search-sidebar-input" placeholder="People, files, posts and groups">
  </div>

  <div id="search-result">
    <img src="images/search/options.jpg">
  </div>
</div>

<div class="sidebar">
  <div class="flex-wrapper">
    <h1>Involve</h1>

    <svg id="search-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" viewBox="0 0 300 300" fill="#959595">
      <g><path stroke-width="9.756173" d="m35.938999,35.085999c-43.272999,43.272003 -43.379,114.158997 -0.105988,157.432007c36.662991,36.663025 93.201992,42.25 135.920986,16.765991l76.343994,74.234985c10.506989,10.197998 27.084991,9.821045 37.116974,-0.843994c10.03302,-10.664978 9.77002,-27.445984 -0.737976,-37.643982l-75.182983,-72.864014c26.360992,-42.846985 21.007996,-100.044983 -16.028015,-137.080994c-43.27298,-43.273008 -114.054993,-43.273008 -157.327,0l0,0l0.000008,0zm31.738979,31.739014c26.10202,-26.102024 67.746033,-26.102024 93.848022,0c26.10199,26.10199 26.10199,67.745972 0,93.847961c-26.10199,26.102051 -67.746002,26.102051 -93.848022,0c-26.10199,-26.10199 -26.10199,-67.745972 0,-93.847961z"/></g>
    </svg>

    <div class="sidebar-navigation">
      <a href="home" id="sidebar-nav-feed" <?php if ($navPageSel == 'feed') { echo 'class="selected"'; }?>>Feed</a>
      <a href="today" id="sidebar-nav-today">Today View</a>
      <br><br>
      <h3>Explore</h3>
      <a href="messages" id="sidebar-nav-messages">Messages</a>
      <a href="classes" id="sidebar-nav-classes" <?php if ($navPageSel == 'classes') { echo 'class="selected"'; }?>>Classes</a>
      <a href="timetable" id="sidebar-nav-timetable" <?php if ($navPageSel == 'timetable') { echo 'class="selected"'; }?>>Timetable</a>
      <a href="backpack" id="sidebar-nav-backpack">Backpack</a>
    </div>

    <button class="join-class-btn" onclick="toggleJoinClass()">+ Join A Class</button>
    <button id="student-code-btn" onclick="toggleSCode()">+ Enter an sCode</button>
  </div>
  <div class="flex-wrapper">
    <div class="sidebar-profile">
      <div class="sidebar-profile-user">
        <img src="<?php echo $userInfo['photo']; ?>" alt="">
        <span><span>Logged in as:</span><?php echo $userInfo['firstName'].' '.$userInfo['lastName']; ?></span>
      </div>

      <div class="sidebar-profile-buttons">
        <a href="user/<?php echo $userInfo['link'];?>">Profile</a>
        <a href="settings">Settings</a>
        <a href="" onclick="logOutUser()">Logout</a>
      </div>
    </div>
  </div>
</div>

<div id="join-class-window">
  <div id="join-class-modal">
    <div class="join-class-header">
      Join a Class
      <div id="join-class-close" onclick="toggleJoinClass()">&#10005;</div>
    </div>
    <div class="join-class-content">
      <label>Enter a class code to join a class.<br>If you don't know what this is please ask your teacher for a code.</label>
      <div id="join-class-input-group">
        <input type="text" id="join-class-code" maxlength="8" placeholder="Group Code">
        <div class="input-submit" onclick="submitClassCode()">&#10140;</div>
      </div>
    </div>
  </div>
</div>

<div id="successful-join-class">You successfully joined a class, click here to refresh.</div>

<style>
.search-group-colour-1 { background: #1abc9c; }
.search-group-colour-2 { background: #16a085; }
.search-group-colour-3 { background: #2ecc71; }
.search-group-colour-4 { background: #27ae60; }
.search-group-colour-5 { background: #f1c40f; }
.search-group-colour-6 { background: #f39c12; }
.search-group-colour-7 { background: #e67e22; }
.search-group-colour-8 { background: #d35400; }
.search-group-colour-9 { background: #3498db; }
.search-group-colour-10 { background: #2980b9; }
.search-group-colour-11 { background: #e74c3c; }
.search-group-colour-12 { background: #c0392b; }
.search-group-colour-13 { background: #9b59b6; }
.search-group-colour-14 { background: #8e44ad; }
.search-group-colour-15 { background: #34495e; }
.search-group-colour-16 { background: #2c3e50; }


#search-sidebar {
  position: fixed;
  height: 100vh;
  width: calc(100% - 265px);
  max-width: 550px;
  background: white;
  box-shadow: 2px 0px 3px 0 rgba(0,0,0,0.3);
  left: 265px;
  top: 0;
  z-index: 10;
  padding: 30px;
  box-sizing: border-box;
  transform: translateX(-105%);
  transition: all ease-in-out 0.3s;

  display: flex;
  flex-direction: column;
}

#search-sidebar.shown {
  transform: translateX(0);
  transition: all ease-in-out 0.3s;
}

#search-sidebar > span {
  position: absolute;
  right: 30px;
  top: 30px;
  font-size: 20px;
  color: #888;
  cursor: pointer;
}

#search-sidebar > span:hover {
  color: #000;
}

#search-sidebar div h4 {
  margin: 10px 0;
  font-size: 18px;
  line-height: 1.5;
}

#search-sidebar div input {
  position: relative;
  display: block;
  width: 100%;
  margin: 0;
  background: none;
  border: none;
  outline: none;
  color: #333;
  font-size: 13px;
  border-radius: 0;
  border-bottom: 2px solid #333;
  line-height: 2;
}

#search-sidebar div input:focus {
  border-bottom: 2px solid #43c5b8;
}

#search-sidebar #search-result {
  position: relative;
  display: block;
  width: 100%;
  height: 100%;
  padding-top: 20px;
  overflow: scroll;
}

#search-result img {
  position: relative;
  width: 100%;
  height: auto;
  margin: 0;
}

#search-result > h3 {
  margin: 0 0 10px 0;
  color: #555;
  font-size: 15px;
  text-decoration: underline;
}

#search-result > span {
  display: block;
  margin: 0;
  color: #555;
  font-size: 12px;
  text-align: center;
}

#search-sidebar #search-result > div,
#search-sidebar #search-result > a {
  position: relative;
  width: calc(100% - 6px);
  margin-left: 3px;
  text-decoration: none;
  color: inherit;
}

#search-result .search-single-user,
#search-result .search-single-file,
#search-result .search-single-group {
  margin-bottom: 30px;
  box-shadow: 1px 1px 3px 0 rgba(0,0,0,0.3);
  overflow: hidden;
}

#search-result .search-single-user .search-single-user-top,
#search-result .search-single-user .search-single-user-bottom {
  position: relative;
  width: 100%;
  box-sizing: border-box;
}

#search-result .search-single-user .search-single-user-top {
  padding: 0 0 0 100px;
  height: 140px;
  display: flex;
  align-items: center;
}

#search-result .search-single-user .search-single-user-top .user-top-information {
  color: #333;
}

#search-result .search-single-user .search-single-user-top .user-top-information h3 {
  font-size: 19px;
  line-height: 18px;
  margin: 0;
  padding: 0;
  color: #000;
}

#search-result .search-single-user .search-single-user-top .user-top-information h4 {
  font-size: 12px;
  font-style: italic;
  line-height: 15px;
}

#search-result .search-single-user .search-single-user-top .user-top-information h4 span {
  position: relative;
  font-weight: 400;
  font-style: normal;
  display: inline-block;
  margin-left: 20px;
  text-transform: capitalize;
}

#search-result .search-single-user .search-single-user-top .user-top-information h4 span::before {
  content: " ";
  display: block;
  position: absolute;
  left: -13px;
  top: 2px;
  height: 10px;
  width: 10px;
  border-radius: 50%;
}

#search-result .search-single-user .search-single-user-top .user-top-information h4 span.unavailable {
  color: #c0392b;
}

#search-result .search-single-user .search-single-user-top .user-top-information h4 span.available {
  color: #27ae60;
}

#search-result .search-single-user .search-single-user-top .user-top-information h4 span.unavailable::before {
  background: #c0392b;
}

#search-result .search-single-user .search-single-user-top .user-top-information h4 span.available::before {
  background: #27ae60;
}

#search-result .search-single-user .search-single-user-top .user-top-information > a {
  font-size: 12px;
  margin: 0;
  padding: 0;
  line-height: 1.4;
  text-decoration: none;
  color: inherit;
}

#search-result .search-single-user .search-single-user-top .user-top-information > a:hover {
  color: black;
  text-decoration: underline;
}

#search-result .search-single-user .search-single-user-top .user-top-image {
  position: absolute;
  top: 20px;
  left: -20px;
  display: inline-block;
  width: 100px;
  height: 100px;
  background: #efefef;
  border-radius: 50%;
  box-shadow: 1px 1px 3px 0 rgba(0,0,0,0.3);
}

#search-result .search-single-user .search-single-user-top .user-top-image img {
  position: relative;
  width: calc(100% - 6px);
  height: calc(100% - 6px);
  margin: 3px;
  border-radius: 50%;
}

#search-result .search-single-user .search-single-user-bottom {
  background: #eee;
  padding: 10px 20px 10px 10px;
  display: flex;
  justify-content: space-between;
  flex-direction: row;
  flex-wrap: wrap;
}

#search-result .search-single-user .search-single-user-bottom .user-bottom-groups .user-bottom-groups-flex > span {
  color: #666;
  text-transform: uppercase;
  font-size: 11px;
  font-weight: 400;
  margin: 0;
  padding: 0;
  line-height: 25px;
}

#search-result .search-single-user .search-single-user-bottom .user-bottom-groups .user-bottom-groups-flex {
  display: flex;
  justify-content: flex-start;
  flex-direction: row;
  flex-wrap: wrap;
  margin-top: 5px;
}

#search-result .search-single-user .search-single-user-bottom .user-bottom-groups .user-bottom-groups-flex a {
  position: relative;
  display: block;
  height: 25px;
  min-width: 30px;
  padding: 0 15px;
  border-radius: 15px;
  text-align: center;
  line-height: 25px;
  color: white;
  text-decoration: none;
  margin-left: 8px;
  text-transform: uppercase;
  font-size: 10px;
}

#search-result .search-single-user .search-single-user-bottom .user-bottom-groups .user-bottom-groups-flex a:hover {
  box-shadow: 1px 1px 3px 0 rgba(0,0,0,0.5);
}

#search-result .search-single-user .search-single-user-bottom .user-bottom-message {
  height: 25px;
}

#search-result .search-single-user .search-single-user-bottom .user-bottom-message svg {
  position: relative;
  height: 100%;
  padding: 0 0 0 5px;
}

#search-result .search-single-user .search-single-user-bottom .user-bottom-message svg:hover {
  fill: #444;
}

#search-result .search-users-flex {
  position: relative;
  width: calc(100% - 3px);
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  flex-direction: row;
  margin-bottom: 30px;
}

#search-result .search-users-flex .result-user,
#search-result .search-users-flex .result-user-disabled {
  box-sizing: border-box;
  display: inline-block;
  width: 31%;
  height: 190px;
  box-shadow: 1px 1px 3px 0 rgba(0,0,0,0.3);
  padding: 0 20px;
  text-align: center;
  transform: scale(1);
  transition: 0.1s all ease-in-out;
  overflow: hidden;
}

#search-result .search-users-flex .result-user-disabled {
  text-align: left;
  padding: 0;
  height: auto;
}

#search-result .search-users-flex .result-user:hover {
  transition: 0.3s all ease-in-out;
  transform: scale(0.97);
  box-shadow: 1px 1px 3px 0 rgba(0,0,0,0.5);
}

#search-result .search-users-flex .result-user h3,
#search-result .search-users-flex .result-user h4,
#search-result .search-users-flex .result-user span {
  position: relative;
  display: inline-block;
  width: 100%;
  margin: 0;
  padding: 0;
}

#search-result .search-users-flex .result-user h3 {
  font-size: 14px;
  color: #000;
  font-weight: bold;
  padding-top: 20px;
}

#search-result .search-users-flex .result-user h4 {
  color: #666;
  font-size: 12px;
  font-style: italic;
  line-height: 16px;
}

#search-result .search-users-flex .result-user span {
  width: auto;
  font-size: 12px;
  margin: 10px 0;
  line-height: 16px;
  text-transform: capitalize;
}

#search-result .search-users-flex .result-user span.unavailable {
  color: #c0392b;
}

#search-result .search-users-flex .result-user span.unavailable::before {
  background: #c0392b;
}

#search-result .search-users-flex .result-user span.available {
  color: #27ae60;
}

#search-result .search-users-flex .result-user span.available::before {
  background: #27ae60;
}

#search-result .search-users-flex .result-user span::before {
  display: block;
  position: absolute;
  content: " ";
  left: -10px;
  top: 4px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

#search-result .search-users-flex .result-user .user-result-image,
#search-result .search-users-flex .result-user-disabled .user-result-image {
  position: relative;
  display: block;
  width: 100%;
}

#search-result .search-users-flex .result-user .user-result-image img {
  box-sizing: border-box;
  position: relative;
  width: 100%;
  height: auto;
  border-radius: 50%;
  margin-bottom: -15%;
  border: 5px solid #ddd;
}

#search-result .search-users-flex .result-user-disabled span {
  display: block;
  font-size: 13px;
  color: #666;
  padding: 20px 20px 5px 20px;
}

#search-result .search-users-flex .result-user-disabled .user-result-image img {
  box-sizing: border-box;
  width: 50%;
  height: auto;
  border-radius: 50%;
  margin-bottom: -12%;
  margin-left: calc(40px + 32%);
  margin-top: -10%;
  border: 5px solid #ddd;
}

#search-result .search-single-file {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
}

#search-result .search-single-file img {
  height: 115px;
  width: 198px;
}

#search-result .search-single-file .file-information {
  height: 115px;
  width: calc(100% - 198px);
  padding: 15px;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  justify-content: space-around;
}

#search-result .search-single-file .file-information .file-title h4,
#search-result .search-single-file .file-information .file-title h5 {
  margin: 0;
  padding: 0;
}

#search-result .search-single-file .file-information .file-title h4 {
  font-size: 16px;
  line-height: 16px;
}

#search-result .search-single-file .file-information .file-title h5 {
  font-size: 13px;
  line-height: 13px;
  color: #555;
  font-style: italic;
  margin-top: 8px;
}

#search-result .search-single-file .file-information .file-reference {
  position: relative;
  height: 25px;
  line-height: 25px;
}

#search-result .search-single-file .file-information .file-reference img {
  position: relative;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  float: left;
  box-shadow: 2px 2px 3px 0 rgba(0,0,0,0.3);
}

#search-result .search-single-file .file-information .file-reference span:nth-child(2) {
  font-size: 10px;
  margin: 0 5px 0 8px;
}

#search-result .search-single-file .file-information .file-reference span:nth-child(3) {
  font-size: 12px;
  color: #333;
}

#search-result .search-files-flex {
  position: relative;
  width: calc(100% - 3px);
  display: flex;
  justify-content: flex-start;
  align-items: flex-end;
  flex-direction: row;
  margin-bottom: 30px;
}

#search-result .search-files-flex a {
  position: relative;
  display: block;
  width: calc((100% / 3) - 10px);
  margin: 0 5px;
  text-decoration: none;
}

#search-result .search-files-flex a img,
#search-result .search-files-flex a .file-img,
#search-result .search-files-flex a iframe {
  position: relative;
  display: block;
  width: 100%;
  height: 86.5px;
  background-size: cover;
  background-position: center;
  border: none;
  margin: none;
  padding: none;
  overflow: hidden;
}

#search-result .search-files-flex a .file-information {
  position: relative;
  width: 100%;
  height: auto;
  display: block;
  padding: 10px;
}

#search-result .search-files-flex a .file-information h4,
#search-result .search-files-flex a .file-information h5 {
  display: block;
  margin: 0;
  padding: 0;
  font-size: 14px;
  line-height: 1.2;
  color: black;
  text-align: center;
}

#search-result .search-files-flex a .file-information h5 {
  font-size: 13px;
  line-height: 13px;
  color: #555;
  font-style: italic;
  margin-top: 10px;
}

#search-result .search-files-flex a .file-information .file-reference {
  margin-top: 10px;
}

#search-result .search-files-flex a .file-information .file-reference span:nth-child(2) {
  margin: 0 2px 0 8px;
}

#search-result > a.search-single-group {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  margin-bottom: 30px;
}

#search-result > a.search-single-group > div {
  box-sizing: border-box;
  position: relative;
  width: 90%;
  padding: 20px 30px 0 30px;
}

#search-result > a.search-single-group .search-group-top {
  background: white;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: flex-end;
  overflow: hidden;
}

#search-result > a.search-single-group .search-group-top img {
  position: relative;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: -40px;
  box-shadow: 0 0 3px 2px rgba(0,0,0,0.3);
}

#search-result > a.search-single-group .search-group-top .search-group-top-left h4 {
  margin: 0;
  padding: 0;
  font-size: 16px;
}

#search-result > a.search-single-group .search-group-top .search-group-top-left h5 {
  margin-bottom: 20px;
  margin-top: 8px;
  font-size: 12px;
  color: #555;
}

#search-result > a.search-single-group .search-group-top .search-group-top-left h5 > span {
  color: #333;
}

#search-result > a.search-single-group .search-group-bottom {
  background: #efefef;
}

#search-result > a.search-single-group .search-group-bottom h5 {
  margin: 0;
  font-size: 12px;
  color: #555;
  margin-bottom: 6px;
}

#search-result > a.search-single-group .search-group-bottom .upcoming-assignment {
  font-size: 10px;
  color: #333;
  margin-bottom: 3px;
}

#search-result > a.search-single-group .search-group-bottom .upcoming-assignment span {
  color: #888;
  margin-left: 20px;
  font-style: normal;
}

#search-result > a.search-single-group .search-group-bottom .latest-post {
  margin-top: 20px;
}

#search-result > a.search-single-group .search-group-bottom .latest-post h6 {
  margin: 0;
  font-size: 12px;
  color: #555;
  margin-bottom: 5px;
}

#search-result > a.search-single-group .search-group-bottom .latest-post .post-message {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  margin-bottom: 10px;
}

#search-result > a.search-single-group .search-group-bottom .latest-post .post-message img {
  position: relative;
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

#search-result > a.search-single-group .search-group-bottom .latest-post .post-message p {
  display: block;
  position: relative;
  margin: 0 0 0 10px;
  width: calc(100% - 40px);
  font-size: 11px;
  line-height: 15px;
  color: #555;
  max-height: 30px;
  overflow: hidden;
}

#search-result > a.search-single-group .search-group-bottom .latest-post .post-message p span {
  display: block;
  color: #333;
}

#search-result > a.search-single-group .search-group-bottom .latest-post-info {
  margin-bottom: 15px;
}

#search-result .search-group-flex {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
}

#search-result .search-group-flex a,
#search-result .search-group-flex .disabled-group {
  position: relative;
  width: calc(100%/3 - 10px);
  text-decoration: none;
  color: black;
  box-shadow: 1px 1px 3px 0 rgba(0,0,0,0.3);
}

#search-result .search-group-flex .search-group-ind-flex {
  position: relative;
  box-sizing: border-box;
  padding: 0 12px;
  width: 100%;
  margin-top: 30px;
  background: white;
  overflow: hidden;
  text-align: center;
}

#search-result .search-group-flex .search-group-ind-flex > div > h4 {
  margin: 15px 0 10px 0;
  font-size: 14px;
  line-height: 1.2;
}

#search-result .search-group-flex .search-group-ind-flex > div > span {
  display: block;
  font-size: 12px;
  line-height: 1.2;
  color: #555;
}

#search-result .search-group-flex .search-group-ind-flex > img {
  position: relative;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: -40px;
  margin-top: 15px;
  box-shadow: 0 0 3px 2px rgba(0,0,0,0.3);
}

#search-result .search-group-flex > div {
  box-sizing: border-box;
  position: relative;
  padding: 15px;
  text-align: center;
}

#search-result .search-group-flex > div h4 {
  font-size: 14px;
  color: #333;
  margin: 10px 0 0 0;
  line-height: 1.2;
}

#search-result .search-group-flex > div p {
  color: #555;
  font-size: 12px;
  margin: 10px 0 20px 0;
  line-height: 1.3;
}

#search-result .search-group-flex > div button {
  margin: 0 auto;
}

#search-result .search-posts-flex {
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-direction: row;
  flex-wrap: wrap;
}

#search-result .search-posts-flex .result-post {
  box-sizing: border-box;
  position: relative;
  width: calc(50% - 8px);
  box-shadow: 1px 1px 3px 0 rgba(0,0,0,0.3);
  overflow: hidden;
  margin-bottom: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-direction: row;
  padding: 10px;
}

#search-result .search-posts-flex .result-post img {
  width: 30px;
  height: 30px;
  box-shadow: 0 0 3px 2px rgba(0,0,0,0.3);
  border-radius: 50%;
}

#search-result .search-posts-flex .result-post > div {
  position: relative;
  width: calc(100% - 45px);
}

#search-result .search-posts-flex .result-post > div h5 {
  color: #333;
  font-size: 12px;
  margin: 0 0 5px 0;
}

#search-result .search-posts-flex .result-post > div h5 span {
  color: #888;
  margin-left: 5px;
}

#search-result .search-posts-flex .result-post > div h6 {
  display: inline-block;
  margin: 0 0 7px 0;
  padding: 5px 7px;
  border-radius: 3px;
  font-size: 10px;
  color: white;
}

#search-result .search-posts-flex .result-post > div p {
  line-height: 15px;
  max-height: 30px;
  overflow: hidden;
  margin: 0;
  font-size: 11px;
  color: #555;
}

.sidebar {
  box-sizing: border-box;
  position: fixed;
  height: 100%;
  width: 265px;
  padding: 40px 0;
  left: 0;
  border-right: 1px solid rgba(0, 0, 0, 0.12);
  z-index: 20;
  background: white;
  box-shadow: 2px 0px 3px 0 rgba(0,0,0,0.1);

  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.sidebar h1 {
  position: relative;
  display: inline-block;
  padding: 0 0 0 30px;
  margin: 0 0 30px 0;
  font-size: 32px;
  line-height: 32px;
  color: #43c5b8;
}

.join-class-btn,
.sidebar #student-code-btn {
  position: relative;
  display: block;
  margin-left: 30px;
  margin-top: 25px;
  padding: 4px 10px;
  border: 1px solid #888;
  background: none;
  font-family: inherit;
  color: #888;
  line-height: 1.5;
  font-size: 11px;
  outline: none;
}

.sidebar #student-code-btn {
  margin-top: 10px;
}

.join-class-btn:hover,
.sidebar #student-code-btn:hover {
  color: #43c5b8;
  border: 1px solid #43c5b8;
  cursor: pointer;
}

.sidebar #search-icon {
  float: right;
  margin: 7px 30px 0 0;
  padding: 3px;
  cursor: pointer;
}

.sidebar .sidebar-navigation h3 {
  margin: 0;
  padding: 0 30px;
  font-size: 11px;
  text-transform: uppercase;
  color: #666;
  margin-bottom: 2px;
}

.sidebar .sidebar-navigation a {
  display: block;
  box-sizing: border-box;
  font-size: 15px;
  font-weight: 400;
  text-decoration: none;
  color: #C9C9C9;
  padding: 8px 25px;
  border-left: 5px solid transparent;
}

.sidebar .sidebar-navigation a:hover {
  color: #999;
}

.sidebar .sidebar-navigation a.selected {
  border-left: 5px solid #43c5b8;
  color: #7D7D7D;
  font-weight: 400;
}

.sidebar .sidebar-profile {
  padding: 0 30px;
}

.sidebar .sidebar-profile .sidebar-profile-user::after {
  content: " ";
  display: block;
  clear: both;
}

.sidebar .sidebar-profile .sidebar-profile-user img {
  float: left;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  box-shadow: 1px 1px 3px 1px rgba(0,0,0,0.4);
}

.sidebar .sidebar-profile .sidebar-profile-user > span {
  float: left;
  margin-left: 12px;
  position: relative;
  max-width: calc(100% - 47px);
}

.sidebar .sidebar-profile .sidebar-profile-user span span {
  display: block;
  color: #333;
  font-size: 12px;
}

.sidebar .sidebar-profile .sidebar-profile-buttons {
  padding-top: 3px;
  padding-left: 47px;
  padding-right: 10px;
  display: flex;
  justify-content: space-between;
}

.sidebar .sidebar-profile .sidebar-profile-buttons a {
  font-size: 13px;
  color: #aaa;
}

#successful-join-class.shown {
  display: block;
}

#successful-join-class {
  display: none;
  position: absolute;
  left: 290px;
  top: calc(100vh - 70px);
  height: 50px;
  background: #1abc9c;
  box-shadow: 0 0 6px 2px rgba(0,0,0,0.2);
  color: white;
  font-size: 14px;
  font-weight: 400;
  line-height: 50px;
  padding: 0 20px;
  cursor: pointer;
}

#join-class-window.shown {
  display: flex;
}

#join-class-window {
  display: none;
  position: fixed;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  z-index: 30;
  background: rgba(16,37,66,0.7);
  justify-content: center;
  align-items: center;
  user-select: none;
}

#join-class-modal {
  position: relative;
  box-sizing: border-box;
  width: 90%;
  max-width: 500px;
  height: auto;
  background: white;
  box-shadow: 3px 3px 10px 0px rgba(0,0,0,0.3);
}

#join-class-modal .join-class-header {
  display: block;
  background: #4d90fe;
  color: white;
  line-height: 0px;
  font-size: 16px;
  padding: 30px;
  margin-bottom: 20px;
}

#join-class-close {
  position: absolute;
  right: 30px;
  cursor: pointer;
}

#join-class-modal .join-class-content {
  position: relative;
  display: block;
  box-sizing: border-box;
  padding: 0 30px 30px 30px;
  text-align: center;
}

#join-class-modal .join-class-content label {
  margin: 0 auto;
  display: block;
  width: 80%;
  line-height: 1.5;
  margin-bottom: 20px;
  font-size: 15px;
  color: #555;
}

#join-class-modal .join-class-content #join-class-input-group {
  display: flex;
  justify-content: center;
}

#join-class-modal .join-class-content #join-class-input-group.shown div {
  display: inline-block;
}

#join-class-modal .join-class-content #join-class-input-group.shown input {
  width: 100px;
}

#join-class-modal .join-class-content #join-class-input-group input,
#join-class-modal .join-class-content #join-class-input-group div {
  display: inline-block;
  height: 30px;
  line-height: 1;
  padding: 0;
  margin: 0;
  outline: none;
  border: none;
  border-bottom: 2px solid black;
}

#join-class-modal .join-class-content #join-class-input-group input {
  width: 130px;
  font-size: 17px;
  padding: 0 5px;
  font-weight: 300;
}

#join-class-modal .join-class-content #join-class-input-group input::-webkit-input-placeholder {
  font-size: 13px;
  text-align: center;
}

#join-class-modal .join-class-content #join-class-input-group input::-webkit-input-placeholder {
  font-size: 13px;
  text-align: center;
}

#join-class-modal .join-class-content #join-class-input-group input::-moz-placeholder {
  font-size: 13px;
  text-align: center;
}

#join-class-modal .join-class-content #join-class-input-group.wrong input,
#join-class-modal .join-class-content #join-class-input-group.wrong div {
  border-bottom: 2px solid #c0392b;
}

#join-class-modal .join-class-content #join-class-input-group div {
  display: none;
  width: 10px;
  line-height: 30px;
  padding: 0 10px;
  font-size: 12px;
  cursor: pointer;
  transition: 0.15s ease-in-out all;
}

#join-class-modal .join-class-content #join-class-input-group div:hover {
  font-size: 15px;
  transition: 0.1s ease-in-out all;
}

</style>

<script>
  var logOutUser = function() {
    var request;
    if (window.XMLHttpRequest) {
      request = new XMLHttpRequest();
    } else {
      request = new ActiveXObject("Microsoft.XMLHTTP");
    }
    request.open('GET', 'php/logout.php', true);
    request.send();
    window.location.reload();
  }

  var selectNav = function(option) {
    document.getElementById('sidebar-nav-'+option).className = 'selected';
  }

  function updateLastOnline() {
    var request;
    if (window.XMLHttpRequest) {
      request = new XMLHttpRequest();
    } else {
      request = new ActiveXObject("Microsoft.XMLHTTP");
    }
    request.open('GET', 'php/lastOnline.php', true);
    request.send();

    setTimeout(updateLastOnline, 29000);
  }
  updateLastOnline();

  // get the search sidebar element
  var searchSidebar = document.getElementById('search-sidebar'),
      searchIcon = document.getElementById('search-icon');

  // toggle the visibility of the search sidebar
  var toggleSearch = function() {
    if(searchSidebar.className == 'shown') {
      searchSidebar.className = '';
      searchIcon.style.fill = '#959595';
    } else {
      searchSidebar.className = 'shown';
      searchIcon.style.fill = '#000';
    }
  }

  // make the search icon and close button listen for when its clicked and toggle the sidebar search
  searchIcon.onclick = toggleSearch;
  document.getElementById('search-sidebar-close').onclick = toggleSearch;

  // search timer variable and input field
  var searchTimer,
      searchInput = document.getElementById('search-sidebar-input'),
      searchOutput = document.getElementById('search-result');

  // send an AJAX request to retrieve search results
  var instantSearch = function() {
    var text = encodeURIComponent(searchInput.value);

    var request;
    if (window.XMLHttpRequest) {
      request = new XMLHttpRequest();
    } else {
      request = new ActiveXObject("Microsoft.XMLHTTP");
    }

    // on return of information from AJAX request
    request.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        searchOutput.innerHTML = this.responseText;
      }
    };

    // send the request to the php file: createaccount.php
    request.open('GET', 'php/search.php?q='+text+'&u=<?php echo $userInfo['link'];?>', true);
    request.send();
  };

  // on key up (after key has been pressed) in the search field fire the instant
  // search function after 300ms of no typing
  searchInput.onkeyup = function() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(instantSearch, 300);
  };


  // join class variables
  var joinClassWindow = document.getElementById("join-class-window");
  var joinClassCode = document.getElementById("join-class-code");
  var joinClassInputGroup = document.getElementById("join-class-input-group");

  // add the submit button when there is text in the field
  joinClassCode.onkeyup = function(e) {
    var text = this.value;
    joinClassInputGroup.className = '';
    if(text) {
      if(e.keyCode == 13) {
        submitClassCode();
      }

      joinClassInputGroup.className = 'shown';
    } else {
      joinClassInputGroup.className = '';
    }
  };

  // toggle visibility of the join class window
  var toggleJoinClass = function() {
    if(joinClassWindow.className == 'shown') {
      joinClassWindow.className = '';
    } else {
      joinClassWindow.className = 'shown';
    }
  };

  var showSuccessMessage = function() {
    document.getElementById("successful-join-class").className = "shown";
  };

  document.getElementById("successful-join-class").onclick = function() {
    location.reload();
  };

  var submitClassCode = function() {
    var classCode = encodeURIComponent(joinClassCode.value);

    var request;
    if (window.XMLHttpRequest) {
      request = new XMLHttpRequest();
    } else {
      request = new ActiveXObject("Microsoft.XMLHTTP");
    }

    // on return of information from AJAX request
    request.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var response = this.responseText;

        if(response == 0) {
          // joined group successfully

          toggleJoinClass();
          showSuccessMessage();

        } else if(response == 2) {
          // no group with that access code

          joinClassInputGroup.className = 'wrong';

        } else {
          // no user with that userHash (user must have changed the javascript code)
          location.reload();
        }
      }
    };

    // send the request to the php file: createaccount.php
    request.open('GET', 'php/joinGroup.php?c='+classCode+'&u=<?php echo $userInfo['link'];?>', true);
    request.send();
  };

  // join class variables
  var joinClassWindow = document.getElementById("join-class-window");
  var joinClassCode = document.getElementById("join-class-code");
  var joinClassInputGroup = document.getElementById("join-class-input-group");

  // add the submit button when there is text in the field
  joinClassCode.onkeyup = function(e) {
    var text = this.value;
    joinClassInputGroup.className = '';
    if(text) {
      if(e.keyCode == 13) {
        submitClassCode();
      }

      joinClassInputGroup.className = 'shown';
    } else {
      joinClassInputGroup.className = '';
    }
  };

  // toggle visibility of the join class window
  var toggleSCode = function() {
    alert('todo');
  };

</script>
