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

    // Calcualte phase setting permutations
    $settings = array(0, 1, 2, 3, 4);
    $settingsPermutations = array();
    getPermutations($settings);

    $results = array();
    foreach($settingsPermutations as $permutation) {
        $result = null;
        foreach($permutation as $phaseSetting) {
            $inputs = array($phaseSetting);
            if(!$result) {
                $inputs[] = 0;
            } else {
                $inputs[] = $result;
            }
            $result = runProgram($opcodes, $inputs)[0];
        }
        $results[] = $result;
    }
    echo max($results);
}

// Calculate parmutations. Use ugly global variable
function getPermutations($items, $permutations = array()) {
    if(empty($items)) {
        global $settingsPermutations;
        $settingsPermutations[] = $permutations;
    } else {
        for($i = count($items) - 1; $i >= 0; $i--) {
            $newItems = $items;
            $newPermutations = $permutations;
            list($temp) = array_splice($newItems, $i, 1);
            array_unshift($newPermutations, $temp);
            getPermutations($newItems, $newPermutations);
        }
    }
}

// Executes program
function runProgram($data, $inputs) {
    $pointer = 0;
    $outputs = array();
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
            // Input value
            case "03":
                $input = array_shift($inputs);
                $data[$data[$pointer + 1]] = $input;
                $pointer += 2;
                break;
            // Output value
            case "04":
                $mode1 = substr($command, -3)[0];
                $outputs[] = value($mode1, $data[$pointer + 1], $data);
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
    return $outputs;
}

function value($mode, $addr, $data) {
    if($mode) {
        return $addr;
    }
    return $data[$addr];
}

?>