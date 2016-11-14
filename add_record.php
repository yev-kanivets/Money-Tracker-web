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
					$error = "Invalid price.";
				}
				if (strlen($title) == 0) {
					$error = "Title can't be empty.";
				}
				if (strlen($category) == 0) {
					$error = "Category can't be empty.";
				}

				if ($error == "") {
					$sql = "";
					if ($_POST['action'] == 'add') {
						$sql = "INSERT INTO records (type, time, price, title, category_id, user_id)
								VALUES (".$type.", '".time()."', '".$price."', '".$title."', '"
								.getCategoryId($conn, $category)."', '".$_SESSION['user_id']."');";
					} else {
						$sql = "UPDATE records SET type=".$type.", price=".$price.", title=".$title.", category_id=".getCategoryId($conn, $category)." 
								WHERE id=".$_POST['record_id'].";";
					}
					echo $sql;
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
                  echo "<p>Error: ".$error->getMessage()."</p>\n";
               }
			   exit();
            }
         ?>
      	</div>
      
      	<div class = "container">
			<form action="add_record.php" method="post">
				<fieldset>
					<h2>Add record</h2>
					<?php
						echo '<input type="hidden" name="action" value="'.$_GET['action'].'" />
							  <input type="hidden" name="record_id" value="'.$_GET['record_id'].'" />
							  <input type="hidden" name="type" value="'.$_GET['type'].'" />
							  <p><input type="number" name="price" size="40" maxlength="40" placeholder="Price" value="'.$_GET['price'].'""/></p>
							  <p><input type="text" name="title" size="40" maxlength="40" placeholder="Title" value="'.$_GET['title'].'"/></p>
							  <p><input type="text" name="category" size="40" maxlength="40" placeholder="Category" value="'.$_GET['category'].'"/></p>
							  <p><input type="submit" name= "add_record" value="Add record"/></p>'
					?>
				</fieldset>
			 </form>
      </div> 
      	</div> 
	</body>
</html>
