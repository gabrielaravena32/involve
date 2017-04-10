<!-- Screen number 1 -->
<div class="tutorial-screen">
  <div>
    <h1>Welcome to Involve!</h1>
    <p>Now that you have created an account let us show you around.<br>Don't worry it wont take long, just click through the slides.</p>
    <button onclick="tutorialScreenGoto(2)">Next &raquo;</button>
  </div>
</div>

<!-- Screen number 2 -->
<div class="tutorial-screen">
  <div class="highlighter hl-1"></div>
  <div class="backgrounder bg-1"></div>
  <div class="backgrounder bg-2"></div>
  <div class="backgrounder bg-3"></div>

  <div class="text-1">
    <img src="images/tutorial/arrow1.png">
    <p>The sidebar is full of handy links for you to navigate your way around the site. If in doubt click <i>Feed</i> and see the most recent posts.<br><br><button onclick="tutorialScreenGoto(3)">Next &raquo;</button></p>
  </div>
</div>

<!-- Screen number 3 -->
<div class="tutorial-screen">
  <div class="highlighter hl-2"></div>
  <div class="highlighter hl-3"></div>
  <div class="highlighter hl-4"></div>
  <div class="highlighter hl-5"></div>
  <div class="backgrounder bg-4"></div>
  <div class="backgrounder bg-5"></div>
  <div class="backgrounder bg-6"></div>
  <div class="backgrounder bg-7"></div>

  <div class="text-2">
    <img src="images/tutorial/arrow2.png">
    <p>Need to find something quick? Just try searching for it using the search icon in the nav bar.<br><br><button onclick="tutorialScreenGoto(4)">Next &raquo;</button></p>
  </div>

</div>

<!-- Screen number 4 -->
<div class="tutorial-screen">
  <div class="highlighter hl-6"></div>
  <div class="backgrounder bg-8"></div>
  <div class="backgrounder bg-9"></div>

  <div class="text-3">
    <p>Use this icon to share! You can send messages and make posts/assignments.<br><br><button onclick="tutorialScreenGoto(5)">Next &raquo;</button></p>
    <img src="images/tutorial/arrow3.png">
  </div>
</div>

<!-- Screen number 5 -->
<div class="tutorial-screen">
  <div class="highlighter hl-7"></div>
  <div class="backgrounder bg-10"></div>
  <div class="backgrounder bg-11"></div>

  <div class="text-4">
    <p>For anything to do with your profile look here. You can update it, view it and log out.<br><br><button onclick="toggleTutorialShown()">End &raquo;</button></p>
    <img src="images/tutorial/arrow4.png">
  </div>
</div>

<div id="tutorial-bar">
  <div>
    <span onclick="tutorialScreenGoto(1)"></span>
    <span onclick="tutorialScreenGoto(2)"></span>
    <span onclick="tutorialScreenGoto(3)"></span>
    <span onclick="tutorialScreenGoto(4)"></span>
    <span onclick="tutorialScreenGoto(5)"></span>
  </div>
  <p onclick="toggleTutorialShown()">Close tutorial.</p>
</div>
