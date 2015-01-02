<?php
### INCLUDE
	require_once('../lib/config.php');
	
### INPUTS
	$inREQUEST		= addslashes($_REQUEST['e']);

#VALIDACOES
	$REQUEST_FILE = 'index_'.$inREQUEST.'.php';

	if (!file_exists($REQUEST_FILE))
		die("ERRO: Parametro REQUEST invalido ($inREQUEST)");

#LOGICA
	include($REQUEST_FILE);
?>