<?php

class Additive{

    public $distance;
    public $steps;
    private $answer;
    private $initialN;

    public function __construct($distance)
    {
        $this->distance = $distance;
        $this->steps = [];
    }

    public function run(){
        $n = count($this->distance);
        $this->initialN = $n;

        for($i=0;$i<$n;$i++){
            $this->answer[] = chr(65 + $i);
        }

        while(count($this->distance) > 2){
            echo "------ACTUAL-----\n";
            echo $this->printMatrix($this->distance);
            echo "******ACTUAL*****\n";
            $m = $this->getMin();
            echo "Min: " . $m["min"] . "\n";
            $gamma = $this->getGamma($m["min"]);
            $less = $gamma * 2;
            echo "Gamma: $gamma\n";
            $this->lessGamma($less);
            echo $this->printMatrix($this->distance);
            $transitive = $this->getTransitive();
            print_r($transitive);

            $this->steps[] = [
                "distance" => $this->distance,
                "answer" => $this->answer,
                "gamma" => $gamma,
                "k" => $transitive["k"],
                "i" => $transitive["i"],
                "j" => $transitive["j"]
            ];

            //Remove col and file k
            delete_col($this->distance, $transitive["k"]);
            delete_row($this->distance, $transitive["k"]);
            delete_row($this->answer, $transitive["k"]);
        }
        echo "------FINAL-----\n";
        echo $this->printMatrix($this->distance);
        echo "******FINAL*****\n";
    }

    public function backTrack(){
        echo "Obteniendo nodos iniciales\n";
        $s = $this->answer[0];
        $e = $this->answer[1];
        $v = $this->distance[0][1];
        echo "\n\n($s)-----$v-----($e)\n\n";

        $graph = $this->emptyGraph();

        $os = $this->getId($s);
        $oe = $this->getId($e);
        $graph[$os][$oe] = $v;
        $graph[$oe][$os] = $v;

        echo $this->printMatrix($graph);

        $y = $this->initialN;
        $current = array_pop($this->steps);
        while ($current){
            $k = $current["k"];
            $i = $current["i"];
            $j = $current["j"];
            $kc = $current["answer"][$k];
            $ic = $current["answer"][$i];
            $jc = $current["answer"][$j];
            $ok = $this->getId($kc);
            $oi = $this->getId($ic);
            $oj = $this->getId($jc);

            $v1 = $current["distance"][$i][$k];
            $v2 = $current["distance"][$j][$k];
            echo "Agregando $kc\n";

            //Removing direct path
            $graph[$oi][$oj] = 0;
            $graph[$oj][$oi] = 0;

            //Adding removed path
            $graph[$oi][$y] = $v1;
            $graph[$y][$oi] = $v1;

            $graph[$oj][$y] = $v2;
            $graph[$y][$oj] = $v2;

            //Increase Gamma
            $graph = $this->increaseGamma($graph, $current["gamma"]);

            //Adding new node
            $graph[$ok][$y] = $current["gamma"];
            $graph[$y][$ok] = $current["gamma"];

            echo "-------BACK------\n";
            echo $this->printMatrix($graph);
            echo "*******BACK******\n";

            $current = array_pop($this->steps);
            $y++;
        }
    }

    public function increaseGamma($graph, $gamma){
        $n = count($graph);
        for($i=0;$i<$n;$i++){
            for($j=0;$j<$n;$j++){
                if($i >= $this->initialN && $j >= $this->initialN)
                    continue;

                if($graph[$i][$j] == 0)
                    continue;

                $graph[$i][$j] += $gamma;
            }
        }

        return $graph;
    }

    public function getId($c){
        return ord($c) - 65;
    }

    public function emptyGraph(){
        $nsteps = count($this->steps);
        $graph = [];

        $s = $nsteps + $this->initialN;

        for($i=0;$i<$s;$i++){
            $graph[$i] = [];
            for($j=0;$j<$s;$j++) {
                $graph[$i][$j] = 0;
            }
        }

        return $graph;
    }

    public function getTransitive(){
        $n = count($this->distance);

        for($i=0; $i<$n; $i++){
            for($j=0; $j<$n; $j++){
                if($i == $j)
                    continue;
                for($k=0; $k<$n; $k++){
                    if($i == $k || $j == $k)
                        continue;

                    if($this->distance[$i][$k] + $this->distance[$k][$j] == $this->distance[$i][$j] ){
                        return ["i" => $i, "j" => $j, "k" => $k];
                    }

                }
            }

            die("Error: No se encontro transitividad.\n");
        }
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

    public function lessGamma($gamma){
        $n = count($this->distance);
        for($i = 0; $i<$n; $i++){
            for($j = 0; $j<$n; $j++){
                if($i==$j)
                    continue;

                $this->distance[$i][$j] -= $gamma;
            }
        }
    }

    public function getGamma($n){
        return floor(($n-1)/2);
    }

    public function getMin(){
        $min = INF;
        $n = count($this->distance);
        $mi = 0;
        $mj = 0;

        for($i = 0; $i<$n; $i++){
            for($j = 0; $j<$n; $j++){
                if($i==$j)
                    continue;

                if($min > $this->distance[$i][$j]){
                    $mi = $i;
                    $mj = $j;
                    $min = $this->distance[$i][$j];
                }
            }
        }

        return ["mi" => $mi, "mj" => $mj, "min" => $min];
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