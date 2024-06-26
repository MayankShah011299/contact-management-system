<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Simple Login Form Example</title>
<style>
    * {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  -webkit-font-smoothing: antialiased;
}

body {
  background: #2F4F4F;
  font-family: 'Rubik', sans-serif;
}

.login-form {
  background: #fff;
  width: 500px;
  margin: 65px auto;
  border-radius: 4px;
  box-shadow: 0 2px 25px rgba(0, 0, 0, 0.2);
}
.login-form h1 {
  padding: 35px 35px 0 35px;
  font-weight: 300;
}
.login-form .content {
  padding: 35px;
  text-align: center;
}
.login-form .input-field {
  margin-bottom: 20px; /* Adjusted margin */
}
.login-form .input-field input {
  font-size: 16px;
  display: block;
  font-family: 'Rubik', sans-serif;
  width: 100%;
  padding: 10px;
  border: 0;
  border-bottom: 1px solid #747474;
  outline: none;
  transition: all .2s;
}
.login-form .input-field input::placeholder {
  text-transform: none; /* Removed uppercase transformation */
}
.login-form .input-field input:focus {
  border-color: #222;
}
.login-form a.link {
  text-decoration: none;
  color: #747474;
  letter-spacing: 0.2px;
  text-transform: uppercase;
  display: inline-block;
  margin-top: 20px;
}
.login-form .action {
  display: flex;
  flex-direction: row;
}
.login-form .action input[type="submit"] {
  width: 100%;
  border: none;
  padding: 18px;
  font-family: 'Rubik', sans-serif;
  cursor: pointer;
  text-transform: uppercase;
  background: #90EE90;
  color: #777;
  border-bottom-left-radius: 4px;
  border-bottom-right-radius: 0;
  letter-spacing: 0.2px;
  outline: 0;
  transition: all .3s;
}
.login-form .action input[type="submit"]:hover {
  background: #d8d8d8;
}
</style>
</head>
<body>

<div class="login-form">
  <form action="USER_REGISTER.php" method="POST">
    <h1>Create New Account</h1>
    <div class="content">
      <div class="input-field">
        <input type="text" placeholder="Enter Name" name="user_name">
      </div>
      <div class="input-field">
        <input type="email" placeholder="Email" autocomplete="nope" name="user_id">
      </div>
      <div class="input-field">
        <input type="password" placeholder="Enter Password" autocomplete="new-password" name="user_password">
      </div>
      <div class="input-field">
        <input type="password" placeholder="Re-enter Password" autocomplete="new-password" name="confirm_password">
      </div>
    </div>
    <div class="action">
      <input type="submit" name="register" value="Register">
      <input type="submit" name="login" value="Login">
    </div>
  </form>
</div>
</body>
</html>

<?php
$con = mysqli_connect('localhost', 'root', '', 'cms_db') or die("Not Connected");

if (isset($_POST['register'])) {
  
  $user_name = $_POST['user_name'];
  $user_id = $_POST['user_id'];
  $user_password = $_POST['user_password'];
  $confirm_password = $_POST['confirm_password'];

  // Check if any of the input fields are null
  if (empty($user_name) || empty($user_id) || empty($user_password) || empty($confirm_password)) {
      echo "Please fill in all the fields";
      exit(); 
  }

  // Ensure password and confirm password match
  if ($user_password !== $confirm_password) {
      echo "Passwords do not match";
      exit(); 
  }

  // Insert the user into the database
  $qry = $con->prepare("INSERT INTO login_reg (USER_NAME, USER_ID, USER_PASSWORD) VALUES (?, ?, ?)");
  $qry->bind_param("sss", $user_name, $user_id, $user_password);
  $result = $qry->execute();

  if ($result) {
      echo "Successfully Registered";
  } else {
      echo "Registration failed";
  }

  $qry->close();
}



mysqli_close($con);


if(isset($_POST['login'])){
    header("Location:USER_LOGIN.php");
    exit();
}
?>
