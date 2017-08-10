<?php
// ================================================================================================
// System : SEOCMS
// Module : shop.class.php
// Date : 08.06.2010
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Purpose : Class definition for all actions with managment of ShopExport
// ================================================================================================

include_once( SITE_PATH . '/modules/mod_shop_export/shop.defines.php' );

// ================================================================================================
//    Class             : ShopExport
//    Date              : 08.06.2010
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment of ShopExport
// ================================================================================================
//    Programmer        : Ihor Trokhymchuk
//    Date              : 08.06.2010
//    Reason for change : Creation
//    Change Request Nbr: N/A
// ================================================================================================
class ShopExport extends Catalog {

    var $user_id = NULL;
    var $module = NULL;
    var $Err = NULL;
    var $lang_id = _LANG_ID;
    var $script = NULL;
    var $db = NULL;
    var $Msg = NULL;
    var $Rights = NULL;
    var $Form = NULL;
    public $resultXMLFilenameArr = NULL;
    public $exportFolder = '/xml/';

    // ================================================================================================
    //    Function          : ShopExport (Constructor)
    //    Date              : 08.06.2010
    //    Parms             : usre_id   / User ID
    //                        module    / module ID
    //    Returns           : Error Indicator
    //    Description       : Opens and selects a dabase
    // ================================================================================================
    function ShopExport($user_id = NULL, $module = NULL) {
        //Check if Constants are overrulled
        ( $user_id != "" ? $this->user_id = $user_id : $this->user_id = NULL );
        ( $module != "" ? $this->module = $module : $this->module = NULL );

        $this->lang_id = _LANG_ID;

        if (empty($this->db))
            $this->db = DBs::getInstance();
        if (empty($this->Form))
            $this->Form = check_init('FormCatalog', 'FrontForm', '"form_mod_market_export"');
        $this->settings = $this->GetShopSettings();
        $this->multi = check_init_txt('TblBackMulti', TblBackMulti);

        $this->resultXMLFilenameArr['nadavi'] = 'nadavi_price.xml';
        $this->resultXMLFilenameArr['meta'] = 'meta_price.xml';
        $this->resultXMLFilenameArr['bigmir'] = 'bigmir_price.xml';
        $this->resultXMLFilenameArr['e-katalog'] = 'e-katalog_price.xml';
        $this->resultXMLFilenameArr['hotline'] = 'hotline_price.xml';
        $this->resultXMLFilenameArr['hotprice'] = 'hotprice_price.xml';
        $this->resultXMLFilenameArr['pay_ua'] = 'pay_ua_price.xml';
        $this->resultXMLFilenameArr['price_ua'] = 'price_ua_price.xml';
        $this->resultXMLFilenameArr['yandex'] = 'yandex_price.xml';
        $this->resultXMLFilenameArr['iua'] = 'iua_price.xml';
        $this->resultXMLFilenameArr['marketgid'] = 'marketgid_price.xml';
    }

// End of Catalog_Stat Constructor
    // ================================================================================================
    // Function : Show()
    // Date : 08.06.2010
    // Returns : true,false / Void
    // Description : show Export Data
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function Show() {
        $script = '/modules/mod_shop_export/shop.backend.php';
        //Write Form Header
        $this->Form->WriteHeader($script);
        AdminHTML::PanelSimpleH();
        ?>
        <table border=0 class="EditTable" width="100%">
            <tr valign="top">
                <td>
        <?= AdminHTML::PanelSimpleH(); ?>
                    <div class="EditTable">
                        <b><?= $this->multi['TXT_MARKET_EXPORT_DATA'] ?>:</b><br/><br/>
                        <div style="font-size: 9px;"><?= $this->multi['TXT_MARKET_EXPORT_DATA_DESCR']; ?></div><br/>
                        <div>
                            <input type="button" style="font-size: 14px;" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_EXPORT_ALL']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=all', 'result_all'); return false;" />
                            <div id="result_all"></div>
        <?
        $val = 'nadavi';
        if ($this->settings[$val] == 1) {
            ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_NADAVI']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>

                                </div>
            <?
        }
        $val = 'meta';
        if ($this->settings[$val] == 1) {
            ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_META']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>
                                </div>
                                <?
                            }
                            $val = 'bigmir';
                            if ($this->settings[$val] == 1) {
                                ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_BIGMIR']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>
                                </div>
                                <?
                            }
                            $val = 'e-katalog';
                            if ($this->settings[$val] == 1) {
                                ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_EKATALOG']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>
                                </div>
                                <?
                            }
                            $val = 'hotline';
                            if ($this->settings[$val] == 1) {
                                ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_HOTLINE']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>
                                </div>
                                <?
                            }
                            $val = 'hotprice';
                            if ($this->settings[$val] == 1) {
                                ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_HOTPRICE']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>
                                </div>
                                <?
                            }
                            $val = 'pay_ua';
                            if ($this->settings[$val] == 1) {
                                ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_PAY_UA']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>
                                </div>
                                <?
                            }
                            $val = 'price_ua';
                            if ($this->settings[$val] == 1) {
                                ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_PRICE_UA']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>
                                </div>
                                <?
                            }
                            $val = 'yandex';
                            if ($this->settings[$val] == 1) {
                                ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_YANDEX']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>
                                </div>
                                <?
                            }
                            $val = 'iua';
                            if ($this->settings[$val] == 1) {
                                ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_I_UA']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>
                                </div>
                                <?
                            }
                            $val = 'marketgid';
                            if ($this->settings[$val] == 1) {
                                ?>
                                <div style="margin: 5px 0px 5px 0px; padding: 10px; background-color: #EAEAEA;">
                                    <input type="button" value="<?= $this->multi['TXT_MARKET_EXPORT_FOR'] . ' ' . $this->multi['TXT_MARKET_MARKETGID']; ?>" onclick="makeExport('<?= $script ?>', 'module=<?= $this->module; ?>&task=save_xml&sel=<?= $val; ?>', 'result_<?= $val; ?>'); return false;" />
                                    <br/><?= $this->multi['TXT_MARKET_EXPORT_FOR_MARKET_LINK']; ?>: <a href="/priceXML/<?= $val; ?>/">http://<?= NAME_SERVER; ?>/priceXML/<?= $val; ?>/</a>
                                    <div id="result_<?= $val; ?>"></div>
                                </div>
                                <?
                            }
                            ?>
                        </div>
        <?= AdminHTML::PanelSimpleF(); ?>
                </td>
            </tr>
        </table>

        <script type="text/javascript">
            function makeExport(script, mydata, div_id){
                did = "#"+div_id;
                $.ajax({
                    type: "POST",
                    data: mydata,
                    url: script,
                    success: function(msg){
                        //alert(msg);
                        $(did).html( msg );
                    },
                    beforeSend : function(){
                        //$("#sss").html("");
                        $(did).html('<div style="text-align:left;"><img src="/admin/images/icons/loading_animation_liferay.gif" alt="" title="" /></div>');
                    }
                });
            } // end of function makeExport
        </script>

        <?
        AdminHTML::PanelSimpleF();
        //AdminHTML::PanelSubF();
        $this->Form->WriteFooter();
    }

// end of function Shop
    // ================================================================================================
    // Function : GetData()
    // Date : 08.06.2010
    // Returns : true,false / Void
    // Description : Get Export Data
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetData() {
        $arr = array();
        $CatalogL = new CatalogLayout();
        $c_settings = $CatalogL->settings;  // Catalog Settings
        /*
         * Для примера создаем подключение к базе данных
         */
        mysql_connect(_HOST, _USER, _PASSWD);
        mysql_select_db(_DBNAME);

        $q = "set character_set_client='" . DB_CHARACTER_SET_CLIENT . "'";
        $res = mysql_query($q);
        //echo '<br>$q='.$q.' $res='.$res;

        $q = "set character_set_results='" . DB_CHARACTER_SET_RESULT . "'";
        $res = mysql_query($q);
        //echo '<br>$q='.$q.' $res='.$res;

        $q = "set collation_connection='" . DB_COLLATION_CONNECTION . "'";
        $res = mysql_query($q);
        //echo '<br>$q='.$q.' $res='.$res;

        /*
         * Например делаем выборку товаров так
         */
        //$sSQL = 'select ' .
        //'i.id as id, i.id_cat as category, i.item_descr as description, i.item_url as click, i.item_pic as img, i.item_cost*i.currency_koef as price, v.vendor_name as vendor, i.model_name as name, i.type as type, i.priority as priority, "UAH" as currency  ' .
        //		'from '.TblModCatalogProp.' as i INNER JOIN vendor as v USING(vendor_id) LIMIT 100';
        // Виборка ID только видимых категорий
        $keys = array_keys($CatalogL->treeCatList);
        $rows = count($keys);
        for ($i = 0; $i < $rows; $i++) {
            $id = $CatalogL->treeCatList[$keys[$i]];
            if (empty($str))
                $str = $id;
            else
                $str = $str . ',' . $id;
        }

        $sSQL = "
        SELECT
             `" . TblModCatalogProp . "`.*,
            `" . TblModCatalogPropSprName . "`.`name`,
            `" . TblModCatalogPropSprFull . "`.`name` AS `description`
        FROM
            `" . TblModCatalogProp . "`
            LEFT JOIN `" . TblModCatalogPropSprName . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprName . "`.`cod` AND `" . TblModCatalogPropSprName . "`.`lang_id`='"._LANG_ID."')
            LEFT JOIN `" . TblModCatalogPropSprFull . "` ON (`" . TblModCatalogProp . "`.`id`=`" . TblModCatalogPropSprFull . "`.`cod` AND `" . TblModCatalogPropSprFull . "`.`lang_id`='"._LANG_ID."')
        WHERE
            `" . TblModCatalogProp . "`.`price` <> '0'
            AND `" . TblModCatalogProp . "`.`visible`='2'
            AND `" . TblModCatalogProp . "`.`exist`='1'
        ";

        if (!empty($str))
            $sSQL .= " AND " . TblModCatalogProp . ".`id_cat` IN (" . $str . ") ";

        $sSQL .= "
            GROUP BY `" . TblModCatalogProp . "`.`id`
            ORDER BY `" . TblModCatalogProp . "`.`id`
        ";

        //$sSQL .= " LIMIT 100";
        // Используем mysql_unbuffered_query для извлечения большого кол-ва строк из базы данных
        //$result = mysql_unbuffered_query($sSQL);
        $result = mysql_query($sSQL);
        //echo "<br />".$sSQL."<br/><br/>res = ".$result."<br>";

        while ($offer = mysql_fetch_assoc($result)) {
            /*
             * Добавляем товары.
             * Вы должны обязательно указать ИД категории, куда переходить на ваш сайт - click, название предложения - name,
             * цену - price и валюту.
             *
             * Внимание! Вам не нужно делать никаких преобразований для URL, экранирований и т.п. Библиоткека сделает все за вас.
             *
             * Вы должны добавить хотя бы один товар
             */

            $categoryLink = $CatalogL->Link($offer['id_cat']);
            $click = $CatalogL->Link($offer['id_cat'], $offer['id']);
            //echo "<br>".$click;
            $row_img = $this->GetPicture($offer['id']);
            if (isset($row_img[0]['path'])) {
                $img = $row_img[0]['path'];
                //$img = $this->GetFirstImgOfProp($offer['id']);
                $settings_img_path = $c_settings['img_path'] . '/' . $offer['id']; // like /uploads/45
                $img_with_path = $settings_img_path . '/' . $img; // like /uploads/45/R1800TII_big.jpg
                //echo "<br>".$img_with_path;
            }
            else
                $img_with_path = '';
            //echo ' <br />$c_settings[img_path]='.$c_settings['img_path'];
            //  $name = $offer['name'];
            $tm = explode(" ", $offer['name']);
            if (isset($tm[0])) {
                $type = $tm[0];
            } else {
                $type = '';
            }
            //echo "<br>".$type;

            if (isset($offer['price']) and !empty($offer['price'])) {
                $price = $offer['price'];
                $priceRUAH = $CatalogL->Currency->Converting($offer['price_currency'], 5, $price);
                $priceRUSD = $CatalogL->Currency->Converting($offer['price_currency'], 1, $price);
                $priceOUSD = $CatalogL->Currency->Converting($offer['opt_price_currency'], 1, $offer['opt_price']);
            } else {
                $price = '0';
                $priceRUAH = '0';
                $priceRUSD = '0';
                $priceOUSD = '0';
            }
            //echo '<br> $offer[name]='.$offer['name'];
            //echo "<br> price = ".$price;
            //echo "<br> priceRUAH = ".$priceRUAH;
            //echo "<br> priceRUSD = ".$priceRUSD;
            //echo "<br> priceOUSD = ".$priceOUSD;

            $vendor = '';

            //$full = html_entity_decode($full, ENT_XML1);
            $full = strip_tags($offer['description']);
            $full = str_replace("&nbsp;", " ", $full);
            $full = str_replace("&nbsp", " ", $full);
            $full = str_replace("nbsp", " ", $full);
            /*
              $full = str_replace(
              array(
              '&laquo;', '&raquo;', '&rsquo;', "&lsquo;", "&ndash;", "&mdash;", "&eacute;", "&oacute;", "&ldquo;", "&bdquo;", "&rdquo;", "&quot;", "&bul;", "&reg;", "&trade;", "&deg;", "&hellip;", "&frac12;", "&frac13;", "&frac14;", "&sup2;", "&sup3;", " & ", "&times;", "&n", "&Alpha;", "&alpha;", "&bull;"
              ),
              array(
              '«', '»', "’", "’", '-', '—', 'é', 'ó', "“", "”", "'", "'", "'", " ", " ", " ", "..", "1/2", "1/3", "1/4", "2", "3", " and ", "x", " ", "Α", "α", "•"
              ),
              $full);
             */
            //$full = html_entity_decode($full, ENT_XML1);


            array_push($arr, array(
                'id' => $offer['id'], // ИД товара
                'category' => $offer['id_cat'], // ИД категории в которой находится товар
                'priority' => "50", // Приоритет - пока не используется
                'img' => $img_with_path, // Адрес(URL) картинки
                'click' => $click, // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                'categoryLink' => $categoryLink, // Адрес(URL) категории в которой находится товар
                'type' => $type, // Тип товара, например - монитор
                'name' => stripslashes($offer['name']), // Название товара
                'vendor' => $vendor, // Название производителя
                'currency' => "USD", // Буквенный код валюты, например USD
                'price' => $price, // Цена товара в указаной валюте
                'priceRUAH' => $priceRUAH, // Цена товара в указаной валюте
                'priceRUSD' => $priceRUSD, // Цена товара в указаной валюте
                'priceOUSD' => $priceOUSD, // Цена товара в указаной валюте
                'description' => $full, // Описание товара
                'art_num' => $offer['art_num'], // код модели (артикул производителя)
                'exist' => $offer['exist'], // наличие товара на складе
                'grnt' => $offer['grnt'] // гпарантия
                    )
            );
        }
        //   print_r($arr);
        return $arr;
    }

// end of function GetData
    // ================================================================================================
    // Function : GetShopSettings()
    // Date: 7.10.2008
    // Returns : true,false / Void
    // Description : return all settings of ShopExport
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetShopSettings() {
        $q = "select * from `" . TblModShopSet . "` where 1";
        $res = $this->db->db_Query($q);
        if (!$res OR !$this->db->result)
            return false;
        $row = $this->db->db_FetchAssoc();
        return $row;
    }

// end of function GetShopSettings()
    // ================================================================================================
    // Function : SaveNadaviXML()
    // Date: 07.06.2010
    // Returns : true,false / Void
    // Description : Save Nadavi XML
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SaveNadaviXML($arr) {
        $db = new DB();
        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'url' => $this->settings['url'], // Адресс
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['nadavi'],
        );

        /* Инициализируем библиотеку */
        $price = new NADAVI_export($options);

        /* Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно. */
        $price->start();

        /* Например делаем выборку курсов валют так */
        $Currency = new SystemCurrencies();
        $value = $Currency->GetValue(5); //UAH

        $price->addCurrency(array('name' => "UAH", 'value' => "1",));

        /* Например делаем выборку наших категорий из базы данных так */
        $q = "SELECT `" . TblModCatalog . "`.*, `" . TblModCatalogSprName . "`.`name`
        FROM `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
        WHERE `" . TblModCatalog . "`.`id`>0
        AND `" . TblModCatalog . "`.`visible`='2'
        AND `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
        AND `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
        ORDER BY `" . TblModCatalog . "` .`move` asc";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }

        //print_r($arr);
        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {
            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        'categoryLink' => $this->settings['url'] . $arr[$j]['categoryLink'], // Адрес(URL) категории в которой находится товар
                        'type' => $arr[$j]['type'], // Тип товара, например - монитор
                        'name' => $arr[$j]['name'], // Название товара
                        'vendor' => $arr[$j]['vendor'], // Произвоитель
                        'currency' => $arr[$j]['currency'], // Буквенный код валюты, например UAH
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'priceRUAH' => $arr[$j]['priceRUAH'], // Цена товара в указаной валюте
                        'priceRUSD' => $arr[$j]['priceRUSD'], // Цена товара в указаной валюте
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'description' => $arr[$j]['description'], // Описание товара
                    )
            );
        } // end for

        /* Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно. */
        $price->end();

        /*
         * Если вы указали название файла, результат работы скрипта будет сохранен в этот файл, и
         * вы можете вывести его содержимое так
         */
        // $price->output();
    }

// end of function SaveNadaviXML
    // ================================================================================================
    // Function : SaveMetaXML()
    // Date: 7.10.2008
    // Returns : true,false / Void
    // Description : Save META XML
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SaveMetaXML($arr) {
        $db = new DB();
        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['meta'],
        );

        /*
         * Инициализируем библиотеку
         */
        $price = new META_export($options);
        /*
         * Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->start();

        /*
         * Например делаем выборку наших категорий из базы данных так
         */
        $q = "SELECT `" . TblModCatalog . "`.*, `" . TblModCatalogSprName . "`.`name`
        FROM `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
        WHERE `" . TblModCatalog . "`.`id`>0
        AND `" . TblModCatalog . "`.`visible`='2'
        AND `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
        AND `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
        ORDER BY `" . TblModCatalog . "` .`move` asc";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }

        /* Например делаем выборку курсов валют так */
        $Currency = new SystemCurrencies();
        $value = $Currency->GetValue(5); //UAH
        $price->addCurrency(
                array(
                    'name' => "UAH",
                    'value' => "1",
                )
        );
        //print_r($arr);
        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {
            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        'type' => $arr[$j]['type'], // Тип товара, например - монитор
                        'name' => $arr[$j]['name'], // Название товара
                        'currency' => $arr[$j]['currency'], // Буквенный код валюты, например UAH
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'description' => $arr[$j]['description'], // Описание товара
                    )
            );
        } // end for

        /*
         * Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->end();

        /*
         * Если вы указали название файла, результат работы скрипта будет сохранен в этот файл, и
         * вы можете вывести его содержимое так
         */
        // $price->output();
    }

// end of function SaveMetaXML
    // ================================================================================================
    // Function : SaveBigmirXML()
    // Date: 8.10.2008
    // Returns : true,false / Void
    // Description : Save BigMir XML
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SaveBigmirXML($arr) {
        $db = new DB();
        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['bigmir'], // Имя файла
            'url' => $this->settings['url'],
        );

        /*
         * Инициализируем библиотеку
         */
        $price = new Bigmir_Export($options);

        /*
         * Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->start();

        /*
         * Например делаем выборку наших категорий из базы данных так
         */
        $q = "SELECT `" . TblModCatalog . "`.*, `" . TblModCatalogSprName . "`.`name`
        FROM `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
        WHERE `" . TblModCatalog . "`.`id`>0
        AND `" . TblModCatalog . "`.`visible`='2'
        AND `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
        AND `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
        ORDER BY `" . TblModCatalog . "` .`move` asc";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }


        /* Например делаем выборку курсов валют так */
        $Currency = new SystemCurrencies();
        $value = $Currency->GetValue(5); //UAH
        $price->addCurrency(
                array(
                    'name' => "UAH",
                    'value' => "1",
                )
        );

        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {
            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        'type' => $arr[$j]['type'], // Тип товара, например - монитор
                        'name' => $arr[$j]['name'], // Название товара
                        'currency' => $arr[$j]['currency'], // Буквенный код валюты, например UAH
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'priceRUAH' => $arr[$j]['priceRUAH'],
                        'priceRUSD' => $arr[$j]['priceRUSD'],
                        'description' => $arr[$j]['description'], // Описание товара
                    )
            );
        } // end for

        /*
         * Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->end();
    }

// end of function SaveBigmirXML
    // ================================================================================================
    // Function : SaveEkatalogXML()
    // Date: 01.09.2010
    // Returns : true,false / Void
    // Description : Save Ekatalog XML
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SaveEkatalogXML($arr) {
        $db = new DB();
        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'url' => $this->settings['url'], // Адресс
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['e-katalog'],
        );

        /* Инициализируем библиотеку */
        $price = new Ekatalog_Export($options);

        /* Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно. */
        $price->start();

        /* Например делаем выборку курсов валют так */
        //$Currency = new SystemCurrencies();
        //$value = $Currency->GetValue(5); //UAH
        //$price->addCurrency(array('name' => "USD", 'value' => $value,));
        $price->addCurrency(array('name' => "UAH", 'value' => "1",));

        /* Например делаем выборку наших категорий из базы данных так */
        $q = "SELECT `" . TblModCatalog . "`.*, `" . TblModCatalogSprName . "`.`name`
        FROM `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
        WHERE `" . TblModCatalog . "`.`id`>0
        AND `" . TblModCatalog . "`.`visible`='2'
        AND `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
        AND `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
        ORDER BY `" . TblModCatalog . "` .`move` asc";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }

        //print_r($arr);
        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {
            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        //'categoryLink' => $this->settings['url'].$arr[$j]['categoryLink'],    // Адрес(URL) категории в которой находится товар
                        'categoryLink' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'type' => $arr[$j]['type'], // Тип товара, например - монитор
                        'name' => $arr[$j]['name'], // Название товара
                        'vendor' => $arr[$j]['vendor'], // Произвоитель
                        'currency' => $arr[$j]['currency'], // Буквенный код валюты, например UAH
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'priceRUAH' => $arr[$j]['priceRUAH'],
                        'priceRUSD' => $arr[$j]['priceRUSD'],
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'description' => $arr[$j]['description'], // Описание товара
                    )
            );
        } // end for

        /* Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно. */
        $price->end();

        /*
         * Если вы указали название файла, результат работы скрипта будет сохранен в этот файл, и
         * вы можете вывести его содержимое так
         */
        // $price->output();
    }

// end of function SaveEkatalogXML
    // ================================================================================================
    // Function : SaveHotLineXML()
    // Date: 01.09.2010
    // Returns : true,false / Void
    // Description : Save Hotline XML
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SaveHotLineXML($arr) {
        $db = new DB();
        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'url' => $this->settings['url'], // Адресс
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['hotline'],
        );

        /* Инициализируем библиотеку */
        $price = new Hotline_Export($options);

        /* Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно. */
        $price->start();

        /* Например делаем выборку курсов валют так */
        $Currency = new SystemCurrencies();
        $value = $Currency->GetValue(5); //UAH

        $price->addCurrency(array('name' => "UAH", 'value' => $value,));

        /* Например делаем выборку наших категорий из базы данных так */
        $q = "SELECT `" . TblModCatalog . "`.*, `" . TblModCatalogSprName . "`.`name`
        FROM `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
        WHERE `" . TblModCatalog . "`.`id`>0
        AND `" . TblModCatalog . "`.`visible`='2'
        AND `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
        AND `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
        ORDER BY `" . TblModCatalog . "` .`move` asc";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }

        //print_r($arr);
        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {
            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        //'categoryLink' => $this->settings['url'].$arr[$j]['categoryLink'],    // Адрес(URL) категории в которой находится товар
                        'categoryLink' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'type' => $arr[$j]['type'], // Тип товара, например - монитор
                        'name' => $arr[$j]['name'], // Название товара
                        'vendor' => $arr[$j]['vendor'], // Произвоитель
                        'currency' => $arr[$j]['currency'], // Буквенный код валюты, например UAH
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'priceRUAH' => $arr[$j]['priceRUAH'], // Цена товара в указаной валюте
                        'priceRUSD' => $arr[$j]['priceRUSD'], // Цена товара в указаной валюте
                        'priceOUSD' => $arr[$j]['priceOUSD'], // Цена товара в указаной валюте
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'description' => $arr[$j]['description'], // Описание товара
                        'code' => $arr[$j]['art_num'], // код модели (артикул производителя)
                        'exist' => $arr[$j]['exist'], // наличие товара на складе
                        'guarantee' => $arr[$j]['grnt'] // гпарантия
                    )
            );
        } // end for

        /* Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно. */
        $price->end();

        /*
         * Если вы указали название файла, результат работы скрипта будет сохранен в этот файл, и
         * вы можете вывести его содержимое так
         */
        // $price->output();
    }

// end of function SaveHotLineXML
    // ================================================================================================
    // Function : SaveHotPriceXML()
    // Date: 01.09.2010
    // Returns : true,false / Void
    // Description : Save Hotline XML
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SaveHotPriceXML($arr) {
        $db = new DB();
        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'url' => $this->settings['url'], // Адресс
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['hotprice'],
        );

        /* Инициализируем библиотеку */
        $price = new Hotprice_Export($options);

        /* Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно. */
        $price->start();

        /* Например делаем выборку курсов валют так */
        $Currency = new SystemCurrencies();
        $value = $Currency->GetValue(5); //UAH

        $price->addCurrency(array('name' => "UAH", 'value' => "1",));

        /* Например делаем выборку наших категорий из базы данных так */
        $q = "SELECT `" . TblModCatalog . "`.*, `" . TblModCatalogSprName . "`.`name`
        FROM `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
        WHERE `" . TblModCatalog . "`.`id`>0
        AND `" . TblModCatalog . "`.`visible`='2'
        AND `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
        AND `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
        ORDER BY `" . TblModCatalog . "` .`move` asc";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }

        //print_r($arr);
        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {
            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        //'categoryLink' => $this->settings['url'].$arr[$j]['categoryLink'],    // Адрес(URL) категории в которой находится товар
                        'categoryLink' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'type' => $arr[$j]['type'], // Тип товара, например - монитор
                        'name' => $arr[$j]['name'], // Название товара
                        'vendor' => $arr[$j]['vendor'], // Произвоитель
                        'currency' => $arr[$j]['currency'], // Буквенный код валюты, например UAH
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'priceRUAH' => $arr[$j]['priceRUAH'],
                        'priceRUSD' => $arr[$j]['priceRUSD'],
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'description' => $arr[$j]['description'], // Описание товара
                    )
            );
        } // end for

        /* Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно. */
        $price->end();

        /*
         * Если вы указали название файла, результат работы скрипта будет сохранен в этот файл, и
         * вы можете вывести его содержимое так
         */
        // $price->output();
    }

// end of function SaveHotPriceXML()
    // ================================================================================================
    // Function : SavePayUaXML()
    // Date: 01.09.2010
    // Returns : true,false / Void
    // Description : Save Pay Ua XML
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SavePayUaXML($arr) {
        $db = new DB();
        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'url' => $this->settings['url'], // Адресс
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['pay_ua'],
        );

        /* Инициализируем библиотеку */
        $price = new Payua_Export($options);

        /* Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно. */
        $price->start();

        /* Например делаем выборку курсов валют так */
        $Currency = new SystemCurrencies();
        $value = $Currency->GetValue(5); //UAH

        $price->addCurrency(array('name' => "UAH", 'value' => "1",));

        /* Например делаем выборку наших категорий из базы данных так */
        $q = "SELECT `" . TblModCatalog . "`.*, `" . TblModCatalogSprName . "`.`name`
        FROM `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
        WHERE
            `" . TblModCatalog . "`.`id`>0
        AND `" . TblModCatalog . "`.`visible`='2'
        AND `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
        AND `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
        ORDER BY `" . TblModCatalog . "` .`move` asc";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }

        //print_r($arr);
        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {
            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        //'categoryLink' => $this->settings['url'].$arr[$j]['categoryLink'],    // Адрес(URL) категории в которой находится товар
                        'categoryLink' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'type' => $arr[$j]['type'], // Тип товара, например - монитор
                        'name' => $arr[$j]['name'], // Название товара
                        'vendor' => $arr[$j]['vendor'], // Произвоитель
                        'currency' => $arr[$j]['currency'], // Буквенный код валюты, например UAH
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'priceRUAH' => $arr[$j]['priceRUAH'],
                        'priceRUSD' => $arr[$j]['priceRUSD'],
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'description' => $arr[$j]['description'], // Описание товара
                    )
            );
        } // end for

        /* Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно. */
        $price->end();

        /*
         * Если вы указали название файла, результат работы скрипта будет сохранен в этот файл, и
         * вы можете вывести его содержимое так
         */
        // $price->output();
    }

// end of function SavePayUaXML
    // ================================================================================================
    // Function : SavePrice_uaXML()
    // Date: 8.10.2008
    // Returns : true,false / Void
    // Description : Save BigMir XML
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SavePrice_uaXML($arr) {
        $db = new DB();
        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['price_ua'], // Имя файла
            'url' => $this->settings['url'],
        );

        /*
         * Инициализируем библиотеку
         */
        $price = new Price_ua_Export($options);

        /*
         * Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->start();

        /*
         * Например делаем выборку курсов валют так
         */
        if ($this->settings['currency']) {
            $currencies = new SystemCurrencies();
            $price->addCurrency(
                    array(
                        'name' => "USD",
                        'value' => $currencies->GetValue(5/* usd */),
                    )
            );
        } else {
            $price->addCurrency(
                    array(
                        'name' => "UAH",
                        'value' => "1",
                    )
            );
        }

        /*
         * Делаем выборку наших категорий из базы данных так
         */
        $q = "SELECT
                        `" . TblModCatalog . "`.*,
                        `" . TblModCatalogSprName . "`.`name`
                    FROM
                        `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
                    WHERE
                        `" . TblModCatalog . "`.`id`>0
                    AND
                        `" . TblModCatalog . "`.`visible`='2'
                    AND
                        `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
                    AND
                        `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
                    ORDER BY
                        `" . TblModCatalog . "` .`move` asc
        ";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }

        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {
            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        //'type'        => $arr[$j]['type'],                // Тип товара, например - монитор
                        'name' => $arr[$j]['name'], // Название товара
                        //'currency'    => $arr[$j]['currency'],            // Буквенный код валюты, например UAH
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'priceRUAH' => $arr[$j]['priceRUAH'],
                        'priceRUSD' => $arr[$j]['priceRUSD'],
                        //'vendor'        => $arr[$j]['vendor'],                // Название производителя
                        'description' => $arr[$j]['description'], // Описание товара
                    )
            );
        } // end for

        /*
         * Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->end();
    }

// end of function SavePrice_uaXML
    // ================================================================================================
    // Function : SaveYandexXML()
    // Date: 08.06.2010
    // Returns : true,false / Void
    // Description : Save Yandex XML
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SaveYandexXML($arr) {
        $db = new DB();
        $stringRus = new stringRus();
        //'market_name'   => $this->settings['name'],    // Название вашего магазина
        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'company_name' => $this->settings['company'], // Название компании
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['yandex'], // Имя файла
            'url' => $this->settings['url'], // Адрес сайта магазина
        );


        /*
         * Инициализируем библиотеку
         */
        $price = new Yandex_Export($options);

        /*
         * Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->start();

        /* Например делаем выборку курсов валют так */
        $currencies = new SystemCurrencies();
        $price->addCurrency(array('name' => "UAH", 'value' => "1"));
        $price->addCurrency(array('name' => "USD", 'value' => $currencies->GetValue(5/* usd */)));

        /*
         * Например делаем выборку наших категорий из базы данных так
         */

        //WHERE `".TblModCatalog."`.`visible`='2'
        $q = "SELECT `" . TblModCatalog . "`.*, `" . TblModCatalogSprName . "`.`name`
                    FROM
                             `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
                    WHERE
                           `" . TblModCatalog . "`.`id`>0
                    AND
                           `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
                    AND
                            `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
                    ORDER BY
                            `" . TblModCatalog . "` .`level` asc";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }

        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {

            $arr[$j]['description'] = str_replace('&bull;', '', $arr[$j]['description']);
            //echo '<br>descr='.$arr[$j]['description'];

            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        'type' => $arr[$j]['type'], // Тип товара, например - монитор
                        'name' => $stringRus->strToLowerRus(htmlspecialchars($arr[$j]['name'])), // Название товара
                        'currency' => $arr[$j]['currency'], // Буквенный код валюты, например UAH
                        'delivery' => $this->settings['delivery'], // Доставка товара (false - Самовызов, true - доставка, описаная в настройках партнерки яндекса)
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'description' => $stringRus->strToLowerRus(htmlspecialchars($arr[$j]['description'])), // Описание товара
                    )
            );
        } // end for
        //ucwords(strtolower($bar));

        /*
         * Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->end();
    }

// end of function SaveYandexXML()
    // ================================================================================================
    // Function : SaveIuaXML()
    // Date: 8.10.2008
    // Returns : true,false / Void
    // Description : Save iUA XML
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SaveIuaXML($arr) {
        $db = new DB();
        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['iua'],
            'url' => $this->settings['url'],
        );

        /*
         * Инициализируем библиотеку
         */
        $price = new Bigmir_Export($options);

        /*
         * Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->start();

        /*
         * Например делаем выборку наших категорий из базы данных так
         */
        $q = "SELECT `" . TblModCatalog . "`.*, `" . TblModCatalogSprName . "`.`name`
        FROM `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
        WHERE `" . TblModCatalog . "`.`id`>0
        AND `" . TblModCatalog . "`.`visible`='2'
        AND `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
        AND `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
        ORDER BY `" . TblModCatalog . "` .`move` asc";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }


        /*
         * Например делаем выборку курсов валют так
         */
        $price->addCurrency(
                array(
                    'name' => "UAH",
                    'value' => "1",
                )
        );

        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {
            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        'type' => $arr[$j]['type'], // Тип товара, например - монитор
                        'name' => $arr[$j]['name'], // Название товара
                        'currency' => $arr[$j]['currency'], // Буквенный код валюты, например UAH
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'description' => $arr[$j]['description'], // Описание товара
                    )
            );
        } // end for

        /*
         * Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->end();
    }

// end of function SaveIuaXML
    // ================================================================================================
    // Function : SaveMarketgidXML()
    // Date: 08.06.2010
    // Returns : true,false / Void
    // Description : Save Yandex XML
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SaveMarketgidXML($arr) {
        $db = new DB();

        $options = array(
            'market_name' => $this->settings['name'], // Название вашего магазина
            'company_name' => $this->settings['company'], // Название компании
            'encoding' => 'WINDOWS-1251', // Кодировка
            'file_name' => SITE_PATH . $this->exportFolder . $this->resultXMLFilenameArr['marketgid'], // Имя файла
            'url' => $this->settings['url'], // Адрес сайта магазина
        );

        /*
         * Инициализируем библиотеку
         */
        $price = new Marketgid_Export($options);

        /*
         * Начинаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->start();

        /*
         * Например делаем выборку наших категорий из базы данных так
         */
        $q = "SELECT `" . TblModCatalog . "`.*, `" . TblModCatalogSprName . "`.`name`
        FROM `" . TblModCatalog . "`, `" . TblModCatalogSprName . "`
        WHERE `" . TblModCatalog . "`.`id`>0
        AND `" . TblModCatalog . "`.`visible`='2'
        AND `" . TblModCatalog . "`.`id`=`" . TblModCatalogSprName . "`.`cod`
        AND `" . TblModCatalogSprName . "`.`lang_id`='" . $this->lang_id . "'
        ORDER BY `" . TblModCatalog . "` .`move` asc";
        $res = $db->db_Query($q);
        while ($r = $db->db_FetchAssoc()) {
            $price->addCategory(
                    array(
                        'id' => $r['id'], // ИД
                        'name' => stripslashes($r['name']), // Название категории
                        'parent' => $r['level'], // ИД родительской категории если есть
                    )
            );
        }

        /*
         * Например делаем выборку курсов валют так
         */
        if ($this->settings['currency']) {
            $currencies = new SystemCurrencies();
            $price->addCurrency(
                    array(
                        'name' => "USD",
                        'value' => $currencies->GetValue(5/* usd */),
                    )
            );
        } else {
            $price->addCurrency(
                    array(
                        'name' => "USD",
                        'value' => "1",
                    )
            );
        }

        $n = count($arr);
        for ($j = 0; $j < $n; $j++) {
            $price->addOffer(
                    array(
                        'id' => $arr[$j]['id'], // ИД товара
                        'category' => $arr[$j]['category'], // ИД категории в которой находится товар
                        'priority' => $arr[$j]['priority'], // Приоритет - пока не используется
                        'img' => $this->settings['url'] . $arr[$j]['img'], // Адрес(URL) картинки
                        'click' => $this->settings['url'] . $arr[$j]['click'], // Адрес(URL) куда должен перейти пользователь на ваш сайт при клике на товар
                        'type' => $arr[$j]['type'], // Тип товара, например - монитор
                        'name' => $arr[$j]['name'], // Название товара
                        'currency' => $arr[$j]['currency'], // Буквенный код валюты, например UAH
                        //'delivery'      => $this->settings['delivery'],            // Доставка товара (false - Самовызов, true - доставка, описаная в настройках партнерки яндекса)
                        'price' => $arr[$j]['price'], // Цена товара в указаной валюте
                        'description' => $arr[$j]['description'], // Описание товара
                    )
            );
        } // end for

        /*
         * Завершаем создание прайс-листа.
         * Этот метод вызывать обязательно.
         */
        $price->end();
    }

// end of function SaveMarketgidXML()
}
