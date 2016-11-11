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
      
      <div class = "container">
         <form action="sign_in.php" method="post">
            <fieldset>
               <input type="hidden" name="action" value="action_sign_in" />
               <p><input type="email" name="email" size="40" maxlength="40" placeholder="Email" /></p>
               <p><input type="password" name="password" size="40" maxlength="40" placeholder="Password" /></p>
               <p><input type="submit" name= "sign_in" value="Sign In" /></p>
               <a href="sign_up.php">Don't have an account? Sign Up</a>
            </fieldset>
         </form>
      </div> 
   </body>
</html>
