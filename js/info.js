

// initialise the scroll variables that dont change often
var scrollNav = document.getElementById('scrollNav'),
    scrollNavClass = 1,
    winHeight = window.innerHeight,
    markerOne = 0,
    markerTwo = document.getElementById('package').offsetTop - (winHeight/5),
    markerThree = document.getElementById('features').offsetTop - (winHeight/5),
    markerFour = document.getElementById('topics').offsetTop - (winHeight/5);

// when the window is resized update the scroll variables
document.onresize = function() {
  winHeight = window.innerHeight;
  markerTwo = document.getElementById('package').offsetTop - (winHeight/5);
  markerThree = document.getElementById('features').offsetTop - (winHeight/5);
  markerFour = document.getElementById('topics').offsetTop - (winHeight/5);
};

// when the document is scrolled refresh the navigation icons on the right
document.onscroll = function() {
  var scrollX = document.body.scrollTop;

  if(scrollX >= markerOne && scrollX < markerTwo) {
    if(scrollNavClass != 1) {
      scrollNav.className = 'active-1';
      scrollNavClass = 1;
    }
  } else if(scrollX < markerThree) {
    if(scrollNavClass != 2) {
      scrollNav.className = 'active-2';
      scrollNavClass = 2;
    }
  } else if(scrollX < markerFour){
    if(scrollNavClass != 3) {
      scrollNav.className = 'active-3';
      scrollNavClass = 3;
    }
  } else{
    if(scrollNavClass != 4) {
      scrollNav.className = 'active-4';
      scrollNavClass = 4;
    }
  }
}

// add jiggle effect to features
