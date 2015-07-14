<?php

#Required values to work

if(!$_REQUEST['Logradouro'] or !$_REQUEST['UF'] or !$_REQUEST['Localidade'])
	exit;
	
require_once('../../lib-correios.php');

$cep = new LibCorreios();

echo $cep->getCepListJson($_REQUEST['UF'], $_REQUEST['Logradouro'], $_REQUEST['Localidade'] );

?>