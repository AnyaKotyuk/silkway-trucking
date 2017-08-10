<?php
// ================================================================================================
// System : SEOCMS
// Module : asked_frontend.class.php
// Version : 1.0.0
// Date : 27.05.2009
//
// Purpose    : Class definition for layout of Asked
//
// ================================================================================================

class AskedLayout extends Asked {

    var $fltr = NULL;
    var $category = null;

    // ================================================================================================
    //    Function          : AskedLayout (Constructor)
    //    Version           : 1.0.0
    //    Date              : 27.05.2009
    //    Returns           : Error Indicator
    //    Description       : Set the variabels
    // ================================================================================================
     function AskedLayout($user_id = NULL, $module = NULL)
     {
         //Check if Constants are overrulled
        ( $user_id != "" ? $this->user_id = $user_id : $this->user_id = NULL );
        ( $module != "" ? $this->module = $module : $this->module = 87 );

         $this->db =  DBs::getInstance();
         $this->Form = check_init('FormAsked', 'FrontForm', "'mod_asked'");
         if(empty($this->multi)) $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
         if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
         if (empty($this->Crypt)) $this->Crypt = check_init('Crypt', 'Crypt');
         if(empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr');
     }


    /**
     * AskedLayout::GetNRows()
     * @author Yaroslav
     * @param bool $limit
     * @return
     */
    function GetNRows( $limit = false )
    {
        $q = "SELECT * FROM ".TblModAsked." WHERE `visible` = '1' ";
        if( $this->fltr!='' ) $q = $q.$this->fltr;
        $q .=" ORDER BY `id` DESC";
        if($limit) $q .= " limit ".$this->start.",".$this->display."";
        $res = $this->db->db_Query( $q );
        //echo "<br>q=".$q." res=".$res;
        if( !$res OR !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        //echo "<br>rows=".$rows;
        $array = array();
        for( $i = 0; $i <$rows; $i++ ){
            $array[] = $this->db->db_FetchAssoc();
        }
        return $array;
    } // end of GetNRows



    /**
     * AskedLayout::ShowAnswersByPages()
     *
     * @author Yaroslav
     * @param mixed $page
     * @return void
     */
    function ShowAnswersByPages()
    {
        $array = $this->GetNRows(true);
        $rows = count($array);
        if(ASKED_SHOW_FORM){
            $this->ShowFormHeader();
        }
        if($rows==0){
            echo View::factory('/modules/mod_asked/tpl_asked_page/tpl_err_page.php')
                ->bind('msg', $this->multi['MSG_NO_ASKED']);
            return;
        }
        for($i = 0; $i < $rows; $i++) {
            $row = $array[$i];
            $rowReal['question'] = stripslashes($row['question']);
            $rowReal['fullAnswer'] = stripslashes($row['answer']);
            $rowReal['author'] = stripslashes($row['author']);
            $rowReal['dttm'] = substr($row['dttm'],0,10);
            if(ASKED_RATING){
                $rowReal['rating+'] = $row['rating'];
                $rowReal['rating-'] = 5-$row['rating'];
            }
            if(empty($rowReal['fullAnswer'])) $rowReal['flag'] = false;
            else $rowReal['flag'] = true;
//            $shortAnswer = $this->Crypt->TruncateStr(strip_tags(stripslashes($fullAnswer), '<a>'),$counttextshow);

            $arr[$i] = $rowReal;
        }

        $array = $this->GetNRows();
        $rows1 = count($array);
        $link = _LINK."ask/";
        $pageLinl = $this->Form->WriteLinkPagesStatic( $link, $rows1, $this->display, $this->start, $this->sort, $this->page );

        echo View::factory('/modules/mod_asked/tpl_asked_page/tpl_asked_page.php')
            ->bind('arr', $arr)
            ->bind('rows', $rows)
            ->bind('multi',$this->multi)
            ->bind('pageLinl',$pageLinl);
    }


    function ShowFormHeader(){
        echo View::factory('/modules/mod_asked/tpl_asked_page/tpl_asked_js.php')
            ->bind('multi',$this->multi);
        echo View::factory('/modules/mod_asked/tpl_asked_page/tpl_asked_form_header.php')
            ->bind('Ask',$this);
    }

    function ShowForm()
    {
        echo View::factory('/modules/mod_asked/tpl_asked_page/tpl_asked_form.php')
            ->bind('Ask',$this);
    }

    function ShowGoodSend(){
        echo $this->multi['TXT_ASKED_GOOD'];
    }


    /**
     * AskedLayout::CheckFields()
     *
     * @author Yaroslav
     * @return
     */
    function CheckFields()
    {
        if (empty($this->author)) $this->Err['asked_author'] = $this->multi['MSG_EMPTY_NAME'];

        if (empty($this->email)) {
            $this->Err['asked_email'] = $this->multi['MSG_EMPTY_EMAIL'];
        } else if (!preg_match("/^[a-zA-Z0-9_.\-]+@[a-zA-Z0-9.\-].[a-zA-Z0-9.\-]+$/", $this->email)) {
            $this->Err['asked_email'] = 'Введите правильный E-mail';
        }

        if (empty($this->question)) $this->Err['asked_question'] = $this->multi['MSG_EMPTY_QUESTION'];

        return $this->Err;
    }


    /**
     * AskedLayout::Category()
     * Show List Of Categories for Left Menu
     * @author Yaroslav
     * @return void
     */
    function Category()
    {
        $q = "SELECT `".TblModAskedCat."`.*
              FROM `".TblModAskedCat."`
              WHERE `lang_id`='"._LANG_ID."'
              ORDER BY `move` ASC ";
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();

        if ($rows>0){
            //$this->Form->Title($this->multi["FLD_CHAPTERS"]);
            ?><div class="vertical-menu">
                <ul><?
                $arr = array();
                for( $i = 0; $i < $rows; $i++ )
                    $arr[] = $this->db->db_FetchAssoc();

                for( $i = 0; $i < $rows; $i++ ){
                    $row = $arr[$i];
                    $name = $row['name'];
                    $q1 = "select * from ".TblModAsked." where category='".$row['cod']."' and visible = '1' ";
                    $res1 = $this->db->db_Query( $q1 );
                    //echo $q1.'<br/> res1 ='.$res1;
                    $rows1 = $this->db->db_GetNumRows();

                    if( $rows1 ) {
                        $class='';
                        if($this->category== $row['cod'])
                            $class='Active';
                        $link =  $this->Link($row['cod']);
                        ?><li><a class="<?=$class;?>" href="<?=$link;?>"><?=$name;?></a></li><?
                    } // end if
                } // end for
                ?></ul>
            </div><?
        }
    } //end of function Category
}
?>