
// function to activate a window given a number (num)
var activateWindow = function(num) {
  // remove the class of active from all other windows
  document.getElementById('createaccount-1').className = 'window';
  document.getElementById('createaccount-2').className = 'window';
  document.getElementById('createaccount-3').className = 'window';
  document.getElementById('createaccount-4').className = 'window';
  document.getElementById('createaccount-5').className = 'window';

  // add the active class to the required window
  document.getElementById('createaccount-'+num).className = 'window active';
};

// Returns whether an email address is valid or not
// Credit: http://stackoverflow.com/questions/46155/validate-email-address-in-javascript
var isValidEmailAddress = function(email) {
  var re = /[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+(?:[A-Z]{2}|com|org|net|gov|mil|biz|info|mobi|name|aero|jobs|museum)\b/;
  return re.test(email);
};

// Returns whether the email is valid and displays this to users
var checkEmail = function(input) {
  // check that the email is a valid email address
  if(!isValidEmailAddress(input)) {
    // otherwise: add the invalid class to both email inputs
    document.getElementById('create-account-s-email').className = "incorrect";
    document.getElementById('create-account-t-email').className = "incorrect";

    // return false (meaning the email failed)
    return false;
  }

  // set up an AJAX request
  var request;
  if (window.XMLHttpRequest) {
    request = new XMLHttpRequest();
  } else {
    request = new ActiveXObject("Microsoft.XMLHTTP");
  }

  // create a reponse variable to determine whether the email is not in database
  var response;

  // on return of information from AJAX request
  request.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {

      // if the username is not found in the database (email is free)
      if(this.responseText == 1) {

        // add the valid class to the email inputs
        document.getElementById('create-account-s-email').className = "valid";
        document.getElementById('create-account-t-email').className = "valid";

        // set reponse to true (meaning the email is valid) and return
        response = true;
        return;

      // else the email has been found in the database
      } else {

        // add the invalid class to the email inputs
        document.getElementById('create-account-s-email').className = "incorrect";
        document.getElementById('create-account-t-email').className = "incorrect";

        // set response to false (meaning the email is not valid) and return
        response = false;
        return;
      }
    }
  };

  // send the request to the php file: testEmail.php with the inputted email
  request.open('GET', 'php/testEmail.php?e=' + input, false);
  request.send();

  // return the response from the database
  return response;
};

// Returns whether the password is valid and displays this to users
var checkPassword = function(input) {
  var max=18, min=5;

  // check that the password is the correct length
  if(input.length < min || input.length > max) {

    // add the invalid class to the password inputs
    document.getElementById('create-account-t-password').className = "incorrect";
    document.getElementById('create-account-s-password').className = "incorrect";

    // return false (invalid password)
    return false;
  }

  // add the valid class to the password inputs
  document.getElementById('create-account-t-password').className = "valid";
  document.getElementById('create-account-s-password').className = "valid";

  // return true (valid password)
  return true;
}

// Returns whether a prefix was selected and displays this to user
var checkPrefix = function(input) {

  // determines whether the default value is still the selected option
  if(input === 'Prefix') {

    // applies the incorrect class to the select element and the arrow
    document.getElementById('create-account-t-prefix').className = "incorrect";
    document.getElementById('create-account-t-prefix-arrow').className = "select-arrow incorrect";

    // returns false (meaning invalid prefix)
    return false;
  }

  // applies the valid class to the select element and the arrow
  document.getElementById('create-account-t-prefix').className = "valid";
  document.getElementById('create-account-t-prefix-arrow').className = "valid select-arrow";

  // returns true (meaning valid prefix)
  return true;
}

// Returns whether a name is valid
var checkName = function(a, input) {
  var re = /^[a-z ,.'-]+$/i,
      valid = re.test(input);

  if(valid) {
    // applies the valid class to the corresponding name input
    document.getElementById('create-account-'+a).className = "valid";

    // returns true (meaning valid name)
    return true;
  } else {
    // applies the incorrect class to the corresponding name input
    document.getElementById('create-account-'+a).className = "incorrect";

    // returns false (meaning invalid name)
    return false;
  }
}

// validate then create an account with the inputted information
var createAccount = function(type) {
  var email = document.getElementById('create-account-'+type+'-email').value,
      pwd = document.getElementById('create-account-'+type+'-password').value,
      first = document.getElementById('create-account-'+type+'-first').value,
      last = document.getElementById('create-account-'+type+'-last').value,
      prefix = '';

  // set a variable to determine whether any errors have been found
  var errorFound = false;

  // check whether the email is valid
  if (!checkEmail(email)) {  errorFound = true;  }

  // check whether the password is valid
  if (!checkPassword(pwd)) { errorFound = true;  }

  // if the user is registering a teacher account
  if(type==='t') {
    // get the prefix value
    prefix = document.getElementById('create-account-t-prefix').value;

    // check whether a valid prefix was selected
    if (!checkPrefix(prefix)) { errorFound = true;  }
  }

  // check whether the first name is valid
  if (!checkName(type+'-first',first)) { errorFound = true;  }

  // check whether the last name is valid
  if (!checkName(type+'-last',last)) { errorFound = true;  }

  // if there have been any errors
  if(errorFound) {

    // return false for the createAccount function
    return false;

  // else there are no errors in the inputted data
  } else {

    // set up an AJAX request
    var request;
    if (window.XMLHttpRequest) {
      request = new XMLHttpRequest();
    } else {
      request = new ActiveXObject("Microsoft.XMLHTTP");
    }

    // on return of information from AJAX request
    request.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        // if the response is 1 (meaning account was created)
        if(this.responseText == 1) {
          // if the user signed up for a teacher account
          if(type==='t') {
            // send the user to window 5
            activateWindow(5);
          // else: the user is a student
          } else {
            // send the user to window 4
            activateWindow(4);
          }
        // else: the account was not created due to an unknown error
        } else {

          // TODO: add an error message

        }
      }
    };

    // prepare the url to be sent with all the user's inputted information
    var url = 'php/makeAccount.php?e=' + email + '&p=' + pwd + '&f=' + first + '&l=' + last + '&t=' + type + '&pr=' + prefix;

    // send the request to the php file: createaccount.php
    request.open('GET', encodeURI(url), true);
    request.send();
  }
}
