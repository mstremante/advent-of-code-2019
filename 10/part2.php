<?php

// Run with php part2.php [input]
// If no input file is specified defaults to input.txt in local directory

$file = "input.txt";
if(isset($argv[1])) {
    $file = $argv[1];
}

if(file_exists($file)) {
    $asteroids = array();
    $handle = fopen($file, "r");
    $y = 0;
    while(($line = fgets($handle)) !== false) {
        $line = trim($line);
        for($x = 0; $x < strlen($line); $x++) {
            if($line[$x] == "#") {
                $asteroids[] = array($x, $y);
            }
        }
        $y++;
    }
    fclose($handle);

    // Find best asteroid
    $cAsteroids = $asteroids;
    $max = 0;
    $x = 0;
    $y = 0;
    foreach($asteroids as $asteroid) {
        $angles = array();
        foreach($cAsteroids as $otherAsteroid) {
            if($asteroid[0] == $otherAsteroid[0] && $asteroid[1] == $otherAsteroid[1]) {
                continue;
            }

            // Calculate angle
            $angle = calculateAngle($asteroid[0], $asteroid[1], $otherAsteroid[0], $otherAsteroid[1]);
            $angles[$angle] = true;
        }
        if(count($angles) > $max) {
            $max = count($angles);
            $x = $asteroid[0];
            $y = $asteroid[1];
        }
    }

    // Station is at $x and $y so calculate all angles based from this station    
    $otherAsteroids = array();
    foreach($asteroids as $asteroid) {
        if($x == $asteroid[0] && $y == $asteroid[1]) {
            continue;
        }
        $angle = calculateAngle($x, $y, $asteroid[0], $asteroid[1]);
        $otherAsteroids[$angle][] = array($asteroid[0], $asteroid[1]);
    }

    // Sort by angle
    ksort($otherAsteroids);
    
    // Flatten array so we can retrieve easily in order
    $flat = array();
    while(count($otherAsteroids)) {
        foreach($otherAsteroids as $angle => &$asteroids) {

            // Find the closest one
            $remove = null;
            $dist = null;
            foreach($asteroids as $i => $ast) {
                $nDist = calculateDistance($x, $y, $ast[0], $ast[1]);
                if(is_null($dist)) {
                    $dist = $nDist;
                    $remove = $i;
                }
                if($nDist < $dist) {
                    $dist = $nDist;
                    $remove = $i;
                }
            }
            $flat[] = $asteroids[$remove];
            unset($asteroids[$remove]);
            if(!count($asteroids)) {
                unset($otherAsteroids[$angle]);
            }
        }
    }
    // Get asteroid at position 200
    echo ($flat[199][0] * 100) + $flat[199][1];
}

function calculateDistance($x0, $y0, $x1, $y1) {
    return sqrt(pow($x1 - $x0, 2) +  pow($y1 - $y0, 2) * 1.0);
}

function calculateAngle($startX, $startY, $destX, $destY) {
    $angle = atan2($destY - $startY, $destX - $startX) * (180 / pi());
    $angle = fmod(($angle + 90), 360);
    if($angle < 0) {
        $angle += 360;
    }
    return $angle . "";
}

?>