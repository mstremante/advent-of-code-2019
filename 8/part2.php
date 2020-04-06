<?php

// Run with php part2.php [input]
// If no input file is specified defaults to input.txt in local directory

$file = "input.txt";
if(isset($argv[1])) {
    $file = $argv[1];
}

if(file_exists($file)) {
    $handle = fopen($file, "r");
    $image = fread($handle, filesize($file));
    fclose($handle);

    $width = 25;
    $height = 6;

    $layerSize = $width * $height;
    $layers = str_split($image, $layerSize);

    $finalImage = "";
    for($i = 0; $i < $layerSize; $i++) {
        foreach($layers as $layer) {
            $pix = $layer[$i];
            if($pix == 1) {
                $finalImage .= "#";
                break;
            } elseif($pix == 0) {
                $finalImage .= " ";
                break;
            }
        }
        if(($i + 1) % 25 == 0 && $i != 0) {
            $finalImage .= "\n";
        }
    }
    echo $finalImage;
}

?>