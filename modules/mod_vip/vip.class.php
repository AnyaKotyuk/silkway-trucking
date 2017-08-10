<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 30.07.14
 * Time: 11:49
 */
class vip {

    public $cod = NULL;
    public $city_cod = NULL;
    public $pageTxt = array();

    public $fltr = NULL;
    public $start = NULL;
    public $display = NULL;
    public $page = NULL;

    public $h1 = NULL;
    public $title = NULL;
    public $description = NULL;
    public $keywords = NULL;
    public $breadcrumb = NULL;

    public $redirectLang_id = NUll;

    function __construct($lang_id=NULL){
        $this->db =  DBs::getInstance();
        if(empty($lang_id)){
            if( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
        }else{
            $this->lang_id = $lang_id;
        }
        $this->Form = check_init('FormCity', 'FrontForm', "'form_city'");
        $this->Lang = check_init("SysLang","SysLang");
        if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr');
        if (empty($this->UploadImages)) $this->UploadImages = new UploadImage(118, null, 'uploads/images/'.TblModvippr);
        if(empty($this->multi)) $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
    }

    function getMultiUrls(){
        $multiUrls = NULL;
        $SysLang = check_init('SysLang', 'SysLang', 'NULL, "front"');
        $mas_lang = $SysLang->GetLangData($this->lang_id, 'front');
        foreach($mas_lang as $k=>$v){
            if($v['cod']==$this->lang_id){
                if(!empty($this->pageTxt['translit'])){
                    $link = $this->pageTxt['translit'];
                }else{
                    $link = '';
                }
                $multiUrls[$SysLang->GetLangShortName($v['cod'])] = $this->Link($link, true);
            }else{
                //создавать нужно именно новый обьект, чтобы сформировать в нем новые массивы с данными на нужной языковой версии.
                $vip = new vip($v['cod']);
                $vip->cod = $this->cod;
                $vip->initPageTxt();
                if(!empty($vip->pageTxt['translit'])){
                    $link = $vip->pageTxt['translit'];
                }else{
                    $link = '';
                }
                $multiUrls[$SysLang->GetLangShortName($v['cod'])] = $vip->Link($link, true, $v['cod']);
                unset($vip);
            }
        }
        //var_dump($multiUrls);
        return $multiUrls;
    }

    function Link($link_city = NULL, $addLink = true,$lang_id=NULL){
        if($addLink){
            if( !defined("_LINK")) {
                $Lang = new SysLang(NULL, "front");
                $tmp_lang = $Lang->GetDefFrontLangID();
                if( ($Lang->GetCountLang('front')>1 OR isset($_GET['lang_st'])) AND $this->lang_id!=$tmp_lang) {
                    define("_LINK", "/".$Lang->GetLangShortName($this->lang_id)."/");
                }
                else {
                    define("_LINK", "/");
                }
            }

            if(empty($lang_id)){
                $lang_id = $this->lang_id;
                $return_link = _LINK;
            }else{
                $Lang = check_init('SysLang', 'SysLang', 'NULL, "front"');
                if($lang_id!=$Lang->GetDefFrontLangID()){
                    $return_link = "/".$Lang->GetLangShortName($lang_id)."/";
                }else{
                    $return_link = "/";
                }
            }

            $return_link .= 'vip/';
        }else{
            $return_link = '/vip/';
        }
        if(!empty($link_city)){
            $return_link .= $link_city.'.html';
        }
        return $return_link;
    }

    function showFormSearch($arrvip = NULL){
        $arrCity = array();
        foreach($arrvip as $rowvip){
            if(!empty($rowvip['city_cod']))
            {
                $arrCity[$rowvip['city_cod']] = $rowvip['city_name'];
                $arrCityTranslit[$rowvip['city_cod']] = $this->get_city_translit($rowvip['city_cod']);
            }
        }
        if(empty($arrCity)) return false;
        $city_cod=$this->get_city_cod($this->city_cod);
        return View::factory('/modules/mod_vip/tpl_vip/tpl_form_search.php')
            ->bind('arrCity',$arrCity)
            ->bind('city_cod',$city_cod)
            ->bind('arrCityTranslit',$arrCityTranslit)
            ->bind('go',$this->multi['TXT_GO'])
            ->bind('labelCity',$this->multi['TXT_CHOOSE_YOUR_CITY'])
            ->bind('vipLabel',$this->multi['TXT_CHOISE_vip_TO_VISIT']);
    }

    function get_city_cod($name)
    {
        $q="SELECT cod FROM `".TblModCity."`
        WHERE translit='".$name."'
        ";
        $res = $this->db->db_Query( $q );
        if( !$res OR !$this->db->result) return false;
        $cod = $this->db->db_FetchAssoc();
        return $cod['cod'];
    }

    function get_city_translit($cod)
    {
        $q="SELECT translit FROM `".TblModCity."`
        WHERE cod='".$cod."'
        ";
        $res = $this->db->db_Query( $q );
        if( !$res OR !$this->db->result) return false;
        $cod = $this->db->db_FetchAssoc();
        return $cod['translit'];
    }

    function showvipList(){
        $arrvip = $this->getArrvip();
        $arrStart = $arrvip;
//        var_dump($arrStart);
        //$form = $this->showFormSearch($arrvip);
        $arrShow = array();
        //filter by city
        /*
        $city_cod=$this->get_city_cod($this->city_cod);
        if(!empty($city_cod)){
            foreach($arrvip as $rowvip){
                if($rowvip['city_cod']==$city_cod){
                    $arrShow[] = $rowvip;
                }
            }
            $arrvip = $arrShow;
            $arrStart = $arrvip;
        }
        */
        //limited
        $arrShow = array();
        $rowStart = $this->start;
        $count = count($arrvip);
        $rowEnd = $this->page * $this->display;
        if($rowEnd>$count) $rowEnd = $count;
//        echo '$rowStart='.$rowStart.' $rowEnd='.$rowEnd;
        for($i=$rowStart;$i<$rowEnd;$i++){
            $arrShow[] = $arrvip[$i];
        }
        $count = count($arrStart);
        if($count>$this->display){
            $link = _LINK.'vip/';
            if(!empty($this->city_cod)) $param_url = '?city_cod='.$this->city_cod;
            else $param_url = '';
            $pages = $this->Form->WriteLinkPagesStatic( $link, $count, $this->display, $this->start, NULL, $this->page,$param_url );
        }else{
            $pages = '';
        }

        /*
        $per=check_init("hotels","hotels");
        ob_start();
        $per->showRandHotels();
        $hotels = ob_get_clean();
        if(empty($hotels) AND empty($pages) AND empty($per) AND empty($arrShow) AND empty($form))
            $content=$this->multi['_MSG_CONTENT_EMPTY'];
        */
        if(empty($pages) AND empty($per) AND empty($arrShow) AND empty($form))
            $content=$this->multi['_MSG_CONTENT_EMPTY'];

        echo View::factory('/modules/mod_vip/tpl_vip/tpl_vip_list.php')
            ->bind('form',$form)
            ->bind('content',$content)
            ->bind('pages',$pages)
            ->bind('per',$per)
            ->bind('arrvip',$arrShow)
            ->bind('more',$this->multi['_TXT_READ_MORE'])
            //->bind('hotels',$hotels)
        ;
    }

    function getArrvip(){
        $q = "SELECT
            `".TblModvippr."`.*
        FROM
            `".TblModvippr."`
        WHERE
            `".TblModvippr."`.lang_id='".$this->lang_id."' and
            `".TblModvippr."`.visible='1' and
            `".TblModvippr."`.`name`!=''
             order by `".TblModvippr."`.move DESC ";
        $res = $this->db->db_Query( $q );
//     echo "<br>q=".$q." res=".$res;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        echo "<br>rows=".$rows;
        $array = array();
        for( $i = 0; $i <$rows; $i++ ){
            $array[] = $this->db->db_FetchAssoc();
        }
        for( $i = 0; $i <$rows; $i++ ){
            if(!empty($array[$i]['img2'])){
                $path = '/images/spr/'.TblModvippr.'/'.$array[$i]['img2'];
                $array[$i]['src'] = ImageK::getResizedImg($path,'size_rect=132x88',85);
            }
            $array[$i]['name'] = stripslashes($array[$i]['name']);
            $array[$i]['name_html'] = htmlspecialchars($array[$i]['name']);
            $array[$i]['link'] = $this->Link($array[$i]['translit']);
        }
        return $array;
    }

    function getCodvipByStr($str=''){
        if(empty($str)) return false;
//        echo '$str='.$str;
        $q = "SELECT
            `".TblModvippr."`.*
        FROM
            `".TblModvippr."`
        WHERE
            `".TblModvippr."`.lang_id='".$this->lang_id."' and
            `".TblModvippr."`.`name`!='' and
            `".TblModvippr."`.`visible` = '1' and
            `".TblModvippr."`.`translit` = '".$str."'  ";
        $res = $this->db->db_Query( $q );
//        echo "<br>q=".$q." res=".$res;
        if( !$res OR !$this->db->result) return false;
        $cod = 0;
        $rows = $this->db->db_GetNumRows();
        if($rows>0){
            $row = $this->db->db_FetchAssoc();
            $this->initPageTxt($row);
            $cod = $row['cod'];
        }else{
            $q = "SELECT
            `".TblModvippr."`.*
            FROM
            `".TblModvippr."`
            WHERE
            `".TblModvippr."`.`name`!='' and
            `".TblModvippr."`.`visible` = '1' and
            `".TblModvippr."`.`translit` = '".$str."'  ";
            $res = $this->db->db_Query( $q );
//        echo "<br>q=".$q." res=".$res;
            if( !$res OR !$this->db->result) return false;
            $rows = $this->db->db_GetNumRows();
            if($rows>0){
                $row = $this->db->db_FetchAssoc();
                $cod = $row['cod'];
                $this->redirectLang_id = $row['lang_id'];
            }
        }
        return $cod;
    }

    function initPageTxt($pageTxt = NULL){
        if(empty($this->pageTxt)){
            if(!empty($pageTxt)){
                $this->pageTxt = $pageTxt;
            }elseif(!empty($this->cod)){
                $q = "SELECT
                `".TblModvippr."`.*,
            FROM
                `".TblModvippr."`
            WHERE
                `".TblModvippr."`.lang_id='".$this->lang_id."' and
                `".TblModvippr."`.`name`!='' and
                `".TblModvippr."`.`cod` = '".$this->cod."'  ";
                $res = $this->db->db_Query( $q );
//                echo "<br>q=".$q." res=".$res;
                if( !$res OR !$this->db->result) return false;
                $rows = $this->db->db_GetNumRows();
                if($rows>0){
                    $this->pageTxt = $this->db->db_FetchAssoc();
                }
            }
        }
    }

    function ShowPath($breadcrumb){
        $this->initPageTxt();
        if(empty($this->pageTxt['name'])) return false;
        $breadcrumb .= '<li><a href="'.$this->Link($this->pageTxt['translit']).'">' . $this->pageTxt['name'] . '</a></li>';
        return $breadcrumb;
    }

    function setSeoData(){
        $this->initPageTxt();
        if(!empty($this->pageTxt)){
            if(!empty($this->pageTxt['h1']))
                $this->h1 = $this->pageTxt['h1'];
            else
                $this->h1 = $this->pageTxt['name'];

            if(!empty($this->pageTxt['mtitle']))
                $this->title = $this->pageTxt['mtitle'];
            else
                $this->title = $this->pageTxt['name'];

            $this->description = $this->pageTxt['mdescr'];
            $this->keywords = $this->pageTxt['mkeywords'];
        }
    }
    function getCountOfPerson($cod){
        $q = "SELECT
        `" . TblModvippr . "`.cod as codExc,
        `" . TblModCountOfPeoplVip . "`.*
        FROM
            `" . TblModvippr . "`
            LEFT JOIN `" . TblModCountOfPeoplVipComp . "`
            ON  `" . TblModvippr . "`.cod = `" . TblModCountOfPeoplVipComp . "`.cod
            LEFT JOIN `" . TblModCountOfPeoplVip . "`
            ON ( `" . TblModCountOfPeoplVip . "`.cod = `" . TblModCountOfPeoplVipComp . "`.cod_countofpeopl and `".TblModCountOfPeoplVip."`.lang_id='".$this->lang_id."')
        WHERE
            `" . TblModvippr . "`.lang_id='" . $this->lang_id . "'
        AND `" . TblModvippr . "`.`name`!=''
        AND `" . TblModvippr . "`.`cod`='". $cod ."'
        AND `" . TblModvippr . "`.`visible` = '1'";

        $res = $this->db->db_Query($q);
//        echo "<br>q=".$q." res=".$res;
        if (!$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo "<br>rows=".$rows;
        $array = array();

        if($rows>0){
            for ($i = 0; $i < $rows; $i++) {
                $row = $this->db->db_FetchAssoc();
             $array[] = $row;
            }
        }
        return $array;
    }

    function getDataForEccursionForm($tableName = TblModExcursionForm, $order = NULL, $where = NULL){

        $q="SELECT `".$tableName."`.*
        FROM `".$tableName."`
        WHERE lang_id='".$this->lang_id."'";
        if($where){
            $q .= " AND ". $where;
        }
        if($order){
            $q.="ORDER BY '" . $order . "'";
        }
            echo '$q='.$q;

//            echo '$q='.$q;
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        $array=array();
        for($i=0;$i<$rows;$i++){
            $array[] = $this->db->db_FetchAssoc();
        }
        return $array;
    }

    function showvipFull(){
//    $this->update_translit(TblModtoursSpr);
        $this->initPageTxt();
//        var_dump($this->pageTxt);
        if(!empty($this->pageTxt['img2'])){
            $this->pageTxt['path'] = '/images/spr/'.TblModvippr.'/'.$this->pageTxt['img2'];
            $this->pageTxt['src'] = ImageK::getResizedImg($this->pageTxt['path'],'size_rect=132x88',85);
            $this->pageTxt['name_html'] = htmlspecialchars($this->pageTxt['name']);
        }
        $page = check_init("PageUser","PageUser");
//        $arrCountOfPeople = $this->getDataForEccursionForm( TblModCountOfPeoplVip, null, "`vip`=" . $this->pageTxt['cod']);
        $arrCountOfPeople = $this->getCountOfPerson( $this->pageTxt['cod']);

//        var_dump($arrCountOfPeople);

        $formDescr = $page->FrontendPages->getSpecContentByCod(13);

        /*
        $formFeedBack = View::factory('/modules/mod_feedback_vip/tpl_feedback_vip/tpl_vip_form.php')
            ->bind('multi',$this->multi)
            ->bind('lang_id',$this->lang_id)
            ->bind('cod',$this->pageTxt['cod'])
            ->bind('arrCountOfPeople',$arrCountOfPeople)
            ->bind('formDescr',$formDescr)
        ;
        */
        if(empty($hotels) AND empty($formFeedBack) AND empty($per) AND empty($this->pageTxt))
            $content=$this->multi['_MSG_CONTENT_EMPTY'];
        echo View::factory('/modules/mod_vip/tpl_vip/tpl_vip_full.php')
            ->bind('arrvip',$this->pageTxt)
            ->bind('per',$per)
            ->bind('content',$content)
            ->bind('formFeedBack',$formFeedBack)
        ;
        $FeedbackLayout = check_init('FeedbackLayout', 'FeedbackLayout');
        $formFeedBack = $FeedbackLayout->show_form();
    }

    function showvipMap(){
        $this->initPageTxt();
        $map = $this->pageTxt['map'];
        echo View::factory('/modules/mod_vip/tpl_vip/tpl_vip_map.php')
            ->bind('map',$map);
    }

    function getArrvipForMainPage()
    {
        $arrReturn = array();
        $arrRating = $this->getArrRating();
        if (!empty($arrRating)) {
            $str_vip = implode(',', array_keys($arrRating));
            $q = "SELECT *
        FROM
            `" . TblModvippr . "`
        WHERE
            `" . TblModvippr . "`.lang_id='" . $this->lang_id . "' and
            `" . TblModvippr . "`.`name`!='' and
            `" . TblModvippr . "`.`visible` = '1' and
            `" . TblModvippr . "`.`cod` in (".$str_vip.") ";
            $res = $this->db->db_Query($q);
//        echo "<br>q=".$q." res=".$res;
            if (!$res OR !$this->db->result) return false;
            $rows = $this->db->db_GetNumRows();
            //echo "<br>rows=".$rows;
            if($rows>0){
                $array = array();
                for ($i = 0; $i < $rows; $i++) {
                    $row = $this->db->db_FetchAssoc();
                    $array[$row['cod']] = $row;
                }
                $i=0;
                foreach($arrRating as $key=>$value){
                    if($i>=20) break;
                    if(!isset($array[$key])) continue;
                    $row = $array[$key];
                    $row['rating'] = $value;
                    $row['link'] = $this->Link($row['translit']);
                    $row['name'] = stripslashes($row['name']);
                    $arrReturn[] = $row;
                    $i++;
                }
            }
        }
        return $arrReturn;
    }

    function getRatingShow(){
        $arrRating = $this->getArrRating($this->cod,false,true);
//        var_dump($arrRating);
    }

    function getArrRating($cod=null,$convertForMain = true,$showIp = false){
        $q = "SELECT *
        FROM
            `".TblModvipprRating."`
        WHERE
            `".TblModvipprRating."`.lang_id='".$this->lang_id."' ";
        if(!empty($cod)) $q .= " and `".TblModvipprRating."`.`vip` = '".$cod."' ";
        $res = $this->db->db_Query( $q );
//        echo "<br>q=".$q." res=".$res;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo "<br>rows=".$rows;
        $array = array();
        for( $i = 0; $i <$rows; $i++ ){
            $row = $this->db->db_FetchAssoc();
            if(isset($array[$row['vip']]['count']))$array[$row['vip']]['count']++;
            else $array[$row['vip']]['count'] = 1;
            if(isset($array[$row['vip']]['sum']))$array[$row['vip']]['sum']+=$row['rating'];
            else $array[$row['vip']]['sum'] = $row['rating'];
            if($showIp) $array[$row['vip']]['ip'][] = $row['ip'];
        }
        if($convertForMain){
            $arrSort = array();
            if(!empty($array)){
               foreach($array as $key=>$row){
                   $arrSort[$key] = round($row['sum']/$row['count'],1);
               }
            }
            if(empty($arrSort)) return false;
            arsort($arrSort);
            return $arrSort;
        }else{
            return $array;
        }
    }

    function showvipOnMailPages($title = NULL){
        $arrvip = $this->getArrvipForMainPage();
        if(empty($arrvip)) return false;
        $rowsEnd = count($arrvip);
        $rowsCenter = round($rowsEnd/2+0.4,0);
        echo View::factory('/modules/mod_vip/tpl_vip/tpl_vip_for_main_pages.php')
            ->bind('title',$title)
            ->bind('arrvip',$arrvip)
            ->bind('rowsCenter',$rowsCenter)
            ->bind('rowsEnd',$rowsEnd)
        ;
    }
//    function update_translit($tbl)
//    {
//        $cr=check_init("SysSpr","SysSpr");
//        $crypt=check_init("Crypt","Crypt");
////        for($i=1;$i<=3;$i++){
//            $q="SELECT cod,name,id FROM `".$tbl."` ";
//
//            $res = $this->db->db_Query( $q );
////        echo "<br>q=".$q." res=".$res;
//            if( !$res OR !$this->db->result) return false;
//            $rows = $this->db->db_GetNumRows();
////            echo "<br>rows=".$rows;
//            $array = array();
//            for( $j = 0; $j <$rows; $j++ ){
//                $row = $this->db->db_FetchAssoc();
//                $array[]=$row;
//            }
//        for($k=0;$k<count($array);$k++)
//        {
//            $translit=$crypt->GetTranslitStr($array[$k]['name']);
//            $q="UPDATE `".$tbl."` set
//            `translit`='".$translit."' WHERE
//            `name`='".$array[$k]['name']."'
//            ";
//                $res = $this->db->db_Query($q);
//        }
////
////        var_dump($array);
//
//    }

}