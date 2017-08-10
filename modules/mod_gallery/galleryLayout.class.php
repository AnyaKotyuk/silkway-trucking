<?php
// ================================================================================================
// System : SEOCMS
// Module : GalleryLayout.class.php
// Version : 2.0.0
// Date : 28.01.2009
// Licensed To:
// Igor  Trokhymchuk  ihoru@mail.ru
// Yaroslav Gyryn    las_zt@mail.ru
//
// Purpose : Class definition for all actions with Layout of Gallery on the Front-End
//
// ================================================================================================
include_once( SITE_PATH . '/modules/mod_gallery/gallery.defines.php' );

class GalleryLayout extends Gallery {

    var $id = NULL;
    var $title = NULL;

    // ================================================================================================
    //    Function          : GalleryLayout (Constructor)
    //    Version           : 1.0.0
    //    Date              : 01.07.2010
    //    Parms             : sort      / field by whith data will be sorted
    //                        display   / count of records for show
    //                        start     / first records for show
    //    Returns           : Error Indicator
    //
    //    Description       : Opens and selects a dabase
    // ================================================================================================
    function GalleryLayout($user_id = NULL, $module = NULL, $display = NULL, $sort = NULL, $start = NULL) {

        //Check if Constants are overrulled
        ( $user_id != "" ? $this->user_id = $user_id : $this->user_id = NULL );
        ( $module != "" ? $this->module = $module : $this->module = 87 );
        ( $display != "" ? $this->display = $display : $this->display = 10 );
        ( $sort != "" ? $this->sort = $sort : $this->sort = NULL );
        ( $start != "" ? $this->start = $start : $this->start = 0 );

        if (defined("_LANG_ID"))
            $this->lang_id = _LANG_ID;

        $this->db = DBs::getInstance();
        $this->Right = check_init('Rights', 'Rights');
        $this->Form = check_init('FormGallery', 'FrontForm', "'form_gallery'");
        if (empty($this->multi))
            $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
        if (empty($this->Spr))
            $this->Spr = check_init('SysSpr', 'SysSpr');
        if (empty($this->Crypt))
            $this->Crypt = check_init('Crypt', 'Crypt');
        if (empty($this->settings))
            $this->settings = $this->GetSettings();
        //if (empty($this->Msg))  $this->Msg = Singleton::getInstance('ShowMsg');
        //$this->Msg->SetShowTable(TblModGallerySprTxt);
        //if (empty($this->Spr))  $this->Spr = Singleton::getInstance('FrontSpr');
        if (empty($this->Form))
            $this->Form = Singleton::getInstance('FrontForm', 'form_gallery');
        if (defined("MOD_BANNER") AND MOD_BANNER AND empty($this->Banner))
            $this->Banner = Singleton::getInstance('Banner');

        $this->UploadImages = new UploadImage(149, null, $this->settings['img_path'], 'mod_gallery_img');

        ( defined("USE_TAGS") ? $this->is_tags = USE_TAGS : $this->is_tags = 0 );
        ( defined("USE_COMMENTS") ? $this->is_comments = USE_COMMENTS : $this->is_comments = 0 );
    }

// End of GalleryLayout Constructor
    // ================================================================================================
    // Function : ShowGalleryTask()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show Gallery navigation by tasks
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowGalleryTask($task = NULL) {
        ?>
        <div style="margin: 13px 0px 0px 0px; float:none; min-height:90px;">
            <div style="width:320px; float:left;">
                        <?= $this->multi['TXT_OFFER_TASK']; ?>:
                <ul>
                    <li>
                    <?
                    $this->fltr = " AND `status`='a'";
                    $rows = $this->GetGallerysRows('limit');
                    if ($rows > 0) {
                        ?><a href="<?= _LINK; ?>gallerys/last/" class="t_link"><?= $this->multi['TXT_FRONT_TITLE_LATEST']; ?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?= $rows; ?></span><? } else {
                        ?><span class="inacive_txt"><?= $this->multi['TXT_FRONT_TITLE_LATEST']; ?></span><? }
                    ?>
                    </li>
                        <?
                        $this->fltr = " AND `status`='e'";
                        $rows = $this->GetGallerysRows('limit');
                        ?>
                    <li>
                    <? if ($rows > 0) { ?><a href="<?= _LINK; ?>gallerys/arch/" class="t_link"><?= $this->multi['TXT_FRONT_TITLE_ARCH']; ?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?= $rows; ?></span><? } else {
                        ?><span class="inacive_txt"><?= $this->multi['TXT_FRONT_TITLE_ARCH']; ?></span><? }
        ?>
                    </li>
                        <?
                        $this->fltr = " AND `status`!='i'";
                        $rows = $this->GetGallerysRows('limit');
                        ?>
                    <li>
            <? if ($rows > 0) { ?><a href="<?= _LINK; ?>gallerys/all/" class="t_link"><?= $this->multi['TXT_ALL_OFFERS']; ?>&nbsp;→</a>&nbsp;<span class="inacive_txt"><?= $rows; ?></span><? } else {
                ?><span class="inacive_txt"><?= $this->multi['TXT_ALL_OFFERS']; ?></span><? }
            ?>
                    </li>
                </ul>
            </div>
        <?
        $this->ShowGalleryCat();
        ?>
        </div>
        <?
    }

    // ================================================================================================
    // Function : ShowGalleryCat()
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show Gallery Category
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowGalleryCat($cat = NULL) {
        $q = "SELECT `" . TblModGalleryCat . "`.*
              FROM `" . TblModGalleryCat . "`
              WHERE `lang_id`='" . $this->lang_id . "'
              AND `name`!=''
              ORDER BY `move` DESC ";
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        if ($rows > 0) {
            ?><div class="catalogLevels">
                <ul><?
            for ($i = 0; $i < $rows; $i++) {
                $row = $this->db->db_FetchAssoc();
                $name = $row['name'];
                $link = $this->Link($row['translit'], NULL);
                ?><li><a href="<?= $link; ?>"><?= $name; ?></a></li><?
            } // end for
            ?>
                </ul>
            </div>
            <?
        } else {
            ?><div class="err" align="center"><?
            echo $this->multi['TXT_NO_DATA'];
            ?></div><?
        }
    }

//end of function ShowGalleryCat()
    // ================================================================================================
    // Function : ShowGalleryNavigation()
    // Version : 1.0.0
    // Date : 01.07.2010
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show News Navigation
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowGalleryNavigation() {
        $q = "SELECT `" . TblModGalleryCat . "`.*
              FROM `" . TblModGalleryCat . "`
              WHERE `lang_id`='" . $this->lang_id . "'
              AND `name`!=''
              ORDER BY `move` DESC ";
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        if ($rows > 0) {
            ?>
            <div class="leftMenu">
                <div class="leftBlockHead"><?= $this->multi['TXT_NOVIGATE']; ?></div>
                <ul>
                    <?
                    for ($i = 0; $i < $rows; $i++) {
                        $row = $this->db->db_FetchAssoc();
                        $name = $row['name'];
                        $link = $this->Link($row['translit'], NULL);
                        ?><li><a href="<?= $link; ?>"><?= $name; ?></a></li><?
                    } // end for
                    ?>
                </ul>
            </div>
            <?
        }
    }

    // ================================================================================================
    // Function : GetGalleryForTags()
    // Date : 23.05.2011
    // Returns :      true,false / Void
    // Description :  Get Gallery For Tags
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetGalleryForTags($idNews = null, $idModule = 156) {
        if ($idNews == null)
            return;

        $q = "
        SELECT
             `" . TblModGallery . "`.id,
             `" . TblModGallery . "`.dttm as start_date,
             `" . TblModGallery . "`.category as id_category,
             `" . TblModGalleryTxt . "`.name,
             `" . TblModGalleryTxt . "`.short as shrt,
             `" . TblModGalleryTxt . "`.translit,
             `" . TblModGalleryCat . "`.name as category,
             `" . TblModGalleryCat . "`.translit as cat_translit,
             `" . TblModGallery . "`.position
        FROM `" . TblModGallery . "`, `" . TblModGalleryTxt . "`, `" . TblModGalleryCat . "`
        WHERE  `" . TblModGallery . "`.category = `" . TblModGalleryCat . "`.cod
              AND `" . TblModGallery . "`.id = `" . TblModGalleryTxt . "`.cod
              AND `" . TblModGalleryTxt . "`.lang_id='" . $this->lang_id . "'
              AND `" . TblModGalleryCat . "`.lang_id='" . $this->lang_id . "'
              AND `" . TblModGalleryTxt . "`.name!=''
              AND  `" . TblModGallery . "`.id  IN (" . $idNews . ")
              ";
        if ($this->fltr != '')
            $q = $q . $this->fltr;
        $q = $q . " ORDER BY `" . TblModGallery . "`.id DESC LIMIT " . $this->start . "," . $this->display . "";

        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        //echo "<br>".$q."<br/> res=".$res." rows=".$rows;

        if ($rows == 0)
            return;

        $array = array();
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc();
            $array[$row['id']] = $row;
            $array[$row['id']]['module'] = $idModule;
        }

        return $array;
    }

// ================================================================================================
// Function : ShowGallerysByPages()
// Parms :
// Returns :      true,false / Void
// Description :  Show Gallery width img by pages
// Programmer : Yaroslav Gyryn
// Date : 06.01.2010
// ================================================================================================
    function ShowGallerysByPages() {
        if(empty($this->cat)){
            $this->PageUser->h1 =
                $this->PageUser->FrontendPages->treePageData[$this->PageUser->FrontendPages->page]['pname'];
        }else{
            $this->PageUser->h1 = $this->Spr->GetNameByCod( TblModGalleryCat, $this->cat);
        }
        $rows = $this->GetGallerysRows('limit');
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for ($i = 0; $i < $rows; $i++) {
            $array[] = $this->db->db_FetchAssoc();
        }
        $gallerys = array();
        for ($i = 0; $i < $rows; $i++) {
            $value = $array[$i];
            $row['name'] = stripslashes($value['sbj']);
            $row['date'] = $this->ConvertDate($value['start_date']);
            //$row['short'] = $this->Crypt->TruncateStr(strip_tags(stripslashes($value['shrt'])), 280);
            $row['short'] = stripslashes($value['shrt']);
            //$row['link_cat'] = $this->Link($value['cat_translit']);
            $row['link'] = $this->Link($value['cat_translit'], $value['translit']);

            $items = $this->UploadImages->GetPictureInArrayExSize($value['id'], $this->lang_id, NULL, 336, 246, true, true, 85);
            $items_keys = array_keys($items);
            $items_count = count($items);
            if ($items_count > 0) {
                $row['img']['alt'] = $items[$items_keys[0]]['name'][$this->lang_id];      // Заголовок
                $row['img']['title'] = $items[$items_keys[0]]['text'][$this->lang_id]; // Описание
                $row['img']['path'] = $items[$items_keys[0]]['path'];                    // Путь уменьшенной копии
                //$path2 = $items[$items_keys[0]]['path2'];                 // Путь большой копии
                //$path_org = $items[$items_keys[0]]['path_original'];   // Путь оригинального изображения
            }else{
                $row['img']['path'] = '/images/design/no-image.jpg';
                $row['img']['title'] = '';
                $row['img']['alt'] = '';
            }
            if($i%2==0)
                $row['class'] = ' left';
            else
                $row['class'] = ' right';
            $gallerys[$i] = $row;
        }
        if ($rows > 0) {
            $n_rows = $this->GetGallerysRows('nolimit');
            $link = $this->Link(null, null);
            $pages =  $this->Form->WriteLinkPagesStatic($link, $n_rows, $this->display, $this->start, $this->sort, $this->page);
        }else $pages = '';

        echo View::factory('/modules/mod_gallery/templates/tpl_gallery_by_pages.php')
            ->bind('gallerys', $gallerys)
            ->bind('multi', $this->multi)
            ->bind('pages', $pages);
    }

// end of function ShowGallerysByPages
    // ================================================================================================
    // Function : GetGallerysRows()
    // Date : 01.01.2010
    // Parms :        $limit / limit or nolimit rows from table
    // Returns :      true,false / Void
    // Description :  Get gallerys
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetGallerysRows($limit = 'limit') {
        $q = "
        SELECT
             `" . TblModGallery . "`.id,
             `" . TblModGallery . "`.dttm as start_date,
             `" . TblModGallery . "`.category as id_category,
             `" . TblModGalleryTxt . "`.name as sbj,
             `" . TblModGalleryTxt . "`.short as shrt,
             `" . TblModGalleryTxt . "`.translit,
             `" . TblModGalleryCat . "`.name as category,
             `" . TblModGalleryCat . "`.translit as cat_translit,
             `" . TblModGallery . "`.position
        FROM `" . TblModGallery . "`, `" . TblModGalleryTxt . "`, `" . TblModGalleryCat . "`
        WHERE  `" . TblModGallery . "`.category = `" . TblModGalleryCat . "`.cod
              AND `" . TblModGallery . "`.id = `" . TblModGalleryTxt . "`.cod
              AND `" . TblModGalleryTxt . "`.lang_id='" . $this->lang_id . "'
              AND `" . TblModGalleryCat . "`.lang_id='" . $this->lang_id . "'
              AND `" . TblModGalleryTxt . "`.name!=''";
        if ($this->fltr != '')
            $q = $q . $this->fltr;

        $q = $q . " ORDER BY `" . TblModGallery . "`.position DESC";
        if ($limit == 'limit')
            $q = $q . " LIMIT " . $this->start . "," . $this->display . "";

        $res = $this->db->db_Query($q);
        //echo $q.'<br/>';
        $rows = $this->db->db_GetNumRows();
        return $rows;
    }

// end of  GetGallerysRows()
    // ================================================================================================
    // Function : ShowGalleryFull()
    // Date : 01.07.2010
    // Returns :      true,false / Void
    // Description :  Show Gallery Full
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function ShowGalleryFull() {
        $rows = $this->GetGalleryData($this->id);
        if ($rows == 0) return false;
        $value = $this->db->db_FetchAssoc();
        $name = stripslashes($value['sbj']);
        $this->PageUser->h1 = $name;

        $images = $this->showUploadImages($this->id);

        $short = stripslashes($value['short']);

        ob_start();
        if ($this->is_tags == 1) {
            if (empty($this->Tags))
                $this->Tags = check_init('FrontTags', 'FrontTags');
            $this->Tags->ShowUsingTags($this->module, $this->id);
        }
        $tags = ob_get_clean();

        ob_start();
        if ($this->is_comments == 1) {
            if (!isset($this->Comments))
                $this->Comments = new FrontComments($this->module, $this->id);
            $this->Comments->ShowCommentsByModuleAndItem();
            $this->Comments->ShowCommentsCountLink();
        }
        $comments = ob_get_clean();

        echo View::factory('/modules/mod_gallery/templates/tpl_gallery_full.php')
                ->bind('multi',$this->multi)
                ->bind('images',$images)
                ->bind('short',$short)
                ->bind('tags',$tags)
                ->bind('comments',$comments);
    }// end of function ShowGalleryFull()


    function showUploadImages($pageId){
        $items = $this->UploadImages->GetPictureInArrayExSize($pageId, $this->lang_id, NULL, 64, 42, true, true, 85,null,460,307);
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
    // Function : GalleryCatLast()
    // Date :    02.03.2011
    // Returns : true/false
    // Description : Show Last video for $cat
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GalleryCatLast($id_cat = 3, $limit = 6, $caption = null) {
        $arr = $this->GetGalleryCatLast($id_cat, $limit);
        if (is_array($arr)) {
            $rows = count($arr);
            if (!$rows)
                return;
            if (!$caption)
                $caption = $this->multi['TXT_FRONT_TITLE_LATEST'];
            ?>
            <div class="last-category-video">
                <div class="news_colum1_1_title"><?= $caption ?></div>
                <?
                for ($i = 0; $i < $rows; $i++) {
                    $row = $arr[$i];
                    $name = strip_tags(stripslashes($row['name']));
                    //$short = $this->Crypt->TruncateStr(strip_tags(stripslashes($row['shrt'])),200);
                    //$short = strip_tags(stripslashes($row['shrt']));
                    /* $link = $this->Link($row['category'], $row['id']);
                      $link_cat = $this->Link( $value['cat_translit']); */
                    $link = $this->Link($row['cat_translit'], $row['translit']);
                    ?>
                    <div class="last-videos left">
                        <div class="time left"><?= $this->ConvertDate($row['dttm'], true); ?></div>
                        <a class="name left" href="<?= $link; ?>"><?= $name; ?></a>
                    </div>
                    <?
                }
                //$linkCat = $this->Link($id_cat);
                /* ?><a class="allNews" href="<?=$linkCat?>">Всі новини</a><? */
                ?>
                <div class="clear">&nbsp;</div>
            </div><?
            }
        }

// end of function GalleryCatLast()
        // ================================================================================================
        // Function : GalleryLast()
        // Date :    02.03.2011
        // Returns : true/false
        // Description : Show Last Photo Gallery
        // Programmer :  Yaroslav Gyryn
        // ================================================================================================
        function GalleryLast() {
            $row = $this->GetGalleryLast();
            if (!is_array($row))
                return;
            $name = strip_tags(stripslashes($row['name']));
            $short = $this->Crypt->TruncateStr(strip_tags(stripslashes($row['short'])), 300);
            $link = $this->Link($row['cat_translit'], $row['translit']);
            ?>
        <div class="captionChapter"><span><?= $this->multi['TXT_GALLERY_TITLE']; ?></span><span class="icoGallery">&nbsp;</span></div>
        <div class="image-last">
            <? /* <a href="<?=$link?>"  title="<?=$name;?>" ><?=$name;?></a> */ ?>
            <?
            $title = $name;
            $items = $this->UploadImages->GetPictureInArrayExSize($row['id'], $this->lang_id, NULL, 300, 194, true, true, 85, NULL, 273, 164);
            $items_keys = array_keys($items);
            $items_count = count($items);
            if ($items_count > 0) {
                $items_count = 1; // Ограничение для видео не более 1 картинки
                for ($j = 0; $j < $items_count; $j++) {
                    $alt = $items[$items_keys[$j]]['name'][$this->lang_id];      // Заголовок
                    $titleImg = $items[$items_keys[$j]]['text'][$this->lang_id]; // Описание
                    $path = $items[$items_keys[$j]]['path'];                    // Путь уменьшенной копии
                    //$path2 = $items[$items_keys[$j]]['path2'];                 // Путь большой копии
                    //$path_org = $items[$items_keys[$j]]['path_original'];   // Путь оригинального изображения
                    ?><a class="image" href="<?= $link; ?>" title="<?= $title; ?>" ><img src="<?= $path; ?>" alt="<?= $alt ?>" title="<?= $titleImg; ?>"/></a><?
            }
        } else {
                ?><a href="<?= $link; ?>" title="<?= $title; ?>"><img src="/images/design/no-image.jpg" width="273" height="164" alt="" /></a><?
        }
        /* <div class="vid_fot_desc"><?=$short;?></div>
          <div class="vid_fot_footer"><a href="/gallery/">Всі фоторепортажі</a></div> */
            ?>
        </div>
        <?
    }

// end of function GalleryLast()
// ================================================================================================
// Function : GetMap()
// Date : 01.07.2010
// Parms :
// Returns :      true,false / Void
// Description :  Show Gallery   Full
// Programmer :  Yaroslav Gyryn
// ================================================================================================
    function showModuleSiteMap() {
        $db1 = new DB();

        $q = "SELECT *
        FROM `" . TblModGalleryCat . "`
        WHERE `lang_id`='" . _LANG_ID . "'
        ORDER BY `cod` ASC
 ";
        //echo '$q = '.$q;
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        ?>
        <ul>
            <?
            for ($i = 0; $i < $rows; $i++) {
                $row = $this->db->db_FetchAssoc();
                ?><li><a href="<?= $this->Link($row['translit']); ?>"><?= $row['name']; ?></a></li><?
            $q1 = "SELECT
            `" . TblModGallery . "`.id,
            `" . TblModGallery . "`.category,
            `" . TblModGalleryTxt . "`.name,
            `" . TblModGalleryTxt . "`.translit
          FROM `" . TblModGallery . "` ,`" . TblModGalleryTxt . "`
          WHERE
            `" . TblModGallery . "`.category ='" . $row['cod'] . "'
          AND
            `" . TblModGallery . "`.id = `" . TblModGalleryTxt . "`.cod
          AND
            `" . TblModGalleryTxt . "`.lang_id = '" . $this->lang_id . "'
          AND
           `" . TblModGallery . "`.status='a'
           AND
           `" . TblModGalleryTxt . "`.name != ''
          ORDER BY
            `" . TblModGallery . "`.dttm DESC
   ";
            $res1 = $db1->db_Query($q1);
            $rows1 = $db1->db_GetNumRows();
            if ($rows1) {
                echo '<ul>';
                for ($j = 0; $j < $rows1; $j++) {
                    $row1 = $db1->db_FetchAssoc();
                    echo '<li><a href="' . $this->Link($row['translit'], $row1['translit']) . '">' . stripslashes($row1['name']) . '</a></li>';
                }
                echo '</ul>';
            }
        }
            ?>
        </ul>
            <?
        }

// ================================================================================================
        function ShowGalleryLast($cnt = 5) {
            $q = "select `" . TblModGallery . "`.*,`" . TblModGalleryTxt . "`name from  `" . TblModGallery . "`,`" . TblModGalleryTxt . "`
   where `" . TblModGallery . "`.status='a'
   AND `" . TblModGallery . "`.id=" . TblModGalleryTxt . "`.cod
   AND `" . TblModGalleryTxt . "`.lang_id='" . $this->lang_id . "'
   order by position desc limit " . $cnt;
            $res = $this->db->db_Query($q);
            $rows = $this->db->db_GetNumRows();
            ?><table border="0" cellspacing="0" cellpadding="0"><?
        for ($i = 0; $i < $rows; $i++) {
            $row = $this->db->db_FetchAssoc($res);
                ?>
                <tr>
                    <td class="b_marker"><img src="/images/design/block_mark.gif" /></td>
                    <td><a href="<?= $this->Link($row['category'], $row['id']); ?>"><?= $row['name']; ?></a>
                        <br><span class="time"><?= $this->ConvertDate($row['dttm'], true); ?></span></td>
                </tr>
                <tr>
                    <td colspan="2" class="b_spacer"></td>
                </tr>
            <?
        }
        ?></table><?
    }

//
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
    function showErr() {
        if ($this->Err) {
            ?>
            <table border=0 cellspacing=10 cellpadding=0 class="err" width="50%" align=center>
                <tr><td><h2><?= $this->Msg->show_text('MSG_ERR', TblSysTxt); ?></h2></td></tr>
                <tr><td><?= $this->Err; ?></td></tr>
            </table>
            <?
        }
    }

//end of fuinction ShowErr()
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
    function ShowTextMessages($text = NULL) {
        echo "<H3 align=center class='msg'>$text</H3>";
    }

//end of function ShowTextMessages()
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
    function showArticlePath() {
        ?>
        <div class="navigation">
            <a href="/"><?= SITE_NAME ?></a> »
        <? if (empty($this->id) and empty($this->category)) { ?>
                Статьи
        <? } else { ?>
                <a href="/gallery/">Статьи</a>
            <? } ?>
            <? if (!empty($this->category) and empty($this->id)) { ?>
                » <?= $this->Spr->GetNameByCod(TblModGalleryCat, $this->category); ?>
            <?
            } else {
                if (!empty($this->category)) {
                    ?>
                    » <a href="<?= $this->Link($this->category); ?>"><?= $this->Spr->GetNameByCod(TblModGalleryCat, $this->category); ?></a>
                <? } ?>
            <? } ?>
            <? if (!empty($this->id)) { ?>
                » <?= strip_tags($this->Spr->GetNameByCod(TblModGalleryTxt, $this->id)); ?>
            <? } ?>
        </div>
            <?
        }

        // ================================================================================================
        // Function : ShowSearchResult()
        // Date :    01.07.2010
        // Parms :   $id - poll id
        // Returns : true/false
        // Description : Show Saerch form for search in the gallery
        // Programmer : Yaroslav Gyryn
        // ================================================================================================
        function showSearchResult($array) {
            $rows = count($array);
            for ($i = 0; $i < $rows; $i++) {
                $value = $array[$i];
                $row['name'] = stripslashes($value['sbj']);
                $row['date'] = $this->ConvertDate($value['start_date']);
                //$row['short'] = $this->Crypt->TruncateStr(strip_tags(stripslashes($value['shrt'])), 280);
                $row['short'] = stripslashes($value['shrt']);
                //$row['link_cat'] = $this->Link($value['cat_translit']);
                $row['link'] = $this->Link($value['cat_translit'], $value['translit']);

                $items = $this->UploadImages->GetPictureInArrayExSize($value['id'], $this->lang_id, NULL, 336, 246, true, true, 85);
                $items_keys = array_keys($items);
                $items_count = count($items);
                if ($items_count > 0) {
                    $row['img']['alt'] = $items[$items_keys[0]]['name'][$this->lang_id];      // Заголовок
                    $row['img']['title'] = $items[$items_keys[0]]['text'][$this->lang_id]; // Описание
                    $row['img']['path'] = $items[$items_keys[0]]['path'];                    // Путь уменьшенной копии
                    //$path2 = $items[$items_keys[0]]['path2'];                 // Путь большой копии
                    //$path_org = $items[$items_keys[0]]['path_original'];   // Путь оригинального изображения
                }else{
                    $row['img']['path'] = '/images/design/no-image.jpg';
                    $row['img']['title'] = '';
                    $row['img']['alt'] = '';
                }
                if($i%2==0)
                    $row['class'] = ' left';
                else
                    $row['class'] = ' right';
                $gallerys[$i] = $row;
            }
            $pages = '';

            echo View::factory('/modules/mod_gallery/templates/tpl_gallery_by_pages.php')
                ->bind('gallerys', $gallerys)
                ->bind('multi', $this->multi)
                ->bind('pages', $pages);
            }

// end of function ShowSearchForm
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
    function GetCategoryIdByTranslit($translit = null) {
        $q = "select *
        from `" . TblModGalleryCat . "`
        where  BINARY `translit`= BINARY '" . $translit . "'
        AND `" . TblModGalleryCat . "`.`lang_id` = '".$this->lang_id."' ";
        $res = $this->db->db_query($q);
        //echo '<br>$q='.$q.' $res='.$res;
        if (!$res || !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        if ($rows==0){
            $q = "select *
            from `" . TblModGalleryCat . "`
            where  BINARY `translit`= BINARY '" . $translit . "'";
            $res = $this->db->db_query($q);
            //echo '<br>$q='.$q.' $res='.$res;
            if (!$res || !$this->db->result) return false;
            $row = $this->db->db_FetchAssoc();
            $this->lang_id_for_redirect = $row['lang_id'];
        }else{
            $row = $this->db->db_FetchAssoc();
        }
        return $row['cod'];
    }

//end of function  GetCategoryIdByTranslit()
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
    function GetCategoryTranslitByCod($cod = null) {
        $lang = _LANG_ID;
        $q = "select translit from `" . TblModGalleryCat . "` where  `cod`= '" . $cod . "' AND `lang_id`= '" . $lang . "' ";
        $res = $this->db->db_query($q);
//     echo $q;
        if (!$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        if ($rows == 0)
            return false;

        $row = $this->db->db_FetchAssoc();
        return $row['translit'];
    }

//end of function  GetCategoryTranslitByCod()
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
    function GetPositionIdByTranslit($position = null, $cat = null) {
        $q = "SELECT `" . TblModGallery . "` .id as id_prop
            FROM `" . TblModGalleryTxt . "` ,`" . TblModGallery . "`
            WHERE
                BINARY `translit` = BINARY '" . $position . "'
                AND
                    `" . TblModGallery . "`.id = `" . TblModGalleryTxt . "`.cod
                AND
                    `" . TblModGalleryTxt . "`.lang_id='" . $this->lang_id . "'";
        if ($cat != NULL)
            $q = $q . " AND `" . TblModGallery . "`.category='" . $cat . "'";

        $res = $this->db->db_query($q);
        //echo '<br>$q='.$q.' $res='.$res;
        if (!$res || !$this->db->result) return false;
        $rows = $this->db->db_GetNumRows();
        if ($rows == 0){
            $q = "SELECT `" . TblModGallery . "` .id as id_prop, `" . TblModGalleryTxt . "` .`lang_id`
            FROM `" . TblModGalleryTxt . "` ,`" . TblModGallery . "`
            WHERE
                BINARY `translit` = BINARY '" . $position . "'
                AND
                    `" . TblModGallery . "`.id = `" . TblModGalleryTxt . "`.cod";
            if ($cat != NULL)
                $q = $q . " AND `" . TblModGallery . "`.category='" . $cat . "'";
            $res = $this->db->db_query($q);
            //echo '<br>$q='.$q.' $res='.$res;
            if (!$res || !$this->db->result) return false;
            $row = $this->db->db_FetchAssoc();
            if($row['lang_id']!=$this->lang_id) $this->lang_id_for_redirect = $row['lang_id'];
        }else{
            $row = $this->db->db_FetchAssoc();
        }
        return $row['id_prop'];
    }

//end of function  GetPositionIdByTranslit()
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

    function GetIdTranslitByCod($position = null, $cat = null) {
        $lang_id = _LANG_ID;
        $q = "SELECT
                    `" . TblModGalleryTxt . "` .translit
            FROM `" . TblModGalleryTxt . "` ,`" . TblModGallery . "`
            WHERE
                BINARY `translit` = BINARY '" . $position . "'
                AND
                    `" . TblModGallery . "`.id = `" . TblModGalleryTxt . "`.cod
                ";
        if (!empty($lang_id))
            $q = $q . " AND `" . TblModGalleryTxt . "`.lang_id='" . $lang_id . "'";
        if ($id_cat != NULL)
            $q = $q . " AND `" . TblModGallery . "`.category='" . $cat . "'";

        $res = $this->db->db_query($q);
//     echo $q;
        if (!$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        if ($rows == 0)
            return false;

        $row = $this->db->db_FetchAssoc();
        return $row['translit'];
    }

//end of function  GetIdTranslitByCod()
    // ================================================================================================
    // Function : ShowGalleryLink()
    // Version : 1.0.0
    // Date : 01.07.2010
    // Parms :
    // Returns : true,false / Void
    // Description : Show Link to Gallery Chapter
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function ShowGalleryLink() {
            ?><div class="leftBlockHead"><?= $this->multi['TXT_GALLERY_TITLE']; ?></div>
        <div class="galleryBlock">
        <?
        $link = _LINK . 'gallery/2010/';
        $items = $this->UploadImages->GetFirstRandomPictureInArray($this->lang_id, 'size_width= 223', null);
        $items_keys = array_keys($items);
        $items_count = count($items);
        if ($items_count > 0) {
            /* $alt= $items[$items_keys]['name'][$this->lang_id];  // Заголовок
              $title= $items[$items_keys]['text'][$this->lang_id];  // Описание */
            $path = $items[$items_keys[0]]['path'];                    // Путь уменьшенной копии
            //$path_org = $items[$items_keys['path_original'];   // Путь оригинального изображения
            ?><a href="<?= $link; ?>" title="<?= $this->multi['TXT_GALLERY_TITLE']; ?>" alt="<?= $this->multi['TXT_GALLERY_TITLE']; ?>"><img src="<?= $path; ?>" alt="<?= $this->multi['TXT_GALLERY_TITLE']; ?>" title="<?= $this->multi['TXT_GALLERY_TITLE'];
            ; ?>"></a><?
        }
        /* ?>
          <a href="<?=$link?>" title="<?=$this->multi['TXT_GALLERY_TITLE'];?>"><img src="/images/design/videoSmall.jpg"></a> */
        ?>
        </div><?
    }

    function showPath($tmp_path = NULL){
        $devider = '&nbsp•&nbsp';
        ob_start();
        echo $tmp_path;
            switch( $this->task ){
               case 'position':
                       if(!empty ($this->id)) {
                           $link = $this->Link($this->cat, NULL);
                           echo $devider;
                           ?><a href="<?=$link?>"><?=$this->Spr->GetNameByCod( TblModGalleryCat, $this->cat);?></a><?
                           echo $devider;
                           echo $this->Spr->GetNameByCod( TblModGalleryTxt, $this->id);
                        }
                       break;
               default:
                       if(!empty($this->cat))
                           echo $devider.$this->Spr->GetNameByCod( TblModGalleryCat, $this->cat);
                       else

                       break;
            }
       return ob_get_clean();
    }

}

//end of class galleryLayout
?>
