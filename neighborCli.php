<?php

require 'Utils.php';
require 'Neighbor.php';

$filename = "distances4.in";

$utils = new Utils();
$distances = $utils->readFile($filename);

$neighbor = new Neighbor($distances);
$neighbor->run();
