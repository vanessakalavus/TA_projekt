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
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["change_password_submit"])) {
    $username = $_POST["username"];
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];

    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn->set_charset("utf8");

    $stmt = $conn->prepare("SELECT id, password FROM haldaja WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($id, $hashedPassword);

    if ($stmt->fetch()) {
        if (password_verify($currentPassword, $hashedPassword)) {
            $stmt->close();

            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE haldaja SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $newHashedPassword, $id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $message = "Salasõna edukalt muudetud!";
                } else {
                    $message = "Salasõna muutmiseks ei leitud sobivat kasutajat.";
                }
            } else {
                $message = "Salasõna muutmisel tekkis viga.";
            }
        } else {
            $message = "Vale kasutajanimi või praegune salasõna.";
        }
    } else {
        $message = "Vale kasutajanimi või praegune salasõna.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/paroolimuut.css">
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Haldaja parooli muutmine</title>
</head>
<body>
    <div class="navbar">
		<a href="?logout=1"><i class="fa fa-sign-out" style="background-color: transparent;"></i> Logi välja</a>
		<a href="paneel.php">Haldaja paneel</a>
	</div>
    <div class="form-container">
		<div class="inner-form-container">
        <form id="passwordForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <h1 class="form-title">Parooli muutmine</h1>
            <div class="form-group">
                <label for="username">Kasutajanimi:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="current_password">Vana salasõna:</label>
                <input type="password" name="current_password" id="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Uus salasõna:</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
			
            <div class="form-group">
                <input type="submit" name="change_password_submit" value="Muuda salasõna">
				
            </div>
			<span><?php echo $message; ?></span>
            
        </form>
		</div>
    </div>
</body>
</html>



