<?php

require 'Utils.php';
require 'Neighbor.php';

$filename = "1113";
$sequences = [
    "TGAAGAGTATGGACCAACATTAAAGAAAGCACACCAC",
    "TTTGTGAAGGCTTCGCAGGTACGTGAAAACCCATCCG",
    "ATGTGGAAGTTGAAGGTAGCAGAAGGAGGAAAAGGGT",
    "AATTTTTGTTGTCAAAGCAGCTTCCTAATGGAGGGTGGGGAG",
    "TCAAAGAGCCTACCAATGGTTAGAGAAATTCAATCCAACTGAAT",
    "TGTTGTCACTTATGCGTGCTGGGCAGGCTGAGATAGATCCGACAC",
    "TGGTTTCTGTGAGCAATTTCATCGGAAGGCAACACTGGGTGTTCGACC",
    "ACAAAATCCTGATGGGTCTTGGTATGGTTGCTGGGGAATTTGCTACACT"
];

$distances = [];

$neighbor = new Neighbor($distances);
$neighbor->distanceFromSequences($sequences);

$ans = $neighbor->run();

$utils = new Utils();

$utils->createFileStar($filename, "distances", $ans["ds"]);
$utils->createFileStar($filename, "qs", $ans["qs"]);