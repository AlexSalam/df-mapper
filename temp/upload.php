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

    foreach ($files as $file) {
      if (preg_match('/.php/', $file) || preg_match('/.bmp/', $file)) {
        if (preg_match('/.bmp/', $file)) {

          // Rename the file to [number(layer)].bmp
          $newFile = preg_replace('/[a-z]|[A-Z]/', '', $file);
          $newFile = explode('-', $newFile);
          $newFile = $newFile[1] . ".bmp";
          $return = shell_exec('mv ' . $file . ' ' . $newFile . ' 2>&1');

        }
      } else {
        if (is_file($file)) {
          unlink($file);
        }
      }
    }
    require_once '../class_bmp-process.php';

    $files = scandir('../temp');

    $min = 0;
    $max = 0;
    foreach ($files as $file) {

      if (substr($file, 0, 1) == '.' || substr($file, 0, 1) == 'u') {
        //ignore
      } else {

        $num = (int) str_replace('.bmp', '', $file);

        if ($num > $max) {
          $max = $num;
          if ($min === 0) {
            $min = $num;
          }
        } else if ($num < $min || $min === 0) {
          $min = $num;
        }

      }

    }

    $BMP = new bmpProcessor($min, $max);

    echo '<pre>';
    set_time_limit('300');
    var_dump($BMP->readBMP($BMP->lowLvl . '.bmp'));
    $count = 0;

    for ($i=$BMP->lowLvl; $i < $BMP->highLvl; $i++) {

      $BMP->$map[$count] = $BMP->processBmpIntoArray($BMP->readBMP($BMP[$i]));
      $count++;

    }

  }

?>
