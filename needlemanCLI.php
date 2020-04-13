<?php

require 'Needleman.php';
require 'Utils.php';

$needleman = new Needleman();
$utils = new Utils();

//echo "Ingrese secuencia A: ";
//$sequenceA = $line = trim(fgets(STDIN));
//echo "Ingrese secuencia B: ";
//$sequenceB = $line = trim(fgets(STDIN));

$sequenceA = 'ATG';
$sequenceB = 'TTATGG';

echo "\nEjecutando algoritmo: \n";

$needleman->setSequences($sequenceA, $sequenceB);
$needleman->compute();
$utils->createFile($sequenceA, $sequenceB, 'matrix-needleman', $needleman->printMatrix());
print_r($needleman->trace());
