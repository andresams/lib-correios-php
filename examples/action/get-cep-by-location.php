<?php

#Required values to work

if(!$_REQUEST['Logradouro'] or !$_REQUEST['UF'] or !$_REQUEST['Localidade'])
	exit;
	
require_once('../../lib-correios.php');

$cep = new LibCorreios();

$lista = $cep->getCepList($_REQUEST['UF'], $_REQUEST['Logradouro'], $_REQUEST['Localidade'] );

#generates a HTML table
$html = '<H1>Lista de CEPs</H1>';
$html .= '<TABLE border = "1">';
$html .='<TR>';
$html .='<TD>Logradouro</TD>';
$html .='<TD>Bairro</TD>';
$html .='<TD>Cidade</TD>';
$html .='<TD>Estado</TD>';
$html .='<TD>CEP</TD>';
$html .='</TR>';
for($i=0; $i< count($lista); $i++)
{
	$html .='<TR>';
	
	foreach($lista[$i] as $index => $value)
		$html .='<TD>'.$value.'</TD>';
	
	$html .='<TR>';
	
}
$html .='</TABLE>';


echo $html;


?>