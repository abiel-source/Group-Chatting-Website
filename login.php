<?php
ob_start(); // fixes bug when redirecting to mainpage
session_start();

require_once "config.php";


if($_SERVER['REQUEST_METHOD'] == 'POST') {
  
  $username = filter_input(INPUT_POST, "username");
  $password = filter_input(INPUT_POST, "password");
  
  // validate username
  // =================
  $isValidUser = validateUsername($username);
  
  // validate password
  // =================
  $isValidPass = validatePassword($password);
  
  //password test logic
  if ($isValidUser and $isValidPass)
  {
      $sql = "select password from user where username = '" . $_POST['username'] . "'";
      $result = $conn->query($sql);
    
      if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if ($row['password'] === $_POST['password']) {
          echo nl2br("login successful\n");
          login();
        }
        else {
          echo nl2br("wrong password\n");
        }
      }
      else {
        echo nl2br("this username does not exist\n");
      }      
  }
  $conn->close();
}


// HELPER FUNCTIONS
// ================
function validateUsername($username)
{
    if (empty(trim($username)))
    {
        echo nl2br("please enter a username\n");
        return false;
    }
    return true;
}

function validatePassword($password)
{
    if (empty(trim($password)))
    {
        echo nl2br("please enter a password\n");
        return false;
    }
    return true;
}

function login() {
  // set up session variables
  $_SESSION['username'] = filter_input(INPUT_POST, "username");
  $_SESSION['password'] = filter_input(INPUT_POST, "password");
  header("Location: mainpage.php");
  exit();
}
?>


<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>GroupChat</title>
  <meta name="description" content="online chat site">
  <meta name="author" content="Abiel Kim">
  <link rel="stylesheet" href="css/access.css?<?php echo time();?>">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300&display=swap" rel="stylesheet">
</head>
<body>
    <div class="header">
        <a href="" class="logo">GroupChat</a>
        <div class="header-right">
            <a href="docs/specifications.html">specifications</a>
            <a href="docs/about.html">about</a>
        </div>
    </div>    

    <div class="form">
        <!--<div class="logs"></div>-->
        <h1>login form</h1>
        <form action="login.php" method="post">
            <label for="username">username</label>
            <input type="text" name="username">              
        
            <label for="password">password</label>
            <input type="password" name="password">            
            
            <button type="submit">login</button>
        </form>
    </div>
    
    <div class="switch_access">
        <a href="register.php">Register</a>
    </div>
    
    <script type="text/javascript">
        sessionStorage.removeItem("username");
        sessionStorage.removeItem("password");
    </script>
</body>
</html>