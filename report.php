<?php
   ob_start();
   session_start();
?>

<html>
	<head>
		<meta charset="utf-8">
		<title>Report</title>
		<link rel="stylesheet" type="text/css" href="css/main.css">
	</head>

	<body>
		<div class="main_container">
			<div class = "header">
				<?php echo "<p class >Welcome, ".$_SESSION['username']."!</p>"; ?>
				<a href="logout.php">Logout</a>
			</div>
			<div class = "container">
			 <?php
				include ("connect.php");

				function getRecords($conn, $ts_from, $ts_to) {
					$ts_to += (24 * 60 * 60 - 1);
					$sql = 'SELECT records.id AS record_id, type, time, records.title AS title, categories.title AS category, price 
							FROM records, categories
							WHERE user_id='.$_SESSION['user_id'].
							' AND category_id=categories.id'.
							' AND time BETWEEN '.$ts_from.' AND '.$ts_to.';';
					return $conn->query($sql);
				}
				
				function displayReport($conn, $ts_from, $ts_to) {
					$sql = 'SELECT records.id AS record_id, type, time, records.title AS title, categories.title AS category, price 
							FROM records, categories
							WHERE user_id='.$_SESSION['user_id'].
							' AND category_id=categories.id'.
							' AND time BETWEEN '.$ts_from.' AND '.$ts_to.';';
							
					$totalIncome = 0;
					$totalExpense = 0;

					$report = array();
					foreach (getRecords($conn, $ts_from, $ts_to) as $row) {
						if (!isset($report[$row['category']])) {
							$report[$row['category']] = 0;
						}
						if ($row['type'] == 0) {
							$totalIncome += $row['price'];
							$report[$row['category']] += $row['price'];
						} else {
							$totalExpense += $row['price'];
							$report[$row['category']] -= $row['price'];
						}
					}
					
					echo '<table>
							<tr>
								<th>Category</th>
								<th>Price</th>
							</tr>';
					
					foreach ($report as $category => $total) {
						$recordClass = "";
						if ($row['type'] == 0) {
							$recordClass = "record_income";
						} else {
							$recordClass = "record_expense";
						}
						echo '<tr class="'.$recordClass.'">
								<td>'.$category.'</td>
								<td>'.$total.'</td>
							 </tr>';
					}
					
					echo '</table>';
				}
							
				function displayShortOverview($conn, $ts_from, $ts_to) {
					$totalIncome = 0;
					$totalExpense = 0;
										
					foreach (getRecords($conn, $ts_from, $ts_to) as $row) {
						if ($row['type'] == 0) {
							$totalIncome += $row['price'];
						} else {
							$totalExpense += $row['price'];
						}
					}
								
					echo '<h2>Short summary</h2>
						  <p>Total income: '.$totalIncome.'<br>
						  Total expense: '.$totalExpense.'<br>
						  Total: '.($totalIncome - $totalExpense).'</p>';
				}
			 
				if (!isset($_SESSION['user_id'])) {
					header('Location: '.'sign_in.php', true, $permanent ? 301 : 302);
					exit();
				}
				
				try {
					$conn = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
					// set the PDO error mode to exception
					$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					displayShortOverview($conn, $_GET['ts_from'], $_GET['ts_to']);
					displayReport($conn, $_GET['ts_from'], $_GET['ts_to']);
				} catch(PDOException $error) {
					echo "<p>Error: ".$error->getMessage()."</p>\n";
				}
			 ?>
			</div>
			<div class = "footer">
				<p>Created by Evgenii Kanivets and Elena Martiuk</p>
			</div>
		</div>
	</body>
</html>
