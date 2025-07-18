<?php
session_start();
// Destroy all distributor session data
session_unset();
session_destroy();
// Redirect to distributor login page
header('Location: login.php');
exit;
?>
