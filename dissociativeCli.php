<?php

require 'Utils.php';
require 'Dissociative.php';

$filename = "dist.txt";

$utils = new Utils();
$distances = $utils->readFile($filename);

$cluster = new Dissociative($distances, 2);
$ans = $cluster->run();
$utils->createFileStar($filename, "distances", $ans);