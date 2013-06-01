<html>
<head>
</head>
<body>
<?php
require_once("secrets.php");
$db = new MySQLi($sqlhost, $sqluser, $sqlpass, $sqldb);
$db->query("drop table if exists ramacles_user");
echo $db->error;
?>
</body>
</html>
