<?php

require 'Utils.php';
require 'Cluster.php';

$filename = "dist.txt";

$utils = new Utils();
$distances = $utils->readFile($filename);

$cluster = new Cluster($distances, 0);
$ans = $cluster->run();
$cluster->copheneticMatrix();

$cop = "\n---------COPHENETIC MATRIX----------\n" . $cluster->printMatrix($cluster->cophenetic) . "\n-----------------\n";
$cop .= "CCC: " . $cluster->calculateCCC() . "\n";

$ans["ds"] .= $cop;
echo $cop;
$utils->createFileStar($filename, "distances", $ans["ds"]);