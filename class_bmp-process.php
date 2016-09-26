<?php

  class bmpProcessor
  {

    function __construct($lowLvl, $highLvl) {

      $this->lowLvl = $lowLvl;
      $this->highLvl = $highLvl;

      // tiles are 16x24px chunks

      $map = array();

      for ($i=$lowLvl; $i < $highLvl; $i++) {
        array_push($map, array());
      }

    }

    function processBmpIntoArray($img) {

      // Take a bmp image resource object and process it into a 144x144 2d array of 'tiles', tiles are 16x24 chunks
      // Some size detected

      if (imagesx($img) % 16 !== 0 || imagesy($img) % 24 !== 0) {
        return 'Layer does not fit expected dimensions';
      }

      

    }

    function readBMP($p_sFile) {

          //    Load the image into a string - I didn't steal this I swear master
          $file    =    fopen($p_sFile,"rb");
          $read    =    fread($file,10);
          while(!feof($file)&&($read<>""))
              $read    .=    fread($file,1024);

          $temp    =    unpack("H*",$read);
          $hex    =    $temp[1];
          $header    =    substr($hex,0,108);

          //    Process the header
          //    Structure: http://www.fastgraph.com/help/bmp_header_format.html
          if (substr($header,0,4)=="424d")
          {
              //    Cut it in parts of 2 bytes
              $header_parts    =    str_split($header,2);

              //    Get the width        4 bytes
              $width            =    hexdec($header_parts[19].$header_parts[18]);

              //    Get the height        4 bytes
              $height            =    hexdec($header_parts[23].$header_parts[22]);

              //    Unset the header params
              unset($header_parts);
          }

          //    Define starting X and Y
          $x                =    0;
          $y                =    1;

          //    Create newimage
          $image            =    imagecreatetruecolor($width,$height);

          //    Grab the body from the image
          $body            =    substr($hex,108);

          //    Calculate if padding at the end-line is needed
          //    Divided by two to keep overview.
          //    1 byte = 2 HEX-chars
          $body_size        =    (strlen($body)/2);
          $header_size    =    ($width*$height);

          //    Use end-line padding? Only when needed
          $usePadding        =    ($body_size>($header_size*3)+4);

          //    Using a for-loop with index-calculation instaid of str_split to avoid large memory consumption
          //    Calculate the next DWORD-position in the body
          for ($i=0;$i<$body_size;$i+=3)
          {
              //    Calculate line-ending and padding
              if ($x>=$width)
              {
                  //    If padding needed, ignore image-padding
                  //    Shift i to the ending of the current 32-bit-block
                  if ($usePadding)
                      $i    +=    $width%4;

                  //    Reset horizontal position
                  $x    =    0;

                  //    Raise the height-position (bottom-up)
                  $y++;

                  //    Reached the image-height? Break the for-loop
                  if ($y>$height)
                      break;
              }

              //    Calculation of the RGB-pixel (defined as BGR in image-data)
              //    Define $i_pos as absolute position in the body
              $i_pos    =    $i*2;
              $r        =    hexdec($body[$i_pos+4].$body[$i_pos+5]);
              $g        =    hexdec($body[$i_pos+2].$body[$i_pos+3]);
              $b        =    hexdec($body[$i_pos].$body[$i_pos+1]);

              //    Calculate and draw the pixel
              $color    =    imagecolorallocate($image,$r,$g,$b);
              imagesetpixel($image,$x,$height-$y,$color);

              //    Raise the horizontal position
              $x++;
          }

          //    Unset the body / free the memory
          unset($body);

          //    Return image-object
          return $image;
      }

    }

?>
