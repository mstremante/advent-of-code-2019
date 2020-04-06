<?php

// Run with php part2.php [input]
// If no input file is specified defaults to input.txt in local directory

$file = "input.txt";
if(isset($argv[1])) {
    $file = $argv[1];
}

if(file_exists($file)) {
    $handle = fopen($file, "r");
    $opcodes = explode(",", fread($handle, filesize($file)));
    fclose($handle);

    // Look for required value
    for($i = 0; $i <= 99; $i++) {
        for($j = 0; $j <= 99; $j++) {
            $opcodes[1] = $i;
            $opcodes[2] = $j;
            $result = runProgram($opcodes);
            if($result == 19690720) {
                echo 100 * $i + $j;
            }
        }
    }
} else {
    exit("file does not exist.");
}

// Executes program
function runProgram($data) {
    $pointer = 0;
    while(($command = $data[$pointer]) != 99) {
        $op1 = $data[$data[$pointer + 1]];
        $op2 = $data[$data[$pointer + 2]];
        switch($command) {
            // Addition
            case 1:
                $data[$data[$pointer + 3]] = $op1 + $op2;
                break;
            // Multiplication
            case 2:
                $data[$data[$pointer + 3]] = $op1 * $op2;
                break;
        }
        $pointer += 4;
    }
    return $data[0];
}

?>