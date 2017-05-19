<?php
include ('login.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
        <title>Study Planner</title>
        <link rel="stylesheet" type="text/css" href="css/styles.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Viewport Meta Tag. Used to fit web page on all devices -->
    </head>
    <body>
        <!-- Link to external JavaScript file -->
        <script src="javascript/validator.js"></script>
        <div id="container">
            <br><br><br>
            <div id="logo">
                <img src="images/tearOffCalendar.png" alt="logo" style="max-width:50%; height:auto;">
            </div>
            <div id="logoText">
                <h1>Study Planner</h1>
            </div>
            <!-- Authentication Form -->
            <form name="authentication" onsubmit="return loginValidator()" action="login.php"  autocomplete="on" method="POST">
                <div class="login">
                    <label><b>Username:*</b></label>
                    <input type="email" id="username" name="username" placeholder="Enter your email" emailPattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" autofocus value=""/>
                    <label><b>Password:*</b></label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" value=""/>
                    <button name="submit" type="submit">Log in</button>
                    <div id="mandatoryFields">
                        <h5>* Mandatory Fields</h5>
                    </div>
                </div>
                <span><?php echo isset($_SESSION['error']) ? $_SESSION['error'] : ""; ?></span>
            </form>
            <hr>
            <div id="registerLink">
                <!-- Registration link -->
                <h5><a href="registration.php">Register</a></h5>
            </div>
            <hr>
            <div id="footerText">
                <h6>Copyright &copy; 2017, Chizzy Meka.</h6>
            </div>
        </div>
    </body>
</html>
