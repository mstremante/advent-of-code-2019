<?php

// Run with php part1.php [input]
// If no input file is specified defaults to input.txt in local directory

$file = "input.txt";
if(isset($argv[1])) {
    $file = $argv[1];
}

if(file_exists($file)) {
    $handle = fopen($file, "r");
    $moons = array();
    while(($moon = fgets($handle)) !== false) {
        $moon = trim($moon);
        preg_match('/<x=(-?\d+), y=(-?\d+), z=(-?\d+)>/', $moon, $matches);
        $moons[] = array(
            "p" => array($matches[1], $matches[2], $matches[3]),
            "v" => array(0, 0, 0)
        );
    }
    fclose($handle);
    

    for($s = 0; $s < 1000; $s++) {

        // Apply gravity
        $moonsCopy = $moons;
        foreach($moons as $id => &$moon) {
            foreach($moonsCopy as $idCopy => $moonCopy) {
                if($id != $idCopy) {
                    for($t = 0; $t < 3; $t++) {
                        if($moon["p"][$t] > $moonCopy["p"][$t]) {
                            $moon["v"][$t] = $moon["v"][$t] - 1;
                            //echo "passinz\n";
                        } elseif($moon["p"][$t] < $moonCopy["p"][$t]) {
                            $moon["v"][$t] = $moon["v"][$t] + 1;
                            //echo "passing\n";
                        }
                    }
                }
            }
        }

        // Apply velocity
        foreach($moons as &$moon) {
            for($t = 0; $t < 3; $t++) {
                $moon["p"][$t] = $moon["p"][$t] + $moon["v"][$t];
            }
        }
    }

    // Calculate total energy
    $energy = 0;
    foreach($moons as &$moon) {
        $potE = abs($moon["p"][0]) + abs($moon["p"][1]) + abs($moon["p"][2]);
        $kinE = abs($moon["v"][0]) + abs($moon["v"][1]) + abs($moon["v"][2]);
        $energy += ($potE * $kinE);
    }
    echo $energy;
}

?>