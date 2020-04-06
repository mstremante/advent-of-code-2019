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
    $result = runProgram($opcodes);
}

// Executes program
function runProgram($data) {
    $pointer = 0;
    while(($command = $data[$pointer]) != 99) {
        // Parse command:
        $op = substr($command, -2);
        $command = str_pad($command, 5, 0, STR_PAD_LEFT);
        switch($op) {

            // Addition and multiplication
            case "01":
            case "02":
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
            case "03":
                $input = readline("Input: ");
                $data[$data[$pointer + 1]] = $input;
                $pointer += 2;
                break;
            // Output value
            case "04":
                $mode1 = substr($command, -3)[0];
                echo value($mode1, $data[$pointer + 1], $data) . "\n";
                $pointer += 2;
                break;
            // Jump if true and jump if false
            case "05":
            case "06":
                $mode1 = substr($command, -3)[0];
                $mode2 = substr($command, -4)[0];
                $op1 = value($mode1, $data[$pointer + 1], $data);
                $op2 = value($mode2, $data[$pointer + 2], $data);
                if(($op == "05" && $op1) || ($op == "06" && !$op1)) {
                    $pointer = $op2;
                } else {
                    $pointer +=3;
                }
                break;
            // Less than and equals
            case "07":
            case "08":
                $mode1 = substr($command, -3)[0];
                $mode2 = substr($command, -4)[0];
                $op1 = value($mode1, $data[$pointer + 1], $data);
                $op2 = value($mode2, $data[$pointer + 2], $data);
                if(($op == "07" && $op1 < $op2) || ($op == "08" && $op1 == $op2)) {
                    $res = 1;
                } else {
                    $res = 0;
                }
                $data[$data[$pointer + 3]] = $res;
                $pointer += 4;
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