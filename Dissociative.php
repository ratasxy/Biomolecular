<?php

require 'Needleman.php';
require_once 'Utils.php';

class Dissociative {
    private $distances;
    private $type;
    private $answer;
    private $clusters;

    public function __construct($distances, $type)
    {
        $this->distances = $distances;
        $this->type = $type;
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

    public function run(){
        $c = count($this->distances);
        for($i=0;$i<$c;$i++){
            $this->answer[] = chr(65 + $i);
        }
        echo "Se inicia \n";
        $txtDistances = "";

        while(count($this->distances) > 0){
            $txtDistances .= "\n------------\n" . $this->printMatrix($this->distances) . "\n------------\n";
            $info = $this->calculeMax();
            $pivot = $info["maxi"];
            $similars = $this->calculeSimilar($info["mins"], $pivot);
            $cs = count($similars);

            $txtpans = "Se divide " . $this->answer[$pivot];
            $clusp = $this->answer[$pivot];
            if($cs > 0){
                $txtpans .= " y arrastra a otros y forma el cluster: " . $this->answer[$pivot];
                for($i=0; $i<$cs; $i++){
                    $txtpans .= $this->answer[$similars[$i]];
                    $clusp .= $this->answer[$similars[$i]];
                }
            }
            $txtpans .= "\n";

            echo $txtpans;

            delete_row($this->distances, $pivot);
            delete_col($this->distances, $pivot);
            delete_row($this->answer, $pivot);

            for($i=0; $i<$cs; $i++){
                delete_row($this->distances, $i);
                delete_col($this->distances, $i);
                delete_row($this->answer, $i);
            }

            $this->clusters[] = $clusp;

            echo $this->printMatrix($this->distances);
        }
        $ans = "El resultado es: " . $this->textAnswer();
        $txtDistances .= "\n---------------Resultado----------\n" . $ans;
        echo $ans;

        return $txtDistances;
    }

    public function textAnswer(){
        $cc = count($this->clusters);
        $clusters = $this->clusters;

        $txt = "";
        for($i=0; $i<$cc; $i++){
            if($i == 0){
                $txt .= $clusters[$i];
                continue;
            }
            $txt .= " - $clusters[$i]";
        }

        $txt .= "\n";

        return $txt;
    }

    public function calculeSimilar($infoMins, $pivot){

        $distance = $this->distances;
        $n = count($distance);

        $similars = array();

        for($i=0; $i<$n; $i++){
            if($i==$pivot)
                continue;

            $min = $infoMins[$i]['min'];
            $dist = $distance[$pivot][$i];
            $a = $min - $dist;
            if(($min - $dist) > 0)
                $similars[] = $i;
        }

        return $similars;
    }

    public function calculeMax(){
        $distance = $this->distances;
        $n = count($distance);
        $mins = array();

        $max = -INF;
        $maxi = -1;
        for($i=0; $i<$n; $i++){
            $min = INF;
            $minj = -1;
            for($j=0; $j<$n; $j++){
                if($i == $j)
                    continue;

                if($this->distances[$i][$j] < $min){
                    $min = $this->distances[$i][$j];
                    $minj = $j;
                }
            }
            $mins[$i] = ["minj" => $minj, "min" => $min];

            if($min > $max){
                $max = $min;
                $maxi = $i;
            }
        }

        return [
          "maxi" => $maxi,
          "max" => $max,
          "mins" => $mins
        ];
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
}

function delete_row(&$array, $offset) {
    return array_splice($array, $offset, 1);
}

function delete_col(&$array, $offset) {
    return array_walk($array, function (&$v) use ($offset) {
        array_splice($v, $offset, 1);
    });
}