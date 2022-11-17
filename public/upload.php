<?php
header('Access-Control-Allow-Origin: *');
$countfiles = count($_FILES['file']['name']);
$allowed_extensions = array('jpg', 'jpeg', 'png', 'pdf');
for ($i = 0; $i < $countfiles; $i++) {
    $filename = $_FILES['file']['name'][$i];
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    if (in_array(strtolower($extension), $allowed_extensions)) {
        if (move_uploaded_file($_FILES['file']['tmp_name'][$i], "upload/sertificates/" . $filename)) {
            echo 1;
        } else {
            echo 0;
        }
    } else {
        echo 0;
    }
}
?>