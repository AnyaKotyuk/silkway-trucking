<?php

class Yandex_Export {

	var $available_options = array(
		'market_name', 'company_name', 'encoding', 'file_name', 'url',
	);
	
	var $categories_params = array(
		'id', 'parent', 'name',
	);
	
	var $currency_params = array(
		'name', 'value',
	);
	
	var $offers_params = array(
		'id', 'category', 'priority', 'click', 'img', 'vendor', 'type', 'name', 'price', 'currency', 'description', 'delivery',
	);
	
	var $supported_currencies = array(
		'UAH', 'RUB', 'USD', 'GBP', 'EUR'
	);
	
	var $options = array(
		'market_name'	=> 'market', 
		'encoding'		=> 'WINDOWS-1251', 
		'file_name'		=> './xml/yandex_price.xml', 
		'url' 			=> 'http://NAME_SERVER/', 
	);
	
	var $fp;

    function Yandex_Export( $options = null ) {
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
            $code = "\t" . '</currencies>' . "\r\n";        
    		$code .= "\t" . '<categories>' . "\r\n";
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

		// Filters
		if ( isset($params['name']) && !empty($params['name']) ) $params['name'] = str_replace(
			array(
				'&', '<', '>', "'", '"',
			),
			array(
				'&amp;', '&lt;', '&gt;', '&apos;', '&quot;',
			),
			$params['name']
		); 

		$code = "\t\t";
		if($params['parent']=="0"){
		$code .= sprintf('<category id="%d">%s</category>', $params['id'], $params['name']) . "\r\n";
		} else {
		$code .= sprintf('<category id="%d" parentId="%d">%s</category>', $params['id'], $params['parent'], $params['name']) . "\r\n";
		}
		$this->put_code($code);    		
    }

	function addCurrency( $params )
	{
		static $init = false;
		
		if ( !$init ) {
            $code = sprintf('<?xml version="1.0" encoding="%s"?>', $this->options['encoding']) . "\r\n";
            $code .= "" . '<!DOCTYPE yml_catalog SYSTEM "shops.dtd">' . "\r\n";
            $code .= sprintf('<yml_catalog date="%s">', date("Y-m-d H:i")) . "\r\n";
            $code .= "" . '<shop>' . "\r\n";
            $code .= sprintf("\t" . '<name>%s</name>', $this->options['market_name']) . "\r\n";
            $code .= sprintf("\t" . '<company>%s</company>', $this->options['company_name']) . "\r\n";
            $code .= sprintf("\t" . '<url>%s</url>', $this->options['url']) . "\r\n";
			$code .= "\t" . '<currencies>' . "\r\n";			
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
		$code .= sprintf('<currency id="%s" rate="%s" />', $params['name'], $params['value']) . "\r\n";
		$this->put_code($code);
	}
	
	function addOffer( $params )
	{
		static $init = false;

		if ( !$init ) {
            $code = "\t" . '</categories>' . "\r\n";
			$code .= "\t" . '<offers>' . "\r\n";			
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

		if ( !isset($params['price']) /*|| empty($params['price'])*/ ) {
            echo '  '.$params['price'];
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

		if ( isset($params['img']) && !empty($params['img']) ) 
            $params['img'] = str_replace(
			array(
				'&', '<', '>', "'", '"',
			),
			array(
				'&amp;', '&lt;', '&gt;', '&apos;', '&quot;',
			),
			$params['img']
		);
        
        if ( isset($params['description']) && !empty($params['description']) )
            $params['description'] = str_replace(
    			array(
    				'&laquo;', '&raquo;', '&rsquo;', "&lsquo;", "&ndash;", "&mdash;", "&eacute;", "&oacute;",
    			),
    			array(
    				'«', '»', "’", "’", '-', '—', 'é', 'ó',
    			),
    			$params['description']
    		);		
        
        $params['name'] = htmlspecialchars(html_entity_decode($params['name'], ENT_QUOTES, 'UTF-8'),ENT_QUOTES, 'UTF-8');
        
		// available="true" - есть в наличии или нет в наличии, Нужно для каждого сайта подстаивать признак наличия.
        $code = "\t\t" . sprintf('<offer id="%d" available="true">', $params['id']) . "\r\n";

        $code .= "\t\t\t" . sprintf('<url>%s</url>', $params['click']) . "\r\n";
		$code .= "\t\t\t" . sprintf('<price>%s</price>', $params['price']) . "\r\n";
		$code .= "\t\t\t" . sprintf('<currencyId>%s</currencyId>', $params['currency']) . "\r\n";
		$code .= "\t\t\t" . sprintf('<categoryId>%s</categoryId>', $params['category']) . "\r\n";
		$params['img']	= ( isset($params['img']) ) ? $params['img']: '';
		$code .= "\t\t\t" . sprintf('<picture>%s</picture>', $params['img']) . "\r\n";
		//$code .= "\t\t\t" . sprintf('<delivery>%s</delivery>', $params['delivery']) . "\r\n";
		$code .= "\t\t\t" . sprintf('<name>%s</name>', $params['name']) . "\r\n";
		if ( isset($params['vendor']) && !empty($params['vendor']) )
		$code .= "\t\t\t" . sprintf('<vendor>%s</vendor>', $params['vendor']) . "\r\n";
		if ( isset($params['description']) && !empty($params['description']) ){ 
          $code .= "\t\t\t" . sprintf('<description>%s</description>', $params['description']) . "\r\n";
        }
		$code .= "\t\t" . '</offer>' . "\r\n";

		$this->put_code($code);
	}
	
	function end()
	{
		$code = "\t" . '</offers>' . "\r\n";
		$code .= '</shop>' . "\r\n";
		$code .= '</yml_catalog>';
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
		//$code = iconv( 'utf-8' , 'windows-1251', $code);
        
        $code = iconv("UTF-8", "CP1251//TRANSLIT//IGNORE", $code);
        //echo '<br>$code='.$code;
		static $init = false;
		//echo "<br> int = ".$init;
		if ( !$this->options['file_name'] ) {
			if ( !$init ) {
		    	header('Content-Type: text/xml');
		    	$init = true;
			}
			echo $code;
		} else {
			if ( !$init ) {
				$this->fp = fopen($this->options['file_name'], 'wb');
				if ( !is_resource($this->fp) ) {
					trigger_error(sprintf('Can\'t create file %s', $this->options['file_name']), E_USER_ERROR);
				}
				if ( is_resource($this->fp) ) flock($this->fp, LOCK_EX);
				$init = true;				
			}
			if ( is_resource($this->fp) ) fwrite($this->fp, $code);
			if ( $end ) {
				if ( !is_resource($this->fp) ) {
					trigger_error(sprintf('Can\'t create file %s', $this->options['file_name']), E_USER_ERROR);
				}
				flock($this->fp, LOCK_UN);
				fclose($this->fp);				
			}
		}
	}    
}
?>
