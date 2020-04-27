<?php

require 'Utils.php';
require 'Neighbor.php';

$filename = "1111";
$sequences = [
    "CAAATGCAGGGACACCACA",
    "TTCTGGGTCACGGTGCTAGGGG",
    "AGCAGCAATGGTGGTTTCCCTGCTTGGGAGCC",
    "ATTTTCCCTATTCATCCAGGGAAAATGTTATGTT",
    "TGAAGAGTATGGACCAACATTAAAGAAAGCACACCAC"
];

$distances = [];

$neighbor = new Neighbor($distances);
$neighbor->distanceFromSequences($sequences);

$ans = $neighbor->run();

$utils = new Utils();

$utils->createFileStar($filename, "distances", $ans["ds"]);
$utils->createFileStar($filename, "qs", $ans["qs"]);