<?php

require 'Utils.php';
require 'Additive.php';

$filename = "additive.txt";

$utils = new Utils();
$distances = $utils->readFile($filename);

$additive = new Additive($distances, 2);
$ans = $additive->run();
$additive->backTrack();