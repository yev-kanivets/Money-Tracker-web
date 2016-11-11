<?php
   session_start();
   unset($_SESSION["user_id"]);
   unset($_SESSION["username"]);
   unset($_SESSION["email"]);
   
   header('Location: '.'sign_in.php', true, $permanent ? 301 : 302);
?>
