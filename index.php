<?php
   ob_start();
   session_start();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Money Tacker</title>
	</head>

	<body>
		<div class = "container">
         <?php
            if (isset($_SESSION['user_id'])) {
				echo "<p>Welcome, ".$_SESSION['username']." (".$_SESSION['user_id'].")!</p>";
			} else {
				header('Location: '.'sign_in.php', true, $permanent ? 301 : 302);
				exit();
			}
         ?>
      	</div>
      
      	<div class = "container">
         	<a href="logout.php">Logout</a>
      	</div> 
	</body>
</html>
