<?php

// Run with php part1.php

$min = 156218;
$max = 652527;

$count = 0;
for($i = $min; $i < $max; $i++) {
    $str = strval($i);
    $len = strlen($str);

    $adjacentCheck = false;
    $increaseCheck = true;

    if($len != 6) {
        continue;
    }

    $first = $str[0];
    for($j = 1; $j < $len; $j++) {
        if($first == $str[$j]) {
            $adjacentCheck = true;
        }
        
        if($first > $str[$j]) {
            $increaseCheck = false;
        }

        $first = $str[$j];
    }

    if($increaseCheck && $adjacentCheck) {
        $count++;
    }
}

echo $count;

?>