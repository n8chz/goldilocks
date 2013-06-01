<!DOCTYPE HTML>

<head>

<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=Edge"><![endif]-->
<!-- see https://developer.mozilla.org/en-US/docs/Persona/Quick_Setup -->

<title>Mozilla Persona login page</title>

<style>

.leftside {
 height:0em;
 width: 2em;
 border: 0.5em solid #59a9d9;
 z-index: 1;
 color:black;
 position: absolute;
 left: 1em;
}

.leftside .notchtext {
 font-size: 1em;
 position: relative;
 top: -0.6em;
 left: -0.3em;
}

.notch {
 height:0;
 border: 0.5em solid #468cc4;
 border-left-color: #59a9d9;
 position: absolute;
 left: 0.5em;
 top: -0.5em;
 z-index: 2;
}

.notch:after {
 content: "";
 height:0;
 width: 1px;
 border: 0.5em solid transparent;
 border-left-color: #468cc4;
 position: absolute;
 top: -0.5em;
 right: -1.5em;
 z-index: 2;
}

.notch .notchtext {
 margin-left: 0.5em;
 width:auto;
}

.notchtext, .user {
 font-size: 0.7em;
 font-family: sans;
 font-weight: bold;
 color:white;
 position: relative;
 bottom: 0.5em;
 white-space: nowrap;
}

.user {
 float: right;
}

.persona-container {
 position: absolute;
 right: 20em;;
}


div.persona-container:hover {
 cursor: pointer;
}

</style>

</head>

<body>


<header>

<div class="persona-container">

<div class="leftside" id="login" onclick="navigator.id.request();" hidden>
<div class="notchtext">&#9823;</div><div class="notch"><span class="notchtext">Sign in with your Email</span></div>
</div>

<div class="leftside" id="logout" onclick="navigator.id.logout();" hidden>
<div class="notchtext">&#9823;</div><div class="notch"><span class="notchtext">Sign out, <span id="user"></span></span></div>
</div>

</div>


</header>


<script src="https://login.persona.org/include.js"></script>
<script src="jquery-1.9.1.min.js"></script>

<script type="text/JavaScript">

// See: http://www.quirksmode.org/js/cookies.html
window.readCookie = function(name) {
 var nameEQ = name + '=';
 var ca = document.cookie.split(';');
 for (var i = 0; i < ca.length; i++) {
  var c = ca[i];
  while (c.charAt(0) == ' ') {
   c = c.substring(1, c.length);
  }
  if (c.indexOf(nameEQ) == 0) {
   return c.substring(nameEQ.length, c.length);
  }
 }
 return null;
};

// see http://phpmaster.com/authenticate-users-with-mozilla-persona/#highlighter_786159

var currentUser = window.readCookie("auth");

if (!currentUser) {
 currentUser = null;
}

if (currentUser != null) {
 currentUser = decodeURIComponent(currentUser);
}


if (currentUser) {
 $("#login").hide();
 $("#logout").show();
 $("#user").text(currentUser);
}
else {
 $("#login").show();
 $("#logout").hide();
}


var watchConstructor = {
 loggedInUser: currentUser,
 onlogin: function (assertion) {
  $.ajax({
    type: "POST",
    data: {assertion: assertion},
    url:  "login.php",
    success: function (res,status, xhr) {
     window.location.reload();
    },
    error: function (xhr, status, err) {
     navigator.id.logout();
     alert("login error: "+err);
    }
  });
 },
 onlogout: function () {
  $.ajax({
    type: "POST",
    url:  "logout.php",
    success: function (res,status, xhr) {
     // alert("logout success");
     window.location.reload();
    },
    error: function (xhr, status, err) {
     alert("logout error: "+err);
    }
  });
 }
};




navigator.id.watch(watchConstructor);

</script>

<div>

<?php

if (isset($_COOKIE["auth"])) {
 require_once("secrets.php");
 # echo "<p>Secrets on board</p>\n";
 $db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
 # echo "<p>Database connected</p>\n";
 $db->query(<<<CREATE_USER_TABLE
  create table if not exists ramacles_user (
   id integer key auto_increment,
   createdate datetime,
   email varchar(100),
   handle varchar(50),
   url varchar(150),
   avatar varchar(16)
  )
CREATE_USER_TABLE
 );
 echo $db->error;
 # echo "<p>Table created</p>\n";
 /* Uncomment for diagnostic purposes only
 $result = $db->query("select * from ramacles_user");
 echo "<pre>\n";
 while ($row = $result->fetch_assoc()) {
  print_r($row);
 }
 echo "</pre>\n";
 */
 $cleanmail = $db->real_escape_string($_COOKIE["auth"]);
 # echo "<p>Mail cleaned</p>\n";
 $result = $db->query("select handle from ramacles_user where email=\"$cleanmail\"");
 # echo "<p>Query made</p>\n";
 # echo $db->error;
 $row = $result->fetch_assoc();
 # echo "<p>Assoc fetched</p>\n";
 if ($row) {
  echo "<p>Welcome back, {$row['handle']}</p>\n";
 } else {
  echo "<p>Your email not on file.\n<a href=\"newacct.php\">Click here</a> if you would like to create an account.</p>\n";
 }
 $result->close();
}



?>


</div>

</body>

</html>

