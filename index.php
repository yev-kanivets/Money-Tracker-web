<?php
   ob_start();
   session_start();
?>

<html>
	<head>
		<meta charset="utf-8">
		<title>Money Tacker</title>
	</head>

	<body>
		<div class = "container">
         <?php
            if (isset($_SESSION['user_id'])) {
				echo "<p>Welcome, ".$_SESSION['username']."!</p>";
			} else {
				include "sign_in.php";
				exit();
			}
         ?>
      	</div>
      
      	<div class = "container">
         	<a href="logout.php">Logout</a>
      	</div> 
	</body>
</html>
