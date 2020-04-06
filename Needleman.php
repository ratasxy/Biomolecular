<?php

class Needleman{
    private $matrix;
    private $sequenceA;
    private $sizeA;
    private $sequenceB;
    private $sizeB;
    private $optimalA;
    private $optimalB;

    private static $up = '↑';
    private static $left = '←';
    private static $nortWest = '↖';

    public function setSequences($sequenceA, $sequenceB){
        $this->sequenceA = $sequenceA;
        $this->sequenceB = $sequenceB;

        $this->sizeA = strlen($this->sequenceA) + 1;
        $this->sizeB = strlen($this->sequenceB) + 1;
    }

    public function compute(){
        if($this->sizeA < 1 && $this->sizeB < 1)
            throw new Exception("Error las cadenas no son validas");

        $this->matrix[$this->sizeA][$this->sizeB];

        for($i = 0; $i < $this->sizeA; $i++) {
            $this->matrix[$i][0]['value'] = -2 * $i;
            $this->matrix[$i][0]['pointer'] = self::$up;
        }

        for($j = 0; $j < $this->sizeB; $j++) {
            $this->matrix[0][$j]['value'] = -2 * $j;
            $this->matrix[0][$j]['pointer'] = self::$left;
        }

        $this->matrix[0][0]['pointer'] = "";

        $this->printMatrix();

        for($i = 1; $i <= $this->sizeA; $i++){
            for($j = 1; $j <= $this->sizeB;$j++){
                $matchOrMismatch = ($this->sequenceA[$i-1] === $this->sequenceB[$j-1]) ? 1 : -1;
                //echo "value:  $matchOrMismatch"
                $match = $this->matrix[$i-1][$j-1]['value'] + $matchOrMismatch;
                $agap = $this->matrix[$i-1][$j]['value'] + -2;
                $bgap = $this->matrix[$i][$j-1]['value'] + -2;

                $max = max($match, $agap, $bgap);

                $pointer = self::$nortWest;
                if($max === $agap) {
                    $pointer = self::$up;
                } else if($max === $bgap) {
                    $pointer = self::$left;
                }

                $this->matrix[$i][$j]['value'] = $max;
                $this->matrix[$i][$j]['pointer'] = $pointer;
            }
        }

        echo "terminado\n";
    }

    public function computeTrace(){
        $i = $this->sizeA;
        $j = $this->sizeB;

        $this->optimalA = array();
        $this->optimalB = array();

        while($i !== 0 && $j !== 0){
            $backA = $this->sequenceA[$i-1];
            $backB = $this->sequenceB[$j-1];
            $pointer = $this->matrix[$i][$j]['pointer'];

            if($pointer === self::$nortWest){
                $i--;
                $j--;
                $this->optimalA[] = $backA;
                $this->optimalB[] = $backB;
            } elseif ($pointer === self::$up){
                $i--;
                $this->optimalA[] = $backA;
                $this->optimalB[] = '-';
            } else {
                $j--;
                $this->optimalA[] = '-';
                $this->optimalB[] = $backB;
            }

        }
    }

    public function printMatrix(){
        for($i = 0; $i < $this->sizeA; $i++){
            for($j = 0; $j < $this->sizeB; $j++){
                echo $this->matrix[$i][$j]['pointer'] . $this->matrix[$i][$j]['value'] . "\t";
            }
            echo "\n\n";
        }
    }

    public function getTrace(){

        $sequenceA = "";
        foreach (array_reverse($this->optimalA) as $value){
            $sequenceA .= $value;
        }

        $sequenceB = "";
        foreach (array_reverse($this->optimalB) as $value){
            $sequenceB .= $value;
        }

        return array(
            "A" => $sequenceA,
            "B" => $sequenceB
        );
    }
}