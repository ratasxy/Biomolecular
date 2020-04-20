<?php

class Utils{
    const pPenalty = 10;
    const qPenalty = 1;
    const kPenalty = 1;

    public function createFile($seqA, $seqB, $suffix, $data){
        echo "Guardando archivo...";
        if(!file_exists(__DIR__ . '/output'))
            mkdir(__DIR__ . '/output');

        if(!file_exists(__DIR__ . "/output/$seqA-$seqB"))
            mkdir(__DIR__ . "/output/$seqA-$seqB");

        $filename = __DIR__ . "/output/$seqA-$seqB/$suffix.txt";

        file_put_contents($filename, $data);
        echo " HECHO\n";
    }

    public function createFileStar($name, $suffix, $data){
        echo "Guardando archivo...";
        if(!file_exists(__DIR__ . '/output'))
            mkdir(__DIR__ . '/output');

        if(!file_exists(__DIR__ . "/output/$name"))
            mkdir(__DIR__ . "/output/$name");

        $filename = __DIR__ . "/output/$name/$suffix.txt";

        file_put_contents($filename, $data);
        echo " HECHO\n";
    }

    public function resultsToText($data){
        $ans = "";
        foreach ($data as $d){
            $ans .= $d['A'] . "\n" . $d['B'] . "\n\n";
        }

        return $ans;
    }

    public function calcPenalty($data){
        $a = $this->seqPenalty($data['A']);
        $b = $this->seqPenalty($data['B']);

        $groups = array_merge($a['gapGroups'], $b['gapGroups']);
        $lengths = array_map('strlen', $groups);

        $p = (count($lengths)) * Utils::pPenalty;
        $k = max($a['max'],$b['max']) * Utils::kPenalty;
        $q = (array_sum($lengths) - (count($lengths))) * Utils::qPenalty;

        return $p + ($q*$k);
    }

    public function getBest($data){
        $best = 0;
        $max = -999999999999999;

        $n = count($data);
        for($i=0;$i<$n;$i++){
            $tmp = $this->calcPenalty($data[$i]);
            if($tmp>$max){
                $best = $i;
                $max = $tmp;
            }
        }

        return $data[$best];
    }

    public function seqPenalty($txt){
        $sum = 0;
        $con = 0;
        $max = 0;
        $bef = "";

        $gapGroups = array();
        $seqGaps = "";

        $size = strlen($txt);

        for($i=0; $i<$size; $i++){
            if($txt[$i] == '-'){
                $sum++;
                $seqGaps .= '-';
                $con++;

            }else if($bef == '-'){
                $gapGroups[] = $seqGaps;
                $seqGaps = "";
                if($max < $con)
                    $max = $con;
                $con = 0;
            }

            $bef = $txt[$i];
        }

        if($bef == '-'){
            $gapGroups[] = $seqGaps;
            if($max < $con)
                $max = $con;
        }

        return array(
            "sum" => $sum,
            "max" => $max,
            "gapGroups" => $gapGroups
        );
    }
}