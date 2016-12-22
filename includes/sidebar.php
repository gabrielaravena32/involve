<div id="sidebar">

  <div id="current-class">
    <div id="current-class-text">Currently you have Physics with Mr Latham</div>
    <div id="current-class-bar"><div id="current-class-progress"></div></div>
  </div>

  <div id="next-classes">
    <h3>Coming Up</h3>

    <div id="next-classes-list">
      <div class="next-classes-item class-colour-3">
        <div class="next-class-room">109</div>
        <div class="next-class-name">English Advanced</div>
      </div>
      <div class="next-classes-item class-colour-9">
        <div class="next-class-room">M1.02</div>
        <div class="next-class-name">Physics</div>
      </div>
      <div class="next-classes-item class-colour-12">
        <div class="next-class-room">88</div>
        <div class="next-class-name">Maths Ext 1</div>
      </div>
    </div>
  </div>

  <div id="active-teachers">
    <h3>Teachers</h3>

    <div id="active-teachers-list">
      <div class="active-user">
        <img src="images/default.png" alt="INSERT_USER's profile photo" />
        <div class="active-user-right">
          <a href="">John Appleased</a>
          <span>Currently online</span>
          <div class="bubble bubble-green"></div> <!-- the colour will need to be dynamic -->
        </div>
      </div>
      <div class="active-user">
        <img src="images/default.png" alt="INSERT_USER's profile photo" />
        <div class="active-user-right">
          <a href="">Joshua Smith</a>
          <span>In Class</span>
          <div class="bubble bubble-yellow"></div>
        </div>
      </div>
      <div class="active-user">
        <img src="images/default.png" alt="INSERT_USER's profile photo" />
        <div class="active-user-right">
          <a href="">Lucy Pan</a>
          <span>Unavailable</span>
          <div class="bubble bubble-red"></div>
        </div>
      </div>
    </div>
  </div>

</div>

<style>
  #sidebar {
    position: fixed;
    right: 0;
    top: 68px;
    bottom: 0;
    width: 215px;
    height: calc(100% - 68px);

    background: #EDF1F2;
    color: #95a5a6;
    overflow: hidden;
  }

  #sidebar > * {
    position: relative;
    width: 100%;
    box-sizing: border-box;
    padding: 10px 15px;
  }

  #sidebar #current-class {
    padding: 20px 15px 15px 15px;
    font-size: 17px;
    font-weight: 300;
    background: #EF695B;
    color: white;
    line-height: 1.5;
    margin-bottom: 10px;
    box-shadow: 0px 0px 5px 2px rgba(0,0,0,0.2);
  }

  #current-class-bar {
    position: relative;
    width: 100%;
    height: 3px;
    margin: 10px 0;
    background: #DD6255;
  }

  #current-class-progress {
    position: relative;
    display: block;
    width: 45%;
    height: 100%;
    background: white;
  }

  #current-class-progress::after {
    content: " ";
    display: block;
    position: absolute;
    width: 9px;
    height: 9px;
    background: white;
    border-radius: 50%;
    right: 0;
    top: -3px;
  }

  #sidebar h3 {
    margin: 0;
    text-transform: uppercase;
    font-size: 13px;
    font-weight: 500;
    margin: 10px 0px;
  }

  .next-classes-item {
    height: 30px;
    margin-bottom: 3px;
    padding: 3px 10px;
    box-sizing: border-box;
    border-radius: 2px;
  }

  .next-classes-item > * {
    position: relative;
    float: left;
    display: inline-block;
    height: 24px;
    line-height: 24px;
    font-size: 13px;
    color: white;
    font-weight: 500;
  }

  .next-classes-item .next-class-name {
    margin-left: 6px;
    font-weight: 400;
  }

  #active-user * {
    position: relative;
  }

  #active-teachers .active-user {
    width: 100%;
    padding: 2px 0;
    height: 40px;
    line-height: 15px;
    font-size: 13px;
  }

  #active-teachers .active-user img {
    height: 25px;
    width: 25px;
    border-radius: 50%;
    float: left;
    margin-right: 6px;
    margin-top: 10px;
  }

  #active-teachers .active-user .active-user-right {
    position: relative;
    padding: 10px 0;
  }

  #active-teachers .active-user .active-user-right a {
    text-decoration: none;
    font-weight: 500;
    color: #7f8c8d;
    display: block;
  }

  #active-teachers .active-user .active-user-right .bubble {
    position: absolute;
    right: 0;
    top: 20px;
    height: 10px;
    width: 10px;
    border-radius: 50%;
  }

  #active-teachers .active-user .active-user-right .bubble-green {
    background: #27ae60;
  }
  #active-teachers .active-user .active-user-right .bubble-yellow {
    background: #f1c40f;
  }
  #active-teachers .active-user .active-user-right .bubble-red {
    background: #c0392b;
  }

</style>
