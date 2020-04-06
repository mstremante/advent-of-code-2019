<?php

// Run with php part1.php [input]
// If no input file is specified defaults to input.txt in local directory

$file = "input.txt";
if(isset($argv[1])) {
    $file = $argv[1];
}

if(file_exists($file)) {
    $handle = fopen($file, "r");
    $opcodes = explode(",", fread($handle, filesize($file)));
    fclose($handle);

    // Replacements as per requirements
    $opcodes[1] = 12;
    $opcodes[2] = 2;

    // Start execution
    $pointer = 0;
    while(($command = $opcodes[$pointer]) != 99) {
        $op1 = $opcodes[$opcodes[$pointer + 1]];
        $op2 = $opcodes[$opcodes[$pointer + 2]];
        switch($command) {
            // Addition
            case 1:
                $opcodes[$opcodes[$pointer + 3]] = $op1 + $op2;
                break;
            // Multiplication
            case 2:
                $opcodes[$opcodes[$pointer + 3]] = $op1 * $op2;
                break;
        }
        $pointer += 4;
    }
    
    echo $opcodes[0];
} else {
    exit("file does not exist.");
}

?>