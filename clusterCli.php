<?php

require 'Utils.php';
require 'Cluster.php';

$filename = "upg.txt";

$utils = new Utils();
$distances = $utils->readFile($filename);

$cluster = new Cluster($distances, 2);
$ans = $cluster->run();

echo "--------------Dendograma:----------";
var_dump($cluster->dendogram);
echo "----------------------------------";

$cluster->copheneticMatrix();

$cop = "\n---------COPHENETIC MATRIX----------\n" . $cluster->printMatrix($cluster->cophenetic) . "\n-----------------\n";
$cop .= "CCC: " . $cluster->calculateCCC() . "\n";

$ans["ds"] .= $cop;
echo $cop;
$utils->createFileStar($filename, "distances", $ans["ds"]);