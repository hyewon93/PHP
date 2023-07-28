<?php 
session_start();

if (isset($_SESSION['id']) && isset($_SESSION['email'])) {

?>
<!DOCTYPE html>
<html>
<head>
	<title>HOME</title>
	<link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
     <form action="./function.php" method="post">
		<input type="hidden" name="f" value="logout" />

          <?php if (isset($_POST['error'])) { ?>
     		<p class="error"><?php echo $_POST['error']; ?></p>
     	<?php } ?>

          <h1>Hello, <?php echo $_SESSION['name']; ?></h1>

          <?php if(!empty($_SESSION['twoFactorAuth'])) { ?>
               <p>
                    Two-Factor Authentication is turned <b>ON</b>.<br>
                    To turn it off, please click <a href="./twoFactorAuthSetting.php">here</a>.
               </p>
          <?php } else { ?>
               <p>
                    Two-Factor Authentication is turned <b>OFF</b>.<br>
                    To turn it on, please click <a href="./twoFactorAuthSetting.php">here</a>.
               </p>
          <?php } ?>
          

     	<button type="submit">Logout</button>
     </form>
</body>
</html>

<?php 
} else {

     echo("<form name='redir_form' action='../index.php' method='post'>");
     echo("<input type='hidden' name='error' value='There was an error logging in.'>");
     echo("</form> <script language='javascript'> document.redir_form.submit(); </script>");
}
 ?>