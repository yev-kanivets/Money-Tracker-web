<?php
	$url = parse_url(getenv("CLEARDB_DATABASE_URL"));
	
	$host = $url["host"];
	$login = $url["user"];
	$password = $url["pass"];
	$dbname = substr($url["path"], 1);
?>
