<?
// ================================================================================================
//    System     : SEOCMS
//    Module     : Department
//    Date       : 01.07.2010
//    Licensed To: Yaroslav Gyryn   
//    Purpose    : Class definition for Department - moule
// ================================================================================================


// ================================================================================================
//    Class             : Department
//    Date              : 01.07.2010
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Department Module
//    Programmer        :  Yaroslav Gyryn
// ================================================================================================
class Department {
    var $id;
    var $dttm;
    var $category;
    var $status;
    var $img;
    var $position;
    var $name;
    var $short;
    var $full;

    var $Right;
    var $Form;
    var $Msg;
    var $Spr;

    var $page;   
    var $display;
    var $sort;
    var $start;
    var $rows;

    var $user_id;
    var $use_image;
    var $module;

    var $fltr;    // filter of group Production

    var $lang_id;
    var $sel = NULL;
    var $Err = NULL;
    var $title;
    var $keywords;
    var $description;

    var $str_cat;
    var $str_art;

    var $settings = null;
 
    // ================================================================================================
    //    Function          : Department (Constructor)
    //    Version           : 1.0.0
    //    Date              : 01.07.2010
    //    Parms             :
    //    Returns           :
    //    Description       : Department
    // ================================================================================================
    function Department()
    {
        if( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
        $this->Right =  new Rights;                   /* create Rights obect as a property of this class */
        $this->Form = new Form( 'form_art' );        /* create Form object as a property of this class */
        $this->Msg = new ShowMsg();                   /* create ShowMsg object as a property of this class */
        $this->Msg->SetShowTable(TblModDepartmentSprTxt);
        $this->use_image=1;
        $this->Spr = new SysSpr( NULL, NULL, NULL, NULL, NULL, NULL, NULL ); /* create SysSpr object as a property of this class */
     
        $this->settings = $this->GetSettings();
    }// end of Department (Constructor) 


    // ================================================================================================
    // Function : GetDepartmentData()
    // Date : 20.09.2009
    // Parms :
    // Returns :      true,false / Void
    // Description :  Return department data
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetDepartmentData( $art_id = NULL )
    {
        if(!$art_id) return true; 
        //$q = "select * from ".TblModNews." where id='$news_id'";
        $q = "SELECT `".TblModDepartment."`.*, 
                            `".TblModDepartmentCat."`.name AS `cat_name`, 
                            `".TblModDepartmentCat."`.translit AS cat_translit,
                            `".TblModDepartmentTxt."`.name AS `sbj`, 
                            `".TblModDepartmentTxt."`.translit, 
                            `".TblModDepartmentTxt."`.short,
                            `".TblModDepartmentTxt."`.full
              FROM `".TblModDepartment."`, `".TblModDepartmentCat."`, `".TblModDepartmentTxt."`
              WHERE `".TblModDepartment."`.id='".$art_id."'
              AND `".TblModDepartment."`.category=`".TblModDepartmentCat."`.cod
              AND `".TblModDepartmentCat."`.lang_id='".$this->lang_id."'
              AND `".TblModDepartment."`.id=`".TblModDepartmentTxt."`.cod
              AND `".TblModDepartmentTxt."`.lang_id='".$this->lang_id."'
             ";
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        return $rows;
    } //end of fuinction GetNewsData() 
 
    // ================================================================================================
    // Function : ConvertDate()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Convert Date to nidle format
    // ================================================================================================
    function ConvertDate($date_to_convert){
    //print_r($tmp = explode("-", $date_to_convert));
    $tmp = explode("-", $date_to_convert);
    $tmp2 = explode(" ", $tmp[2]);
    //$m_word = NULL;
    $month = NULL;
    $day = NULL;
    $year = NULL;
    //$time = NULL;
    
    //$month =  $this->Spr->GetShortNameByCod(TblSysSprMonth, intval($tmp[1]), $this->lang_id, 1);
    $month =  $tmp[1];
    $day = intval($tmp2[0]);
    $year = $tmp[0];
    //$time = $tmp2[1];
 
    //if ( isset($settings['dt']) AND $settings['dt']=='0' )  
        return $day.".".$month.".".$year;
    //return $day.".".$month.".".$year." ".$time;    
} // end of function ConvertDate



    // ================================================================================================
    // Function : Link()
    // Date : 12.01.2010
    // Parms :
    // Returns :
    // Description : Return Link 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function Link( $cat=NULL , $id = NULL, $param=NULL)
    {
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
        if($cat==NULL)
            return _LINK.'department/';
            
        if($cat!=NULL and $id==NULL) 
            return _LINK.'department/'.$cat.'/';
            
        if($id!=NULL and $param==NULL)
            return _LINK.'department/'.$cat.'/'.$id.'.html';
        else
            return _LINK.'department/'.$cat.'/'.$id.'/'.$param.'/';
    } // end of function Link()


    // ================================================================================================
    // Function : GetIdCatByIdArt()
    // Date : 01.07.2010
    // Parms :
    // Returns :
    // Description : 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetIdCatByIdArt($id){
      $q = "select * from ".TblModDepartment." where 1 and `id`='".$id."'";
      $res = $this->db->db_Query( $q );
      $rows = $this->db->db_GetNumRows();
    //echo "<br> q=".$q." res=".$res." rows=".$rows;
      $row = $this->db->db_FetchAssoc();    
    return $row['category']; 
    } // end of function GetIdCatByIdArt

    // ================================================================================================
    // Function : GetIdArtByStrArt()
    // Date : 01.07.2010
    // Parms :
    // Returns :
    // Description : 
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetIdArtByStrArt($str_art)
    {
        $q = "select * from ".TblModDepartmentLinks." where 1 and `link`='".$str_art."'";
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();
        //echo "<br>GetIdArtByStrArt  q=".$q." res=".$res." rows=".$rows;
        $row = $this->db->db_FetchAssoc();
        //echo "<br>ART q=".$q." res=".$res.' rows='.$rows.' cod='.$row['cod']; 
        return $row['cod']; 
    } // end of function GetIdArtByStrArt

    
    
    //------------------------------------------------------------------------------------------------------------
    //---------------------------- FUNCTION FOR SETTINGS OF OFFERS START ---------------------------------------       
    //------------------------------------------------------------------------------------------------------------
        
   // ================================================================================================
   // Function : GetSettings()
   // Date : 01.07.2010
   // Parms :
   // Returns : true,false / Void
   // Description : return all settings of Gatalogue
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
   function GetSettings()
   {       
    $db = new DB();
    $q="select * from `".TblModDepartmentSet."` where 1";
    $res = $db->db_Query( $q );
    //echo "<br /> q = ".$q." res = ".$res;
    if( !$db->result ) return false;
    $row = $db->db_FetchAssoc();
    $db1 = new DB();
    $q1="select * from `".TblModDepartmentSetSprMeta."` where `lang_id`='$this->lang_id' ";
    $res1 = $db1->db_Query( $q1 );
    //echo "<br /> q = ".$q." res = ".$res;
    if( !$db1->result ) 
        return false;
    $row1 = $db1->db_FetchAssoc();
    $row['title']=$row1['title'];
    $row['keywords']=$row1['keywords'];
    $row['description']=$row1['description'];
    return $row;         
   } // end of function GetSettings() 

   // ================================================================================================
   // Function : SetMetaData()
   // Date : 01.07.2010
   // Parms :
   // Returns : true,false / Void
   // Description : set title, description and keywords for this module or for current news or category
   //               of news
   // Programmer : Yaroslav Gyryn
   // ================================================================================================
  function SetMetaData()
   {
      $db1 = new DB();
    $q1="select * from `".TblModDepartmentSetSprMeta."` where `lang_id`='$this->lang_id' ";
    $res1 = $db1->db_Query( $q1 );
    //echo "<br /> q = ".$q." res = ".$res;
    if( !$db1->result ) return false;
    $row1 = $db1->db_FetchAssoc();
    $this->title=$row1['title'];
    $this->keywords=$row1['keywords'];
    $this->description=$row1['description'];
   //echo " META SET: id=".$this->id." cat=".$this->category;
    $q = "SELECT `name`, `title`, `keywords`, `description` 
          FROM `".TblModDepartmentTxt."`
          WHERE `".TblModDepartmentTxt."`.cod='".$this->id."'
          AND `".TblModDepartmentTxt."`.lang_id='".$this->lang_id."'
         ";
    $res = $this->db->db_Query( $q );
//        echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result; 
    if ( !$res OR !$this->db->result ) return false;
    $rows = $this->db->db_GetNumRows();
    $row=$this->db->db_FetchAssoc();
    if( $this->id ) {$title =  $row['title'];
    if( empty($title) ) $title =  $row['name'];}
    else {
    if( $this->category ) $title = $this->Spr->GetNameByCod( TblModDepartmentCat, $this->category, _LANG_ID, 1);
    }
    if( !empty($title) ) $this->title = $title.' | '.$this->title;
    else {
   // echo "<br>task=".$this->task;
    switch($this->task){
    case 'all':  $title = $this->Msg->show_text('TXT_META_TITLE_ALL');
                break;
    case 'last':  $title = $this->Msg->show_text('TXT_META_TITLE_LAST');
                break;
    case 'arch':  $title = $this->Msg->show_text('TXT_META_TITLE_ARCH');
                break;
    }
     if( !empty($title) ) $this->title = $title.' | '.$this->title;   
    }
    
  //  echo "<br> start = ".$this->start;
  //  echo "<br> display = ".$this->display;
  //  echo "<br> rows = ".$this->rows;
     $curr = round($this->start/$this->display, 0);
     $end = round($this->rows/$this->display, 0);
     
   //  echo "<br> curr = ".$curr;
   //  echo "<br> end = ".$end;
                                                    
     $page = $end-$curr;
     if($page>1) $this->title = $this->title." | ". $this->Msg->show_text('TXT_DEPARTMENT_TITLE').' '.($this->start+1)."...".($this->start+$this->display);
       
   if( $this->id ) $descr = $row['description'];
    else {
    if( $this->category ) $descr = $this->Spr->GetNameByCod( TblModDepartmentCat, $this->category, _LANG_ID, 1);
    }
    
    if( !empty($descr) ) $this->description = $descr.'. '.$this->description;
    else {
        if( !empty($title) ) $this->description = $title.'. '.$this->description;
    }

   if( $this->id ) $keywrds = $row['keywords'];
    else {
   if( $this->category ) $keywrds = $this->Spr->GetNameByCod( TblModDepartmentCat, $this->category, _LANG_ID, 1);
   }
   
   if( !empty($keywrds) ) $this->keywords = $keywrds.', '.$this->keywords;
   //else $this->keywords = $title .', '.$this->keywords; 
   
   } //end of function  SetMetaData()  


// ================================================================================================
 // Function : QuickSearch()
 // Date : 01.07.2010
 // Parms : 
 // Returns : true,false / Void
 // Description :
 // Programmer : Yaroslav Gyryn
 // ================================================================================================    
 function QuickSearch($search_keywords){
   $tmp_db = new DB();
   
   $search_keywords = stripslashes($search_keywords);
   
   $sel_table = NULL;
   $str_like = NULL;
   $filter_cr = ' OR ';

    $str_like = $this->build_str_like(TblModDepartmentTxt.'.name', $search_keywords);
    $str_like .= $filter_cr.$this->build_str_like(TblModDepartmentTxt.'.short', $search_keywords);
    //$str_like .= $filter_cr.$this->build_str_like(TblModDepartmentTxt.'.full', $search_keywords); 
    $sel_table = "`".TblModDepartment."`, `".TblModDepartmentTxt."`, `".TblModDepartmentCat."` ";
   
          /* $q = "SELECT `".TblModDepartment."`.*, 
                            `".TblModDepartmentCat."`.name AS `cat_name`, 
                            `".TblModDepartmentCat."`.translit AS cat_translit,
                            `".TblModDepartmentTxt."`.name AS `sbj`, 
                            `".TblModDepartmentTxt."`.translit, 
                            `".TblModDepartmentTxt."`.short,
                            `".TblModDepartmentTxt."`.full
              FROM `".TblModDepartment."`, `".TblModDepartmentCat."`, `".TblModDepartmentTxt."`
              WHERE `".TblModDepartment."`.id='".$art_id."'
              AND `".TblModDepartment."`.category=`".TblModDepartmentCat."`.cod
              AND `".TblModDepartmentCat."`.lang_id='".$this->lang_id."'
              AND `".TblModDepartment."`.id=`".TblModDepartmentTxt."`.cod
              AND `".TblModDepartmentTxt."`.lang_id='".$this->lang_id."'
             ";*/
             
   
   $q ="SELECT `".TblModDepartment."`.id, 
                      `".TblModDepartment."`.category, 
                      `".TblModDepartment."`.status,
                      `".TblModDepartmentTxt."`.name, 
                       `".TblModDepartment."`.position,
                       `".TblModDepartmentTxt."`.translit, 
                       `".TblModDepartmentCat."`.translit AS cat_translit
        FROM ".$sel_table."
        WHERE (".$str_like.")
        AND `".TblModDepartmentTxt."`.lang_id = '".$this->lang_id."'
        AND `".TblModDepartment."`.id = `".TblModDepartmentTxt."`.cod
        AND `".TblModDepartment."`.category=`".TblModDepartmentCat."`.cod
        AND `".TblModDepartmentCat."`.lang_id='".$this->lang_id."'
        ORDER BY `".TblModDepartment."`.id, `".TblModDepartment."`.position";

   $res = $this->db->db_Query( $q );
   //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$this->db->result;
   if ( !$res) return false;
   if( !$this->db->result ) return false;  
   $rows = $this->db->db_GetNumRows();
 // echo "<br> rows = ";
 //  print_r($rows);
   return $rows;
 } // end of function QuickSearch
 
 
// ================================================================================================
// Function : build_str_like
// Date : 01.07.2010 
//
// Parms : $find_field_name - name of the field by which we want to do search
//         $field_value - value of the field
// Returns : str_like_filter - builded string with special format;
// Description : create the string for SQL-command SELECT for search in the text field by any word
// Programmer : Yaroslav Gyryn
// ================================================================================================
function build_str_like($find_field_name, $field_value)
{
    $str_like_filter=NULL;
    // cut unnormal symbols
    $field_value=preg_replace("/[^\w\x7F-\xFF\s]/", " ", $field_value);
    // delete double spacebars
    $field_value=str_replace(" +", " ", $field_value);
    $wordmas=explode(" ", $field_value);

    for ($i=0; $i<count($wordmas); $i++){
          $wordmas[$i] = trim($wordmas[$i]);
          if (EMPTY($wordmas[$i])) continue;
          if (!EMPTY($str_like_filter)) $str_like_filter=$str_like_filter." AND ".$find_field_name." LIKE '%".$wordmas[$i]."%'";
          else $str_like_filter=$find_field_name." LIKE '%".$wordmas[$i]."%'";
    }
    if ($i>1) $str_like_filter="(".$str_like_filter.")";
    //echo '<br>$str_like_filter='.$str_like_filter;
    return $str_like_filter;
} //end offunction build_str_like()


// ================================================================================================
// Function : GetTranslitById()
// Version : 1.0.0
// Date : 01.07.2010 
// Parms :  $id    - id of the category
// Returns : true,false / Void
// Description :  return translit for category or current position
// Programmer : Yaroslav Gyryn
// ================================================================================================
function GetTranslitById($id = NULL, $lang_id = NULL)
{      
    $db = new DB();
    $q = "SELECT translit FROM `".TblModDepartmentTxt."` WHERE `cod`='".$id."'";
    if( !empty($lang_id) ) $q = $q." AND `lang_id`='".$lang_id."'";
    $res =$db->db_Query( $q );
    //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
    if( !$res OR !$db->result ) return false;
    $row = $db->db_FetchAssoc();
    //echo '<br>$row[translit]='.$row['translit'];
    return $row['translit'];
}// end of function GetTranslitById()       


// ================================================================================================
// Function : SaveTranslit()
// Date : 01.07.2010 
// Parms :  $id_cat - if of the category
//          $id - id of the current position
//          $name_ind - translit name of the position
//          $name -  name of the current position
//          $translit_old - old values of translit field 
// Returns : true,false / Void
// Description :  save translit name of current position
// Programmer : Yaroslav Gyryn
// ================================================================================================
function SaveTranslit($id_cat, $id, $name = NULL, $translit, $translit_old, $lang_id)
{
    $db = new DB();
    $Crypt = new Crypt();
    $translitNew = NULL;
    $translit[$lang_id] = $this->Form->GetRequestTxtData($translit[$lang_id], 1);
    $translit_old[$lang_id] = $this->Form->GetRequestTxtData($translit_old[$lang_id], 1);
    $name[$lang_id] = $this->Form->GetRequestTxtData($name[$lang_id], 1);
    //echo '<br/>$translit_old[$lang_id]='.$translit_old[$lang_id].' $name_ind[$lang_id]='.$name_ind[$lang_id];
    
    //if exist old translit $translit_old[$lang_id] and it = current translit $name_ind[$lang_id] then no needs to save translit. 
    //Old translit must not to change automaticaly, only manualy!
    if( (!empty($translit_old[$lang_id]) AND $translit_old[$lang_id]==$translit[$lang_id]) ) 
        return $translit[$lang_id];
    
    //generate translit only for new position of Department
    if( empty($translit_old[$lang_id]) ){
        //First check translit field and make transliteration of it 
        if( isset($translit[$lang_id]) AND !empty($translit[$lang_id])){
            $translitNew = $Crypt->GetTranslitStr(stripslashes($translit[$lang_id]));
            $translitNew = $this->GetTranslit($translitNew, $id_cat, $id, $lang_id);
        }
        //else check other field for generate translit and make transliteration of it
        elseif( isset($name[$lang_id]) AND !empty($name[$lang_id]) ) {
            $translitNew = $Crypt->GetTranslitStr(stripslashes($name[$lang_id]));
            $translitNew = $this->GetTranslit($translitNew, $id_cat, $id, $lang_id);
        }
    }
    else{
        $translitNew = stripslashes($translit[$lang_id]);
    }
    
    return $translitNew;
}

// ================================================================================================
// Function : GetTranslit()
// Date : 01.07.2010
// Parms :  $str        - string for checking
//          $id_cat     - id of the category
//          $id_prop    - id of the current position
//          $lang_id
// Returns : true,false / Void
// Description :  check the name for exist in translit
// Programmer : Yaroslav Gyryn
// ================================================================================================
function GetTranslit($str = NULL, $id_cat = NULL, $id_prop = NULL, $lang_id=NULL)
{      
    $db = new DB();
    
    $q = "SELECT 
                    `".TblModDepartmentTxt."`.translit, 
                    `".TblModDepartment."` .id as id_prop 
            FROM `".TblModDepartmentTxt."` ,`".TblModDepartment."` 
            WHERE 
                BINARY `translit` = BINARY '".$str."'
                AND
                    `".TblModDepartment."`.id = `".TblModDepartmentTxt."`.cod
                ";
    if( !empty($lang_id) ) 
            $q = $q." AND `".TblModDepartmentTxt."`.lang_id='".$lang_id."'";
    if( $id_cat!=NULL ) 
            $q = $q." AND `".TblModDepartment."`.category='".$id_cat."'";
    //-------- если проверяется конкретная позиция,а не категория -------
    $res =$db->db_Query( $q );
    //echo '<br>$q='.$q.' $res='.$res.' $db->result='.$db->result;
    if( !$res OR !$db->result ) return false;
    $rows = $db->db_GetNumRows();
    //echo '<br>$rows='.$rows;
    for($i=0;$i<$rows;$i++){
        $row = $db->db_FetchAssoc();
        //echo '<br>$id_prop='.$id_prop.' $row[id_prop]='.$row['id_prop'].' $row[translit]='.$row['translit'];
        
        // проверка конкретной позиции, если найденный транслит не пренадлежит данной позиции $id_prop, то значит уже есть такой транслит
        // у другой позиции $row['id_prop'], поэтому возвращаем его.
        if( $id_prop!=$row['id_prop'] ){
            return $row['translit'].$id_prop;
        }
    }// end for
    //echo '<br>$return='.$return;
    return $str;
}// end of function GetTranslit() 
                               

// ================================================================================================
// Function : GetRelatPropAsIndex()
// Date : 01.12.2010
//          $id    - id of current position
// Returns : true,false / Void
// Description :  Get Relat Prop As Index
// Programmer : Yaroslav Gyryn
// ================================================================================================
   function GetRelatPropAsIndex( $id = NULL )
   {
    $tmp_db = new DB();
    $q = "SELECT * FROM `".TblModDepartmentPropRelat."` WHERE (`id_prop1`='".$id."' OR `id_prop2`='".$id."') ORDER BY `move` asc";
    $res = $tmp_db->db_Query( $q );
    //echo '<br> $q='.$q.' $res='.$res.' $tmp_db->result='.$tmp_db->result;
    if ( !$res OR !$tmp_db->result ) return false;
    $rows = $tmp_db->db_GetNumRows();
    $arr_row = NULL;
    for ($i=0;$i<$rows;$i++) {
        $row = $tmp_db->db_FetchAssoc();
        $arr_row[$row['id_prop1']]=$row;
        $arr_row[$row['id_prop2']]=$row;
        //echo '<br> $row[id]='.$row['id'].' $arr_row['.$i.']='.$arr_row[$i];
    }
    //echo '<br> $arr_row='.$arr_row;   
    return $arr_row;
   } //end of function GetRelatPropAsIndex()       

   
// ================================================================================================
// Function : GetDepartmentCatInArr()
// Date : 01.12.2010
//          $level    - level 
// Returns : true,false / Void
// Description :  Get Department Cat In Arr
// Programmer : Yaroslav Gyryn
// ================================================================================================
   function GetDepartmentCatInArr($level=0)
   {
       $db = new DB();
        $q = "SELECT 
                `".TblModDepartmentCat."`.cod,
                `".TblModDepartmentCat."`.name 
              FROM 
                `".TblModDepartmentCat."`
              WHERE 
                `".TblModDepartmentCat."`.lang_id='".$this->lang_id."' 
              ORDER BY 
                `".TblModDepartmentCat."`.`move` 
              ";  
        $res = $db->db_Query( $q );
        //echo '<br>'.$q.' <br/>res='.$res.' $db->result='.$db->result;
        if( !$res )return false;
        $rows = $db->db_GetNumRows();
        //echo '<br> $rows='.$rows;
       $arr = array(); 
        for( $i = 0; $i < $rows; $i++ ){
            $row=$db->db_FetchAssoc();
            $arr[ $level ][$row['cod']]=$row;
        }
        return $arr;
   }//end of function GetDepartmentCatInArr()
      

// ================================================================================================
// Function : PrepareDepartmentForSelect()
// Date : 01.12.2010
// Returns : true,false / Void
// Description :  Prepare Department For Select
// Programmer : Yaroslav Gyryn
// ================================================================================================
   function PrepareDepartmentForSelect($level = 0, $arr_result, $spacer = NULL, $front_back = 'back', $show_sublevels = true, $show_content = false, $show_cnt_pos = false, $show_cnt_params_for_cat = false, $value=NULL, $curr_idcat = NULL, $counter=0)
   {   
       if(empty($arr_result)) $arr_result = array();
       //echo '<hr>on start $counter='.$counter.' ';
       //print_r($arr_result); 
       if( !isset($this->temp_arr_categs) OR !is_array($this->temp_arr_categs) ) {
           $this->temp_arr_categs = $this->GetDepartmentCatInArr($level);
           //echo '<hr/>';
           //print_r($this->temp_arr_categs);
       }
       
       if( !isset($this->temp_arr_categs[$level]) ) return $arr_result;
       
       //$rows = count($this->temp_arr_categs);
       //echo '<br> $rows='.$rows;
       if($show_content) $disable='disabled="disabled"';
       else $disable='';
       $i=0;
       $arr_categs = array_keys($this->temp_arr_categs[$level]);
       //echo '<hr/>';
       //print_r($arr_categs);
       $rows = count($arr_categs);
       for($i=0; $i<$rows; $i++) {
       //foreach( $this->temp_arr_categs[$level] as $k=>$v){
            $row=$this->temp_arr_categs[$level][$arr_categs[$i]];
            //echo '<br />0000000000$row=';print_r($row);
            if($curr_idcat==$row['cod'] AND empty($disable)) $disable='disabled="disabled"';
            else $disable_cat=$disable;
            
            $output_str =  $spacer.'- '.stripslashes($row['name']);
            $arr_result[$counter]['id'] = $row['cod'];
            $arr_result[$counter]['level'] = $level;
            $arr_result[$counter]['name'] = $output_str;
            $arr_result[$counter]['disable'] = $disable;
            $arr_result[$counter]['spacer'] = $spacer;
            $counter++;
       }                      
       return $arr_result;
   }// end of function PrepareDepartmentForSelect() 
   

// ================================================================================================
// Function : PreparePositionsTreeForSelect()
// Date : 01.12.2010
// Returns : true,false / Void
// Description :  Prepare Positions Tree For Select
// Programmer : Yaroslav Gyryn
// ================================================================================================
   function PreparePositionsTreeForSelect($levels = 'all', $front_back = 'back', $sort = "move", $asc_desc = "asc", $disable_idprops = NULL)
   {
       $arr = array();
       $q = "SELECT
                        `".TblModDepartment."`.id,
                        `".TblModDepartment."`.category,
                        `".TblModDepartmentTxt."`.name 
               FROM 
                        `".TblModDepartmentTxt."`, `".TblModDepartment."`
               WHERE 
                        `".TblModDepartmentTxt."`.lang_id = '".$this->lang_id."'
               AND
                        `".TblModDepartmentTxt."`.cod =  `".TblModDepartment."`.id
            ";
       if( strstr($levels, ",")) 
            $q.=" `".TblModDepartmentProp."`.`id_cat` IN ('".$levels."')";
       elseif( $levels!='all') 
            $q.=" `".TblModDepartmentProp."`.`id_cat`='".$levels."'";
            
       $q .= " ORDER BY `".TblModDepartment."`.`position` ASC ";
       
       $res = $this->db->db_Query( $q );
       //echo '<br>'.$q.' <br/>$res='.$res.' $this->db->result='.$this->db->result;  
       if( !$res OR !$this->db->result ) return false;
       $rows = $this->db->db_GetNumRows();
       
       for($i=0;$i<$rows;$i++){
           $row = $this->db->db_FetchAssoc();
           /*if(is_array($disable_idprops)){
               if(isset($disable_idprops[$row['id']])) $disable_prop='disabled="disabled"';
               else $disable_prop='';
           }
           elseif($disable_idprops==$row['id']) $disable_prop='disabled="disabled"';
           else */$disable_prop='';
            
           $arr[$row['category']][$row['id']]=$row;
           $arr[$row['category']][$row['id']]['disable'] = $disable_prop;
       }
       return $arr;
   }//end of function PreparePositionsTreeForSelect()
   
   
   
// ================================================================================================
// Function : ShowDepartmentInSelect()
// Date : 01.12.2010
// Returns : true,false / Void
// Description :  Show Department In Select
// Programmer : Yaroslav Gyryn
// ================================================================================================
   function ShowDepartmentInSelect($arr_categs, $arr_props, $default_val, $select_name = 'arr_prop[]', $value=NULL, $params='')
   {
       ?><select name="<?=$select_name;?>" class="<?=$params;?> slct0"><?
        if( $value=='' ){?><option value="" selected disabled="disabled"><?=$default_val;?></option><?}
        else {?><option value="" disabled="disabled"><?=$default_val;?></option><?}
        $rows = count($arr_categs);
        for($i=0;$i<$rows;$i++){
            $id_cat = $arr_categs[$i]['id'];
            ?><option value="<?='categ='.$id_cat;?>" <?=$arr_categs[$i]['disable'];?>><?=stripslashes($arr_categs[$i]['name']);?></option><?
            if(isset($arr_props[$id_cat])){
                $arr = array_keys($arr_props[$id_cat]);
                $rows2 = count($arr);
                for($j=0;$j<$rows2;$j++){
                    $row = $arr_props[$id_cat][$arr[$j]];
                    if($value!='' and $value == $row['id']) {
                        ?><option value="<?=$row['id'];?>"selected <?=$row['disable'];?>><?=$arr_categs[$i]['spacer'].'&nbsp;&nbsp;&nbsp;'.stripslashes($row['name']);?></option><?
                    }
                    else {
                        ?><option value="<?=$row['id'];?>"<?=$row['disable'];?>><?=$arr_categs[$i]['spacer'].'&nbsp;&nbsp;&nbsp;'.stripslashes($row['name']);?></option><?
                    }
                    
                }
            }
        }
        ?></select><?
   }//end of function ShowDepartmentInSelect()
   
   
   /* News::ShowCategoryInComboBox()
    * 
    * @param mixed $id_category
    * @param mixed $name_fld
    * @param mixed $val
    * @param integer $width
    * @param string $default_val
    * @param mixed $params
    * @return
    */
   function ShowCategoryInComboBox( $id_department, $name_fld, $val, $width=40, $default_val = '&nbsp;', $params=NULL )
   {
      if (empty($this->Crypt)) $this->Crypt = new Crypt();
      if ($width==0) $width=250;

      $tmp_db = new DB();
      $q = "SELECT 
                `".TblModDepartmentTxt."`.cod,
                `".TblModDepartmentTxt."`.name 
            FROM 
                `".TblModDepartment."`, `".TblModDepartmentTxt."`
            WHERE 
                `".TblModDepartment."`.id = `".TblModDepartmentTxt."`.cod
              AND
                `".TblModDepartment."`.status = 'a'
              AND
                `".TblModDepartmentTxt."`.lang_id = '".$this->lang_id."'
              GROUP BY `".TblModDepartment."`.position DESC
            ";
      
      $res = $tmp_db->db_Query($q);
      //echo '<br>'.$q.'<br/> $res='.$res.' $tmp_db->result='.$tmp_db->result;
      if (!$res) return false;
      $rows = $tmp_db->db_GetNumRows();

      $mas_spr[''] = $default_val;
      for($i=0; $i<$rows; $i++)
      {
           $row_spr=$tmp_db->db_FetchAssoc();
           $name = $this->Crypt->TruncateStr(strip_tags(stripslashes($row_spr['name'])),140);
           $mas_spr[$row_spr['cod']] = $name;
      }
      $this->Form->Select( $mas_spr, $name_fld, $val,  $width, $params );
   }  //end of function ShowCategoryInComboBox
   
   
      

   // ================================================================================================
   // Function : GetDoctortData()
   // Date : 01.07.2010
   // Parms :
   // Returns :      true,false / Void
   // Description :  Get List of doctors
   // Programmer :  Yaroslav Gyryn
   // ================================================================================================
   function GetDoctortData($id = NULL)
   {
        if(!$id) 
            return false; 
        $q = "SELECT `".TblModDepartmentDoctor."`.*, 
                            `".TblModDepartmentDoctorTxt."`.name, 
                            `".TblModDepartmentDoctorTxt."`.post,
                            `".TblModDepartmentDoctorTxt."`.work_time
              FROM `".TblModDepartmentDoctor."`,`".TblModDepartmentDoctorTxt."`
              WHERE
                     `".TblModDepartmentDoctor."`.id =`".TblModDepartmentDoctorTxt."`.cod
              AND `".TblModDepartmentDoctor."`.id_department ='".$id."'
              AND `".TblModDepartmentDoctorTxt."`.lang_id='".$this->lang_id."'
              AND `".TblModDepartmentDoctorTxt."`.name != ''
              AND `".TblModDepartmentDoctor."`.status ='a'
              ORDER BY  `".TblModDepartmentDoctor."`.position ASC
             ";
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' <br/>$res='.$res.' $this->db->result='.$this->db->result; 
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        return $rows;
   } //end of function GetDoctortData()   
                                     
} //--- end of class
