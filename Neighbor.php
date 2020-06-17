<?php

require_once 'Needleman.php';
require_once 'Utils.php';
require_once 'Dendogram.php';
class Neighbor {
    private $distances;
    private $dsCopy;
    private $sumMatrix;
    private $answer;
    public $dendogram;

    public function __construct($distances)
    {
        $this->distances = $distances;
        $this->dendogram = new Dendogram();
    }

    public function run(){

        $txtDistances = "";
        $txtQs = "";
        $txtAns = "";
        $u = 0;
        $this->dsCopy = $this->distances;

        $c = count($this->distances);
        for($i=0;$i<$c;$i++){
            $this->answer[] = chr(65 + $i);
        }

        while(count($this->distances) > 2){
            $q = $this->calculateQ();
            $txtDistances .= "\n------------\n" . $this->printMatrix($this->distances) . "\n------------\n";
            $txtQs .= "\n------------\n" . $this->printMatrix($q) . "\n------------\n";
            $union = $this->getUnionNodes($q);
            //echo "Se unen: " . $union["f"] . " con " .  $union["g"] . "\n";
            $dep = $this->answer[$union["f"]] . $this->answer[$union["g"]];
            $txtAns .= "Se unen: " . $this->answer[$union["f"]] . " con " .  $this->answer[$union["g"]] . " y forma: $dep" . "\n";
            $value = $union["value"];
            $this->dendogram->add($this->answer[$union["f"]], $this->answer[$union["g"]], $value);
            #print_r($this->dendogram);
            #echo "----------\n";
            delete_row($this->answer, $union["f"]);
            delete_row($this->answer, $union["g"]-1);
            //array_unshift($this->answer, "U$u");
            array_unshift($this->answer, "$dep");
            $this->getNewDistances($union["f"], $union["g"]);
            $u++;
        }
        $fix = $this->fixFinalDendogram();

        $txtAns .= "El ordenamiento optimo es: " . implode("", array_reverse($this->answer)) . "\n";
        $txtDistances .= "\n---------------Resultado----------\n" . $txtAns;
        return ["ds" => $txtDistances, "qs" => $txtQs];
    }

    public function fixFinalDendogram(){
        $node = $this->dendogram->head->b;
        $f = 'b';
        if($this->dendogram->isTerminal($this->dendogram->head->a)) {
            $node = $this->dendogram->head->a;
            $f = 'a';
        }

        $num = $this->sumfix($node);
        $num2 = round($num / 2,2);

        if($f == 'b') {
            $this->dendogram->head->bvalue = $num2;
            $this->dendogram->head->avalue = abs( ($this->dendogram->head->a->value / 2) - $num2);
        } else {
            $this->dendogram->head->avalue = $num2;
            $this->dendogram->head->bvalue = abs ( ($this->dendogram->head->b->value / 2) - $num2);
        }
    }

    public function sumfix($n){

        $count = 0;
        $p = count($this->dsCopy);
        $n = ord($n) - 65;

        echo "Calculo $n -- $p\n";


        for($i=0; $i<$p; $i++){
            if($n == $i)
                continue;
            $count += $this->dsCopy[$n][$i];
        }

        return (float) $count / (float) ($p-1);
    }

    public function distanceFromSequences($sequences){
        $utils = new Utils();

        $size = count($sequences);
        $score = array();
        $answer = array();
        $distances = array();

        for ($i = 0; $i < $size; $i++) {
            $answer[$i] = array();
            $score[$i] = array();
            $distances[$i] = array();

            for ($j = $i; $j < $size; $j++) {
                if ($i == $j) {
                    $score[$i][$j] = 0;
                    $distances[$i][$j] = 0;
                }
                echo "calculando $i - $j...\n";
                $needleman = new Needleman();
                $needleman->setSequences($sequences[$i], $sequences[$j]);
                $needleman->compute();

                $score[$i][$j] = $needleman->getScore();
                $score[$j][$i] = $needleman->getScore();
                $answer = $needleman->traceUnique();
                //var_dump($answer);
                //$answer = $utils->getBest($answer);

                $d = $this->calculeDist($answer);
                $d = round($d * 100, 2);
                $distances[$i][$j] = $d;
                $distances[$j][$i] = $d;
            }
        }

        for ($i = 0; $i < $size; $i++) {
            for ($j = $i; $j < $size; $j++) {
                $distances[$j][$i] = $distances[$i][$j];
            }
        }

        //var_dump($distances);
        echo "------------------\n" . $this->printMatrix($distances) . "------------------\n";
        $this->distances = $distances;
    }

    public function calculeDist($answer){
        $a = $answer["A"];
        $b = $answer["B"];
        $n = strlen($a);

        $numerator = 0;
        $denumer = 0;

        for($i = 0; $i<$n; $i++){
            if($a[$i] == "-" || $b[$i] == "-"){
                if($a[$i] == "-" && $b[$i] == "-"){
                    echo "Doble gap\n";
                }else{
                    $numerator++;
                }
            }else{
                $denumer++;
            }
        }

        return ((float) $numerator / (float) $denumer);
    }

    public function getNewDistances($f, $g){
        $distances = $this->distances;
        $n = count($distances);

        $sumMatrix = $this->sumMatrix;

        $dfu = (($distances[$f][$g])/2) + (($sumMatrix[$f] - $sumMatrix[$g])/(2*($n-2)));

        $dgu = $distances[$f][$g] - $dfu;

        //echo "$sumMatrix[$f] -- $sumMatrix[$g] ---> $f -- $g ---> $dfu -- $dgu\n";

        $newn = $n-1;

        $nDistances = array();
        for($i=0;$i<$newn;$i++){
            $nDistances[$i] = array();
            for($j=0;$j<$newn;$j++){
                $nDistances[$i][$j] = 0;
            }
        }

        $keys = array();
        for($i=0;$i<$n;$i++){
            $keys[] = $i;
        }

        $tmp = $distances;
        delete_row($tmp, $f);
        delete_col($tmp, $f);
        delete_row($tmp, $g-1);
        delete_col($tmp, $g-1);

        delete_row($keys, $f);
        delete_row($keys, $g-1);

        $nt = count($tmp);

        for($i=0;$i<$nt;$i++){
            for($j=0;$j<$nt;$j++){
                    $nDistances[$i+1][$j+1] = $tmp[$i][$j];
            }
        }

//        for($i=0;$i<$n;$i++)
//        {
//            $t = ($distances[$f][$i] + $distances[$g][$i] - $distances[$f][$g])/2;
//            echo    "$t\t";
//        }
//        echo "\n";

        //var_dump($keys);

        for($j=1;$j<$newn;$j++){
            $i = $keys[$j-1];
            $t = round(($distances[$f][$i] + $distances[$g][$i] - $distances[$f][$g])/2,2);
            $nDistances[0][$j] = $t;
            $nDistances[$j][0] = $t;
        }

//        for($i=0;$i<$n;$i++)
//        {
//            if($i == $f || $i == $g)
//                continue;
//
//            $t = ($distances[$f][$i] + $distances[$g][$i] - $distances[$f][$g])/2;
//            if($i == 0){
//                echo "entro1\n";
//                $nDistances[0][$i+1] = $t;
//                $nDistances[$i+1][0] = $t;
//                continue;
//            }
//            if($i == 1){
//                echo "entro2\n";
//                $nDistances[0][$i] = $t;
//                $nDistances[$i][0] = $t;
//                continue;
//            }
//            echo "entro3\n";
//            $nDistances[0][$i-1] = $t;
//            $nDistances[$i-1][0] = $t;
//        }

        //echo $this->printMatrix($tmp);
        //echo "\n------------------\n";
        //echo $this->printMatrix($nDistances);
        $this->distances = $nDistances;
    }

    public function printMatrix($matrix){
        $data = "";
        $size = count($matrix);
        for($i = 0; $i < $size; $i++){
            for($j = 0; $j < $size; $j++){
                $data .= $matrix[$i][$j] . "\t";
            }
            $data .= "\n";
        }

        return $data;
    }

    public function calculateQ(){
        $distances = $this->distances;
        $n = count($distances);

        $sumMatrix = $this->calculateSums($distances);
        $this->sumMatrix = $sumMatrix;

        $q = array();

        for($i=0;$i<$n;$i++){
            $q[$i] = array();
            for($j=$i;$j<$n;$j++){
                if($i == $j){
                    $q[$i][$j] = "-";
                    continue;
                }
                //echo "(($n - 2) * $distances[$i][$j]) - $sumMatrix[$i] - $sumMatrix[$j]\n";
                $calc = round($distances[$i][$j] - ((1/($n-2))/($sumMatrix[$i] + $sumMatrix[$j])),2);
                //$calc = (($n - 2) * $distances[$i][$j]) - $sumMatrix[$i] - $sumMatrix[$j];
                $q[$i][$j] = $calc;
            }
        }

        return $q;
    }

    public function getUnionNodes($q)
    {
        $n = count($q);
        $min = INF;
        $mi = -1;
        $mj = -1;

        for($i=0;$i<$n;$i++){
            for($j=$i;$j<$n;$j++){
                if($q[$i][$j] == "-")
                    continue;

                if($q[$i][$j] < $min){
                    $mi = $i;
                    $mj = $j;
                    $min = $q[$i][$j];
                }
            }
        }

        return ["f" => $mi, "g" => $mj, "value" => $min];
    }

    public function calculateSums($distances){
        $n = count($distances);

        $sumMatrix = array();

        for($i = 0; $i<$n; $i++)
        {
            $tCols = 0;
            for($j = 0; $j<$n; $j++){
                if ($i == $j)
                    continue;
                try {
                    $tCols += $distances[$i][$j];
                }
                catch (Exception $e){
                    echo "error: " . $distances[$i][$j] . "\n";
                }
            }
            $sumMatrix[$i] = $tCols;
        }

        return $sumMatrix;
    }
}

function delete_row(&$array, $offset) {
    return array_splice($array, $offset, 1);
}

function delete_col(&$array, $offset) {
    return array_walk($array, function (&$v) use ($offset) {
        array_splice($v, $offset, 1);
    });
}