<?php

// Run with php part1.php [input]
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
    $leastZeros = 0;
    foreach($layers as $key => $layer) {
        $count = substr_count($layer, "0");
        if($count < $layerSize) {
            $leastZeros = $key;
            $layerSize = $count;
        }
    }

    // Multiply for checksum
    $layer = $layers[$leastZeros];
    echo substr_count($layer, "1") * substr_count($layer, "2");
}

?>