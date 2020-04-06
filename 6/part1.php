<?php

// Run with php part1.php [input]
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

    // Count orbits
    $count = 0;
    foreach($orbits as $planet => $satellites) {
        while($planet != "COM") {
            $planet = getPlanet($planet, $orbits);
            $count++;
        }
    }
    echo $count;
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
