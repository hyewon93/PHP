<!DOCTYPE html>
<html>
<head>
	<title>LOGIN</title>
	<link rel="stylesheet" type="text/css" href="./public_html/css/style.css">
</head>
<body>
     <form action="./public_html/function.php" method="post">
		<input type="hidden" name="f" value="login" />
		<input type="hidden" name="type" value="email" />

     	<h2>LOGIN</h2>

     	<?php if (isset($_POST['error'])) { ?>
     		<p class="error"><?php echo $_POST['error']; ?></p>
     	<?php } ?>

		<?php if (isset($_POST['message'])) { ?>
            <p class="success"><?php echo $_POST['message']; ?></p>
        <?php } ?>

     	<label>Email</label>
     	<input type="text" name="email" placeholder="Email" required><br>

     	<label>Password</label>
     	<input type="password" name="password" placeholder="Password" required><br>

     	<button type="submit">Login</button>
        <a href="./public_html/signup.php" class="ca">Create an account</a>
     </form>
</body>
</html>