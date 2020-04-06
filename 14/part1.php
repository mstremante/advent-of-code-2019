<?php

// Run with php part1.php [input]
// If no input file is specified defaults to input.txt in local directory

$file = "input.txt";
if(isset($argv[1])) {
    $file = $argv[1];
}

if(file_exists($file)) {
    $handle  = fopen($file, "r");
    $recipes = array();

    // Parse input into a array keyed on the ingredients
    while(($line = fgets($handle)) !== false) {
        $recipe = trim($line);
        preg_match('/(.*)=>(.*)/', $line, $matches);
        $output = explode(" ", trim($matches[2]));
        $inputs = explode(",", trim($matches[1]));
        $iList  = array();
        foreach($inputs as $input) {
            $input   = explode(" ", trim($input));
            $iList[$input[1]] = $input[0];
        }
        $recipes[$output[1]] = array(
            "q" => $output[0],
            "i" => $iList
        );
    }
    fclose($handle);

    // Start from FUEL and work backwords
    $results = getIngredients("FUEL", 1, $recipes);
    echo $results["ORE"];
}

$reserve = array();
function getIngredients($output, $quantity, $recipes) {
    global $reserve;
    $needed = array();
    if(!isset($recipes[$output])) {
        return array($output => $quantity);
    }

    $ingredients = $recipes[$output]["i"];
    $produced    = $recipes[$output]["q"];

    // Use up reserve whenever available
    if(isset($reserve[$output]) && $reserve[$output] > 0) {
        if($reserve[$output] >= $quantity) {
            $reserve[$output] -= $quantity;
            return array();
        } else {
            $quantity -= $reserve[$output];
            $reserve[$output] = 0;
        }
    }

    if($quantity > $produced) {
        $multiplier = ceil($quantity / $produced);
        $reminder   = ($produced * $multiplier) - $quantity;
    } else {
        $multiplier = 1;
        $reminder   = $produced - $quantity;
    }

    // Add to reserve
    if(!isset($reserve[$output])) {
        $reserve[$output] = $reminder;
    } else {
        $reserve[$output] = $reserve[$output] + $reminder;
    }

    foreach($ingredients as $i => $q) {
        $temp = getIngredients($i, $q * $multiplier, $recipes);
        foreach($temp as $i => $q) {
            if(isset($needed[$i])) {
                $needed[$i] = $needed[$i] + $q;
            } else {
                $needed[$i] = $q;
            }
        }
    }
    return $needed;
}

?>