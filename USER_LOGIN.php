    <!DOCTYPE html>
    <html lang="en" >
    <head>
      <meta charset="UTF-8">
      <title>Simple Login Form Example</title>
      

    </head>
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
      display: -webkit-box;
      display: flex;
      -webkit-box-orient: vertical;
      -webkit-box-direction: normal;
              flex-direction: column;
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
      padding: 12px 5px;
    }
    .login-form .input-field input {
      font-size: 16px;
      display: block;
      font-family: 'Rubik', sans-serif;
      width: 100%;
      padding: 10px 1px;
      border: 0;
      border-bottom: 1px solid #747474;
      outline: none;
      -webkit-transition: all .2s;
      transition: all .2s;
    }
    .login-form .input-field input::-webkit-input-placeholder {
      text-transform: uppercase;
    }
    .login-form .input-field input::-moz-placeholder {
      text-transform: uppercase;
    }
    .login-form .input-field input:-ms-input-placeholder {
      text-transform: uppercase;
    }
    .login-form .input-field input::-ms-input-placeholder {
      text-transform: uppercase;
    }
    .login-form .input-field input::placeholder {
      text-transform: uppercase;
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
      display: -webkit-box;
      display: flex;
      -webkit-box-orient: horizontal;
      -webkit-box-direction: normal;
              flex-direction: row;
    }
    .login-form .action button {
      width: 100%;
      border: none;
      padding: 18px;
      font-family: 'Rubik', sans-serif;
      cursor: pointer;
      text-transform: uppercase;
      background: #007bff;
      color: #2d3b55;
      border-bottom-left-radius: 4px;
      border-bottom-right-radius: 0;
      letter-spacing: 0.2px;
      outline: 0;
      -webkit-transition: all .3s;
      transition: all .3s;
    }
    .login-form .action button:hover {
      background: #d8d8d8;
    }
    .login-form .action button:nth-child(2) {
      background: #007bff;
      color: #fff;
      border-bottom-left-radius: 0;
      border-bottom-right-radius: 4px;
    }
    .login-form .action button:nth-child(2):hover {
      background: #2d3b55;
    }
    </style>
    <body>

    <div class="login-form">
      <form method="Post" action="USER_LOGIN.php" name="signin-form">
        <h1>Login</h1>
        <div class="content">
          <div class="input-field">
            <input type="email" placeholder="Email" name="username">
          </div>
          <div class="input-field">
            <input type="password" placeholder="Password" name="password">
          </div>
          
        </div>
        <div class="action">
          <button type="submit" name="register" value="register" >Register</button>
          <button type="submit" name="login" value="login">Sign in</button>
        </div>
      </form>
    </div>

    </body>
    </html>

<?php
if(isset($_POST["login"])) {
    $conn = mysqli_connect("localhost", "root", "", "cms_db") or die("NOT CONNECTED");   
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Convert input username to lowercase
    $username_lower = strtolower($username);

    // Query the database with lowercase username
    $qry = "SELECT USER_ID, USER_PASSWORD FROM login_reg WHERE LOWER(USER_ID) = '$username_lower'";
    $result = mysqli_query($conn, $qry);
    
    // Fetch the result
    $row = mysqli_fetch_assoc($result);

    // Check if the username exists in the database
    if($row) 
    {
        // Check if the password matches
        if($password == $row['USER_PASSWORD'])
        {
            if(!empty($_POST["username"]) && !empty($_POST["password"]))
              {
                header("Location:MAIN_CMS.php");
                exit();
              }
              else{
                echo "Please enter both the information";
              }
            }
          else
          {
            echo "INCORRECT PASSWORD";
          }
        } else {
            echo "USERNAME NOT FOUND";
        }
        //} else {
        //echo "USERNAME NOT FOUND";
    }
    


if(isset($_POST["register"])){
    header("Location:USER_REGISTER.php");
}
?>
