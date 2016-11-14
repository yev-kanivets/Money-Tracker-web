<?php
   ob_start();
   session_start();

   include('connect.php');

   function deleteRecord($conn, $record_id) {
   		$sql = "DELETE FROM records
   				WHERE id=".$record_id.";";
   		if ($conn->exec($sql)) {
   			header('Location: '.'index.php', true, $permanent ? 301 : 302);
   		} else {
   			header('Location: '.'error.php?error='.'Failed to delete a record', true, $permanent ? 301 : 302);
   		}
   		exit();
   }

   if (isset($_SESSION['user_id'])) {
		try {
			$conn = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			deleteRecord($conn, $_GET['record_id']);
		} catch(PDOException $error) {
			echo "<p>Error: ".$error->getMessage()."</p>\n";
		}
	} else {
		header('Location: '.'sign_in.php', true, $permanent ? 301 : 302);
		exit();
	}
?>
