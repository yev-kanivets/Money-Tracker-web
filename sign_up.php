<?php
   ob_start();
   session_start();
?>

<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <title>Signer au Compteur d'argent</title>
      <link rel="stylesheet" type="text/css" href="css/login.css">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
   </head>
   <body>
      <div class = "container">
            <?php
               include ("connect.php");

               function startSessionForUser($userId, $full_name, $email) {
                  $_SESSION['valid'] = true;
                  $_SESSION['timeout'] = time();
                  // Ending a session in 30 minutes from the starting time.
                  $_SESSION['expire'] = $_SESSION['start'] + (30 * 60);
                  $_SESSION['user_id'] = $userId;
                  $_SESSION['username'] = $full_name;
                  $_SESSION['email'] = $email;
               }

               function isUserExists($conn, $email) {
                  $sql = "SELECT * FROM users
                        WHERE email='".$email."';";
                  return $conn->query($sql)->fetchColumn() > 0;
               }

               function signUp($conn, $full_name, $email, $password, $confirm_password) {
                  $error = "";

                  if (strlen($full_name) == 0) {
                     $error = "Nom ne doit pas être vide.";
                  }
                  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                     $error = "E-mail n'est pas valable.";
                  }
                  if (strlen($password) < 6 || $password != $confirm_password) {
                     $error = "Mots de passe doivent être éguaux et longueur > 5.";
                  }
                  if (isUserExists($conn, $email)) {
                     $error = "Utilisateur avec cet e-mail existe déjà.";
                  }

                  if ($error == "") {
                     $createUserSql = "INSERT INTO users (created_at, full_name, email, password)
                                 VALUES (".time().", '".$full_name."', '".$email."', '".$password."');";
                     $conn->exec($createUserSql);
                     startSessionForUser($conn->lastInsertId(), $full_name, $email);
                     header('Location: '.'index.php', true, $permanent ? 301 : 302);
                  } else {
                     header('Location: '.'error.php?error='.$error, true, $permanent ? 301 : 302);
                  }
                  exit();
               }
               
               if (isset($_POST['sign_up'])) {
                  try {
                     $conn = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
                     // set the PDO error mode to exception
                     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                     signUp($conn, $_POST['full_name'], $_POST['email'], $_POST['password'], $_POST['confirm_password']);
                  } catch(PDOException $error) {
                     echo "<p>Erreur: ".$error->getMessage()."</p>\n";
                  }
               }
            ?>
      </div>
         
      <section id="login">
          <div class="container">
            <div class="row">
                <div class="col-xs-12">
                   <div class="form-wrap">     
                   <h1>Signer au Compteur d'argent</h1>             
                          <form role="form"  action="sign_up.php" method="post" id="login-form" autocomplete="on">
                              <div class="form-group">
                                  <label for="full_name" class="sr-only">Nom</label>
                                  <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Nom">
                              </div>
                              <div class="form-group">
                                  <label for="email" class="sr-only">E-mail</label>
                                  <input type="email" name="email" id="email" class="form-control" placeholder="E-mail">
                              </div>
                              <div class="form-group">
                                  <label for="password" class="sr-only">Mot de passe</label>
                                  <input type="password" name="password" id="key" class="form-control" placeholder="Mot de passe">
                              </div>
                              <div class="form-group">
                                  <label for="confirm_password" class="sr-only">Mot de passe</label>
                                  <input type="password" name="confirm_password" id="key" class="form-control" placeholder="Confirmer le mot de passe">
                              </div>
                              <input type="submit" id="btn-login" class="btn btn-custom btn-lg btn-block" name="sign_up" value="Signer">
                          </form>
                          <a href="sign_in.php" class="forget">Avez-vous déjà un compte? Entrer</a>
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
