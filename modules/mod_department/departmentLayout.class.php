<?php
// ================================================================================================
// System : SEOCMS
// Module : DepartmentLayout.class.php
// Version : 2.0.0
// Date : 28.01.2009
// Licensed To:
// Igor  Trokhymchuk  ihoru@mail.ru
// Yaroslav Gyryn    las_zt@mail.ru
//
// Purpose : Class definition for all actions with Layout of Department on the Front-End
//
// ================================================================================================
include_once( SITE_PATH.'/modules/mod_department/department.defines.php' );

class DepartmentLayout extends Department{

    var $id = NULL;
    var $title = NULL;

    // ================================================================================================
    //    Function          : DepartmentLayout (Constructor)
    //    Version           : 1.0.0
    //    Date              : 01.07.2010
    //    Parms             : sort      / field by whith data will be sorted
    //                        display   / count of records for show
    //                        start     / first records for show
    //    Returns           : Error Indicator
    //
    //    Description       : Opens and selects a dabase
    // ================================================================================================
    function DepartmentLayout($user_id = NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL) {

        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
        ( $display  !="" ? $this->display = $display  : $this->display = 10   );
        ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
        ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );

        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;

        $this->db =  DBs::getInstance();
        $this->Form = check_init('FormDepartment', 'FrontForm', "'form_department'");
        if(empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr');
        if (empty($this->Crypt)) $this->Crypt = check_init('Crypt', 'Crypt');

        if (empty($this->Msg)) $this->Msg = new ShowMsg();
        $this->Msg->SetShowTable(TblModDepartmentSprTxt);
        //if (empty($this->Spr)) $this->Spr = new  SysSpr();
        //if (empty($this->Form)) $this->Form = new FrontForm('form_art');
        //if (empty($this->db)) $this->db = new DB();
        //if (empty($this->Crypt)) $this->Crypt = new Crypt();

        $this->settings = $this->GetSettings();
        $this->UploadImages = new UploadImage(149, null, $this->settings['img_path'],'mod_department_img');
        //$this->multi = $this->Spr->GetMulti(TblModDepartmentSprTxt);
        if(empty($this->multi)) $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);

        ( defined("USE_TAGS")       ? $this->is_tags = USE_TAGS         : $this->is_tags=0      );
        ( defined("USE_COMMENTS")   ? $this->is_comments = USE_COMMENTS : $this->is_comments=0  );
    } // End of DepartmentLayout Constructor


    // ================================================================================================
    // Function : ShowDepartmentTask()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show Department navigation by tasks
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowDepartmentTask( $task = NULL )
    {
        ?>
        <div style="margin: 13px 0px 0px 0px; float:none; min-height:90px;">
         <div style="width:320px; float:left;">
          <?=$this->multi['TXT_OFFER_TASK'];?>:
          <ul>
           <li>
          <?
          $this->fltr = " AND `status`='a'";
          $rows = $this->GetDepartmentsRows('limit');
          if($rows>0){?><a href="<?=_LINK;?>departments/last/" class="t_link"><?=$this->multi['TXT_FRONT_TITLE_LATEST'];?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?=$rows;?></span><?}
          else{?><span class="inacive_txt"><?=$this->multi['TXT_FRONT_TITLE_LATEST'];?></span><?}
          ?>
           </li>
          <?
          $this->fltr = " AND `status`='e'";
          $rows = $this->GetDepartmentsRows('limit');
          ?>
          <li>
          <?
          if($rows>0){?><a href="<?=_LINK;?>departments/arch/" class="t_link"><?=$this->multi['TXT_FRONT_TITLE_ARCH'];?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?=$rows;?></span><?}
          else{?><span class="inacive_txt"><?=$this->multi['TXT_FRONT_TITLE_ARCH'];?></span><?}
          ?>
          </li>
          <?
          $this->fltr = " AND `status`!='i'";
          $rows = $this->GetDepartmentsRows('limit');
          ?>
          <li>
          <?
          if($rows>0){?><a href="<?=_LINK;?>departments/all/" class="t_link"><?=$this->multi['TXT_ALL_OFFERS'];?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?=$rows;?></span><?}
          else{?><span class="inacive_txt"><?=$this->multi['TXT_ALL_OFFERS'];?></span><?}
          ?>
          </li>
          </ul>
         </div>
         <?$this->ShowDepartmentCat();?>
        </div>
        <?
    }


    // ================================================================================================
    // Function : DepartmentsTabCat()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show Department Category in tab List
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function DepartmentsTabCat( )
    {
        $idCat = $this->cat;
        $db = new DB();
        $q = "SELECT `".TblModDepartmentCat."`.*
              FROM `".TblModDepartmentCat."`
              WHERE `lang_id`='".$this->lang_id."'
              AND `name`!=''
              ORDER BY `move` ASC ";
        $res = $db->db_Query( $q );
        $rows = $db->db_GetNumRows();
        ?><div id="tabCat"><?
        if ($rows>0){
            ?><ul><?
            for( $i = 0; $i < $rows; $i++ ){
                $row = $db->db_FetchAssoc();
                $cod = $row['cod'];
                $name = $row['name'];
                $link =  $this->Link($row['translit'], NULL);
                $class = ($cod == $idCat )? 'active':'';
                /*echo '<br/>$class ='.$class;
                echo '<br/>$cod ='.$cod;*/
                ?><li class="<?=$class;?>"><a class="<?=$class;?>" href="<?=$link;?>"><?=$name;?></a><div class="rightTab">&nbsp;</div></li><?
            } // end for
            ?></ul><?
        }
        else {
            ?><div class="err" align="center"><?
                echo $this->multi['TXT_NO_DATA'];
            ?></div><?
        }
    ?></div><?
    }//end of function DepartmentsTabCat()

    // ================================================================================================
    // Function : ShowDepartmentCat()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show Department Category
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowDepartmentCat( $cat = NULL )
    {
        $db = new DB();
        $q = "SELECT
                    `".TblModDepartmentCat."`.*
              FROM
                        `".TblModDepartmentCat."`
              WHERE
                    `lang_id`='".$this->lang_id."'
              AND
                    `name`!=''
              ORDER BY
                    `move` ASC ";
        $res = $db->db_Query( $q );
        $rows = $db->db_GetNumRows();
        ?><div class="chapterTitle"><span><?=$this->multi['TXT_DEPARTMENT_TITLE'];?></span></div><?
        if ($rows>0){
            ?><ul class="insideMenu"><?
            for( $i = 0; $i < $rows; $i++ ){
                $row = $db->db_FetchAssoc();
                $name = $row['name'];
                $link =  $this->Link($row['translit'], NULL);
                ?><li><a href="<?=$link;?>"><?=$name;?></a></li><?
            } // end for
            ?></ul><?
        }
        else {
            ?><div class="err" align="center"><?
                echo $this->multi['TXT_NO_DATA'];
            ?></div><?
        }

    }//end of function ShowDepartmentCat()


    /**
     * DepartmentLayout::ShowDepartmentsLinks()
     * @author Yaroslav
     * @return void
     */
    function ShowDepartmentsLinks( )
    {
    $q = "
        SELECT
             `".TblModDepartment."`.id,
             `".TblModDepartment."`.category,
             `".TblModDepartmentTxt."`.name,
             `".TblModDepartmentTxt."`.translit,
             `".TblModDepartmentCat."`.translit as cat_translit,
             `".TblModDepartment."`.position
        FROM `".TblModDepartment."`, `".TblModDepartmentTxt."`, `".TblModDepartmentCat."`
        WHERE  `".TblModDepartment."`.category = `".TblModDepartmentCat."`.cod
              AND `".TblModDepartment."`.id = `".TblModDepartmentTxt."`.cod
              AND `".TblModDepartmentTxt."`.lang_id='".$this->lang_id."'
              AND `".TblModDepartmentCat."`.lang_id='".$this->lang_id."'
              AND `".TblModDepartmentTxt."`.name!=''";
        $q = $q." ORDER BY `".TblModDepartment."`.position DESC";
        //if($limit=='limit') $q = $q." LIMIT ".$this->start.",".$this->display."";
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();
        if ($rows>0){
            $arr = array();
            for( $i = 0; $i < $rows; $i++ ){
                $arr[] = $this->db->db_FetchAssoc();
            }
            ?><div class="chapterTitle"><span><?=$this->multi['TXT_DEPARTMENT_TITLE'];?></span></div><?
            ?><ul class="insideMenu"><?
            for( $i = 0; $i < $rows; $i++ ){
                $row = $arr[$i];
                $name = stripslashes($row['name']);
                $link =  $this->Link($row['cat_translit'] ,$row['translit']);
                ?><li><a href="<?=$link;?>"><?=$name;?></a></li><?
            } // end for
            ?></ul><?
        }
        else {
            ?><div class="err" align="center"><?
                echo $this->multi['TXT_NO_DATA'];
            ?></div><?
        }

    }


    /**
     * DepartmentLayout::ShowDepartmentNavigation()
     * @author Yaroslav
     * @return void
     */
    function ShowDepartmentNavigation() {
        /*$q = "SELECT `".TblModDepartmentCat."`.*
              FROM `".TblModDepartmentCat."`
              WHERE `lang_id`='".$this->lang_id."'
              AND `name`!=''
              ORDER BY `move` ASC ";*/
        $q = "
            SELECT
                 `".TblModDepartment."`.id,
                 `".TblModDepartment."`.category,
                 `".TblModDepartmentTxt."`.name,
                 `".TblModDepartmentTxt."`.translit,
                 `".TblModDepartmentCat."`.translit as cat_translit,
                 `".TblModDepartment."`.position
            FROM `".TblModDepartment."`, `".TblModDepartmentTxt."`, `".TblModDepartmentCat."`
            WHERE  `".TblModDepartment."`.category = `".TblModDepartmentCat."`.cod
                  AND `".TblModDepartment."`.id = `".TblModDepartmentTxt."`.cod
                  AND `".TblModDepartmentTxt."`.lang_id='".$this->lang_id."'
                  AND `".TblModDepartmentCat."`.lang_id='".$this->lang_id."'
                  AND `".TblModDepartmentTxt."`.name!=''";
        $q = $q." ORDER BY `".TblModDepartment."`.position DESC";
        $q = $q." LIMIT 0, 10";
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();
        if ($rows>0){
            $arr = array();
            for( $i = 0; $i < $rows; $i++ ){
                $arr[] = $this->db->db_FetchAssoc();
            }
        ?>
        <div class="captionTitle"><span><?=$this->multi['TXT_DEPARTMENT_TITLE'];?></span></div>
            <ul class="rightMenu">
            <?
            for( $i = 0; $i < $rows; $i++ ){
                $row = $arr[$i];
                $name = stripslashes($row['name']);
                $link =  $this->Link($row['cat_translit'],$row['translit']);
                ?><li><a href="<?=$link;?>"><?=$name;?></a></li><?
            } // end for
            ?>
            </ul>
            <a class="detailMain" href="<?=$this->Link()?>">Смотреть все отделения&nbsp;&rarr;</a>
        <?
        }
    }

// ================================================================================================
// Function : ShowDepartmentsByPages()
// Returns :      true,false / Void
// Description :  Show Department width img by pages
// Programmer : Yaroslav Gyryn
// Date : 06.01.2010
// ================================================================================================
function ShowDepartmentsByPages()
{
    $rows = $this->GetDepartmentsRows('limit');
    if($rows==0) {
        ?><div class="err" align="center"><?
            echo $this->multi['MSG_NO_DEPARTMENT'];
        ?></div><?
        return 0;
    }

    for( $i = 0; $i <$rows; $i++ ) {
         $value = $this->db->db_FetchAssoc();
         $name =stripslashes($value["sbj"]);
         $link_cat = $this->Link( $value['cat_translit']);
         $short = $this->Crypt->TruncateStr(strip_tags(stripslashes($value['shrt'])),550);
         $link = $this->Link( $value['cat_translit'], $value['translit']);
         ?>
          <div class="video-new">
                <div class="video-new-title"><a href="<?=$link;?>" title="<?=$name;?>"><?=$name;?></a></div>
                <div class="video-new-image" align="center">
                 <?
                 //$main_img_data = $this->GetMainImageData($value['id'], 'front');
                 //$items = $this->UploadImages->GetPictureInArray($value['id'], $this->lang_id,'size_height=185',85,1);
                 $items = $this->UploadImages->GetPictureInArrayExSize($value['id'], $this->lang_id,NULL,285,185,true,true,85,false,1,215);
                 $title =  stripslashes($value['sbj']);
                 $items_keys = array_keys($items);
                 $items_count = count($items);
                 if($items_count>0) {
                    $items_count =1; // Ограничение для видео не более 1 картинки
                    for($j=0; $j<$items_count; $j++){
                        $alt= $items[$items_keys[$j]]['name'][$this->lang_id];      // Заголовок
                        $titleImg= $items[$items_keys[$j]]['text'][$this->lang_id]; // Описание
                        $path = $items[$items_keys[$j]]['path'];                    // Путь уменьшенной копии
                        $path_org = $items[$items_keys[$j]]['path_original'];
                        ?><a href="<?=$link;?>" title="<?=$title;?>" ><img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$titleImg;?>"/></a><?
                        /*?>
                        <a href="<?=$path_org;?>" class="highslide" onclick="return hs.expand(this);">
                           <img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$title;?>">
                         </a><?*/
                    }
                 }
                 else {
                    ?><a href="<?=$link;?>" title="<?=$title;?>"><img  src="/images/design/no-image-big.jpg" width="285" height="185" alt="" /></a><?
                 }
                 ?>
            </div>
            <div class="video-short">
                <div class="short2"><?=$short;?></div>
                <a class="detail" href="<?=$link;?>"><?=$this->multi['TXT_DETAILS'];?>&nbsp;&rarr;</a>
            </div>
         </div>

    <?
    }
    if($rows>0){?>
        <div class="clear">&nbsp;</div>
        <div class="page-navi-class"><?
         $n_rows = $this->GetDepartmentsRows('nolimit');
         $value = $this->db->db_FetchAssoc();
         $link = $this->Link( $value['cat_translit']);
         //$link = $this->Link( $this->cat, NULL );
         $this->Form->WriteLinkPagesStatic( $link, $n_rows, $this->display, $this->start, $this->sort, $this->page );
    ?></div>
       <div class="clear">&nbsp;</div>
    <?
    }
} // end of function ShowDepartmentsByPages


    // ================================================================================================
    // Function : GetDepartmentsRows()
    // Date : 01.01.2010
    // Parms :        $limit / limit or nolimit rows from table
    // Returns :      true,false / Void
    // Description :  Get departments
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetDepartmentsRows($limit='limit')
    {
        $q = "
        SELECT
             `".TblModDepartment."`.id,
             `".TblModDepartment."`.category as id_category,
             `".TblModDepartmentTxt."`.name as sbj,
             `".TblModDepartmentTxt."`.short as shrt,
             `".TblModDepartmentTxt."`.translit,
             `".TblModDepartmentCat."`.name as category,
             `".TblModDepartmentCat."`.translit as cat_translit,
             `".TblModDepartment."`.position
        FROM `".TblModDepartment."`, `".TblModDepartmentTxt."`, `".TblModDepartmentCat."`
        WHERE  `".TblModDepartment."`.category = `".TblModDepartmentCat."`.cod
              AND `".TblModDepartment."`.id = `".TblModDepartmentTxt."`.cod
              AND `".TblModDepartmentTxt."`.lang_id='".$this->lang_id."'
              AND `".TblModDepartmentCat."`.lang_id='".$this->lang_id."'
              AND `".TblModDepartmentTxt."`.name!=''";
        if( $this->fltr!='' ) $q = $q.$this->fltr;

        $q = $q." ORDER BY `".TblModDepartment."`.position DESC";
        if($limit=='limit') $q = $q." LIMIT ".$this->start.",".$this->display."";

        //if($limit=='limit'){
            $res = $this->db->db_Query( $q );
            $rows = $this->db->db_GetNumRows();
       /* }
        else{
            $tmp_db = new DB();
            $res = $tmp_db->db_Query( $q );
            $rows = $tmp_db->db_GetNumRows();*/
        //}
        //echo "<br>q=".$q." res=".$res." rows=".$rows;
        return $rows;
    } // end of  GetDepartmentsRows()


    // ================================================================================================
    // Function : ShowDepartmentFull()
    // Version : 1.5.0
    // Date : 01.07.2010
    // Returns :      true,false / Void
    // Description :  Show Department Full
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowDepartmentFull()
    {
        $rows = $this->GetDepartmentData($this->id);
        if($rows==0)
            return false;
        $value = $this->db->db_FetchAssoc();
        $linkCat= $this->Link( $value['cat_translit']);

        ?>
        <div class="right"><a href="javascript:history.back()">←<?=$this->multi['TXT_FRONT_GO_BACK'];?></a></div>
         <?$name = stripslashes($value['sbj']);?>
         <div class="video-new-title"><?=$name;?></div>
         <?

        //$items = $this->UploadImages->GetPictureInArrayExSize($this->id, $this->lang_id,NULL,160,160,true,true,85,NULL,631,420);
         $items = $this->UploadImages->GetPictureInArray($this->id, $this->lang_id,'size_width=598',85);
         $items_keys = array_keys($items);
         $items_count = count($items);
         if($items_count>0) {
            $items_count=1;
             ?><div class="imageCategory"><?
            /*?>
            <div class="imageBlockDetail " align="center">
                <div id="imageLarge">
                    <a href="<?=$items[$items_keys[0]]['path_original'];?>" class="highslide" onclick="return hs.expand(this);">
                        <img src="<?=$items[$items_keys[0]]['path2']; ;?>" alt="<?=$items[$items_keys[0]]['name'][$this->lang_id];  ?>" title="<?=$items[$items_keys[0]]['text'][$this->lang_id];;?>">
                    </a>
                </div>
                <div><?*/
                    //$responce ='';
                    for($j=0; $j<$items_count; $j++) {
                        $title= $items[$items_keys[$j]]['name'][$this->lang_id];  // Заголовок
                        $alt= $items[$items_keys[$j]]['text'][$this->lang_id];  // Описание
                        if(empty($title))
                            $title = $name;
                        if(empty($alt))
                            $alt = $name;
                        $path = $items[$items_keys[$j]]['path'];                    // Путь уменьшенной копии
                        //$path2 = $items[$items_keys[$j]]['path2'];                 // Путь большой копии
                        $path_org = $items[$items_keys[$j]]['path_original'];   // Путь оригинального изображения
                        //$link="javascript:showImage('".$path2."', '".$path_org."', '".$alt."',  '".$title."')";
                        ?><div class="imageDetail">
                                <?/*<a href="<?=$link;?>"><img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$title;?>"></a>*/?>
                                <a href="<?=$path_org;?>" class="highslide" onclick="return hs.expand(this);"><img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$title;?>"/></a>
                            </div>
                                <?/*<a href="<?=$path_org;?>" class="highslide" onclick="return hs.expand(this);">
                                    <img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$title;?>">
                                 </a>
                                 <div class="highslide-caption"><?=$title;?></div>*/
                       /* $responce .= '<li>
                                  <a href="'.$path_org.'" class="highslide"  title="'.$title .'" onclick="return hs.expand(this);" ><img src="'.$path.'" alt="'.$alt.'" title="'.$title.'"></a>
                                    <div class="highslide-caption">'.$title.'</div>
                                 </li>';*/
                    }
                    //echo $responce;
                ?></div><?
         }

         $short = stripslashes($value['short']);
         if(empty($short))
            $short = $this->multi['TXT_DEPARTMENT_EMPTY'];
         ?><div><?=$short;?><br/></div>
         <?
         if( empty($this->Article) )
            $this->Article = check_init('ArticleLayout', 'ArticleLayout');

         $this->Article->showArticlesLinksForDepartment($this->id);
             /*?>
             <div class="region">
                 <?$link = $this->Link($value['cat_translit'],$value['translit'],'list');?>
                 <a href="<?=$link?>"><?=$this->multi['TXT_DOCTOR_LIST'];?></a>
             </div>
             <?*/
         //}
         /*$full_news = stripslashes($value['full_art']);
         ?><div><?=$full_news;?></div><?
         ?>
         <?*/
} // end of function ShowDepartmentFull()



// ================================================================================================
// Function : GetMap()
// Date : 01.07.2010
// Returns :      true,false / Void
// Description :  Show Department   Full
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function showModuleSiteMap() {
 $db = new DB();
 $db1 = new DB();

 $q = "SELECT *
        FROM `".TblModDepartmentCat."`
        WHERE `lang_id`='"._LANG_ID."'
        ORDER BY `cod` ASC
 ";
 //echo '$q = '.$q;
 $res = $db->db_Query( $q );
 $rows = $db->db_GetNumRows();
?>
<ul>
<?
 for( $i = 0; $i < $rows; $i++ )
 {
   $row = $db->db_FetchAssoc();
   ?><li><a href="<?=$this->Link( $row['translit']);?>"><?=$row['name'];?></a></li><?
   $q1 = "SELECT
            `".TblModDepartment."`.id,
            `".TblModDepartment."`.category,
            `".TblModDepartmentTxt."`.name,
            `".TblModDepartmentTxt."`.translit
          FROM `".TblModDepartment."` ,`".TblModDepartmentTxt."`
          WHERE
            `".TblModDepartment."`.category ='".$row['cod']."'
          AND
            `".TblModDepartment."`.id = `".TblModDepartmentTxt."`.cod
          AND
            `".TblModDepartmentTxt."`.lang_id = '".$this->lang_id."'
          AND
           `".TblModDepartment."`.status='a'
           AND
           `".TblModDepartmentTxt."`.name != ''
          ORDER BY
            `".TblModDepartment."`.position DESC
   ";
   $res1 = $db1->db_Query( $q1 );
   $rows1 = $db1->db_GetNumRows();
   //echo '$q1 = '.$q1.'<br/>$res1'.$res1;
   if( $rows1 )
   {
    echo '<ul>';
    for( $j = 0; $j < $rows1; $j++ )
    {
      $row1 = $db1->db_FetchAssoc();
      echo '<li><a href="'.$this->Link($row['translit'], $row1['translit']).'">'.stripslashes($row1['name']).'</a></li>';
    }
    echo '</ul>';
   }
 }
?>
</ul>
<?
}

// ================================================================================================
function ShowDepartmentLast($cnt=5){
   $db = new DB();
   $q = "select `".TblModDepartment."`.*,`".TblModDepartmentTxt."`name from  `".TblModDepartment."`,`".TblModDepartmentTxt."`
   where `".TblModDepartment."`.status='a'
   AND `".TblModDepartment."`.id=".TblModDepartmentTxt."`.cod
   AND `".TblModDepartmentTxt."`.lang_id='".$this->lang_id."'
   order by position desc limit ".$cnt;
   $res = $db->db_Query( $q);
   $rows = $db->db_GetNumRows();
   ?><table border="0" cellspacing="0" cellpadding="0"><?
    for( $i=0; $i<$rows; $i++ )
    {
    $row = $db->db_FetchAssoc($res);
    ?>
    <tr>
      <td class="b_marker"><img src="/images/design/block_mark.gif" /></td>
      <td><a href="<?=$this->Link($row['category'], $row['id']);?>"><?=$row['name'];?></a>
              <br><span class="time"><?=$this->ConvertDate($row['dttm'], true);?></span></td>
    </tr>
    <tr>
      <td colspan="2" class="b_spacer"></td>
    </tr>
    <?
    }
?></table><?
}  //


   // ================================================================================================
   // Function : ShowErr()
   // Date : 01.07.2010
   // Returns :      true,false / Void
   // Description :  Show errors
   // Programmer :  Yaroslav Gyryn
   // ================================================================================================
   function showErr()
   {
     if ($this->Err){
       ?>
        <table border=0 cellspacing=10 cellpadding=0 class="err" width="50%" align=center>
         <tr><td><h2><?=$this->Msg->show_text('MSG_ERR', TblSysTxt);?></h2></td></tr>
         <tr><td><?=$this->Err;?></td></tr>
        </table>
       <?
     }
   } //end of function ShowErr()

   // ================================================================================================
   // Function : ShowTextMessages
   // Version : 1.0.0
   // Date : 19.01.2006
   //
   // Parms :
   // Returns : true,false / Void
   // Description : Show the text messages
   // ================================================================================================
   // Programmer : Yaroslav Gyryn
   // Date : 19.01.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function ShowTextMessages( $text = NULL )
   {
     echo "<H3 align=center class='msg'>$text</H3>";
   } //end of function ShowTextMessages()


    // ================================================================================================
   // Function : ShowNavigation
   // Version : 1.0.0
   // Date : 19.01.2006
   //
   // Parms :
   // Returns : true,false / Void
   // Description : Show navigation of news
   // ================================================================================================
   // Programmer : Yaroslav Gyryn
   // Date : 19.01.2006
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function showArticlePath(){
     ?>
     <div class="navigation">
     <a href="/"><?=SITE_NAME?></a> »
     <? if(empty($this->id) and empty($this->category)){ ?>
     Статьи
     <?} else { ?>
     <a href="/department/">Статьи</a>
     <? } ?>
      <? if(!empty($this->category) and empty($this->id)){ ?>
      » <?=$this->Spr->GetNameByCod( TblModDepartmentCat, $this->category );?>
      <? } else {
         if(!empty($this->category)) {
         ?>
         » <a href="<?=$this->Link($this->category);?>"><?=$this->Spr->GetNameByCod( TblModDepartmentCat, $this->category );?></a>
         <? }?>
      <? }?>
       <? if(!empty($this->id)){ ?>
         » <?=strip_tags($this->Spr->GetNameByCod( TblModDepartmentTxt, $this->id));?>
       <? } ?>
     </div>
     <?
   }
   // ================================================================================================
  // Function : ShowSearchResult()
  // Date :    01.07.2010
  // Parms :   $id - poll id
  // Returns : true/false
  // Description : Show Saerch form for search in the news
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
function showSearchResult($rows)
{
    if($rows>0){
    ?><ul><?
    for($i=0; $i<$rows; $i++ )
    {
        $row = $this->db->db_FetchAssoc();
        $link = $this->Link( $row['cat_translit'], $row['translit']);
        ?><li><a href="<?=$link;?>"><?=stripslashes( $row['name']);?></a></li><?
    }
    ?>
    </ul>
    <?} else{
        $FrontendPages = new FrontendPages();
     echo $FrontendPages->Msg->show_text('SEARCH_NO_RES');
           }
}// end of function ShowSearchForm


   // ================================================================================================
   // Function : GetCategoryIdByTranslit()
   // Version : 1.0.0
   // Date : 01.07.2010
   //
   // Parms :
   // Returns :      true,false / Void
   // Description :  Show errors
   // Programmer :  Yaroslav Gyryn
   // ================================================================================================
   function GetCategoryIdByTranslit($translit=null)
   {
      $db= new DB();
      $q="select cod from `".TblModDepartmentCat."` where  BINARY `translit`= BINARY '".$translit."'";
      $res = $db->db_query( $q);
//     echo $q;
     if( !$db->result )
            return false;
     $rows = $db->db_GetNumRows();
     if($rows ==0)
        return false;

     $row = $db->db_FetchAssoc();
     return $row['cod'];

   } //end of function  GetCategoryIdByTranslit()


   // ================================================================================================
   // Function : GetCategoryTranslitByCod()
   // Version : 1.0.0
   // Date : 01.07.2010
   //
   // Parms :
   // Returns :      true,false / Void
   // Description :  Show errors
   // Programmer :  Yaroslav Gyryn
   // ================================================================================================
   function GetCategoryTranslitByCod($cod=null)
   {
      $db= new DB();
      $lang = _LANG_ID;
      $q="select translit from `".TblModDepartmentCat."` where  `cod`= '".$cod."' AND `lang_id`= '".$lang."' ";
      $res = $db->db_query( $q);
//     echo $q;
     if( !$db->result )
            return false;
     $rows = $db->db_GetNumRows();
     if($rows ==0)
        return false;

     $row = $db->db_FetchAssoc();
     return $row['translit'];

   } //end of function  GetCategoryTranslitByCod()

   // ================================================================================================
   // Function : GetPositionIdByTranslit()
   // Version : 1.0.0
   // Date : 01.07.2010
   //
   // Parms :
   // Returns :      true,false / Void
   // Description :  Show errors
   // Programmer :  Yaroslav Gyryn
   // ================================================================================================
   function GetPositionIdByTranslit($position=null, $cat = null)
   {
    $db= new DB();
    $lang_id = _LANG_ID;
    $q = "SELECT
                    `".TblModDepartment."` .id as id_prop
            FROM `".TblModDepartmentTxt."` ,`".TblModDepartment."`
            WHERE
                BINARY `translit` = BINARY '".$position."'
                AND
                    `".TblModDepartment."`.id = `".TblModDepartmentTxt."`.cod
                ";
    if( !empty($lang_id) )
            $q = $q." AND `".TblModDepartmentTxt."`.lang_id='".$lang_id."'";
    if( $cat!=NULL )
            $q = $q." AND `".TblModDepartment."`.category='".$cat."'";

      $res = $db->db_query( $q);
//     echo $q;
     if( !$db->result )
            return false;
     $rows = $db->db_GetNumRows();
     if($rows ==0)
        return false;

     $row = $db->db_FetchAssoc();
     return $row['id_prop'];

   } //end of function  GetPositionIdByTranslit()
// ================================================================================================
   // Function : GetIdTranslitByCod()
   // Version : 1.0.0
   // Date : 01.07.2010
   //
   // Parms :
   // Returns :      true,false / Void
   // Description :  Show errors
   // Programmer :  Yaroslav Gyryn
   // ================================================================================================
   function GetIdTranslitByCod($position=null, $cat = null)
   {
    $db= new DB();
    $lang_id = _LANG_ID;
    $q = "SELECT
                    `".TblModDepartmentTxt."` .translit
            FROM `".TblModDepartmentTxt."` ,`".TblModDepartment."`
            WHERE
                BINARY `translit` = BINARY '".$position."'
                AND
                    `".TblModDepartment."`.id = `".TblModDepartmentTxt."`.cod
                ";
    if( !empty($lang_id) )
            $q = $q." AND `".TblModDepartmentTxt."`.lang_id='".$lang_id."'";
    if( $id_cat!=NULL )
            $q = $q." AND `".TblModDepartment."`.category='".$cat."'";

      $res = $db->db_query( $q);
//     echo $q;
     if( !$db->result )
            return false;
     $rows = $db->db_GetNumRows();
     if($rows ==0)
        return false;

     $row = $db->db_FetchAssoc();
     return $row['translit'];

   } //end of function  GetIdTranslitByCod()


     // ================================================================================================
    // Function : ShowDepartmentLink()
    // Version : 1.0.0
    // Date : 01.07.2010
    // Parms :
    // Returns : true,false / Void
    // Description : Show Link to Department Chapter
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowDepartmentLink()
    {
       ?><div class="leftBlockHead"><?=$this->multi['TXT_DEPARTMENT_TITLE'];?></div>
         <div class="departmentBlock">
            <?$link = _LINK.'department/2010/';
                $items = $this->UploadImages->GetFirstRandomPictureInArray($this->lang_id, 'size_width= 223', null);
                $items_keys = array_keys($items);
                $items_count = count($items);
                if($items_count>0) {
                        /*$alt= $items[$items_keys]['name'][$this->lang_id];  // Заголовок
                        $title= $items[$items_keys]['text'][$this->lang_id];  // Описание */
                        $path = $items[$items_keys[0]]['path'];                    // Путь уменьшенной копии
                        //$path_org = $items[$items_keys['path_original'];   // Путь оригинального изображения
                        ?><a href="<?=$link;?>" title="<?=$this->multi['TXT_DEPARTMENT_TITLE'];?>" alt="<?=$this->multi['TXT_DEPARTMENT_TITLE'];?>"><img src="<?=$path;?>" alt="<?=$this->multi['TXT_DEPARTMENT_TITLE'];?>" title="<?=$this->multi['TXT_DEPARTMENT_TITLE'];;?>"></a><?
              }
            /*?>
            <a href="<?=$link?>" title="<?=$this->multi['TXT_DEPARTMENT_TITLE'];?>"><img src="/images/design/videoSmall.jpg"></a>*/?>
         </div><?
     }

    // ================================================================================================
    // Function : ShowPath()
    // Date : 23.11.2010
    // Returns : true,false / Void
    // Description : Show Full Path
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
   function ShowPath($class=null) {
       /*if(empty($class))
            $FrontendPages = new FrontendPages();
       else*/
       $devider = ' / ';
       $FrontendPages  = &$class;
       $this->page_txt = $FrontendPages->GetPageData($FrontendPages->page, $FrontendPages->lang_id);
       $departments = stripslashes($this->page_txt['pname']);
       $home = $this->Msg->show_text('TXT_FRONT_HOME_PAGE', TblModPagesSprTxt);
       $str='<a href="'._LINK.'">'.$home.'</a> '.$devider;
       switch( $this->task ){
            case 'cat':
                $str .= '<a href="'.$this->Link().'">'.$departments.'</a>'.$devider.$this->catName;
                break;

            case 'position':
                $str .= '<a href="'.$this->Link().'">'.$departments.'</a>'.$devider.
                          '<a href="'.$this->Link($this->catTranslit).'">'.$this->catName.'</a>'.$devider.$this->positionName;
                break;

            case 'list':
                $str .= '<a href="'.$this->Link().'">'.$departments.'</a>'.$devider.
                          '<a href="'.$this->Link($this->catTranslit).'">'.$this->catName.'</a>'.$devider.
                          '<a href="'.$this->Link($this->catTranslit, $this->positionTranslit).'">'.$this->positionName.'</a>'.$devider.$this->multi['TXT_DOCTOR_LIST'];
                break;

            default:
                $str .= $departments;
       }
       return $str;
   }

// ================================================================================================
// Function : ShowDoctorsList()
// Date : 29.11.2010
// Returns : true,false / Void
// Description : Show Doctors List
// Programmer : Yaroslav Gyryn
// ================================================================================================
function ShowDoctorsList() {

        $rows = $this->GetDoctortData($this->id);

        ?><div id="content">
             <h2><?=$this->multi['TXT_DOCTOR_LIST'];?></h2><?
            if($rows==0)  {
                echo $this->multi['MSG_NO_DOCTORS'];
            }
            for($i=0; $i<$rows; $i++) {
                $row = $this->db->db_FetchAssoc();
                $email = stripslashes($row['email']);
                $name = stripslashes($row['name']);
                $post = stripslashes($row['post']);
                $work_time = stripslashes($row['work_time']);
                $work_time = str_replace("\n", "<br/>", $work_time);

                ?><div class="doctor">
                <div class="doctorName"><?=$name;?></div>
                 <?if(!empty($post)) {
                     echo $this->multi['FLD_POST'];?>:  <b><?=$post?></b><br/>
                 <?}?>

                 <?if(!empty($work_time)) {
                     echo $this->multi['_FLD_WORK_TIME'];?>: <br/> <b><?=$work_time?></b><br/>
                 <?}?>
                   </div>
                <?
            }
         ?></div><?
}


// ================================================================================================
// Function : DepartmentsCatList()
// Date : 29.11.2010
// Returns : true,false / Void
// Description : Show Full Departments Cat List
// Programmer : Yaroslav Gyryn
// ================================================================================================
function DepartmentsCatList()
{
    $db_tmp=new DB();
    $q = "SELECT
                     `".TblModDepartmentCat."`.*
              FROM
                     `".TblModDepartmentCat."`
              WHERE
                     `lang_id`='".$this->lang_id."'
              AND
                     `name`!=''
              ORDER BY
                      `move` ASC ";

    $res = $this->db->db_Query($q);
    $rows = $this->db->db_GetNumRows();
    //echo '4row='.$rows.'<br/>'.$q ;
    ?><div class="newsCatBlock"><?
        for($i=0; $i<$rows; $i++)
        {
            $row = $this->db->db_FetchAssoc();
            $name = $row['name'];
            $link =  $this->Link($row['translit'], NULL);
            ?><div class="cat-column">
                <a <?=(($this->cat==$row['cod'])?'class="newscat_link_curr"':'class="newscat_link"')?>  href="<?=$link;?>"><?=$name;?></a><?
                ?><ul class="cat-sub"><?
                $q = "SELECT
                            `".TblModDepartmentTxt."`.cod,
                            `".TblModDepartmentTxt."`.name,
                            `".TblModDepartmentTxt."`.translit
                      FROM
                            `".TblModDepartmentTxt."`,
                            `".TblModDepartment."`
                      WHERE
                            `".TblModDepartmentTxt."`.lang_id = '".$this->lang_id."'
                      AND
                            `".TblModDepartment."`.category = '".$row['cod']."'
                      AND
                            `".TblModDepartmentTxt."`.cod = `".TblModDepartment."`.id
                      ORDER BY
                            `name` ASC ";
                $resCat = $db_tmp->db_Query($q);
                $rowsCat = $db_tmp->db_GetNumRows();
                //echo  '$q='.$q.'$resCat='.$resCat.'$rowsCat='.$rowsCat;
                for($j=0; $j<$rowsCat; $j++)
                {
                    $rowCat = $db_tmp->db_FetchAssoc();
                    $link = $this->Link( $row['translit'], $rowCat['translit'],'list');
                    ?><li><a <?=(($this->id==$rowCat['cod'])?'class="curr"':'')?> href="<?=$link;?>"><?=$rowCat['name']?></a></li><?
                }
            ?>
            </div>
            <?
        }
        ?>
        <div class="clear"></div>
    </div>
    <?
}

    /**
     * DepartmentLayout::ShowDepartmentLinkForArticles()
     * @author Yaroslav
     * @param mixed $id
     * @return
     */
    function ShowDepartmentLinkForArticles ($id) {
        if(!$id) return false;
        $q = "SELECT `".TblModDepartment."`.*,
                    `".TblModDepartmentCat."`.translit AS cat_translit,
                    `".TblModDepartmentTxt."`.name AS `sbj`,
                    `".TblModDepartmentTxt."`.translit
              FROM `".TblModDepartment."`, `".TblModDepartmentCat."`, `".TblModDepartmentTxt."`
              WHERE `".TblModDepartment."`.id='".$id."'
              AND `".TblModDepartment."`.category=`".TblModDepartmentCat."`.cod
              AND `".TblModDepartmentCat."`.lang_id='".$this->lang_id."'
              AND `".TblModDepartment."`.id=`".TblModDepartmentTxt."`.cod
              AND `".TblModDepartmentTxt."`.lang_id='".$this->lang_id."'
        ";
        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        if($rows >0) {
             $value = $this->db->db_FetchAssoc();
             $name = stripslashes($value["sbj"]);
             $link = $this->Link( $value['cat_translit'], $value['translit']);
             ?><a href="<?=$link;?>"><?=$name;?></a><?
         }
    }

   } //end of class departmentLayout
?>
