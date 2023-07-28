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
     <form action="./public_html/function.php" method="post">
		<input type="hidden" name="f" value="logout" />

          <h1>Hello, <?php echo $_SESSION['name']; ?></h1>

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