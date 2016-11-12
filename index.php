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
				header('Location: '.'sign_in.php', true, $permanent ? 301 : 302);
				exit();
			}
         ?>
      	</div>
      
      	<div class = "container">
			<a href="add_record.php?type=income">Add income</a>
			<a href="add_record.php?type=expense">Add expense</a>
         	<a href="logout.php">Logout</a>
      	</div> 
		
		<div class = "container">
			<table>
				<caption>Records</caption>
				<tr>
					<th>Time</th>
					<th>Title</th>
					<th>Category</th>
					<th>Price</th>
				</tr>
				<?php
					include ("connect.php");
					
					function editUrl($record) {
						return '<a href="">Edit</a>';
					}
					
					function deleteUrl($record) {
						return '<a href="">Delete</a>';
					}
					
					function displayRecords($conn) {
						$sql = 'SELECT time, records.title AS title, categories.title AS category, price 
								FROM records, categories
								WHERE user_id='.$_SESSION['user_id'].'
								AND category_id=categories.id;';
						foreach ($conn->query($sql) as $row) {
							echo '<tr>
									<td>'.$row['time'].'</td>
									<td>'.$row['title'].'</td>
									<td>'.$row['category'].'</td>
									<td>'.$row['price'].'</td>
									<td>'.editUrl($row).'</td>
									<td>'.deleteUrl($row).'</td>
								 </tr>';
						}
					}
					
					try {
						$conn = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
						// set the PDO error mode to exception
						$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
						displayRecords($conn);
					} catch(PDOException $error) {
						echo "<p>Error: ".$error->getMessage()."</p>\n";
					}
				?>
			</table>
		</div>
	</body>
</html>
