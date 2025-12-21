<?php
session_start();

/* Remove remember-me cookie */
if (isset($_COOKIE['remember_token'])) {
    setcookie("remember_token", "", time() - 3600, "/");
}

session_unset();
session_destroy();

header("Location: ../");
exit();