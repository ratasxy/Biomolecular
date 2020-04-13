<?php

class Smith{
    public $matrix;
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
            $this->matrix[$i][0]['value'] = 0;
            $this->matrix[$i][0]['pointer'] = array();
        }

        for($j = 0; $j < $this->sizeB; $j++) {
            $this->matrix[0][$j]['value'] = 0;
            $this->matrix[0][$j]['pointer'] = array();
        }

        $this->matrix[0][0]['pointer'] = array("");

        for($i = 1; $i < $this->sizeA; $i++){
            for($j = 1; $j < $this->sizeB;$j++) {
                $matchOrMismatch = ($this->sequenceA[$i - 1] === $this->sequenceB[$j - 1]) ? 1 : -1;
                $match = $this->matrix[$i - 1][$j - 1]['value'] + $matchOrMismatch;
                $agap = $this->matrix[$i - 1][$j]['value'] + -2;
                $bgap = $this->matrix[$i][$j - 1]['value'] + -2;
                $zero = 0;

                $max = max($match, $agap, $bgap, $zero);

                $pointer = array(self::$nortWest);

                if($agap == $match && $match == $agap){
                    $pointer = array(
                        $pointer = self::$up,
                        $pointer = self::$left,
                        $pointer = self::$nortWest,
                    );
                }

                if($max === $agap) {
                    $pointer = array(self::$up);
                    if($agap == $match)
                    {
                        $pointer = array(
                            $pointer = self::$up,
                            $pointer = self::$nortWest
                        );
                    }

                } else if($max === $bgap) {
                    $pointer = array(self::$left);
                    if($bgap == $match)
                    {
                        $pointer = array(
                            $pointer = self::$left,
                            $pointer = self::$nortWest
                        );
                    }

                }

                $this->matrix[$i][$j]['value'] = $max;
                if($max == 0)
                    $this->matrix[$i][$j]['pointer'] = array();
                else
                    $this->matrix[$i][$j]['pointer'] = $pointer;
            }
        }
    }

    public function getMax(){
        $max = 0;

        for($i = 1; $i < $this->sizeA; $i++) {
            for ($j = 1; $j < $this->sizeB; $j++) {
                if($this->matrix[$i][$j]['value'] > $max)
                    $max = $this->matrix[$i][$j]['value'];
            }
        }

        echo "El valor maximo es: $max\n";

        $maxList = array();

        for($i = 1; $i < $this->sizeA; $i++) {
            for ($j = 1; $j < $this->sizeB; $j++) {
                if($this->matrix[$i][$j]['value'] == $max)
                    $maxList[] = array($i, $j);
            }
        }

        return $maxList;

    }


    public function trace($i=-1, $j=-1){
        $maxList = $this->getMax();

        $allTrace = array();

        foreach ($maxList as $oneMax)
        {
            echo "Obteniendo trace de " . $oneMax[0] . " - " . $oneMax[1] . "\n";
            $allTrace = array_merge($allTrace, $this->makeTrace($oneMax[0], $oneMax[1]));
        }

        return $allTrace;
    }

    public function test($b, $c1, $c2){
        foreach ($b as $key => $a){
            $a['A'] = $a['A'] . $c1;
            $a['B'] = $a['B'] . $c2;

            $b[$key] = $a;
        }
        return $b;
    }

    public function makeTrace($i, $j){
        $pointers = $this->matrix[$i][$j]['pointer'];

        if($pointers == array()){
            return array(array(
                "A" => "",
                "B" => ""
            ));
        }

        $ans = array();
        foreach ($pointers as $pointer){
            $a = array();
            if ($pointer === self::$nortWest) {
                $a = $this->makeTrace($i-1, $j-1);
                $a = $this->test($a, $this->sequenceA[$i-1], $this->sequenceB[$j-1]);
            } elseif ($pointer === self::$up) {
                $a = $this->makeTrace($i-1, $j);
                $a = $this->test($a, $this->sequenceA[$i-1], "-");
            } else {
                $a = $this->makeTrace($i, $j-1);
                $a = $this->test($a, "-", $this->sequenceB[$j-1]);
            }

            $ans = array_merge($ans, $a);

            //print_r($ans);
        }

        return $ans;

    }

    public function printMatrix(){
        $data = "";
        for($i = 0; $i < $this->sizeA; $i++){
            for($j = 0; $j < $this->sizeB; $j++){
                $data .= $this->printPointer($this->matrix[$i][$j]['pointer']) . $this->matrix[$i][$j]['value'] . "\t";
            }
            $data .= "\n";
        }

        return $data;
    }

    public function printPointer($pointer){
        $ptext = "";
        if(is_array($pointer)){
            foreach ($pointer as $p)
                $ptext .= $p;
        }else{
            $ptext .= $pointer;
        }
        return $ptext;
    }

}