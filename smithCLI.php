<?php

require 'Smith.php';
require 'Utils.php';

$smith = new Smith();
$utils = new Utils();

echo "Ingrese secuencia A: ";
$sequenceA = $line = trim(fgets(STDIN));
echo "Ingrese secuencia B: ";
$sequenceB = $line = trim(fgets(STDIN));

//$sequenceA = 'HEAGAWGHEE';
//$sequenceB = 'PAWHEAE';

echo "\nEjecutando algoritmo: \n";

$smith->setSequences($sequenceA, $sequenceB);
$smith->compute();
$utils->createFile($sequenceA, $sequenceB, 'matrix-smith', $smith->printMatrix());
$smith->getMax();
$answer = $smith->trace();
echo "Total de respuestas: " . count($answer) . "\n";
$utils->createFile($sequenceA, $sequenceB, 'answers-smith', $utils->resultsToText($answer));
$best = $utils->getBest($answer);
echo "Mejor respuesta: \n\t" . $best['A'] . "\n\t" . $best['B'] . "\n";
die("\nFIN\n\n");


