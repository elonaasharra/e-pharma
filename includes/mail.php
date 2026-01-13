<?php
require_once __DIR__ . '/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

function sendEmail($data) {
    $mail = new PHPMailer(true);

    try {
        // SMTP (plotëso me të tuat)
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "noreplyweb005@gmail.com";
        $mail->Password = "rpzo ydoo szhx eccb";
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        $mail->setFrom("noreplyweb005@gmail.com", "E-Pharma");
        $mail->addAddress($data["user_email"]);

        $link = isset($data["link"]) ? $data["link"] : ("http://localhost/e-pharma/public/verify.php?token=" . urlencode($data["token"]));

        $mail->isHTML(true);
        $actionText = "Open Link";
        $subject = "E-Pharma";

        if (strpos($link, "verify.php") !== false) {
            $actionText = "Verify Email";
            $subject = "Verify your email";
        } elseif (strpos($link, "reset_password.php") !== false) {
            $actionText = "Reset Password";
            $subject = "Reset your password";
        }

        $mail->Subject = $subject;

        $mail->Body = '
<div style="font-family: Arial, sans-serif; background:#f4f4f4; padding:30px;">
  <div style="max-width:600px; background:#ffffff; padding:30px; margin:auto; border-radius:8px; text-align:center;">
    <h2 style="color:#333;">E-Pharma</h2>
    <p style="font-size:16px; color:#555;">Please click the button below to continue:</p>

    <a href="'.$link.'" style="
        display:inline-block;
        padding:14px 28px;
        background:#0d6efd;
        color:#ffffff;
        text-decoration:none;
        border-radius:6px;
        font-size:16px;
        margin-top:20px;">
      '.$actionText.'
    </a>

    <p style="font-size:12px; color:#999; margin-top:30px;">
      If you did not request this action, please ignore this email.
    </p>

    <p style="font-size:12px; color:#999; margin-top:10px;">
      Or copy this link: <br><a href="'.$link.'">'.$link.'</a>
    </p>
  </div>
</div>
';

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
