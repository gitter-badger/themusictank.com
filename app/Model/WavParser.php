<?php
class WavParser
{


  function findValues($byte1, $byte2){
    $byte1 = hexdec(bin2hex($byte1));
    $byte2 = hexdec(bin2hex($byte2));
    return ($byte1 + ($byte2*256));
  }



    // https://github.com/afreiday/php-waveform-svg/blob/master/php-waveform-svg.php
    public function parse($filename, $resolution = 100)
    {
        $waveform = array();

        $heading = array();
        $handle = fopen($filename, "r");
        // wav file header retrieval
        $heading[] = fread($handle, 4);
        $heading[] = bin2hex(fread($handle, 4));
        $heading[] = fread($handle, 4);
        $heading[] = fread($handle, 4);
        $heading[] = bin2hex(fread($handle, 4));
        $heading[] = bin2hex(fread($handle, 2));
        $heading[] = bin2hex(fread($handle, 2));
        $heading[] = bin2hex(fread($handle, 4));
        $heading[] = bin2hex(fread($handle, 4));
        $heading[] = bin2hex(fread($handle, 2));
        $heading[] = bin2hex(fread($handle, 2));
        $heading[] = fread($handle, 4);
        $heading[] = bin2hex(fread($handle, 4));

        // wav bitrate
        $peek = hexdec(substr($heading[10], 0, 2));
        $byte = $peek / 8;

        // checking whether a mono or stereo wav
        $channel = hexdec(substr($heading[6], 0, 2));

        $ratio = ($channel == 2 ? 40 : 80);

        // start putting together the initial canvas
        // $data_size = (size_of_file - header_bytes_read) / skipped_bytes + 1
        $data_size = floor((filesize($filename) - 44) / ($ratio + $byte) + 1);
        $data_point = 0;

        while(!feof($handle) && $data_point < $data_size){
            if ($data_point++ % $resolution == 0) {
              $bytes = array();

              // get number of bytes depending on bitrate
              for ($i = 0; $i < $byte; $i++)
                $bytes[$i] = fgetc($handle);

              switch($byte){
                // get value for 8-bit wav
                case 1:
                  $data = $this->findValues($bytes[0], $bytes[1]);
                  break;
                // get value for 16-bit wav
                case 2:
                  if(ord($bytes[1]) & 128)
                    $temp = 0;
                  else
                    $temp = 128;
                  $temp = chr((ord($bytes[1]) & 127) + $temp);
                  $data = floor($this->findValues($bytes[0], $temp) / 256);
                  break;
              }

              // skip bytes for memory optimization
              fseek($handle, $ratio, SEEK_CUR);

              // draw this data point
              // data values can range between 0 and 255
              $waveform[] = $data;
              //$waveform[] = number_format($data_point / $data_size * 100, 2);

            } else {
              // skip this one due to lack of detail
              fseek($handle, $ratio + $byte, SEEK_CUR);
            }
        }

        // close and cleanup
        fclose($handle);

        return $waveform;
    }
}

