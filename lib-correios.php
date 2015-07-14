<?php

class LibCorreios
{
	private $mcurl;
	private $agent;
	private $url_get_cep;
	private $url_get_address;


	function __construct()
	{
		#Randomly changes the user-agent so we can fool the server verification
		
		if((time()%2)==0)
			$this->agent = 'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14';
		else
			$this->agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12';
		
		$this->url_get_cep = 'http://www.buscacep.correios.com.br/servicos/dnec/consultaLogradouroAction.do';
		$this->url_get_address = 'http://www.buscacep.correios.com.br/servicos/dnec/consultaLogradouroAction.do';
		
	}
	
	private function setConnect($type)
	{
		#type 1 = get CEP by Address
		#type 2 = get Address by Cep
		
		if($type==1)
			$this->mcurl = curl_init($this->url_get_cep);
		else if
			($type==2)
			$this->mcurl = curl_init($this->url_get_address);
			
		curl_setopt($this->mcurl, CURLOPT_POST, 1);
		curl_setopt($this->mcurl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($this->mcurl, CURLOPT_HEADER, 0);
		curl_setopt($this->mcurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->mcurl, CURLOPT_USERAGENT, $this->agent);
		
		
	}
	
	public function getCepList($uf, $logradouro, $localidade, $tipo= false, $numero=false, $inicio=0, $fim=10)
	{
		$this->setConnect(1);
		$postvars  ='UF='.$uf; #state initials
		
		$postvars .='&Localidade='.$localidade; #same as CITY
		$postvars .='&Metodo=listaLogradouro'; #search using address
		$postvars .='&TipoConsulta=logradouro'; 
		$postvars .='&StartRow='.$inicio; #starting row
		$postvars .='&EndRow='.$fim; #ending row
		
		/*
		* $tipo refers to the location type and can assume the following values:
		*
		* Rua, Outros, Aeroporto, Alameda, Área, Avenida, Campo, Chácara, Colônia, 
	¨	* Condomínio, Conjunto, Distrito, Esplanada, Estação, Estrada, Favela, Fazenda
		* Feira, Jardim, Ladeira, Lago, Lagoa, Largo, Loteamento, Morro, Núcleo, Parque
		* Passarela, Pátio, Praça, Quadra, Recanto, Residencial, Rodovia, Rua, Setor
		* Sitio, Travessa, Trecho, Vale, Vereda, Via, Viaduto, Viela, Vila.
		*/
		
		if($tipo)
			$postvars .='&Tipo='.$tipo;
			
		#Location number
		if($numero)
			$postvars .='&Numero='.$numero;
		
		curl_setopt($this->mcurl, CURLOPT_POSTFIELDS, $postvars);
		$res = curl_exec($this->mcurl);
		
		#Getting only the HTML table...
		
		$res = explode("<?xml version = '1.0' encoding = 'ISO-8859-1'?>", $res);
		$res = explode('<table width="645">', $res[1]);
		
		$contents= $res[0];
		
		$DOM = new DOMDocument;
		$DOM->loadHTML($contents);
		
		#...to turn it into an Array...
		
		
		$items = $DOM->getElementsByTagName('tr');
		
		foreach ($items as $node)
		{
			$in = 0;
			foreach ($node->childNodes as $element)
			{
				switch($in)
					{
						case 0: $index = 'logradouro'; break;
						case 2: $index = 'bairro'; break;
						case 4: $index = 'cidade'; break;
						case 6: $index = 'estado'; break;
						case 8: $index = 'cep'; break;
						default: $index=false; break;
						
						
					}
					if($index)
					$str[$index]= $element->nodeValue;
				
				$in++;
			}
			
			$rows[]= $str;
			unset($str);
		}
		
		#... and then return it as json
		
		return $rows;
		
	}
	
	public function getCepListJson($uf, $logradouro, $localidade, $tipo= false, $numero=false, $inicio=0, $fim=10)
	{
	
		return json_encode($this->getCepList($uf, $logradouro, $localidade, $tipo= false, $numero=false, $inicio=0, $fim=10));
		
		
	}
	
	public function getCepAddress($cep, $inicio=0, $fim=10)
	{
		$postvars  ='CEP='.$cep; #state initials
		
		
		$postvars .='&Metodo=listaLogradouro'; #search using address
		$postvars .='&TipoConsulta=cep'; 
		$postvars .='&StartRow='.$inicio; #starting row
		$postvars .='&EndRow='.$fim; #ending row
		$this->setConnect(2);
		
		curl_setopt($this->mcurl, CURLOPT_POSTFIELDS, $postvars);
		$res = curl_exec($this->mcurl);
		
		
		
		#Getting only the HTML table...
		
		$res = explode("<?xml version = '1.0' encoding = 'ISO-8859-1'?>", $res);
		$res = explode('<table width="645">', $res[1]);
		
		$contents= $res[0];
		
		$DOM = new DOMDocument;
		$DOM->loadHTML($contents);
		
		#...to turn it into an Array...
		
		
		$items = $DOM->getElementsByTagName('tr');
		
		
		
		foreach ($items as $node)
		{
			$in = 0;
			foreach ($node->childNodes as $element)
			{
				switch($in)
					{
						case 0: $index = 'logradouro'; break;
						case 2: $index = 'bairro'; break;
						case 4: $index = 'cidade'; break;
						case 6: $index = 'estado'; break;
						case 8: $index = 'cep'; break;
						default: $index=false; break;
						
						
					}
					if($index)
					@$str->$index= $element->nodeValue;
				
				$in++;
			}
			
			
		}
		
		#... and then return it as json
		
		return $str;
		
		
		
	}
	
	public function getCepListXML($uf, $logradouro, $localidade, $tipo= false, $numero=false, $inicio=0, $fim=10)
	{
	
		#Still working on it
		
		
	}
	
	private function arrayToXML($myAray)
	{
		
		#Still working on it
		
		
		
	}
	
	
}
