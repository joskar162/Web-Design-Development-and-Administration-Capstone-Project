<?php
// Include config file
require_once(__DIR__ . '/config/config.php');


// TODO: Destroy session
session_start();
session_unset();
session_destroy();


// TODO: Redirect to login page
header("Location: login.php");
exit();

?>
