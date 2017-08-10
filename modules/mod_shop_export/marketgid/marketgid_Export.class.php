<?php

class Marketgid_Export {

	var $available_options = array(
		'market_name', 'encoding', 'file_name', 'url', 'company_name',
	);
	
	var $categories_params = array(
		'id', 'parent', 'name',
	);
	
	var $currency_params = array(
		'name', 'value',
	);
	
	var $offers_params = array(
		'id', 'category', 'priority', 'click', 'img', 'vendor', 'type', 'name', 'price', 'currency', 'description',
	);
	
	var $supported_currencies = array(
		'UAH', 'RUB', 'USD', 'GBP', 'EUR'
	);
	
	var $options = array(
		'market_name'	=> 'market', 
		'encoding'		=> 'WINDOWS-1251', 
		'file_name'		=> '/xml/marketgid_price.xml', 
		'url' 			=> 'http://NAME_SERVER/',
        'company_name'  => '',
	);
	
	var $fp;

    function Marketgid_Export( $options = null ) {
		if ( !is_null($options) ) {
			foreach ( $options as $option_name=>$option_value ) {
				if ( !in_array($option_name, $this->available_options) ) {
					trigger_error(sprintf('Undefined option %s', $option_name), E_USER_WARNING);
				} else {
					$this->options[$option_name] = $option_value; 
				}
			}
		}
    }

	function start()
	{
		$this->put_code('');
	}
    
    function addCategory( $params )
    {
    	static $init = false;
		
    	if ( !$init ) {
    		$code = sprintf('<?xml version="1.0" encoding="%s"?>', $this->options['encoding']) . "\r\n";
    		$code .= sprintf('<price date="%s">', date("Y-m-d H:i")) . "\r\n";
    		$code .= sprintf('<name>%s</name>', $this->options['market_name']) . "\r\n";
    		$code .= sprintf('<url>%s</url>', $this->options['url']) . "\r\n";
    		$code .= "\t" . '<catalog>' . "\r\n";
    		$this->put_code($code);    		
    		$init = true;
    	}

		foreach ( $params as $param_name=>$param_value ) {
			if ( !in_array($param_name, $this->categories_params) ) {
				trigger_error(sprintf('Undefined categories param %s', $param_name), E_USER_WARNING);				
			}
		}

		if ( !isset($params['id']) || empty($params['id']) ) {
			trigger_error('You should set category ID', E_USER_ERROR);
			return;
		}

		if ( !isset($params['name']) || empty($params['name']) ) {
			trigger_error('You should set category name', E_USER_ERROR);
			return;
		}
		
		$params['parent'] = ( isset($params['parent']) && !empty($params['parent']) ) ? $params['parent']: 0;

		$code = "\t\t";
		$code .= sprintf('<category id="%d" parentID="%d"><![CDATA[%s]]></category>', $params['id'], $params['parent'], $params['name']) . "\r\n";
		$this->put_code($code);    		
    }

	function addCurrency( $params )
	{
		static $init = false;
		
		if ( !$init ) {
			$code = "\t" . '</catalog>' . "\r\n";		
    		$this->put_code($code);    		
    		$init = true;
		}

		foreach ( $params as $param_name=>$param_value ) {
			if ( !in_array($param_name, $this->currency_params) ) {
				trigger_error(sprintf('Undefined currency param %s', $param_name), E_USER_WARNING);				
			}		
		}
		
		if ( !isset($params['name']) || empty($params['name']) ) {
			trigger_error('You should set currency name', E_USER_ERROR);
			return;
		}

		if ( !isset($params['value']) || empty($params['value']) ) {
			trigger_error('You should set currency value', E_USER_ERROR);
			return;
		}
		
		if ( !in_array(strtoupper($params['name']), $this->supported_currencies) ) {
			trigger_error('Unsupported currency', E_USER_WARNING);
			return;
		}

		$code = "\t\t";
		if($params['value']=="1"){
		$code .= sprintf('<currency code="%s" />', $params['name']) . "\r\n";
		} else {
		$code .= sprintf('<currency code="%s" rate="%s" />', $params['name'], $params['value']) . "\r\n";
		}
		$this->put_code($code);
	}
	
	function addOffer( $params )
	{
		static $init = false;

		if ( !$init ) {
		//	$code = "\t" . '</currencies>' . "\r\n";
			$code = "\t" . ' <region>Киев</region>' . "\r\n";			
			$code .= "\t" . '<items>' . "\r\n";			
    		$this->put_code($code);    		
    		$init = true;
		}

		foreach ( $params as $param_name=>$param_value ) {
			if ( !in_array($param_name, $this->offers_params) ) {
				trigger_error(sprintf('Undefined currency param %s', $param_name), E_USER_WARNING);				
			}		
		}				

		if ( !isset($params['click']) || empty($params['click']) ) {
			trigger_error(sprintf('You should set parameter %s', 'click'), E_USER_WARNING);
			return;
		}

		if ( !isset($params['name']) || empty($params['name']) ) {
			trigger_error(sprintf('You should set parameter %s', 'name'), E_USER_WARNING);
			return;
		}

		if ( !isset($params['price']) || empty($params['price']) ) {
			trigger_error(sprintf('You should set parameter %s', 'price'), E_USER_WARNING);
			return;
		}

		if ( !isset($params['currency']) || empty($params['currency']) ) {
			trigger_error(sprintf('You should set parameter %s', 'currency'), E_USER_WARNING);
			return;
		}

		if ( !in_array(strtoupper($params['currency']), $this->supported_currencies) ) {
			trigger_error(sprintf('Unsupported currency %s', $params['currency']), E_USER_WARNING);
			return;
		}
		
		// Filters
		if ( isset($params['click']) && !empty($params['click']) ) $params['click'] = str_replace(
			array(
				'&', '<', '>', "'", '"',
			),
			array(
				'&amp;', '&lt;', '&gt;', '&apos;', '&quot;',
			),
			$params['click']
		); 

		if ( isset($params['img']) && !empty($params['img']) ) $params['img'] = str_replace(
			array(
				'&', '<', '>', "'", '"',
			),
			array(
				'&amp;', '&lt;', '&gt;', '&apos;', '&quot;',
			),
			$params['img']
		);		

		$code = "\t\t" . sprintf('<item id="%d">', $params['id']) . "\r\n"; 
		$code .= "\t\t\t" . sprintf('<name><![CDATA[%s]]></name>', $params['name']) . "\r\n";
		$code .= "\t\t\t" . sprintf('<categoryId><![CDATA[%s]]></categoryId>', $params['category']) . "\r\n";
		$code .= "\t\t\t" . sprintf('<price>%s</price>', $params['price']) . "\r\n";
		$code .= "\t\t\t" . sprintf('<url>%s</url>', $params['click']) . "\r\n";
		$params['img']	= ( isset($params['img']) ) ? $params['img']: '';
		$code .= "\t\t\t" . sprintf('<image><![CDATA[%s]]></image>', $params['img']) . "\r\n";
		if ( isset($params['vendor']) && !empty($params['vendor']) )
		$code .= "\t\t\t" . sprintf('<vendor><![CDATA[%s]]></vendor>', $params['vendor']) . "\r\n";
		if ( isset($params['description']) && !empty($params['description']) ) 
		$code .= "\t\t\t" . sprintf('<description><![CDATA[%s]]></description>', $params['description']) . "\r\n";
		$code .= "\t\t" . '</item>' . "\r\n";

		$this->put_code($code);
	}
	
	function end()
	{
		$code = "\t" . '</items>' . "\r\n";
		$code .= '</price>';
		$this->put_code($code, $end = true);
	}
    
    function output()
    {
		if ( file_exists($this->options['file_name']) && is_readable($this->options['file_name']) ) {
	    	header('Content-Type: text/xml');
	    	header('Content-Length: ' . filesize($this->options['file_name']));    	
	
	    	return readfile($this->options['file_name']);			
		} else {
			trigger_error(sprintf('Can\'t read file %s', $this->options['file_name']), E_USER_WARNING);
		}
    }

	function put_code( $code, $end = false )
	{
		static $init = false;
		$path = SITE_PATH.'/xml';
        
        $code = iconv( 'utf-8' , 'CP1251//IGNORE', $code);
		//echo "<br> int = ".$init.' $path='.$path.' $this->options[file_name]='.$this->options['file_name'];
		if ( !$this->options['file_name'] ) {
			if ( !$init ) {
		    	header('Content-Type: text/xml');
		    	$init = true;
			}
			echo $code;
		} else {
			if ( !$init ) {
				if( !is_dir($path)) mkdir($path, 0777);
                else @chmod($path,0777);
                
                $this->fp = fopen($this->options['file_name'], 'wb');
                //echo '<br />$this->fp='.$this->fp;
				if ( !is_resource($this->fp) ) {
					trigger_error(sprintf('Can\'t create file %s', $this->options['file_name']), E_USER_ERROR);
				}
				if ( is_resource($this->fp) ) flock($this->fp, LOCK_EX);
				$init = true;				
			}
			if ( is_resource($this->fp) ) {
                $res = fwrite($this->fp, $code);
            }
			if ( $end ) {
				if ( !is_resource($this->fp) ) {
					trigger_error(sprintf('Can\'t create file %s', $this->options['file_name']), E_USER_ERROR);
				}
				flock($this->fp, LOCK_UN);
				fclose($this->fp);	
                @chmod($path,0755);			
			}
		}
	}    
}
?>
