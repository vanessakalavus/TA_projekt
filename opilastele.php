<?php
	require_once "../../config2.php";

	$category_id = 1;
	
	require_once "page_content.php";
	require_once "main_header.php";
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Õpetajatele</title>
	<link rel="stylesheet" type="text/css" href="css/newphp.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

</head>
<body>
	

	<h1>Õpilastele</h1>

	<div class="main">
		<?php echo $html; ?>
	</div>

	<script type="text/javascript" src="js/collapseable_button.js"></script>

</body>
</html>