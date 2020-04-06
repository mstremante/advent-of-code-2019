<?php

// Run with php part1.php [input]
// If no input file is specified defaults to input.txt in local directory

$file = "input.txt";
if(isset($argv[1])) {
    $file = $argv[1];
}

if(file_exists($file)) {
    $handle = fopen($file, "r");
    $fuel = 0;
    while(($mass = fgets($handle)) !== false) {
        $fuel += (floor(floatval($mass) / 3) - 2);
    }
    fclose($handle);
    echo $fuel;
} else {
    exit("file does not exist.");
}

?>