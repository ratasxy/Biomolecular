<?php

require 'Utils.php';
require 'Cluster.php';

$filename = "2000";
$sequences = [
    "TTCTGGGTCACGGTGCTAGGGG",
    "ATTTTCCCTATTCATCCAGGGAAAATGTTATGTT",
    "TTTGTGAAGGCTTCGCAGGTACGTGAAAACCCATCCG",
    "AATTTTTGTTGTCAAAGCAGCTTCCTAATGGAGGGTGGGGAG",
    "TGTTGTCACTTATGCGTGCTGGGCAGGCTGAGATAGATCCGACAC",
];

$distances = [];

$cluster = new Cluster($distances, 0);
$cluster->distanceFromSequences($sequences);
$ans = $cluster->run();

$utils = new Utils();
$utils->createFileStar($filename, "distances", $ans["ds"]);