<?php
	function traza($msg){
	    $nombre_file = "traza.txt";
        $gestor = fopen($nombre_file,"a+");
        $msg .= "\n";
        fputs($gestor,$msg);
        fclose($gestor);
    }
?>