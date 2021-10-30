<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>Raffle/Lottery Script</title>
<meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />
<link rel="stylesheet" type="text/css" href="css/screen.css" />

<!--
  last update 2021/10/28  
-->

<style type="text/css">
  /*CSS For Floatie*/
  #dhtmlfloatie{
  color: black;
  position: absolute;
  left: 0;
  left: -900px;
  filter:alpha(opacity=0);
  -moz-opacity:0;
  border: 2px solid black;
  padding: 5px;
  z-index: 100;
  }
  /*end*/

  p, td, th {
  font: 1em Arial, Helvetica, sans-serif;
  }
  .datatable {
  border: 1px solid #D6DDE6;
  border-collapse: collapse;
  width: 100%;
  }
  .datatable td {
  border: 1px solid #D6DDE6;
  padding: 4px;
  }
  .datatable th {
  border: 1px solid #828282;
  background-color: #BCBCBC;
  font-weight: bold;
  text-align: left;
  padding-left: 4px;
  }
  .datatable caption {
  font: bold 0.9em Arial, Helvetica, sans-serif;
  color: #33517A;
  text-align: left;
  padding-top: 3px;
  padding-bottom: 8px;
  }

  .datatable tr:hover, .datatable tr.hilite {
  background-color: #DFE7F2;
  color: #000000;
  }
  a.info{
  color:#FFFFFF;
  text-decoration:none;
  border-bottom:1px dotted #FFFFFF;
  }


  .style1 {
    color: #FF0000;
    font-weight: bold;
    font-size: 14px;
  }
  .style2 {color: #FFFFFF}
  .style3 {font-size: 14px; font-weight: bold;}

</style>

  <script type="text/javascript">
  var floattext=new Array()
  floattext[0]='Date auction ends.'
  floattext[1]='Price of a single ticket. Only one ticket may be purchased per auction.'
  floattext[2]='Out of the total number of people who enter this raffle, the percentage of people who win. Unlike many other raffles, our raffles have many winners!'
  floattext[3]='Amount of money available to be won. This number goes up as people enter the raffle.'
  floattext[4]='Logged in users can purchase tickets. Clicking Details allows you to view full auction details that are not displayed by default.'
  floattext[5]='Each auction has a unique identification number.'
  floattext[6]='Date auction began.'
  floattext[7]='How long the auction will last.'
  floattext[8]='Percentage of the pot that is available to be won. For example, if this percentage was 90% and if 1000 people enter paying $1, then the prize money would be $900 (it would then be split among the percentage of winners), and the site keeps $100, enabling it to stay open and provide this great service. Everybody wins!'
  floattext[9]='The total tickets purchased for this auction'
  floattext[10]='The total amount of money, or pot, associated with this auction. This number is based on the amount of tickets bought, and the cost of each ticket. '

  var floatiewidth="250px" //default width of floatie in px
  var floatieheight="60px" //default height of floatie in px. Set to "" to let floatie content dictate height.
  var floatiebgcolor="lightyellow" //default bgcolor of floatie
  var fadespeed=70 //speed of fade (5 or above). Smaller=faster.

  var baseopacity=0
  function slowhigh(which2){
    imgobj=which2
    browserdetect=which2.filters? "ie" : typeof which2.style.MozOpacity=="string"? "mozilla" : ""
    instantset(baseopacity)
    highlighting=setInterval("gradualfade(imgobj)",fadespeed)
  }

  function instantset(degree){
    cleartimer()
    if (browserdetect=="mozilla")
    imgobj.style.MozOpacity=degree/100
    else if (browserdetect=="ie")
    imgobj.filters.alpha.opacity=degree
  }

  function cleartimer(){
    if (window.highlighting) clearInterval(highlighting)
  }

  function gradualfade(cur2){
    if (browserdetect=="mozilla" && cur2.style.MozOpacity<1)
    cur2.style.MozOpacity=Math.min(parseFloat(cur2.style.MozOpacity)+0.1, 0.99)
    else if (browserdetect=="ie" && cur2.filters.alpha.opacity<100)
    cur2.filters.alpha.opacity+=10
    else if (window.highlighting)
    clearInterval(highlighting)
  }

  function ietruebody(){
    return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
  }

  function paramexists(what){
    return(typeof what!="undefined" && what!="")
  }

  function showfloatie(thetext, e, optbgColor, optWidth, optHeight){
    var dsocx=(window.pageXOffset)? pageXOffset: ietruebody().scrollLeft;
    var dsocy=(window.pageYOffset)? pageYOffset : ietruebody().scrollTop;
    var floatobj=document.getElementById("dhtmlfloatie")
    floatobj.style.left="-900px"
    floatobj.style.display="block"
    floatobj.style.backgroundColor=paramexists(optbgColor)? optbgColor : floatiebgcolor
    floatobj.style.width=paramexists(optWidth)? optWidth+"px" : floatiewidth
    floatobj.style.height=paramexists(optHeight)? optHeight+"px" : floatieheight!=""? floatieheight : ""
    floatobj.innerHTML=thetext
    var floatWidth=floatobj.offsetWidth>0? floatobj.offsetWidth : floatobj.style.width
    var floatHeight=floatobj.offsetHeight>0? floatobj.offsetHeight : floatobj.style.width
    var winWidth=document.all&&!window.opera? ietruebody().clientWidth : window.innerWidth-20
    var winHeight=document.all&&!window.opera? ietruebody().clientHeight : window.innerHeight
    e=window.event? window.event : e
    floatobj.style.left=dsocx+winWidth-floatWidth-5+"px"
    if (e.clientX>winWidth-floatWidth && e.clientY+20>winHeight-floatHeight)
    floatobj.style.top=dsocy+5+"px"
    else
    floatobj.style.top=dsocy+winHeight-floatHeight-5+"px"
    slowhigh(floatobj)
  }

  function hidefloatie(){
    var floatobj=document.getElementById("dhtmlfloatie")
    floatobj.style.display="none"
  }
</script>

</head>
<title>Lottery Script</title>
<script language="javascript">
function confirmTicketBuy(lottery_id) {
   if (confirm("Are you sure you want to buy this ticket?")) {
      new_href = "index.php?go=buyticket&id=" + lottery_id;
      location.href = new_href;
   }
}
</script>
</head>
<body>
<table border="0" align="center" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <div id="siteBox">
        <div id="header">
          <h1 class="title">7WIN </h1>
          <h2 class="subTitle"><a href="#">7WIN.CA</a></h2>
          <!-- Opera bug fix -->
          &nbsp;
        </div>
        <div id="content">
          <div id="news">
            <div class="block">
              <h1>
                Menu
	            </h1>
              <div class="links">
                <a href="index.php">Main Page</a>
                <?php
                // echo("-------logged compare");
                if (isset($logged) && ($logged)){ ?>
                <a href="account.php">My Account</a>
                <a href="index.php?go=logout">Logout</a>
                <a href="index.php?go=support">Support Center</a>
                <?php } else {?>
                <a href="signup.php">Sign Up</a>
                <a href="index.php?go=login">Login</a>
                <?php }?>
                <a href="index.php?go=archive">Archive</a>
                <a href="index.php?go=faq">Frequently Asked Questions</a>
              </div>
              <div class="bottom">&nbsp;</div>
            </div>
          </div>
        <div id="text">





