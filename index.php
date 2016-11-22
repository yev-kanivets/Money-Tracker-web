<?php
   ob_start();
   session_start();

    if (!isset($_SESSION['user_id'])) {
	  	header('Location: '.'sign_in.php', true, $permanent ? 301 : 302);
		exit();
	}
?>

<html>
	<head>
		<meta charset="utf-8">
		<title>Compteur d'argent</title>
		<link rel="stylesheet" type="text/css" href="css/main.css">
	</head>

	<body>
		<div class = "main_container">	      
			<div class = "header">
				<?php echo "<p class >Bienvenu, ".$_SESSION['username']."!</p>"; ?>
				<a href="logout.php">Sortir</a>
			</div>

	      	<div class = "links_container">
				<a class = "link_add_income" href="add_record.php?action=add&type=0">Ajouter un revenu</a>
				<a class = "link_add_expense" href="add_record.php?action=add&type=1">Ajouter une dépense</a>
	      	</div>
			
			<div class = "period_container">
				<form>
					<?php
						$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
						echo '<input type="date" name="date_from" value="'.$dateFrom.'">'; 
					?>
					<?php
						$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : date("Y-m-t", strtotime(date('Y-m-01')));  
						echo '<input type="date" name="date_to" value="'.$dateTo.'">'; 
					?>
					<input type="submit" value="Afficher les notes"/>
					<?php
						$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
						$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : date("Y-m-t", strtotime(date('Y-m-01')));
						
						$ts_from = strtotime($dateFrom);
						$ts_to = strtotime($dateTo) + (24 * 60 * 60 - 1);
						
						echo '<a href="report.php?ts_from='.$ts_from.'&ts_to='.$ts_to.'">Compte rendu</a>';
					?>
				<form>
			</div>
			
			<div class = "container">
					<?php
						include ("connect.php");
						
						function editUrl($record) {
							return '<a href="add_record.php?action=edit&record_id='.$record['record_id'].'&type='.$record['type'].'&price='.$record['price'].'&title='.$record['title'].'&category='.$record['category'].'">Edit</a>';
						}
						
						function deleteUrl($record) {
							return '<a href="delete_record.php?record_id='.$record['record_id'].'">Delete</a>';
						}
						
						function getRecords($conn, $ts_from, $ts_to) {
							$ts_to += (24 * 60 * 60 - 1);
							$sql = 'SELECT records.id AS record_id, type, time, records.title AS title, categories.title AS category, price 
									FROM records, categories
									WHERE user_id='.$_SESSION['user_id'].
									' AND category_id=categories.id'.
									' AND time BETWEEN '.$ts_from.' AND '.$ts_to.';';
							return $conn->query($sql);
						}
						
						function displayRecords($conn, $ts_from, $ts_to) {							
							echo '<table>
									<tr>
										<th>Temps</th>
										<th>Titre</th>
										<th>Catégorie</th>
										<th>Prix</th>
									</tr>';
									
							foreach (getRecords($conn, $ts_from, $ts_to) as $row) {
								$recordClass = "";
								if ($row['type'] == 0) {
									$recordClass = "record_income";
								} else {
									$recordClass = "record_expense";
								}
								echo '<tr class="'.$recordClass.'">
										<td>'.date("Y-m-d H:i", $row['time']).'</td>
										<td>'.$row['title'].'</td>
										<td>'.$row['category'].'</td>
										<td>'.$row['price'].'</td>
										<td>'.editUrl($row).'</td>
										<td>'.deleteUrl($row).'</td>
									 </tr>';
							}
							
							echo '</table>';
						}
						
						try {
							$conn = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
							// set the PDO error mode to exception
							$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

							$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : date('Y-m-01');
							$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : date("Y-m-t", strtotime(date('Y-m-01')));
							
							displayRecords($conn, strtotime($dateFrom), strtotime($dateTo));
						} catch(PDOException $error) {
							echo "<p>Erreur: ".$error->getMessage()."</p>\n";
						}
					?>
			</div>

			<div class = "footer">
				<p>Crée par Evgenii Kanivets et Hélène Martiuk</p>
			</div>
		</div>
	</body>
</html>
