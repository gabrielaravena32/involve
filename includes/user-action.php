<div id="user-action-dock">
  <div class="flex-container">
    <!-- Default + icon -->
    <a>+</a>

    <!-- Create post -->
    <a onclick="createPostWindow()">
      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="20" height="20" viewBox="0 0 512 512" enable-background="new 0 0 512 512"fill="white">
        <path d="M493.278,154.515l-22.625,22.641L334.871,41.39l22.625-22.641c25-25,65.531-25,90.531,0l45.25,45.266  C518.246,89,518.246,129.515,493.278,154.515z M176.465,426.031c-6.25,6.25-6.25,16.375,0,22.625c6.25,6.281,16.375,6.281,22.625,0  l248.938-248.875l-22.656-22.641L176.465,426.031z M63.309,312.906c-6.25,6.25-6.25,16.375,0,22.625s16.375,6.25,22.625,0  L334.871,86.64l-22.625-22.625L63.309,312.906z M357.465,109.25L108.559,358.156c-12.5,12.469-12.469,32.75,0,45.25  c12.5,12.5,32.75,12.563,45.281-0.031l248.906-248.859L357.465,109.25z M153.778,471.219c-7.656-7.656-11.344-17.375-12.719-27.375  c-3.25,0.5-6.531,0.969-9.875,0.969c-17.094,0-33.156-6.688-45.25-18.781c-12.094-12.125-18.75-28.156-18.75-45.25  c0-3.125,0.469-6.156,0.906-9.188c-10.344-1.406-19.906-5.938-27.406-13.438c-0.719-0.719-0.969-1.688-1.625-2.469L-0.004,512  l155.906-39.031C155.215,472.344,154.434,471.875,153.778,471.219z"/>
      </svg>
    </a>

    <!-- Send message -->
    <a href="messages/new">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 26 26" fill="white">
        <path d="M 13 0.1875 C 5.924 0.1875 0.1875 5.252 0.1875 11.5 C 0.1875 14.676732 1.67466 17.538895 4.0625 19.59375 C 3.5416445 22.603047 0.17600428 23.827728 0.40625 24.65625 C 3.4151463 25.900544 9.377016 23.010935 10.28125 22.5625 C 11.155019 22.728689 12.06995 22.8125 13 22.8125 C 20.076 22.8125 25.8125 17.748 25.8125 11.5 C 25.8125 5.252 20.076 0.1875 13 0.1875 z"></path>
      </svg>
    </a>
  </div>
</div>

<div id="create-post">
  <div class="create-post-modal">
    <div id="create-post-close" onclick="closePost()">
      <div></div>
      <div></div>
    </div>

    <div class="create-post-title">Create Post</div>
    <div class="create-post-form">

      <?php
      // add classes to the select input
      $noClass = false;
      if(!$groupLink) {
        $output = '<label>Send to</label>
              <select id="create-post-input-group">
                <option selected disabled>Select a Group</option>';

        $classQuery = $conn->query("SELECT groupLink, groupName
                                    FROM groups
                                    WHERE
                                      groupID IN (SELECT groupID FROM userGroups WHERE userID = {$userInfo['userID']})
                                    OR
                                      teacherID={$userInfo['userID']};");
        if($classQuery->num_rows > 0) {
          while($class = $classQuery->fetch_assoc()) {
            $output .= '<option value="'.$class['groupLink'].'">'.$class['groupName'].'</option>';
          }
          $output .= '</select>';
        } else {
          $output = '<p class="important">You do not have any classes to send a post to. Please join a class.</p>';
          $noClass = true;
        }
        echo $output;
      } else {
        echo '<p>Message will be sent to '.$groupInfo['groupName'].'.</p>';
      }

      // add an assignment toggle for teachers
      if($userInfo['type'] == 't') {
        $tmr = new DateTime('tomorrow', new Datetimezone('Australia/Sydney'));
        $date = $tmr->format("Y-m-d");
        echo '<label>Assignment</label>
              <label id="toggle" for="create-post-input-ass" onclick="switchAssignment()"><div></div></label>
              <input type="checkbox" id="create-post-input-ass" />

              <div id="input-name">
                <label>Assignment Name</label>
                <input type="text" placeholder="Title the post" id="create-post-input-name" />
              </div>

              <div id="input-due">
                <label>Due Date</label>
                <input type="date" value="'.$date.'" min="'.date('Y-m-d').'" id="create-post-input-due" />
              </div>';
      }

      ?>
      <label>Message</label>
      <textarea id="create-post-input-text" placeholder="Share with your class" rows="1"></textarea>

      <div id="createPostAttachFiles"></div>

      <div class="create-post-form-bottom">
        <div class="create-post-icons">
          <a onclick="attachFile()">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1000 1000" height="100%" fill="#666">
              <g>
                <g transform="translate(0.000000,88.000000) scale(0.100000,-0.100000)">
                  <path d="M3594.9,658.2C3179,467,2808.1,73.6,2650.7-342.2c-78.7-202.4-123.7-1540-123.7-3563.3v-3237.2l314.7-595.8C3291.4-8604.1,3988.3-9020,5000-9020s1708.6,415.9,2158.2,1281.4l314.7,595.8v2855.1v2855.1h-337.2h-326L6776-4343.9c-33.7-2742.6-44.9-2933.8-269.7-3226.1c-438.4-595.7-798.1-775.5-1506.3-775.5c-708.1,0-1067.9,179.8-1506.2,775.5c-224.8,292.3-236.1,483.4-269.8,3529.6c-22.5,2281.9,11.2,3304.8,101.2,3529.6c146.1,348.4,618.2,651.9,1000.4,651.9c382.2,0,854.2-303.5,1000.5-640.7c89.9-213.6,123.6-1124.1,101.1-2888.8c-33.7-2304.3-56.2-2585.4-224.8-2697.8c-146.1-89.9-258.5-89.9-393.4,0c-179.8,112.4-202.3,370.9-236,2394.2l-22.5,2259.4h-337.2h-337.2v-2248.1c0-2461.7,56.2-2765.2,618.2-3012.4c382.2-179.9,674.4-168.6,1056.7,33.7c539.5,281,584.4,550.7,550.7,3574.5c-33.7,2585.4-44.9,2776.5-269.7,3068.7c-123.7,168.6-359.7,415.9-517.1,528.3C4932.5,793,4044.6,871.7,3594.9,658.2z"></path>
                </g>
              </g>
            </svg>
          </a>
          <a onclick="attachLink()">
            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" height="100%" viewBox="0 0 512 512" fill="#666">
              <path d="M131.525,357.807l226.281-226.281l22.625,22.625L154.15,380.432L131.525,357.807z M218.588,361.244  c2.719,10.594,0.453,22.219-7.844,30.5l-45.266,45.281c-12.5,12.5-32.734,12.5-45.25,0l-45.25-45.281  c-12.516-12.5-12.516-32.719,0-45.25l45.25-45.25c8.281-8.281,19.859-10.531,30.453-7.875l47.578-47.563  c-37.469-26.594-89.734-23.359-123.281,10.188l-45.266,45.25c-37.422,37.438-37.422,98.344,0,135.781l45.266,45.25  c37.422,37.438,98.328,37.438,135.766,0l45.25-45.25c33.563-33.563,36.75-85.875,10.141-123.344L218.588,361.244z M482.275,74.979  l-45.25-45.266c-37.438-37.422-98.344-37.422-135.781,0l-45.25,45.266c-33.547,33.547-36.781,85.813-10.188,123.281l47.594-47.594  c-2.688-10.578-0.438-22.156,7.844-30.438l45.25-45.25c12.5-12.5,32.75-12.516,45.25,0l45.281,45.25  c12.5,12.516,12.469,32.766,0,45.25l-45.281,45.266c-8.281,8.297-19.906,10.547-30.5,7.844l-47.563,47.563  c37.438,26.625,89.781,23.406,123.344-10.156l45.25-45.25C519.713,173.307,519.713,112.4,482.275,74.979z"/>
            </svg>
          </a>
        </div>

        <div class="create-post-buttons">
          <a onclick="closePost()">Cancel</a>
          <?php
            if($noClass) {
              echo '<a class="disabled">Post</a>';
            } else {
              echo '<a onclick="submitPost()">Post</a>';
            }
          ?>
        </div>
      </div>
    </div>
  </div>
</div>


<div id="attachFile">
  <div class="attachFile-modal">
    <div onclick="closeFile()" class="attachFile-close-button">
      <div></div>
      <div></div>
    </div>

    <div class="attachFile-top">
      <h4>Attach files to your Post</h4>
      <div class="attachFile-top-headings">
        <a id="attachFile-upload-heading" onclick="fileSelectUpload()" class="selected">Upload</a>
        <a id="attachFile-backpack-heading" onclick="fileSelectBackpack()">My Backpack</a>
      </div>
    </div>

    <div id="attachFile-content-upload" class="attachFile-content selected t1">
      <input type="file" id="attachFile-file" multiple placeholder="">
      <div class="text-box">
        <span>Drag files here</span>
        <span>- or -</span>
        <label for="attachFile-file">Click here to select files from your computer</label>
      </div>
      <div class="text-box">
        <span>Drop them here!</span>
      </div>
      <div id="uploadFilePreview"></div>
    </div>

    <div id="attachFile-content-backpack" class="attachFile-content">
      <div class="attachFile-backpack-order-bar">
        <span>Order By:</span>
        <select id="backpack-order-by">
          <option selected value="1">Most Recent</option>
          <option value="2">Name</option>
          <option value="3">Favourites</option>
        </select>
      </div>
      <div id="attachFile-backpack-items"></div>
    </div>

    <div class="attachFile-bottom">
      <a onclick="addFiles()">Attach Files</a>
      <a onclick="closeFile()">Cancel</a>
    </div>
  </div>
</div>

<div id="reponse-elem">Post Successful</div>

<style>
  #user-action-dock {
    position: fixed;
    width: 56px;
    height: 71px;
    bottom: 0;
    right: 0;
    padding: 0 15px;
    z-index: 2;
  }

  #user-action-dock:hover {
    height: 203px;
  }

  .flex-container {
    position: relative;
    display: flex;
    flex-direction: column;
  }

  #user-action-dock:hover .flex-container {
    flex-direction: column-reverse;
  }

  #user-action-dock .flex-container a {
    position: relative;
    display: block;
    float: right;
    width: 56px;
    height: 56px;
    margin-bottom: 15px;
    border-radius: 50%;
    background: #34495e;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    box-shadow: -1px -oc1px 5px 1px rgba(0,0,0,0.4);
  }

  #user-action-dock:hover .flex-container a {
    margin-bottom: 10px;
  }

  #user-action-dock a:nth-child(1) {
    background: #505959;
    font-size: 30px;
    font-weight: 300;
  }

  #user-action-dock a:nth-child(2):after,
  #user-action-dock a:nth-child(3):after {
    line-height: 16px;
    font-size: 11px;
    display: inline;
    position: absolute;
    top: 16px;
    right: 68px;
    padding: 5px;
    background: #7f8c8d;
    border-radius: 2px;
  }

  #user-action-dock a:nth-child(2):after {
    content: "Create\0000a0Post";
  }

  #user-action-dock a:nth-child(3):after {
    content: "Send\0000a0Message";
  }

  #create-post, #attachFile {
    position: fixed;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    width: 100vw;
    height: 100vh;
    z-index: 5;
    background: rgba(16,37,66,0.7);
    display: none;
    justify-content: center;
    align-items: center;
  }

  #create-post.active, #attachFile.active {
    display: flex;
  }

  #create-post .create-post-modal, #attachFile .attachFile-modal {
    position: relative;
    box-sizing: border-box;
    width: 90%;
    max-width: 600px;
    height: auto;
    background: white;
    box-shadow: 3px 3px 10px 0px rgba(0,0,0,0.3);
  }

  #create-post .create-post-modal .create-post-title {
    display: block;
    background: #4d90fe;
    color: white;
    line-height: 0px;
    font-size: 16px;
    padding: 30px;
    margin-bottom: 20px;
  }

  #create-post .create-post-modal #create-post-close {
    position: absolute;
    right: 21px;
    top: 21px;
    width: 18px;
    height: 18px;
    cursor: pointer;
  }

  #create-post .create-post-modal #create-post-close > div {
    top: 0;
    left: 0;
    transform: rotate(45deg);
    transform-origin: left top;
    position: absolute;
    height: 1px;
    width: 25.4558441227px;
    background: white;
  }

  #create-post .create-post-modal #create-post-close > div:nth-child(2) {
    left: -7px;
    transform-origin: top right;
    transform: rotate(315deg);
  }

  #create-post .create-post-modal .create-post-form {
    position: relative;
    box-sizing: border-box;
    width: 100%;
    padding: 10px 30px;
  }

  #create-post .create-post-modal .create-post-form label {
    display: block;
    font-size: 10px;
    font-weight: 400;
    color: #aaa;
  }

  #create-post .create-post-modal .create-post-form > input {
    display: none;
  }

  #create-post .create-post-modal .create-post-form #toggle {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 25px;
    background: #efefef;
    box-shadow: 0 0 3px 0 rgba(0,0,0,0.3);
    border-radius: 12.5px;
    margin: 3px 0 15px 0;
    cursor: pointer;
  }

  #create-post .create-post-modal .create-post-form #toggle div {
    float: left;
    width: 23px;
    height: 23px;
    margin: 1px;
    border-radius: 50%;
    background: #e74c3c;
    transition: 0.2s all ease-in-out;
  }

  #create-post .create-post-modal .create-post-form #toggle.toggled div {
    margin: 1px 1px 1px 27px;
    background: #2ecc71;
    transition: 0.2s all ease-in-out;
  }

  #create-post .create-post-modal .create-post-form #input-due,
  #create-post .create-post-modal .create-post-form #input-name {
    display: none;
  }

  #create-post .create-post-modal .create-post-form #input-due.shown,
  #create-post .create-post-modal .create-post-form #input-name.shown {
    display: block;
  }

  #create-post .create-post-modal .create-post-form select,
  #create-post .create-post-modal .create-post-form textarea,
  #create-post .create-post-modal .create-post-form div > input {
    position: relative;
    display: block;
    margin: 0 0 20px 0;
    background: none;
    border: none;
    outline: none;
    color: #333;
    font-size: 14px;
    border-radius: 0;
    border-bottom: 2px solid #333;
    font-family: inherit;
  }

  #create-post .create-post-modal .create-post-form div > input[type="text"] {
    width: 300px;
    line-height: 2;
  }

  #create-post .create-post-modal .create-post-form select {
    padding: 5px 20px 5px 0;
    line-height: 1.5;
    height: 35px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
  }

  #create-post .create-post-modal .create-post-form select.requestToFill,
  #create-post .create-post-modal .create-post-form textarea.requestToFill {
    border-bottom: 2px solid #c0392b;
    color: #c0392b;
  }

  #create-post .create-post-modal .create-post-form textarea.requestToFill::-webkit-input-placeholder {
    color: #c0392b;
  }
  #create-post .create-post-modal .create-post-form textarea.requestToFill:-moz-placeholder {
    color: #c0392b;
  }
  #create-post .create-post-modal .create-post-form textarea.requestToFill::-moz-placeholder {
    color: #c0392b;
  }
  #create-post .create-post-modal .create-post-form textarea.requestToFill:-ms-input-placeholder {
    color: #c0392b;
  }

  #create-post .create-post-modal .create-post-form select option:disabled {
    display:none;
  }

  #create-post .create-post-modal .create-post-form textarea {
    resize: none;
    width: 100%;
    line-height: 2;
    padding: none;
  }

  #create-post .create-post-modal .create-post-form textarea::-webkit-input-placeholder {
    color: #333;
    line-height: 2;
  }
  #create-post .create-post-modal .create-post-form textarea:-moz-placeholder {
    color: #333;
    line-height: 2;
  }
  #create-post .create-post-modal .create-post-form textarea::-moz-placeholder {
    color: #333;
    line-height: 2;
  }
  #create-post .create-post-modal .create-post-form textarea:-ms-input-placeholder {
    color: #333;
    line-height: 2;
  }

  #create-post .create-post-modal .create-post-form input::-webkit-input-placeholder {
    color: #333;
    line-height: 2;
  }
  #create-post .create-post-modal .create-post-form input:-moz-placeholder {
    color: #333;
    line-height: 2;
  }
  #create-post .create-post-modal .create-post-form input::-moz-placeholder {
    color: #333;
    line-height: 2;
  }
  #create-post .create-post-modal .create-post-form input:-ms-input-placeholder {
    color: #333;
    line-height: 2;
  }

  #create-post .create-post-modal .create-post-form p {
    margin: 0 0 20px 0;
    font-size: 12px;
    color: #333;
  }

  #create-post .create-post-modal .create-post-form p.important {
    color: #c0392b;
  }

  #createPostAttachFiles {
    display: flex;
    justify-content: flex-start;
    flex-direction: row;
    flex-wrap: wrap;
  }

  #createPostAttachFiles .attach-file {
    position: relative;
    display: inline-block;
    background: #fefefe;
    border: 1px solid #6a6a6a;
    border-radius: 2px;
    padding: 5px;
    font-size: 13px;
    margin: 0 10px 10px 0;
  }

  #createPostAttachFiles .attach-file > span:nth-child(1) span {
    color: #aaa;
    font-style: italic;
    padding-right: 30px;
  }

  #createPostAttachFiles .attach-file > span:nth-child(2) {
    color: #666;
    cursor: pointer;
  }

  #createPostAttachFiles .attach-file > span:nth-child(2):hover {
    color: #000;
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    position: relative;
    width: 100%;
    padding-top: 5px;
    margin-bottom: 20px;
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom .create-post-icons {
    position: relative;
    display: inline-block;
    height: 20px;
    width: auto;
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom .create-post-icons a {
    margin-left: 15px;
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom .create-post-icons a:nth-child(1) {
    margin-left: 0;
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom .create-post-icons a svg:hover {
    fill: #333;
    cursor: pointer;
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom .create-post-buttons a {
    padding: 10px 15px;
    border-radius: 1px;
    text-transform: uppercase;
    font-size: 12px;
    margin-right: 10px;
    cursor: pointer;
    color: #666;
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom .create-post-buttons a:nth-child(1):hover {
    color: #000;
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom .create-post-buttons a:nth-child(2) {
    background: #4d90fe;
    color: white;
    box-shadow: 1px 1px 3px 0 rgba(0,0,0,0.2);
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom .create-post-buttons a:nth-child(2):hover {
    background: #357ae8;
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom .create-post-buttons a.disabled {
    background: #aaa;
    cursor: not-allowed;
  }

  #create-post .create-post-modal .create-post-form .create-post-form-bottom .create-post-buttons a.disabled:hover {
    background: #888;
  }

  #attachFile .attachFile-modal {
    max-width: 700px;
  }

  #attachFile .attachFile-modal > * {
    position: relative;
    box-sizing: border-box;
    display: block;
    width: 100%;
    padding: 0 30px;
    z-index: 0;
  }

  #attachFile .attachFile-modal .attachFile-top h4 {
    font-size: 20px;
    line-height: 1;
    margin: 0;
    padding: 30px 0 15px 0;
  }

  #attachFile .attachFile-modal .attachFile-top .attachFile-top-headings {
    border-bottom: 1px solid #aaa;
  }

  #attachFile .attachFile-modal .attachFile-top .attachFile-top-headings a {
    display: inline-block;
    margin-left: 10px;
    padding: 10px 0;
    line-height: 20px;
    cursor: pointer;
  }

  #attachFile .attachFile-modal .attachFile-top .attachFile-top-headings a.selected {
    border-bottom: 4px solid #357ae8;
    padding: 10px 0 6px 0;
  }


  #attachFile .attachFile-modal .attachFile-top .attachFile-top-headings a:nth-child(1) {
    margin-left: 0;
  }

  #attachFile .attachFile-modal .attachFile-content {
    display: none;
    height: 60vh;
    max-height: 500px;
    justify-content: center;
    align-items: center;
  }

  #attachFile .attachFile-modal .attachFile-content.selected {
    display: flex;
  }

  #attachFile .attachFile-modal .attachFile-content input[type="file"] {
    display: block;
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
  }

  #attachFile .attachFile-modal .attachFile-content .text-box {
    display: none;
  }

  #attachFile .attachFile-modal .attachFile-content.t1 .text-box:nth-child(2),
  #attachFile .attachFile-modal .attachFile-content.t2 .text-box:nth-child(3) {
    display: block;
  }

  #attachFile .attachFile-modal .attachFile-content .text-box > * {
    display: block;
    text-align: center;
    color: #aaa;
  }

  #attachFile .attachFile-modal .attachFile-content .text-box > span:nth-child(1) {
    font-size: 30px;
  }

  #attachFile .attachFile-modal .attachFile-content .text-box > span:nth-child(2) {
    font-size: 18px;
    margin: 10px 0;
  }

  #attachFile .attachFile-modal .attachFile-content .text-box > label {
    color: #5a5a5a;
    padding: 5px 10px;
    font-size: 13px;
  }

  #attachFile .attachFile-modal .attachFile-content .text-box > label:hover {
    color: #000;
    border: 1px solid #000;
  }

  #uploadFilePreview {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: auto;
    z-index: -1;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: row;
    flex-wrap: wrap;
  }

  #uploadFilePreview .file-preview {
    display: block;
    background: #f6f6f6;
    box-shadow: 1px 1px 5px 0px rgba(0,0,0,0.3);
    margin: 10px;
    font-size: 13px;
    line-height: 20px;
    color: #666;
    padding: 5px 10px;
    text-align: left;
  }

  #uploadFilePreview .file-preview span svg {
    float: left;
  }

  #uploadFilePreview .file-preview > span:nth-child(1) {
    font-size: 18px;
    margin-right: 10px;
  }

  #uploadFilePreview .file-preview > span:nth-child(1).failed {
    color: #c0392b;
  }

  #uploadFilePreview .file-preview > span:nth-child(1).success {
    color: #2ecc71;
  }

  #uploadFilePreview .file-preview span span {
    font-style: italic;
    color: #aaa;
  }

  #attachFile .attachFile-modal #attachFile-content-backpack {
    flex-direction: column;
    justify-content: space-between;
    align-items: flex-start;
  }

  #attachFile .attachFile-modal #attachFile-content-backpack .attachFile-backpack-order-bar {
    display: block;
    padding: 20px 0 15px 0;
    line-height: 23px;
    font-size: 15px;
  }

  #attachFile .attachFile-modal #attachFile-content-backpack .attachFile-backpack-order-bar select {
    display: inline-block;
    font-size: 15px;
    font-family: inherit;
    margin: 0 0 0 10px;
    background: none;
    border: none;
    border-bottom: 2px solid #333;
    outline: none;
    color: #333;
    border-radius: 0;
    padding: 5px 20px 5px 0;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
  }

  #attachFile-backpack-items {
    position: relative;
    box-sizing: border-box;
    width: 100%;
    height: calc(100% - 58px);
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    flex-direction: row;
    overflow-y: scroll;
    background: #f5f5f5;
    padding: 15px;
  }

  #attachFile-backpack-items .backpack-item {
    position: relative;
    width: 210px;
    height: 120px;
    overflow: hidden;
    box-shadow: 2px 2px 5px 0px rgba(0,0,0,0.3);
    margin: 10px 0;
  }

  #attachFile-backpack-items .backpack-item .favourite {
    content: "★";
    box-sizing: border-box;
    display: block;
    position: absolute;
    top: 0;
    right: 0;
    width: 30px;
    height: 30px;
    padding: 5px;
    font-size: 15px;
    color: #f1c40f;
  }

  #attachFile-backpack-items .backpack-item > iframe,
  #attachFile-backpack-items .backpack-item > div:nth-child(1) {
    position: relative;
    width: 210px;
    height: 120px;
    background-size: cover;
    background-position: center;
    border: none;
  }

  #attachFile-backpack-items .backpack-item > .backpack-image-word {
    background-image: url(images/attachments/word.gif);
  }
  #attachFile-backpack-items .backpack-item > div.backpack-image-ppt {
    background-image: url(images/attachments/ppt.gif);
  }
  #attachFile-backpack-items .backpack-item > div.backpack-image-excel {
    background-image: url(images/attachments/excel.gif);
  }
  #attachFile-backpack-items .backpack-item > div.backpack-image-pdf {
    background-image: url(images/attachments/pdf.gif);
  }
  #attachFile-backpack-items .backpack-item > div.backpack-image-code {
    background-image: url(images/attachments/code.gif);
  }
  #attachFile-backpack-items .backpack-item > div.backpack-image-zip {
    background-image: url(images/attachments/zip.gif);
  }
  #attachFile-backpack-items .backpack-item > div.backpack-image-text {
    background-image: url(images/attachments/text.gif);
  }
  #attachFile-backpack-items .backpack-item > div.backpack-image-r-text {
    background-image: url(images/attachments/text.gif);
  }

  #attachFile-backpack-items .backpack-item .backpack-item-hover {
    position: absolute;
    display: none;
    left: 0;
    right: 0;
    bottom: 0;
    box-sizing: border-box;
    background: white;
    box-shadow: -3px -3px 5px 1px rgba(0,0,0,0.3);
    padding: 5px 15px;
  }

  #attachFile-backpack-items .backpack-item:hover .backpack-item-hover {
    display: block;
  }

  #attachFile-backpack-items .backpack-item .backpack-item-hover span {
    display: block;
    color: #333;
    font-size: 13px;
    line-height: 1.5;
  }

  #attachFile-backpack-items .backpack-item .backpack-item-hover span:nth-child(2) {
    font-style: italic;
    color: #666;
    font-size: 12px;
  }

  #attachFile .attachFile-modal .attachFile-bottom {
    width: calc(100% - 60px);
    border-top: 1px solid #aaa;
    padding: 20px 0 30px 0;
    margin: 0 30px;
  }

  #attachFile .attachFile-modal .attachFile-bottom a {
    padding: 10px 15px;
    border-radius: 1px;
    text-transform: uppercase;
    font-size: 12px;
    margin-right: 10px;
    cursor: pointer;
    color: #666;
  }

  #attachFile .attachFile-modal .attachFile-bottom a:nth-child(2):hover {
    color: #000;
  }

  #attachFile .attachFile-modal .attachFile-bottom a:nth-child(1) {
    background: #4d90fe;
    color: white;
    box-shadow: 1px 1px 3px 0 rgba(0,0,0,0.2);
  }

  #attachFile .attachFile-modal .attachFile-bottom a:nth-child(1):hover {
    background: #357ae8;
  }

  #attachFile .attachFile-modal .attachFile-close-button {
    position: absolute;
    right: 30px;
    top: 30px;
    height: 18px;
    width: 18px;
    padding: 0;
    cursor: pointer;
    z-index: 2;
  }

  #attachFile .attachFile-modal .attachFile-close-button div {
    position: absolute;
    left: 0;
    top: 0;
    display: block;
    height: 1px;
    width: 25px;
    background: #333;
    transform-origin: left top;
    transform: rotate(45deg);
  }

  #attachFile .attachFile-modal .attachFile-close-button div:nth-child(2) {
    transform: rotate(135deg);
    left: 18px;
    top: 1px;
  }

  #attachFile .attachFile-modal .attachFile-close-button:hover div {
    background: #000;
  }

  #reponse-elem {
    display: none;
    position: fixed;
    right: 80px;
    bottom: 18px;
    height: 51px;
    background: white;
    box-shadow: 0px 0px 5px 0 rgba(0,0,0,0.3);
    z-index: 1;
    font-size: 14px;
    line-height: 50px;
    padding: 0 20px;
    color: #16a085;
  }

  #reponse-elem.shown {
    display: block;
  }

</style>

<script src="https://cdn.filesizejs.com/filesize.min.js"></script>
<script src="https://www.gstatic.com/firebasejs/3.6.4/firebase.js"></script>
<script>


// show the newly created post
var showRecentPost = function(postHash) {
  var request,
      contentElem = document.getElementById('content');;

  // set the correct request type (AJAX)
  if (window.XMLHttpRequest) {
    request = new XMLHttpRequest();
  } else {
    request = new ActiveXObject("Microsoft.XMLHTTP");
  }

  // on return of information from AJAX request
  request.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      if(contentElem.innerHTML == '<p>Loading...</p>') {
        contentElem.innerHTML = '';
      }

      // add the HTML from the request to the element on this page
      contentElem.innerHTML = this.responseText + contentElem.innerHTML;
    }
  };

  // send the request to the php file: testEmail.php with the inputted email
  request.open('GET', 'php/getPost.php?ph='+postHash+'&uh=<?php echo $userInfo['link']; ?>', true);
  request.send();
};

// create post window
var createPostWindow = function () {
  document.getElementById('create-post').className = 'active';
  text.select();
};

// close post button
var closePost = function () {
  document.getElementById('create-post').className = '';
};

// clear files and close post button
var reallyClosePost = function () {
  document.getElementById('create-post').className = '';

  // clear the files stored in pages data
  intermediateFiles = [];
  attachedFiles = [];
  document.getElementById("attachFile-file").value = "";
};

// submit the post
var submitPost = function() {
  var groupHash = <?php
                    if($groupLink) {
                      echo '"'.$groupLink.'"';
                    } else {
                      echo 'document.getElementById("create-post-input-group").value';
                    }
                  ?>,
      text = document.getElementById('create-post-input-text').value,
      request;

  if(groupHash == 'Select a Group') {

    var select = document.getElementById('create-post-input-group');
    select.className = 'requestToFill';
    select.addEventListener('change', function() {
      select.className = '';
    });

  } else if(text != '') {

    var assignment = false;
    <?php
      if($userInfo['type'] == 't') {
        echo 'if (document.getElementById("create-post-input-ass").checked) { assignment = true; }';
      }
    ?>
    var due, url;
    if (assignment) {
      due = document.getElementById('create-post-input-due').value;
      var title = document.getElementById('create-post-input-name').value;
      url = 'php/createPost.php?h=' + encodeURIComponent(randomPostHash) + '&t=' + encodeURIComponent(text) + '&g=' + groupHash + '&a=1&d=' + encodeURIComponent(due) + '&u=<?php echo $userInfo['link'];?>&ti='+title;
    } else {
      url = 'php/createPost.php?h=' + encodeURIComponent(randomPostHash) + '&t=' + encodeURIComponent(text) + '&g=' + groupHash + '&u=<?php echo $userInfo['link'];?>';
    }

    if (window.XMLHttpRequest) {
      request = new XMLHttpRequest();
    } else {
      request = new ActiveXObject("Microsoft.XMLHTTP");
    }

    // on return of information from AJAX request
    request.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var responseElem = document.getElementById('reponse-elem');
        if(this.responseText == 0) {
          responseElem.innerHTML = 'An error has occured, please reload and try again.';
          responseElem.className = 'failed';
          reallyClosePost();
          setTimeout(function() {
            responseElem.className = '';
          }, 10000);
        } else {
          responseElem.innerHTML = 'Post Uploaded';
          responseElem.className = 'shown';
          reallyClosePost();
          setTimeout(function() {
            responseElem.className = '';
          }, 5000);
          showRecentPost(this.responseText);
        }
      }
    };

    // send the request to the php file: requestBackpackFiles.php
    request.open('GET', url);
    request.send();

  } else {
    var textarea = document.getElementById('create-post-input-text');
    textarea.className = 'requestToFill';
    textarea.addEventListener('change', function() {
      textarea.className = '';
    });
  }
};

// toggle elements
var toggle = document.getElementById('toggle'),
    duedateInput = document.getElementById('input-due'),
    assignmentnameInput = document.getElementById('input-name');

// switch post type to assignment
var switchAssignment = function() {
  if(toggle.className == 'toggled') {
    toggle.className = '';
    duedateInput.className = '';
    assignmentnameInput.className = '';
  } else {
    toggle.className = 'toggled';
    duedateInput.className = 'shown';
    assignmentnameInput.className = 'shown';
  }
};




// attach a file to the post
var attachFile = function() {
  closePost();
  document.getElementById('attachFile').className = 'active';
  document.getElementById('uploadFilePreview').innerHTML = '';
};

// close the attach file window
var closeFile = function() {
  // remove all the files that were uploaded during the session (not the ones that have been attached)
  intermediateFiles = [];
  document.getElementById("attachFile-file").value = "";

  // hide attach file window and open create post window
  createPostWindow();
  document.getElementById('attachFile').className = '';
};

// switch the attach file window to upload mode
var fileSelectUpload = function() {
  document.getElementById('attachFile-content-backpack').className = 'attachFile-content';
  document.getElementById('attachFile-content-upload').className = 'attachFile-content selected t1';
  document.getElementById('attachFile-backpack-heading').className = '';
  document.getElementById('attachFile-upload-heading').className = 'selected';
};

var newBackpackInfo = function() {
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
      if(this.responseText == '') {
        // add a message to the user
        document.getElementById('attachFile-backpack-items').innerHTML = 'No items in backpack.';
      } else {
        // add the HTML to the page from the request
        document.getElementById('attachFile-backpack-items').innerHTML = this.responseText;
      }
    }
  };

  // order by
  var orderBy = document.getElementById('backpack-order-by').value;

  // send the request to the php file: requestBackpackFiles.php
  request.open('GET', 'php/requestBackpackFiles.php?uh=<?php echo $userInfo['link']; ?>&o=' + orderBy);
  request.send();
}

// on change of the select element make the information change too
document.getElementById('backpack-order-by').addEventListener('change', newBackpackInfo, false);

// switch the attach file window to backpack mode
var fileSelectBackpack = function() {
  document.getElementById('attachFile-content-upload').className = 'attachFile-content';
  document.getElementById('attachFile-content-backpack').className = 'attachFile-content selected';
  document.getElementById('attachFile-upload-heading').className = '';
  document.getElementById('attachFile-backpack-heading').className = 'selected';

  // request the backpack HTML for the post
  newBackpackInfo();
};

// variables needed for file upload
var randomPostHash = '<?php echo md5(uniqid('', true)); ?>', // random hash for storing files
    fileCounter = 0,  // the number of files (to make sure no file has the same name)
    attachedFiles = [], // the files attached
    intermediateFiles = []; // the files uploaded but not attached

// FIREBASE initialisation process
var config = {
  apiKey: "AIzaSyBhl6sYMC2vY-6khkDD-61zIJ3GY5UTqyk",
  storageBucket: "involve-a4e62.appspot.com"
};
firebase.initializeApp(config);
var storageRef = firebase.storage().ref();

// called to upload the files upon change of the file input
var uploadFiles = function(evt) {
  // stop the adding of a file from doing anything by default
  evt.stopPropagation();
  evt.preventDefault();

  // get the number of files added
  var numFiles = evt.target.files.length;

  // show the drag and drop screen
  draggable.className = 'attachFile-content selected t1';

  // for each file uploaded
  for(i=0;i<numFiles;i++) {
    fileCounter ++; // progress the fileCounter

    // file information variables
    var file = evt.target.files[i],
        name = file.name,
        size = file.size,
        num = fileCounter;

    // should give the type of all the valid file formats (images, code, word,
    //     powerpoints, excel, zip, plain text and pdfs) otherwise assign an
    //     undefined tag meaning bad file format
    switch (file.type) {
      case 'image/jpeg':
      case 'image/gif':
      case 'image/png':
      case 'image/tiff':
      case 'image/x-tiff':
        type = 'img';
        break;
      case 'text/html':
      case 'text/css':
      case 'text/javascript':
      case 'application/x-pointplus':
        type = 'code';
        break;
      case 'application/msword':
      case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
        type = 'word';
        break;
      case 'application/mspowerpoint':
      case 'application/powerpoint':
      case 'application/x-mspowerpoint':
      case 'application/vnd.ms-powerpoint':
      case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
        type = 'ppt';
        break;
      case 'application/excel':
      case 'application/x-excel':
      case 'application/x-msexcel':
      case 'application/vnd.ms-excel':
        type = 'excel';
        break;
      case 'application/x-compressed':
      case 'application/x-zip-compressed':
      case 'application/zip':
      case 'multipart/x-zip':
        type = 'zip';
        break;
      case 'text/plain':
        type = 'text';
        break;
      case 'application/pdf':
        type = 'pdf';
        break;
      case 'application/rtf':
      case 'application/x-rtf':
      case 'text/rtf':
      case 'text/richtext':
        type = 'r-text';
        break;
      default:
        type="undef";
        break;
    }

    // metadata to be included with the file
    var metadata = {
          'contentType': file.type,
          customMetadata: {
            'num': num,
            'name': name,
            'size': size,
            'type': type
          }
        };

    // add the file to the storage (firebase)
    storageRef.child('files/' + fileCounter + randomPostHash + name).put(file, metadata).then(function(snapshot) {
      // get the external URL of the newly created file
      url = snapshot.metadata.downloadURLs[0];

      // get the file preview ID
      var a = document.getElementById('file-preview-'+snapshot.metadata.customMetadata.num+'-status');

      // add a green tick (indicating uploaded)
      a.innerHTML = '&#10004;';
      a.className = 'success';

      // add the file to the intermediate file's array
      intermediateFiles.push(['u', snapshot.metadata.customMetadata.name, snapshot.metadata.customMetadata.size, snapshot.metadata.customMetadata.type, url]);

      var request;
      if (window.XMLHttpRequest) {
        request = new XMLHttpRequest();
      } else {
        request = new ActiveXObject("Microsoft.XMLHTTP");
      }

      // send the request to the php file: requestBackpackFiles.php
      request.open('GET', 'php/uploadTempFile.php?p='+encodeURIComponent(randomPostHash)+'&l='+encodeURIComponent(url)+'&n='+snapshot.metadata.customMetadata.name+'&t='+snapshot.metadata.customMetadata.type+'&u=<?php echo $userInfo['link']; ?>');
      request.send();
    });

    // preview the file (showing it is loading)
    var filePreview = document.getElementById('uploadFilePreview');
    filePreview.innerHTML =  filePreview.innerHTML + `<div class="file-preview">
      <span id="file-preview-`+num+`-status">
        <svg width="15" height="15" viewBox="0 0 38 38" xmlns="http://www.w3.org/2000/svg" stroke="#666">
          <g fill="none" fill-rule="evenodd">
              <g transform="translate(1 1)" stroke-width="3">
                  <circle stroke-opacity=".5" cx="18" cy="18" r="18"/>
                  <path d="M36 18c0-9.94-8.06-18-18-18">
                      <animateTransform
                          attributeName="transform"
                          type="rotate"
                          from="0 18 18"
                          to="360 18 18"
                          dur="1s"
                          repeatCount="indefinite"/>
                  </path>
              </g>
          </g>
        </svg>
      </span>

      <span>`+name+` <span>(`+filesize(size, {round:1})+`)</span></span>
    </div>`;
  }
};

// get the elements needed for drag and drop
var draggable = document.getElementById("attachFile-content-upload"),
    input = document.getElementById("attachFile-file");

// if the files change (dragged or manually inputted)
input.addEventListener('change', uploadFiles, false);

// show that that a file is being dragged over
['dragover','dragenter'].forEach(function(event) {
  input.addEventListener(event, function() {
    // add some effect saying to realease file
    draggable.className = 'attachFile-content selected t2';
  });
});

// remove any effect left behind by a file being dragged over the box
['dragleave','dragend'].forEach( function(event) {
  input.addEventListener(event, function() {
    // go back to the first text box
    draggable.className = 'attachFile-content selected t1';
  });
});

// remove a specific attached file (clicking x)
var removeUploadedFile = function(num) {
  attachedFiles.splice(num, 1); // remove from array
  createPostUpdateFiles(); // update the page
};

// show the attached files on page
var createPostUpdateFiles = function() {
  // get reference to HTML element
  var div = document.getElementById('createPostAttachFiles');
  div.innerHTML = ''; // remove anything in it already

  // for each attached file
  for(i=0;i<attachedFiles.length;i++) {
    var file = attachedFiles[i];
    // add some HTML
    div.innerHTML = div.innerHTML + '<div class="attach-file"><span>'+file[1]+' <span>('+filesize(file[2], {round:1})+')</span></span><span onclick="removeUploadedFile('+i+')">&#10005;</span></div>';
  }
}

// add the selected files to the post
var addFiles = function() {
  // push all the files uploaded to the attached files
  attachedFiles.push.apply(attachedFiles, intermediateFiles);
  // show them on the page
  createPostUpdateFiles();
  // open the create post window
  createPostWindow();
  // hide the attach file window
  document.getElementById('attachFile').className = '';
  // clear the uploaded files array and input (in order to not double submit a file for attachment)
  intermediateFiles = [];
  document.getElementById("attachFile-file").value = "";
};

// attach a link to the post
var attachLink = function() {
  alert('attaching a link');
};

</script>
