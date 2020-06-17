<?php

require 'Utils.php';
require 'Neighbor.php';

$filename = "upg.txt";

$utils = new Utils();
$distances = $utils->readFile($filename);

$neighbor = new Neighbor($distances);
$ans = $neighbor->run();
echo "--------------Dendograma:----------";
var_dump($neighbor->dendogram);
$neighbor->dendogram->toDot();
echo "----------------------------------";

$utils->createFileStar($filename, "distances", $ans["ds"]);
$utils->createFileStar($filename, "qs", $ans["qs"]);