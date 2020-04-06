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

    $computer = new Computer($opcodes);
    echo join("\n", $computer->runProgram(array(2)));
}

class Computer {

    private $data = array();
    private $pointer = 0;
    private $relativeBase = 0;
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
                    $op1 = $this->getValue($command, 1);
                    $op2 = $this->getValue($command, 2);
                    if($op == 01) {
                        $res = $op1 + $op2;
                    } else {
                        $res = $op1 * $op2;
                    }
                    $this->setValue($command, 3, $res);
                    $this->pointer += 4;
                    break;
                // Input value
                case "03":
                    if(!count($inputs)) {
                        return $outputs;
                    }
                    $input = array_shift($inputs);
                    $this->setValue($command, 1, $input);
                    $this->pointer += 2;
                    break;
                // Output value
                case "04":
                    $mode1 = substr($command, -3)[0];
                    $outputs[] = $this->getValue($command, 1);
                    $this->pointer += 2;
                    break;
                // Jump if true and jump if false
                case "05":
                case "06":
                    $op1 = $this->getValue($command, 1);
                    $op2 = $this->getValue($command, 2);
                    if(($op == "05" && $op1 != 0) || ($op == "06" && $op1 == 0)) {
                        $this->pointer = $op2;
                    } else {
                        $this->pointer +=3;
                    }
                    break;
                // Less than and equals
                case "07":
                case "08":
                    $op1 = $this->getValue($command, 1);
                    $op2 = $this->getValue($command, 2);
                    if(($op == "07" && $op1 < $op2) || ($op == "08" && $op1 == $op2)) {
                        $res = 1;
                    } else {
                        $res = 0;
                    }
                    $this->setValue($command, 3, $res);
                    $this->pointer += 4;
                    break;
                // Adjust relative base
                case "09":
                    $op1 = $this->getValue($command, 1);
                    $this->relativeBase += $op1;
                    $this->pointer += 2;
                    break;
            }
        }
        $this->finished = true;
        return $outputs;
    }

    function getValue($command, $param) {
        $mode = substr($command, -($param + 2))[0];
        $addr = $this->data[$this->pointer + $param];
        if($mode == 1) {
            return $addr;
        } elseif($mode == 2) {
            $addr = $addr + $this->relativeBase;
        }
        // Initialize memory
        if(!isset($this->data[$addr])) {
            $this->data[$addr] = 0;
        }
        return $this->data[$addr];
    }

    function setValue($command, $param, $value) {
        $mode = substr($command, -($param + 2))[0];
        $addr = $this->data[$this->pointer + $param];
        if($mode == 1 || $mode == 0) {
            $this->data[$addr] = $value;
        } elseif($mode == 2) {
            $this->data[$addr + $this->relativeBase] = $value;
        }
    }

    function isFinished() {
        return $this->finished;
    }
}

?>