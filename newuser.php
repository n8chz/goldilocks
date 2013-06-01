<!DOCTYPE HTML>

<head>

<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

<title>New user account</title>

</head>

<body>
<?php

require_once("secrets.php");
echo "<!-- Secrets on board -->\n";
$db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
echo "<!-- Database connected -->\n";
$handle = $_POST["handle"];
$newhandle = $db->real_escape_string($handle);
$result = $db->query("select handle from ramacles_user where handle = '$newhandle'");
echo $db->error;
echo "<!-- handle query dun -->\n";
$row = $result->fetch_assoc();
if ($row) {
 echo <<<RETRY
  <div>
  <p>Handle <span class="handle">$handle</span> is already taken.</p>
  <p>Click <a href="newacct.php">here</a> to try again.</p>
  </div>
RETRY;
}
else {
 $origname=$_FILES["avatar"]["name"];
 $components = preg_split("/\./", $origname);
 $suffix = $components[1];
 $tmpname=$_FILES["avatar"]["tmp_name"]; # http://us3.php.net/manual/en/function.move-uploaded-file.php 
 $components = preg_split("/\//", $tmpname);
 $logname = $components[2].".".$suffix; # construct image filename from tempname and suffix
 $muf = move_uploaded_file($tmpname, "avatars/$logname");
 echo ($muf ? "" : "<p>File upload failed: $origname -&gt; $logname</p>\n");
 shell_exec("mogrify -resize 64x64 avatars/$logname"); # we don't want huge files as avatars.
 $email = $db->real_escape_string($_POST["email"]);
 $handle = $db->real_escape_string($_POST["handle"]);
 $url = isset($_POST["url"]) ? ($db->real_escape_string($_POST["url"])) : null; # look into this
 echo "<!-- $url -->\n";
 $db->query("insert into ramacles_user(createdate, email, handle, url, avatar) values (now(), '$email', '$handle', '$url', '$logname')");
 echo $db->error;
 echo "<div><h1>New account created:</h1>\n";
 if ($url) echo "<a href=\"$url\">\n";
 echo "<img src=\"avatars/$logname\" />\n<span class=\"handle\">$handle</span>\n";
 if ($url) echo "</a>\n";
 echo "</div>\n";
}
$result->close();

?>

<p><a href=".">Back to home page</a></p>

</body>

</html>

