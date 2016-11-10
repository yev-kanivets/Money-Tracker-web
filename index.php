<!DOCTYPE html>

<html>
	<head>
		<meta charset="utf-8">
		<title>Money Tacker</title>
	</head>

	<body>
		<?php
			include ("connect.php");

			/* Sign Up page */

			function showSignUpPage() {
				echo '<form action="'.$PHP_SELF.'" method="get">
						<fieldset>
							<input type="hidden" name="action" value="action_sign_up" />
							<p><input type="text" name="full_name" size="40" maxlength="40" placeholder="Full name" /></p>
							<p><input type="email" name="email" size="40" maxlength="40" placeholder="Email" /></p>
							<p><input type="password" name="password" size="40" maxlength="40" placeholder="Password" /></p>
							<p><input type="password" name="confirm_password" size="40" maxlength="40" placeholder="Confirm password" /></p>
							<p><input type="submit" value="Sign Up" /></p>
						</fieldset>
					</form>';
			}

			function isUserExists($conn, $email) {
				$sql = "SELECT * FROM users
						WHERE email='".$email."';";
				return $conn->query($sql)->fetchColumn() > 0;
			}

			function signUp($conn, $full_name, $email, $password, $confirm_password) {
				$error = "";

				if (strlen($full_name) == 0) {
					$error = "Full name can't be empty.";
				}
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$error = "Email is invalid.";
				}
				if (strlen($password) < 6 || $password != $confirm_password) {
					$error = "Passwords must be equal and length > 5.";
				}
				if (isUserExists($conn, $email)) {
					$error = "User with such email already exists.";
				}

				if ($error == "") {
					$createUserSql = "INSERT INTO users (created_at, full_name, email, password)
									VALUES (".time().", '".$full_name."', '".$email."', '".$password."');";
					$conn->exec($createUserSql);
					showRecordListPage();
				} else {
					showErrorPage($error);
				}
			}

			/* Sign in page */

			function showSignInPage() {
				echo '<form action="'.$PHP_SELF.'" method="get">
						<fieldset>
							<input type="hidden" name="action" value="action_sign_in" />
							<p><input type="email" name="email" size="40" maxlength="40" placeholder="Email" /></p>
							<p><input type="password" name="password" size="40" maxlength="40" placeholder="Password" /></p>
							<p><input type="submit" value="Sign In" /></p>
						</fieldset>
					</form>';
			}

			function isUserCredsValid($conn, $email, $password) {
				$sql = "SELECT * FROM users
						WHERE email='".$email."' AND password='".$password."';";
				return $conn->query($sql)->fetchColumn() > 0;
			}

			function signIn($conn, $email, $password) {
				if (isUserCredsValid($conn, $email, $password)) {
					showRecordListPage();
				} else {
					showErrorPage("User credentials are invalid.");
				}
			}

			function showRecordListPage() {
				echo '<p>Main page<p>';
			}

			function showErrorPage($error) {
				echo "<p>Error: ".$error."<p>";
			}

			function startSessionForUser($userId, $full_name, $email) {
				$_SESSION['valid'] = true;
                $_SESSION['timeout'] = time();
                // Ending a session in 30 minutes from the starting time.
                $_SESSION['expire'] = $_SESSION['start'] + (30 * 60);
                $_SESSION['user_id'] = $userId;
                $_SESSION['username'] = $full_name;
                $_SESSION['email'] = $email;
			}
			
			try {
				$conn = new PDO("mysql:host=$host;dbname=$dbname", $login, $password);
				// set the PDO error mode to exception
				$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

				switch ($_GET["action"]) {
					case "action_show_sign_up_page":
						showSignUpPage();
						break;

					case "action_sign_up":
						signUp($conn, $_GET['full_name'], $_GET['email'], $_GET['password'], $_GET['confirm_password']);
						break;

					case "action_show_sign_in_page":
						showSignInPage();
						break;

					case "action_sign_in":
						signIn($conn, $_GET['email'], $_GET['password']);
						break;
						
					default:
						showSignInPage();
						break;
				}

			} catch(PDOException $erreur) {
				echo "<p>Erreur ".$erreur->getMessage()."</p>\n";
			}			
		?>
	</body>
</html>