<?php
ob_start(); // fixes bug when redirecting to mainpage

require_once "config.php";


if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $username = filter_input(INPUT_POST, "username");
    $password = filter_input(INPUT_POST, "password");
    $confirm = filter_input(INPUT_POST, "confirm");
    
    // username validation
    // ===================
    $userIsValid = validateUsername($username);
    
    // query if username already exists in database
    $exsql = "select lower(username) as username from user";
    $result = $conn->query($exsql);
    while ($row = $result->fetch_assoc())
    {
        if (strtolower($username) == $row["username"])
        {
            echo nl2br("sorry, username already exists\n");
            $userIsValid = false;
            break;
        }
    }
    
    // password validation
    // ===================
    $passIsValid = validatePassword($password, $confirm);
    
    // create account in database
    // ==========================
    if ($userIsValid and $passIsValid)
    {
        $sql = "insert into user (username, password) values ('{$username}', '{$password}')";
        
        if ($conn->query($sql) === true)
        {
            echo "SUCCESS::new record created successfully";
        }
        else
        {
            echo "ERROR::SQL FAILED OPERATION";
        }
    }
}


// HELPER FUNCTIONS
// ================
function validateUsername($username)
{
    if ($username == null)
    {
        echo nl2br("please enter a username\n");
        return false;
    }
    if (strlen($username) < 6 or strlen($username) > 20)
    {
        echo nl2br("please enter a username between 6 and 20 characters\n");
        return false;
    }
    
    $chars = str_split($username);
    foreach ($chars as $ch)
    {
        if ((ord($ch) >= 65 and ord($ch) <= 90) 
            or (ord($ch) >= 97 and ord($ch) <= 122))
        {
            continue;
        }
        else
        {
            echo nl2br("please enter a username with letters only and no spaces\n");
            return false;
        }
    }
    
    return true;
}

function validatePassword($password, $confirm)
{
    if ($password !== $confirm)
    {
        echo nl2br("passwords do not match\n");
        return false;
    }
    if ($password == null)
    {
        echo nl2br("please enter a password\n");
        return false;
    }
    if (strlen($password) < 6 or strlen($password) > 20)
    {
        echo nl2br("please enter a password between 6 and 20 characters\n");
        return false;
    }
    
    $chars = str_split($password);
    foreach ($chars as $ch)
    {
        if ((ord($ch) >= 65 and ord($ch) <= 90) 
            or (ord($ch) >= 97 and ord($ch) <= 122))
        {
            continue; // letters are permitted
        }
        else if (ord($ch) >= 48 and ord($ch) <= 57)
        {
            continue; // numbers are permitted
        }
        else
        {
            echo nl2br("please enter a password with letters and numbers only and no spaces\n");
            return false;
        }
    }
    
    return true;
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
        <a href="login.php" class="logo">GroupChat</a>
        <div class="header-right">
            <a href="docs/specifications.html">specifications</a>
            <a href="docs/about.html">about</a>
        </div>
    </div>

    <div class="form">
        <h1>registration form</h1>
        <form action="register.php" method="post">
            <label for="username">username</label>
            <input type="text" name="username">
    
            <label for="password">password</label>
            <input type="password" name="password">
            
            <label for="password">confirm password</label>
            <input type="password" name="confirm">
    
            <button type="submit">register</button>
        </form>
    </div>
    <div class="switch_access">
        <a href="login.php">back to login</a>
    </div>
</body>
</html>