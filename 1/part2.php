<?php

// Run with php part2.php [input]
// If no input file is specified defaults to input.txt in local directory

$file = "input.txt";
if(isset($argv[1])) {
    $file = $argv[1];
}

if(file_exists($file)) {
    $handle = fopen($file, "r");
    $fuel = 0;
    while(($mass = fgets($handle)) !== false) {
        while(($mass = (floor(floatval($mass) / 3) - 2)) > 0) {
            $fuel += $mass;
        }
    }
    fclose($handle);
    echo $fuel;
} else {
    exit("file does not exist.");
}

?>