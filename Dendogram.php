<?php

class NodeDendo {
    public $name;
    public $a;
    public $b;
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
        $name = $a . $b;
        $ac = $this->obtain($a);
        $bc = $this->obtain($b);

        $tmp = new NodeDendo($name, $ac, $bc, $value);

        $this->nodes[$name] = $tmp;

        if(!$this->isTerminal($a))
            unset($this->nodes[$a]);

        if(!$this->isTerminal($b))
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
                echo "entro a\n";
                $current = $current->a;
                continue;
            }
            if($this->isOnNode($current->b, $a) && $this->isOnNode($current->b, $b)){
                $current = $current->b;
                echo "entro a\n";
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
}