<?php
/**
 * search.class.php
 * class for Contol of search result on Front-end
 * @package Dynamic Pages Package of SEOCMS
 * @author Igor Trokhymchuk  <bi@seotm.com>
 * @version 1.1, 05.08.2011
 * @copyright (c) 2010+ by SEOTM
 */
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_search/search.defines.php' );

/**
 * Class Search
 * class for ctore and control of search result on Front-end.
 * @author Igor Trokhymchuk  <bi@seotm.com>
 * @version 1.1, 05.08.2011
 */
class Search{
    public $user_id = NULL;
    public $module = NULL;
    public $Err=NULL;

    public $sort = NULL;
    public $display = 50;
    public $start = 0;
    public $fln = NULL;
    public $width = 500;
    public $spr = NULL;
    public $srch = NULL;

    public $db = NULL;
    public $Msg = NULL;
    public $Right = NULL;
    public $Form = NULL;
    public $Spr = NULL;

    public $date = NULL;
    public $time = NULL;
    public $query = NULL;
    public $ip = NULL;
    public $result = NULL;

    /**
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @param null $user_id
     * @param null $module
     * @param null $display
     * @param null $sort
     * @param null $start
     * @param null $width
     */
    function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
        ( $display  !="" ? $this->display = 60  : $this->display = 60   );
        ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
        ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
        ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

        $this->db =  DBs::getInstance();
        $this->Right =  check_init('RightsNews', 'Rights', "'".$this->user_id."','".$this->module."'");
        $this->Form = check_init('FormNews', 'FrontForm', "'form_mod_links_set'");
        if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr');
        $this->ip = $_SERVER['REMOTE_ADDR'];
        if (empty($this->multi)){
            $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
        }
    }

    // ================================================================================================
    // Function : save_search()
    // Version : 1.0.0
    // Date : 27.03.2008
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :
    // ================================================================================================
    // Programmer :  Alex Kerest
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function save_search($cnt){

        $q = "INSERT INTO `".TblModSearchResult."` SET
        `query` = '".$this->query."',
        `modname` = '".$this->modname."',
        `ip` = '".$this->ip."',
        `date` = '".strftime('%Y-%m-%d %H:%M', strtotime('now'))."',
        `cnt` = '".$cnt."'
        ";
        $res = $this->db->db_Query( $q );
//        echo "<br> q = ".$q." res = ".$res;
    } // end of function save_search

    /**
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @param $name
     * @param $val
     */
    function formSearchBigOnElemSelect($name,$val){
        ?>
        <option value="<?=$val?>"<?
        if($val==$this->modname){
            ?> selected="selected" <?
        }
        ?>><?=$name;?></option>
        <?
    }

    /**
     * Class method formSearchBig
     * show html block with big search form
     * @param boolean $flag - flag for show title
     * @return html block with big search form
     * @author Igor Trokhymchuk  <bi@seotm.com>
     * @version 1.0, 05.04.2012
     */
    function formSearchBig($flag = false){
        ob_start();
        ?>
        <div align="center">
        <?if($flag){
            ?><div>
                <h3><?=$this->multi['MSG_FRONT_MOD_SEARCH_SHORT_QUERY'];?></h3>
            </div>
        <?}?>
        <form action="<?=_LINK;?>search/result/" method="get">
            <div class="input-one-item">
                <div class="input-label"><?=$this->multi['TXT_FRONT_MOD_SEARCH_PHRASE_SEARCH'];?>:</div>
                <div class="input-text">
                    <input type="text" name="query" size="30" class="formstyle" value="<?=stripslashes($this->query);?>">
                </div>
            </div>
            <div class="input-one-item">
                <div class="input-label"><?=$this->multi['TXT_FRONT_MOD_SEARCH_IN_TOPIC'];?>:</div>
                <div class="input-text">
                    <select name="modname" class="select2" style="width:200px;">
                        <option value="all"><?=$this->multi['TXT_FRONT_MOD_SEARCH_IN_ALL_TOPICS'];?></option>
                        <?if (defined("MOD_PAGES") AND MOD_PAGES){
                            $this->formSearchBigOnElemSelect($this->multi['TXT_FRONT_MOD_SEARCH_BY_DYNAMIC_PAGES'],'pages');
                        }
                        if (defined("MOD_PUBLIC") AND MOD_PUBLIC){
                            $this->formSearchBigOnElemSelect($this->multi['TXT_FIND_FO_OBJAV'],'public');
                        }
                        if (defined("MOD_ARTICLE") AND MOD_ARTICLE){
                            $this->formSearchBigOnElemSelect($this->multi['TXT_FRONT_MOD_SEARCH_BY_ARTICLES'],'articles');
                        }
                        if (defined("MOD_CATALOG") AND MOD_CATALOG){
                            $this->formSearchBigOnElemSelect($this->multi['TXT_FRONT_MOD_SEARCH_BY_CATALOG'],'catalog');
                        }
                        if (defined("MOD_NEWS") AND MOD_NEWS){
                            $this->formSearchBigOnElemSelect($this->multi['TXT_FRONT_MOD_SEARCH_BY_NEWS'],'news');
                        }
                        if (defined("MOD_GALLERY") AND MOD_GALLERY){
                            $this->formSearchBigOnElemSelect($this->multi['TXT_SEARC_FOR_GALLERY'],'gallery');
                        }
                        if (defined("MOD_VIDEO") AND MOD_VIDEO){
                            $this->formSearchBigOnElemSelect($this->multi['TXT_SEARCH_IN_VIDEO'],'video');
                        }
                        if (defined("MOD_DEALERS") AND MOD_DEALERS){
                            $this->formSearchBigOnElemSelect($this->multi['TXT_HISTORY_TRUE'],'dealers');
                        }?>
                    </select>
                </div>
            </div>
            <div class="input-one-item">
                <input class="button" type="submit" value="<?=$this->multi['TXT_FRONT_SEARCH'];?>"/>
            </div>
        </form>
        </div>
        <?
        $content = ob_get_clean();
        return $content;
    }

    /**
     * Class method formSearchSmall
     * show html block with small search form
     * @return html block with small search form
     * @author Igor Trokhymchuk  <bi@seotm.com>
     * @version 1.0, 05.04.2012
     */
    function formSearchSmall(){
        ?><div class="search" id="findBox">
            <form name="quick_find" method="get" action="<?=_LINK?>search/result/">
                <?$value = $this->multi['TXT_SEARCH'];?>
                <input type="text" value="<?=stripslashes($this->query);?>"  name="query"
                       size="20" maxlength="100" class="text-search"/>
                <input type="submit" value="<?=$value;?>" />
            </form>
        </div><?
    }

    /**
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @param $arrRes
     * @return array
     */
    function sortForRelav($arrRes){
        $arrRelav = array();
        foreach($arrRes as $key=>$val){
            if(isset($val['relev']))
                $arrRelav[$key] = $val['relev'];
            else
                $arrRelav[$key] = 0;
        }
        $arr_return = array();
        while(!empty($arrRelav)){
            $arrSearch = $this->doublemax($arrRelav);
            $arr_return[] = $arrRes[$arrSearch["i"]];
            unset($arrRelav[$arrSearch["i"]]);
        }
//        var_dump($arr_return);
        return $arr_return;
    }

    /**
     * @param $mylist
     * @return array
     */
    function doublemax($mylist){
        $maxvalue=max($mylist);
        while(list($key,$value)=each($mylist)){
            if($value==$maxvalue)$maxindex=$key;
        }
        return array("m"=>$maxvalue,"i"=>$maxindex);
    }

    /**
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @param $arr
     * @param $arrKey
     * @param $nameCategory
     * @param $Ob
     * @param $modname
     * @return array
     */
    function convertMas($arr,$arrKey,$nameCategory,$Ob,$modname){
        $mas = array();
        if(!empty($arr)){
        foreach($arr as $row){
            $rowAdd = array();
            foreach($arrKey as $key=>$val){
                $rowAdd[$key] = $row[$val];
            }
            switch($modname){
                case 'news':
                    $rowAdd['link'] = $Ob->Link($row['id_cat'],$row['link']);
                    break;
                case 'pages':
                    $rowAdd['link'] = $Ob->Link($row['id']);
                    break;
                case 'gallery':
                    $rowAdd['link'] = $Ob->getLink( $row['cat'], $row['link']);
                    break;
                case 'video':
                    $rowAdd['link'] = $Ob->getLink( $row['category'], $row['link']);
                    break;
                case 'dealers':
                    $rowAdd['link'] = _LINK.'dealers/'. $row['link'].'.html';
                    break;
            }

            $rowAdd['category'] = $nameCategory;
            $mas[] = $rowAdd;
        }
        }
        return $mas;
    }

    /**
     * Class method searchSwitch
     * contil for search on front-end
     * @param string $modname - name of the module
     * @return array with data to show
     * @author Igor Trokhymchuk  <bi@seotm.com>
     * @version 1.0, 05.04.2012
     */
    function searchSwitch($modname = NULL,$arrRes = array()){
        $mas = array();
        switch( $modname ) {
            case 'catalog':
                $arr = $this->Page->Catalog->QuickSearch($this->query);
                //var_dump($arr);
                $rows = count($arr);
                if($rows>0 && !empty($arr)){
                    $mas = $this->convertMas($arr,array('id'=>'id','head'=>'name','link'=>'link','relev'=>'relev'),
                    $this->multi['TXT_FRONT_MOD_SEARCH_IN_CATALOG'],$this->Page->Catalog,$modname);
                    //$this->Page->FrontendPages->ShowSearchResHead($this->multi['TXT_FRONT_MOD_SEARCH_IN_CATALOG']);
                    //$this->Page->Catalog->showSearchResult($arr);
                }
                break;
            case 'pages':// pages
                $arr = $this->Page->FrontendPages->QuickSearch($this->query);
                $mas = $this->convertMas($arr,array('id'=>'id','head'=>'pname','relev'=>'relev'),
                    $this->multi['TXT_FRONT_MOD_SEARCH_BY_DYNAMIC_PAGES'],$this->Page->FrontendPages,$modname);
                break;
            /* case 'public':
                    $arr = $this->Page->Public->QuickSearch($this->query);
                break;*/
            case 'articles':
                 $arr = $this->Page->Article->QuickSearch($this->query);
                 $mas = $this->convertMas($arr,array('id'=>'id','head'=>'name','date'=>'dttm','link'=>'link','relev'=>'relev'),
                 $this->multi['TXT_FRONT_MOD_SEARCH_BY_ARTICLES'],null,$modname);
                 break;
            case 'gallery':
                 $arr = $this->Page->Gallery->QuickSearch($this->query);
                 $mas = $this->convertMas($arr,array('id'=>'id','head'=>'name','date'=>'start_date','relev'=>'relev'),
                 $this->multi['TXT_FRONT_MOD_SEARCH_BY_GALLERY'],$this->Page->Gallery,$modname);
                 break;
            /*Треба дописати інші модулі*/
            case 'news':
                $arr = $this->Page->News->QuickSearch($this->query);
                $mas = $this->convertMas($arr,array('id'=>'id','head'=>'name','date'=>'start_date','relev'=>'relev'),
                $this->multi['TXT_FRONT_MOD_SEARCH_BY_NEWS'],$this->Page->News,$modname);
                break;
            case 'video':
                $arr = $this->Page->Video->QuickSearch($this->query);
                $mas = $this->convertMas($arr,array('id'=>'id','head'=>'name','date'=>'dttm','relev'=>'relev'),
                $this->multi['TXT_SEARCH_IN_VIDEO'],$this->Page->Video,$modname);
                break;
            case 'dealers':
                $arr = $this->Page->Dealer->QuickSearch($this->query);
                $mas = $this->convertMas($arr,array('id'=>'id','head'=>'name','relev'=>'relev'),
                $this->multi['TXT_HISTORY_TRUE'],$this->Page->Video,$modname);
                break;
        }
        return array_merge($arrRes,$mas);
    }

    /**
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @param $arr_res
     */
    function ShowSearchInSmall($arr_res){
        $rows = count($arr_res);
        //echo '<br>$rows='.$rows;
        if ($rows > 0) {
            ?>
            <ul class="ul-res-search"><?
            $end = $this->start + $this->display;
            if($end>$rows) $end = $rows;
            for ($i = $this->start; $i < $end; $i++) {
                $row = $arr_res[$i];
                $link = $row['link'];
                //var_dump($link);
                if(!empty($row['relev']))
                    $relev = $row['relev'];
                else
                    $relev = 0;
                ?>
                <li>
                    <span class="search-number"> <?=$i + 1?> </span>
                    <a href=<?= $link ?> class="search-res-name"><?= stripslashes($row['head']); ?></a><br />
                    <?if(!empty($row['date'])){?>
                        <span class="date-in-search"><?= $row['date'];?></span>
                    <?}?>
                    <div class="category-res-search" ><?=$row['category']?></div>
                </li>
            <?
            }
            ?></ul><?
            $link_all = '/search/result/';
            $param_link ='?query='.$this->query.'&modname='.$this->modname ;
            echo $this->Form->WriteLinkPagesStatic( $link_all, $rows, $this->display, $this->start, $this->sort, $this->page, $param_link);
        } else {
            echo $this->Msg->show_text('SEARCH_NO_RES');
        }
    }


} //end of class moderation
?>