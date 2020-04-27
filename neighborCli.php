<?php

require 'Utils.php';
require 'Neighbor.php';

$filename = "distancia.txt";

$utils = new Utils();
$distances = $utils->readFile($filename);

$neighbor = new Neighbor($distances);
$ans = $neighbor->run();

$utils->createFileStar($filename, "distances", $ans["ds"]);
$utils->createFileStar($filename, "qs", $ans["qs"]);