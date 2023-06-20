<?php

	require_once "../../config2.php";

	// Loon ühenduse andmebaasiga
		$conn = new mysqli($server_host, $server_user_name, $server_password, $database);
		$conn->set_charset("utf8");

	if (isset($_GET['jk'])) {
		$case = $_GET['case'];
		$jk = $_GET['jk'];
		//prepare statement
		$stmt = $conn->prepare("SELECT kysimus, vastus FROM vastusevali WHERE enesetest_id = ? AND jarjekord = ? AND kustutatud IS NULL");
		echo $stmt->error;
		echo $conn->error;

		$stmt->bind_param("ii", $case, $jk);
		$stmt->execute();

		$stmt->bind_result($ask, $anw);

		echo $ask;
		echo $anw;

		//fetch data
		if ($stmt->fetch()) {
	        echo $ask . ";" .$anw;
	    }
	    $stmt->close();
	}
	
	$conn->close();