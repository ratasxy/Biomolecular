<?php

require 'Utils.php';
require 'Dissociative.php';

$filename = "tarea1";
$sequences = [
    "CAAATGCAGGGACACCACA",
    "TTCTGGGTCACGGTGCTAGGGG",
    "AGCAGCAATGGTGGTTTCCCTGCTTGGGAGCC"
];

$distances = [];

$cluster = new Dissociative($distances, 1);
$cluster->distanceFromSequences($sequences);
$ans = $cluster->run();

$utils = new Utils();
$utils->createFileStar($filename, "distances", $ans);