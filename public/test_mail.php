<?php
require_once __DIR__ . '/../includes/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../includes/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../includes/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

$mail = new PHPMailer(true);
echo "PHPMailer OK";
