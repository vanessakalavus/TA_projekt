<?php
require_once "../../config2.php";
require_once "Classes/SessionManager.class.php";
	SessionManager::sessionStart("koolieetika", 0, "~treimart/koolieetika_uuem/", "greeny.cs.tlu.ee");
	
	if(!isset($_SESSION["user_id"])){
	    header("Location: sisselog.php");
	    exit(); 
	}
	//logime välja
	if(isset($_GET["logout"])){
		session_destroy();
		header("Location: sisselog.php");
	    exit();
	}
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
//lisada emaili kontroll!
function sign_up($username,$email, $password){
    $notice = 0;
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    $conn->set_charset("utf8");
    $stmt = $conn->prepare("SELECT id FROM haldaja WHERE kustutatud IS NULL AND (username = ? OR email = ?)");
    echo $conn->error;
    $stmt->bind_param("ss", $username, $email);
    $stmt->bind_result($id_from_db);
    $stmt->execute();
    if($stmt->fetch()){
        $notice = 2;
    } else {
        $stmt->close();
        $stmt = $conn->prepare("INSERT INTO haldaja (username, email, password) VALUES(?,?,?)");
        echo $conn->error;
        //krüpteerime salasõna
        $pwd_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sss", $username, $email, $pwd_hash);
        if($stmt->execute()){
            $notice = 1;
            // echo $stmt->error;
        } else {
            $notice = 3;
        }

    }
    //echo $stmt->error;
    $stmt->close();
    $conn->close();
    return $notice;
}


$notice = null;
    $username = null;
    $email = null;
    $password = null;

    //muutujad võimalike veateadetega
    $username_error = null;
    $email_error = null;
    $password_error = null;

    if($_SERVER["REQUEST_METHOD"] === "POST"){
		if(isset($_POST["user_data_submit"])){
			
			if(isset($_POST["username_input"]) and !empty($_POST["username_input"])){
                $username = test_input($_POST["username_input"]);
                if($username != $_POST["username_input"]){
                    $username_error = "Kontrolli kasutajanime!";
                }   
            } else {
                $username_error = "Palun sisesta kasutajanimi!";
            }
			
			if(isset($_POST["email_input"]) and !empty($_POST["email_input"])){
                $email = test_input($_POST["email_input"]);
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                    $email_error = "Palun kontrolli oma meiliaadressi!";
                }
            } else {
                $email_error = "Palun sisesta oma meiliaadress!";
            }
            
            if(isset($_POST["password_input"]) and !empty($_POST["password_input"])){
                $password = test_input($_POST["password_input"]);
                if(strlen($password) < 8){
                    $password_error = "Sisestatud salasõna on liiga lühike!";
                }
            } else {
                $password_error = "Palun sisesta oma salasõna!";
            }
            //kui kõik kombes, salvestame uue kasutaja
            if(empty($username_error) and empty($email_error) and empty($password_error)){
				
                //salvestame andmetabelisse
                $notice = sign_up($username, $email, $_POST["password_input"]);
				if($notice == 1){
					$notice = "Uus kasutaja edukalt loodud!";
					$username = null;
					$email = null;
				} else {
					if($notice == 2){
						$notice = "Selline kasutaja on juba olemas!";
						$username = null;
						$email = null;
					} else {
						$notice = "Uue kasutaja loomisel tekkis tõrge!";
					}
				}
			}
		}//if submit lõppeb
	}//if POST lõppeb		
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	 <title>Uue kasutaja lisamine</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/uuskasutaja.css">
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
</head>  
<body>
    <div class="navbar">
		<a href="?logout=1"><i class="fa fa-sign-out" style="background-color: transparent;"></i> Logi välja</a>
		<a href="paneel.php">Haldaja paneel      </a>
	</div>
    <div class="container">
        <div class="form-container">
            <h1 class="form-title">Uue kasutaja lisamine</h1>
            <form id="userForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="form-group">
                    <label for="username">Kasutajanimi:</label>
                    <input name="username_input" id="username_input" type="text"  value="<?php echo $username_error; ?>"><span><?php echo $username_error; ?></span><br>
                </div>
                <div class="form-group">
					<label for="email_input">E-mail:</label>
					<input type="email" name="email_input" id="email_input" value="<?php echo $email; ?>"><span><?php echo $email_error; ?></span><br>
                </div>
                <div class="form-group">
                    <label for="password_input">Salasõna (min 8 tähemärki):</label>
                    <div class="password">
						<input name="password_input" id="password_input" type="password"><span><?php echo $password_error; ?></span>
                        <br>
                        <br>
                        <label for="showPassword">Näita salasõna</label>
                        <input type="checkbox"  id="showPassword" onchange="togglePasswordVisibility()">
                    </div>
                </div>
                <input name="user_data_submit" type="submit" value="Loo kasutaja">
	            <span><?php echo $notice; ?></span>
                <div>
                    <br>
                <!-- <a href="paneel.php" class="back-button">Tagasi haldaja paneelile</a> -->
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password_input");
            var showPasswordCheckbox = document.getElementById("showPassword");

            if (showPasswordCheckbox.checked) {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
    </script>    

</body>
</html>


<!-- Javascript samas failis -->

<!-- <script>
        function togglePasswordVisibility() {
            var passwordInput = document.getElementById("password_input");
            var showPasswordCheckbox = document.getElementById("showPassword");
            
            if (showPasswordCheckbox.checked) {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }
    </script> -->

