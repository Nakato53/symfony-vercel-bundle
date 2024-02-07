<?php
    $file = __DIR__ . '/../public/' . $_SERVER['SCRIPT_NAME'];

    $path_parts = pathinfo($file);
    $extension = $path_parts['extension'];

    switch(strtolower($extension)) {
        case 'css':
            header('content-type: text/css');
            break;
        case 'js':
            header('content-type: application/javascript');
            break;
        case 'png':
            header('content-type: image/png');
            break;
        case 'jpg':
            header('content-type: image/jpeg');
            break;
        case 'gif':
            header('content-type: image/gif');
            break;
        case 'ico':
            header('content-type: image/x-icon');
            break;
        case 'svg':
            header('content-type: image/svg+xml');
            break;
        case 'woff':
            header('content-type: application/font-woff');
            break;
        case 'woff2':
            header('content-type: application/font-woff2');
            break;
        case 'ttf':
            header('content-type: application/font-sfnt');
            break;
        case 'eot':
            header('content-type: application/vnd.ms-fontobject');
            break;
        case 'otf':
            header('content-type: font/opentype');
            break;
        case 'json':
            header('content-type: application/json');
            break;
        case 'xml':
            header('content-type: application/xml');
            break;
        case 'txt':
            header('content-type: text/plain');
            break;
        case 'pdf':
            header('content-type: application/pdf');
            break;
        default:
            die();
    }

    $im = file_get_contents($file);
    echo $im;

