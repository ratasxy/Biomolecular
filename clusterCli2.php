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

$utils = new Utils();
$utils->createFileStar($filename, "distances", $ans["ds"]);