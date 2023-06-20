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
    /*valitud kategooria*/
	$category = 1;
	if(isset($_POST["category"])){
		$category = $_POST["category"];
	}
	/*echo $category;*/
	
	//DB ühenduse loomine
	$conn = new mysqli($GLOBALS["server_host"], $GLOBALS["server_user_name"], $GLOBALS["server_password"], $GLOBALS["database"]);
	$conn->set_charset("utf8");	

	//max tekstiloigu id päring
	$stmt=$conn->prepare("SELECT MAX(id) FROM tekstiloik");
	$stmt->execute();
	$stmt->bind_result($max_id_DB);
	$stmt->fetch();
	$stmt->close();
	$new_tekstiloik_id=$max_id_DB+1;

	//kategooriate küsimise andmebaasist
	$selected_category = isset($_POST["category"]) ? $_POST["category"] : $category;
	$query2 = $conn->prepare("SELECT id, nimetus FROM kategooria");
	echo $conn->error;
	$query2->bind_result($category_id_DB,$category_name_DB);
	$query2->execute();
	echo $query2->error;
	$category_html = null;
	while ($query2->fetch()) {
		$category_html .= '<option value="'.$category_id_DB .'"';
		if ($category_id_DB == $selected_category) {
		   $category_html .= ' selected';
		}
		$category_html .= '>';
		$category_html .= $category_name_DB;
		$category_html .= "</option> \n";
	 }
	$query2->close();

	
	//valitud kategooria materjalide päring
	$max_stmt = $conn->prepare("SELECT max(jarjestus) FROM tekstiloik WHERE kategooria_id = ? AND kustutatud IS NULL");
	$max_stmt->bind_param("i", $selected_category);
	$max_stmt->bind_result($max_order);
	$max_stmt->execute();
	$max_stmt->fetch();
	$max_stmt->close();
	$stmt = $conn->prepare("SELECT id, pealkiri, jarjestus, avalik, liik  FROM tekstiloik WHERE kategooria_id = ? AND kustutatud IS NULL ORDER BY jarjestus ASC");
	echo $conn->error;
	$stmt->bind_param("i", $selected_category);
	$stmt->bind_result($id_from_db, $title, $order, $status, $type);
	$stmt->execute();
	$html = null;
	while($stmt->fetch()){
		if($type=="M"){
			$ref_link="lisapostitus.php?id=".$id_from_db;
			$what_content="[Materjal]";
		}else{
			$ref_link="lisa_enesetest.php?id=".$id_from_db;
			$what_content="[Enesetest]";
		}
		if(is_null($title)){
			$title='<p> [pealkiri puudub]	';
		}
		$html .= '<p><b>' .$title. '</b>' ;
		$html .= ' <b>||</b> Sisu tüüp: <em>'.$what_content .'</em>';
		$html .= ' <b>||</b> Seisund: ';
		if ($status == 1) {
			$html .= '<em>[Avalik]</em></p>';
		} else {
			$html .= '<em>[Peidetud]</em></p>';
		}
		$html .= '<div class="sisu">';
		$html .= '<br>';
		$html .= '<a href="' . $ref_link . '" class="button" id="changeBtn">Muuda</a>';

		$html .= '<form method="POST" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '">';
		$html .= '<input type="hidden" name="category" value="' . $selected_category . '">';
		$html .= '<input type="submit" name="deleteBtn[' .$id_from_db .']" id="deleteBtn' .$id_from_db .'" value="Kustuta">';
		$html .= '</form>';

		$html .= '<form method="POST" action="'. htmlspecialchars($_SERVER["PHP_SELF"]) .'">';
		$html .= '<input type="hidden" name="category" value="' .$selected_category .'">';
		$html .= '<input type="hidden" name="id['.$id_from_db.']" value="' .$id_from_db .'">';
		$html .= '<label for="orderValue">Järjestus:</label>';
		$html .= '<input type="number" min="1" max="100" name="orderValue[' .$id_from_db .']" value="'. $order.'">';
		$html .= '<input type="submit" name="updateBtn[' .$id_from_db .']" value="Uuenda">';
		$html .= '</form>';

		$html .= '</div>';
		$html .= '<hr>';
	}
	echo $stmt->error;
	$stmt->close();
	
	// jarjekorra muutmine
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		if (isset($_POST["updateBtn"])) {
		  foreach ($_POST["updateBtn"] as $id_from_db => $value) {
			$id = $_POST["id"][$id_from_db];
			$orderValue = $_POST["orderValue"][$id_from_db];
	  
			// Update the value in the database
			$update_stmt = $conn->prepare("UPDATE tekstiloik SET jarjestus = ? WHERE id = ?");
			$update_stmt->bind_param("ii", $orderValue, $id);
			$update_stmt->execute();
	  
			// Check if the update was successful
			if ($update_stmt->affected_rows > 0) {
			//   echo "Järjestus muudetud: $id.";
			} else {
			  // echo "Järjestust ei õnnestunud muuta: $id.";
			}
	  
			$update_stmt->close();
		  }
			echo "<meta http-equiv='refresh' content='0'>";
		}
	  }

	//kustuta nupp
	if (isset($_POST['deleteBtn'])) {
		foreach ($_POST['deleteBtn'] as $id => $value) {
        $stmt = $conn->prepare("UPDATE tekstiloik SET kustutatud = now() WHERE id = ?");
        echo $conn->error;
        echo $stmt->error;
        $stmt->bind_param("i", $id);

        if ($stmt->execute() === false) {
            $entry_error = "Midagi läks valesti: " . $stmt->error;
        } else {
            $affected_rows = $stmt->affected_rows;
            if ($affected_rows > 0) {
              //  $entry_error = "Kirje on kustutatud!";
			  echo "<meta http-equiv='refresh' content='0'>";
            } else {
              //  $entry_error = "Kustutamine ebaõnnestus: Kirjet ID-ga $id ei leitud.";
            }
        }
        $stmt->close();
		}
	}
	//lisa enesetest/lisa materjal muutujad ning andmebaasi sisestused
	$max_order_nr=$max_order+1;


//testi lisamise insert tekstiloiku
	if (isset($_POST["submit_test"])){
	$testi_liik=$_POST["testi_liik"];
	$stmt = $conn->prepare("INSERT INTO tekstiloik(kategooria_id,haldaja_id,jarjestus,liik) VALUES (?,?,?,?)");
	$stmt->bind_param("iiis", $_POST["selected_category"], $user_id, $_POST["max_order_nr"], $_POST["testi_liik"]);
	echo $conn->error;
	if ($stmt->execute()) {
		echo "Enesetest ON lisatud";
	}
	$stmt->close();
	header("Location: lisa_enesetest.php?id=".$new_tekstiloik_id);
        exit();
}
//materjali lisamise nupu insert tekstiloiku
if (isset($_POST["submit_txt"])){
	$stmt = $conn->prepare("INSERT INTO tekstiloik(kategooria_id,haldaja_id,jarjestus,liik) VALUES (?,?,?,?)");
	$stmt->bind_param("iiis", $_POST["selected_category"], $user_id, $_POST["max_order_nr"], $_POST["teksti_liik"]);
	if ($stmt->execute()) {
		echo "Materjal ON lisatud";
	}
	$stmt->close();
	header("Location: lisapostitus.php?id=".$new_tekstiloik_id);
        exit();
}

	$conn->close();	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<meta name="viewport" poll="width=device-width, initial-scale=1">
	<title>Sisuhaldus</title>
	<link rel="stylesheet" type="text/css" href="css/sisuhaldus.css">
	<link rel="stylesheet"href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
	<div class="navbar">
		<a href="?logout=1"><i class="fa fa-sign-out" style="background-color: transparent;"></i> Logi välja</a>
		<a href="paneel.php">Haldaja paneel</a>
	</div>
	<main>
	<div class="content-container">
		<h1>Sisuhaldus</h1>
		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
			<label for="category"style="font-weight: 500; font-size: 18px;">Kategooria: </label> 
				<select name="category" id="category" onchange="this.form.submit();"> 
					<?php echo $category_html;?> </select>
				</select>
		</form>
		<hr>
		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
			<?php echo $html; ?>
		</form>

		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
			<input type="hidden" name="testi_liik" value="E">
			<input type="hidden" name="selected_category" value="<?php echo $selected_category; ?>">
			<input type="hidden" name="max_order_nr" value="<?php echo $max_order_nr; ?>">
			<input type="submit" name="submit_test" id="newTestBtn" value="Lisa enesetest">
		
		</form>
		<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data">
			<input type="hidden" name="teksti_liik" value="M">
			<input type="hidden" name="selected_category" value="<?php echo $selected_category; ?>">
			<input type="hidden" name="max_order_nr" value="<?php echo $max_order_nr; ?>">
			<input type="submit" name="submit_txt" id="newTextBtn" value="Lisa materjal">
		</form> 
	</div>
	</main>
</body>
</html>
<style>
  .sisu {
    display: flex;
    align-items: flex-start;;
  }

  .sisu form {
    margin-right: 10px;
  }

  .sisu .button {
    margin-right: 10px;
  }
</style>