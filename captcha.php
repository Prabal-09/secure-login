<?php
session_start();


$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
$captcha_length = 6;
$captcha_code = '';

for ($i = 0; $i < $captcha_length; $i++) {
    $captcha_code .= $characters[rand(0, strlen($characters) - 1)];
}


$_SESSION["captcha"] = $captcha_code;

$image = imagecreate(150, 40);
$bg = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);

imagestring($image, 5, 35, 10, $captcha_code, $text_color);


header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>
