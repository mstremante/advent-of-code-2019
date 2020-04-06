<?php

// Run with php part2.php [input]
// If no input file is specified defaults to input.txt in local directory

$file    = "input.txt";
$reserve = array("ORE" => 1000000000000);
$fuel    = 0;

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

    $fuel    = 0;
    $oreLeft = $reserve["ORE"];

    // Brute force approach to get this done quick as I did not have time
    // Calculate how much ORE we use for one FUEL
    getIngredients("FUEL", 1, $recipes);
    $fuel++;

    // Check the difference
    $diff = $oreLeft - $reserve["ORE"];

    // Divide by what is left
    $div = ceil($reserve["ORE"] / $diff);

    // While it is a big number
    while(strlen($reserve["ORE"]) > 10) {

        // Get us close to using up all ORE
        getIngredients("FUEL", $div, $recipes);
        $fuel += $div;

        $oreLeft = $reserve["ORE"];
        getIngredients("FUEL", 1, $recipes);
        $fuel++;

        $diff = $oreLeft - $reserve["ORE"];
        $div = ceil(ceil($reserve["ORE"] / $diff) / 10);
    }

    // Keep going at small increments
    while($reserve["ORE"] >= 0) {
        getIngredients("FUEL", 1, $recipes);
        $fuel++;
    }
    echo $fuel;
}

function getIngredients($output, $quantity, $recipes) {
    global $reserve, $fuel;
    $needed = array();

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

    if($output == "ORE") {
        echo $fuel;
        exit();
    }

    $ingredients = $recipes[$output]["i"];
    $produced    = $recipes[$output]["q"];

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