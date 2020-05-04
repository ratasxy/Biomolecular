<?php

require 'Utils.php';
require 'Cluster.php';

$filename = "dist.txt";

$utils = new Utils();
$distances = $utils->readFile($filename);

$cluster = new Cluster($distances, 0);
$ans = $cluster->run();

$utils->createFileStar($filename, "distances", $ans["ds"]);