<?php
    require_once "../../config2.php";
	require_once "Classes/SessionManager.class.php";
	SessionManager::sessionStart("koolieetika", 0, "~treimart/koolieetika_uuem/", "greeny.cs.tlu.ee");
	
	//eemaldada võimalus kustutada enda kontot!!!
	
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
    
    $conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
    
    if ($conn->connect_error) {
        die("Andmebaasiga ühendamisel tekkis viga: " . $conn->connect_error);
    }
    
    if (isset($_GET['delete'])) {
        $id = $_GET['delete'];
        
        $deleteQuery = "UPDATE haldaja SET kustutatud = now() WHERE id = ?";
        $stmt = $conn->prepare($deleteQuery);
        $stmt->bind_param("i", $id);
        echo $stmt->error;
        if ($stmt->execute()) {
            echo '<p>Kasutaja edukalt kustutatud.</p>';
        } else {
            echo '<p>Kasutaja kustutamisel tekkis viga! </p>';
        }
        
        $stmt->close();
    }
    
    $sql = "SELECT id, username FROM haldaja WHERE kustutatud IS NULL AND id <> " .$_SESSION["user_id"];
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) :
	$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
	<title>Kontod</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/kontod.css">
    <link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <div class="navbar">
		<a href="?logout=1"><i class="fa fa-sign-out" style="background-color: transparent;"></i> Logi välja</a>
		<a href="paneel.php">Haldaja paneel</a>
	</div>
    <h1>Hetkel on admin õigused järgmistel kasutajatel:</h1>
        <table>
            <tr>
                <th>Kasutajanimi</th>
                <th>Toiming</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row["username"]; ?></td>
                    <td>
                    <a class="delete-button" href="?delete=<?php echo $row["id"]; ?>" onclick="return confirm('Kas olete kindel, et soovite selle kasutaja kustutada?')">Kustuta</a>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else : ?>
        <p>Andmebaasis pole hetkel ühtegi kasutajanime.</p>
    <?php endif; ?>
<br>
<button type="button" style="display: block; margin: auto;" onclick="window.location.href = 'uuskasutaja.php'">Lisa uus kasutaja</button>
	<br>
	<!-- <button type="button" style="display: block; margin: auto;" onclick="window.location.href = 'paneel.php'">Tagasi paneelile</button> -->
</body>
</html>



