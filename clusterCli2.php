<?php

require 'Utils.php';
require 'Cluster.php';

$filename = "tarea2";
$sequences = [
    "TTCTGGGTCACGGTGCTAGGGG",
    "ATTTTCCCTATTCATCCAGGGAAAATGTTATGTT",
    "TTTGTGAAGGCTTCGCAGGTACGTGAAAACCCATCCG",
    "AATTTTTGTTGTCAAAGCAGCTTCCTAATGGAGGGTGGGGAG",
    "TGTTGTCACTTATGCGTGCTGGGCAGGCTGAGATAGATCCGACAC",
    "ACAAAATCCTGATGGGTCTTGGTATGGTTGCTGGGGAATTTGCTACACT",
    "ACATAGAGGGTCATAGTACCATGTTTGGATCTGCATTGAGCTACATTGCA",
    "AGGATGAGACCACTAGGTACCTTTGCATTGGAAGTGTAGAGAAGGTGTTATA",
    "TTTATGAGTGGTCAGGGTGCAATCCCCTTCCACCAGAGTTCTGGCTTCTACCCAAA",
    "GCAGGCCCATTATTCTTCGTTCAACCTTTGGTAATGGCACTGTACATTACAGGATCCCTTGA",
    "TCTTTGAAGAAACTCTGATTGAGAGGGAGTATGTAGAGTGCACTGGTTCAGCAATGCAAGCCCTGGC",
    "TGTAGGCCCAATCACTGCCTTAGTCAGATCACTAAGAAAAGAATTGTACAATGAGCCTTATGATCGAG",
];

$distances = [];

$cluster = new Cluster($distances, 2);
$cluster->distanceFromSequences($sequences);
$ans = $cluster->run();

echo "--------------Dendograma:----------";
#var_dump($cluster->dendogram);
$cluster->dendogram->toDot();
echo "----------------------------------";

$cluster->copheneticMatrix();

$cop = "\n---------COPHENETIC MATRIX----------\n" . $cluster->printMatrix($cluster->cophenetic) . "\n-----------------\n";
$cop .= "CCC: " . $cluster->calculateCCC() . "\n";

$ans["ds"] .= $cop;
echo $cop;

$utils = new Utils();
$utils->createFileStar($filename, "distances", $ans["ds"]);