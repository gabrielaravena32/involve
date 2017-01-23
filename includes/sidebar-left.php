<div class="sidebar">
  <div class="flex-wrapper">
    <h1>Involve</h1>

    <svg id="search-icon" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="18" height="18" viewBox="0 0 300 300">
      <g><path fill="#959595" stroke-width="9.756173" d="m35.938999,35.085999c-43.272999,43.272003 -43.379,114.158997 -0.105988,157.432007c36.662991,36.663025 93.201992,42.25 135.920986,16.765991l76.343994,74.234985c10.506989,10.197998 27.084991,9.821045 37.116974,-0.843994c10.03302,-10.664978 9.77002,-27.445984 -0.737976,-37.643982l-75.182983,-72.864014c26.360992,-42.846985 21.007996,-100.044983 -16.028015,-137.080994c-43.27298,-43.273008 -114.054993,-43.273008 -157.327,0l0,0l0.000008,0zm31.738979,31.739014c26.10202,-26.102024 67.746033,-26.102024 93.848022,0c26.10199,26.10199 26.10199,67.745972 0,93.847961c-26.10199,26.102051 -67.746002,26.102051 -93.848022,0c-26.10199,-26.10199 -26.10199,-67.745972 0,-93.847961z"/></g>
    </svg>

    <div class="sidebar-navigation">
      <a href="home" id="sidebar-nav-feed">Feed</a>
      <a href="" id="sidebar-nav-today">Today View</a>
      <br><br>
      <h3>Explore</h3>
      <a href="messages" id="sidebar-nav-messages">Messages</a>
      <a href="" id="sidebar-nav-classes">Classes</a>
      <a href="" id="sidebar-nav-assessments">Assessments</a>
      <a href="" id="sidebar-nav-timetable">Timetable</a>
      <a href="" id="sidebar-nav-backpack">Backpack</a>
    </div>
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

<style>
.sidebar {
  box-sizing: border-box;
  position: fixed;
  height: 100%;
  width: 265px;
  padding: 40px 0;
  left: 0;
  border-right: 1px solid rgba(0, 0, 0, 0.12);

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

</script>
