<?php

// Run with php part2.php [input]
// If no input file is specified defaults to input.txt in local directory

$file = "input.txt";
if(isset($argv[1])) {
    $file = $argv[1];
}

if(file_exists($file)) {
    $handle = fopen($file, "r");
    $wires = array();
    while(($wire = fgets($handle)) !== false) {
        $wires[] = explode(",", trim($wire));
    }
    fclose($handle);

    // Calculate coordinates at each step. Use multi dimensional array
    $nWires = count($wires);
    $coords = array();
    $wireId = 0;
    foreach($wires as $wire) {
        $x = $y = 0;
        $steps = 1;
        $coords[$x][$y][$wireId] = 0;
        foreach($wire as $movement) {
            $value = substr($movement, 1);
            for($i = 0; $i < $value; $i++) {
                switch($movement[0]) {
                    case 'R':
                        $x++;
                        break;
                    case 'L':
                        $x--;
                        break;
                    case 'D':
                        $y--;
                        break;
                    case 'U':
                        $y++;
                        break;
                }

                // Keep lowest step value
                if(!isset($coords[$x][$y][$wireId])) {
                    $coords[$x][$y][$wireId] = $steps;
                }
                $steps++;
            }
        }
        $wireId++;
    }

    // Search for intersections and calculate best distance based on steps
    $bestDistance = null;
    foreach($coords as $xc => $y) {
        foreach($y as $yc => $wires) {
            if(count($wires) == $nWires) {
                $distance = array_sum($wires);
                if(!$bestDistance && $distance) {
                    $bestDistance = $distance;
                } elseif($distance < $bestDistance && $distance) {
                    $bestDistance = $distance;
                }
            }
        }
    }

    echo $bestDistance;
} else {
    exit("file does not exist.");
}

?>