<?php
// TODO: Include config file
require_once(__DIR__ . '/config.php');


// TODO: Destroy session
session_start();
session_unset();
session_destroy();


// TODO: Redirect to login page
header("Location: login.php");
exit();

?>
