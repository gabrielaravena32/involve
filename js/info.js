
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
}
