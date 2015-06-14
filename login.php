<?php
//login.php

require_once 'includes/global.inc.php';

$error = "";
$username = "";
$password = "";

//check to see if they've submitted the login form
if(isset($_POST['submit-login'])) { 

	$username = $_POST['username'];
	$password = $_POST['password'];

	$userTools = new UserTools();
	if($userTools->login($username, $password)){
		//successful login, redirect them to a page
		$user = unserialize($_SESSION['user']);
		if($user->isadmin == 0)
			{
				header("Location: index.php");
			}
		else header("Location: admin_index.php");
	}else{
		$error = "Incorrect username or password. Please try again.";
	}
}
?>

<html>
<head>
	<title>Login</title>
</head>
<body>

<?php
if($error != "")
{
    echo $error."<br/>";
}
?>
<div id="login">
	<form id="form_login" action="login.php" method="post">
	    Username: <input type="text" name="username" value="<?php echo $username; ?>" ><br/>
	    Password: <input type="password" name="password" value="<?php echo $password; ?>" /><br/>
	    <input type="submit" value="Login" name="submit-login" />
	</form>
</div>
	
</body>
</html>