<?php
	/*
	*	Christian Marín
	*	Class to Parse XMLs
	*/
	
	class iDocsXmlParser{
		
		/**Internal Vars**/
		private $_config;
		private $_xml;
		private $_xpath;
		private $_valuesSeparator;
		
		/**iDocs Vars**/
		private $pathToTemplates;
		
		
		public function __construct($xml_path, $config_path, $separator = ','){
			
			$config = file_get_contents($config_path);
			$this->_config = json_decode($config, true);
			$this->_xml = new DomDocument();
			$this->_xml->load($xml_path);
			$this->_xpath = new DOMXPath($this->_xml);
			$this->_valuesSeparator = $separator;
			
			foreach ($this->_config['namespaces'] as $ns => $url){
				$this->_xpath->registerNamespace($ns, $url);
			}
		}
		
		public function getValue($key){
			
			$field = $this->_config['fields'][$key];
			if($field['value_source'] == 'xpath'){
				$values = $this->_xpath->query($field['value']);
				$str_result = '';
				if($this->isMandatory($key) && count($values) == 0) throw new Exception('Error: Mandatory field ' . $field['table_field'] . ' empty.');
				if(!$this->allowMultipleValues($key) && count($values) > 1) throw new Exception('Error: Too many values for field ' . $field['table_field'] . '.');
				foreach ($values as $entry) {
					if($field['value_type'] == 'date'){
						$value = $this->validationDate($entry->nodeValue, $field['value_format']);
					}elseif($field['value_type'] == 'flag'){
						$value = $this->validationFlag($entry->nodeValue);
					}else $value = $entry->nodeValue;
					$str_result .= $value . $this->_valuesSeparator;
				}
				$str_result = substr($str_result, 0, strlen($str_result)-1);
				return $str_result;
			}elseif($field['value_source'] == 'fixed'){
				return $field['value'];
			}
		}		

		public function getTableField($key){
			
			$field = $this->_config['fields'][$key];
			return $field['table_field'];
		}
		
		public function isMandatory($key){
			
			$field = $this->_config['fields'][$key];
			if ($field['mandatory'] == 'true'){
				return 1;
			}
			return 0;
		}

		public function allowMultipleValues($key){
			
			$field = $this->_config['fields'][$key];
			if ($field['allow_more_values'] == 'true'){
				return 1;
			}
			return 0;
		}

		public function getKeys(){
			
			$keys = array();
			foreach ($this->_config['fields'] as $order => $object){
				$keys[] = $order;
			}
			return $keys;
		}
		
		private function validationDate($string_date, $format){
			
			//find the position of each value
			$day_position = strpos($format,"DD");
			$month_position = strpos($format,"MM");
			$year_position = strpos($format,"YYYY");
			//get days, months and years split
			$day = intval(substr($string_date, $day_position, 2));
			$month = intval(substr($string_date, $month_position, 2));
			$year = intval(substr($string_date, $year_position, 4));
			
			//array of days on each month
			$days_on_months = array(
				"1" => "31",
				"2" => "29",
				"3" => "31",
				"4" => "30",
				"5" => "31",
				"6" => "30",
				"7" => "31",
				"8" => "31",
				"9" => "30",
				"10" => "31",
				"11" => "30",
				"12" => "31",
			);
			
			if(strlen($format) != strlen($string_date)){
				//echo 'wrong Format <br>';
				throw new Exception('Wrong date format for: '. $string_date .', expecting: ' . $format);
			}else{
				if($month > 12 or $month < 1){
					//echo 'wrong month <br>';
					throw new Exception('Wrong month it is not between 1-12.');
				}else{
					if($day > $days_on_months[$month] or $day < 1){
						//echo 'wrong day <br>';
						$month_name = new DateTime('2000-'.$month.'-01');
						throw new Exception('Date with more days than expected for month '.$month_name->format('M').', date format is: '. $format . '.'); 
					}
				}
				if($year < 1970 or $year > 2070){
					//echo 'wrong year <br>';
					throw new Exception('Date with unexpected years (expecting between 1970 and 2070), date format is: '. $format . '.');  
				}
			}
			if($month < 10){
				$month = '0' . $month;
			}
			return $year . '-' .$month. '-' . $day;
		}
		
		private function validationFlag($value){
			
			if ($value == 'Y'){
				return 1;
			}
			return 0;
		}
	}
?>