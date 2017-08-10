<?php
/**
 * (с) 2010 <Hotprice>
 *
 * Библиотека поставляется как есть.
 * Вы используете эту библиотеку на свой страх и риск.
 *
 * Зачем использовать эту библиотеку?
 *
 * - при изменении формата xml-ля прайс-листа, вам нужно будет только обновить библиотеку.
 * - экранирует все специальные символы за вас
 * - следит за порядком элементов, вам об этом не нужно задумываться
 * - может работать с большим кол-вом товаров

 */
class Hotprice_Export {

	var $available_options = array(
		'market_name', 'url', 'encoding', 'file_name',
	);

	var $categories_params = array(
		'id', 'parent', 'name',
	);

	var $currency_params = array(
		'name', 'value',
	);

	var $offers_params = array(
		'id', 'category', 'priority', 'click', 'categoryLink', 'img', 'vendor', 'type', 'name', 'price', 'currency', 'priceRUAH', 'priceRUSD', 'priceOUSD', 'description',
	);

	var $supported_currencies = array(
		'UAH', 'RUB', 'USD', 'GBP', 'EUR'
	);

	var $options = array(
		'market_name'	=> 'Cyfrosvit',
        'url'    => 'cyfrosvit.com.ua',
		'encoding'		=> 'WINDOWS-1251',
		'file_name'		=> '/xml/hotprice.xml',
	);

	var $fp;

    function Hotprice_Export( $options = null ) {
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
        static $init = false;
        if ( !$init ) {
            $init = true;
            $code = sprintf('<?xml version="1.0" encoding="%s"?>', $this->options['encoding']) . "\r\n";
            $code.= sprintf('<yml_catalog date="%s">', date("Y-m-d H:i")) . "\r\n";
            $code.= sprintf('<shop name="%s" url="%s">', $this->options['market_name'], $this->options['url'])  . "\r\n";
        }
		$this->put_code($code);
	}


    function end()
    {
        $code = "\t\t" . '</items>' . "\r\n";
        $code.= "\t" . '</shop>' . "\r\n";
        $code.= '</yml_catalog>' . "\r\n";
        $this->put_code($code, $end = true);
    }

    function addCategory( $params )
    {
    	static $init = false;

    	if ( !$init ) {
    		$code = "\t" . '<catalog>' . "\r\n";
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
		$code .= sprintf('<category id="%d" parentId="%d"><![CDATA[%s]]></category>', $params['id'], $params['parent'], $params['name']) . "\r\n";
		$this->put_code($code);
    }

	function addCurrency( $params )
	{
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

        $code = "\t" . '<currencies>' . "\r\n";
		$code .= sprintf("\t\t" .'<currency id="%s" rate="%s" />', $params['name'], $params['value']) . "\r\n";
        $code .= "\t" . '</currencies>' . "\r\n";

		$this->put_code($code);
	}

	function addOffer( $params )
	{
		static $init = false;

		if ( !$init ) {
			$code = "\t" . '</catalog>' . "\r\n";
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

		if ( !isset($params['price']) ) {
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

		//$code = "\t\t" . sprintf('<item id="%d" category="%d" priority="%d">', $params['id'], $params['category'], $params['priority']) . "\r\n";
        $code = "\t\t" . sprintf('<item id="%d">', $params['id']). "\r\n";
		/*if ( isset($params['type']) && !empty($params['type']) )
		    $code .= "\t\t\t" . sprintf('<type><![CDATA[%s]]></type>', $params['type']) . "\r\n";*/
		$code .= "\t\t\t" . sprintf('<name><![CDATA[%s]]></name>', $params['name']) . "\r\n";
        $code .= "\t\t\t" . sprintf('<url>%s</url>', $params['click']) . "\r\n";
        $code .= "\t\t\t" . sprintf('<price>%s</price>', $params['priceRUAH']) . "\r\n";
        $code .= "\t\t\t" . sprintf('<categoryId>%s</categoryId>', $params['categoryLink']) . "\r\n";
        //$code .= "\t\t\t" . sprintf('<price cid="%s">%s</price>', $params['currency'], $params['price']) . "\r\n";
        if ( isset($params['vendor']) && !empty($params['vendor']) )
            $code .= "\t\t\t" . sprintf('<vendor><![CDATA[%s]]></vendor>', $params['vendor']) . "\r\n";
        $params['img']    = ( isset($params['img']) ) ? $params['img']: '';

        $code .= "\t\t\t" . sprintf('<image>%s</image>', $params['img']) . "\r\n";

        if ( isset($params['description']) && !empty($params['description']) )
		    $code .= "\t\t\t" . sprintf('<description><![CDATA[%s]]></description>', $params['description']) . "\r\n";

        $code .= "\t\t" . '</item>' . "\r\n";

		$this->put_code($code);
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

        if(!empty($code)) {
            $code = iconv("UTF-8", "CP1251//TRANSLIT//IGNORE", $code);
            if($code==false)
                echo '<br/>Error converting string';
        }

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
				if ( !is_resource($this->fp) ) {
					trigger_error(sprintf('Can\'t create file %s', $this->options['file_name']), E_USER_ERROR);
				}
				if ( is_resource($this->fp) ) flock($this->fp, LOCK_EX);
				$init = true;
			}
			if ( is_resource($this->fp) ) fwrite($this->fp, $code);
			if ( $end ) {
				flock($this->fp, LOCK_UN);
				fclose($this->fp);
                @chmod($path,0755);
			}
		}
	}
}
?>
