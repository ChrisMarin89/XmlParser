<?php
	
	require_once('XmlParser.php');
	
	$xml_location = 'example' . DIRECTORY_SEPARATOR . 'ubl_invoice.xml';
	$config_location = 'templates' . DIRECTORY_SEPARATOR . 'ubl_template.json';
	$parser = new iDocsXMLParser($xml_location, $config_location, ';');
	
	foreach($parser->getKeys() as $key){
		echo '----------' . $key . '----------<br>';
		echo $parser->getValue($key) . '<br>';
		echo $parser->getTableField($key) . '<br>';
		echo $parser->isMandatory($key) . '<br>';
		echo $parser->allowMultipleValues($key) . '<br>';
	}
	
?>