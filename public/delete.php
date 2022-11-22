<?php
header('Access-Control-Allow-Origin: *');
if (isset($_POST)) {
    $request_body = json_decode(file_get_contents('php://input'), 1);
    $files = json_decode($request_body['data']);
    foreach ($files as $file) {
        echo $file->path;
        unlink("." . $file->path);
    }
}

?>