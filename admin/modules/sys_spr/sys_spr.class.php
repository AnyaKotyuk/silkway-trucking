<?php

include_once( SITE_PATH.'/admin/include/defines.inc.php' );
include_once( SITE_PATH.'/admin/modules/sys_spr/sys_spr.define.php' );
include_once( SITE_PATH.'/sys/classes/sysSpr.class.php' );

/**
* Class SysSpr
* Class definition for all actions with reference-books
* @package System Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 02.04.2012
* @copyright (c) 2005+ by SEOTM
*/
class SysSpr extends SystemSprSetting {

    public  $user_id = NULL;
    public  $module = NULL;
    public  $module_name = NULL;
    public  $info_msg = NULL;

    public  $sort = NULL;
    public  $display = 15;
    public  $start = 0;
    public  $fln = NULL;
    public  $spr = NULL;
    public  $srch = NULL;
    public  $Err = NULL;

    public  $field_type = NULL;
    public  $uselevels = 0;
    public  $level = NULL;
    public  $level_new = NULL;
    public  $usemeta = 0;
    public  $usemove = 0;
    public  $usetranslit = 0;
    public  $usecolors = 0;
    public  $usename = 1;
    public  $msg = NULL;
    public  $Rights = NULL;
    public  $Form = NULL;
    public  $script = NULL;
    public  $root_script = NULL;
    public  $parent_script = NULL;
    public  $parent_id = NULL;
    public  $Msg_text = NULL;
    public  $db = NULL;
    public  $useuploadimages = NULL;
    public  $useuploadfiles = NULL;
    public  $asc_desc = 'desc';

    public $arraySettings = array();
    public $arraySettingsType = array();
    public $arraySettingsForLangAndMove = array();
    public $arrayValues = array();

    /**
    * SysSpr::__construct()
    *
    * @param integer $user_id
    * @param integer $module_id
    * @param integer $display
    * @param string $sort
    * @param integer $start
    * @param integer $width
    * @param integer $spr
    * @return void
    */
    function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL, $spr=NULL) {
            //Check if Constants are overrulled
            ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
            ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
            ( $display  !="" ? $this->display = $display  : $this->display = 20   );
            ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
            ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
            ( $spr      !="" ? $this->spr     = $spr      : $this->spr     = NULL  );

            if ( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;

            if( defined("AJAX_RELOAD") AND AJAX_RELOAD==1){
                $this->make_encoding = 1;
                $this->encoding_from = 'utf-8';
                $this->encoding_to = 'windows-1251';
            }

            if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module);
            if (empty($this->db)) $this->db = DBs::getInstance();
            if (empty($this->Form)) $this->Form = check_init('form_sys_spr', 'Form', "'form_sys_spr'");
            if (empty($this->Crypt)) $this->Crypt = check_init('Crypt', 'Crypt');
            if (empty($this->ln_sys)) $this->ln_sys = check_init('LangSys', 'SysLang');

            if (empty($this->multi)) $this->multi = check_init_txt('TblBackMulti',TblBackMulti);

        parent::__construct();

    } // End of SysSpr Constructor

    /**
     *
     * @programmer Bogdan Iglinsky
     */
    function initArraySettings(){
        $arrExistSettings =  $this->arrExistSettings;
        $keys = array_keys($_REQUEST);
        foreach($arrExistSettings as $key=>$value):
//            echo '<br>$key='.$key.' $value='.$_REQUEST[$key];
            if(in_array($key,$keys) && $_REQUEST[$key]==1){
                if($value['type']=='box'){
                    if(isset($value['fields']) && !empty($value['fields'])){
                        foreach($value['fields'] as $keyField=>$fieldValue){
                            $_REQUEST[$keyField] = $fieldValue;
                        }
                    }
                    continue;
                }
                if(isset($arrExistSettings[$key]['lang']))
                    $this->arraySettingsForLangAndMove[$arrExistSettings[$key]['lang']][] = $arrExistSettings[$key];
                $this->arraySettings[$key] = $arrExistSettings[$key];
                if(isset($arrExistSettings[$key]['type']))
                    $this->arraySettingsType[$arrExistSettings[$key]['type']][] = $arrExistSettings[$key];
            }
        endforeach;

        $this->initSort();
//        var_dump($this->arraySettings);
//        var_dump($this->arraySettingsForLangAndMove);
    }

    /**
     *
     * @programmer Bogdan Iglinsky
     */
    function getValuesFromRequest(){
//        var_dump($_REQUEST);
        if(isset($this->arraySettingsForLangAndMove['oneLang']) && !empty($this->arraySettingsForLangAndMove['oneLang'])){
            foreach($this->arraySettingsForLangAndMove['oneLang'] as $rowTxt){
                switch($rowTxt['type']){
                    case 'spr':
                        $this->arrayValues[$rowTxt['filterName']] = AntiHacker::AntiHackRequest($rowTxt['filterName']);
                        switch($rowTxt['typeLink']){
                            case 'one':
                                $this->arrayValues[$rowTxt['name']] = AntiHacker::AntiHackRequest($rowTxt['name']);
                                break;
                            case 'onemany':
                                $this->arrayValues[$rowTxt['nameMany']] = AntiHacker::AntiHackArrayRequest($rowTxt['nameMany']);
                            case 'many':
                                $this->arrayValues[$rowTxt['name']] = AntiHacker::AntiHackArrayRequest($rowTxt['name']);
                                break;

                        }
                        break;
                    case 'datestatus':
                        $this->arrayValues[$rowTxt['name'].'_status'] = AntiHacker::AntiHackRequest($rowTxt['name'].'_start');
                        $this->arrayValues[$rowTxt['name'].'_end'] = AntiHacker::AntiHackRequest($rowTxt['name'].'_end');
                        $this->arrayValues[$rowTxt['name']] = AntiHacker::AntiHackRequest($rowTxt['name']);
                        break;
                    case 'icon':
                        if (!isset($_FILES[$rowTxt['helpField']])){
                            $this->arrayValues[$rowTxt['helpField']] = NULL;
                        }else{
                            $this->arrayValues[$rowTxt['helpField']] = $_FILES[$rowTxt['helpField']];
                        }
                    default:
//                        echo '<br>name='.$rowTxt['name'];
                    $this->arrayValues[$rowTxt['name']] = AntiHacker::AntiHackRequest($rowTxt['name']);
                    break;
                }
            }
        }
        if(isset($this->arraySettingsForLangAndMove['manyLang']) && !empty($this->arraySettingsForLangAndMove['manyLang'])){
            foreach($this->arraySettingsForLangAndMove['manyLang'] as $rowTxt){
                switch($rowTxt['type']){
                    case 'spr':
                        $this->arrayValues[$rowTxt['filterName']] = AntiHacker::AntiHackRequest($rowTxt['filterName']);
                        $this->arrayValues[$rowTxt['name']] = AntiHacker::AntiHackArrayRequest($rowTxt['name']);
                        break;
                    case 'datestatus':
                        $this->arrayValues[$rowTxt['name'].'_status'] = AntiHacker::AntiHackArrayRequest($rowTxt['name'].'_start');
                        $this->arrayValues[$rowTxt['name'].'_end'] = AntiHacker::AntiHackArrayRequest($rowTxt['name'].'_end');
                        $this->arrayValues[$rowTxt['name']] = AntiHacker::AntiHackArrayRequest($rowTxt['name']);
                        break;
                    case 'icon':
                        if (!isset($_FILES[$rowTxt['helpField']])){
                            $this->arrayValues[$rowTxt['helpField']] = NULL;
                        }else{
                            $this->arrayValues[$rowTxt['helpField']] = $_FILES[$rowTxt['helpField']];
                        }
                    default:
                        $this->arrayValues[$rowTxt['name']] = AntiHacker::AntiHackArrayRequest($rowTxt['name']);
                        break;
                }
            }
        }
    }

    // ================================================================================================
    // Function : AddTbl
    // Version : 1.0.0
    // Date : 09.01.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Dynamicly modify structure of tables
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
   function AddTbl()
    {
        if( $this->usemove==1 ) {
            $this->AutoInsertColumnMove( $this->spr );
            $this->AutoInsertDataIntoColumnMove( $this->spr );
        }

        if($this->uselevels==1){
           // add field level to the table $this->spr
           if ( !$this->Rights->IsFieldExist($this->spr, "level") ) {
               $q = "ALTER TABLE `".$this->spr."` ADD `level` INT( 11 ) UNSIGNED DEFAULT '0';";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;

               $q = "ALTER TABLE `".$this->spr."` ADD INDEX ( `level` ) ;";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
           if ( !$this->Rights->IsFieldExist($this->spr, "node") ) {
               $q = "ALTER TABLE `".$this->spr."` ADD `node` SMALLINT( 5 ) UNSIGNED DEFAULT '0';";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
               $this->AutoInsertColumnNode( $this->spr );
           }
        }

       if($this->usemeta==1){
           // add field mtitle, mdescr, mkeywords to the table $this->spr for meta data
           if ( !$this->Rights->IsFieldExist($this->spr, "mtitle") ) {
               $q = "ALTER TABLE `".$this->spr."` ADD `mtitle` VARCHAR( 255 ) ,
                     ADD `mdescr` VARCHAR( 255 ) ,
                     ADD `mkeywords` VARCHAR( 255 ) ;";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
       }

       if($this->usetranslit==1){
           // add field translit
           if ( !$this->Rights->IsFieldExist($this->spr, "translit") ) {
               $q = "ALTER TABLE `".$this->spr."` ADD `translit` VARCHAR( 255 );";
               $res = $this->Rights->db_Query( $q );
               //echo '<br>$q='.$q.' $res='.$res;
               if( !$res )return false;
           }
       }


        foreach($this->arraySettings as $rowTxt){
            if($rowTxt['type']=='values' || $rowTxt['type']=='footnote') continue;
            $name = $rowTxt['name'];
            if($rowTxt['type']=='spr'){
                if($rowTxt['typeLink']=='many'){
                    $spr = $this->spr.'_'.$name;
                    if ( !$this->db->IsTableExist($spr) ) {
                        $q = "CREATE TABLE `".$spr."` (
                      `id` int(4) unsigned NOT NULL auto_increment
                      ,`cod` int(4) unsigned NOT NULL default '0'
                      ,`cod_".$name."` int(4) unsigned NOT NULL default '0' ";
                      if($rowTxt['lang']=='manyLang')$q .=",`lang_id` int(4) unsigned NOT NULL default '0'";
                      $q .=",PRIMARY KEY  (`id`)
                      ,KEY `cod` (`cod`)";
                        if($rowTxt['lang']=='manyLang')$q .=" ,KEY `lang_id` (`lang_id`)";
                      $q.=",KEY `cod_".$name."` (`cod_".$name."`)
                      )";
                        $res = $this->db->db_Query($q);
                        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
                        if ( !$res OR !$this->db->result ) return false;
                    }
                    continue;
                }
                if($rowTxt['typeLink']=='onemany'){
                    $nameMany = $rowTxt['nameMany'];
                    $spr = $this->spr.'_'.$name;
                    if ( !$this->db->IsTableExist($spr) ) {
                        $q = "CREATE TABLE `".$spr."` (
                      `id` int(4) unsigned NOT NULL auto_increment
                      ,`cod` int(4) unsigned NOT NULL default '0'
                      ,`cod_".$name."` int(4) unsigned NOT NULL default '0'
                      ,`cod_".$nameMany."` int(4) unsigned NOT NULL default '0'
                      ";
                      if($rowTxt['lang']=='manyLang')$q .=",`lang_id` int(4) unsigned NOT NULL default '0'";
                      $q .=",PRIMARY KEY  (`id`)
                      ,KEY `cod` (`cod`)";
                        if($rowTxt['lang']=='manyLang')$q .=" ,KEY `lang_id` (`lang_id`)";
                      $q.=",KEY `cod_".$name."` (`cod_".$name."`)
                      ,KEY `cod_".$nameMany."` (`cod_".$nameMany."`)
                      )";
                        $res = $this->db->db_Query($q);
                        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
                        if ( !$res OR !$this->db->result ) return false;
                    }
                    continue;
                }
            }
            if($rowTxt['type']=='datestatus'){
                if ( !$this->Rights->IsFieldExist($this->spr, $name) ) {
                    $q = "ALTER TABLE `".$this->spr."` ADD `".$name."` datetime;  ";
                    $res = $this->Rights->db_Query( $q );
//                    echo '<br>$q='.$q.' $res='.$res;
                    if( !$res )return false;
                    if($rowTxt['addIndex']){
                        $q = "ALTER TABLE `" . $this->spr . "` ADD INDEX ( `" . $name . "` ) ;";
                        $res = $this->Rights->db_Query( $q );
//                        echo '<br>$q='.$q.' $res='.$res;
                        if( !$res )return false;
                    }
                }
                $name = $rowTxt['name'].'_end';
                if ( !$this->Rights->IsFieldExist($this->spr, $name) ) {
                    $q = "ALTER TABLE `".$this->spr."` ADD `".$name."` datetime;  ";
                    $res = $this->Rights->db_Query( $q );
//                    echo '<br>$q='.$q.' $res='.$res;
                    if( !$res )return false;
                    if($rowTxt['addIndex']){
                        $q = "ALTER TABLE `" . $this->spr . "` ADD INDEX ( `" . $name . "` ) ;";
                        $res = $this->Rights->db_Query( $q );
//                        echo '<br>$q='.$q.' $res='.$res;
                        if( !$res )return false;
                    }
                }
                $name = $rowTxt['name'].'_status';
                if ( !$this->Rights->IsFieldExist($this->spr, $name) ) {
                    $q = "ALTER TABLE `".$this->spr."` ADD `".$name."` set('a','e','n') DEFAULT 'a';  ";
                    $res = $this->Rights->db_Query( $q );
//                    echo '<br>$q='.$q.' $res='.$res;
                    if( !$res )return false;
                    if($rowTxt['addIndex']){
                        $q = "ALTER TABLE `" . $this->spr . "` ADD INDEX ( `" . $name . "` ) ;";
                        $res = $this->Rights->db_Query( $q );
//                        echo '<br>$q='.$q.' $res='.$res;
                        if( !$res )return false;
                    }
                }
                continue;
            }
//            echo '$name='.$name;
            if (!$this->Rights->IsFieldExist($this->spr, $name)) {
                switch ($rowTxt['type']) {
                    case 'set':
                        $q = "ALTER TABLE `" . $this->spr . "` ADD `" . $name . "` set('0','1') DEFAULT '1'";
                        break;
                    case 'date':
                        $q = "ALTER TABLE `" . $this->spr . "` ADD `" . $name . "` datetime   ";
                        break;
                    case 'icon':
                    case 'text':
                    case 'textarea':
                        $q = "ALTER TABLE `" . $this->spr . "` ADD `" . $name . "` VARCHAR( 255 ) ;";
                        break;
                    case 'spr':
                        if($rowTxt['typeLink']=='one')
                        $q = "ALTER TABLE `" . $this->spr . "` ADD `" . $name . "` INT( 11 ) ;";
                        break;
                    case 'htmlarea':
                        $q = "ALTER TABLE `" . $this->spr . "` ADD `" . $name . "` text; ";
                        break;
                }
                if(isset($q) && !empty($q)){
                    $res = $this->Rights->db_Query($q);
                    //echo '<br>$q='.$q.' $res='.$res;
                    if (!$res) return false;
                }
                if (isset($rowTxt['addIndex']) && $rowTxt['addIndex']) {
                    $q = "ALTER TABLE `" . $this->spr . "` ADD INDEX ( `" . $name . "` ) ;";
                    $res = $this->Rights->db_Query($q);
                    //echo '<br>$q='.$q.' $res='.$res;
                    if (!$res) return false;
                }
            }
        }

    }//end of function AddTbl()

    /**
     * @programmer Bogdan Iglinsky
     *
     */
    function initSort(){
        if( empty($this->sort) ) {
            $tmp_db = DBs::getInstance();
            $q = "SELECT * FROM `".$this->spr."` WHERE 1 LIMIT 1";
            $res = $tmp_db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            //print_r($tmp_db->result);
            if ( !$res OR !$tmp_db->result ) return false;
            $fields_col = mysql_num_fields($tmp_db->result);
            $this->field_type = mysql_field_type($tmp_db->result,1);
            //echo '<br> $fields_col='.$fields_col.' $this->field_type='.$this->field_type;
            if(isset($this->arraySettingsType['sort']) && !empty($this->arraySettingsType['sort'])){
//                var_dump($this->arraySettingsType['sort']);
                foreach($this->arraySettingsType['sort'] as $rowTxt){
//                    var_dump($rowTxt);
                    $this->sort = $rowTxt['name'];
                    $this->asc_desc = $rowTxt['typeLink'];
                }
            }else {
                if ($fields_col > 4 & $this->field_type != 'string') $this->sort = 'move';
                else $this->sort = 'cod';
            }
        }
    }

    // ================================================================================================
    // Function : GetContent
    // Version : 1.0.0
    // Date : 19.03.2008
    //
    // Parms :
    // Returns : true,false / Void
    // Description : execute SQL query
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 19.03.2008
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetContent($limit='limit')
    {
        // select (R)
        $q=" SELECT `".$this->spr."`.*, srp.name as lang_name
             FROM `".$this->spr."`
             LEFT JOIN `".TblSysLang."` as srp ON (srp.cod=`".$this->spr."`.lang_id AND srp.lang_id='"._LANG_ID."')
             WHERE 1";
        //if( ($this->fln!=NULL) || ($this->srch!=NULL)  ) $q = $q." WHERE 1";
        if( $this->uselevels==1 AND empty($this->srch) ) $q = $q." AND `".$this->spr."`.level='".$this->level."'";
        if( $this->srch!=NULL ) $q = $q." AND (`".$this->spr."`.cod LIKE '%".$this->srch."%' OR `".$this->spr."`.name LIKE '%".$this->srch."%')";
//        var_dump($this->fln);
        if( $this->fln!=NULL ) {
            if ( $this->srch ) $q = $q." AND `".$this->spr."`.lang_id='".$this->fln."'";
            else $q = $q." AND `".$this->spr."`.lang_id='".$this->fln."'";
        }
        if( !empty($this->id_cat)) $q .= " AND `id_cat`='".$this->id_cat."'";
        if( !empty($this->id_param)) $q .= " AND `id_param`='".$this->id_param."'";

        if(isset($this->arraySettingsType['spr']) && !empty($this->arraySettingsType['spr'])){
            foreach($this->arraySettingsType['spr'] as $rowTxt){
                if($rowTxt['typeLink']=='one')
                    if(isset($this->arrayValues[$rowTxt['filterName']]) && !empty($this->arrayValues[$rowTxt['filterName']])){
                        $q .= " AND `".$rowTxt['name']."` = '".$this->arrayValues[$rowTxt['filterName']]."' ";
                    }
            }
        }

        if ($this->fln!=NULL) $q=$q." GROUP BY `".$this->spr."`.cod ORDER BY `".$this->spr."`.".$this->sort." ".$this->asc_desc;
        else $q=$q." ORDER BY `".$this->spr."`.".$this->sort." ".$this->asc_desc;
        if($this->sort!='move') $q .= ", `move` ".$this->asc_desc;
//        if($limit=='limit'  AND $this->srch==NULL ) $q = $q." LIMIT ".$this->start.", ".$this->display;
        $result = $this->Rights->QueryResult($q, $this->user_id, $this->module);
//        echo '<br> $q='.$q.' $this->user_id='.$this->user_id.' $this->module='.$this->module.' $this->Rights->result='.$this->Rights->result. ' $this->spr='.$this->spr.' $res='.$res;
//        echo '<br> $q='.$q.' $result='.$this->Rights->result;
        if ( !$this->Rights->result ) return false;
        if( !isset($this->field_type) OR empty($this->field_type)) $this->field_type = mysql_field_type($this->Rights->result,1);
        //echo '<br> $fields_col='.$fields_col.' $this->field_type='.$this->field_type;
//        var_dump($result);
//        echo $q;
        return $result;
    }//end of function GetContent()


    // ================================================================================================
    // Function : GetContent
    // Version : 1.0.0
    // Date : 19.03.2008
    //
    // Parms :
    // Returns : true,false / Void
    // Description : execute SQL query
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 19.03.2008
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetContentArr($limit='limit')
    {
        // select (R)
        $this->Rights = DBs::getInstance();
        $q=" SELECT `".$this->spr."`.*, srp.name as lang_name
             FROM `".$this->spr."`
             LEFT JOIN `".TblSysLang."` as srp ON (srp.cod=`".$this->spr."`.lang_id AND srp.lang_id='"._LANG_ID."')
             WHERE 1";
        //if( ($this->fln!=NULL) || ($this->srch!=NULL)  ) $q = $q." WHERE 1";
        if( $this->uselevels==1 AND empty($this->srch) ) $q = $q." AND `".$this->spr."`.level='".$this->level."'";
        if( $this->srch!=NULL ) $q = $q." AND (`".$this->spr."`.cod LIKE '%".$this->srch."%' OR `".$this->spr."`.name LIKE '%".$this->srch."%')";
//        var_dump($this->fln);
        if( $this->fln!=NULL ) {
            if ( $this->srch ) $q = $q." AND `".$this->spr."`.lang_id='".$this->fln."'";
            else $q = $q." AND `".$this->spr."`.lang_id='".$this->fln."'";
        }
        if( !empty($this->id_cat)) $q .= " AND `id_cat`='".$this->id_cat."'";
        if( !empty($this->id_param)) $q .= " AND `id_param`='".$this->id_param."'";

        if(isset($this->arraySettingsType['spr']) && !empty($this->arraySettingsType['spr'])){
            foreach($this->arraySettingsType['spr'] as $rowTxt){
                if($rowTxt['typeLink']=='one')
                    if(isset($this->arrayValues[$rowTxt['filterName']]) && !empty($this->arrayValues[$rowTxt['filterName']])){
                        $q .= " AND `".$rowTxt['name']."` = '".$this->arrayValues[$rowTxt['filterName']]."' ";
                    }
            }
        }

        if ($this->fln!=NULL) $q=$q." GROUP BY `".$this->spr."`.cod ORDER BY `".$this->spr."`.".$this->sort." ".$this->asc_desc;
        else $q=$q." ORDER BY `".$this->spr."`.".$this->sort." ".$this->asc_desc;
        if($this->sort!='move') $q .= ", `move` ".$this->asc_desc;
//        if($limit=='limit'  AND $this->srch==NULL ) $q = $q." LIMIT ".$this->start.", ".$this->display;
        $result = $this->Rights->db_Query($q, $this->user_id, $this->module);
//        echo '<br> $q='.$q.' $this->user_id='.$this->user_id.' $this->module='.$this->module.' $this->Rights->result='.$this->Rights->result. ' $this->spr='.$this->spr.' $res='.$res;
//        echo '<br> $q='.$q.' $result='.$this->Rights->result;
        if ( !$this->Rights->result ) return false;
        if( !isset($this->field_type) OR empty($this->field_type)) $this->field_type = mysql_field_type($this->Rights->result,1);
        //echo '<br> $fields_col='.$fields_col.' $this->field_type='.$this->field_type;
//        var_dump($result);
        $n = $this->Rights->db_GetNumRows($result);
//        echo '$n='.$n;
        $res = array();
        for($i = 0; $i<$n; $i++){
            $res[] = $this->Rights->db_FetchAssoc();
        }
        return $res;
    }//end of function GetContent()



    // ================================================================================================
    // Function : show
    // Version : 1.0.0
    // Date : 09.01.2005
    //
    // Parms :         $user_id  / user ID
    //                 $module   / Module read  / Void
    //                 $display  / How many records to show / Void
    //                 $sort     / Sorting data / Void
    //                 $start    / First record for show / Void
    //                 spr       / name of the table for this module
    // Returns : true,false / Void
    // Description : Show data from $module table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
   function show()
    {
        ?>
        <div id="wndw0"><?$this->showList();?></div>
        <script language="JavaScript">
         function makesearch(uri, div_id){
             //document.<?=$this->Form->name?>.task.value='make_search';
             document.<?=$this->Form->name?>.srch.value=document.getElementById('srch').value;
             //alert('task='+document.<?=$this->Form->name?>.task.value);
              $.ajax({
                    type: "POST",
                    dataType : "html",
                    data: '&srch='+document.<?=$this->Form->name?>.srch.value,
                    url: uri,
                    success: function(data){
                      $("#"+div_id).empty();
                      $("#"+div_id).append(data);
                    },
                    beforeSend: function(){
                        $("#"+div_id).html('<div style="border:0px solid #000000; padding-top:5px; padding-bottom:5px; text-align:left;" align="center"><img src="/admin/images/icons/loading_animation_liferay.gif"></div>');
                    }
              });
         }
        </script>
        <?
    } //end of fuinction show

    // ================================================================================================
    // Function : showList
    // Version : 1.0.0
    // Date : 21.05.2008
    //
    // Parms :         $user_id  / user ID
    //                 $module   / Module read  / Void
    //                 $display  / How many records to show / Void
    //                 $sort     / Sorting data / Void
    //                 $start    / First record for show / Void
    //                 spr       / name of the table for this module
    // Returns : true,false / Void
    // Description : Show data from $module table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function showList()
    {
        //======== If don't exist $this->spr then create it! ;) ===========
        $this->CreateSpr( $this->spr );
        $this->AddTbl();
        $this->ShowJS();
        //=================================================================
        /* Init Table  */
        $tbl = new html_table( 0, 'center', 650, 1, 5 );
        $result = $this->GetContent('nolimit');
        $rows = count ($result);
        //echo '<br> rows='.$rows;
        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );

        if ( $this->parent_script!=NULL ) {
            $pg = check_init('PageAdmin', 'PageAdmin');
            $func = $pg->GetFunction( $this->parent_id );
            //echo '<br>$this->parent_id ='.$this->parent_id ;
            $link = str_replace('_AND_', '&', $this->parent_script);
            if(!empty($this->root_script)) $link .= '&parent_script='.$this->root_script;
            echo '<br /><a href="'.$link.'">'.$this->multi['FLD_BACK'].' → '.stripslashes($func['module_name']).'</a>';
        }
        $this->Form->Hidden( 'module_name', $this->module_name );
        $this->Form->Hidden( 'spr', $this->spr );
        $this->Form->Hidden( 'root_script', $this->root_script );
        $this->Form->Hidden( 'parent_script', $this->parent_script );
        $this->Form->Hidden( 'parent_id', $this->parent_id );
        /* Write Table Part */
        AdminHTML::TablePartH();
        ?>
         <tr>
          <td><?
           /* Write Links on Pages */
           $this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );?>
          </td>
         </tr>
         <tr>
          <td>
          <div class="topPanel">
            <div class="SavePanel"><?
           /* Write Top Panel (NEW,DELETE - Buttons) */
           $this->Form->WriteTopPanel( $this->script );
           ?></div><div class="SelectType"><?
           echo $this->Form->TextBox('srch', $this->srch, 30, 'id="srch"');
           $url = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&task=make_search&uselevels='.$this->uselevels.'&level='.$this->level.'&node='.$this->node;
           ?><input type="submit" value="<?=$this->multi['TXT_SEARCH'];?>" onclick="makesearch('<?=$url;?>','wndw0'); return false;"><?
           //echo "<br>fln=".$this->fln;
           if(!empty($this->arraySettingsForLangAndMove['manyLang']))$this->Form->WriteSelectLangChange( $this->script, $this->fln );?>
            </div><?
              if(isset($this->arraySettingsType['spr']) && !empty($this->arraySettingsType['spr'])){
                  foreach($this->arraySettingsType['spr'] as $rowTxt){
                      if($rowTxt['typeLink']=='many' || $rowTxt['typeLink']=='onemany') continue;
//                      var_dump($rowTxt);
//                      $arrNameSpr[$rowTxt['name']] = $this->GetArrNameBySpr($rowTxt['nameSpr']);
                      if(isset($this->arrayValues[$rowTxt['filterName']]))$value = $this->arrayValues[$rowTxt['filterName']];
                      else $value = '';
                      ?><div class="SelectType">
                      <label>
                          <?=$rowTxt['label'];?>
                          <?
                          $params=' onchange="location.href=\''.$this->script.'&'.$rowTxt['filterName'].'=\'+this.value" ';
                      //filterName
                      $this->ShowInComboBox( $rowTxt['nameSpr'], $rowTxt['name'],$value, 0, '&nbsp;', 'move', 'asc',$params );
                      ?></label>
                      </div><?
                  }
              }
              ?>
           </div>
          </td>
         </tr>
         <tr>
          <td>
           <div name="load" id="load"></div>
           <div class="warning"><?=$this->info_msg?></div>
           <div id="result"></div>
           <div id="debug">
            <?
            $this->ShowContentHTML($result);
            ?>
           </div>
          </td>
         </tr>
         <?
        AdminHTML::TablePartF();
        /* Write Form Footer */
        $this->Form->WriteFooter();
    } //end of fuinction showList()

    /**
     * @param $name
     * @return string
     * @programmer Bogdan Iglinsky
     */
    function getLinkSort($name){
        $str = $this->script."&sort=".$name;
        if($this->sort==$name){
//            echo '$this->asc_desc='.$this->asc_desc;
            if($this->asc_desc=='desc') $str .='&asc_desc=asc';
            else  $str .='&asc_desc=desc';
        }
        return $str;
    }

    // ================================================================================================
    // Function : ShowContentHTML
    // Version : 1.0.0
    // Date : 21.05.2008
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Show content
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 21.05.2008
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
   function ShowContentHTML($result=NULL)
    {
        $this->checkStatus();
        if(!isset($result)) {
            $result = $this->GetContent();
        }
//        var_dump($result);
        $rows = count ($result);
        if($rows>($this->display+$this->start)) $ch = $this->start + $this->display;
        else $ch = $rows;

        ?><div class="path"><?
        if( !empty($this->srch)){ echo $this->multi['_TXT_SEARCH_RESULT'];?>:<?}
        elseif($this->uselevels==1){$this->ShowPathToLevel($this->spr, $this->level, $this->script, NULL, NULL);}
        $arrShowUniqueFields['usetranslit']['label'] = 1;
        $arrShowUniqueFields['usetranslit']['value'] = 1;
        $arrShowUniqueFields['usemeta']['label'] = 1;
        $arrShowUniqueFields['usemeta']['value'] = 1;
        ?></div>
        <table border="0" cellpadding="0" cellspacing="1" width="100%" class="table table-striped table-hover table-bordered">
         <tr>
          <Th class="THead">
              <input value="0" id="cAll" onclick="if (this.value == '1') {unCheckAll(<?=$ch;?>); this.value = '0';} else {checkAll(<?=$ch;?>); this.value = '1';}" type="checkbox"/>
              <br/><? $this->Form->LinkTitle($this->getLinkSort('cod'), $this->multi['_FLD_CODE']);?>
          </Th>
             <?
             foreach($this->arraySettings as $key=>$rowTxt) {
                 if($rowTxt['type']=='sort') continue;
                 if ($rowTxt['showInList']) {
                     if (isset($arrShowUniqueFields[$key]['label']) && $arrShowUniqueFields[$key]['label'] == 0) continue;
                     if ($rowTxt['type'] == 'footnote') {
                         if (isset($arrShowUniqueFields[$rowTxt['useFields']]['label']))
                             $arrShowUniqueFields[$rowTxt['useFields']]['label'] = 0;
                     }
                     ?>
                     <Th class="THead"><?
                         switch ($rowTxt['type']) {
                             case 'values':
                                 echo $rowTxt['label'];
                                 break;
                             case 'spr':
                                 if($rowTxt['typeLink']=='many' || $rowTxt['typeLink']=='onemany'){
                                     echo $rowTxt['label'];
                                     break;
                                 }
                             default:
                                 $this->Form->LinkTitle($this->getLinkSort($rowTxt['name']), $rowTxt['label']);
                                 break;
                         }
                         ?></Th> <?
                 }
             }
          if( $this->usetranslit==1 && $arrShowUniqueFields['usetranslit']['label']==1){
              $arrShowUniqueFields['usetranslit']['label'] = 0;
              ?>
          <Th class="THead"><? $this->Form->LinkTitle($this->getLinkSort('translit'), $this->multi['FLD_PAGE_URL']);?></Th>
          <?}
          if ($this->field_type!='string' AND $this->uselevels==1) {?>
          <Th class="THead"><?=$this->multi['FLD_SUBLEVEL'];?></Th>
          <?
          }
          if( $this->usemeta==1 && $arrShowUniqueFields['usemeta']['label']==1 ){?>
          <Th class="THead"><?=$this->multi['_TXT_META_DATA'];?></Th>
          <?}
             if(!empty($this->arraySettingsForLangAndMove['manyLang'])){?>
          <Th class="THead"><? $this->Form->LinkTitle($this->getLinkSort('lang_id'), $this->multi['_FLD_LANGUAGE']);?></Th>
          <?
             }
          if ($this->field_type!='string') {?>
          <Th class="THead"><?=$this->multi['FLD_DISPLAY']?></Th>
          <?}?>
         </tr>
         <?
         $a=$rows;
         $up = 0;
//         echo '<br>$rows='.$rows.' $this->field_type='.$this->field_type;
//         echo '<br>$rows='.$rows.' $this->start='.$this->start.' $ch='.$ch;
         if(!empty($result))
         for( $i = $this->start; $i < $ch; ++$i )
         {
             $row = $result[$i];
             //echo '<br>$i='.$i.' $this->start='.$this->start.' $this->display='.$this->display.' $this->start+$this->display='.($this->start+$this->display);
             //if( $i >=$this->start && $i < ( $this->start+$this->display ) )
             //{
                if( (float)$i/2 == round( $i/2 ) ) $class='TR1';
                else $class='TR2';
                ?>
                <tr class="<?//=$class;?>">
                 <td align="center">
                     <?=$this->Form->CheckBox( "id_del[]", $row['cod'], null, "check".$i );?>
                     <br/><?=$this->Form->Link( "$this->script&task=edit&id=".$row['id'], stripslashes($row['cod']), $this->multi['TXT_EDIT'] );?>
                 <?
             foreach($this->arraySettings as $key=>$rowTxt):
                 if($rowTxt['type']=='sort') continue;
                 if($rowTxt['showInList']){
                     if(isset($arrShowUniqueFields[$key]['value']) && $arrShowUniqueFields[$key]['value']==0) continue;
                     ?><td><?
                     switch($rowTxt['type']){
                         case 'text':
                         case 'textarea':
                         case 'date':
                             echo stripslashes($row[$rowTxt['name']]);
                             break;
                         case 'datestatus':
                             $this->showStatus($row[$rowTxt['name'].'_status']);
                             ?><br/><b><?=$this->multi['TXT_START_DATE']?>:</b> <?=$row[$rowTxt['name']];
                             ?><br/><b><?=$this->multi['TXT_END_DATE']?>:</b> <?=$row[$rowTxt['name'].'_end'];
                             break;
                         case 'set':
                            ?>
                             <div id="<?=$rowTxt['name'].$row['cod'];?>">
                             <?
                             $this->ShowVisibility($row['cod'], $row[$rowTxt['name']],$rowTxt['name'],$rowTxt['label']);
                             ?></div>
                             <?
                             break;
                         case 'htmlarea':
                             if( !empty($row[$rowTxt['name']])) $this->Form->ButtonCheck();
                             break;
                         case 'icon':
                                 if ( !empty($row[$rowTxt['name']]) ){
                                     switch($rowTxt['lang']){
                                         case 'oneLang':
                                             $fln = null;
                                             $flnHref = null;
                                             break;
                                         case 'manyLang':
                                             $fln = $this->fln;
                                             $flnHref =  $this->fln.'/';
                                             break;
                                     }
                                     ?><a href="<?=Spr_Img_Path_Small.$this->spr.'/'.$flnHref.$row[$rowTxt['name']];?>" target="_blank" alt="<?=$this->multi['TXT_ZOOM_IMG'];?>" title="<?=$this->multi['TXT_ZOOM_IMG'];?>"><?
                                     echo $this->ShowImage($this->spr, $fln, $row[$rowTxt['name']], 'size_width=75', 100, NULL, "border=0");
                                     ?></a><br /><?
                                     echo $row[$rowTxt['name']];
                                 }
                             break;
                         case 'spr':
                             if($rowTxt['typeLink']=='one'){
                                 if(!isset($arrNameSpr[$rowTxt['name']]))
                                     $arrNameSpr[$rowTxt['name']] = $this->GetArrNameBySpr($rowTxt['nameSpr']);
                                 if(isset($arrNameSpr[$rowTxt['name']][$row[$rowTxt['name']]])){
                                     echo $arrNameSpr[$rowTxt['name']][$row[$rowTxt['name']]];
                                 }
                             }
                             if($rowTxt['typeLink']=='many'){
                                 if(!isset($arrNameSpr[$rowTxt['name']]))
                                     $arrNameSpr[$rowTxt['name']] = $this->GetArrNameBySprMany($rowTxt['nameSpr'],
                                         $this->spr.'_'.$rowTxt['name'],$rowTxt['name']);
//                                 var_dump($arrNameSpr[$rowTxt['name']]);
                                 if(isset($arrNameSpr[$rowTxt['name']][$row['cod']])){
                                     echo $arrNameSpr[$rowTxt['name']][$row['cod']];
                                 }
                             }
                             break;
                         case 'values':
//                             echo '<br>name='.$rowTxt['name'];
                             $strName = $this->multi['TXT_ADD_EDIT'];
                            if(isset($rowTxt['visible'])&&!empty($rowTxt['visible']))
                             switch($rowTxt['visible']){
                                 case 'rating':
                                     if(!isset($arrRating[$rowTxt['name']]) || empty($arrRating[$rowTxt['name']])){
                                         $arrRating[$rowTxt['name']] =
                                             $this->getArrayRating($rowTxt['tableValues'],$rowTxt['parentName']);
                                     }
                                     if(isset($arrRating[$rowTxt['name']][$row['cod']]['count'])){
                                         $rowRating = $arrRating[$rowTxt['name']][$row['cod']];
                                         $strName = $this->multi['FLD_AVERAGE_RATING'].': <b style="font-size: 13px;">'.$rowRating['average'].'</b>
                                            ['.$rowRating['count'].']<br>'.$strName;
                                     }
                                     break;
                                 case 'values':
                                     if(!isset($arrValues[$rowTxt['name']]) || empty($arrValues[$rowTxt['name']])){
                                         $arrValues[$rowTxt['name']] = $this->getArrayValues($rowTxt['tableValues'],$rowTxt['parentName']);
//                                         var_dump($arrValues);
                                     }
                                     if(isset($arrValues[$rowTxt['name']][$row['cod']])){
                                         $strName = $arrValues[$rowTxt['name']][$row['cod']].'<br>'.$strName;
                                     }
                                     break;
                             }
                             $link = "index.php?module=".$rowTxt['moduleValues'].'&'.$rowTxt['filterName'].'='.$row['cod'].
                                 '&parent_script=index.php?module='.$this->module.'&parent_id='.$this->module;
                             $this->Form->Link( $link, $strName, $this->multi['TXT_ADD_EDIT'] );
                             break;
                         case 'footnote':
//                             echo '<br>useFields='.$rowTxt['useFields'];
//                             if(isset($arrShowUniqueFields[$rowTxt['useFields']]['value']))
                                 $arrShowUniqueFields[$rowTxt['useFields']]['value'] = 0;
                             switch($rowTxt['useFields']){
                                 case 'usetranslit':
                                     echo $row['translit'];
                                     break;
                                 case 'usemeta':
                                     if( !empty($row['mtitle'])){?><div><?=$this->Form->ButtonCheck(); echo ' ',$this->multi['FLD_PAGES_TITLE']; ?></div><?}?>
                                     <?if( !empty($row['mdescr'])){?><div><?=$this->Form->ButtonCheck(); echo ' ',$this->multi['FLD_PAGES_DESCR'];?></div><?}?>
                                     <?if( !empty($row['mkeywords'])){?><div><?=$this->Form->ButtonCheck(); echo ' ',$this->multi['FLD_KEYWORDS'];?></div><?}
                                     break;
                             }
                             break;
                     }
                     ?></td><?
                 }
             endforeach;
//             var_dump($this->arraySettingsType['footnote']);
//             var_dump($arrShowUniqueFields);
                 if( $this->usetranslit==1 && $arrShowUniqueFields['usetranslit']['value'] ){
                     ?>
                 <td align="center"><?=$row['translit'];?></td>
                 <?}
                 if( $this->field_type!='string' AND $this->uselevels==1 ){?>
                 <td>
                  <?
                  $sbl = $this->IsSubLevels($this->spr, $row['cod']);
                  if( $sbl==0 ) $txt_tmp = $this->multi['TXT_CREATE_SUBLEVEL'];
                  else $txt_tmp = $this->multi['FLD_SUBLEVEL'];
//                  echo $row['cod'].'<br />node'.$row['node'];
                  $url = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&task=show&uselevels='.$this->uselevels.'&level='.$row['cod'].'&node='.$row['node'].'&srch=&start=0';
                  ?>
                  <a href="<?=$this->script;?>&level=<?=$row['cod'];?>&node=<?=$row['node'];?>" onclick="GoToSubLevel('<?=$url;?>', 'wndw0' ); return false;"><?=$txt_tmp;?></a> <?if($sbl>0) { ?><span class="simple_text"><?=' ['.$sbl.']';?></span><?}?>
                 </td>
                 <?
                 }
                 if( $this->usemeta==1 && $arrShowUniqueFields['usemeta']['value']  ){?>
                   <td align="left" style="padding:5px; font-weight:normal;" nowrap="nowrap">
                    <?if( !empty($row['mtitle'])){?><div><?=$this->Form->ButtonCheck(); echo ' ',$this->multi['FLD_PAGES_TITLE']; ?></div><?}?>
                    <?if( !empty($row['mdescr'])){?><div><?=$this->Form->ButtonCheck(); echo ' ',$this->multi['FLD_PAGES_DESCR'];?></div><?}?>
                    <?if( !empty($row['mkeywords'])){?><div><?=$this->Form->ButtonCheck(); echo ' ',$this->multi['FLD_KEYWORDS'];?></div><?}?>
                   </td>
                 <?
                 }
         if(!empty($this->arraySettingsForLangAndMove['manyLang'])){?>
                 <td align="center" style="padding:0px 2px 0px 2px;"><?=$row['lang_name'];?></td>
                 <?
         }
                 //echo '<br> $this->field_type='.$this->field_type;
                 if ($this->field_type!='string') {

                 ?><td align="center" nowrap><?
                   $url = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&uselevels='.$this->uselevels;
//                     echo '$up='.$up;
                   if( $i!=0 && ($this->sort=='move' ||
                           (isset($result[$i-1]) && $result[$i-1][$this->sort]==$row[$this->sort] )))
                   {
                       $this->Form->ButtonUpAjax($this->script_ajax, $row['id'], $url, 'debug', 'move', $row['move']);
                   }
                   else{?><img src="images/spacer.gif" width="12"/><?}
                   //for replace
                   ?>&nbsp;<?
                     if($this->sort!='move' && ((!isset($result[$i-1]) or $result[$i-1][$this->sort]!=$row[$this->sort]) and
                         (!isset($result[$i+1]) or $row[$this->sort]!=$result[$i+1][$this->sort]))) {
                         $this->Form->TextBox('debug', $row['move'], 3,' disabled="disabled" style="background: rgb(194, 194, 194);" ');
                     }else{
                         $this->Form->TextBoxReplace($url, 'debug', 'move', $row['move'], $row['cod']);
                     }
                     ?>&nbsp;<?
                   if( $i!=($rows-1) && ($this->sort=='move' || $result[$i+1][$this->sort]==$row[$this->sort] ))
                   {
                       $this->Form->ButtonDownAjax($this->script_ajax, $row['id'], $url, 'debug', 'move', $row['move']);
                   }
                   else{?><img src="images/spacer.gif" width="12"/><?}
                   $up=$row['id'];
                   $a=$a-1;
                 ?></td><?
                 }
                 //echo '<TD>'; $this->Form->Link("$scriplink&task=add_lang&id=".$row['cod'], '&nbsp;&nbsp;'.$this->multi['_LNK_OTHER_LANGUAGE'].'&nbsp;&nbsp');
                 ?>
                </tr><?

         }//end for
         ?>
        </table>
        <script language="JavaScript">
         function GoToSubLevel(uri, div_id){
              $.ajax({
                    type: "POST",
                    dataType : "html",
                    url: uri,
                    success: function(data){
                      $("#"+div_id).empty();
                      $("#"+div_id).append(data);
                    },
                    beforeSend: function(){
                        $("#"+div_id).html('<div style="border:0px solid #000000; padding-top:5px; padding-bottom:5px; text-align:left;" align="center"><img src="/admin/images/icons/loading_animation_liferay.gif"></div>');
                    }
              });
         }
        </script>
        <?
    }//end of function ShowContentHTML()

    /**
    * Class method ShowVisibility
    * show visibility of product
    * @param
    * @version 1.0, 31.01.2011
    * @return
     * @programmer Bogdan Iglinsky
    */
    function ShowVisibility($cod, $value ,$name ,$label='')
    {
        if( $value == 0 ) {
            ?><a href="#" onclick="QuickChangeData('<?=$name.$cod;?>', 'module=<?=$this->module;?>&spr=<?=$this->spr;
            ?>&task=change_<?=$name?>&<?=$name?>=1&cod=<?=$cod;?>');return false;"><?
            echo $this->Form->Img( '/admin/images/icons/publish_x.png', $label, 'border=0' );
            ?></a><?
        }
        if( $value == 1 ) {
            ?><a href="#" onclick="QuickChangeData('<?=$name.$cod;?>', 'module=<?=$this->module;?>&spr=<?=$this->spr;
            ?>&task=change_<?=$name?>&<?=$name?>=0&cod=<?=$cod;?>');return false;"><?
            echo $this->Form->Img( '/admin/images/icons/tick.png', $label, 'border=0' );?></a><?
        }
        return;
    }//end of function ShowVisibility

    /**
    * Class method ChangeVisibleProp
    * change visibility of product
    * @param integer $id - id of the product
    * @param $new_visible - new value for field visible
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 31.01.2011
    * @return true or false
    */
    function ChangeVisibleProp($cod, $new_visible=0,$name='visible')
    {
        $q = "UPDATE `".$this->spr."` SET `".$name."`='".$new_visible."' WHERE `cod`='".$cod."';";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        //echo '<br />$q='.$q.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result) return false;
        return true;
    }//end of function ChangeVisibleProp

   // ================================================================================================
   // Function : GetRowByCODandLANGID()
   // Version : 1.0.0
   // Date : 09.02.2005
   //
   // Parms :
   //                 $id   / id of editing record / Void
   //                 $mas  / array of form values
   // Returns : true,false / Void
   // Description : edit/add records in News module
   // ================================================================================================
   // Programmer : Andriy Lykhodid
   // Date : 09.02.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
    function GetRowByCODandLANGID( $cod, $ln )
   {
    $Row = NULL;
    $q = "SELECT * FROM `".$this->spr."` WHERE `cod`='".addslashes($cod)."' AND `lang_id`='".$ln."'";
    $res = $this->Rights->Query( $q, $this->user_id, $this->module );
    //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
    if( !$res OR !$this->Rights->result ) return $Row;
    $Row = $this->Rights->db_FetchAssoc();
    return $Row;
   }

    /**
     * @programmer Bogdan Iglinsky
     *
     */
    function initEmptySprForFilter(){
        if(isset($this->arraySettingsType['spr']) && !empty($this->arraySettingsType['spr'])){
            foreach($this->arraySettingsType['spr'] as $key=>$rowTxt){
//                var_dump($rowTxt);
                switch($rowTxt['lang']){
                    case 'oneLang':
                        if(!empty($this->arrayValues[$rowTxt['filterName']]) && empty($this->arrayValues[$rowTxt['name']])){
                            $this->arrayValues[$rowTxt['name']] = $this->arrayValues[$rowTxt['filterName']];
                        }
                        break;
                    case 'manyLang':

                        break;
                }
            }
        }
    }

    /**
     * @param null $status
     * @programmer Bogdan Iglinsky
     */
    function showStatus($status = NULL){
        if(empty($status)) return false;
        $arrName['a'] = $this->multi['TXT_STATUS_ACTIVE'];
        $arrName['e'] = $this->multi['TXT_STATUS_EXPIRED'];
        $arrName['n'] = $this->multi['TXT_STATUS_NEXT'];
        $arrColor['a'] = 'green';
        $arrColor['e'] = 'red';
        $arrColor['n'] = 'blue';
        if(isset($arrName[$status]) && isset($arrColor[$status])){
            ?><span style="color: <?=$arrColor[$status]?>"><?=$arrName[$status]?></span><?
        }
    }

    /**
     * @param null $table
     * @param null $id
     * @param string $name
     * @param string $dop_field
     * @param string $dop_field_value
     * @return array
     * @programmer Bogdan Iglinsky
     */
    function getSprManyLink($table = NULL,$id = NULL,$name='',$dop_field = '',$dop_field_value = ''){
        $q=" SELECT `".$table."`.*
             FROM `".$table."`
             WHERE `".$table."`.`cod` = '".$id."' ";
        if(!empty($dop_field)) $q .= " AND `cod_".$dop_field."` = '".$dop_field_value."' ";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result ) return false;
        $result = $this->Rights->QueryResult($q, $this->user_id, $this->module);
        $arrReturn = array();
        if(!empty($result)){
            foreach($result as $row){
                $arrReturn[] = $row['cod_'.$name];
            }
        }
        return $arrReturn;
    }

   // ================================================================================================
   // Function : edit
   // Version : 1.0.0
   // Date : 09.01.2005
   //
   // Parms :         $user_id  / user ID
   //                 $module   / Module read  / Void
   //                 $id       / id of editing record / Void
   //                 $mas      / mas with value from $_REQUEST
   //                 spr       / name of the table for this module
   // Returns : true,false / Void
   // Description : Show data from $spr table for editing
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.01.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
    function edit()
   {
       $arrShowUniqueFields['usetranslit'] = 1;
       $arrShowUniqueFields['usemeta'] = 1;

    $Panel = new PanelLTE();
    $this->initEmptySprForFilter();
    ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $(".uicheckbox").button();
            });
        </script>
     <?

    if( $this->id!=NULL )
    {
     $q="SELECT * FROM `".$this->spr."` WHERE id='".$this->id."'";
     // edit (U)
     $result = $this->Rights->QueryResult($q, $this->user_id, $this->module);
     if( !is_array($result)) return false;
     $mas = $result[0];
    }
    /* Write Form Header */
    $this->Form->WriteHeaderFormImg( $this->script );

       $this->Form->Hidden('module_name', $this->module_name);
       $this->Form->Hidden('spr', $this->spr);
       $this->Form->Hidden('root_script', $this->root_script);
       $this->Form->Hidden('parent_script', $this->parent_script);
       $this->Form->Hidden('parent_id', $this->parent_id);
       $this->Form->Hidden('sort', $this->sort);
       $this->Form->Hidden('display', $this->display);
       $this->Form->Hidden('start', $this->start);
       $this->Form->Hidden('fln', $this->fln);
       $this->Form->Hidden('srch', $this->srch);

       $this->Form->Hidden('lang_id', "");
       $this->Form->Hidden('uselevels', $this->uselevels);
       $this->Form->Hidden('usecolors', $this->usecolors);
       $this->Form->Hidden('usetranslit', $this->usetranslit);
       $this->Form->Hidden('useuploadfiles', $this->useuploadfiles);
       $this->Form->Hidden('useuploadimages', $this->useuploadimages);
       $this->Form->Hidden('edit_lang', "");
       $this->Form->Hidden('item_icon', "");

       if( $this->id!=NULL ) $txt = $this->multi['TXT_EDIT'];
    else $txt = $this->multi['_TXT_ADD_DATA'];

    AdminHTML::PanelSubH( $txt );

       $this->navigationKey($this->script);
       ?><div style="
        position: fixed;bottom: 0;right: 20%;z-index: 100; background: #ffffff;border: 1px dashed #bfbfbf;padding: 10px;
        "><?$this->navigationKey($this->script);?></div><?

    //-------- Show Error text for validation fields --------------
    $this->ShowErrBackEnd();
    //-------------------------------------------------------------

    /* Write Simple Panel*/
    AdminHTML::PanelSimpleH();

       $q="SELECT `name` FROM `".$this->spr."` ORDER BY `name` desc LIMIT 1";
       $res = $this->Rights->Query($q, $this->user_id, $this->module);
       //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
       $tmp = $this->Rights->db_FetchAssoc();
       if(!empty($tmp))$name_type = mysql_field_type($this->Rights->result,0);
       //echo '<br>$name_type='.$name_type;
       if(!empty($this->arraySettingsType['htmlarea'])){
           $settings=SysSettings::GetGlobalSettings();
           $this->textarea_editor = $settings['editer']; //'tinyMCE';
           $this->Form->IncludeSpecialTextArea( $settings['editer']);
       }
       if(!empty($this->arraySettingsType['date']) || !empty($this->arraySettingsType['datestatus'])){
           $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false);
           $calendar->load_files();
       }

    $q="SELECT * FROM `".$this->spr."` ORDER BY `cod` desc LIMIT 1";
    $res = $this->Rights->Query($q, $this->user_id, $this->module);
    //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
    $tmp = $this->Rights->db_FetchAssoc();
        $this->field_type = mysql_field_type($this->Rights->result,1);

     if( isset($mas['id']) ) $this->Form->Hidden( 'id', $mas['id'] );
     else $this->Form->Hidden( 'id', '' );
     ?>
     <tr>
      <td width="100"><b><?echo $this->multi['_FLD_CODE']?>:</b></td>
      <td>
       <?
       if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->cod : $val=$mas['cod'];
       else $val=$this->cod;

       if ( $this->id ){
           $this->Form->TextBox( 'cod', $val, 50 );
           $this->Form->Hidden( 'cod_old', $val );
       }
       else{
           if(empty($new_cod)) $new_cod = 1;
//           echo '$this->field_type='.$this->field_type;
           if ($this->field_type=='int'){
               $new_cod = $tmp['cod']+1;
               $this->Form->TextBox( 'cod', $new_cod, 50, 'readonly="readonly"' );
           }
           else{
               $this->Form->TextBox( 'cod', $val, 50 );
           }
       }
       ?>
      </td>
     </tr>
     <?
       if(isset($this->arraySettingsForLangAndMove['oneLang']) && !empty($this->arraySettingsForLangAndMove['oneLang'])){
           foreach($this->arraySettingsForLangAndMove['oneLang'] as $rowTxt){
               if($rowTxt['type']!='values'){
               ?>
               <tr>
                   <td>
                       <b><?=$rowTxt['label']?>:</b>
                   </td>
                   <td>
                           <?
                           if( $this->id!=NULL ){
                               $value = '';
                               if($this->Err!=NULL) {
                                   if(isset($this->arrayValues[$rowTxt['name']]))$value=$this->arrayValues[$rowTxt['name']];
                               } else{
                                   if(isset($mas[$rowTxt['name']]))$value=$mas[$rowTxt['name']];
                               }
                           }else{
                               if ($this->Err != NULL) {
                                   $value = $this->arrayValues[$rowTxt['name']];
                               } else {
                                   if (!empty($rowTxt['defVal'])) $value = $rowTxt['defVal'];
                                   else $value = $this->arrayValues[$rowTxt['name']];
                               }
                           }
                           switch($rowTxt['type']){
                               case 'set':
                                   ($value==1)? $checked = 'checked="checked"': $checked = NULL;
                                   (empty($rowTxt['nameLabel'])) ? $label = $rowTxt['label']:$label=$rowTxt['nameLabel'];
                                   ?>
                       <div class="EditTable">
                                   <input id="<?=$rowTxt['name']?>Check" class="uicheckbox" type="checkbox" name="<?=$rowTxt['name']?>"
                                       <?=$checked?> value="1" onclick=""/>
                                   <label for="<?=$rowTxt['name']?>Check"><?=$label?></label>
                           </div><?
                                   break;
                               case 'textarea':
                                   $this->Form->TextArea( $rowTxt['name'], $value, 10, 80,' style="height: 50px;"'  );
                                   if(!empty($rowText['helpField'])) echo '<br>'.$rowText['helpField'];
                                   break;
                               case 'text':
                                   $this->Form->TextBox( $rowTxt['name'], $value, 100 );
                                   if(!empty($rowText['helpField'])) echo '<br>'.$rowText['helpField'];
                                   break;
                               case 'htmlarea':
                                   $name_id = $rowTxt['name'];
                                   $this->Form->SpecialTextArea( $this->textarea_editor, $name_id,
                                       stripslashes($value), 15, 70, 'style="width:100%;"', $this->lang_id, $name_id );
                                   if(!empty($rowText['helpField'])) echo '<br>'.$rowText['helpField'];
                                   break;
                               case 'icon':
                                   if( !empty($value) ) {
                                       ?><table border=0 cellpadding=0 cellspacing=5>
                                       <tr>
                                       <td><?
                                       $this->Form->Hidden( $rowTxt['name'], $value );
                                       ?><?
                                       echo $this->ShowImage($this->spr, NULL, $value, 'size_width=150', 85, NULL, NULL);
                                       ?><td class='EditTable'><?
                                       echo '<br>'.$this->GetImgFullPath($value, $this->spr, NULL).'<br>';
                                       ?><a href="javascript:form_sys_spr.item_icon.value='<?=$rowTxt["name"];
                                       ?>';form_sys_spr.task.value='delIcon';form_sys_spr.submit();"><?=$this->multi['_TXT_DELETE_IMG'];?></a><?
                                       ?></table><?
                                       echo '<b>'.$this->multi['_TXT_REPLACE_IMG'].':</b>';
                                   }
                                   ?>
                                   <INPUT TYPE="file" NAME="<?=$rowTxt['helpField']?>" size="40" VALUE="<?=$value?>">
                                   <?
                                   break;
                               case 'spr':
//                                           var_dump($value);
                                   switch($rowTxt['typeLink']){
                                       case 'one':
                                           $this->ShowInComboBox( $rowTxt['nameSpr'], $rowTxt['name'],$value, 0 );
                                           break;
                                       case 'many':
                                           if(empty($value) && !empty($mas['cod'])){
                                               $value = $this->getSprManyLink($this->spr.'_'.$rowTxt['name'],$mas['cod'],$rowTxt['name']);
//                                            var_dump($value);
                                           }
                                           $this->ShowInCheckBox( $rowTxt['nameSpr'], $rowTxt['name'],5,$value );
                                           break;
                                       case 'onemany':
                                           $cod = '';
                                           if(empty($value) && !empty($mas['cod'])){
                                               $value = $this->getSprManyLink($this->spr.'_'.$rowTxt['name'],$mas['cod'],$rowTxt['name']);
                                               $cod = $mas['cod'];
                                           }
                                           $this->ShowInCheckBoxNew( $rowTxt['nameSpr'],$rowTxt['nameSprMany'],
                                               $rowTxt['name'], $rowTxt['nameMany'],$rowTxt['id_bond'],$value ,$cod);
                                           break;
                                   }
                                   break;
                               case 'date':
                                   //if( empty($start_date_val) ) $start_date_val = strftime('%Y-%m-%d %H:%M', strtotime('now'));
                                   $a1 = array('firstDay'       => 1, // show Monday first
                                       'showsTime'      => true,
                                       'showOthers'     => true,
                                       'ifFormat'       => '%Y-%m-%d %H:%M',
                                       'timeFormat'     => '12');
                                   $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                                       'name'        => $rowTxt['name'],
                                       'value'       => $value );
                                   //echo '<br>$a1='.$a1.' $a2='.$a2.' $start_date_val='.$start_date_val;
                                   $calendar->make_input_field( $a1, $a2 );
                                   break;
                               case 'datestatus':
                                   $status = '';
                                   if(isset($mas[$rowTxt['name'].'_status'])) $status = $mas[$rowTxt['name'].'_status'];
                                   $this->showStatus($status);
                                   echo '<br>';
                                   if( $this->id!=NULL ){
                                       $value_end = '';
                                       if($this->Err!=NULL) {
                                           if(isset($this->arrayValues[$rowTxt['name'].'_end']))$value_end=$this->arrayValues[$rowTxt['name'].'_end'];
                                       } else{
                                           if(isset($mas[$rowTxt['name'].'_end']))$value_end=$mas[$rowTxt['name'].'_end'];
                                       }
                                   }else{
                                       if ($this->Err != NULL) {
                                           $value_end = $this->arrayValues[$rowTxt['name'].'_end'];
                                       } else {
                                           if (!empty($rowTxt['defVal_end'])) $value_end = $rowTxt['defVal_end'];
                                           else $value_end = $this->arrayValues[$rowTxt['name'].'_end'];
                                       }
                                   }
                                   ?><b><?=$this->multi['TXT_START_DATE']?>:</b> <?
                                   $a1 = array('firstDay'       => 1, // show Monday first
                                       'showsTime'      => true,
                                       'showOthers'     => true,
                                       'ifFormat'       => '%Y-%m-%d %H:%M',
                                       'timeFormat'     => '12');
                                   $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                                       'name'        => $rowTxt['name'],
                                       'value'       => $value );
                                   //echo '<br>$a1='.$a1.' $a2='.$a2.' $start_date_val='.$start_date_val;
                                   $calendar->make_input_field( $a1, $a2 );
                                   ?> <b><?=$this->multi['TXT_END_DATE']?>:</b> <?
                                   $a1 = array('firstDay'       => 1, // show Monday first
                                       'showsTime'      => true,
                                       'showOthers'     => true,
                                       'ifFormat'       => '%Y-%m-%d %H:%M',
                                       'timeFormat'     => '12');
                                   $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                                       'name'        => $rowTxt['name'].'_end',
                                       'value'       => $value_end );
                                   //echo '<br>$a1='.$a1.' $a2='.$a2.' $start_date_val='.$start_date_val;
                                   $calendar->make_input_field( $a1, $a2 );

                                   break;
                               case 'footnote':
                                   break;
                           }
                           ?>
                   </td>
               </tr>
           <?
           }
           }
       }
     if($this->uselevels==1){?>
     <tr>
      <td><b><?echo $this->multi['_FLD_LEVEL']?>:</b></td>
      <td>
       <?
       $arr_levels = $this->GetStructureInArray($this->spr, 0, NULL, $this->multi['_TXT_ROOT_LEVEL'], '&nbsp;', isset($this->arraySettings['useshort']), $this->usename, $this->uselevels);
//       echo '<br>mas=<pre>'; print_r($arr_levels);echo '</pre>';
       $this->Form->Select( $arr_levels, 'level_new', $this->level, NULL, NULL, $mas['cod'] );
       ?>
      </td>
     </tr>
     <?}?>
     <?
     if($this->usecolors==1){
         if( $this->id!=NULL ) $this->Err!=NULL ? $colorsBit=$this->colorsBit : $colorsBit=$mas['colorsBit'];
         else $colorsBit="ffffff";
         ?>
     <tr>
      <td><b><?echo $this->multi['_FLD_COLORS_FIELD']?>:</b></td>
      <td>
        <link rel="stylesheet" href="/sys/js/colorpicker/css/colorpicker.css" type="text/css" />
        <link rel="stylesheet" media="screen" type="text/css" href="/sys/js/colorpicker/css/layout.css" />
	<script type="text/javascript" src="/sys/js/colorpicker/js/colorpicker.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
               $('#colorSelector').ColorPicker({
                    color: '#<?=$colorsBit?>',
                    onShow: function (colpkr) {
                            $(colpkr).fadeIn(500);
                            return false;
                    },
                    onHide: function (colpkr) {
                            $(colpkr).fadeOut(500);
                            return false;
                    },
                    onChange: function (hsb, hex, rgb) {
                            $('#colorSelector div').css('backgroundColor', '#' + hex);
                             $('#colorBitField').val(hex);

                    }

            });
            });
        </script>
          <div id="colorSelector" style="float: left;"><div style="background-color: #<?=$colorsBit?>"></div></div>
          <input type="hidden" id="colorBitField" name="colorBit" value="<?=$colorsBit?>"/>
       <?
       //$this->Form->TextBox(  'level_new',$tmp['colorsBit'], 10 );
       ?>
      </td>
     </tr>
     <?}

     ?>
     <tr>
      <td colspan="2">
       <?
       if( $this->usetranslit==1) {$this->ShowJS();}

       $ln_arr = $this->ln_sys->LangArray( _LANG_ID );
       if ( empty($ln_arr) )  $ln_arr[1]='';

       $Panel->WritePanelHead( "SubPanel_", $ln_arr, $this->lang_id );

       if(!empty($this->arraySettingsForLangAndMove['manyLang']))
       while( $el = each( $ln_arr ) )
       {
             $lang_id = $el['key'];
             $lang = $el['value'];
             $mas_s[$lang_id] = $lang;

             $Panel->WriteItemHeader( $lang, $lang_id, $this->lang_id );
                if($this->id){
                    $row = $this->GetRowByCODandLANGID( $mas['cod'], $lang_id );
                }else{
                    $row='';
                }
                echo "\n <table border=0 class='EditTable'>";

           if(isset($this->arraySettingsForLangAndMove['manyLang']) && !empty($this->arraySettingsForLangAndMove['manyLang'])){
               foreach($this->arraySettingsForLangAndMove['manyLang'] as $key=>$rowTxt){
                   if($rowTxt['type']=='values') continue;
                   if(isset($arrShowUniqueFields[$key]) && $arrShowUniqueFields[$key]==0) continue;
                   echo "\n <tr>";
                   echo "\n <td>";
                   if(!empty($rowTxt['label']))echo "<b>".$rowTxt['label'].":</b>";
                   echo "\n <td>";
                   isset($this->arrayValues[$rowTxt['name']][$lang_id]) ? $oldVal = $this->arrayValues[$rowTxt['name']][$lang_id]:$oldVal = $rowTxt['defVal'];
                   if( $this->id!=NULL ) $this->Err!=NULL ? $val= $oldVal: $val = (isset($row[$rowTxt['name']]) ? $row[$rowTxt['name']] : '');
                   else $val=$oldVal;
//                   echo '$rowTxt='.$rowTxt['type'];
                   switch($rowTxt['type']){
                       case 'text':
                           $this->Form->TextBox( $rowTxt['name'].'['.$lang_id.']', stripslashes($val), 50 );
                           if(!empty($rowTxt['helpField'])) echo '<div>'.$rowTxt['helpField'].'</div>';
                           break;
                       case 'textarea':
                           $this->Form->TextArea( $rowTxt['name'].'['.$lang_id.']', stripslashes($val), 10, 80,' style="height: 50px;"' );
                           if(!empty($rowTxt['helpField'])) echo '<div>'.$rowTxt['helpField'].'</div>';
                           break;
                       case 'set':
                           ($val==1)? $checked = 'checked="checked"': $checked = NULL;
                           (empty($rowTxt['nameLabel'])) ? $label = $rowTxt['label']:$label=$rowTxt['nameLabel'];
                           ?>
                           <div class="EditTable">
                           <input id="<?=$rowTxt['name'].$lang_id?>Check" class="uicheckbox" type="checkbox" name="<?=$rowTxt['name'].'['.$lang_id.']'?>"
                               <?=$checked?> value="1" onclick=""/>
                           <label for="<?=$rowTxt['name'].$lang_id?>Check"><?=$label?></label>
                           </div><?
                           break;
                       case 'htmlarea':
                           $this->Form->SpecialTextArea( $this->textarea_editor, $rowTxt['name'].'['.$lang_id.']',
                               stripslashes($val), 30, 70, 'style="width:100%"', $lang_id, $rowTxt['name'].$lang_id );
                           if(!empty($rowTxt['helpField'])) echo '<div>'.$rowTxt['helpField'].'</div>';
                           break;
                       case 'icon':
                           if( !empty($val) ) {
                               ?><table border=0 cellpadding=0 cellspacing=5>
                               <tr>
                               <td><?
                               $this->Form->Hidden( $rowTxt['name'].'['.$lang_id.']', $val );
                               //$this->Form->Hidden( 'item_img', NULL );
                               ?><?
                               echo $this->ShowImage($this->spr, $lang_id, $val, 'size_width=150', 85, NULL, NULL);
                               ?><td class='EditTable'><?
                               echo '<br>'.$this->GetImgFullPath($val, $this->spr, $lang_id).'<br>';
                               ?><a href="javascript:form_sys_spr.edit_lang.value='<?=$lang_id;
                               ?>';form_sys_spr.item_icon.value='<?=$rowTxt['name'];
                               ?>';form_sys_spr.task.value='delIcon';form_sys_spr.submit();"><?=$this->multi['_TXT_DELETE_IMG'];?></a><?
                               ?></table><?
                               echo '<b>'.$this->multi['_TXT_REPLACE_IMG'].':</b>';
                           }
                           ?>
                           <INPUT TYPE="file" NAME="<?=$rowTxt['helpField']?>[<?=$lang_id;?>]" size="40" VALUE="<?=$val?>">
                           <?
                           break;
                       case 'spr':
                           $this->ShowInComboBox( $rowTxt['nameSpr'], $rowTxt['name'].'['.$lang_id.']',$value, 0 );
                           break;
                       case'date':
                           //if( empty($start_date_val) ) $start_date_val = strftime('%Y-%m-%d %H:%M', strtotime('now'));
                           $a1 = array('firstDay'       => 1, // show Monday first
                               'showsTime'      => true,
                               'showOthers'     => true,
                               'ifFormat'       => '%Y-%m-%d %H:%M',
                               'timeFormat'     => '12');
                           $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                               'name'        => $rowTxt['name'].'['.$lang_id.']',
                               'value'       => $value );
                           //echo '<br>$a1='.$a1.' $a2='.$a2.' $start_date_val='.$start_date_val;
                           $calendar->make_input_field( $a1, $a2 );
                           break;
                       case 'footnote':
                           if(isset($arrShowUniqueFields[$rowTxt['useFields']]) && $arrShowUniqueFields[$rowTxt['useFields']]==1)
                               $arrShowUniqueFields[$rowTxt['useFields']] = 0;
                           switch($rowTxt['useFields']){
                               case 'usetranslit':
                                   if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->translit[$lang_id] : $val = $row['translit'];
                                    else $val=$this->translit[$lang_id];
                                   $this->showTranslit($val,$lang_id);
                                   break;
                               case 'usemeta':
                                   $this->showMeta($row,$lang_id);
                                   break;
                   }
                           break;
                   }

               }
           }

                if( $this->usetranslit==1 && $arrShowUniqueFields['usetranslit']==1) {
                    echo "\n <tr>";
                    echo "\n <td valign=top><b>".$this->multi['FLD_PAGE_URL'].":</b>";
                    echo "\n <td>";
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->translit[$lang_id] : $val = $row['translit'];
                    else $val=$this->translit[$lang_id];
                    $this->showTranslit($val,$lang_id);
                }

                echo   "\n </table>";

                if( $this->usemeta==1 && $arrShowUniqueFields['usemeta']==1) {
                    if( $this->id!=NULL ) $rowShow = $row;else $rowShow = array();
                    $this->showMeta($rowShow,$lang_id);
                }

             $Panel->WriteItemFooter();
        }
        if(empty($mas['cod'])){
            if(isset($new_cod)){
                $val = $new_cod;
            }else{
                $val = '';
            }
        }else{
            $val = $mas['cod'];
        }
        if(isset($this->useuploadimages) && $this->useuploadimages==1){
            $this->UploadImages->ShowFormToUpload(NULL,$val);
        }
        if(isset($this->useuploadfiles) && $this->useuploadfiles==1){
            $this->UploadFile->ShowFormToUpload(NULL,$val);
        }
        $Panel->WritePanelFooter();
        AdminHTML::PanelSimpleF();
       $this->navigationKey($this->script);

    AdminHTML::PanelSubF();
    $this->Form->WriteFooter();

    return true;
   }  //end of fuinction edit

    /**
     * @param $script
     * @programmer Bogdan Iglinsky
     */
    function navigationKey($script){
        $this->Form->WriteSavePanel( $script );
        $this->Form->WriteSaveAndReturnPanel( $script );
        $this->Form->WriteCancelPanel( $script );
    }

    /**
     * @param $val
     * @param $lang_id
     * @programmer Bogdan Iglinsky
     */
    function showTranslit($val,$lang_id){
                    if( $this->id ){
                        $params = 'disabled';
                        $this->Form->Hidden( 'translit['.$lang_id.']', stripslashes($val) );
                    }
                    else {
                        $params="onkeyup=\"CheckTranslitField('translit".$lang_id."','tbltranslit".$lang_id."');\"";
                        //$params='';
                    }
                    $this->Form->TextBox( 'translit['.$lang_id.']', stripslashes($val), 40, 'id="translit'.$lang_id.'"; style="font-size:10px;" '.$params );
                    if( $this->id ){?>&nbsp;<?$this->Form->ButtonSimple("btn", $this->multi['TXT_EDIT'], NULL, "id='button".$lang_id."' onClick=\"EditTranslit('translit".$lang_id."','button".$lang_id."');\"");}
                    else{
                        ?><br>
                        <table class='EditTable' id="tbltranslit<?=$lang_id;?>" width="600">
                         <tr>
                          <td><img src='images/icons/info.png' alt='' title='' border='0' /></td>
                          <td class='info'><?=$this->multi['HELP_FLD_PAGE_URL'];?></td>
                         </tr>
                         <tr>
                          <td></td>
                          <td>
                           <?
                           foreach($this->arraySettingsType['textarea'] as $rowText){
                        $this->Form->Radio( 'translit_from['.$lang_id.']', $rowText['label'], $rowText['name'], 'name' );
                               ?> <br/><?
                           }
                    $this->Form->Radio( 'translit_from['.$lang_id.']', $this->multi['TXT_NO_AUTO_TRANSLIT'], 'name', 'name' );
                           ?>
                          </td>
                        </table><?
                        ?><br/><?
                    }
                }

    /**
     * @param $row
     * @param $lang_id
     * @programmer Bogdan Iglinsky
     */
    function showMeta($row,$lang_id){
                    echo "\n<fieldset title='".$this->multi['_TXT_META_DATA']."'> <legend><span style='vetical-align:middle; font-size:15px;'><img src='images/icons/meta.png' alt='".$this->multi['_TXT_META_DATA']."' title='".$this->multi['_TXT_META_DATA']."' border='0' /> ".$this->multi['_TXT_META_DATA']."</span></legend>";
                    echo "\n <table border=0 class='EditTable'>";
                    echo "\n <tr>";
                    echo "\n <td><b>".$this->multi['FLD_PAGES_TITLE'].":</b>";
                    echo "\n <br>";
                    echo '<span class="help">'.$this->multi['HELP_MSG_PAGE_TITLE'].'</span>';
                    echo "\n <br>";
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mtitle[$lang_id] : $val=$row['mtitle'];
                    else $val=$this->mtitle[$lang_id];
                    $this->Form->TextBox( 'mtitle['.$lang_id.']', stripslashes($val), 70 );
                    echo "<hr width='70%' align='left' size='1'>";

                    echo "\n <tr>";
                    echo "\n <td><b>".$this->multi['FLD_PAGES_DESCR'].":</b>";
                    echo "\n <br>";
                    echo '<span class="help">'.$this->multi['HELP_MSG_PAGE_DESCRIPTION'].'</span>';
                    echo "\n <br>";
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mdescr[$lang_id] : $val=$row['mdescr'];
                    else $val=$this->mdescr[$lang_id];
                    $this->Form->TextArea( 'mdescr['.$lang_id.']', stripslashes($val), 3, 70 );
                    echo "<hr width='70%' align='left' size='1'>";

                    echo "\n <tr>";
                    echo "\n <td><b>".$this->multi['FLD_KEYWORDS'].":</b>";
                    echo "\n <br>";
                    echo '<span class="help">'.$this->multi['_HELP_MSG_PAGE_KEYWORDS'].'</span>';
                    echo "\n <br>";
                    if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->mkeywords[$lang_id] : $val=$row['mkeywords'];
                    else $val=$this->mkeywords[$lang_id];
                    $this->Form->TextArea( 'mkeywords['.$lang_id.']', stripslashes($val),3, 70 );
                    echo "\n </table>";
                    echo "</fieldset><br>";
                }

   // ================================================================================================
   // Function : ShowJS()
   // Version : 1.0.0
   // Date : 08.08.2007
   // Parms :
   // Returns : true,false / Void
   // Description : show form with rating from users about goods
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 08.08.2007
   // Reason for change : Reason Description / Creation
   // Change Request Nbr:
   // ================================================================================================
  function ShowJS()
   {
       ?>
        <script type="text/javascript">
        function EditTranslit(div_id, idbtn){
            Did = "#"+div_id;
            idbtn = "#"+idbtn;
            if( !window.confirm('<?=$this->multi['MSG_DO_YOU_WANT_TO_EDIT_TRANSLIT'];?>')) return false;
            else{
              $(Did).removeAttr("disabled")
                     .focus();
              $(idbtn).css("display", "none");
            }
        } // end of function EditTranslit
        function CheckTranslitField(div_id, idtbl){
            Did = "#"+div_id;
            idtbl = "#"+idtbl;
            //alert('val='+(Did).val());
            if( $(Did).val()!='') $(idtbl).css("display", "none");
            else $(idtbl).css("display", "block");
        } // end of function EditTranslit

        function QuickChangeData(div_id, mydata){
            did = "#"+div_id;
            $.ajax({
                    type: "POST",
                    data: mydata,
                    url: "/admin/modules/sys_spr/sys_spr.php",
                    success: function(msg){
                        //alert(msg);
                        $(did).html( msg );
                    },
                    beforeSend : function(){
                        //$("#sss").html("");
                        $(did).html('<div style="text-align:center;"><img src="/admin/images/ajax-loader.gif" alt="" title="" /></div>');
                    }
            });
        } // end of function QuickChangeData
        </script>
        <?
   }//end of function ShowJS()

    // ================================================================================================
    // Function : CheckFields
    // Version : 1.0.0
    // Date : 18.04.2010
    //
    // Parms :
    // Returns : true,false / Void
    // Description : check fields
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 18.04.2010
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function CheckFields()
    {
        $this->Err = NULL;

        if (empty($this->cod)) $this->Err .= $this->multi['MSG_ERR_SPR_EMPTY_CODE'] . '<br />';

        $q = "SELECT * FROM `" . $this->spr . "` WHERE `cod`='" . $this->cod . "'";
        $res = $this->Rights->Query($q, $this->user_id, $this->module);
        //echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if (!$res OR !$this->Rights->result) return false;
        $rows_by_cod = $this->Rights->db_GetNumRows();
        //echo '<br />$rows_by_cod='.$rows_by_cod.' $this->id='.$this->id;
        //if already exist record with same cod when we want create new one, then show error
        if ($rows_by_cod > 0 AND empty($this->id)) $this->Err .= $this->multi['MSG_ERR_SPR_CODE_ALREADY_EXIST'] . '<br />';

        return $this->Err;
    }//end of function CheckFields()

    // ================================================================================================
    // Function : save
    // Version : 1.0.0
    // Date : 09.01.2005
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Store data to the table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 09.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
   function save()
    {
        $q = "SELECT * FROM `" . $this->spr . "` WHERE `cod`='" . $this->cod . "'";
        $res = $this->Rights->Query($q, $this->user_id, $this->module);
//        echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if (!$res OR !$this->Rights->result) return false;
        $rows_by_cod = $this->Rights->db_GetNumRows();
        $num_fields = mysql_num_fields($this->Rights->result);

        $ln_arr = $this->ln_sys->LangArray(_LANG_ID);
        if (empty($ln_arr)) $ln_arr[1] = '';
        if ($this->uselevels == 1) {
            $node = $this->GetNodeForPosition($this->spr, $this->level_new);
        }
//        var_dump($_REQUEST);
//        var_dump($this->arrayValues);
        while ($el = each($ln_arr)) {
            //echo '<br> $el[key]='.$el['key'].' $name[ '.$el['key'].' ]='.$name[ $el['key'] ];
            $lang_id = $el['key'];

            $mtitle = $this->Form->GetRequestTxtData($this->mtitle[$lang_id]);
            $mdescr = $this->Form->GetRequestTxtData($this->mdescr[$lang_id]);
            $mkeywords = $this->Form->GetRequestTxtData($this->mkeywords[$lang_id]);
            $translit = $this->Form->GetRequestTxtData($this->translit[$lang_id]);
            if (empty($translit)) {
                if (!$this->id) {
                    if ( !empty($this->translit_from))
                        if (isset($this->arrayValues[$this->translit_from[$lang_id]])) {
                            $translit = $this->Crypt->GetTranslitStr(strip_tags($this->arrayValues[$this->translit_from[$lang_id]][$lang_id]));
                        }
                } else {
                    if (isset($this->arraySettingsType['textarea']) && !empty($this->arraySettingsType['textarea']))
                        foreach ($this->arraySettingsType['textarea'] as $rowText) {
                            if (empty($translit)) $translit = $this->Crypt->GetTranslitStr(strip_tags($this->arrayValues[$rowText['name']][$lang_id]));
//                            else {echo '$translit='.$translit;die();}
                        }
                }
            }
            if(!empty($translit)){
                $translit = str_replace('/','',$translit);
            }

            $q = "SELECT * FROM `" . $this->spr . "` WHERE `cod`='" . $this->cod_old . "' AND `lang_id`='" . $lang_id . "'";
            $res = $this->Rights->Query($q, $this->user_id, $this->module);
            //echo '<br> $q='.$q.' $res='.$res.' $$this->Rights->result='.$this->Rights->result;
            if (!$res OR !$this->Rights->result) return false;
            $rows = $this->Rights->db_GetNumRows();
            $row = $this->Rights->db_FetchAssoc();
            $translit = $this->getUniqueTranslit($translit,$row);
//            echo '$translit='.$translit;die();
            if ($rows > 0) //--- update
            {
                $q = "UPDATE `" . $this->spr . "` ";
            } else //--- insert
            {
                $q = "INSERT INTO `" . $this->spr . "` ";
            }

            $q .= " SET
              `cod`='" . $this->cod . "',
              `lang_id`='" . $lang_id . "' ";
            if ($this->uselevels == 1) $q = $q . ", `level`='" . $this->level_new . "', `node`='" . $node . "'";
            if ($this->usemeta == 1) $q = $q . ", `mtitle`='" . $mtitle . "', `mdescr`='" . $mdescr . "', `mkeywords`='" . $mkeywords . "'";
            if ($this->usetranslit == 1) $q = $q . ", `translit`='" . $translit . "'";
            if ($this->usecolors == 1) $q = $q . ", `colorsBit`='" . $this->colorBit . "'";
            if (!empty($this->id_cat)) $q = $q . ", `id_cat`='" . $this->id_cat . "'";
            if (!empty($this->id_param)) $q = $q . ", `id_param`='" . $this->id_param . "'";

            if (isset($this->arraySettingsForLangAndMove['oneLang']) && !empty($this->arraySettingsForLangAndMove['oneLang'])) {
                foreach ($this->arraySettingsForLangAndMove['oneLang'] as $rowTxt) {
                    if($rowTxt['type']=='values' || $rowTxt['type']=='footnote') continue;
//                    var_dump($rowTxt);
                    if($rowTxt['type']=='spr' && ($rowTxt['typeLink']=='many'
                        || $rowTxt['typeLink']=='onemany')){
                        $q1 = "DELETE FROM `".$this->spr."_".$rowTxt['name']."` WHERE `cod`='".$this->cod."'";
                        $this->Rights->Query( $q1, $this->user_id, $this->module );
                        if(isset($this->arrayValues[$rowTxt['name']]) && !empty($this->arrayValues[$rowTxt['name']])){
                            foreach($this->arrayValues[$rowTxt['name']] as $rowSpr){
                                $q1 = "INSERT INTO `".$this->spr."_".$rowTxt['name']."` SET
                                `cod`='" . $this->cod . "',
                                `cod_". $rowTxt['name'] ."`='" . $rowSpr . "'";
                                if($rowTxt['typeLink']=='onemany'){
                                    if(isset($this->arrayValues[$rowTxt['nameMany']][$rowSpr]) && !empty($this->arrayValues[$rowTxt['nameMany']][$rowSpr]))
                                    foreach($this->arrayValues[$rowTxt['nameMany']][$rowSpr] as $rowSprMany){
                                        $q2 =  $q1. ", `cod_". $rowTxt['nameMany'] ."`='" . $rowSprMany . "'";
                                        $res1 = $this->Rights->Query( $q2, $this->user_id, $this->module );
//                                        echo '<br> $q2='.$q2.' $res1='.$res1.' $this->Rights->result='.$this->Rights->result;
                                    }
                                }
                                $res1 = $this->Rights->Query( $q1, $this->user_id, $this->module );
//                                echo '<br> $q1='.$q1.' $res1='.$res1.' $this->Rights->result='.$this->Rights->result;
                            }
                        }
                    }else{
                        switch ($rowTxt['type']) {
                            case 'icon':
                                isset($this->arrayValues[$rowTxt['name']]) ? $val = $this->arrayValues[$rowTxt['name']] : $val = NULL;
                                if (!empty($val) AND isset($row) AND !empty($row[$rowTxt['name']]) AND $row[$rowTxt['name']] != $val) {
                                    $this->DelItemImage($rowTxt['name'], $this->cod);
                                }
                                break;
                            case 'datestatus':
                                isset($this->arrayValues[$rowTxt['name'].'_end'])
                                    ? $val_end = $this->arrayValues[$rowTxt['name'].'_end'] : $val_end = NULL;
                                $q .= ", `" . $rowTxt['name'] . "_end`='" . $val_end . "'";
                            default:
                                isset($this->arrayValues[$rowTxt['name']])
                                    ? $val = $this->arrayValues[$rowTxt['name']] : $val = NULL;
                                break;
                        }
                    $q .= ", `" . $rowTxt['name'] . "`='" . $val . "'";
                    }
                }
            }
            if (isset($this->arraySettingsForLangAndMove['manyLang']) && !empty($this->arraySettingsForLangAndMove['manyLang'])) {
                foreach ($this->arraySettingsForLangAndMove['manyLang'] as $rowTxt) {
                    if($rowTxt['type']=='values' || $rowTxt['type']=='footnote') continue;
                    if($rowTxt['type']=='spr' && $rowTxt['typeLink']=='many'){

                    }else{
                        switch ($rowTxt['type']) {
                            case 'icon':
                                isset($this->arrayValues[$rowTxt['name']][$lang_id]) ? $val = $this->arrayValues[$rowTxt['name']][$lang_id] : $val = NULL;
    //                        echo '<br>$val='.$val.' $lang_id='.$lang_id;
                                if (!empty($val) AND isset($row) AND !empty($row[$rowTxt['name']]) AND $row[$rowTxt['name']] != $val) {
                                    $this->DelItemImage($rowTxt['name'], $this->cod, $lang_id);
                                }
                                break;
                            case 'datestatus':
                                isset($this->arrayValues[$rowTxt['name'].'_end'][$lang_id])
                                    ? $val_end = $this->arrayValues[$rowTxt['name'].'_end'][$lang_id] : $val_end = NULL;
                                $q .= ", `" . $rowTxt['name'] . "_end`='" . $val_end . "'";
                            default:
                                if (isset($this->arrayValues[$rowTxt['name']][$lang_id]) && !empty($this->arrayValues[$rowTxt['name']][$lang_id]))
                                    $val = $this->Form->GetRequestTxtData($this->arrayValues[$rowTxt['name']][$lang_id]);
                                else $val = '';
                                break;
                        }
                        $q .= ", `" . $rowTxt['name'] . "`='" . $val . "'";
                    }
                }
            }

            if ($rows > 0) {
                $q .= " WHERE `cod`='" . $this->cod_old . "' AND `lang_id`='" . $lang_id . "'";
            }

            $res = $this->Rights->Query($q, $this->user_id, $this->module);
//            echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
            if (!$res OR !$this->Rights->result) return false;
            if (empty($this->id)) $this->id = $this->Rights->db_GetInsertID();
        } //--- end while

//        echo '<br> $rows_by_cod='.$rows_by_cod.' $num_fields='.$num_fields;
        if ($rows_by_cod == 0 AND $num_fields > 4) {
            $q = "SELECT MAX(`move`) as maxx FROM `" . $this->spr . "` WHERE `lang_id`='" . _LANG_ID . "'";
            $res = $this->Rights->Query($q, $this->user_id, $this->module);
//            $rows = $this->Rights->db_GetNumRows();
            $my = $this->Rights->db_FetchAssoc();
            $maxx = $my['maxx'] + 1; //add link with position auto_incremental
            $q = "UPDATE `" . $this->spr . "` SET `move`='" . $maxx . "' WHERE `cod`='" . $this->cod . "'";
            $res = $this->Rights->Query($q, $this->user_id, $this->module);
//            echo '<br> $q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
            if (!$res OR !$this->Rights->result) return false;
        }
        if (isset($this->useuploadfiles) && $this->useuploadfiles == 1) {
            ob_start();
            $this->UploadFile->SaveFiles($this->cod);
            $res = ob_get_clean();
            $this->Err .= $res;
        }
        if (isset($this->useuploadimages) && $this->useuploadimages == 1) {
            ob_start();
            $this->UploadImages->SaveImages($this->cod);
            $res = ob_get_clean();
            $this->Err .= $res;
        }
        $this->checkStatus($this->cod);
//        echo '<br>return true';
        return true;
    } //end of fuinction save

    /**
     * @param $link
     * @param $row
     * @param int $cnt
     * @return mixed
     * @programmer Bogdan Iglinsky
     */
    function getUniqueTranslit($link,$row,$cnt=0){
        if(empty($link)){
            return $link;
        }
        if($cnt>6){
            return $link;
        }
        $q = "SELECT * FROM `" . $this->spr . "`
        WHERE `translit`='" . $link . "'
        and `cod`!= '".$row['cod']."' ";
        $res = $this->Rights->Query($q, $this->user_id, $this->module);
        //echo '<br> $q='.$q.' $res='.$res.' $$this->Rights->result='.$this->Rights->result;
        if (!$res OR !$this->Rights->result) return false;
        $rows = $this->Rights->db_GetNumRows();
        //echo '<br>$rows='.$rows.' $cnt='.$cnt;
        if($rows>0){
            if($cnt<1 AND !empty($row['cod'])){
                $nextLink = $link.'-'.$row['cod'];
            }elseif($cnt<5 AND !empty($row['id'])){
                $nextLink = $link.'-'.$row['id'];
            }else{
                $nextLink = $link.'-'.time();
            }
            $link = $this->getUniqueTranslit($nextLink,$row,$cnt+1);
        }
        return $link;
    }

    /**
     * @param string $filename
     * @param string $exp
     * @return string
     * @programmer Bogdan Iglinsky
     */
    function expNameFile($filename = '', &$exp = '')
    {
        $name_no_ext = '';
        if (!empty($filename)) {
            $arr_filename = explode('.', $filename);
            $strlen_arr_filename = count($arr_filename);
            $exp = $arr_filename[$strlen_arr_filename - 1];
            unset($arr_filename[$strlen_arr_filename - 1]);
            $name_no_ext = implode('.', $arr_filename);
        }
        return $name_no_ext;
    }

    /**
     * @param null $cod
     * @programmer Bogdan Iglinsky
     */
    function checkStatus($cod = NULL){
        if(isset($this->arraySettingsType['datestatus']) && !empty($this->arraySettingsType['datestatus'])){
            foreach($this->arraySettingsType['datestatus'] as $rowTxt){
                $this->updateStatus($this->spr,$rowTxt['name'],$cod);
            }
        }
    }

     // ================================================================================================
    // Function : SavePicture
    // Version : 1.0.0
    // Date : 03.04.2006
    //
    // Parms :
    // Returns : $res / Void
    // Description : Save the file (image) to the folder  and save path in the database (table user_images)
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 03.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function SavePicture()
    {
        $this->Err = NULL;
        if (!isset($this->arraySettingsType['icon']) || empty($this->arraySettingsType['icon'])) return false;
        $arrValues = array();
        $arrCopy = array();
        $arrCopyFile = array();
//        var_dump($_FILES);
//        var_dump($this->arrayValues);
        foreach ($this->arraySettingsType['icon'] as $rowTxt):
//            var_dump($rowTxt);
            switch($rowTxt['lang']){
                case 'manyLang':
                    $ln_arr = $this->ln_sys->LangArray(_LANG_ID);
                    if (empty($ln_arr)) $ln_arr[1] = '';
                    while ($el = each($ln_arr)) {
                        $lang_id = $el['key'];
//                        echo '<br><br>$lang_id='.$lang_id.' helpField='.$rowTxt['helpField'];
//                        echo "<br>is_uploaded_file=".is_uploaded_file($_FILES[$rowTxt['helpField']]["tmp_name"][$lang_id]);
//                        echo '<br>size='.$_FILES[$rowTxt['helpField']]["size"][$lang_id];
                            if (isset($_FILES[$rowTxt['helpField']]) && isset($_FILES[$rowTxt['helpField']]["name"][$lang_id])
                                && $_FILES[$rowTxt['helpField']]["size"][$lang_id]) {
                                $filename = $_FILES[$rowTxt['helpField']]['tmp_name'][$lang_id];
                                $fileRealName = $_FILES[$rowTxt['helpField']]['name'][$lang_id];
                                $exp = '';
                                $name_no_ext = $this->expNameFile($fileRealName,$exp);
//                                echo '$name_no_ext='.$name_no_ext;
                                $this->checkImg($filename,$fileRealName,$exp);
                                if(empty($this->Err)){
                                    if (!empty($name_no_ext)) {
                                        $name_no_ext = $this->Crypt->GetTranslitStr(strip_tags($name_no_ext));
                                    }
//                                    var_dump($this->arrayValues[$rowTxt['name']][$lang_id]);
                                    if(!empty($name_no_ext))
                                        $this->arrayValues[$rowTxt['name']][$lang_id] = $name_no_ext . '_' .$rowTxt['name']. time() . '.' . $exp;
                                    $arrValues[] = $filename;
                                    $rowCopy = array();
                                    $rowCopy[] = Spr_Img_Path;
                                    $rowCopy[] = Spr_Img_Path . $this->spr;
                                    $rowCopy[] = Spr_Img_Path . $this->spr.'/'.$lang_id;
                                    $arrCopyFile[] = $this->arrayValues[$rowTxt['name']][$lang_id];
                                    $arrCopy[] = $rowCopy;
                                }
                            }
                    } // end while
                break;
                case 'oneLang':
                    if (isset($_FILES[$rowTxt['helpField']]) && isset($_FILES[$rowTxt['helpField']]["name"])
                        && $_FILES[$rowTxt['helpField']]["size"] && is_uploaded_file($_FILES[$rowTxt['helpField']]["tmp_name"])) {
                        $filename = $_FILES[$rowTxt['helpField']]['tmp_name'];
                        $fileRealName = $_FILES[$rowTxt['helpField']]['name'];
                        $exp = '';
                        $name_no_ext = $this->expNameFile($fileRealName,$exp);
                        $this->checkImg($filename,$fileRealName,$exp);
                        if(empty($this->Err)){
                            if (!empty($name_no_ext)) {
                                $name_no_ext = $this->Crypt->GetTranslitStr(strip_tags($name_no_ext));
                            }
                            if(!empty($name_no_ext))
                                $this->arrayValues[$rowTxt['name']] = $name_no_ext . '_' . time() . '.' . $exp;
                            $arrValues[] = $filename;
                            $rowCopy = array();
                            $rowCopy[] = Spr_Img_Path;
                            $rowCopy[] = Spr_Img_Path . $this->spr;
                            $arrCopyFile[] = $this->arrayValues[$rowTxt['name']];
                            $arrCopy[] = $rowCopy;
                        }

                    }
                    break;
            }
        endforeach;
//        var_dump($arrValues);
//        var_dump($arrCopyFile);
//        var_dump($arrCopy);
        if(empty($this->Err) && !empty($arrValues)){
            $count = count($arrValues);
            for($i=0;$i<$count;$i++){
                $filename = $arrValues[$i];
                $filenameCopy = $arrCopyFile[$i];
                $copyDir = '';
                foreach($arrCopy[$i] as $dir):
                    $copyDir = $dir;
                    if (!file_exists($dir)) mkdir($dir, 0777);
                    else @chmod($dir, 0777);
                endforeach;
//                echo '$filename='.$filename.' $copyDir='.$copyDir.'$filenameCopy='.$filenameCopy;
                if (!copy($filename, $copyDir.'/'.$filenameCopy)) {
                    $this->Err .= $this->multi['MSG_ERR_FILE_MOVE'] . ' (' . $filenameCopy . ')<br>';
                }
                foreach($arrCopy[$i] as $dir):
                    @chmod($dir, 0755);
                endforeach;
            }
        }
        return $this->Err;
    } // end of function SavePicture()

    /**
     * @param $filename
     * @param $fileRealName
     * @param $exp
     * @programmer Bogdan Iglinsky
     */
    function checkImg($filename,$fileRealName,$exp){
        $max_image_width = SPR_MAX_IMAGE_WIDTH;
        $max_image_height = SPR_MAX_IMAGE_HEIGHT;
        $max_image_size = SPR_MAX_IMAGE_SIZE;
        $valid_types = array("gif", "jpg", "png","jpeg");

        $filesize = filesize($filename);
        if ($filesize > $max_image_size) {
            $this->Err .= $this->multi['MSG_ERR_FILE_PROPERTIES'] . ' '.$max_image_size .
                ' (' . $fileRealName . ')<br>';
        }
        if ( !empty($exp) && !in_array(mb_strtolower($exp),$valid_types)) {
            $this->Err .= $this->multi['MSG_ERR_FILE_TYPE'] . ' (' . $fileRealName . ')<br>';
        }else{
            $getimagesize = getimagesize($filename);
            if ($getimagesize[0]>$max_image_width || $getimagesize[1]>$max_image_height) {
                $this->Err = $this->Err.$this->multi['MSG_ERR_FILE_PROPERTIES'].
                    ' ['.$max_image_width.'x'.$max_image_height.'] ('.$fileRealName.')<br>';
            }
        }
    }

   // ================================================================================================
   // Function : del
   // Version : 1.0.0
   // Date : 09.01.2005
   //
   // Parms :         $user_id  / user ID
   //                 $module   / Module read  / Void
   //                 $id_del   / array of the records which must be deleted / Void
   // Returns : true,false / Void
   // Description :  Remove data from the table
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.01.2005
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
    function del($id_del)
   {
       $this->Form->Hidden( 'sort', $this->sort );
       $this->Form->Hidden( 'fln', $this->fln );
       $this->Form->Hidden( 'display', $this->display );
       $this->Form->Hidden( 'start', $this->start );
       $kol=count( $id_del );
       //echo '<br>$kol='.$kol;
       $del=0;
       for( $i=0; $i<$kol; $i++ )
       {
        $u=$id_del[$i];
        /*
        $q = "SELECT * FROM `".$this->spr."` WHERE `cod`='".$u."' GROUP BY `cod`";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result ) return false;
        $row = $this->Rights->db_FetchAssoc();
        */
        if ($this->uselevels == 1)
        {
        //--- select sublevels of curent category ---
        $q="SELECT * FROM `".$this->spr."` WHERE `level`='".$u."' GROUP BY `cod`";
        $res_tmp = $this->Rights->Query( $q, $this->user_id, $this->module );
        //if ($res_tmp)
        $rows_tmp = $this->Rights->db_GetNumRows();
        //else $rows_tmp = 0;
        //echo '<br>$q='.$q.' $res_tmp='.$res_tmp.' $this->Rights->result='.$this->Rights->result.' $rows_tmp='.$rows_tmp;
        $id_del_l=NULL;
        for( $i_ = 0; $i_ < $rows_tmp; $i_++ )
        {
          $row_tmp = $this->Rights->db_FetchAssoc();
          $id_del_l[$i_] = $row_tmp['cod'];
        }
        //echo '<br>$id_del_l=';print_r($id_del_l);
        //--- delete sublevels ---
        if( $rows_tmp>0 )$this->del( $id_del_l );
        }
        //delete image
        if ( !$this->DelImageByCod($u) ) return false;
        //delete current level
        $q = "DELETE FROM `".$this->spr."` WHERE `cod`='".addslashes($u)."'";
        if( !empty($this->id_cat) ) $q = $q." AND `id_cat`='".$this->id_cat."'";
        if( !empty($this->id_param) ) $q = $q." AND `id_param`='".$this->id_param."'";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if( !$res OR !$this->Rights->result ) return false;

        if(isset($this->useuploadfiles) && $this->useuploadfiles==1){
            $this->UploadFile->DeleteAllFilesForPosition($u);
        }
        if(isset($this->useuploadimages) && $this->useuploadimages==1){
            $this->UploadImages->DeleteAllImagesForPosition($u);
        }

        $del=$del+1;
       }
       return $del;
   } //end of fuinction del

    // ================================================================================================
    // Function : up()
    // Version : 1.0.0
    // Date : 11.02.2005
    // Parms :
    // Returns :      true,false / Void
    // Description :  Up position
    // ================================================================================================
    // Programmer :  Andriy Lykhodid
    // Date : 11.02.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
   function up($table, $level = 0)
    {
     $q = "SELECT * FROM `".$table."` WHERE `move`='".$this->move."'";
     if($this->uselevels==1) $q .= " AND `level`='".$level."'";
        if(isset($this->arraySettingsType['spr']) && !empty($this->arraySettingsType['spr'])){
            foreach($this->arraySettingsType['spr'] as $key=>$rowTxt){
                if($rowTxt['typeLink']=='one')
                    if(!empty($this->arrayValues[$rowTxt['filterName']]))
                        $q .= " AND `".$rowTxt['name']."`='".$this->arrayValues[$rowTxt['filterName']]."'";
            }
        }
     $q = $q." GROUP BY `cod`";
//        echo ' $this->user_id='.$this->user_id.' $this->module='.$this->module;die();
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;//die();
     if( !$res )return false;
     $rows = $this->Rights->db_GetNumRows();
     $row = $this->Rights->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['cod'];

     $q="SELECT * FROM `".$table."` WHERE `move`>'".$this->move."'";
     if($this->uselevels==1) $q .= " AND `level`='".$level."'";
        if(isset($this->arraySettingsType['spr']) && !empty($this->arraySettingsType['spr'])){
            foreach($this->arraySettingsType['spr'] as $key=>$rowTxt){
                if($rowTxt['typeLink']=='one')
                    if(!empty($this->arrayValues[$rowTxt['filterName']]))
                        $q .= " AND `".$rowTxt['name']."`='".$this->arrayValues[$rowTxt['filterName']]."'";
            }
        }
        if($this->sort!='move'){
            $q .= " AND `".$this->sort."` ='".$row[$this->sort]."' ";
        }

         if($this->asc_desc=='asc') $asc_desc = $this->asc_desc;
        else $asc_desc = 'asc';
        $q .= " GROUP BY `cod` ORDER BY `".$this->sort."` ".$asc_desc;
        if($this->sort!='move') $q .= ", `move` ".$asc_desc;
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//     echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
     if( !$res )return false;
     $rows = $this->Rights->db_GetNumRows();
     $row = $this->Rights->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['cod'];

     //echo '<br> $move_down='.$move_down.' $move_up ='.$move_up;
     if( $move_down!=0 AND $move_up!=0 )
     {
     $q="UPDATE `".$table."` SET `move`='".$move_down."' WHERE `cod`='".$id_up."'";
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
     $q="UPDATE `".$table."` SET `move`='".$move_up."' WHERE `cod`='".$id_down."'";
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
     }
    } // end of function up()

    // ================================================================================================
    // Function : down()
    // Version : 1.0.0
    // Date : 11.02.2005
    // Parms :
    // Returns :      true,false / Void
    // Description :  Down position
    // ================================================================================================
    // Programmer :  Andriy Lykhodid
    // Date : 11.02.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
   function down($table, $level = 0)
    {
     $q="SELECT * FROM `".$table."` WHERE `move`='".$this->move."'";
     if($this->uselevels==1) $q = $q." AND `level`='".$level."'";
        if(isset($this->arraySettingsType['spr']) && !empty($this->arraySettingsType['spr'])){
            foreach($this->arraySettingsType['spr'] as $key=>$rowTxt){
                if($rowTxt['typeLink']=='one')
                    if(!empty($this->arrayValues[$rowTxt['filterName']]))
                        $q .= " AND `".$rowTxt['name']."`='".$this->arrayValues[$rowTxt['filterName']]."'";
            }
        }
     $q = $q." GROUP BY `cod`";
//        echo ' $this->user_id='.$this->user_id.' $this->module='.$this->module;die();
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//        echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;//die();
     if( !$res )return false;
     $rows = $this->Rights->db_GetNumRows();
     $row = $this->Rights->db_FetchAssoc();
     $move_up = $row['move'];
     $id_up = $row['cod'];
     $q="SELECT * FROM `".$table."` WHERE `move`<'".$this->move."'";
     if($this->uselevels==1) $q=$q." AND `level`='".$level."'";
        if(isset($this->arraySettingsType['spr']) && !empty($this->arraySettingsType['spr'])){
            foreach($this->arraySettingsType['spr'] as $key=>$rowTxt){
                if($rowTxt['typeLink']=='one')
                    if(!empty($this->arrayValues[$rowTxt['filterName']]))
                        $q .= " AND `".$rowTxt['name']."`='".$this->arrayValues[$rowTxt['filterName']]."'";
            }
        }

        if($this->sort!='move'){
            $q .= " AND `".$this->sort."` ='".$row[$this->sort]."' ";
        }
        $asc_desc = 'desc';
     $q .= " GROUP BY `cod` ORDER BY `".$this->sort."` ".$asc_desc;
        if($this->sort!='move') $q .= ", `move` ".$asc_desc;
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//     echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
     if( !$res )return false;
     $rows = $this->Rights->db_GetNumRows();
     $row = $this->Rights->db_FetchAssoc();
     $move_down = $row['move'];
     $id_down = $row['cod'];

     if( $move_down!=0 AND $move_up!=0 )
     {
     $q="UPDATE `".$table."` SET `move`='".$move_down."' WHERE `cod`='".$id_up."'";
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;

     $q="UPDATE `$table` SET `move`='".$move_up."' WHERE `cod`='".$id_down."'";
     $res = $this->Rights->Query( $q, $this->user_id, $this->module );
     //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
     }
    } // end of function down()


   // ================================================================================================
   // Function : AutoInsertColumnMove
   // Version : 1.0.0
   // Date : 18.04.2006
   //
   // Parms :         $spr      / name of table, from which will be select data
   // Returns : true,false / Void
   // Description :
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 18.04.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AutoInsertColumnMove( $spr )
   {
      $tmp_db = new DB();
      $q = "SELECT * FROM `".$spr."` WHERE 1 LIMIT 1";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $fields_col = mysql_num_fields($tmp_db->result);
      if ($fields_col==4) {
        $q = "ALTER TABLE `".$spr."` ADD `move` INT( 11 ) UNSIGNED NULL";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res ) return false;
        if ( !$tmp_db->result ) return false;

        $q = "ALTER TABLE `".$spr."` ADD INDEX ( `move` )";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res ) return false;
        if ( !$tmp_db->result ) return false;

        $res = $this->AutoInsertDataIntoColumnMove( $spr );
        if ( !$res ) return false;
      }
      return true;
   } //end of function AutoInsertColumnMove()

   // ================================================================================================
   // Function : AutoInsertColumnNode
   // Version : 1.0.0
   // Date : 18.04.2006
   //
   // Parms :         $spr      / name of table, from which will be select data
   // Returns : true,false / Void
   // Description :
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 18.04.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AutoInsertColumnNode( $spr )
   {
      $node = 0;
      $str = '0';
      while(1)
      {
      $q = "SELECT `cod` FROM `".$spr."` WHERE `level`=".$str;
      $res = $this->db->db_Query($q);
//      echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$this->db->result ) return false;
      $rows = $this->db->db_GetNumRows();
      if ($rows == 0) return true;
      $row = $this->db->db_FetchAssoc();
      $str = $row['cod'];
      for ($i=1;$i<$rows;$i++){
        $row = $this->db->db_FetchAssoc();
        $str .= ', '.$row['cod'];
      }
      $q = "UPDATE `".$spr."` SET
             `node`='".$node."'
             WHERE `cod` in (".$str.")";
             $res = $this->db->db_Query($q);
//        echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      $node++;
      }
      return true;
   } //end of function AutoInsertColumnNode()

   // ================================================================================================
   // Function : AutoInsertDataIntoColumnMove
   // Version : 1.0.0
   // Date : 18.04.2006
   //
   // Parms :         $spr      / name of table, from which will be select data
   // Returns : true,false / Void
   // Description :
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 18.04.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AutoInsertDataIntoColumnMove( $spr )
   {
      $tmp_db = new DB();
      $tmp_db2 = new DB();
      $q = "SELECT * FROM `".$spr."` WHERE 1 AND `lang_id`='"._LANG_ID."'";
      $res = $tmp_db->db_Query($q);
      //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
      if ( !$res ) return false;
      if ( !$tmp_db->result ) return false;
      $rows = $tmp_db->db_GetNumRows();
      for ($i=0;$i<$rows;$i++){
        $row = $tmp_db->db_FetchAssoc();
        $q = "UPDATE `".$spr."` SET
             `move`='".$row['cod']."'
             WHERE `cod`='".$row['cod']."'";
        $res = $tmp_db2->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if ( !$res ) return false;
        if ( !$tmp_db2->result ) return false;
      }
      return true;
   } //end of function AutoInsertDataIntoColumnMove()

   // ================================================================================================
   // Function : CreateSpr
   // Version : 1.0.0
   // Date : 09.11.2006
   // Parms :   $spr      / name of table, where will be adding field `img`
   // Returns : true,false / Void
   // Description : create spr with name $this->spr id it is not exist. This is for Automaticly creation of spr
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 09.11.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
    function CreateSpr( $spr )
   {
      if ( !$this->db->IsTableExist($spr) ) {
        $q = "CREATE TABLE `".$spr."` (
              `id` int(4) unsigned NOT NULL auto_increment";
        if(strstr($spr, 'spr_txt'))
            $q = $q.",`cod` varchar(100) default NULL";
        else
            $q = $q.",`cod` int(4) unsigned NOT NULL default '0'";
        $q = $q.",`lang_id` int(4) unsigned NOT NULL default '0'";

        $q = $q.",`name` varchar(255) default NULL";

        $q = $q.",`move` int(11) unsigned default NULL";
        if($this->usecolors==1)
            $q = $q.",`colorsBit` varchar(10) default 'ffffff'";
        if($this->uselevels==1)
            $q = $q.",`level` int(11) unsigned default '0',`node` smallint(5) unsigned default '0'";
        $q = $q."
              ,PRIMARY KEY  (`id`)
              ,KEY `cod` (`cod`)
              ,KEY `lang_id` (`lang_id`)
              ,KEY `move` (`move`)";
        if($this->uselevels==1)
              $q = $q.",KEY `level` (`level`)";
        $q = $q.")";

        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res OR !$this->db->result ) return false;
      }
      return true;
   } //end of function CreateSpr()

   // ================================================================================================
   // Function : DelItemImage
   // Version : 1.0.0
   // Date : 06.11.2006
   //
   // Parms :   $img   / name of the image
   // Returns : true,false / Void
   // Description :  Remove iamge from table and from the disk
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 06.11.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
    function DelItemImage($nameIcon=NULL,$cod=NULL, $lang_id=NULL)
   {
       if(empty($nameIcon) || empty($cod)) return false;
       $q = "SELECT * FROM `".$this->spr."` WHERE `cod`='".$cod."' ";
       if(!empty($lang_id))$q .= " AND `lang_id`='".$lang_id."'";
       $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//       echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
       if(!$res || !$this->Rights->result ) return false;
       $rows = $this->Rights->db_GetNumRows();
//       echo '$rows='.$rows;
       if ($rows == 0) return false;
       for($i=0;$i<$rows;$i++){
           $row = $this->Rights->db_FetchAssoc();
           if( empty($lang_id)){
               $path = Spr_Img_Path.$this->spr;
           }else{
               $path = Spr_Img_Path.$this->spr.'/'.$lang_id;
           }
           $path_file = $path.'/'.$row[$nameIcon];
//           echo '$path_file='.$path_file;
           // delete file which store in the database
           if (file_exists($path_file)) {
               @unlink ($path_file);
           }
//           echo '<br> $path='.$path;
           $handle = @opendir($path);
           //echo '<br> $handle='.$handle;
           $cols_files = 0;
           while ( $file = readdir($handle) ) {
//               echo '<br> $file='.$file;
               $mas_file=explode(".",$file);
               $mas_img_name=explode(".",$row[$nameIcon]);
               if ( strstr($mas_file[0], $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT) ) {
                   @unlink ($path.'/'.$file);
               }
               if ($file == "." || $file == ".." ) {
                   $cols_files++;
               }
           }
           closedir($handle);
       }

       $q = "UPDATE `".$this->spr."` SET `".$nameIcon."` = NULL WHERE `cod`='".$cod."' ";
       if(!empty($lang_id))$q .= " AND `lang_id`='".$lang_id."'";
       $res = $this->Rights->Query( $q, $this->user_id, $this->module );
//       echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
       if(!$res || !$this->Rights->result ) return false;
       return true;
  } //end of function DelItemImage()

   // ================================================================================================
   // Function : DelImageByCod
   // Version : 1.0.0
   // Date : 07.11.2006
   //
   // Parms :   $cod   / cod of the item record
   // Returns : true,false / Void
   // Description :  Remove image by cod from table and from the disk
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 06.11.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
    function DelImageByCod($cod)
   {
       if(isset($this->arraySettingsType['icon'])
           && !empty($this->arraySettingsType['icon'])){
           foreach($this->arraySettingsType['icon'] as $rowTxt){
               if ( !$this->DelItemImage($rowTxt['name'],$cod) ) return false;
           }
       }
       return true;
  } //end of function DelImageByCod()

   // ================================================================================================
   // Function : ShowErrBackEnd()
   // Version : 1.0.0
   // Date : 10.01.2006
   //
   // Parms :
   // Returns :      true,false / Void
   // Description :  Show errors
   // ================================================================================================
   // Programmer :  Igor Trokhymchuk
   // Date : 10.01.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
  function ShowErrBackEnd()
   {
     if ($this->Err){
       echo '
        <table border=0 cellspacing=0 cellpadding=0 class="err" align="center">
         <tr><td align="left">'.$this->Err.'</td></tr>
        </table>';
     }
   } //end of fuinction ShowErrBackEnd()

   // ================================================================================================
   // Function : ShowPathToLevel()
   // Version : 1.0.0
   // Date : 22.05.2008
   //
   // Parms :        $id - id of the record in the table
   // Returns :      $str / string with name of the categoties to current level of catalogue
   // Description :  Return as links path of the categories to selected level of catalogue
   // ================================================================================================
   // Programmer :  Igor Trokhymchuk
   // Date : 22.05.2008
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
  function ShowPathToLevel( $spr, $level, $script, $lang_id=NULL, $str = NULL )
   {
     if($level!=0)
     {
         if( empty($lang_id) ) $lang_id = $this->lang_id;
         $q="SELECT ";
         $q.='t0.name as name0, t0.level as level0, t0.node as node0, t0.cod as cod0 ';
         for($i=1; $i<=$this->node;$i++)
         {
             $q.=', t'.$i.'.name as name'.$i.', '.'t'.$i.'.level as level'.$i.', '.'t'.$i.'.node as node'.$i.', '.'t'.$i.'.cod as cod'.$i;
         }
         $q.= ' FROM '.$spr.' AS t0 ';
         for($i=1; $i<=$this->node;$i++)
         {
             $q.='LEFT JOIN '.$spr.' AS t'.$i.' ON t'.$i.'.level = t'.($i-1).'.cod ';
         }
         $q.=' WHERE 1 ';
         if( !empty($lang_id) )
         for($i=0; $i<=$this->node;$i++)
         {
             //$q.='LEFT JOIN '.$spr.' AS t'.$i.' ON t'.$i.'.level = t'.($i-1).'.cod ';
             $q = $q.' AND t'.$i.'.lang_id="'.$lang_id.'"';
         }

         $q = $q." AND t".($i-1).".cod='".$level."' GROUP BY t0.cod ";
         $res = $this->db->db_Query( $q );
    //     echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
         if( !$res )return false;
         $row = $this->db->db_FetchAssoc();
         $url0 = '/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&task=show_sublevel&uselevels='.$this->uselevels.'&level=0&srch=';
         echo '<a href="'.$script.'&level=0" onclick="GoToSubLevel('."'".$url0."'".', '."'wndw0'".' ); return false;">'.$this->multi['_TXT_ROOT_LEVEL'].'</a> <span class="not_href">></span>';
         for($i=0; $i<=$this->node;$i++)
         {
             $url='/admin/modules/sys_spr/sys_spr.php?'.$this->script_ajax.'&task=show_sublevel&uselevels='.$this->uselevels.'&level='.$row['cod'.$i].'&node='.$row['node'.$i].'&srch=';
             if($i!=$this->node)
                echo '<a href="'.$script.'&level='.$row['cod'.$i].'&node='.$row['node'.$i].'" onclick="GoToSubLevel('."'".$url."'".', '."'wndw0'".' ); return false;">'.strip_tags($row['name'.$i]).'</a> <span class="not_href">></span> ';
             else
                echo '<b>'.strip_tags($row['name'.$i]).'</b>';
         }
     }
     return true;
   }//end of function ShowPathToLevel()


    /**
     * @param $Table
     * @param $TableMany
     * @param $name_fld
     * @param $nameMany
     * @param $id_bond
     * @param $val
     * @param null $cod_bond
     * @programmer Bogdan Iglinsky
     */
    function ShowInCheckBoxNew($Table,$TableMany,$name_fld, $nameMany,$id_bond,$val,$cod_bond = NULL){
        $row1 = NULL;
        if (empty($name_fld)) $name_fld=$Table;

        $tmp_db = DBs::getInstance();

        $q = "SELECT * FROM `".$Table."` WHERE `lang_id`='"._LANG_ID."'";
        $res = $tmp_db->db_Query($q);
//        echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res) return false;
        $rows = $tmp_db->db_GetNumRows();
        $arr_data = array();
        for( $i = 0; $i < $rows; $i++ )
        {
            $row000 = $tmp_db->db_FetchAssoc();
            $arr_data[$i] = $row000;
        }
        ?>
        <table border="0" cellpadding="1" cellspacing="1" align="left" class="checkbox_tbl">
            <tr>
                <?
                for( $i = 0; $i < $rows; $i++ )
                {
                $row1 = $arr_data[$i];
                $col_check=1;
                    ?></tr><tr><?

                $checked ='>';
                if (is_array($val)) {
                    if (isset($val))
                        foreach($val as $k=>$v)
                        {
                            if (isset($k) and ($v==$row1['cod'])) $checked = " checked".$checked;
                            //echo '<br>$k='.$k.' $v='.$v.' $row1[cod]='.$row1['cod'];
                        }
                }
                $align= 'left';
                ?><td align="<?=$align?>" valign="top" class="checkbox"><?

                        //echo "<table border='0' cellpadding='1' cellspacing='0'><tr><td><input class='checkbox' type='checkbox' name='".$name_fld."[]' value='".$row1['cod']."' ".$disabled." ".$checked.'</td><td>'.stripslashes($row1['name']).'</td></tr></table>';
                        ?>
                        <label class="checkbox_td">
                            <input class="checkbox" type="checkbox" name="<?=$name_fld;?>[]"
                                   value="<?=$row1['cod'];?>"onchange="$('#contentFor<?=$row1['cod']?>').toggle();" <?=$checked;?>
                            <?=stripslashes($row1['name']);?>
                        </label>
                    <?


                    //======= show sublevels START ===========
                    if(!empty($row1['cod'])){
//                        echo 'cod='.$row1['cod'];
                        $value = $this->getSprManyLink($this->spr.'_'.$name_fld,$cod_bond,$nameMany,$name_fld,$row1['cod']);
//                        var_dump($value);
                    }
                        ?>
                        <table border="0" id="contentFor<?=$row1['cod']?>" cellpadding="1" cellspacing="0"<?
                        if($checked=='>'){
                            ?> style="display: none" <?
                        }
                        ?>>
                            <tr>
                                <td style="padding:0px 0px 0px 20px;"><?
                                    $this->ShowInCheckBox( $TableMany, $nameMany.'['.$row1['cod'].']', 1, $value, 'left', NULL, 'move', 'asc', false, $row1['cod'],$id_bond);
                                    ?>
                                </td>
                            </tr>
                        </table>
                    <?
                    //======= show sublevels END ===========
                    ?></td><?
                $col_check++;
                }
                ?>
            </tr>
        </table>
    <?
    }

    /**
     *
     * @programmer Bogdan Iglinsky
     */
    function showInFunc(){
//        var_dump($this->arrExistSettings);
        if(isset($this->arrExistSettings) && !empty($this->arrExistSettings)){
            foreach($this->arrExistSettings as $key=>$rowTxt){
                ?><li><?=$key;?> - <?=$rowTxt['label']?>. Тип: <b><?=$rowTxt['type']?></b><?
                if(isset($rowTxt['typeLink'])){
                    ?> typeLink=<?=$rowTxt['typeLink'];
                }
                ?></li><?
            }
        }
    }

    /**
     * @param $rating_spr
     * @param $fieldCod
     * @param null $cod
     * @param int|string $lang_id
     * @return array
     * @programmer Bogdan Iglinsky
     */
    function getArrayRating($rating_spr,$fieldCod,$cod=NULL,$lang_id=_LANG_ID){
        if(!isset($row[$fieldCod])) return false;
        if(empty($cod) && !empty($this->cod)) $cod = $this->cod;
        $q=" SELECT `".$rating_spr."`.*
             FROM `".$rating_spr."`
             WHERE `lang_id` = ".$lang_id."";
        if( !empty($cod)) $q .= " AND `".$fieldCod."`='".$cod."'";

        $returnArray = $this->Rights->QueryResult($q, $this->user_id, $this->module);
        //echo '<br> $q='.$q.' $this->user_id='.$this->user_id.' $this->module='.$this->module.' $this->Rights->result='.$this->Rights->result. ' $rating_spr='.$rating_spr.' $res='.$res;
//        echo '<br> $q='.$q.' $result=';
//        var_dump($returnArray);
        if (empty($returnArray) || !$this->Rights->result ) return false;
        $arrReturn = array();
        foreach($returnArray as $row){
            if(!isset($arrReturn[$row[$fieldCod]]['count']))
                $arrReturn[$row[$fieldCod]]['count'] = 1;
            else $arrReturn[$row[$fieldCod]]['count'] ++;
            if(!isset($arrReturn[$row[$fieldCod]]['sum'])) $arrReturn[$row[$fieldCod]]['sum'] = $row['rating'];
            else $arrReturn[$row[$fieldCod]]['sum'] += $row['rating'];
            $arrReturn[$row[$fieldCod]]['items'][] =  $row;
        }
        if(!empty($arrReturn)){
            foreach($arrReturn as $key=>$row){
                $count = $row['count'];
                $sum = $row['sum'];
                $arrReturn[$key]['average'] = round($sum/$count,1);
            }
        }
//        var_dump($arrReturn);
        return $arrReturn;
    }

    /**
     * @param $rating_spr
     * @param $fieldCod
     * @param null $cod
     * @param int|string $lang_id
     * @return array
     * @programmer Bogdan Iglinsky
     */
    function getArrayValues($rating_spr,$fieldCod,$cod=NULL,$lang_id=_LANG_ID){
        if(empty($cod) && !empty($this->cod)) $cod = $this->cod;
        $q=" SELECT `".$rating_spr."`.*
             FROM `".$rating_spr."`
             WHERE `lang_id` = ".$lang_id."";
        if( !empty($cod)) $q .= " AND `".$fieldCod."`='".$cod."'";

        $returnArray = $this->Rights->QueryResult($q, $this->user_id, $this->module);
        //echo '<br> $q='.$q.' $this->user_id='.$this->user_id.' $this->module='.$this->module.' $this->Rights->result='.$this->Rights->result. ' $rating_spr='.$rating_spr.' $res='.$res;
//        echo '<br> $q='.$q.' $result=';
//        var_dump($returnArray);
        if (empty($returnArray) || !$this->Rights->result ) return false;
        $arrReturn = array();
        foreach($returnArray as $row){
            if(!isset($arrReturn[$row[$fieldCod]])) $arrReturn[$row[$fieldCod]] = $row['name'];
            else $arrReturn[$row[$fieldCod]] .= '<br>'.$row['name'];
        }
//        var_dump($arrReturn);
        return $arrReturn;
    }

    /**
     * @param null $spr
     * @param string $name
     * @param null $cod
     * @return bool
     * @programmer Bogdan Iglinsky
     */
    function updateStatus($spr = NULL,$name = '', $cod = NULL ){
        if(empty($spr) || empty($name)) return false;
        $q = "select * from ".$spr."";
        if(!empty($cod))$q .= "  where  `cod` = '".$cod."' ";
        $res = $this->db->db_Query( $q );
//          echo '<br/> $q='.$q.' $res='.$res;
        if(!$res) return false;
        $rows = $this->db->db_GetNumRows();
        $arr = array();
        for( $i = 0; $i < $rows; $i++ ){
            $arr[] = $this->db->db_FetchAssoc();
        }

        $dt_now = strftime('%Y-%m-%d %H:%M', strtotime('now'));
        for( $i = 0; $i < $rows; $i++ ){
            $tmp = $arr[$i];
            $status = '';
//              echo '<br>start='.$tmp[$name].' end='.$tmp[$name.'_end'].' now='.$dt_now;
            if($tmp[$name.'_end'] > $dt_now && $tmp[$name] < $dt_now){
                $status = 'a';
            }elseif($tmp[$name.'_end'] < $dt_now){
                $status = 'e';
            }elseif($tmp[$name] > $dt_now){
                $status = 'n';
            }
            if($tmp[$name.'_status']!=$status){
                $q = "update ".$spr." set `".$name."_status`='".$status."' where `id`='".$tmp['id']."'";
                $res = $this->db->db_Query( $q );
//                  echo '<br/> $q='.$q.' $res='.$res;
            }
        }
        return  true;
    } //--- end of CheckStatus

    /**
     * @param $Table
     * @param int|string $lang_id
     * @param bool $visible
     * @return array
     * @programmer Bogdan Iglinsky
     */
    function GetArrNameBySpr($Table, $lang_id = _LANG_ID,$visible = false){
        $q="SELECT * FROM `".$Table."` WHERE `lang_id`='".$lang_id."'";
        if($visible) $q .= " and `visible` = '1' ";
        $res = $this->db->db_Query($q);
//         echo '<br> $q='.$q.'  $this->db->result='.$this->db->result;
        if ( !$res OR !$this->db->result ) return false;
        $row_num = $this->db->db_GetNumRows();
        $arrReturn = array();
        for ($i=0;$i<$row_num;$i++)
        {
            $row_res = $this->db->db_FetchAssoc();
            $arrReturn[$row_res['cod']] = $row_res['name'];
        }
        return $arrReturn;
    }

    /**
     * @param $Table
     * @param $Table2
     * @param $name
     * @param int|string $lang_id
     * @param bool $visible
     * @return array
     * @programmer Bogdan Iglinsky
     */
    function GetArrNameBySprMany($Table,$Table2,$name, $lang_id = _LANG_ID,$visible = false){
        $q="SELECT `".$Table."`.* ,`".$Table2."`.`cod` as `cod_spr`
         FROM `".$Table."`,`".$Table2."`
         WHERE `".$Table."`.`lang_id`='".$lang_id."'
         and `".$Table."`.`cod` = `".$Table2."`.`cod_".$name."`";
        if($visible) $q .= " and `visible` = '1' ";
        $res = $this->db->db_Query($q);
//         echo '<br> $q='.$q.'  $this->db->result='.$this->db->result;
        if ( !$res OR !$this->db->result ) return false;
        $row_num = $this->db->db_GetNumRows();
        $arrReturn = array();
        for ($i=0;$i<$row_num;$i++)
        {
            $row_res = $this->db->db_FetchAssoc();
            if(!isset($arrReturn[$row_res['cod_spr']])) $arrReturn[$row_res['cod_spr']] = '';
            if(!empty($arrReturn[$row_res['cod_spr']])) $arrReturn[$row_res['cod_spr']] .= ', ';
            $arrReturn[$row_res['cod_spr']] .= $row_res['name'];
        }
        return $arrReturn;
    }
 }  //end of class SysSpr
