<?php

#Required values to work

if(!$_REQUEST['cep'])
	exit;
	
require_once('../../lib-correios.php');

$cep = new LibCorreios();

$end=($cep->getCepAddress($_REQUEST['cep']) );

echo 'Endereço: '.$end->logradouro;'\n\n';
echo 'Bairro: '.$end->bairro;'\n';
echo 'Cidade: '.$end->cidade;'\n';
echo 'Estado: '.$end->estado;'\n';
echo 'CEP: '.$end->bairro;'\n';

?>