<?php
// ================================================================================================
// System : SEOCMS
// Module : VideoLayout.class.php
// Version : 2.0.0
// Date : 28.01.2009
// Licensed To:
// Igor  Trokhymchuk  ihoru@mail.ru
// Yaroslav Gyryn    las_zt@mail.ru
//
// Purpose : Class definition for all actions with Layout of Video on the Front-End
//
// ================================================================================================
include_once( SITE_PATH.'/modules/mod_video/video.defines.php' );

class VideoLayout extends Video{

    var $id = NULL;
    var $title = NULL;
    var $is_tags = NULL;

    // ================================================================================================
    //    Function          : VideoLayout (Constructor)
    //    Date              : 01.07.2010
    //    Parms             : sort      / field by whith data will be sorted
    //                        display   / count of records for show
    //                        start     / first records for show
    //    Returns           : Error Indicator
    //    Description       : Opens and selects a dabase
    // ================================================================================================
    function VideoLayout($user_id = NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL) {

        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = 83 );
        ( $display  !="" ? $this->display = $display  : $this->display = 10   );
        ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
        ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );

        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;


        if (empty($this->db)) $this->db =  DBs::getInstance();
        if (empty($this->Form)) $this->Form = check_init('FormVideo', 'FrontForm', "'form_video'");
        if(empty($this->multi)) $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
        if(empty($this->Spr)) $this->Spr = check_init('FrontSpr', 'FrontSpr');
        if (empty($this->Crypt)) $this->Crypt = check_init('Crypt', 'Crypt');

        //if (empty($this->Msg))  $this->Msg = Singleton::getInstance('ShowMsg');
        //$this->Msg->SetShowTable(TblModVideoSprTxt);
        //if (empty($this->Spr))  $this->Spr = Singleton::getInstance('FrontSpr');
        //if (empty($this->Form))  $this->Form = Singleton::getInstance('FrontForm','form_video');
        //if (empty($this->db))  $this->db = Singleton::getInstance('DB');
        //if (empty($this->Crypt)) $this->Crypt = Singleton::getInstance('Crypt');

        if(empty($this->settings)) $this->settings = $this->GetSettings();
        if(empty($this->Banner)) $this->Banner = Singleton::getInstance('Banner');
        $this->UploadVideo = new UploadVideo(156, null, $this->settings['img_path'],'mod_video_file_video');
        $this->UploadImages = new UploadImage(156, null, $this->settings['img_path'],'mod_video_file_img');

        //$this->multi = $this->Spr->GetMulti(TblModVideoSprTxt);

        ( defined("USE_TAGS")       ? $this->is_tags = USE_TAGS         : $this->is_tags=0      );
        ( defined("USE_COMMENTS")   ? $this->is_comments = USE_COMMENTS : $this->is_comments=0  );
    } // End of VideoLayout Constructor


    // ================================================================================================
    // Function : ShowVideoTask()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show Video navigation by tasks
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowVideoTask( $task = NULL )
    {
        ?>
        <div style="margin: 13px 0px 0px 0px; float:none; min-height:90px;">
         <div style="width:320px; float:left;">
          <?=$this->multi['TXT_OFFER_TASK'];?>:
          <ul>
           <li>
          <?
          $this->fltr = " AND `status`='a'";
          $rows = $this->GetVideosRows('limit');
          if($rows>0){?><a href="<?=_LINK;?>videos/last/" class="t_link"><?=$this->multi['TXT_FRONT_TITLE_LATEST'];?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?=$rows;?></span><?}
          else{?><span class="inacive_txt"><?=$this->multi['TXT_FRONT_TITLE_LATEST'];?></span><?}
          ?>
           </li>
          <?
          $this->fltr = " AND `status`='e'";
          $rows = $this->GetVideosRows('limit');
          ?>
          <li>
          <?
          if($rows>0){?><a href="<?=_LINK;?>videos/arch/" class="t_link"><?=$this->multi['TXT_FRONT_TITLE_ARCH'];?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?=$rows;?></span><?}
          else{?><span class="inacive_txt"><?=$this->multi['TXT_FRONT_TITLE_ARCH'];?></span><?}
          ?>
          </li>
          <?
          $this->fltr = " AND `status`!='i'";
          $rows = $this->GetVideosRows('limit');
          ?>
          <li>
          <?
          if($rows>0){?><a href="<?=_LINK;?>videos/all/" class="t_link"><?=$this->multi['TXT_ALL_OFFERS'];?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?=$rows;?></span><?}
          else{?><span class="inacive_txt"><?=$this->multi['TXT_ALL_OFFERS'];?></span><?}
          ?>
          </li>
          </ul>
         </div>
         <?
         $this->ShowVideoCat();
         ?>
        </div>
        <?
    }

    // ================================================================================================
    // Function : ShowVideoCat()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show Video Category
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowVideoCat( $cat = NULL )
    {
        $q = "SELECT `".TblModVideoCat."`.*
              FROM `".TblModVideoCat."`
              WHERE `lang_id`='".$this->lang_id."'
              AND `name`!=''
              ORDER BY `move` ASC ";
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();
        if ($rows>0){
            ?><div class="catalogLevels">
            <ul><?
            for( $i = 0; $i < $rows; $i++ ){
                $row = $this->db->db_FetchAssoc();
                $name = $row['name'];
                $link =  $this->Link($row['translit'], NULL);
                ?><li><a href="<?=$link;?>"><?=$name;?></a></li><?
            } // end for
            ?>
            </ul>
            </div>
            <?
        }
        else {
            ?><div class="err" align="center"><?
                echo $this->multi['TXT_NO_DATA'];
            ?></div><?
        }
    }//end of function ShowVideoCat()



    // ================================================================================================
    // Function : GetVideosForTags()
    // Date : 23.05.2011
    // Returns :      true,false / Void
    // Description :  Get Videos For Tags
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetVideosForTags( $idNews=null, $idModule = 156)
    {
        if($idNews== null)
            return;

    $q = "
        SELECT
             `".TblModVideo."`.id,
             `".TblModVideo."`.dttm as start_date,
             `".TblModVideo."`.category as id_category,
             `".TblModVideoTxt."`.name,
             `".TblModVideoTxt."`.short as shrt,
             `".TblModVideoTxt."`.translit,
             `".TblModVideoCat."`.name as category,
             `".TblModVideoCat."`.translit as cat_translit,
             `".TblModVideo."`.position
        FROM `".TblModVideo."`, `".TblModVideoTxt."`, `".TblModVideoCat."`
        WHERE  `".TblModVideo."`.category = `".TblModVideoCat."`.cod
              AND `".TblModVideo."`.id = `".TblModVideoTxt."`.cod
              AND `".TblModVideoTxt."`.lang_id='".$this->lang_id."'
              AND `".TblModVideoCat."`.lang_id='".$this->lang_id."'
              AND `".TblModVideoTxt."`.name!=''
              AND  `".TblModVideo."`.id  IN (".$idNews.")
              ";
        if( $this->fltr!='' ) $q = $q.$this->fltr;
        $q = $q." ORDER BY `".TblModVideo."`.id DESC LIMIT ".$this->start.",".$this->display."";

        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();
        //echo "<br>".$q."<br/> res=".$res." rows=".$rows;

        if($rows==0)
             return;

         $array = array();
         for( $i = 0; $i <$rows; $i++ ){
             $row = $this->db->db_FetchAssoc();
             $array[$row['id']] = $row;
             $array[$row['id']]['module'] = $idModule;
         }

         return $array;
    }


    // ================================================================================================
    // Function : ShowVideosByPages()
    // Parms :
    // Returns :  true,false / Void
    // Description :  Show Video width img by pages
    // Programmer : Yaroslav Gyryn
    // Date : 06.05.2011
    // ================================================================================================
    function ShowVideosByPages()
    {
        ?><script type="text/javascript" language="javascript">
        $(document).ready( function(){
            $(".videoData embed").attr({width:317, height:217});
            $(".videoData object").attr({width:317, height:217});
            $('.videoData iframe').attr({width:317, height:217});
        });
        </script><?
         $rows = $this->GetVideosRows('limit');
         $rows = $this->db->db_GetNumRows();
         $array = array();
         for( $i = 0; $i <$rows; $i++ ){
            $array[] = $this->db->db_FetchAssoc();
         }
         if($rows==0){
             ?><div class="err"><?=$this->multi['MSG_NO_VIDEO'];?></div><?
             return;
         }
         for( $i = 0; $i <$rows; $i++ ){
             $value = $array[$i];
             $name = stripslashes($value['name']);
             $date = $this->ConvertDate($value['start_date']);
             $short = $this->Crypt->TruncateStr(strip_tags(stripslashes($value['shrt'])),280);
             $full = stripslashes($value['full']); // Код видео с YouTube
             $link_cat = $this->Link( $value['cat_translit']);
             $link = $this->Link( $value['cat_translit'], $value['translit']);
             ?>
             <div class="video-new">
                <div class="video-new-image">
                <?
                     $title = $name;
                     //$items = $this->UploadImages->GetPictureInArray($value['id'], $this->lang_id,'size_width=315',85,1);
                     $items = $this->UploadImages->GetPictureInArrayExSize($value['id'], $this->lang_id,NULL,310,210,true,true,85,true,1,210);
                     $items_keys = array_keys($items);
                     $items_count = count($items);
                     if($items_count>0) {
                        $items_count =1; // Ограничение для видео не более 1 картинки
                        for($j=0; $j<$items_count; $j++){
                            $alt= $items[$items_keys[$j]]['name'][$this->lang_id];      // Заголовок
                            $titleImg= $items[$items_keys[$j]]['text'][$this->lang_id]; // Описание
                            $path = $items[$items_keys[$j]]['path'];                    // Путь уменьшенной копии
                            //$path2 = $items[$items_keys[$j]]['path2'];                 // Путь большой копии
                            //$path_org = $items[$items_keys[$j]]['path_original'];   // Путь оригинального изображения
                            ?><a class="image" href="<?=$link;?>" title="<?=$title;?>"><img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$titleImg;?>" /></a><?
                        }
                     }
                     elseif (!empty($full)) {
                            ?><div class="videoData"><?=$full;?></div><?
                     }
                     else {
                        ?><a href="<?=$link;?>" title="<?=$title;?>"><img src="/images/design/videoPreview.gif"  width="310" height="210" alt="" /></a><?
                     }
                    ?>

                    </div>
                    <div class="video-short">
                        <div class="dateArticles"><?=$date;?></div>
                        <div class="video-new-title"><a href="<?=$link;?>" title="<?=$name;?>"><?=$name;?></a></div>
                        <div class="short"><?=$short;?></div>
                        <a class="btnWatch" href="<?=$link;?>"><?=$this->multi['TXT_WATCH'];?></a>
                    </div>
                </div>
               <?
         }
         if($rows>0){
         ?>
         <div class="clear">&nbsp;</div>
         <div class="page-navi-class"><?
             $n_rows = $this->GetVideosRows('nolimit');
             $link = $this->Link(null, NULL );
             $this->Form->WriteLinkPagesStatic( $link, $n_rows, $this->display, $this->start, $this->sort, $this->page );
         ?></div><?
         }
    }

    // ================================================================================================
    // Function : GetVideosRows()
    // Date : 01.01.2010
    // Parms :        $limit / limit or nolimit rows from table
    // Returns :      true,false / Void
    // Description :  Get videos
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetVideosRows($limit='limit')
    {
        $q = "
        SELECT
             `".TblModVideo."`.id,
             `".TblModVideo."`.dttm as start_date,
             `".TblModVideo."`.category as id_category,
             `".TblModVideoTxt."`.name,
             `".TblModVideoTxt."`.short as shrt,
             `".TblModVideoTxt."`.full,
             `".TblModVideoTxt."`.translit,
             `".TblModVideoCat."`.name as category,
             `".TblModVideoCat."`.translit as cat_translit,
             `".TblModVideo."`.position
        FROM `".TblModVideo."`, `".TblModVideoTxt."`, `".TblModVideoCat."`
        WHERE
                     `".TblModVideo."`.category = `".TblModVideoCat."`.cod
              AND `".TblModVideo."`.id = `".TblModVideoTxt."`.cod
              AND `".TblModVideoTxt."`.lang_id='".$this->lang_id."'
              AND `".TblModVideoCat."`.lang_id='".$this->lang_id."'
              AND `".TblModVideoTxt."`.name!=''";
        if( $this->fltr!='' ) $q = $q.$this->fltr;

        $q = $q." ORDER BY `".TblModVideo."`.position DESC";
        if($limit=='limit')
            $q = $q." LIMIT ".$this->start.",".$this->display."";

        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();
        //echo "<br>q=".$q." res=".$res." rows=".$rows;
        return $rows;
    } // end of  GetVideosRows()

    // ================================================================================================
    // Function : ShowVideoFull()
    // Date : 01.07.2011
    // Returns :      true,false / Void
    // Description :  Show Video Full
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowVideoFull()
    {
    $rows = $this->GetVideoData($this->id);
    if($rows==0)
        return false;
    ?><script type="text/javascript" language="javascript">
    $(document).ready( function(){
        $(".outputVideo embed").attr({width:650, height:445});
        $(".outputVideo object").attr({width:650, height:445});
        $('.outputVideo iframe').attr({width:650, height:445});
    });
    </script><?

    $value = $this->db->db_FetchAssoc();
    $name = stripslashes($value['sbj']);
    $title = $name;
    //$linkCat= $this->Link( $value['cat_translit']);
    $short =  stripslashes($value['short']);
    $full = stripslashes($value['full']);
    ?>
        <div class="backContainer">
            <a class="btnBack right" href="javascript:window.history.go(-1);">← <?=$this->multi['MOD_NEWS_BACK'];?></a>
        </div>
        <div class="output-video">
        <?
        $array = $this->UploadVideo->GetListOfVideoFrontend($this->id, $this->lang_id);
        if(count($array)>0) {
           $path = $this->UploadVideo->path.'/'.$value['id'];
           $items = $array;
           $text = $this->UploadVideo->multi_lang;
           $lang = $this->lang_id;
           $items_keys = array_keys($items);
           $items_count = count($items);
           for($i=0; $i<$items_count; $i++) {
                $filename = $items[$items_keys[$i]]['path'];
                $fullpath ='/'.$path.'/'.$filename;

                if (!empty($items[$items_keys[$i]]['name'][$lang]))
                   $name = stripslashes($items[$items_keys[$i]]['name'][$lang]);
                   // $name = $items[$items_keys[$i]]['name'][$lang];
                else
                    $name = $filename;
                $title= $items[$items_keys[$i]]['text'][$lang]; // Описание
                ?>
                <a href="<?=$fullpath?>" style="display:block; width:650px; height:480px;" id="playerhref"></a>
                <script type="text/javascript">
                    flowplayer("playerhref", "/modules/mod_video/player/flowplayer-3.2.7.swf");
                </script>
                <?
           }
        }
        elseif (!empty($full))
            echo $full;
         ?>
         </div>
         <div class="dateArticles"><?=$this->ConvertDate($value['dttm']);?></div>
         <h2 class="gallery"><?=$name;?></h2>
         <div class="output-video"><?=$short;?></div><?

         if ($this->is_tags == 1) {
            if (empty($this->Tags))
                $this->Tags = check_init('FrontTags', 'FrontTags');
            $this->Tags->ShowUsingTags($this->module, $this->id);
        }

        if ($this->is_comments == 1) {
            if (!isset($this->Comments))
                $this->Comments = new FrontComments($this->module, $this->id);
            $this->Comments->ShowCommentsByModuleAndItem();
            $this->Comments->ShowCommentsCountLink();
        }
} // end of function ShowVideoFull()


    // ================================================================================================
    // Function : VideoCatLast()
    // Date :    02.03.2011
    // Returns : true/false
    // Description : Show Last video for $cat
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function VideoCatLast( $id_cat=3, $limit=6, $caption = null){
        $arr= $this->GetVideoCatLast( $id_cat, $limit);
        if(is_array($arr)) {
            $rows = count($arr);
            if(!$rows)
                return;
            if(!$caption )
                $caption = $this->multi['TXT_FRONT_TITLE_LATEST'];
         ?>
         <div class="last-category-video">
         <div class="news_colum1_1_title"><?=$caption?></div>
         <?
            for( $i=0; $i<$rows; $i++ )
            {
              $row = $arr[$i];
              $name = strip_tags(stripslashes($row['name']));
              //$short = $this->Crypt->TruncateStr(strip_tags(stripslashes($row['shrt'])),200);
              //$short = strip_tags(stripslashes($row['shrt']));
              /*$link = $this->Link($row['category'], $row['id']);
              $link_cat = $this->Link( $value['cat_translit']);*/
              $link = $this->Link( $row['cat_translit'], $row['translit']);

              ?>
              <div class="last-videos left">
                  <div class="time left"><?=$this->ConvertDate($row['dttm'], true);?></div>
                  <a class="name left" href="<?=$link;?>"><?=$name;?></a>
              </div>
              <?
            }
            //$linkCat = $this->Link($id_cat);
            /*?><a class="allNews" href="<?=$linkCat?>">Всі новини</a><?*/
            ?>
            <div class="clear">&nbsp;</div>
         </div><?
         }
    } // end of function VideoCatLast()

    // ================================================================================================
    // Function : VideoLast()
    // Date :    02.03.2011
    // Returns : true/false
    // Description : Show Last video for $cat
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function VideoLast( ){
        $row = $this->GetVideoLast();
        if(!is_array($row))
            return;
        ?><script type="text/javascript" language="javascript">
        $(document).ready( function(){
            $(".videoLast embed").attr({width:300, height:225});
            $(".videoLast object").attr({width:300, height:225});
            $('.videoLast iframe').attr({width:300, height:225});
        });
        </script><?

        $name = strip_tags(stripslashes($row['name']));
        $short = $this->Crypt->TruncateStr(strip_tags(stripslashes($row['short'])),300);
        $full  = stripslashes($row['full']);
        $link = $this->Link( $row['cat_translit'], $row['translit']);
        $title = $name;
        ?>
         <div class="captionChapter"><span><?=$this->multi['TXT_VIDEO_TITLE'];?></span><span class="icoVideo">&nbsp;</span></div>
         <div class="video-last" title="<?=$title;?>">
                <?
                /*<div class="vid_fot_title"><a href="<?=$link?>"  title="<?=$name;?>" ><?=$name;?></a></div>*/
                 $items = $this->UploadImages->GetPictureInArrayExSize($row['id'], $this->lang_id,NULL,273,164,true,true,85,NULL,273,164);
                 $items_keys = array_keys($items);
                 $items_count = count($items);
                 if($items_count>0) {
                    $items_count =1; // Ограничение для видео не более 1 картинки
                    for($j=0; $j<$items_count; $j++){
                        $alt= $items[$items_keys[$j]]['name'][$this->lang_id];      // Заголовок
                        $titleImg= $items[$items_keys[$j]]['text'][$this->lang_id]; // Описание
                        $path = $items[$items_keys[$j]]['path'];                    // Путь уменьшенной копии
                        //$path2 = $items[$items_keys[$j]]['path2'];                 // Путь большой копии
                        //$path_org = $items[$items_keys[$j]]['path_original'];   // Путь оригинального изображения
                        ?><a class="image" href="<?=$link;?>" title="<?=$title;?>"><img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$titleImg;?>"/></a><?
                    }
                 }
                 elseif(!empty($full))
                    echo $full;
                 else {
                    ?><a href="<?=$link;?>" title="<?=$title;?>"><img src="/images/design/videoPreview.gif" width="300" height="225" alt="" /></a><?
                 }
                /*?>
                <div class="vid_fot_desc"><?=$short;?></div>
                <div class="vid_fot_footer"><a href="/video/">Все відео</a></div>*/?>
            </div>
         <?
    } // end of function VideoLast()


// ================================================================================================
// Function : GetMap()
// Date : 01.07.2010
// Parms :
// Returns :      true,false / Void
// Description :  Show Video   Full
// Programmer :  Yaroslav Gyryn
// ================================================================================================
function showModuleSiteMap() {
 $db1 = new DB();

 $q = "SELECT *
        FROM `".TblModVideoCat."`
        WHERE `lang_id`='"._LANG_ID."'
        ORDER BY `cod` ASC
 ";
 //echo '$q = '.$q;
 $res = $this->db->db_Query( $q );
 $rows = $this->db->db_GetNumRows();
?>
<ul>
<?
 for( $i = 0; $i < $rows; $i++ )
 {
   $row = $this->db->db_FetchAssoc();
   ?><li><a href="<?=$this->Link( $row['translit']);?>"><?=$row['name'];?></a></li><?
   $q1 = "SELECT
            `".TblModVideo."`.id,
            `".TblModVideo."`.category,
            `".TblModVideoTxt."`.name,
            `".TblModVideoTxt."`.translit
          FROM `".TblModVideo."` ,`".TblModVideoTxt."`
          WHERE
            `".TblModVideo."`.category ='".$row['cod']."'
          AND
            `".TblModVideo."`.id = `".TblModVideoTxt."`.cod
          AND
            `".TblModVideoTxt."`.lang_id = '".$this->lang_id."'
          AND
           `".TblModVideo."`.status='a'
           AND
           `".TblModVideoTxt."`.name != ''
          ORDER BY
            `".TblModVideo."`.dttm DESC
   ";
   $res1 = $db1->db_Query( $q1 );
   $rows1 = $db1->db_GetNumRows();
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
function ShowVideoLast($cnt=5){
   $q = "select `".TblModVideo."`.*,`".TblModVideoTxt."`name from  `".TblModVideo."`,`".TblModVideoTxt."`
   where `".TblModVideo."`.status='a'
   AND `".TblModVideo."`.id=".TblModVideoTxt."`.cod
   AND `".TblModVideoTxt."`.lang_id='".$this->lang_id."'
   order by position desc limit ".$cnt;
   $res = $this->db->db_Query( $q);
   $rows = $this->db->db_GetNumRows();
   ?><table border="0" cellspacing="0" cellpadding="0"><?
    for( $i=0; $i<$rows; $i++ )
    {
    $row = $this->db->db_FetchAssoc($res);
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
   // Version : 1.0.0
   // Date : 01.07.2010
   //
   // Parms :
   // Returns :      true,false / Void
   // Description :  Show errors
   // ================================================================================================
   // Programmer :  Yaroslav Gyryn
   // Date : 01.07.2010
   // Reason for change : Creation
   // Change Request Nbr:
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
   } //end of fuinction ShowErr()

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
     <a href="/video/">Статьи</a>
     <? } ?>
      <? if(!empty($this->category) and empty($this->id)){ ?>
      » <?=$this->Spr->GetNameByCod( TblModVideoCat, $this->category );?>
      <? } else {
         if(!empty($this->category)) {
         ?>
         » <a href="<?=$this->Link($this->category);?>"><?=$this->Spr->GetNameByCod( TblModVideoCat, $this->category );?></a>
         <? }?>
      <? }?>
       <? if(!empty($this->id)){ ?>
         » <?=strip_tags($this->Spr->GetNameByCod( TblModVideoTxt, $this->id));?>
       <? } ?>
     </div>
     <?
   }
   // ================================================================================================
  // Function : ShowSearchResult()
  // Version : 1.0.0
  // Date :    25.09.2006
  // Parms :   $id - poll id
  // Returns : true/false
  // Description : Show Saerch form for search in the news
  // Programmer : Dmitriy Kerest
  // ================================================================================================

function showSearchResult($rows)
{
    if($rows>0){
    ?>

    <ul>
    <?
    for($i=0; $i<$rows; $i++ )
    {
        $row = $this->db->db_FetchAssoc();
        ?>
        <li><a href="<?=$this->Link( $row['category'], $row['id'])?>"><?=stripslashes( $row['name']);?></a></li>
        <?
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
      $q="select cod from `".TblModVideoCat."` where  BINARY `translit`= BINARY '".$translit."'";
      $res = $this->db->db_query( $q);
//     echo $q;
     if( !$this->db->result )
            return false;
     $rows = $this->db->db_GetNumRows();
     if($rows ==0)
        return false;

     $row = $this->db->db_FetchAssoc();
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
      $lang = _LANG_ID;
      $q="select translit from `".TblModVideoCat."` where  `cod`= '".$cod."' AND `lang_id`= '".$lang."' ";
      $res = $this->db->db_query( $q);
//     echo $q;
     if( !$this->db->result )
            return false;
     $rows = $this->db->db_GetNumRows();
     if($rows ==0)
        return false;

     $row = $this->db->db_FetchAssoc();
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
    $lang_id = _LANG_ID;
    $q = "SELECT
                    `".TblModVideo."` .id as id_prop
            FROM `".TblModVideoTxt."` ,`".TblModVideo."`
            WHERE
                BINARY `translit` = BINARY '".$position."'
                AND
                    `".TblModVideo."`.id = `".TblModVideoTxt."`.cod
                ";
    if( !empty($lang_id) )
            $q = $q." AND `".TblModVideoTxt."`.lang_id='".$lang_id."'";
    if( $cat!=NULL )
            $q = $q." AND `".TblModVideo."`.category='".$cat."'";

      $res = $this->db->db_query( $q);
//     echo $q;
     if( !$this->db->result )
            return false;
     $rows = $this->db->db_GetNumRows();
     if($rows ==0)
        return false;

     $row = $this->db->db_FetchAssoc();
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
    $lang_id = _LANG_ID;
    $q = "SELECT
                    `".TblModVideoTxt."` .translit
            FROM `".TblModVideoTxt."` ,`".TblModVideo."`
            WHERE
                BINARY `translit` = BINARY '".$position."'
                AND
                    `".TblModVideo."`.id = `".TblModVideoTxt."`.cod
                ";
    if( !empty($lang_id) )
            $q = $q." AND `".TblModVideoTxt."`.lang_id='".$lang_id."'";
    if( $id_cat!=NULL )
            $q = $q." AND `".TblModVideo."`.category='".$cat."'";

      $res = $this->db->db_query( $q);
//     echo $q;
     if( !$this->db->result )
            return false;
     $rows = $this->db->db_GetNumRows();
     if($rows ==0)
        return false;

     $row = $this->db->db_FetchAssoc();
     return $row['translit'];

   } //end of function  GetIdTranslitByCod()


    // ================================================================================================
    // Function : ShowVideoLink()
    // Version : 1.0.0
    // Date : 01.07.2010
    // Parms :
    // Returns : true,false / Void
    // Description : Show Link to Video Chapter
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowVideoLink()
    {
       ?><div class="leftBlockHead"><?=$this->multi['TXT_VIDEO_TITLE'];?></div>
         <div class="videoBlock">
            <?$link = _LINK.'video/';?>
            <a href="<?=$link?>" title="<?=$this->multi['TXT_VIDEO_TITLE'];?>"><img src="/images/design/videoSmall.jpg"></a>
         </div><?
    }

   } //end of class videoLayout
?>