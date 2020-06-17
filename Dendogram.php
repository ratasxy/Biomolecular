<?php

class NodeDendo {
    public $name;
    public $a;
    public $avalue;
    public $b;
    public $bvalue;
    public $value;

    public function __construct($name, $a, $b, $value)
    {
        $this->name = $name;
        $this->a = $a;
        $this->b = $b;
        $this->value = $value;
    }

}

class Dendogram {
    public $nodes;
    public $head;

    public function __construct()
    {
    }

    public function add($a, $b, $value){
        echo "Uniendo $a con $b y valor $value\n";
        $name = $a . $b;
        $ac = $this->obtain($a);
        $bc = $this->obtain($b);

        $tmp = new NodeDendo($name, $ac, $bc, $value);
        $tmp->avalue = round($value/2,2);
        $tmp->bvalue = round($value/2, 2);

        $this->nodes[$name] = $tmp;

        if(!$this->isTerminal($a))
            $tmp->avalue = abs( ($ac->value/2)  - $value/2);
            unset($this->nodes[$a]);

        if(!$this->isTerminal($b))
            $tmp->bvalue = abs( ($bc->value/2)  - $value/2);
            unset($this->nodes[$b]);

        $this->head = $this->nodes[$name];
    }

    public function obtain($a){
        if($this->isTerminal($a)){
            return $a;
        }else{
            if(isset($this->nodes[$a]))
                return $this->nodes[$a];
        }
    }

    public function isTerminal($a){
        if(strlen($a) == 1)
            return true;

        return false;
    }

    public function getUnion($a, $b){
        /** @var NodeDendo $current */
        $current = $this->head;

        while (true){
            if($this->isOnNode($current->a, $a) && $this->isOnNode($current->a, $b)) {
                $current = $current->a;
                continue;
            }
            if($this->isOnNode($current->b, $a) && $this->isOnNode($current->b, $b)){
                $current = $current->b;
                continue;
            }
            return $current->value;
        }
    }

    public function isOnNode($node, $a){
        if(strpos($node->name, $a) !== false)
            return true;

        return false;
    }


    public function toDot(){
        echo "**************************\n";
        $ans =  "digraph Result {\n";
        $ans .= $this->toDoI($this->head);
        $ans .= "}\n";

        echo $ans;
        return $ans;
    }

    public function toDoI($n){

        $t = "";

        if(!is_object($n))
            return "";

        $t .= $n->name . " -> ";

        if(!is_object($n->a))
            $t .= $n->a;
        else
            $t .= $n->a->name;
        $v = $n->avalue;
        $t .= " [ label=\"$v\" ]\n";


        $t .= $n->name . " -> ";
        if(!is_object($n->b))
            $t .= $n->b;
        else
            $t .= $n->b->name;
        $v = $n->bvalue;
        $t .= " [ label=\"$v\" ]\n";

        $t .= $this->toDoI($n->a);
        $t .= $this->toDoI($n->b);

        return $t;
    }
}