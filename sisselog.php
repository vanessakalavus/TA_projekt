<?php 
require_once "../../config2.php";
	require_once "Classes/SessionManager.class.php";
	SessionManager::sessionStart("koolieetika", 0, "~treimart/koolieetika_uuem/", "greeny.cs.tlu.ee");
	
	$server_host = "localhost";
	$server_user_name = "if22";
	$server_password = "if22pass";
	$database = "if22_koolieetika2";
	
	$login_error = null;
	if(isset($_POST["login_submit"])){
		$username = $_POST["email_input"]; 
		$password = $_POST["password_input"];
		$login_error = null;
		$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
		$conn->set_charset("utf8");
		$stmt = $conn->prepare("SELECT password FROM haldaja WHERE (username = ? OR email = ?) AND kustutatud IS NULL");
		echo $conn->error;
		$stmt->bind_param("ss", $username, $username);
		$stmt->bind_result($password_from_db);
		$stmt->execute();
		if($stmt->fetch()){
				//kasutaja on olemas, parool tuli ...
				if(password_verify($password, $password_from_db)){
					$stmt->close();
					$stmt = $conn->prepare("SELECT id, username, email FROM haldaja WHERE (username = ? OR email = ?) AND kustutatud IS NULL");
					echo $conn->error;
					$stmt->bind_param("ss", $username, $username);
					$stmt->bind_result($id_from_db, $username_from_db, $email_from_db);
					$stmt->execute();
					if($stmt->fetch()){
						//parool õige, oleme sees!
						//määran sessioonimuutujad
						$_SESSION["user_id"] = $id_from_db;
						$_SESSION["username"] = $username_from_db;
						$_SESSION["email"] = $email_from_db;
						$stmt->close();
						$conn->close();
						header("Location: paneel.php");
						exit();
					} else {
						$login_error = "Sisselogimisel tekkis tõrge!";
					}
				} else {
					$login_error = "Kasutajatunnus või salasõna oli vale!";
				}
			} else {
				$login_error = "Kasutajatunnus või salasõna oli vale!";
		}
		
		$stmt->close();
		$conn->close();
		
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Haldaja sisselogimine</title>
	<link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
	<div class="container">
		<div class="form-container">
			<h1>Haldaja sisselogimine</h1>
			<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
				<div class="form-group">
					<input type="text" name="email_input" placeholder="Kasutajatunnus või meiliaadress">
					<br>
				</div>
				<div class="form-group">
					<input type="password" name="password_input" placeholder="Salasõna">
					<br>
				</div>
				<div class="form-group">
					<input type="submit" name="login_submit" value="Logi sisse">
					<br> <br>
					<p><?php echo $login_error; ?></p>
				</div>
			</form>
		</div>
	</div>

</body>
</html>


<!-- require_once "../../config2.php" -->


