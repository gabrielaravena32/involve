<?php
// include the connect script
include_once "php/connect.php";

$im = new Imagick();
$im->setResolution(300,300);
$im->readImage('file.pdf[0]');
$im->setImageFormat('jpg');
$im->thumbnailImage(114, 186, true, true);
$im->setImageCompression(imagick::COMPRESSION_JPEG);
$im->setImageCompressionQuality(100);
$im->stripImage();
$data = $im->getImageBlob();
$data = $conn->real_escape_string($data);

$conn->query("UPDATE files SET internalBlob='$data' WHERE fileID=2;");


$result = $conn->query("SELECT internalBlob FROM files WHERE fileID=2 LIMIT 1;");
$result = $result->fetch_assoc();
$result = $result['internalBlob'];

echo '<img src="data:image/jpeg;base64,' .  base64_encode($result)  . '" />';

?>
