<?php
$file = __DIR__ . '/../public/' . $_SERVER['SCRIPT_NAME'];
$path_parts = pathinfo($file);
if(isset($path_parts['extension'])){
    header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
    echo "404 Not Found";
    die;
}
return require __DIR__ . '/../public/index.php';