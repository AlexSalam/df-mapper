<!-- Upload form -->

<head>
  <link src="css/main.css" rel="stylesheet">
</head>
<body>
  <form action="temp/upload.php" method="post" enctype="multipart/form-data">
    <input type="file" name="bmp-tar" id="bmp-tar">
    <input type="submit">
  </form>
</body>

<?php

  //var_dump($_GET);
  if (count($_GET) > 0) {
    foreach($_GET as $error => $i) {
      echo "<p>" . str_replace("_", " ", $error) . "</p><br>";
    }
  }

?>
