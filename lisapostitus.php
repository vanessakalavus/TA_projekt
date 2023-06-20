<?php
require_once "../../config2.php";
require_once "Classes/SessionManager.class.php";
	SessionManager::sessionStart("koolieetika", 0, "~treimart/koolieetika_uuem/", "greeny.cs.tlu.ee");

	if(!isset($_SESSION["user_id"])){
	    header("Location: sisselog.php");
	    exit(); 
	}
	$user_id=$_SESSION["user_id"];
	//logime välja
	if(isset($_GET["logout"])){
		session_destroy();
		header("Location: sisselog.php");
	    exit();
	}

//ajutised väärtusehoidjad testimiseks

$section_heading=null;
$material=null;


// $page_section väärtuse saamine
if (isset($_GET['id'])) {
	$txt_section_id = $_GET['id'];
}else {
	die('Veebilehe URL pole pealkirjaga seostatud<br>
		<a href="sisuhaldus.php">Tagasi sisuhalduslehele</a>');
}

	//DB ühendus
$conn = new mysqli($server_host, $server_user_name, $server_password, $database);
$conn->set_charset("utf8");

// Kui andmebaasist leitakse kirjeid, sisestatakse need vormi
	$stmt = $conn->prepare("SELECT id, tekst FROM materjal WHERE tekstiloik_id = ?");
	$stmt->bind_param("i", $txt_section_id);
	$stmt->bind_result($material_id_DB, $material_DB);
	$stmt->execute();
	if ($stmt->fetch()) {
		$material_id = $material_id_DB;
		$material = $material_DB;
	}
	$stmt->close();


//Pealkirja leidmine tekstiloigu tabelist
	$stmt = $conn->prepare("SELECT tekstiloik.pealkiri, materjal.tekstiloik_id FROM materjal 
	RIGHT JOIN tekstiloik ON materjal.tekstiloik_id = tekstiloik.id WHERE tekstiloik.id = ?");
	echo $conn->error;
	$stmt->bind_param("i", $txt_section_id);
	$stmt->bind_result($section_heading_DB, $page_id_DB);
	$stmt->execute();
	if ($stmt->fetch()) {
		$section_heading = $section_heading_DB;
	}
	$stmt->close();

	// Kontrolli, kas leht on avalikustatud
	$stmt = $conn->prepare("SELECT avalik FROM tekstiloik WHERE id = ?");
	echo $conn->error;
	$stmt->bind_param("i", $txt_section_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$row=null;
	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
	}
	
	$public = $row['avalik'] ?? 0;
	
	if ($public === 1) {
		$state = "Peida";
	} else {
		$state = "Avalikusta";
	}
	$stmt->close();

// Vajaliku informatsiooni form2_submitmine andmebaasi peale nupu vajutust
if (isset($_POST['form2_submit'])) {
	$material = $_POST["myTextarea"];

	// Tekstilõigu pealkirja uuendamine
	$section_heading = $_POST["post_heading"];
	$stmt = $conn->prepare("UPDATE tekstiloik SET pealkiri = ? WHERE id = ?");
	echo $conn->error;
	$stmt->bind_param("si", $section_heading, $txt_section_id);
	$stmt->execute();
	$stmt->close();

	//materjali sisu lisamine/uuendamine andmebaasi
	$stmt = $conn->prepare("SELECT id FROM materjal WHERE tekstiloik_id = ?");
		echo $conn->error;
		$stmt->bind_param("i", $txt_section_id);
		$stmt->execute();
		if ($stmt->fetch()) {
			$stmt->close();
			$stmt = $conn->prepare("UPDATE materjal SET tekst = ? WHERE tekstiloik_id = ?");
			echo $conn->error;
			$stmt->bind_param("si", $material, $txt_section_id);
			$stmt->execute();
		} else {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO materjal (tekstiloik_id, tekst) VALUES (?,?)");
			$stmt->bind_param("is", $txt_section_id, $material);
			//echo "Proovin juhtumi kirjeldust LISADA";
			if ($stmt->execute()) {
				//echo "Juhtumi kirjeldus ON lisatud";
			}
		}

		// Refresh igaks juhuks, et muudatusi kindlasti näha oleks.
		header("Refresh:0");
		$stmt->close();

	}

	// Enesetesti avalikustamine/peitmine
	if (isset($_POST['enesetesti_avalikustamine'])) {
		if ($public === 0) {
			$new_public = 1;
		} else {
			$new_public = 0;
		}
		$stmt = $conn->prepare("UPDATE tekstiloik SET avalik = ? WHERE id = ?");
		echo $conn->error;
		$stmt->bind_param("ii", $new_public, $txt_section_id);
		$stmt->execute();

		// lae leht uuesti, et avalikustamise muudatust näha oleks
		header("Refresh:0");
	}

//DB ühenduse sulgemine
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
	<div class="navbar">
		<a href="?logout=1"><i class="fa fa-sign-out" style="background-color: transparent;"></i> Logi välja</a>
		<a href="paneel.php">Haldaja paneel</a>
	</div>
	
	<title>Loo postitus</title>
	<link rel="stylesheet" type="text/css" href="css/addpost.css">
	<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script
    type="text/javascript"
    src='https://cdn.tiny.cloud/1/igoy30690a59botlnd8slkdqzsyezp52ircuq3856iai7jkj/tinymce/6/tinymce.min.js'
    referrerpolicy="origin">
  </script>
  <script type="text/javascript">
  tinymce.init({
    selector: '#myTextarea',
    width: 600,
    height: 300,
    plugins: [
      'advlist', 'autolink', 'link', 'image', 'lists', 'charmap', 'preview', 'anchor', 'pagebreak',
      'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'insertdatetime',
      'media', 'table', 'template', 'help'
    ],
    toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | ' +
      'bullist numlist outdent indent | link image | print preview media fullscreen | ',
    menu: {
      favs: { title: 'My Favorites', items: 'code visualaid | searchreplace' }
    },
    menubar: 'favs file edit view insert tools table help',
    content_css: 'css/content.css'
  });
  </script>

</head>
<body>

	<!--test-->

	<h1> Materjali lisamise/muutmise vorm </h1>
	<div class="container">		
		<section id="vasakpool">

			<form id="myForm2" name="myForm2" method="POST" action="<?php echo $_SERVER['PHP_SELF']."?id=". $txt_section_id; ?>">
			<label for="post_heading">Postituse pealkiri:</label>
			<input type="text" name="post_heading" placeholder="Lõigu nimetus" value="<?php echo $section_heading; ?>" maxlength="50"><br>
			
			<label for="myTextarea">Postituse sisu:</label><br>
			<textarea id="myTextarea" name="myTextarea" placeholder="Siia tuleb postituse sisu."><?php echo $material; ?></textarea><br>
			
			<!-- <label for="avalikusta">Avalikusta:</label>
			<input id="avalikusta" type="checkbox" value="1" name="avalikusta" /><br> -->
			
			<button type="submit" name="form2_submit" value="form2_submit">Salvesta</button>
			<button type="submit" id="enesetesti_avalikustamine" name="enesetesti_avalikustamine"><?php echo $state;?></button><br>
			<button type="button" onclick="location.href = 'sisuhaldus.php'"><i class="fa fa-fw fa-backward"></i> Tagasi sisuhaldusesse</button>
			</form>
		</section>
		
	</div>

</body>
</html>