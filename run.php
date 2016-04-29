<?php

require_once('classes/ArchiveParser.php');

function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    if (!(error_reporting() & $errno)) return;

    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");

try {
    $archive = new ArchiveParser('source/new.rar');
    $pass = $archive->unpackBrute(0, 200);
    if (is_null($pass)) {
        echo 'Password not found';
    } else {
        echo 'Password find: '.$pass;
    }
} catch (Exception $ex) {
    echo "Error: ".$ex->getMessage()."\n";
}
