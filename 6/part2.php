<?php

// Run with php part2.php [input]
// If no input file is specified defaults to input.txt in local directory

$file = "input.txt";
if(isset($argv[1])) {
    $file = $argv[1];
}

$orbits = array();
if(file_exists($file)) {
    $handle = fopen($file, "r");
    while(($orbit = fgets($handle)) !== false) {
        $planets = explode(")", trim($orbit));
        if(array_key_exists($planets[0], $orbits)) {
            $orbits[$planets[0]][] = $planets[1];
        } else {
            $orbits[$planets[0]] = array($planets[1]);
        }

        if(!array_key_exists($planets[1], $orbits)) {
            $orbits[$planets[1]] = array();
        }
    }
    fclose($handle);

    // Find distances to center
    $path1 = findPathToPlanet("YOU", "COM", $orbits);
    $path2 = findPathToPlanet("SAN", "COM", $orbits);
    foreach($path1 as $step1) {
        foreach($path2 as $step2) {
            if($step1 == $step2) {
                echo array_search($step1, $path1) + array_search($step2, $path2);
                exit();
            }
        }
    }
}

function findPathToPlanet($start, $dest, $orbits) {
    $path = array();
    while($start != $dest) {
        $start = getPlanet($start, $orbits);
        $path[] = $start;
    }
    return $path;
}

// Ugly brute force
function getPlanet($satellite, $orbits) {
    foreach($orbits as $planet => $satellites) {
        foreach($satellites as $name) {
            if($satellite == $name) {
                return $planet;
            }
        }
    }
}

?>
