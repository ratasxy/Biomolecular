<?php

class Utils{
    public function createFile($seqA, $seqB, $suffix, $data){
        echo "Guardando matrix...";
        if(!file_exists(__DIR__ . '/output'))
            mkdir(__DIR__ . '/output');

        if(!file_exists(__DIR__ . "/output/$seqA-$seqB"))
            mkdir(__DIR__ . "/output/$seqA-$seqB");

        $filename = __DIR__ . "/output/$seqA-$seqB/$suffix.txt";

        file_put_contents($filename, $data);
        echo " HECHO\n";
    }
}