<!DOCTYPE html>
<html>
<head>
	<title>SIGN UP</title>
	<link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
     <form action="function.php" method="post">
          <input type="hidden" name="f" value="singup" />

     	<h2>SIGN UP</h2>
     	<?php if (isset($_POST['error'])) { ?>
     		<p class="error"><?= $_POST['error'] ?></p>
     	<?php } ?>

          <label>Name</label>
          <input type="text" name="name" placeholder="Name" required><br>

          <label>Email</label>
          <input type="text" name="email" placeholder="Email" required><br>

     	<label>Password</label>
     	<input type="password" name="password" placeholder="Password" required><br>

          <label>Re Password</label>
          <input type="password" name="re_password" placeholder="Re-Password" required><br>

     	<button type="submit">Sign Up</button>
          <a href="../index.php" class="ca">Already have an account?</a>
     </form>
</body>
</html>