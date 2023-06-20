<?php

	require_once "../../config2.php";

	// Loon ühenduse andmebaasiga
		$conn = new mysqli($server_host, $server_user_name, $server_password, $database);
		$conn->set_charset("utf8");

	if (isset($_GET['mt'])) {
		$mt = $_GET['mt'];
		//prepare statement
		$stmt = $conn->prepare("SELECT tugimaterjal FROM enesetest WHERE id = ?");
		echo $stmt->error;
		echo $conn->error;

		$stmt->bind_param("i", $mt);
		$stmt->execute();

		$stmt->bind_result($material);

		//fetch data
		if ($stmt->fetch()) {
	        //echo $material;
	    }
	    $stmt->close();
	}
	
	$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Tugimaterjal</title>
	<link rel="stylesheet" type="text/css" href="css/newphp.css">
</head>
<body>
	<div class="main">
	<h1>Tugimaterjal enesetesti sooritamiseks</h1>
	<?php echo $material; ?>
	</div>

</body>
</html>