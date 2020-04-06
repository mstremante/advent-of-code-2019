<?php

// Run with php part2.php

$min = 156218;
$max = 652527;

$count = 0;
for($i = $min; $i < $max; $i++) {
    $str = strval($i);
    $len = strlen($str);

    $adjacentCheck = false;
    $increaseCheck = true;
    $hasGroupOfTwo = false;

    if($len != 6) {
        continue;
    }

    for($j = 0; $j < $len; $j++) {
        $consecutive = 1;
        while(($j + 1) < $len && $str[$j] == $str[$j + 1]) {
            $adjacentCheck = true;
            $consecutive++;
            $j++;
        }

        if($consecutive == 2) {
            $hasGroupOfTwo = true;
        }

        if(($j + 1) < $len && $str[$j] > $str[$j + 1]) {
            $increaseCheck = false;
        }
    }

    if($increaseCheck && $adjacentCheck && $hasGroupOfTwo) {
        $count++;
    }
}

echo $count;

?>