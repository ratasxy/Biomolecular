<?php

require 'Smith.php';
require 'Utils.php';

$smith = new Smith();
$utils = new Utils();

//echo "Ingrese secuencia A: ";
//$sequenceA = $line = trim(fgets(STDIN));
//echo "Ingrese secuencia B: ";
//$sequenceB = $line = trim(fgets(STDIN));

$sequenceA = 'PAWHEAE';
$sequenceB = 'HEAGAWGHEE';

echo "\nEjecutando algoritmo: \n";

$smith->setSequences($sequenceA, $sequenceB);
$smith->compute();
$utils->createFile($sequenceA, $sequenceB, 'matrix-smith', $smith->printMatrix());
$smith->getMax();
print_r($smith->trace());
