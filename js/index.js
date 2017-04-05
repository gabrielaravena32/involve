document.getElementById('navigation').className = '';

document.onscroll = function() {
  // variables
  var scrollX = document.body.scrollTop;
  var winHeight = window.innerHeight;
  var navBackground = false;

  // navigation background colour
  var navElem = document.getElementById('navigation');
  var withinRange = scrollX >= (winHeight - 75);
  if (withinRange && navBackground === false) {
    navBackground = true;
    navElem.className = 'bg';
  } else if(withinRange){} else {
    navBackground = false;
    navElem.className = '';
  }

  // hero parallax
  var heroModalElem = document.getElementById('hero-modal');
  heroModalElem.style.transform = "translate(0px,"+scrollX*2/3+"px)";

  // teacher image parallax
  var teacherOffset = document.getElementById('info-teacher').offsetTop;
  var teacherImgElem = document.getElementById('teacher-img');
  if (scrollX < (teacherOffset-68)) {
    teacherImgElem.style.transform = "translate("+(scrollX-teacherOffset+68)/4+"px,0px)";
  } else {
    teacherImgElem.style.transform = "translate(0px,0px)";
  }

  // student image parallax
  var studentOffset = document.getElementById('info-student').offsetTop;
  var studentImgElem = document.getElementById('student-img');
  if (scrollX < (studentOffset-68)) {
    studentImgElem.style.transform = "translate("+(scrollX-studentOffset+68)/-4+"px,0px)";
  } else {
    studentImgElem.style.transform = "translate(0px,0px)";
  }

  // download parallax
  var documentOffset = document.getElementById('download').offsetTop;
  var downloadImgElem = document.getElementById('download-image');
  downloadImgElem.style.transform = "translate("+(scrollX-documentOffset+68)/4+"px,0px)";
};


// Source: http://stackoverflow.com/questions/4770025/how-to-disable-scrolling-temporarily
// Credit to: galambalazs (22/1/11)
var keys = {37: 1, 38: 1, 39: 1, 40: 1, 32: 1};
function preventDefault(e) {
  e = e || window.event;
  if (e.preventDefault) {
    e.preventDefault();
  }
  e.returnValue = false;
}
function preventDefaultForScrollKeys(e) {
    if (keys[e.keyCode]) {
        preventDefault(e);
        return false;
    }
}
function disableScroll() {
  if (window.addEventListener) { // older FF
    window.addEventListener('DOMMouseScroll', preventDefault, false);
  }
  window.onwheel = preventDefault; // modern standard
  window.onmousewheel = document.onmousewheel = preventDefault; // older browsers, IE
  window.ontouchmove = preventDefault; // mobile
  document.onkeydown = preventDefaultForScrollKeys;
}
function enableScroll() {
    if (window.removeEventListener) {
      window.removeEventListener('DOMMouseScroll', preventDefault, false);
    }
    window.onmousewheel = document.onmousewheel = null;
    window.onwheel = null;
    window.ontouchmove = null;
    document.onkeydown = null;
}



// sign in link
document.getElementById('nav-signin').onclick = function(e) {
  e.preventDefault();
  signinClick();
};

var signinClick = function() {
  // show modal
  document.getElementById('sign-in-modal').className = 'active';
  document.getElementById('blur-zone').className = 'active';
  disableScroll();

  // on escape key click close modal
  document.onkeyup = function(e) {
    if(e.which === 27) {
      closeSignInWindow();
    }
  };

  // on clicking outside modal close modal
  var a = false;
  document.getElementById('blur-zone').onclick = function() {
    if (a === true) {
      closeSignInWindow();
    }
    a = true;
  };

  // on clicking x close modal
  document.getElementById('sign-in-close').onclick = function(e) {
    e.preventDefault();
    closeSignInWindow();
  };
};

var closeSignInWindow = function() {
  document.getElementById('sign-in-modal').className = '';
  document.getElementById('blur-zone').className = '';
  document.onkeyup = null;
  document.getElementById('blur-zone').onclick = null;
  document.getElementById('sign-in-close').onclick = null;
  enableScroll();
};

var passwordWrong = function() {
  document.getElementById('signin-pwd').className = 'incorrect';
  signinClick();
};

var emailWrong = function() {
  document.getElementById('signin-email').className = 'incorrect';
  signinClick();
};
