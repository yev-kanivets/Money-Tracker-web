<?php
   ob_start();
   session_start();
?>

<html>
	<head>
		<meta charset="utf-8">
		<title>Ajouter note</title>
		<link rel="stylesheet" type="text/css" href="css/form.css">
		<link rel="stylesheet" type="text/css" href="css/login.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		
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
		<div class = "container">
			 <?php
				include ("connect.php");
				
				function getCategoryId($conn, $category) {
					$selectCategorySql = "SELECT * FROM categories WHERE title='".$category."';";
					$result = $conn->query($selectCategorySql);

					$categoryId = -1;
					if ($result->rowCount() == 0) {
						$addCategorySql = "INSERT INTO categories (title) VALUES ('".$category."');";
						$conn->exec($addCategorySql);
						$categoryId = $conn->lastInsertId();
					} else {
						$row = $result->fetch();
						$categoryId = $row['id'];
					}
					
					return $categoryId;
				}
			 
				function addRecord($conn, $type, $price, $title, $category) {
					$error = "";
					
					if (is_int($price) && $price >= 0) {
						$error = "Prix non valable.";
					}
					if (strlen($title) == 0) {
						$error = "Titre ne peut pas être vide.";
					}
					if (strlen($category) == 0) {
						$error = "Catégorie ne peut pas être vide.";
					}

					if ($error == "") {
						$sql = "";
						if ($_POST['action'] == 'add') {
							$sql = "INSERT INTO records (type, time, price, title, category_id, user_id)
									VALUES (".$type.", '".time()."', '".$price."', '".$title."', '"
									.getCategoryId($conn, $category)."', '".$_SESSION['user_id']."');";
						} else {
							$sql = "UPDATE records SET type=".$type.", price=".$price.", title='".$title."', category_id=".getCategoryId($conn, $category)." 
									WHERE id=".$_POST['record_id'].";";
						}
						$conn->exec($sql);
						header('Location: '.'index.php', true, $permanent ? 301 : 302);
					} else {
						header('Location: '.'error.php?error='.$error, true, $permanent ? 301 : 302);
					}
					exit();
				}
			 
				if (!isset($_SESSION['user_id'])) {
					header('Location: '.'sign_in.php', true, $permanent ? 301 : 302);
					exit();
				}
				
				if (isset($_POST['add_record'])) {
				   try {
					  $conn = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
					  // set the PDO error mode to exception
					  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					  addRecord($conn, $_POST['type'], $_POST['price'], $_POST['title'],
									$_POST['category']);
				   } catch(PDOException $error) {
					  echo "<p>Erreur: ".$error->getMessage()."</p>\n";
				   }
				   exit();
				}
			 ?>
		</div>
		
		<section id="login">
          <div class="container">
            <div class="row">
                <div class="col-xs-12">
                   <div class="form-wrap">     
                   <h1>
						<?php
							$record = "";
							if ($_GET['type'] == 0) {
								$record = "un revenu";
							} else {
								$record = "une dépense";
							}
							echo 'Ajouter '.$record;
						?>
				   </h1>             
					<form role="form"  action="add_record.php" method="post" id="login-form" autocomplete="on">
						<?php
							$record_id = isset($_GET['record_id']) ? $_GET['record_id'] : null;
							$price = isset($_GET['price']) ? $_GET['price'] : null;
							$title = isset($_GET['title']) ? $_GET['title'] : null;
							$category = isset($_GET['category']) ? $_GET['category'] : null;
							
							echo '	<input type="hidden" name="action" value="'.$_GET['action'].'" />
									<input type="hidden" name="record_id" value="'.$record_id.'" />
									<input type="hidden" name="type" value="'.$_GET['type'].'" />
								  
									<div class="form-group">
										<label for="price" class="sr-only">Prix</label>
										<input type="number" name="price" id="email" class="form-control" placeholder="Prix" value="'.$price.'">
									</div>
									<div class="form-group">
										<label for="title" class="sr-only">Titre</label>
										<input type="text" name="title" id="email" class="form-control" placeholder="Titre" value="'.$title.'">
									</div>
									<div class="form-group">
										<label for="category" class="sr-only">Catégorie</label>
										<input type="text" name="category" id="email" class="form-control" placeholder="Catégorie"  value="'.$category.'">
									</div>
								  
									<input type="submit" name= "add_record" id="btn-login" class="btn btn-custom btn-lg btn-block" value="Ajouter '.$record.'"/>'
						?>
                    </form>
                    <hr>
                   </div>
               </div> <!-- /.col-xs-12 -->
            </div> <!-- /.row -->
          </div> <!-- /.container -->
      </section>

      <footer id="footer">
          <div class="container">
              <div class="row">
                  <div class="col-xs-12">
                      <p>Crée par Evgenii Kanivets et Hélène Martiuk</p>
                  </div>
              </div>
          </div>
      </footer>
	  
	</body>
</html>
