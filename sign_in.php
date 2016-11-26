<?php
   ob_start();
   session_start();
?>

<!DOCTYPE html>
<html>
   <head>
      <meta charset="utf-8">
      <title>Entrer sur Compteur d'argent</title>
      <link rel="stylesheet" type="text/css" href="css/login.css">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	  <meta name="viewport" content="width=device_width, user-scalable=no, initial-scale=1.0">
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

               function signIn($conn, $email, $password) {
                  $sql = "SELECT * FROM users
                        WHERE email='".$email."' AND password='".$password."';";
                  $result = $conn->query($sql);

                  if ($result->rowCount() > 0) {
                     $row = $result->fetch();
                     startSessionForUser($row['id'], $row['full_name'], $row['email']);
                     header('Location: '.'index.php', true, $permanent ? 301 : 302);
                  } else {
                     header('Location: '.'error.php?error=User credentials are invalid.', true, $permanent ? 301 : 302);
                  }
                  exit();
               }
               
               if (isset($_POST['sign_in'])) {
                  try {
                     $conn = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
                     // set the PDO error mode to exception
                     $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                     signIn($conn, $_POST['email'], $_POST['password']);
                  } catch(PDOException $error) {
                     echo "<p>Error: ".$error->getMessage()."</p>\n";
                  }
               }
            ?>
      </div>

      <section id="login">
          <div class="container">
            <div class="row">
                <div class="col-xs-12">
                   <div class="form-wrap">     
                   <h1>Entrer sur Compteur d'argent</h1>             
                          <form role="form"  action="sign_in.php" method="post" id="login-form" autocomplete="on">
                              <div class="form-group">
                                  <label for="email" class="sr-only">E-mail</label>
                                  <input type="email" name="email" id="email" class="form-control" placeholder="E-mail">
                              </div>
                              <div class="form-group">
                                  <label for="password" class="sr-only">Mot de passe</label>
                                  <input type="password" name="password" id="key" class="form-control" placeholder="Mot de passe">
                              </div>
                              <input type="submit" id="btn-login" class="btn btn-custom btn-lg btn-block" name= "sign_in" value="Entrer">
                          </form>
                          <a href="sign_up.php" class="forget">Vous n'avez pas encore de compte? Signer</a>
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
