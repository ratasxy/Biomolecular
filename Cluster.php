<?php

require 'Needleman.php';
require_once 'Utils.php';

class Cluster {
    private $distances;
    private $type;
    private $answer;

    public function __construct($distances, $type)
    {
        $this->distances = $distances;
        $this->type = $type;
    }

    public function run(){
        $txtDistances = "";
        $txtAns = "";

        $c = count($this->distances);
        for($i=0;$i<$c;$i++){
            $this->answer[] = chr(65 + $i);
        }

        while(count($this->distances) > 1){
            $txtDistances .= "\n------------\n" . $this->printMatrix($this->distances) . "\n------------\n";
            $union = $this->getUnionNodes();
            $dep = $this->answer[$union["f"]] . $this->answer[$union["g"]];
            $value = $union["value"];
            $txtAns .= "Se unen: " . $this->answer[$union["f"]] . " con " .  $this->answer[$union["g"]] . " y forma: $dep" . " valor: $value\n";
            delete_row($this->answer, $union["f"]);
            delete_row($this->answer, $union["g"]-1);
            array_unshift($this->answer, "$dep");
            $this->getNewDistances($union["f"], $union["g"]);
        }

        $txtAns .= "El ordenamiento optimo es: " . implode("", array_reverse($this->answer)) . "\n";
        $txtDistances .= "\n---------------Resultado----------\n" . $txtAns;
        return ["ds" => $txtDistances];
    }

    public function getUnionNodes()
    {
        $distance = $this->distances;
        $n = count($distance);
        $min = INF;
        $mi = -1;
        $mj = -1;

        for($i=0;$i<$n;$i++){
            for($j=$i;$j<$n;$j++){
                if($distance[$i][$j] == 0)
                    continue;

                if($distance[$i][$j] < $min){
                    $mi = $i;
                    $mj = $j;
                    $min = $distance[$i][$j];
                }
            }
        }

        return ["f" => $mi, "g" => $mj, "value" => $min];
    }

    public function distanceFromSequences($sequences)
    {
        $utils = new Utils();

        $size = count($sequences);
        $score = array();
        $answer = array();
        $distances = array();

        for($i=0;$i<$size;$i++){
            for($j=0;$j<$size;$j++){
                $distances[$i][$j] = 0;
            }
        }

        for ($i = 0; $i < $size; $i++) {
            $answer[$i] = array();
            $score[$i] = array();

            for ($j = $i; $j < $size; $j++) {
                if ($i == $j) {
                    $score[$i][$j] = 0;
                    $distances[$i][$j] = "-";
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

        echo $this->printMatrix($distances);

        $this->distances = $distances;
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

        for($j=1;$j<$newn;$j++){
            $i = $keys[$j-1];
            $t = round($this->calcDist($f, $g, $i),2);
            $nDistances[0][$j] = $t;
            $nDistances[$j][0] = $t;
        }

        $nDistances[0][0] = 0;

        $this->distances = $nDistances;
    }

    public function calcDist($f, $g, $i){
        switch ($this->type){
            case 0:
                return min($this->distances[$f][$i] ,$this->distances[$g][$i]);
            case 1:
                return max($this->distances[$f][$i] ,$this->distances[$g][$i]);
            case 2:
                return ($this->distances[$f][$i] + $this->distances[$g][$i])/2;
        }
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