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
	$notice = null;
	$section_title = null;
	$page_section = 0;
	$test_case = 0;
	$scenario = null;

	$question1 = null;
	$solution1 = null;

	// Loon ühenduse andmebaasiga
	$conn = new mysqli($server_host, $server_user_name, $server_password, $database);
	$conn->set_charset("utf8");

	// $page_section väärtuse saamine lehe URL-i kaudu
	if (isset($_GET['id'])) {
		$page_section = $_GET['id'];
	} else {
		die('Veebilehe URL pole tekstilõiguga seostatud<br>
			<a href="sisuhaldus.php">Tagasi sisuhalduslehele</a>');
	}

	//echo $page_section;

	// Kui andmebaasist leitakse kirje juhtumile, sisestatakse see vormi
	$stmt = $conn->prepare("SELECT id, juhtum, tugimaterjal FROM enesetest WHERE tekstiloik_id = ?");
	$stmt->bind_param("i", $page_section);
	$stmt->bind_result($id, $juht, $supp);
	$stmt->execute();
	if ($stmt->fetch()) {
		$test_case = $id;
		$scenario = $juht;
		$support = $supp;
	}
	$stmt->close();
	//echo $test_case;

	// Loeme kokku, mitu enesetesti küsimust sellel juhtumil kokku on
	$stmt = $conn->prepare("SELECT COUNT(jarjekord) FROM vastusevali WHERE enesetest_id = ? AND kustutatud IS NULL");
	echo $conn->error;
	$stmt->bind_param("i", $test_case);
	$stmt->execute();
	$stmt->bind_result($jarjekord_count);
	$stmt->fetch();
	$stmt->close();
	//echo $jarjekord_count;
	$x = $jarjekord_count - 1;

	// Leia küsimuse ja vastuse väli kõikidele eksisteerivatele
	for ($i=1; $i < $jarjekord_count+1 ; $i++) { 
		$stmt = $conn->prepare("SELECT kysimus, vastus FROM vastusevali WHERE enesetest_id = ? AND jarjekord = ?");
		$stmt->bind_param("ii", $test_case, $i);
		$stmt->bind_result($kysi, $lahe);
		$stmt->execute();
		$question_now = "question". $i;
		$solution_now = "solution". $i;
		if ($stmt->fetch()) {
			$$question_now = $kysi;
			$$solution_now = $lahe;
		}	
		$stmt->close();
	}

	//Pealkirja leidmine tekstiloigu tabelist
	$stmt = $conn->prepare("SELECT tekstiloik.pealkiri, enesetest.tekstiloik_id FROM enesetest RIGHT JOIN tekstiloik ON enesetest.tekstiloik_id = tekstiloik.id WHERE tekstiloik.id = ?");
	echo $conn->error;
	$stmt->bind_param("i", $page_section);
	$stmt->bind_result($lk_pealkiri, $lk_id);
	$stmt->execute();
	if ($stmt->fetch()) {
		$section_title = $lk_pealkiri;
	}
	$stmt->close();

	// Kontrolli, kas leht on avalikustatud
	$stmt = $conn->prepare("SELECT avalik FROM tekstiloik WHERE id = ?");
	echo $conn->error;
	$stmt->bind_param("i", $page_section);
	$stmt->execute();
	$result = $stmt->get_result();
	$row = $result->fetch_assoc();
	$public = $row['avalik'];
	//echo $public;
	if ($public === 1) {
		$state = "Peida";
	} else {
		$state = "Avalikusta";
	}

	//echo $state;

	// Vajaliku informatsiooni salvestamine andmebaasi peale nupu vajutust
	if (isset($_POST['enesetesti_salvestamine'])) {
		$scenario = $_POST["postituse_tutvustus"];
		$support = $_POST["myTextarea"];


		$notice = "Andmeid on uuendatud!";
		//echo $page_section;

		// Tekstilõigu pealkirja uuendamine
		$section_title = $_POST["postituse_pealkiri"];
		$stmt = $conn->prepare("UPDATE tekstiloik SET pealkiri = ? WHERE id = ?");
		echo $conn->error;
		$stmt->bind_param("si", $section_title, $page_section);
		//echo $section_title;
		//echo $page_section;
		$stmt->execute();
		$stmt->close();

		// Juhtumi kirjelduse ja abistava materjali lisamine või uuendamine andmebaasi
		$stmt = $conn->prepare("SELECT id FROM enesetest WHERE tekstiloik_id = ?");
		echo $conn->error;
		$stmt->bind_param("i", $page_section);
		//$stmt->bind_result($id);
		$stmt->execute();
		if ($stmt->fetch()) {
			//$page_section = $id;
			$stmt->close();
			$stmt = $conn->prepare("UPDATE enesetest SET juhtum = ? , tugimaterjal= ? WHERE tekstiloik_id = ?");
			echo $conn->error;
			$stmt->bind_param("ssi", $scenario, $support, $page_section);
			$stmt->execute();
			//echo "Proovin juhtumi kirjeldust UUENDADA";
		} else {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO enesetest (tekstiloik_id, juhtum, tugimaterjal) VALUES (?,?, ?)");
			$stmt->bind_param("iss", $page_section, $scenario, $support);
			//echo "Proovin juhtumi kirjeldust LISADA";
			if ($stmt->execute()) {
				//echo "Juhtumi kirjeldus ON lisatud";
			}
		}

		$order = 1;

		do {

			$current_post_q = "kysimus". $order;
			$current_post_s = "lahendus". $order;

			$current_question = $_POST[$current_post_q];
			$current_solution = $_POST[$current_post_s];

			//echo "See küsimus on: ". $current_question;

			if ($current_question != "") {
				$stmt = $conn->prepare("SELECT kysimus, vastus FROM vastusevali WHERE enesetest_id = ? AND jarjekord = ?");
				echo $conn->error;
				$stmt->bind_param("ii", $test_case, $order);
				$stmt->bind_result($kys, $lah);
				$stmt->execute();
				//echo "kontroll". $stmt->error;
				if ($stmt->fetch()) {
					$stmt->close();
					$stmt = $conn->prepare("UPDATE vastusevali SET kysimus = ?, vastus = ?, kustutatud = NULL WHERE enesetest_id = ? AND jarjekord = ?");
					echo $conn->error;
					$stmt->bind_param("ssii", $current_question, $current_solution, $test_case, $order);
					$stmt->execute();
					//echo $current_question;
					//echo "Proovin küsimust UUENDADA";
				} else {
					$stmt->close();
					$stmt = $conn->prepare("INSERT INTO vastusevali (enesetest_id, kysimus, vastus, jarjekord) values(?,?,?,?)");
					echo $conn->error;
					$stmt->bind_param("issi", $test_case, $current_question, $current_solution, $order);
					//echo "proovin küsimust lisada";
					if($stmt->execute()){
						//echo "Küsimus lisatud";
					}
				}
				//echo $order;
				$order += 1;
			} else {
				$stmt = $conn->prepare("SELECT id FROM vastusevali WHERE enesetest_id = ? AND jarjekord >= ?");
				echo $conn->error;
				$stmt->bind_param("ii", $test_case, $order);
				$stmt->bind_result($id_from_db);
				$stmt->execute();
				//echo "kysimus". $order. " peaks olema kustutatud";
				if ($stmt->fetch()) {
					$stmt->close();
					$stmt = $conn->prepare("UPDATE vastusevali SET kustutatud = CURRENT_TIMESTAMP WHERE enesetest_id = ? AND jarjekord >= ?");
					echo $conn->error;
					$stmt->bind_param("ii", $test_case, $order);
					$stmt->execute();
				}
			}
		} while ($current_question != "");
		$stmt->close();

		header("Refresh:0");
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
		$stmt->bind_param("ii", $new_public, $page_section);
		$stmt->execute();

		// lae leht uuesti, et avalikustamise muudatust näha oleks
		header("Refresh:0");
	}

	//echo $order-1;
	
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<input type="hidden" id="x" value="<?php echo $x; ?>">
	<script type="text/javascript" src="js/add_question.js" defer></script>
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
	<title>Enesetesti loomine</title>
</head>
<body>
<div class="navbar">
		<a href="?logout=1"><i class="fa fa-sign-out" style="background-color: transparent;"></i> Logi välja</a>
		<a href="paneel.php">Haldaja paneel</a>
	</div>
	<h1>Lisa uus enesetest</h1>
	<div class="main">	
		<!--<p><?php echo $notice;?></p>-->
		<form method="POST" action="<?php echo $_SERVER['PHP_SELF']."?id=". $page_section; ?>">
		<!--<label for="kategooria">Kategooria:</label>
		<select id="kategooria_valik" name="kategooria_valik">
			<?php echo $kategooria_html;?>
		</select><br>-->
		<label for="pealkiri">Lõigu pealkiri</label><br>
		<input type="text" id="postituse_pealkiri" name="postituse_pealkiri" placeholder="Anna lõigule unikaalne nimetus" value="<?php echo $section_title; ?>" maxlength="50"><br>
		<label for="tutvustus">Enesetesti stsenaariumi kirjeldus:</label><br>
		<textarea id="postituse_tutvustus" name="postituse_tutvustus" rows="10" maxlength="2000" placeholder="Kirjelda enesetesti juhtumit"><?php echo $scenario; ?></textarea><br>
		
		<label for="myTextarea">Abistav materjal</label><br>
		<textarea id="myTextarea" name="myTextarea" placeholder="Siia saab sisestada vihjeid/toetavat materjali küsimustele vastamiseks. Hüperlinkida/pilte lisada saab insert funktsiooni kaudu."><?php echo $support; ?></textarea><br>
		<br>
			<div id="myDiv">
				<input type="text" id="kysimus1" name="kysimus1" placeholder="küsimus" value="<?php echo $question1 ?>"><br>
				<input type="text" id="lahendus1" name="lahendus1" placeholder="lahendus (valikuline)" value="<?php echo $solution1; ?>">
			</div>
			<button type="button" id="question_add_btn" data-case="<?php echo $test_case;?>"><i class="fa fa-fw fa-plus"></i> Lisa küsimus</button>
			<button type="button" id="question_remove_btn"><i class="fa fa-fw fa-trash"></i> Eemalda</button><br>
			<br>
			<!--<label for="juhtmaterjal">Lisa juhendavat materjali (valikuline):</label>
			<input type="file" name="juhtmaterjal">
			<br>-->
			<button type="submit" id="enesetesti_avalikustamine" name="enesetesti_avalikustamine"><i class="fa fa-fw fa-send"></i> <?php echo $state;?></button>
			<button type="submit" id="enesetesti_salvestamine" name="enesetesti_salvestamine"><i class="fa fa-fw fa-check"></i> Salvesta</button>
			<button type="button" onclick="location.href = 'sisuhaldus.php'"><i class="fa fa-fw fa-backward"></i> Tagasi sisuhaldusesse </button>
		</form>
	</div>

</body>
</html>