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
			include ("connect.php");

			function displayReport($conn, $ts_from, $ts_to) {
						$sql = 'SELECT records.id AS record_id, type, time, records.title AS title, categories.title AS category, price 
								FROM records, categories
								WHERE user_id='.$_SESSION['user_id'].
								' AND category_id=categories.id'.
								' AND time BETWEEN '.$ts_from.' AND '.$ts_to.';';
						
						$totalIncome = 0;
						$totalExpense = 0;

						$report = array();
						foreach ($conn->query($sql) as $row) {
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
								<caption>Records</caption>
								<tr>
									<th>Category</th>
									<th>Total</th>
								</tr>';
								
						foreach ($report as $category => $total) {
							echo '<tr>
									<td>'.$category.'</td>
									<td>'.$total.'</td>
								 </tr>';
						}
						
						echo '</table>';
						
						echo '<h2>Short summary</h2>';
						echo '<p>Total income: '.$totalIncome.'</p>';
						echo '<p>Total expense: '.$totalExpense.'</p>';
						echo '<p>Total: '.($totalIncome - $totalExpense).'</p>';
					}
		 
            if (!isset($_SESSION['user_id'])) {
				header('Location: '.'sign_in.php', true, $permanent ? 301 : 302);
				exit();
			}
			
			try {
                $conn = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
                // set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                displayReport($conn, $_GET['ts_from'], $_GET['ts_to']);
            } catch(PDOException $error) {
                echo "<p>Error: ".$error->getMessage()."</p>\n";
            }
         ?>
      	</div>
	</body>
</html>
