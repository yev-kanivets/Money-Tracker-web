<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8">
		<title>Money Tacker</title>
	</head>

	<body>
		<?php
			include ("connect.php");
			
			try {
				$conn = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				echo "<p>Connected successfully and test git</p>";
			} catch(PDOException $erreur) {
				echo "<p>Erreur ".$erreur->getMessage()."</p>\n";
			}			
		?>
	</body>
</html>