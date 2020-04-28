<?php

require_once 'Needleman.php';
require_once 'Utils.php';
class Neighbor {
    private $distances;
    private $sumMatrix;
    private $answer;

    public function __construct($distances)
    {
        $this->distances = $distances;
    }

    public function run(){

        $txtDistances = "";
        $txtQs = "";
        $u = 0;

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
            echo "Se unen: " . $this->answer[$union["f"]] . " con " .  $this->answer[$union["g"]] . " y forma: U$u" . "\n";
            delete_row($this->answer, $union["f"]);
            delete_row($this->answer, $union["g"]-1);
            array_unshift($this->answer, "U$u");
            $this->getNewDistances($union["f"], $union["g"]);
            $u++;
        }
        return ["ds" => $txtDistances, "qs" => $txtQs];
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
                $answer = $needleman->trace();
                $answer = $utils->getBest($answer);

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
                $calc = (($n - 2) * $distances[$i][$j]) - $sumMatrix[$i] - $sumMatrix[$j];
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

        return ["f" => $mi, "g" => $mj];
    }

    public function calculateSums($distances){
        $n = count($distances);

        $sumMatrix = array();

        for($i = 0; $i<$n; $i++)
        {
            $tCols = 0;
            for($j = 0; $j<$n; $j++){
                $tCols += $distances[$i][$j];
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