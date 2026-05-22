<?php  
session_start();
$_SESSION['loggedIN'] = false;
$_SESSION = array();
session_destroy();
?>
<!DOCTYPE HTML>
<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>the Fronion - Log Out</title>
  <link href="styles-main.css" rel="style.css" type="text/css">

</head>

<body>
<?php require_once "login.php" ?>
</body>

</html>