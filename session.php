<?php

// Establish connection to the server
$mysqli = mysqli_connect("localhost", "root", "");
// Selecting Database
$db = mysqli_select_db($mysqli, "p00702");
// Starting session
session_start();
// Storing Session
$user_check = $_SESSION['login_user'];
$userid = $_SESSION['user_id'];
$firstname = $_SESSION['user_firstname'];
$weekNumber = $_SESSION['count'];
// Test to see the content of the global session variable
//*Commented out* print_r($_SESSION);
// SQL Query To Fetch Complete Information Of User
$ses_sql = mysqli_query($mysqli, "SELECT username FROM logins WHERE username='$user_check'");
$row = mysqli_fetch_assoc($ses_sql);
$login_session = $row['username'];
if (!isset($login_session)) {
    // Closing Connection
    mysqli_close($mysqli);
    // Redirecting To Home Page
    header('Location: index.php');
    // Make sure that codes below do not execut upon redirection.
    exit;
}


/*Session Timeout*/
//Expire the session if user is inactive for 60
//minutes or more.
$expireAfter = 60;
 
//Check to see if our "last action" session
//variable has been set.
if(isset($_SESSION['last_action'])){
    
    //Figure out how many seconds have passed
    //since the user was last active.
    $secondsInactive = time() - $_SESSION['last_action'];
    
    //Convert our minutes into seconds.
    $expireAfterSeconds = $expireAfter * 60;
    
    //Check to see if they have been inactive for too long.
    if($secondsInactive >= $expireAfterSeconds){
        //User has been inactive for too long.
        //Kill their session.
        session_unset();
        session_destroy();
        // Redirecting To Home Page
        header('Location: index.php');
        // Make sure that codes below do not execut upon redirection.
        exit;
    }
    
}
 
//Assign the current timestamp as the user's
//latest activity
$_SESSION['last_action'] = time();