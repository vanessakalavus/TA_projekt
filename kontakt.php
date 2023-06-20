<?php
require_once "../../config2.php";
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
	
	function test_input($data) {
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	$entry_error = null;
	$email_error = null;
	if(isset($_POST["change_button"])){
		if(isset($_POST["email"]) and !empty($_POST["email"]) and !empty($_POST["username"])){
                $email = test_input($_POST["email"]);
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $email_error = "Palun kontrolli oma meiliaadressi!";
                }
            } else {
                $entry_error = "Palun täida kõik väljad!";
            }
		if(empty($email_error)) {
			$username = $_POST["username"];
			$email = $_POST["email"];
			$entry_error = null;
			$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
			//kõigepealt kontrollime, et sellise kasutajanime või meiliga ei eksisteeriks juba kasutaja
			$conn->set_charset("utf8");
			$stmt = $conn->prepare("SELECT * FROM haldaja WHERE kustutatud IS NULL AND username = ? AND id <> ?");
			echo $conn->error;
			echo $stmt->error;
			$stmt->bind_param("si", $username, $_SESSION["user_id"]);
			$stmt->execute();
			$result = $stmt->get_result();
			if($result->num_rows > 0) {
				$entry_error = "Sellise kasutajanimega kasutaja juba eksisteerib!";
				$stmt->close();
			} else {
				$stmt = $conn->prepare("SELECT * FROM haldaja WHERE kustutatud IS NULL AND email = ? AND id <> ?");
				echo $conn->error;
				echo $stmt->error;
				$stmt->bind_param("si", $email, $_SESSION["user_id"]);
				$stmt->execute();
				$result = $stmt->get_result();
				if($result->num_rows > 0 ) {
				$entry_error = "Sellise meiliaadressiga kasutaja juba eksisteerib!";
				$stmt->close();
				} else {
					//kõik korras, viime muudatused sisse
					$stmt = $conn->prepare("UPDATE haldaja SET username = ?, email = ? WHERE id = ?");
					echo $conn->error;
					echo $stmt->error;
					$stmt->bind_param("ssi", $_POST["username"], $_POST["email"], $_SESSION["user_id"]);
					if($stmt->execute() == false){
						$entry_error = "Midagi läks valesti!";
					} else {
						$entry_error = "Kontaktandmed on muudetud!";
						$_SESSION["username"] = $_POST["username"];
						$_SESSION["email"] = $_POST["email"];
					}
					$stmt->close();
					$conn->close();
				}
			} 
		} 
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Muuda kontaktandmeid</title>
	<link rel="stylesheet" type="text/css" href="css/kontakt.css">
	<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
	<div class="navbar">
		<a href="?logout=1"><i class="fa fa-sign-out" style="background-color: transparent;"></i> Logi välja</a>
		<a href="paneel.php">Haldaja paneel</a>
	</div>
	<div class="container">
	<h1>Muuda kontaktandmeid</h1>
		<div class="form-container">
			<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
				<div class="form-group">
					<label for="kasutajanimi">Kasutajanimi:</label><br>
					<input type="text" name="username" id="username" value="<?php echo $_SESSION["username"]; ?>"><br>
				</div>
				<div class="form-group">
					<label for="email">Meiliaadress:</label><br>
					<input type="text" name="email" id="email" value="<?php echo $_SESSION["email"]; ?>"><br>
				</div>
				<div class="form-group">
					<input type="submit" name="change_button" id="change_button" value="Salvesta muudatused">
					<!--<a href="paneel.php"><button type="button">Tagasi</button></a>-->
					<p id="message"> <?php echo $entry_error; echo $email_error;?></p>
				</div>
			</form>
		</div>	
	</div>
</body>
</html>