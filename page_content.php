<?php
	$html = null;
	$order = 0;

	// Loon ühenduse andmebaasiga
	$conn = new mysqli($server_host, $server_user_name, $server_password, $database);
	$conn->set_charset("utf8");

	// Loen kokku, mitu tekstilõiku kategooria alla kuulub
	$stmt = $conn->prepare("SELECT COUNT(jarjestus) FROM tekstiloik WHERE kategooria_id = ? AND kustutatud IS NULL AND avalik = 1");
	echo $conn->error;
	$stmt->bind_param("i", $category_id);
	$stmt->execute();
	$result = $stmt->get_result();
	while ($row = $result->fetch_assoc()) {
		$amount_from_db = $row['COUNT(jarjestus)'];
	}
	$stmt->execute();

	// Kontrolli iga tekstilõigu jaoks, kas see on tekstimaterjal või enesetest
	for ($i=0; $i < $amount_from_db; $i++) { 
		$order += 1;
		$stmt->close();
		$stmt = $conn->prepare("SELECT id, pealkiri, liik FROM tekstiloik WHERE kategooria_id = ? AND jarjestus = ? AND avalik = 1 AND kustutatud IS NULL");
		echo $conn->error;
		$stmt->bind_param("ii", $category_id, $order);
		$stmt->bind_result($id_from_db, $title_from_db, $liik_from_db);
		$stmt->execute();
		$title = $title_from_db;
		if ($stmt->fetch()) {
			if ($liik_from_db == "E") {
				// Kui on enesetest, loon html-i nupu, mille sees on juhtumi kirjeldus
				$stmt->close();
				$stmt = $conn->prepare("SELECT enesetest.id, enesetest.juhtum, enesetest.tugimaterjal FROM enesetest JOIN tekstiloik ON tekstiloik.id = enesetest.tekstiloik_id WHERE jarjestus = ? AND kategooria_id = ?");
				echo $conn->error;
				$stmt->bind_param("ii", $order, $category_id);
				$stmt->bind_result($eid_from_db ,$juhtum_from_db, $materjal_from_db);
				$stmt->execute();
				//$juhtum_from_db = base64_encode($stmt->get_result()->fetch_assoc()['juhtum']);
				$stmt->fetch();
				$juhtum_from_db = base64_encode($juhtum_from_db);
				$eid = $eid_from_db;
				$materjal = $materjal_from_db;
				//echo "Materjal: ". $materjal_from_db;

				$html.= '<button type="button" class="collapsible" style="background-color: #86C2EB; color: white; border: none;">Lahenda enesetest: '. $title_from_db. '</button>
			<div class="poll" id="poll" style="background-color: #ADDEEE; border: none;">
			<h3>Juhtumi kirjeldus</h3><div id="scenario">';
				$html.= base64_decode($juhtum_from_db);

				// Kuvan sisse kõik testküsimused
				$stmt->close();
				$stmt = $conn->prepare("SELECT kysimus FROM vastusevali WHERE enesetest_id = ? AND kustutatud IS NULL ORDER BY jarjekord");
				$stmt->bind_param("i", $eid);
				$stmt->bind_result($question_from_db);
				$stmt->execute();
				$html.= '</div><h3>Testküsimused</h3>';
				while ($stmt->fetch()) {
					//echo $question_from_db. "\n";
					$html.= "<label for='question'>". $question_from_db. "</label><br>
					<textarea id='question_field' class='question_field' name='question_field' placeholder='Teie vastus'></textarea><br>";
				}

				$html.= "<p id='result'>Kas oled küsimustele ära vastanud? Nüüd loe juurde ka <a href='tugimaterjal.php?mt=". $eid. "' target='blank'>juhendavat materjali</a> ja mõtle, kas soovid mõnda oma vastustest veel muuta!</p>
				<button type='button' id='save_anwser_btn' name='save_anwser_btn'>Lõpeta vastamine</button>";
				$html.= "</div>";
			} else {
				// Kui on tekstmaterjal, lisan html-i andmebaasist leitud teksti
				$stmt->close();
				$stmt = $conn->prepare("SELECT materjal.tekst FROM materjal JOIN tekstiloik ON tekstiloik.id = materjal.tekstiloik_id WHERE jarjestus = ? AND tekstiloik.kategooria_id = ?");
				$stmt->bind_param("ii", $order, $category_id);
				$stmt->bind_result($tekst_from_db);
				$stmt->execute();
				$stmt->fetch();
				$text = base64_encode($tekst_from_db);
				$html.= base64_decode($text);
			}
		}
	}