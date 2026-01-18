<?php
session_start();
session_unset();
session_destroy();
// fshi cookie "remember me" (do e përdorim më vonë)
setcookie("remember_me", "", time() - 3600, "/");
header("Location: /e-pharma/public/login.php");
exit;
