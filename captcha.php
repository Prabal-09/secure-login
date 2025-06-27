<?php
session_start();

$code = rand(1000, 9999);
$_SESSION["captcha"] = $code;

$image = imagecreate(80, 30);
$bg = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);

imagestring($image, 5, 15, 8, $code, $text_color);

header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>
