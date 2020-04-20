<?php

require 'Needleman.php';
require 'Utils.php';

class Star {
    private $sequences;
    private $matrix;
    private $answers;
    private $pivot;

    public function setSequences($sequences){
        $this->sequences = $sequences;

        $this->matrix = array();
        $this->answers = array();
    }

    public function getPivot()
    {
        return $this->pivot;
    }

    public function calculeMatrix(){
        $utils = new Utils();

        $size = count($this->sequences);

        for ($i = 0; $i < $size; $i++) {
            $this->answers[$i] = array();
            $this->matrix[$i] = array();

            for ($j = 0; $j < $size; $j++) {
                $needleman = new Needleman();
                $needleman->setSequences($this->sequences[$i], $this->sequences[$j]);
                $needleman->compute();

                $this->matrix[$i][$j] = $needleman->getScore();
                $answer = $needleman->trace();
                $this->answers[$i][$j] = $utils->getBest($answer);


                if ($i == $j) {
                    $this->matrix[$i][$j] = 0;
                }
            }
        }

    }

    public function matrixToText()
    {
        $answer = "";
        $size = count($this->sequences);

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $answer .= $this->matrix[$i][$j] . "\t";
            }
            $answer .= "\n";
        }

        return $answer;
    }

    public function sumsToText(){
        $ans = "";

        $size = count($this->sequences);

        for ($i = 0; $i < $size; $i++) {
            $current = 0;
            for ($j = 0; $j < $size; $j++) {
                $current += $this->matrix[$i][$j];
            }
            $ans .= $current . "\t";
        }

        return $ans;
    }

    public function calculeMaxLine()
    {
        $line = -1;
        $max = -(INF);

        $size = count($this->sequences);

        for ($i = 0; $i < $size; $i++) {
            $current = 0;
            for ($j = 0; $j < $size; $j++) {
                $current += $this->matrix[$i][$j];
            }

            if($current > $max)
            {
                $line = $i;
                $max = $current;
            }
        }

        return $line;
    }

    public function align()
    {
        $pivot = $this->calculeMaxLine();
        $this->pivot = $pivot;

        $size = count($this->sequences);

        $answer = array();

        for ($i = 0; $i<$size; $i++)
        {
            if($i == $pivot)
            {
                continue;
            }

            if(empty($answer)){
                $answer[] = $this->answers[$pivot][$i]['A'];
                $answer[] = $this->answers[$pivot][$i]['B'];
                continue;
            }

            $answer[] = $this->answers[$pivot][$i]['B'];
        }

        $answer = $this->normalizeGap($answer);
        return $this->formatAlign($answer);
    }

    public function textAlignments()
    {
        $ans = "";
        $size = count($this->sequences);

        for ($i = 0; $i<$size; $i++)
        {
            $ans .= "=====================$this->pivot----$i=====================\n";
            $ans .= "A: " . $this->answers[$this->pivot][$i]["A"] . "\n";
            $ans .= "B: " . $this->answers[$this->pivot][$i]["B"] . "\n";
            $ans .= "\n";
        }

        return $ans;
    }

    public function normalizeGap($align){
        $ans = "";
        $size = count($align);
        $max = 0;
        for($i = 0; $i < $size; $i++)
        {
            $current = strlen($align[$i]);
            if($current > $max)
                $max = $current;
        }

        for($i = 0; $i < $size; $i++)
        {
            $align[$i] = $align[$i] . $this->generateGaps($max - strlen($align[$i]));
        }

        return $align;
    }

    public function generateGaps($n)
    {
        $ans = "";
        for($i=0;$i<$n;$i++){
            $ans .= "-";
        }
        return $ans;
    }

    public function formatAlign($align)
    {
        $ans = "";
        $size = count($align);
        for($i = 0; $i < $size; $i++)
        {
            $ans .= $align[$i] . "\n";
        }
        return $ans;
    }

    public function calcule($name)
    {
        $utils = new Utils();
        $this->calculeMatrix();
        $answer = $this->align();
        $data = $this->matrixToText();
        $data .= "-------------------------------------\n";
        $data .= $this->sumsToText() . "  ==== PIVOT: S" . ($this->pivot+1) . "\n";

        $utils->createFileStar($name, 'matrix', $data);

        $aligns = $this->textAlignments();
        $utils->createFileStar($name, 'aligns', $aligns);


        $utils->createFileStar($name, 'answer', $answer);

    }
}