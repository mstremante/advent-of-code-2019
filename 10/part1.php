<?php

// Run with php part1.php [input]
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

    // For each asteroid check how many more can be seen
    $cAsteroids = $asteroids;
    $max = 0;
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
        }
    }
    echo $max;
}

function calculateAngle($startX, $startY, $destX, $destY) {
    $angle = atan2($destY - $startY, $destX - $startX) * (180 / pi());
    return $angle . "";
}

?>