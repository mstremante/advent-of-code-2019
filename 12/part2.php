<?php

// Run with php part2.php [input]
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

    // Look for  one repetion on each axis first
    $startPos = $moons;
    $dSteps = array();
    for($i = 0; $i < 3; $i++) {
        $steps = 0;
        while(true) {
            $moonsCopy = $moons;
            foreach($moons as $id => &$moon) {
                foreach($moonsCopy as $idCopy => &$moonCopy) {
                    if($id != $idCopy) {
                        if($moon["p"][$i] > $moonCopy["p"][$i]) {
                            $moon["v"][$i] = $moon["v"][$i] - 1;
                        } elseif($moon["p"][$i] < $moonCopy["p"][$i]) {
                            $moon["v"][$i] = $moon["v"][$i] + 1;
                        }
                    }
                }
            }

            // Apply velocity
            foreach($moons as &$moon) {
                $moon["p"][$i] = $moon["p"][$i] + $moon["v"][$i];
            }
            $steps++;

            // Check for repetition
            $done = true;
            foreach($moons as $id => &$moon) {
                if($moon["p"][$i] != $startPos[$id]["p"][$i]) {
                    $done = false;
                }
            }

            if($done) {
                $dSteps[] = $steps + 1;
                break;
            }
        }
    
    }

    echo lcm($dSteps[0], lcm($dSteps[1], $dSteps[2]));
}

function lcm($m, $n) {
    if($m == 0 || $n == 0) {
        return 0;
    }
    $r = ($m * $n) / gcd($m, $n);
    return abs($r);
}
 
function gcd($a, $b) {
    while($b != 0) {
        $t = $b;
        $b = $a % $b;
        $a = $t;
    }
    return $a;
}

?>