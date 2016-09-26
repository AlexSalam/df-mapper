<?php

  // File for handling file uploads

  // Do some auth shit

  $error = array(
    "error" => false,
    "msg" => array()
  );

  if ($_FILES['bmp-tar']['type'] != "application/x-gzip") {
    $error['error'] = true;
    array_push($error['msg'], "Upload of incorrect MIME type, Uploads must be gzip archives.");
  }
  if ($_FILES['size'] === 0 || $_FILES['size'] > 5000000) {
    $error['error'] = true;
    array_push($error['msg'], "Upload too large, you are limited to 50mb.");
  }
  if ($error['error']) {
    $returnString = "";
    foreach ($error['msg'] as $msg) {
      $returnString .= urlencode($msg) . '&';
    }
    header('location: ../index.php?' . $returnString);
  } else {

    //If no errors process upload

    move_uploaded_file($_FILES['bmp-tar']['tmp_name'], "map.tar.gz");

    $return = shell_exec('tar -xvzf map.tar.gz 2>&1'); // Search return for some error?

    $files = scandir('../temp');
    echo '<pre>';
    foreach ($files as $file) {
      if (preg_match('/.php/', $file) || preg_match('/.bmp/', $file)) {
        if (preg_match('/.bmp/', $file)) {
          $return = shell_exec('mv ' . $file . ' ../bmp-processing');
        }
      } else {
        if (is_file($file)) {
          unlink($file);
        }
      }
    }
    require_once '../class_bmp-process.php';

  }

?>
