<?php

// set the page selection to feed
$navPageSel = 'feed';

// include the connect script
include_once "php/connect.php";

// the path for a redirect to home
$path = '.';
// include the redirect script
include_once "php/redirect.php";

// the number of posts to load initially (there is a load more button)
$numPostsToLoad = 20;

// the number of posts available to the user
$numPostsQuery = $conn->query("SELECT COUNT(p.postID) AS num
                                FROM posts AS p
                                WHERE
                                  p.`groupID` IN (SELECT groupID FROM userGroups WHERE userID={$userInfo['userID']})
                                  OR
                                  p.`groupID` IN (SELECT groupID FROM groups WHERE teacherID={$userInfo['userID']});");
$numPosts = $numPostsQuery->fetch_assoc()['num'];

?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Involve | Home</title>

    <link rel="stylesheet" href="css/home.css">
  </head>
  <body>

    <?php include_once "includes/sidebar-left.php"; ?>

    <!-- Page content -->
    <div class="content" id="content"><p>Loading...</p></div>

    <!-- sidebar on right -->
    <?php include_once "includes/sidebar-right.php"; ?>

    <!-- User Action -->
    <?php include_once "includes/user-action.php"; ?>

    <script>
      // Create a dynamic textbox
      // I modified the code a bit to work for my case but this should be good
      // Credit: http://stackoverflow.com/questions/454202/creating-a-textarea-with-auto-resize
      var observe;
      if (window.attachEvent) {
          observe = function (element, event, handler) {
              element.attachEvent('on'+event, handler);
          };
      }
      else {
          observe = function (element, event, handler) {
              element.addEventListener(event, handler, false);
          };
      }

      function init (elem) {
        function resize (e) {
          elem.className = '';
          elem.style.height = 'auto';
          if(elem.scrollHeight === 0) {
            elem.style.height = '28px';
          } else if(elem.scrollHeight > window.innerHeight/2) {
            elem.style.height = (window.innerHeight/2)+'px';
          } else {
            elem.style.height = elem.scrollHeight+'px';
          }
        }
        function delayedResize (e) {
          window.setTimeout(function() {
            resize(e);
          }, 0);
        }
        observe(elem, 'change',  function() { resize(elem); });
        observe(elem, 'cut',     function() { delayedResize(elem); });
        observe(elem, 'paste',   function() { delayedResize(elem); });
        observe(elem, 'drop',    function() { delayedResize(elem); });
        observe(elem, 'keydown', function() { delayedResize(elem); });
        resize(elem);
      }

      // make the textarea dynamic
      var text = document.getElementById('create-post-input-text');
      init(text);

      // create an array of all the textareas already initialised
      var alreadyInit = [];

      // function called every time a comment input is clicked
      var setInit = function(code) {
        // if the comment input has NOT already been initialised
        if(alreadyInit.indexOf(code) === -1) {

          // initialise it
          init(document.getElementById('comment-text-'+code));

          // add it to the array
          alreadyInit.push(code);

          // change the display of the element
          document.getElementById('comment-input-'+code).className = 'comment-input in-use';
        } else {
          // change the display of the element
          document.getElementById('comment-input-'+code).className = 'comment-input in-use';
        }
      };

      // clear the comment post button
      var clearAppearance = function(code) {
        // if there is no text in the comment
        if (document.getElementById('comment-text-'+code).value == '') {
          // remove styling
          document.getElementById('comment-input-'+code).className = 'comment-input';
        }
      };

      // show comments
      var showComments = function(code) {
        document.getElementById('post-comment-show-'+code).className = 'hidden';
        document.getElementById('post-comment-section-'+code).className = 'post-comment-section shown';
      };

      // hide comments
      var hideComments = function(code) {
        document.getElementById('post-comment-show-'+code).className = '';
        document.getElementById('post-comment-section-'+code).className = 'post-comment-section';
      };


      // make a comment
      var commentOnPost = function(code) {
        // get the comment text
        var text = document.getElementById('comment-text-'+code).value;

        // set up a request
        var request;
        if (window.XMLHttpRequest) {
          request = new XMLHttpRequest();
        } else {
          request = new ActiveXObject("Microsoft.XMLHTTP");
        }

        // on return of information from AJAX request
        request.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            // the comment was successful
            if(this.responseText != 0) {
              // get the element that the comment will move into
              var commentSection = document.getElementById('post-comments-html-'+code);
              // add the comment into the element
              commentSection.innerHTML = commentSection.innerHTML + '<div class="comment"><img src="<?php echo $userInfo['photo']; ?>" /><div><span><a href="user"><?php echo $userInfo['firstName'].' '.$userInfo['lastName']; ?></a> Right Now</span><p>'+this.responseText+'</p></div></div>';

              // clear the comment input
              document.getElementById('comment-text-'+code).value = '';
              // show the comment section (if not already shown)
              showComments(code);
              // remove the comment input styling
              clearAppearance(code);
            }
          }
        };

        // send the request to the php file: testEmail.php with the inputted email
        request.open('GET', 'php/addComment.php?p='+code+'&u=<?php echo $userInfo['link']; ?>&t='+ encodeURIComponent(text), true);
        request.send();
      };

      // Remove an HTML element
      // Credit: http://stackoverflow.com/questions/3387427/remove-element-by-id
      Element.prototype.remove = function() {
        this.parentElement.removeChild(this);
      }
      NodeList.prototype.remove = HTMLCollection.prototype.remove = function() {
        for(var i = this.length - 1; i >= 0; i--) {
          if(this[i] && this[i].parentElement) {
            this[i].parentElement.removeChild(this[i]);
          }
        }
      }

      // variables for the posts loading
      var numberOfPostsAvailableToUser = <?php echo $numPosts; ?>,
          numCurrentPosts = 0,
          contentElem = document.getElementById('content');

      var loadPosts = function() {
        if (numCurrentPosts < numberOfPostsAvailableToUser) {
          var request;

          // set the correct request type (AJAX)
          if (window.XMLHttpRequest) {
            request = new XMLHttpRequest();
          } else {
            request = new ActiveXObject("Microsoft.XMLHTTP");
          }

          // on return of information from AJAX request
          request.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {

              // if there is a load more posts button remove it
              if(document.getElementById('load-more-posts')) {
                document.getElementById('load-more-posts').remove();
              }

              if(contentElem.innerHTML == '<p>Loading...</p>') {
                contentElem.innerHTML = '';
              }

              // add the HTML from the request to the element on this page
              contentElem.innerHTML = contentElem.innerHTML + this.responseText;

              // add to the number of current posts the number of most number of posts added
              numCurrentPosts = numCurrentPosts + <?php echo $numPostsToLoad; ?>;

              // if there are more posts not shown add a 'load more posts' button
              if (numCurrentPosts < numberOfPostsAvailableToUser) {
                contentElem.innerHTML = contentElem.innerHTML + '<a id="load-more-posts" onclick="loadPosts()">Load More Posts</a>';
              } else {
                contentElem.innerHTML = contentElem.innerHTML + '<p>All done, no more posts to load.</p>';
              }
            }
          };

          // create the default URL
          var url = 'php/requestLoadPosts.php?uh=<?php echo $userInfo['link']; ?>&ntl=<?php echo $numPostsToLoad; ?>&np=' + numCurrentPosts;

          // if there are no posts on the screen add the f=1 identifier to show it is the first post
          if(numCurrentPosts === 0) {
            url = 'php/requestLoadPosts.php?uh=<?php echo $userInfo['link']; ?>&ntl=<?php echo $numPostsToLoad; ?>&f=1';
          }

          // send the request to the php file: testEmail.php with the inputted email
          request.open('GET', url, true);
          request.send();

        // else: there are no posts to load
        } else {
          // if there is a button (for some reason)
          if (document.getElementById('load-more-posts')) {
            // remove the button
            document.getElementById('load-more-posts').remove();
          }

          contentElem.innerHTML = '<img class="no-posts-img" src="images/tutorial/no-posts.png"><p><span>You have no posts to view yet.</span>Try joining a class or creating a post for your class.<br><br><a onclick="toggleJoinClass()">+ Join A Class</a></p>';
        }
      }

      loadPosts();
    </script>
  </body>
</html>
