<?php

require 'Utils.php';
require 'Cluster.php';

$filename = "tarea";
$sequences = [
    "CAAATGCAGGGACACCACA",
    "TTCTGGGTCACGGTGCTAGGGG",
    "AGCAGCAATGGTGGTTTCCCTGCTTGGGAGCC"
];

$distances = [];

$cluster = new Cluster($distances, 1);
$cluster->distanceFromSequences($sequences);
$ans = $cluster->run();

$cluster->copheneticMatrix();

$cop = "\n---------COPHENETIC MATRIX----------\n" . $cluster->printMatrix($cluster->cophenetic) . "\n-----------------\n";
$cop .= "CCC: " . $cluster->calculateCCC() . "\n";

$ans["ds"] .= $cop;
echo $cop;

$utils = new Utils();
$utils->createFileStar($filename, "distances", $ans["ds"]);