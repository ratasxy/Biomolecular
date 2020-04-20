<?php

require 'Star.php';

$name = "2";
$sequences = array(
    "ATTGCCATT",
    "ATGGCCATT",
    "ATCCAATTTT",
    "ATCTTCTT",
    "ACTGACC"
);

$star = new Star();
$star->setSequences($sequences);
$star->calcule($name);

