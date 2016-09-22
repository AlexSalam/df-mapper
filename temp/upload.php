<?php

  // File for handling file uploads

  var_dump($_FILES);
  var_dump($_POST);

  // Do some auth shit

  $error = array(
    "error" => false,
    "msg" => array()
  );

  if ($_FILES['type'] != "application/x-gtar") {
    $error['error'] = true;
    array_push($error['msg'], "Upload of incorrect MIME type, Uploads must be GNU tar archives.");
  }
  if ($_FILES['size'] === 0 || $_FILES['size'] > 5000000) {
    $error['error'] = true;
    array_push($error['msg'], "Upload too large, you are limited to 50mb.");
  }

  die;
  if ($error['error'] === true) {
    $returnString = "";
    foreach ($error['msg'] as $msg) {
      $returnString .= urlencode($msg) . '&';
    }
    header('location: ../index.php?' . $returnString);
  }

?>
