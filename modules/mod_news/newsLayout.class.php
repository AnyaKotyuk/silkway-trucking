<?php
// ================================================================================================
// System : CMS
// Module : newsLayout.class.php
// Date : 01.03.2011
// Licensed To:  Yaroslav Gyryn
// Purpose : Class definition for all actions with Layout of News on the Front-End
// ================================================================================================
include_once( SITE_PATH.'/modules/mod_news/news.defines.php' );
/**
* Class User
* Class definition for all Pages - user actions
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 22.02.2011
* @property Right $Right
* @property FrontendPages $FrontendPages
* @property UserAuthorize $Logon
* @property FrontForm $Form
* @property db $db
* @property TblFrontMulti $multi
*/
class NewsLayout extends News{

   var $id = NULL;
   var $is_tags = NULL;
   var $module = NULL;
   var $is_comments = NULL;
   var $fltr = NULL;
   var $db = NULL;
   var $Form = NULL;
   var $Right = NULL;

   //static $instance = NULL;

    // ================================================================================================
    //    Function          : NewsLayout (Constructor)
    //    Date              : 01.03.2011
    //    Parms             : sort      / field by whith data will be sorted
    //                        display   / count of records for show
    //                        start     / first records for show
    //    Returns           : Error Indicator
    //    Description       : Opens and selects a dabase
    // ================================================================================================
    function __construct($display=NULL, $sort=NULL, $start=NULL, $module=NULL) {
        //Check if Constants are overrulled
        ( $display  !="" ? $this->display = $display  : $this->display = 20   );
        ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
        ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
        ( $module   !="" ? $this->module  = $module   : $this->module  = 24 );

        parent::__construct();
        $this->Form = check_init('FormNews', 'FrontForm', "'form_news'");
        if(empty($this->Right)) $this->Right =  check_init('Rights', 'Rights');
        if(empty($this->multi)) $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);

        $this->UploadImages = new UploadImage('24', 'null', 'uploads/images/news');

        ( defined("USE_TAGS")       ? $this->is_tags = USE_TAGS         : $this->is_tags=0      );
        ( defined("USE_COMMENTS")   ? $this->is_comments = USE_COMMENTS : $this->is_comments=0  );
    } // End of NewsLayout Constructor


     // ================================================================================================
    // Function : ShowNewsLinks()
    // Date : 01.03.2011
    // Returns :      true,false / Void
    // Description :  Show News Links
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
     function ShowNewsLinks()
    {
        if(empty($this->settings))
            $this->settings = $this->GetSettings();

        $q = "select * from `".TblModNews."`, `".TblModNewsSprSbj."`
              where `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod
              and `".TblModNewsSprSbj."`.lang_id='".$this->lang_id."'
              and  `".TblModNewsSprSbj."`.name != ''
              and status='a'";
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();
        //echo 'q='.$q.' res='.$res;
        ?>
        <div align="center">
        <div class="newsLinks">
         <div class="newsLinksBlock" align="left">
             <div class="m_title">
                <?=$this->multi['TXT_NOVIGATE'];?>:
              </div>
              <?
              if($rows>0){
                  ?><br/><a href="<?=_LINK;?>news/last/" class="t_link"><?=$this->multi['MOD_NEWS_LATEST'];?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?=$rows;?></span><?
              }
              else {
                  ?><br/><span class="inacive_txt"><?=$this->multi['MOD_NEWS_LATEST'];?></span><?
              }

              $q = "select * from `".TblModNews."`, `".TblModNewsSprSbj."`
                    where `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod
                    and `".TblModNewsSprSbj."`.lang_id='".$this->lang_id."'
                    and  `".TblModNewsSprSbj."`.name != ''
                    and status='e'";
              $res = mysql_query( $q );
              $rows = mysql_num_rows( $res );

              if($rows>0){?><br/><a href="<?=_LINK;?>news/arch/" class="t_link"><?=$this->multi['MOD_NEWS_ARCH'];?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?=$rows;?></span><?}
              else{?><br/><span class="inacive_txt"><?=$this->multi['MOD_NEWS_ARCH'];?></span><?}

              $q = "select * from `".TblModNews."`, `".TblModNewsSprSbj."`
                    where `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod
                    and `".TblModNewsSprSbj."`.lang_id='".$this->lang_id."'
                    and  `".TblModNewsSprSbj."`.name != ''
                    and status!='i'";
              $res = mysql_query( $q );
              $rows = mysql_num_rows( $res );

              if($rows>0){?><br/><a href="<?=_LINK;?>news/all/" class="t_link"><?=$this->multi['MOD_NEWS_ALL'];?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?=$rows;?></span><?}
              else{?><br/><span class="inacive_txt"><?=$this->multi['MOD_NEWS_ALL'];?></span><?}
              ?>
          </div>
          <?
          $this->NewsCategory();

          if ( isset($this->settings['subscr']) AND $this->settings['subscr']=='1' ) {
            ?>
            <div class="newsLinksBlock" align="left">
             <div class="m_title">
                <a href="<?=_LINK;?>news/subscribe/"><?=$this->multi['TXT_SUBSCRIBE'];?></a>
              </div>
             </div>
            <?
          }
        ?>
        </div>
    </div>
    <?
    } // end of function ShowNewsLinks


    // ================================================================================================
    // Function : NewsCategory()
    // Date : 01.03.2011
    // Returns :      true,false / Void
    // Description :  Show News Category ...
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function NewsCategory()
    {
        $q = "SELECT `".TblModNewsCat."`.*
              FROM `".TblModNewsCat."`
              WHERE `lang_id`='"._LANG_ID."'
              ORDER BY `move` ASC ";
        $res = $this->db->db_Query( $q );
        $rows = $this->db->db_GetNumRows();

        if ($rows>0){
            //if( isset($settings['subscr']) AND $settings['subscr']=='1' ) {$style_add='float:left;';}
            ?>
            <div class="newsLinksBlock" align="left">
             <div class="m_title">
                <?=$this->multi["_FLD_NEWS_CATEGORY"];?>:
              </div>
            <?
            for( $i = 0; $i < $rows; $i++ ){
                $row = $this->db->db_FetchAssoc();
                $name = $row['name'];
                $q1 = "select * from ".TblModNews." where id_category='".$row['cod']."' and status!='i' ";
                $res1 = $this->db->db_Query( $q1 );
                $rows1 = $this->db->db_GetNumRows();

                if( $rows1 ) {
                    $link =  $this->Link($row['cod'], NULL);
                    ?><br/><a class="t_link" href="<?=$link;?>"><?=$name;?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?=$rows1;?></span><?
                } // end if
            } // end for
            ?>
            </div>
            <?
        }
    } //end of function NewsCategory

    // ================================================================================================
    // Function : GetNewsForTags()
    // Date : 23.05.2011
    // Returns :      true,false / Void
    // Description :  Get News For Tags
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetNewsForTags( $idNews=null, $idModule = 83)
    {
        if($idNews== null)
            return;
        $q = "
        SELECT
             `".TblModNews."`.id,
             `".TblModNews."`.start_date,
             `".TblModNews."`.id_category,
             `".TblModNewsSprSbj."`.name,
             `".TblModNewsSprShrt."`.name as shrt,
             `".TblModNewsCat."`.name as category,
             `".TblModNews."`.display,
             `".TblModNewsLinks."`.link
        FROM `".TblModNews."`, `".TblModNewsSprSbj."`, `".TblModNewsCat."`, `".TblModNewsSprShrt."` , `".TblModNewsLinks."`
        WHERE
                     `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod
              AND `".TblModNews."`.id_category = `".TblModNewsCat."`.cod
              AND `".TblModNews."`.id = `".TblModNewsSprShrt."`.cod
              AND `".TblModNews."`.id = `".TblModNewsLinks."`.cod
              AND `".TblModNewsSprSbj."`.lang_id='".$this->lang_id."'
              AND `".TblModNewsSprShrt."`.lang_id='".$this->lang_id."'
              AND `".TblModNewsCat."`.lang_id='".$this->lang_id."'
              AND `".TblModNewsSprSbj."`.name!=''
              AND  `".TblModNews."`.id  IN (".$idNews.")
              ";
         if( $this->fltr!='' ) $q = $q.$this->fltr;
         //$q = $q." order by `".TblModNews."`.id DESC ";
         //$q = $q." order by `".TblModNews."`.id DESC limit ".$this->start.",".$this->display."";
         $res = $this->db->db_Query( $q );
         $rows = $this->db->db_GetNumRows();
         //echo "<br><br>".$q."<br/> res=".$res." rows=".$rows;
         if($rows==0)
             return;

         $array = array();
         for( $i = 0; $i <$rows; $i++ ){
             $row = $this->db->db_FetchAssoc();
             $array[$row['id']] = $row;
             $array[$row['id']]['module'] = $idModule;
             //$array[$row['id']] = $i;
         }
         return $array;

    }

    /**
     * NewsLayout::ShowNewsByPages()
     * Show all news by Pages
     * @author Yaroslav
     * @return
     */
    function ShowNewsByPages()
    {
        $array = $this->GetNRowsNews(true);
        $rows = count($array);

//        var_dump($array);
        //echo '<br>$rows='.$rows;
        $Page = check_init('PageUser', 'PageUser');
        $Page->showContent2Box .= ' news-content';
        if($rows==0){
            echo View::factory('/modules/mod_news/tpl_news/tpl_news_empty.php')
                ->bind('err', $this->multi['MSG_NO_NEWS']);
            return;
        }
        for( $i = 0; $i <$rows; $i++ ){
//            var_dump($array);
            $value = $array[$i];
            $rowReal = array();
            $rowReal['Name_cat'] = $value['Name_cat'];
            $rowReal['link_cat'] = $value['link_cat'];
            $rowReal['name'] = htmlspecialchars(stripslashes($value['name']));
            if ( isset($this->settings['dt']) AND $this->settings['dt']=='1' ){
                $rowReal['date']['day'] = $this->GetDate($value['start_date'], 'day');
                $rowReal['date']['month'] = $this->GetDate($value['start_date'], 'month');
            }else{
                $rowReal['date']['day'] = '';
                $rowReal['date']['month'] = '';
            }
            $rowReal['short'] = strip_tags(stripslashes($value['short']));
//            echo '<br>is_full='.$value['is_full'];
//            echo $value['link'];

            if($value['is_full']==1){
//                var_dump($value['link']);
                $rowReal['link'] = $this->Link( $value['id_cat'], $value['link']);
            }else{
                $rowReal['link'] = '';
            }
            //img start

            $rowReal['img'] = $value['main_img'];
            if(!empty($rowReal['img'])) {
                $rowReal['src'] = $this->ShowImage( $rowReal['img'], $value['id'], 'size_width=128', 85, NULL, "",true);
            }else{
                $rowReal['src'] = '';
            }
            $rowReal['img_alt'] = htmlspecialchars(stripcslashes($value['main_img_alt']));
            if(empty($rowReal['img_alt'])){
                $rowReal['img_alt'] = $rowReal['name'];
            }

            $rowReal['img_title'] = htmlspecialchars(stripcslashes($value['main_img_title']));
            if(empty($rowReal['img_title'])){
                $rowReal['img_title'] = $rowReal['name'];
            }
            //img end
            $arr[$i] = $rowReal;
//            var_dump($rowReal['link']);
        }
        if($rows>0){
            $array_all = $this->GetNRowsNews();
            $n_rows = count($array_all);
            //echo '<br>$n_rows='.$n_rows;
            $link = $this->Link( $this->id_cat, NULL );
            $pages = $this->Form->WriteLinkPagesStatic( $link, $n_rows, $this->display, $this->start, $this->sort, $this->page );
        }else{
            $pages = '';
        }
        echo View::factory('/modules/mod_news/tpl_news/tpl_news_by_pages.php')
            ->bind('arr', $arr)
            ->bind('pages', $pages)
            ->bind('name_datail', $this->multi['TXT_DETAILS'])
            ->bind('data', $this->multi['TXT_TOP_DATA'])
            ->bind('rows', $rows);
            //->bind('pages',$pages);
    } // end of functtion ShowNewsByPages

function ShowNewsByCalendar(){
//echo 'monthForAjax='.$this->monthForAjax.'yearForAjax='.$this->yearForAjax.'<br>';



    if($this->monthForAjax==12){
        $endMonth = '01';
        $endYear = $this->yearForAjax +1;
    }else{
        $endMonth = $this->monthForAjax+1;
        if($endMonth<10){
            $endMonth = '0'.$endMonth;
        }
        $endYear = $this->yearForAjax;
    }

    if($this->monthForAjax<10){
        $this->monthForAjax = '0'.$this->monthForAjax;
    }

    $q = "SELECT
            *
        FROM
            `".TblModNews."`
        WHERE
            `".TblModNews."`.`status`!='n' and
            `".TblModNews."`.`visible` = '1' and
            `start_date`>"."'".$this->yearForAjax."-".$this->monthForAjax."-00 00:00:00"."'"." and
            `start_date`<"."'".$endYear."-".$endMonth."-00 00:00:00"."'"."
         ";
    $q = $q." order by `".TblModNews."`.display DESC ";
//echo $q;//die();
    $res = $this->db->db_Query( $q );
    //echo '<br>$q='.$q.' $res='.$res;
    if( !$res OR !$this->db->result) return false;
    $rows = $this->db->db_GetNumRows();
    $array = array();
    for( $i = 0; $i <$rows; $i++ ){
        $array[$i] = $this->db->db_FetchAssoc();

        $array[$i]['start_date'] = substr($array[$i]['start_date'], 0 , 10);

    }
//    var_dump($array);
    $numberDates = array();
    for( $i = 0; $i < $rows; $i++ ){
        $rowsData = 0;
        $row = $array[$i];
        $i_date = $row['start_date'];
        for( $j = 0; $j < $rows; $j++ ){
            $row_j = $array[$j];
            $j_date = $row_j['start_date'];
            if($j_date == $i_date){
                $rowsData++;
            }
        }
        $numberDates[$i_date] = $rowsData;
    }
    $str = '';
    foreach($numberDates as $key=>$val){
        $str .= $key.'='.$val.';';
    }
    $str = substr($str, 0, -1);
    //echo '<br>$str='.$str;
    return $str;
    ?>

    <?
}


function ShowNewsByArchive($day)
{
    $array = $this->GetArchiveNRows($day, true);
    $rows = count($array);
    $Page = check_init('PageUser', 'PageUser');
    $Page->showContent2Box .= ' news-content';
    if($rows==0){
        echo View::factory('/modules/mod_news/tpl_news/tpl_news_empty.php')
            ->bind('err', $this->multi['MSG_NO_NEWS']);
        return;
    }
    for( $i = 0; $i <$rows; $i++ ){
        $value = $array[$i];
        $rowReal = array();
        $rowReal['Name_cat'] = $value['Name_cat'];
        $rowReal['link_cat'] = $value['link_cat'];
        $rowReal['name'] = htmlspecialchars(stripslashes($value['name']));
        if ( isset($this->settings['dt']) AND $this->settings['dt']=='1' ){
            $rowReal['date'] = $this->ConvertDate($value['start_date'], false, '-');
        }else{
            $rowReal['date'] = '';
        }
        $rowReal['short'] = strip_tags(stripslashes($value['short']));
//            echo '<br>is_full='.$value['is_full'];
        if($value['is_full']==1){
            $rowReal['link'] = $this->Link( $value['id_cat'], $value['link']);
        }else{
            $rowReal['link'] = '';
        }
        //img start
        $rowReal['img'] = $value['main_img'];
        if(!empty($rowReal['img'])) {
            $rowReal['src'] = $this->ShowImage( $rowReal['img'], $value['id'], 'size_width=128', 85, NULL, "",true);
        }else{
            $rowReal['src'] = '';
        }
        $rowReal['img_alt'] = htmlspecialchars(stripcslashes($value['main_img_alt']));
        if(empty($rowReal['img_alt'])){
            $rowReal['img_alt'] = $rowReal['name'];
        }

        $rowReal['img_title'] = htmlspecialchars(stripcslashes($value['main_img_title']));
        if(empty($rowReal['img_title'])){
            $rowReal['img_title'] = $rowReal['name'];
        }
        //img end
        $arr[$i] = $rowReal;
    }
    $day = $this->ConvertDateArchive($day, ' ');
    echo View::factory('/modules/mod_news/tpl_news/tpl_news_by_archive.php')
        ->bind('arr', $arr)
        ->bind('day', $day)
        ->bind('rows', $rows);
} // end of functtion ShowNewsByPages

    // ================================================================================================
    // Function : GetTodayNews()
    // Date : 01.03.2011
    // Returns :      true,false / Void
    // Description :  Write count of today news
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetTodayNews()
    {
        $q = "select * from ".TblModNews." where 1 and `start_date`>'".date("Y-m-d")." 0:00:00' and `start_date`<'".date("Y-m-d")." 23:59:59' ";
        $q = $q." order by id DESC";
        $res = $this->db->db_Query( $q );
        //echo "<br> q = ".$q." res = ".$res;
        $rows = $this->db->db_GetNumRows();
        return $this->Msg->show_text('TXT_NEWS_TODAY').": ".$rows;
    } // end of  GetTodayNews


    // ================================================================================================
    // Function : NewsShowFull()
    // Date : 01.04.2011
    // Returns :      true,false / Void
    // Description :  Show Full-text of news
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function NewsShowFull()
    {
//        echo 'full';
        $tmp_db = DBs::getInstance();

        $value = $this->GetNewsData($this->id);
        //var_dump($value);
        $this->title = $value['name'];
        $this->short = $value['short'];
        $path = NewsImg_Path.$value['id'].'/'.$value['main_img'];
        $this->main_img_src = $path;
        $this->main_img_ready = ImageK::getResizedImg($path, 'size_auto=400', '85', NULL);

        if( !isset($value['id']) ) return false;

        if ( isset($this->settings['dt']) AND $this->settings['dt']=='1' ){
            $date['day'] = $this->GetDate($value['start_date'], 'day');
            $date['month'] = $this->GetDate($value['start_date'], 'month');
        }else{
            $date['day'] = '';
            $date['month'] = '';
        }

        if ( isset($this->settings['img']) AND $this->settings['img']=='1' ){
            $images = $this->ShowUploadImagesByNews($value['id']);
        }else{
            $images = '';
        }

        if ( isset($this->settings['full_descr']) AND $this->settings['full_descr']=='1' ) {
            $full = stripcslashes($value['full']);
        }else{
            $full = '';
        }
        ob_start();
        if( $this->is_tags==1 ){
           if (empty($this->Tags))
                $this->Tags = Singleton::getInstance('FrontTags');

           $this->Tags->ShowUsingTags($this->module, $this->id);
        }
        $tags = ob_get_clean();

        ob_start();
        if($this->is_comments==1){
            $this->Comments = new CommentsLayout($this->module, $this->id);
            $this->Comments->ShowComments();
        }
        $comments = ob_get_clean();

        $News = check_init('NewsLayout', 'NewsLayout');
        ob_start();
        $News->ShowNewsByTags( $this->id );
        $news_by_tags = ob_get_clean();
        echo View::factory('/modules/mod_news/tpl_news/tpl_news_full.php')
            ->bind('value',$value)
            ->bind('date',$date)
            ->bind('images',$images)
            ->bind('tags',$tags)
            ->bind('comments',$comments)
            ->bind('full',$full)
            ->bind('text_back',$this->multi['TXT_FRONT_GO_BACK'])
            ->bind('such_news',$this->multi['TXT_SUCH_NEWS'])
            ->bind('text_tegs',$this->multi['TEXT_TEGS'])
            ->bind('text_name',$this->multi['text_name'])
            ->bind('news_by_tags', $news_by_tags);
    } // end if function NewsShowFull()

    // ================================================================================================
    // Function : ShowUploadImagesByNews()
    // Date : 19.06.2013
    // Parms : $pageId - id of the news
    // Returns : true,false / Void
    // Description : Show Upload Images List
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowUploadImagesByNews($pageId) {
        $items = $this->UploadImages->GetPictureInArrayExSize($pageId, $this->lang_id, NULL, 125, 75, true, true, 85,null,640,430);
        //var_dump($items);
        $items_keys = array_keys($items);
        $items_count = count($items);
        if ($items_count > 0) {
            return View::factory('/modules/mod_news/tpl_news/tpl_news_imeges.php')
                ->bind('items', $items)
                ->bind('items_count', $items_count)
                ->bind('multi', $this->multi)
                ->bind('lang_id', $this->lang_id)
                ->bind('items_keys', $items_keys);
        } else {
            return '';
        }
    }

    // ================================================================================================
    // Function : ShowSearchForm()
    // Date : 01.03.2011
    // Parms :   $id - poll id
    // Returns : true/false
    // Description : Show Saerch form for search in the news
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowSearchForm()
    {
        ?>
        <form  method="post" action="<?=_LINK?>news/?task=search_result">
        <table cellpadding="0" cellspacing="0" style="padding-bottom:10px; padding-top:10px;" align="center">
            <tr>
                <td colspan="2" class="menu" align="center"><h3>Быстрый поиск:</h3></td>
            </tr>
            <tr>
                <td class="menu" style="padding-left:8px; padding-right:7px;"><input type="text" maxlength="50" class="search_input" name="s_keywords" /></td>
                <td class="menu"><input type="submit" name="submit" value="Поиск"/></td>
            </tr>
        </table>
        </form>
        <?
    }// end of function ShowSearchForm


  // ================================================================================================
  // Function : ShowSearchResult
  // Date : 01.03.2011
  // Parms :   $rows
  // Returns : true/false
  // Description : Show Saerch form for search in the news
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function showSearchResult($rows)
  {
    if($rows>0){
        $arr = array();
        for($i=0; $i<$rows; $i++ )
        {
            $arr[] = $this->db->db_FetchAssoc();
        }
        ?><ul><?
        for($i=0; $i<$rows; $i++ ) {
            $row = $arr[$i];
//            var_dump($row);
            $link = $this->Link($row['id_cat'], $row['id']);
            ?><li><a href="<?=$link?>"><?=stripslashes(  $row['news_name']);?></a></li><?
        }
        ?></ul><?
    }
    else{
        echo $this->Msg->show_text('SEARCH_NO_RES', TblModPagesSprTxt);
    }
  }// end of function ShowSearchResult


// ================================================================================================
// Function : NewsLast()
// Date : 01.03.2011
// Parms :   $limit - limit of show
// Description : Show last news
// Programmer : Yaroslav Gyryn
// ================================================================================================
function showNewsLastWidget()
{
    $this->display = 3;
    $array = $this->GetNRowsLastNews(true);
    $rows = count($array);
    $Page = check_init('PageUser', 'PageUser');
    $Page->showContent2Box .= ' news-content';
    if($rows==0){
        echo View::factory('/modules/mod_news/tpl_news/tpl_news_empty.php')
            ->bind('err', $this->multi['MSG_NO_NEWS']);
        return;
    }
    for( $i = 0; $i <$rows; $i++ ){
        $value = $array[$i];
        $rowReal = array();
        $rowReal['name'] = htmlspecialchars(stripcslashes($value['name']));
        $rowReal['short'] = stripcslashes($value['short']);
        if ( isset($this->settings['dt']) AND $this->settings['dt']=='1' ){
            $rowReal['date']['day'] = $this->GetDate($value['start_date'], 'day');
            $rowReal['date']['month'] = $this->GetDate($value['start_date'], 'month');
        }else{
            $rowReal['date']['day'] = '';
            $rowReal['date']['month'] = '';
        }
        $rowReal['link'] = $this->Link($value['id_cat'], $value['link']);

        //img start
        if ( isset($this->settings['img']) AND $this->settings['img']=='1' ){
            $rowReal['img'] = $value['main_img'];
            if(!empty($rowReal['img'])) {
                $rowReal['src'] = $this->ShowImage( $rowReal['img'], $value['id'], 'size_rect=100x80', 75, NULL, "",true);
            }else{
                $rowReal['src'] = '';
            }
            $rowReal['img_alt'] = htmlspecialchars(stripcslashes($value['main_img_alt']));
            if(empty($rowReal['img_alt'])){
                $rowReal['img_alt'] = $rowReal['name'];
            }

            $rowReal['img_title'] = htmlspecialchars(stripcslashes($value['main_img_title']));
            if(empty($rowReal['img_title'])){
                $rowReal['img_title'] = $rowReal['name'];
            }
        }else{
            $rowReal['img'] = '';
        }
        $array[$i] = $rowReal;
    }
    if($rows>0){
        $array_all = $this->GetNRowsLastNews();
        $n_rows = count($array_all);
        $link = '/newsmain/';
        //echo '<br>$n_rows='.$n_rows.' $this->display='.$this->display.' $this->start='.$this->start;
        $pages = $this->Form->WriteLinkPagesStaticHomePage( $link, $n_rows, $this->display, $this->start, $this->sort, $this->page, false, 1);
    }else{
        $pages = '';
    }
//    var_dump($array);
    echo View::factory('/modules/mod_news/tpl_news/tpl_news_last_widget.php')
        ->bind('arr', $array)
        ->bind('name_datail', $this->multi['MOD_NEWS_ALL'])
        ->bind('rows', $rows)
        ->bind('pages',$pages)
        ->bind('title',$this->multi['_TXT_NEWS_TITLE']);

}   //end of function NewsLast





    function showNewsTopSlider(){
        $array = $this->GetNRowsTopMainNews(4);
        $rows = count($array);
        if($rows==0){
//            echo View::factory('/modules/mod_news/tpl_news/tpl_news_empty.php')
//                ->bind('err', $this->multi['MSG_NO_NEWS']);
            return;
        }


        for( $i = 0; $i <$rows; $i++ ){
            $row = $array[$i];
            if(!empty($row['id'])){
                $rowReal['id'] = $row['id'];
            }
            if(!empty($row['top_name'])){
                $rowReal['name'] = htmlspecialchars(stripslashes($row['top_name']));
            }else {
                $rowReal['name'] = htmlspecialchars(stripslashes($row['name']));
            }
            if(!empty($row['top_short'])){
                $rowReal['short'] = htmlspecialchars(stripslashes($row['top_short']));
            }else {
                $rowReal['short'] = htmlspecialchars(stripslashes($row['short']));
            }

            if ( isset($this->settings['dt']) AND $this->settings['dt']=='1' ){
                $rowReal['date'] = $this->ConvertDate($row['start_date']);
            }else{
                $rowReal['date'] = '';
            }
            $rowReal['link'] = $this->Link($row['id_cat'], $row['link']);

            //img start
            if ( isset($this->settings['img']) AND $this->settings['img']=='1' ){
                if(!empty($row['top_img'])){
                    $rowReal['img'] = htmlspecialchars(stripslashes($row['top_img']));
                }else {
                    $rowReal['img'] = htmlspecialchars(stripslashes($row['main_img']));
                }
                if(!empty($rowReal['img'])) {
                    $rowReal['src'] = $this->ShowImage( $rowReal['img'], $row['id'], 'size_rect=100x80', 75, NULL, "",true);
                    $rowReal['src2'] = $this->ShowImage( $rowReal['img'], $row['id'], 'size_rect=430x320', 75, NULL, "",true);
                }else{
                    $rowReal['src'] = '';
                    $rowReal['src2'] = '';
                }
                $rowReal['img_alt'] = htmlspecialchars(stripcslashes($row['main_img_alt']));
                if(empty($rowReal['img_alt'])){
                    $rowReal['img_alt'] = $rowReal['name'];
                }

                $rowReal['img_title'] = htmlspecialchars(stripcslashes($row['main_img_title']));
                if(empty($rowReal['img_title'])){
                    $rowReal['img_title'] = $rowReal['name'];
                }
            }else{
                $rowReal['img'] = '';
            }
            //img end

            $array[$i] = $rowReal;
        }

?>
<!--    <div class="top-news-main">-->
        <?
            $row_top = $array[0];
            ?>
            <section class="img-top">
                <a href="<?php echo $row_top['link'];?>" id="image" class="image_big ">
                    <div class="img1">
                        <div class="img2">
                            <img src="<?= $row_top['src2'] ?>" alt="<?= $row_top['img_alt'] ?>"
                                 title="<?= $row_top['img_title']; ?>"/>
                        </div>
                    </div>
                    <div class="short"><?= $row_top['short'] ?></div>
                </a>
            </section>
        <?

        ?>
        <div class="main-top">
            <?
            for( $i=0; $i<$rows; $i++ )
            {
                $row = $array[$i];
                $name[$i] = $row['name'];
                $link[$i] = $row['link'];
                $short = $row['short'];

                    if(!empty($row['img'])){?>
                        <div class="news-top-img" id="news_top_img_<?=$i?>">
                            <a href="<?=$link[$i];?>" title="<?=$name[$i]?>" >
                                <img src="<?=$row['src']?>" alt="<?=$row['img_alt']?>"  title="<?=$row['img_title']?>" rel="<?=$row['src2']?>" short="<?=$short?>" imgbig="<?=$link[$i]?>" class="image"/>
                            </a>
                        </div>
                    <?}
            }

            ?></div>
<!--    </div>-->
    <script>
        $(".image").hover(function() {
            var image = $(this).attr("rel"),
                imgbig,
                short = $(this).attr("short");
            $(this).parent('a');
            /*$('#image').hide();*/
            $('#image').fadeIn('slow');
            $('.img2').html('<img src="' + image + '" href="' + image + '"/>');
            $('.short').html('<span>'+short+'</span>');
            imgbig =  $(this).attr("imgbig");
            $('.top-news-main a#image').attr('href', imgbig);
            return false;
        });
    </script>
<?php

    }

    function showNewsTopLast(){
        $array = $this->GetNRowsTopNews(false, 1);
        $rows = count($array);
        if($rows>0){
            $array_top = $this->GetNRowsTopNews($this->displayTop, 1);
            $array_top_count = count($array_top);
            for( $i = 0; $i <$array_top_count; $i++ ) {
                $row = $array_top[$i];
                //var_dump($row);
                if (!empty($row['id'])) {
                    $rowReal['id'] = $row['id'];
                }
                if (!empty($row['top_name'])) {
                    $rowReal['name'] = htmlspecialchars(stripslashes($row['top_name']));
                } else {
                    $rowReal['name'] = htmlspecialchars(stripslashes($row['name']));
                }
                if (!empty($row['top_short'])) {
                    $rowReal['short'] = stripslashes($row['top_short']);
                } else {
                    $rowReal['short'] = stripslashes($row['short']);
                }

                if (isset($this->settings['dt']) AND $this->settings['dt'] == '1') {
                    $rowReal['date'] = $this->ConvertDate($row['start_date']);
                } else {
                    $rowReal['date'] = '';
                }
                $rowReal['link'] = $this->Link($row['id_cat'], $row['link']);

                //img start
                if (isset($this->settings['img']) AND $this->settings['img'] == '1') {
                    if (!empty($row['top_img'])) {
                        $rowReal['img'] = htmlspecialchars(stripslashes($row['top_img']));
                    } else {
                        $rowReal['img'] = htmlspecialchars(stripslashes($row['main_img']));
                    }
                    if (!empty($rowReal['img'])) {
                        $rowReal['src'] = $this->ShowImage($rowReal['img'], $row['id'], 'size_rect=100x80', 75, NULL, "", true);
                        $rowReal['src2'] = $this->ShowImage($rowReal['img'], $row['id'], 'size_rect=430x320', 75, NULL, "", true);
                    } else {
                        $rowReal['src'] = '';
                        $rowReal['src2'] = '';
                    }
                    $rowReal['img_alt'] = htmlspecialchars(stripslashes($row['main_img_alt']));
                    if (empty($rowReal['img_alt'])) {
                        $rowReal['img_alt'] = $rowReal['name'];
                    }

                    $rowReal['Name_cat'] = htmlspecialchars(stripslashes($row['Name_cat']));
                    $rowReal['link_cat'] = htmlspecialchars(stripslashes($row['link_cat']));

                    $rowReal['img_title'] = htmlspecialchars(stripslashes($row['main_img_title']));
                    if (empty($rowReal['img_title'])) {
                        $rowReal['img_title'] = $rowReal['name'];
                    }
                } else {
                    $rowReal['img'] = '';
                }
                //img end

                $arr[$i] = $rowReal;
            }


            $link = '/newstop/';
            //echo '<br>$rows='.$rows.' $this->startTop='.$this->startTop;
            $pages = $this->Form->WriteLinkPagesStaticTopHomePage( $link, $rows, $this->displayTop, $this->startTop, $this->sort, $this->page, false, 1);
        }else{
            $pages = '';
        }
        //var_dump($arr);
        echo View::factory('/modules/mod_news/tpl_news/tpl_news_top.php')
            ->bind('arr', $arr)
            ->bind('pages', $pages)
            ->bind('rows', $array_top_count)
            ->bind('title',$this->multi['TXT_TOP_PRODUCT'])
            ->bind('data',$this->multi['TXT_TOP_DATA']);
    }







function ShowCatigoriNews($level = 0, $cnt_sublevels = 10, $cnt = 0){
    $q = "SELECT
            `".TblModNewsCat."`.`id`,
            `".TblModNewsCat."`.`name`,
            `".TblModNewsCat."`.`cod`,
            `".TblModNewsCat."`.`level`,
            `".TblModNewsCat."`.`translit`
          FROM
            `".TblModNewsCat."`
          WHERE
            `".TblModNewsCat."`.`level`=0
            AND `".TblModNewsCat."`.`lang_id`='".$this->lang_id."'
          ORDER BY `".TblModNewsCat."`.`move` asc
          ";

    $res = $this->db->db_Query($q);
    //echo "<br> q = ".$q." res = ".$res;
    $rows = $this->db->db_GetNumRows($res);
    if($rows == 0)
        return;
    $arr = array();
    for( $i=0; $i<$rows; $i++ )
    {
        $arr[] = $this->db->db_FetchAssoc($res);
    }
    $lich = 0;
    ?><ul class="menu-category-news"><?
    for( $i=0; $i<$rows; $i++ ) {
        $row = $arr[$i];
        $link = $this->Link($row['cod']);
        ?><?
//var_dump($row);
        if($row['level'] == 0){
            if($i == 10){?>
                <li class="top-category-new" >
                    <a href="" title=""><?=$this->multi['TXT_OTHER']?></a>
                    <ul class="other-blok-menu">
            <?}else{?>
                <li class="top-category-new" >
                    <a href="<?=$link?>" title=""><?=$row['name']?></a>
            <?}
?>

        <?}else{
            /*if($lich == 0){*/?><!--
                <ul class="other-blok-menu">
            <?/*}*/?>
                <li>
                    <a href="<?/*=$link*/?>" title=""><?/*=$row['name']*/?></a>
                </li>
            --><?/*$lich++;*/
        }?>

    <?}
    ?></ul></li><?
}

// ================================================================================================
// Function : NewsLastColumn()
// Date : 01.03.2011
// Parms :   $limit - limit of show
// Description : Show last news in right Column
// Programmer : Yaroslav Gyryn
// ================================================================================================
function showNewsLastColumn($limit = 10, $str = null)
{
    $q = "SELECT * FROM
            `".TblModNews."`, `".TblModNewsNames."`, `".TblModNewsShort."`
        WHERE
            `".TblModNews."`.id = `".TblModNewsNames."`.id_news and
            `".TblModNews."`.id = `".TblModNewsShort."`.id_news and
            `".TblModNewsNames."`.lang_id='".$this->lang_id."' and
            `".TblModNews."`.status='a' and
            `".TblModNews."`.`visible`=1 AND
            `".TblModNewsShort."`.`lang_id`='".$this->lang_id."'
        ORDER BY `display` desc LIMIT ".$limit;

    $res = $this->db->db_Query($q);
    //echo "<br> q = ".$q." res = ".$res;
    $rows = $this->db->db_GetNumRows($res);
    $arr = array();
    for( $i=0; $i<$rows; $i++ )
    {
      $arr[] = $this->db->db_FetchAssoc($res);
    }
    for( $i=0; $i<$rows; $i++ )
    {
        $row = $arr[$i];
        $rowReal['name'] = htmlspecialchars(stripcslashes($row['name']));
        if ( isset($this->settings['dt']) AND $this->settings['dt']=='1' ){
            $rowReal['date'] = $this->ConvertDate($row['start_date']);
        }else{
            $rowReal['date'] = '';
        }
        $rowReal['link'] = $this->Link($row['id_cat'], $row['link']);



        //img start
        if ( isset($this->settings['img']) AND $this->settings['img']=='1' ){
            $rowReal['img'] = $row['main_img'];
            if(!empty($rowReal['img'])) {
                $rowReal['src'] = $this->ShowImage( $rowReal['img'], $row['id'], 'size_rect=60x60', 75, NULL, "",true);
            }else{
                $rowReal['src'] = '';
            }
            $rowReal['img_alt'] = htmlspecialchars(stripcslashes($row['main_img_alt']));
            if(empty($rowReal['img_alt'])){
                $rowReal['img_alt'] = $rowReal['name'];
            }

            $rowReal['img_title'] = htmlspecialchars(stripcslashes($row['main_img_title']));
            if(empty($rowReal['img_title'])){
                $rowReal['img_title'] = $rowReal['name'];
            }
        }else{
            $rowReal['img'] = '';
        }
        //img end


        $arr[$i] = $rowReal;
    }

    echo View::factory('/modules/mod_news/tpl_news/tpl_news_last.php')
        ->bind('arr', $arr)
        ->bind('name_datail', $this->multi['MOD_NEWS_ALL'])
        ->bind('rows', $rows)
        ->bind('title',$this->multi['_TXT_NEWS_TITLE']);
}

// ================================================================================================
// Function : NewsPropertyLast()
// Date : 01.03.2011
// Parms :   $limit - limit of show
// Description : Show last news
// Programmer : Yaroslav Gyryn
// ================================================================================================
function NewsPropertyLast($limit = 10, $property = 1)
{
    $q = "SELECT
            `".TblModNews."`.id,
            `".TblModNews."`.start_date,
            `".TblModNews."`.id_category,
            `".TblModNewsSprSbj."`.lang_id,
            `".TblModNewsSprSbj."`.name,
            `".TblModNewsSprShrt."`.name as shrt,
            `".TblModNewsLinks."`.link
        FROM
            `".TblModNews."`, `".TblModNewsSprSbj."`, `".TblModNewsSprShrt."`, `".TblModNewsLinks."`
        WHERE
            `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod and
            `".TblModNews."`.id = `".TblModNewsSprShrt."`.cod and
            `".TblModNews."`.id = `".TblModNewsLinks."`.cod and
            `".TblModNewsSprShrt."`.lang_id='".$this->lang_id."' and
            `".TblModNews."`.status='a' and
            `".TblModNewsSprSbj."`.`lang_id`='".$this->lang_id."' and
            `".TblModNewsSprSbj."`.`name`!='' and
            `".TblModNews."`.top = '0' and
            `".TblModNews."`.property = ".$property."
        ORDER BY `display` desc LIMIT ".$limit;

    $res = $this->db->db_Query($q);
    //echo "<br> q = ".$q." res = ".$res;
    $rows = $this->db->db_GetNumRows($res);
    if($rows == 0)
        return;
    $arr = array();
    for( $i=0; $i<$rows; $i++ )
    {
      $arr[] = $this->db->db_FetchAssoc($res);
    }
    ?><div class="news_colum1_2">
        <div class="news_colum1_2_title">Новини України</div>
    <?
    for( $i=0; $i<$rows; $i++ )
    {
      $row = $arr[$i];
      //$link = $this->Link($row['id_category'], $row['id']);
      $link = $this->Link($row['id_category'], $row['id'], $row['link']);
        ?>
        <div>
            <div class="news_colum1_2_text_time"><?=$this->ConvertDate($row['start_date'],true);?></div>
            <div class="news_colum1_2_text"><a href="<?=$link;?>"><?=strip_tags(stripslashes($row['name']));?></a></div>
        </div>
        <?
    }
    ?>
    <div class="clear"></div>
    <a class="btnMoreNews" href="/news/">Всі новини </a>
    </div><?
}   //end of function NewsPropertyLast


// ================================================================================================
// Function : NewsLine()
// Date : 01.03.2011
// Parms :   $limit - limit of show
// Description : Show news Line
// Programmer : Yaroslav Gyryn
// ================================================================================================
function NewsLine($limit = 5)
{
    $q = "SELECT
            `".TblModNews."`.id,
            `".TblModNews."`.start_date as start_date,
            `".TblModNews."`.id_category as id_category,
            `".TblModNewsSprSbj."`.lang_id as lang_id,
            `".TblModNewsSprSbj."`.name as name,
            `".TblModNewsSprShrt."`.name as shrt,
            `".TblModNewsLinks."`.link
        FROM
            `".TblModNews."`, `".TblModNewsSprSbj."`, `".TblModNewsSprShrt."`, `".TblModNewsLinks."`
        WHERE
            `".TblModNews."`.id = `".TblModNewsSprSbj."`.cod and
            `".TblModNews."`.id = `".TblModNewsSprShrt."`.cod and
            `".TblModNews."`.id = `".TblModNewsLinks."`.cod and
            `".TblModNewsSprShrt."`.lang_id='".$this->lang_id."' and
            `".TblModNews."`.status='a' and
            `".TblModNews."`.line='1' and
            `".TblModNewsSprSbj."`.`lang_id`='".$this->lang_id."' and
            `".TblModNewsSprSbj."`.`name`!=''
        ORDER BY
            `display` desc LIMIT ".$limit;

    $res = $this->db->db_Query($q);
    //echo "<br>".$q." <br/>res = ".$res;
    $rows = $this->db->db_GetNumRows($res);
    $arr = array();
    for( $i=0; $i<$rows; $i++ )
    {
      $arr[] = $this->db->db_FetchAssoc($res);
    }
    $str ='';
    for( $i=0; $i<$rows; $i++ )
    {
      $row = $arr[$i];
      $link = $this->Link($row['id_category'], $row['id'], $row['link']);
      $name = strip_tags(stripslashes($row['name']));
      $short =strip_tags(stripslashes($row['shrt']));
      $str[$i] = '<a href="'.$link.'" >'.$name.'</a>';
      /*?><a title="<?=$name ;?>" href="<?=$link;?>"><?=$name;?></a><?*/
    }
    if($rows>5)
        $rows = 5;
    else
        $rows = $i;
    switch($rows) {
        case 1:
            echo "<script>setText('".$rows."', '". $str[0]."');</script>";
            break;
        case 2:
            echo "<script>setText('".$rows."', '". $str[0]."','". $str[1]."');</script>";
            break;
        case 3:
            echo "<script>setText('".$rows."', '". $str[0]."','". $str[1]."','". $str[2]."');</script>";
            break;
        case 4:
            echo "<script>setText('".$rows."', '". $str[0]."','". $str[1]."','". $str[2]."','". $str[3]."');</script>";
            break;
        case 5:
            echo "<script>setText('".$rows."', '". $str[0]."','". $str[1]."','". $str[2]."','". $str[3]."','". $str[4]."');</script>";
            break;
    }
    if(!empty($str))
        return $str[$rows-1];
    else
        return $str;
}   //end of function NewsLine()


// ================================================================================================
// Function : NewsTop()
// Date : 01.03.2011
// Description : Show top news
// Programmer : Yaroslav Gyryn
// ================================================================================================
function NewsTop()
{
    if(empty($this->Article)) $this->Article = Singleton::getInstance('ArticleLayout');

    // Масив ТОП-новин
    $arrTopNews = $this->GetTopNews();
    $rowsNews = count($arrTopNews);

//    // Масив ТОП-статей
//    $arrTopArticles = $this->Article->GetTopArticles();
//    $rowsArticles = count($arrTopArticles);

    // Об'єднаний масив ТОП-новин і ТОП-статей
    $arrMergedNews = array();
//    $arrMergedNews = array_merge($arrTopNews, $arrTopArticles);
    $arrMergedNews = array_merge($arrTopNews);
    $rowsArrMergedNews = count($arrMergedNews);
    $keys = array_keys($arrMergedNews);

    // Формування і сортування масиву по даті зі збереженням індекса
    $topNews = null;
    for($i=0; $i<$rowsArrMergedNews; $i++) {
         if($arrMergedNews[$i]['top_main']==1)
            $topNews =  $keys[$i];
         $this->date[$i]['start_date'] = $arrMergedNews[$i]['start_date'];
         $this->date[$i]['index'] = $keys[$i];
    }
//    rsort($this->date);

    //Масив із 5 елементів для вивода на сайті
    $arrNews = array();
    if($topNews!=null)
        $arrNews[0] = $arrMergedNews[$topNews]; // Головна Топ новина або Топ стаття

    for($i=0, $n=0; $i<$rowsArrMergedNews; $i++) {
        if($topNews != $arrMergedNews[$this->date[$i]['index']])  {
            $arrNews[] = $arrMergedNews[$this->date[$i]['index']]; // Інші новини
            $n++;
        }
        if($topNews!=null) {
            if($n==4)
                break;
        }
        else {
            if($n==5)
                break;
        }

    }
    $rows = count($arrNews);
    //print_r($arrNews);
    if($rows==0)
        return;
    $str ='';
    $topId ='';
    $topSubject='';
    $toplink='';
    $topShort='';
    $topImage='';
    $type=array();
    $short = array(null,null,null,null,null);
    $link = array(null,null,null,null,null);
    $pathImage = array(null,null,null,null,null);
    //Вивод масиву на сайті
    for( $i=0; $i<$rows; $i++ )
    {
      $row = $arrNews[$i];
      $id[$i] =$row['id'];
      $link[$i] = $row['link'];

      $name[$i] = strip_tags(stripslashes($row['name']));
      $name[$i] = str_replace("'", "`", $name[$i]);

      $short[$i] = strip_tags($row['short']);
      $short[$i] = str_replace("\n", "", $short[$i]);
      //$short[$i] = str_replace("'", "\'", $short[$i]);
      $short[$i] = str_replace("'", "`", $short[$i]);
      $short[$i] = str_replace("", "\'", $short[$i]);
      $short[$i] = str_replace("\r", "", $short[$i]);

      $image[$i]= stripslashes($row['image']);
      $type[$i] = $row['type'];
      if(empty($image[$i]))
          $pathImage[$i] = '/images/design/noTopImage.jpg';
      else {
          if($type[$i]=='news')
            $pathDir = NewsImg_Path;
          if($type[$i]=='articles')
            $pathDir = ArticleImg_Path;
          $pathImage[$i] =  $pathDir.$id[$i].'/'.$image[$i];
      }


      if($row['top_main']==1) {  // Якщо основна топ новина
          $topId = $id[$i];
          $topType = $type[$i];  // news or article
          $topSubject = $name[$i];
          $toplink = $link[$i];
          $topShort = $short[$i];
          $topImage = $image[$i];
      }
      $str[$i] = '<a href="'.$link[$i].'" >'.$name[$i].'</a>';
      /*?><a title="<?=$name ;?>" href="<?=$link;?>"><?=$name;?></a><?*/
    }
    if(empty($topId)) {  // Якщо не виявилось жодної основної топ новини чи топ статті
          $topId = $id[0];
          $topType = $type[0];  // news or article
          $topSubject = $name[0];
          $toplink = $link[0];
          $topShort = $short[0];
          $topImage = $image[0];
    }
    ?>
    <div id="news_galery">
        <div id="galery_grey"></div>
        <div id="galery_left_bg">
            <div id="gal_news_title"><a href="<?=$toplink;?>"><?=$topSubject;?></a></div>
            <div id="gal_news_text"><?=$topShort;?></div>
            <div class="but_full_ver"><a href="<?=$toplink;?>">Повна версія</a></div>
        </div>
        <div id="galery_img">
        <?
        if(!empty($topId)) {
            if($topType=='news') { echo $this->ShowImage( $topImage, $topId, 'size_width=435', 85, NULL, "border=0 alt='' title='' "); }
            if($topType=='articles') { echo $this->Article->ShowImage( $topImage, $topId, 'size_width=435', 85, NULL, "border=0 alt='' title='' ");}
        }
        ?>
        </div>
        <div id="gal_news_short_block">
        <? for($i=0; $i<$rows; $i++) {
            $class= '';
            if($i==0)
                $class= '_active';
            ?><div id="num<?=$i?>" class="galery_news_short<?=$class?>">
                    <span class="galery_short_img" onclick="location.href='<?=$link[$i];?>'">
                    <?
                    if(!empty($image[$i])) {
                        if($type[$i]=='news') {echo $this->ShowImage( $image[$i], $id[$i], 'size_width=121', 85, NULL, "border=0 alt='".$name[$i]."' title='".$name[$i]."'  ");}
                        if($type[$i]=='articles') {echo $this->Article->ShowImage( $image[$i], $id[$i], 'size_width=121', 85, NULL, "border=0 alt='".$name[$i]."' title='".$name[$i]."'  ");}
                    }
                    else {
                        ?><img  src="/images/design/no-image.jpg" width="121" height="82" alt="" /><?
                    }?>
                    </span>
                    <div class="gal_sh_text" onclick="location.href='<?=$link[$i];?>'"><?/*<a title="" href="<?//=$link[$i];?>">*/?><?=$name[$i];?><?/*</a>*/?></div>
                 </div>
            <?
        }?>
        </div>
    </div>
    <?
    echo "<script>setTopShortText('".$rows."', '". $short[0]."','". $short[1]."','". $short[2]."','". $short[3]."','". $short[4]."');</script>";
    echo "<script>setTopHref('".$rows."', '". $link[0]."','". $link[1]."','". $link[2]."','". $link[3]."','". $link[4]."');</script>";
    echo "<script>setTopImage('".$rows."', '".$pathImage[0]."','".$pathImage[1]."','".$pathImage[2]."','".$pathImage[3]."','".$pathImage[4]."');</script>";
}   //end of function NewsTop()


    // ================================================================================================
    // Function : NewsCatLast()
    // Date :    02.03.2011
    // Returns : true/false
    // Description : Show Last news for $cat
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function NewsCatLast( $id_cat=3, $limit=6, $caption = null){
        $arr= $this->GetNewsCatLast( $id_cat, $limit);
        if(is_array($arr)) {
            $rows = count($arr);
            if($rows==0)
                return;
            if(!$caption )
                $caption = $this->multi['MOD_NEWS_CAT_LATEST'];
         ?>
         <div class="clear">&nbsp;</div>
         <div class="lastCategoryNews">
         <div class="news_colum1_1_title"><?=$caption?></div>
         <div class="innerSpacer"><?
            for( $i=0; $i<$rows; $i++ )
            {
              $row = $arr[$i];
              $name = strip_tags(stripslashes($row['name']));
              //$short = $this->Crypt->TruncateStr(strip_tags(stripslashes($row['shrt'])),200);
              //$short = strip_tags(stripslashes($row['shrt']));
              $link = $this->Link($row['id_category'], $row['id']);
              ?>
              <div class="lastNews">
                  <div class="date"><?=$this->ConvertDate($row['start_date'], false, true);?></div>
                  <a class="name" href="<?=$link;?>"><?=$name;?></a>
              </div>
              <?
            }
            //$linkCat = $this->Link($id_cat);
            /*?><a class="allNews" href="<?=$linkCat?>">Всі новини</a><?*/
            ?>
            <div class="clear">&nbsp;</div>
            </div>
            </div><?
         }
    } // end of function NewsCatLast


    /**
     * NewsLayout::GetMap()
     * Show map of News
     * @return void
     */
    function showModuleSiteMap(){
          $q = "SELECT cod, name FROM `".TblModNewsCat."` WHERE `lang_id`='".$this->lang_id."' ORDER BY `cod` ASC ";
          $res = $this->db->db_Query( $q );
          $rows = $this->db->db_GetNumRows();
          if($rows==0) return false;
          $arrCateg = array();
          for( $i = 0; $i < $rows; $i++ ){
              $arrCateg[$i] = $this->db->db_FetchAssoc();
          }
          ?>
          <ul><?
          for( $i = 0; $i < $rows; $i++ ){
              $row = $arrCateg[$i];
              $name = $row['name'];
              $q1 = "SELECT
                       `".TblModNews."`.id,
                       `".TblModNews."`.id_cat,
                       `".TblModNewsNames."`.*
                   FROM
                        `".TblModNews."`,
                        `".TblModNewsNames."`
                   WHERE
                        `".TblModNews."`.id_cat='".$row['cod']."'
                    AND
                        `".TblModNews."`.id=`".TblModNewsNames."`.`id_news`
                    AND
                        `".TblModNewsNames."`.name !=''
                    AND
                        `".TblModNewsNames."`.lang_id='"._LANG_ID."'
                    AND
                        `".TblModNews."`.status!='i'
                    ORDER BY `".TblModNews."`.`display` asc";

              $res1 = $this->db->db_Query( $q1 );
              $rows1 = $this->db->db_GetNumRows();
              //echo "<br> ".$q1." <br/>res=".$res1." rows=".$rows1;
              if( $rows1 )
              {
                  $arrNews = array();
                  for( $k = 0; $k < $rows1; $k++ )
                  {
                      $arrNews[] = $this->db->db_FetchAssoc();
                  }
                  $catLink = $this->Link($row['cod'], NULL);
                  ?><li><a href="<?=$catLink;?>"><?=$name?></a></li>
                  <ul><?
                  for( $j = 0; $j < $rows1; $j++ )
                  {
                      $row1 = $arrNews[$j];
                      $link = $this->Link($row1['id_cat'], $row1['link']);
                      //$link = $this->Link($row1['id_category'], $row1['id'], $row1['link']);
                      ?><li><a href="<?=$link;?>"><?=stripslashes($row1['name'])?></a></li><?
                  }
                  ?></ul><?
              }
          }
          ?></ul><?
    } // end of function GetMap



    //====================================== SubSribe START ===========================================

    // ================================================================================================
    // Function : SubscrForm()
    // Date : 01.03.2011
    // Description : subscribers registration simply form
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SubscrForm()
    {
        ?>
        <h2><?=$this->multi['TXT_SUBSCRIBE'];?></h2>
        <form action="<?=_LINK?>news/" method=post>
        <input type=hidden name="task" value="save_reg_data">
        <input type=hidden name="categories" value="all">
        <?=$this->showErr();?>
        <table border="0" cellspacing="3" cellpadding="0">
         <tr>
           <td aling="right"><?=$this->multi['_FLD_EMAIL'];?>:</td>
           <td><?=$this->Form->TextBox('subscriber', $this->subscriber, 15 );?></td>
           <td colspan="2" align="right"><input type="submit" value="<?=$this->multi['TXT_SAVE'];?>"></td>
         </tr>
        </table>
        </form>
        <?
    } // end of function  SubscrForm


    // ================================================================================================
    // Function : SubscrRegForm()
    // Date : 01.03.2011
    // Description : subscribers registration form
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function SubscrRegForm()
    {
        ?>
        <form action="<?=_LINK?>news/subscribe/add/" method=post>
        <input type=hidden name="task" value="save_reg_data">
        <?=$this->showErr();?>
        <table border="0" cellspacing="3" cellpadding="0">
         <tr>
           <td aling="right"><?=$this->multi['FLD_NEWS_SUBSCR_EMAIL'];?>:</td>
           <td><?=$this->Form->TextBox('subscriber', $this->subscriber, 30 );?></td>
         </tr>
         <tr>
          <td aling="right"><?=$this->multi['FLD_NEWS_SUBSCR_PASSWORD'];?>:</td>
          <td><?=$this->Form->Password('subscr_pass', $this->subscr_pass, 30 );?></td>
         </tr>
         <tr>
          <td colspan="2">
         <?
         $this->Err!=NULL ? $val=$this->categories : $val = $val = $this->Spr->GetListName( TblModNewsCat, $this->lang_id, 'arr', 'cod', 'asc', 'cod' );
         $cnt_cat_in_row = 4;
         $cnt_rows = ceil($this->Spr->GetCountValuesInSprOnLang( TblModNewsCat, $this->lang_id )/$cnt_cat_in_row);
         $this->Spr->ShowInCheckBox( TblModNewsCat, 'categories', $cnt_rows, $val, 'left' );?>
          </td>
         </tr>
         <tr>
          <td colspan="2" align="center"><input type="submit" value="<?=$this->multi['TXT_SAVE'];?>"></td>
         </tr>
        </table>
        </form>
        <?
    } // end of function  SubscrRegForm

    // ================================================================================================
    // Function : CheckFields()
    // Date : 01.03.2011
    // Returns :      true,false / Void
    // Description :  check fields
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function CheckFields()
    {
         $this->Err=NULL;

         if (empty( $this->subscriber )) $this->Err = $this->Err.$this->multi['MSG_FLD_EMAIL_EMPTY'].'<br>';
         else{
           // if ( $this->email!=$this->email2 ) $this->Err = $this->Err.$this->Msg->show_text('MSG_NOT_MATCH_REENTER_EMAIL').'<br>';
            if (!preg_match("^[a-zA-Z0-9_.\-]+@[a-zA-Z0-9.\-].[a-zA-Z0-9.\-]+$", $this->subscriber)) $this->Err = $this->Err.$this->multi['MSG_NOT_VALID_EMAIL'].'<br>';
            else{
                $q = "SELECT * FROM ".TblModNewsSubscr." WHERE `login`='".$this->subscriber."'";
                $res = $this->db->db_Query( $q );
                //echo "<br>11 q=".$q." res=".$res;
                if( !$res ) return false;
                $rows = $this->db->db_GetNumRows();
                if( $rows>0 ) $this->Err = $this->Err.$this->multi['MSG_EMAIL_ALREADY_EXIST'].'<br/>';
            }
         }
         return $this->Err;
    } //end of function CheckFields()
    //====================================== SubSribe END =============================================


    // ================================================================================================
    // Function : ShowErr()
    // Date : 01.03.2011
    // Returns :      true,false / Void
    // Description :  Show errors
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function showErr()
    {
        if ($this->Err){
        ?>
        <div class="err"  align=center>
            <h2><?=$this->Msg->show_text('MSG_ERR', TblSysTxt);?></h2>
            <?=$this->Err;?>
        </div>
        <?
        }
    } //end of function ShowErr()

    // ================================================================================================
    // Function : ShowTextMessages
    // Date : 01.03.2011
    // Returns : true,false / Void
    // Description : Show the text messages
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowTextMessages( $text = NULL )
    {
        echo "<H3 align=center class='msg'>$text</H3>";
    } //end of function ShowTextMessages()

    // ================================================================================================
    // Function : ShowNewsPath()
    // Returns :      true,false / Void
    // Description :  Show News Path
    // Programmer : Yaroslav Gyryn
    // Date :   01.03.2011
    // ================================================================================================
    function ShowNewsPath ($pagetxt) {
        /*$devider = '&nbsp→&nbsp';*/

        $devider = '';

        $add_str = '';
        ob_start();
        /**/?><!--<a href="<?/*=_LINK;*/?>"><?/*=$this->multi['TXT_HOME_PAGE'];*/?></a>--><?/*=$devider;*/
        //var_dump($this->task);
        switch( $this->task ){
            case 'showall':
                $cat_name_children = $this->treeNewsCat[$this->id_cat]['level'];
                if($cat_name_children == 0){
                    $add_str = '<span class="path_new_hleb">'.$this->name_to_path.'</span>';
                }
                else{
                    $cat_name_parent = $this->treeNewsCat[$cat_name_children]['name'];
                    $link = $this->Link($cat_name_children, NULL);
                    $parent = '<a href="'.$link.'">'.$cat_name_parent.'</a>';
                    $add_str = $parent.'<span class="path_new_hleb">'.$this->name_to_path.'</span>';
                }
                if(!empty($this->id_cat)){}
                    //$add_str = '<span class="path_new_hleb">'.$this->name_to_path.'</span>';
                else
                    $add_str = $devider.$this->multi['MOD_NEWS_ALL'];
                break;
            case 'arch':
                $add_str = $devider.$this->multi['MOD_NEWS_ARCH'];
                break;
            case 'showa':
                $add_str = $devider.$this->multi['MOD_NEWS_LATEST'];
                break;
            case 'showfull':
                if(!empty ($this->id)) {
                    $link = $this->Link($this->id_cat, NULL);
                    $cat_name = $this->treeNewsCat[$this->id_cat]['name'];
                    if($this->treeNewsCat[$this->id_cat]['level'] == 0)
                        $add_str = '<a href="'.$link.'">'.$cat_name.'</a>';
                    else {
                        $IdCat = $this->treeNewsCat[$this->id_cat]['level'];
                        $link2 = $this->Link($IdCat, NULL);
                        $cat_name2 = $this->treeNewsCat[$IdCat]['name'];
                        $add_str = '<a href="' . $link2 . '">' . $cat_name2 . '</a> '.' <a href="' . $link . '">' . $cat_name . '</a>';
                    }
                    //$add_str = $devider.'<a href="'.$link.'">'.$cat_name.'</a>'.$devider.$this->name_to_path;
                }
                break;
            default:
                break;
        }
        if(!empty($add_str)){
//            ?><!--<a href="--><?//=_LINK;?><!--news/">--><?//=$pagetxt['pname'];?><!--</a>--><?//
        }else{
            $path = '<div class="path_new_hleb">'.$this->name_to_path.'</div>';
            echo $path;
        }
        echo $add_str;
        return ob_get_clean();
    }
//--- end of function ShowNewsPath() -----------------------------------------------------------------------------------



function ShowPath($id_page, $path = NULL, $make_link = false) {
//    var_dump($this->id_cat);
    $res = NULL;
    $devider = '<img style="margin: 0 5px" src="http://'.NAME_SERVER.'/images/design/brcr.png">';

    $path = '<a href="' . _LINK . '">' . $this->multi['TXT_FRONT_HOME_PAGE'] . '</a> ';
    if(!$this->task){
        $path .=  $devider . ' '.'<span>'. $this->multi['TXT_FRONT_NEWS_PAGE'] .'</span>';
    }
    else {
        if(isset($this->id)) {
            $path .=  $devider . ' '.'<a href="http://' . NAME_SERVER . '/news">' . $this->multi['TXT_FRONT_NEWS_PAGE'] . '</a> ';
            $news_data = $this->GetNewsData($this->id);
            $path .=  $devider . ' '.'<span>'. $news_data['name'] .'</span>';
        }
    }



    return $path;
    if ($id_page > 0) {
        $row = $this->treePageData[$id_page];
        $name = stripslashes($row['pname']);
        $link = $this->Link($row['id']);

        if (!empty($path)) {
            $path = '<a href="' . $link . '">' . $name . '</a> ' . $devider . ' <a>' . $path.'</a>';
        } else {
            if ($make_link == 1) {
                $path = '<a href="' . $link . '">' . $name . '</a>';
            } else
                $path = $name;
        }
        if ($row['level'] > 0) {
            $path = $this->ShowPath($row['level'], $path, $make_link);
        } else
            $path = '<a href="' . _LINK . '">' . $this->multi['TXT_FRONT_HOME_PAGE'] . '</a> ' . $devider . ' <a>' . $path . "</a>";
    } else {
        $path = '<a href="' . _LINK . '">' . $this->multi['TXT_FRONT_HOME_PAGE'] . '</a> ' . $devider . ' ';
    }

    return $path;
}


function ShowNewsByTags( $news_id ){
    $params = 'null, ' . $this->module;
    $Tags = check_init('Tags', 'SystemTags', $params);
//var_dump($this->module, $news_id, MOD_NEWS);
    $ralatedItems = $Tags->GetSimilarItems($this->module, $news_id);
    if($ralatedItems == NULL)
        return false;
    $str_news_ids = implode(',', array_keys($ralatedItems[$this->module]));

    $q = "
          SELECT *
          FROM
            `".TblModNews."`,
            `".TblModNewsShort."`,
            `".TblModNewsNames."`
          WHERE
            `".TblModNews."`.id = `".TblModNewsShort."`.id_news and
            `".TblModNews."`.id = `".TblModNewsNames."`.id_news and
            `".TblModNewsShort."`.lang_id='".$this->lang_id."' and
            `".TblModNewsNames."`.`lang_id`='".$this->lang_id."' and
            `".TblModNewsNames."`.`name`!='' and
            `".TblModNews."`.`status`!='n' and
            `".TblModNews."`.`visible` = '1' and
            `".TblModNews."`.`id` IN (".$str_news_ids.")
        ";

    $q = $q." order by `".TblModNews."`.display DESC limit 0,5 ";
    $res = $this->db->db_Query( $q );
    if( !$res OR !$this->db->result) return false;
    $rows = $this->db->db_GetNumRows();
    $array = array();
    for( $i = 0; $i <$rows; $i++ ){
        $array[$i] = $this->db->db_FetchAssoc();
    }
    $rows = count($array);
    $Page = check_init('PageUser', 'PageUser');
    $Page->showContent2Box .= ' news-content';
    if($rows==0){
        return;
    }
    for( $i = 0; $i <$rows; $i++ ){
        $value = $array[$i];
        $rowReal = array();
        $rowReal['name'] = htmlspecialchars(stripslashes($value['name']));
        if ( isset($this->settings['dt']) AND $this->settings['dt']=='1' ){
            $rowReal['date'] = $this->ConvertDate($value['start_date']);
        }else{
            $rowReal['date'] = '';
        }
        if($value['is_full']==1){
            $rowReal['link'] = $this->Link( $value['id_cat'], $value['link']);
        }else{
            $rowReal['link'] = '';
        }
        $arr[$i] = $rowReal;
    }
    if($rows > 5 ) $rows = 5;
//    var_dump($arr);
    ?><div class="block_similar_tags"><?
    for($i=0;$i<$rows;$i++){
        $row = $arr[$i];?>
      <div class="similar-tags">
          <a href="<?=$row['link']?>" alt="<?=$row['name']?>" title="<?=$row['name']?>"><?=$row['name']?></a>
          <span>//<?=$row['date']?></span>
      </div>
    <?}?>
    </div>
<?}

function ShowTags( $tag ){
    $db = new DB();
    $q = "
              SELECT `id_item`
              FROM `" . TblSysModTagsSprName . "`, `" . TblSysModTags . "`
              WHERE
                `lang_id`='" . _LANG_ID . "' AND
                `name`='" . $tag . "' AND
                `id_module`='" . $this->module . "'
            ";
    echo $q;
    $res = $db->db_Query($q);
    if (!$res) return false;
    $rows = $this->db->db_GetNumRows();

    if( $row = $this->db->db_FetchAssoc() ) {
        var_dump($row);
    }

}

function getRelatedNewsByTags( $id_module, $item_id, $is_compact=false)
{
    $params = 'null, ' . $this->module;
    $Tags = check_init('Tags', 'SystemTags', $params);

    $ralatedItems = $Tags->GetSimilarItems($id_module, $item_id);
    if(!empty($ralatedItems[MOD_NEWS]))
        $str_news_ids = implode(',', array_keys($ralatedItems[MOD_NEWS]));
    else
        $str_news_ids = '';
//        var_dump($str_news_ids); exit;

    $q = "
          SELECT *
          FROM
            `" . TblModNews . "`,
            `" . TblModNewsShort . "`,
            `" . TblModNewsNames . "`
          WHERE
            `" . TblModNews . "`.id = `" . TblModNewsShort . "`.id_news and
            `" . TblModNews . "`.id = `" . TblModNewsNames . "`.id_news and
            `" . TblModNewsShort . "`.lang_id='" . $this->lang_id . "' and
            `" . TblModNewsNames . "`.`lang_id`='" . $this->lang_id . "' and
            `" . TblModNewsNames . "`.`name`!='' and
            `" . TblModNews . "`.`status`!='n' and
            `" . TblModNews . "`.`visible` = '1' and
            `" . TblModNews . "`.`id` IN (" . $str_news_ids . ")
        ";

    $q = $q . " order by `" . TblModNews . "`.display DESC limit 0,5 ";
    $res = $this->db->db_Query($q);
//        echo "<br>q=".$q." res=".$res;
    if (!$res OR !$this->db->result) return false;
    $rows = $this->db->db_GetNumRows();
    //echo "<br>rows=".$rows;
    $array = array();
    for ($i = 0; $i < $rows; $i++) {
        $array[$i] = $this->db->db_FetchAssoc();
    }

    $rows = count($array);
    $Page = check_init('PageUser', 'PageUser');
    $Page->showContent2Box .= ' news-content';
    if ($rows == 0) {
        return;
    }
    for ($i = 0; $i < $rows; $i++) {
        $value = $array[$i];
        $rowReal = array();
        $rowReal['Name_cat'] = $value['Name_cat'];
        $rowReal['link_cat'] = $value['link_cat'];
        $rowReal['name'] = htmlspecialchars(stripslashes($value['name']));
        if (isset($this->settings['dt']) AND $this->settings['dt'] == '1') {
            $rowReal['date'] = $this->ConvertDate($value['start_date']);
        } else {
            $rowReal['date'] = '';
        }
        $rowReal['short'] = strip_tags(stripslashes($value['short']));
//            echo '<br>is_full='.$value['is_full'];
        if ($value['is_full'] == 1) {
            $rowReal['link'] = $this->Link($value['id_cat'], $value['link']);
        } else {
            $rowReal['link'] = '';
        }
        //img start
        $rowReal['img'] = $value['main_img'];
        if ($is_compact) {
            if (!empty($rowReal['img'])) {
                $rowReal['src'] = $this->ShowImage($rowReal['img'], $value['id'], 'size_rect=50x37', 85, NULL, "", true);
            } else {
                $rowReal['src'] = '';
            }
        } else {
            if (!empty($rowReal['img'])) {
                $rowReal['src'] = $this->ShowImage($rowReal['img'], $value['id'], 'size_rect=100x66', 85, NULL, "", true);
            } else {
                $rowReal['src'] = '';
            }
        }
        $rowReal['img_alt'] = htmlspecialchars(stripcslashes($value['main_img_alt']));
        if (empty($rowReal['img_alt'])) {
            $rowReal['img_alt'] = $rowReal['name'];
        }

        $rowReal['img_title'] = htmlspecialchars(stripcslashes($value['main_img_title']));
        if (empty($rowReal['img_title'])) {
            $rowReal['img_title'] = $rowReal['name'];
        }
        //img end
        $arr[$i] = $rowReal;
    }
    if ($rows > 0) {
        $array_all = $this->GetNRows();
        $n_rows = count($array_all);
        $link = $this->Link($this->id_cat, NULL);
        $pages = $this->Form->WriteLinkPagesStatic($link, $n_rows, $this->display, $this->start, $this->sort, $this->page);
    } else {
        $pages = '';
    }
    if($is_compact == 2){
        return View::factory('/modules/mod_news/tpl_news/tpl_news_by_catalog.php')
            ->bind('arr', $arr)
            ->bind('rows', $rows);
    }
    if ($is_compact) {
        return View::factory('/modules/mod_news/tpl_news/tpl_news_by_tags.php')
            ->bind('arr', $arr)
            ->bind('rows', $rows);
    } else {
        echo View::factory('/modules/mod_news/tpl_news/tpl_news_by_pages.php')
            ->bind('data', $this->multi['TXT_TOP_DATA'])
            ->bind('arr', $arr)
            ->bind('pages', $pages)
            ->bind('rows', $rows);
    }

}
//--- end of function getRelatedNewsByTags() ---------------------------------------------------------------------------

} //end of class NewsLayout

?>
