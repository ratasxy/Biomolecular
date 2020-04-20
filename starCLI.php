<?php

require 'Star.php';

$sequences = array(
    'TTAGCATCG',
    'TAGCTCCCGAT',
    'TTTCGATCGTA',
    'TGGCATGCTAG',
    'TTGCTA'
);

$star = new Star();
$star->setSequences($sequences);
$star->calcule();

