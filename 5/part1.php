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
    $result = runProgram($opcodes);
}

// Executes program
function runProgram($data) {
    $pointer = 0;
    while(($command = $data[$pointer]) != 99) {
        // Parse command:
        $op = substr($command, -2);
        switch($op) {

            // Addition and multiplication
            case 01:
            case 02:
                $command = str_pad($command, 5, 0, STR_PAD_LEFT);
                $mode1 = substr($command, -3)[0];
                $mode2 = substr($command, -4)[0];
                $op1 = value($mode1, $data[$pointer + 1], $data);
                $op2 = value($mode2, $data[$pointer + 2], $data);
                if($op == 01) {
                    $res = $op1 + $op2;
                } else {
                    $res = $op1 * $op2;
                }
                $data[$data[$pointer + 3]] = $res;
                $pointer += 4;
                break;
            // Save value
            case 03:
                $command = str_pad($command, 3, 0, STR_PAD_LEFT);
                $input = readline("Input: ");
                $data[$data[$pointer + 1]] = $input;
                $pointer += 2;
                break;
            // Output value
            case 04:
                $command = str_pad($command, 3, 0, STR_PAD_LEFT);
                $mode1 = substr($command, -3)[0];
                echo value($mode1, $data[$pointer + 1], $data) . "\n";
                $pointer += 2;
                break;
        }
    }
}

function value($mode, $addr, $data) {
    if($mode) {
        return $addr;
    }
    return $data[$addr];
}

?>