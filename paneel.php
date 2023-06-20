<?php
require_once "Classes/SessionManager.class.php";
	SessionManager::sessionStart("koolieetika", 0, "~treimart/koolieetika_uuem/", "greeny.cs.tlu.ee");
	if(isset($_GET["logout"])){
		session_destroy();
		header("Location: sisselog.php");
		exit();
	}
	if(!isset($_SESSION["user_id"])){
	    header("Location: sisselog.php");
	    exit(); 
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Haldaja paneel</title>
	<link rel="stylesheet" type="text/css" href="css/paneel.css">
	<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
	<div class="log-out">
		<a href="?logout=1"><i class="fa fa-sign-out" style="background-color: transparent;"></i> Logi välja</a>
	</div>
	<div class="paneel">
		<h1>Tere tulemast, haldaja!</h2>
		<h2>Koolieetika veebilehe sisuhalduspaneel</h2>
		<div class="sisu">
			<section id="parempool">
				<p>Kontohaldus:</p>
				<a href="kontakt.php"><button type="button">Muuda kontaktandmeid</button></a><br>
				<br>
				<a href="paroolimuut.php"><button type="button">Muuda parooli</button></a><br>
				<br>
				<a href="kontod.php"><button type="button">Halda kontosid</button></a><br>
			</section>

			<section id="vasakpool">
				<p>Lisa või muuda postitusi: </p>
				<a href="sisuhaldus.php"><button type="button">Sisu muutmine</button></a><br>
				<br>
			</section>
		</div>
	</div>
</body>
</html>
