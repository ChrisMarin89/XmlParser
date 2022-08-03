# XmlParser
Simply way to parse an XML with Namespaces, Attributes, etc thanks to a json template

You can just open the TryItYouserlf.php file to verify that the entire XML example is being readed with always the same foreach loop in all you future functions.

Give to the json all the namespaces that XML is using.

You can read as many nodes values as Fields nodes you add in the json template

The entire criteria is being uploaded into a json file where you can decide
  table_field -> A relationShip with your app,
	value_source -> xpath or fixed,
	value_type -> string, date,... (only date has an internal validation given a format) 
	value -> ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ar:Invoice/ar:OracleTransferDocHeaderExtAttrExtension/ar:ValueText[@typeCode='Delivery Channel'],
	mandatory -> true / false,
	allow_more_values -> true / false

Visit http://xpather.com/ and upload there an example of the xml you need to read in order to see all the xpath. You can also add the criteria of value source fixed in case you want to send always the same parameter.
