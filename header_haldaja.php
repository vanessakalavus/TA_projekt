<?php
require_once "../../config2.php";

/* if(!isset($_SESSION["user_id"])){
	    header("Location: logi_sisse.php");
	    exit(); 
	} */
	//logime välja
	if(isset($_GET["logout"])){
		session_destroy();
		header("Location: sisselog.php");
	    exit();
	}
?>

<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<meta name="viewport" poll="width=device-width, initial-scale=1">
	<title>Koolieetika</title>
	<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
	<div class="navbar">
		<a href="?logout=1"><i class="fa fa-sign-out" style="background-color: transparent;"></i> Logi välja</a>
		<a href="paneel.html">Haldaja paneel</a>
	</div>

</body>
</html>