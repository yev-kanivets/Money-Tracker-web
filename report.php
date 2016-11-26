<?php
   ob_start();
   session_start();
?>

<html>
	<head>
		<meta charset="utf-8">
		<title>Compte rendu</title>
		<link rel="stylesheet" type="text/css" href="css/main.css">
		
		<link rel="apple-touch-icon" sizes="57x57" href="/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
		<link rel="manifest" href="/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">
	</head>

	<body>
		<div class="main_container">
			<div class = "header">
				<?php echo "<p class >Bienvenu, ".$_SESSION['username']."!</p>"; ?>
				<a href="logout.php">Sortir</a>
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
							' AND time BETWEEN '.$ts_from.' AND '.$ts_to.
							' ORDER BY time DESC;';
					return $conn->query($sql);
				}
				
				function displayReport($conn, $ts_from, $ts_to) {							
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
								<th>Catégorie</th>
								<th>Prix</th>
							</tr>';
					
					foreach ($report as $category => $total) {
						$recordClass = "";
						if ($total >= 0) {
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
								
					echo '<h2>Bref copmte rendu</h2>
						  <p>Revenu total: '.$totalIncome.'<br>
						  Dépense total: '.$totalExpense.'<br>
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
				<p>Crée par Evgenii Kanivets et Hélène Martiuk</p>
			</div>
		</div>
	</body>
</html>
