<?php

require 'Needleman.php';

$needleman = new Needleman();

//echo "Ingrese secuencia A: ";
//$sequenceA = $line = trim(fgets(STDIN));
//echo "Ingrese secuencia B: ";
//$sequenceB = $line = trim(fgets(STDIN));

$sequenceA = 'ACTGATTCA';
$sequenceB = 'ACGCATCA';

echo "\nEjecutando algoritmo: \n";

$needleman->setSequences($sequenceB, $sequenceA);
$needleman->compute();
$needleman->printMatrix();
print_r($needleman->trace());

//$ans = $needleman->getTrace();

echo "\n\nUna de las cadenas con mejor score es:\n";
echo "Secuencia A: " . $ans["A"] . "\n";
echo "Secuencia B: " . $ans["B"] . "\n";