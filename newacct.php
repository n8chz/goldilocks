<!DOCTYPE HTML>

<head>

<meta http-equiv="Content-type" content="text/html;charset=UTF-8" />

<title>Create new account</title>

</head>

<body>

<form enctype="multipart/form-data" action="newuser.php" method="POST">
<fieldset><legend>Create new user account</legend>
<label for="email">Your email address:</label>
<input name="email" id="email" type="email" value="<?php echo $_COOKIE['auth'] ?>" readonly /><br />
<label for="handle">Create a handle for yourself:</label>
<input name="handle" id="handle" type="text" autofocus required /><br />
<label for="url">URL of your web page (optional):</label>
<input name="url" id="url" type="url" /><br />
<label for="avatar">Upload an image for use as an avatar (optional):</label>
<input name="avatar" id="avatar" type="file" accept="image/*" /><br />
<input type="submit" value="Continue" />
</fieldset>
</form>

</body>

</html>

