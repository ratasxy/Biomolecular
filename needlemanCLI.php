<?php

require 'Needleman.php';
require 'Utils.php';

$needleman = new Needleman();
$utils = new Utils();

echo "Ingrese secuencia A: ";
$sequenceA = $line = trim(fgets(STDIN));
echo "Ingrese secuencia B: ";
$sequenceB = $line = trim(fgets(STDIN));

//$sequenceA = 'HEAGAWGHEE';
//$sequenceB = 'PAWHEAE';

echo "\nEjecutando algoritmo: \n";

$needleman->setSequences($sequenceA, $sequenceB);
$needleman->compute();
$utils->createFile($sequenceA, $sequenceB, 'matrix-needleman', $needleman->printMatrix());
$answer = $needleman->trace();
echo "Total de respuestas: " . count($answer) . "\n";
$utils->createFile($sequenceA, $sequenceB, 'answers-needleman', $utils->resultsToText($answer));
$best = $utils->getBest($answer);
echo "Mejor respuesta: \n\t" . $best['A'] . "\n\t" . $best['B'] . "\n";
die("\nFIN\n\n");