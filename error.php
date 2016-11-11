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
            echo "<p>Error: ".$_GET['error']."</p>";
         ?>
      </div>
   </body>
</html>
