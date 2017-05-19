<?php

// Start session
session_start();
// Destroy all sessions and...
if (session_destroy()) {
    // redirect user to home page
    header("Location: index.php");
    // Make sure that codes below do not execute upon redirection.
    exit;
}