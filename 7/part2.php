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

    // Calcualte phase setting permutations
    $settings = array(5, 6, 7, 8, 9);
    $settingsPermutations = array();
    getPermutations($settings);

    $results = array();
    foreach($settingsPermutations as $permutation) {

        // Amplifiers
        $amplifiers = array(
            new Computer($opcodes), // A
            new Computer($opcodes), // B
            new Computer($opcodes), // C
            new Computer($opcodes), // D
            new Computer($opcodes)  // E
        );

        $result = null;
        $position = 0;
        // First loop with phase setting
        foreach($permutation as $phaseSetting) {
            $inputs = array($phaseSetting);
            if(!$result) {
                $inputs[] = 0;
            } else {
                $inputs[] = $result;
            }
            $result = $amplifiers[$position]->runProgram($inputs)[0];
            $position++;
        }

        do {
            $result = $amplifiers[$position % 5]->runProgram($result)[0];
            $position++;
        } while(!$amplifiers[($position) % 5]->isFinished());

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

class Computer {

    private $data = array();
    private $pointer = 0;
    private $finished = false;

    function __construct($program) {
        $this->data = $program;
    }

    // Executes program
    function runProgram($inputs = array()) {
        $outputs = false;
        if(!is_array($inputs)) {
            $inputs = array($inputs);
        }
        while(($command = $this->data[$this->pointer]) != 99) {
            // Parse command:
            $op = substr($command, -2);
            $command = str_pad($command, 5, 0, STR_PAD_LEFT);
            switch($op) {

                // Addition and multiplication
                case "01":
                case "02":
                    $mode1 = substr($command, -3)[0];
                    $mode2 = substr($command, -4)[0];
                    $op1 = $this->value($mode1, $this->data[$this->pointer + 1]);
                    $op2 = $this->value($mode2, $this->data[$this->pointer + 2]);
                    if($op == 01) {
                        $res = $op1 + $op2;
                    } else {
                        $res = $op1 * $op2;
                    }
                    $this->data[$this->data[$this->pointer + 3]] = $res;
                    $this->pointer += 4;
                    break;
                // Input value
                case "03":
                    if(!count($inputs)) {
                        return $outputs;
                    }
                    $input = array_shift($inputs);
                    $this->data[$this->data[$this->pointer + 1]] = $input;
                    $this->pointer += 2;
                    break;
                // Output value
                case "04":
                    $mode1 = substr($command, -3)[0];
                    $outputs[] = $this->value($mode1, $this->data[$this->pointer + 1]);
                    $this->pointer += 2;
                    break;
                // Jump if true and jump if false
                case "05":
                case "06":
                    $mode1 = substr($command, -3)[0];
                    $mode2 = substr($command, -4)[0];
                    $op1 = $this->value($mode1, $this->data[$this->pointer + 1]);
                    $op2 = $this->value($mode2, $this->data[$this->pointer + 2]);
                    if(($op == "05" && $op1 != 0) || ($op == "06" && $op1 == 0)) {
                        $this->pointer = $op2;
                    } else {
                        $this->pointer +=3;
                    }
                    break;
                // Less than and equals
                case "07":
                case "08":
                    $mode1 = substr($command, -3)[0];
                    $mode2 = substr($command, -4)[0];
                    $op1 = $this->value($mode1, $this->data[$this->pointer + 1]);
                    $op2 = $this->value($mode2, $this->data[$this->pointer + 2]);
                    if(($op == "07" && $op1 < $op2) || ($op == "08" && $op1 == $op2)) {
                        $res = 1;
                    } else {
                        $res = 0;
                    }
                    $this->data[$this->data[$this->pointer + 3]] = $res;
                    $this->pointer += 4;
                    break;
            }
        }
        $this->finished = true;
        return $outputs;
    }

    function value($mode, $addr) {
        if($mode) {
            return $addr;
        }
        return $this->data[$addr];
    }

    function isFinished() {
        return $this->finished;
    }
}

?>