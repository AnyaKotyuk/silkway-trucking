<?
/**
 * article.class.php
 * Class definition for Article
 * @package Catalog Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 21.06.2013
 * @copyright (c) 2010+ by SEOTM
 */

/**
 * Class Article
 * @property db $db
 * @property TblFrontMulti $multi
 * @property PageUser $PageUser
 */
class Article {

    public $id;
    public $dttm;
    public $category;
    public $status;
    public $img;
    public $position;
    public $name;
    public $short;
    public $full;
    public $Right;
    public $Form;
    public $Msg;
    public $Spr;
    public $page;
    public $display;
    public $sort;
    public $start;
    public $rows;
    public $user_id;
    public $use_image;
    public $module;
    public $task = NULL;
    public $fltr;    // filter of group Production
    public $lang_id;
    public $sel = NULL;
    public $Err = NULL;
    public $title;
    public $keywords;
    public $description;
    public $str_cat;
    public $str_art;
    public $settings = null;

    /**
     * Class Constructor
     * Set the variabels
     * @return true/false
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 21.06.2013
     */
    function Article($lang_id) {
        if(empty($lang_id)){
            if( defined("_LANG_ID") ) $this->lang_id = _LANG_ID;
        }else{
            $this->lang_id = $lang_id;
        }
        $this->Right = new Rights;                   /* create Rights obect as a property of this class */
        $this->Form = new Form('form_art');        /* create Form object as a property of this class */
        $this->Msg = new ShowMsg();                   /* create ShowMsg object as a property of this class */
        $this->Msg->SetShowTable(TblModArticleSprTxt);
        $this->use_image = 1;
        if(empty($this->Spr)){
            $this->Spr = check_init('SysSpr', 'SysSpr');
        }

        $this->settings = $this->GetSettings();
    }

// end of Article (Constructor)

    /**
     * Article::getImagesCount()
     * return count of images for current article with $id
     * @params string $task - name of task
     * @return integer $rows - count of images
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 21.06.2013
     */
    function getImagesCount($id_art) {
        $image = NULL;
        $tmp_db = DBs::getInstance();

        $q = "SELECT * FROM `" . TblModArticleImg . "` WHERE 1 AND `id_art`='" . $id_art . "' order by `move`";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res or !$tmp_db->result)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        return $rows;
    }

//end of function GetImagesCount()
    // ================================================================================================
    // Function : GetImages
    // Version : 1.0.0
    // Date : 13.10.2006
    //
   // Parms : $id_art  / id of the article
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 13.10.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetImages($id_art) {
        $image = NULL;
        $tmp_db = DBs::getInstance();

        $q = "SELECT * FROM `" . TblModArticleImg . "` WHERE 1 AND `id_art`='" . $id_art . "' order by `move`";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res or !$tmp_db->result)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $arr = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $tmp_db->db_FetchAssoc();
            //echo '<br>$row[id_val]'.$row['id_val'];
            $arr[$i] = $row['path'];
        }
        return $arr;
    }

//end of function GetImages()
    // ================================================================================================
    // Function : GetImagesToShow
    // Version : 1.0.0
    // Date : 13.10.2006
    //
   // Parms : $id_art  / id of the user
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 13.10.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetImagesToShow($id_art) {
        $image = NULL;
        $tmp_db = DBs::getInstance();

        $q = "SELECT * FROM `" . TblModArticleImg . "` WHERE 1 AND `id_art`='" . $id_art . "' AND `show`=1 order by `move`";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res or !$tmp_db->result)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $arr = NULL;
        for ($i = 0; $i < $rows; $i++) {
            $row = $tmp_db->db_FetchAssoc();
            //echo '<br>$row[id_val]'.$row['id_val'];
            $arr[$i]['id'] = $row['id'];
            $arr[$i]['path'] = $row['path'];
            $arr[$i]['descr'] = $row['descr'];
            $arr[$i]['move'] = $row['move'];
            $arr[$i]['show'] = $row['show'];
        }
        return $arr;
    }

//end of function GetImagesToShow()
    // ================================================================================================
    // Function : GetMainImage
    // Version : 1.0.0
    // Date : 13.10.2006
    //
   // Parms :   $id_art    / id of the user
    //           $part       /  for front-end or for back-end
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 13.10.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetMainImage($id_art, $part = 'front') {
        $image = NULL;
        $tmp_db = DBs::getInstance();

        $q = "SELECT * FROM `" . TblModArticleImg . "` WHERE 1 AND `id_art`='" . $id_art . "'";
        if ($part == 'front')
            $q = $q . " AND `show`=1";
        $q = $q . " order by `move`";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res or !$tmp_db->result)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $row = $tmp_db->db_FetchAssoc();
        return $row['path'];
    }

//end of function GetMainImage()
    // ================================================================================================
    // Function : GetMainImageData
    // Version : 1.0.0
    // Date : 13.10.2006
    //
   // Parms :   $id_art    / id of the user
    //           $part       /  for front-end or for back-end
    // Returns : return $image for current value with cod=$cod
    // Description : return image for current value with cod=$cod, if it is exist
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 13.10.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetMainImageData($id_art, $part = 'front') {
        $image = NULL;
        $tmp_db = DBs::getInstance();

        $q = "SELECT * FROM `" . TblModArticleImg . "` WHERE 1 AND `id_art`='" . $id_art . "'";
        if ($part == 'front')
            $q = $q . " AND `show`=1";
        $q = $q . " order by `move`";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res or !$tmp_db->result)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $row = $tmp_db->db_FetchAssoc();
        return $row;
    }

//end of function GetMainImageData()

    /**
     * Class method ShowImage
     * function for show image
     * @param $img - id of the picture, or relative path of the picture /images/mod_articles/24094/12984541610.jpg or name of the picture 12984541610.jpg
     * @param $id_art - id of the news
     * @param $size - Can be "size_auto" or  "size_width" or "size_height"
     * @param $quality - quality of the image from 0 to 100
     * @param $wtm - make watermark or not. Can be "txt" or "img"
     * @param $parameters - other parameters for TAG <img> like border
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.1, 20.10.2011
     * @return true or false
     */
    function ShowImage($img = NULL, $id_art, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL, $return_src = false) {
        if (!strstr($img, '.') AND !strstr($img, '/')) {
            $img_data = $this->GetPictureData($img);
            if (!isset($img_data['id_art'])) {
                return false;
            }
            $settings_img_path = $this->settings['img_path'] . '/' . $img_data['id_art']; // like /uploads/45
            $img_name = $img_data['path'];  // like R1800TII_big.jpg
            $img_with_path = $settings_img_path . $img_name; // like /uploads/45/R1800TII_big.jpg
            if (!strstr($parameters, 'alt'))
                $alt = $this->GetPictureAlt($img);
            if (!strstr($parameters, 'title'))
                $title = $this->GetPictureTitle($img);
        }
        else {
            $rpos = strrpos($img, '/');
            if ($rpos > 0) {
                $settings_img_path = substr($img, 0, $rpos);
                $img_name = substr($img, $rpos + 1, strlen($img) - $rpos);
                $img_with_path = $img;
            } else {
                if (!$id_art)
                    return false;
                $settings_img_path = $this->settings['img_path'] . '/' . $id_art; // like /uploads/45
                $img_name = $img;
                $img_with_path = $settings_img_path . '/' . $img;
            }
            $alt = '';
            $title = '';
        }
        $imgSmall = ImageK::getResizedImg($img_with_path, $size, $quality, $wtm);
        if(empty($imgSmall)){
            return false;
        }
        if($return_src){
            return $imgSmall;
        }else{
            return '<img src="'.$imgSmall.'" '.$parameters.' />';
        }
    }

    // ================================================================================================
    // Function : GetExtationOfFile
    // Version : 1.0.0
    // Date : 31.08.2009
    //
    // Parms :  $filename - name of the image
    // Returns : $res / Void
    // Description : return extenation of file
    // ================================================================================================
    // Programmer : Oleg Morgalyuk
    // Date : 31.08.2009
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetExtationOfFile($filename) {
        return $ext = substr($filename, 1 + strrpos($filename, "."));
    }

// end of function GetExtationOfFile()
    // ================================================================================================
    // Function : GetImgFullPath
    // Version : 1.0.0
    // Date : 06.11.2006
    //
    // Parms :  $img - name of the image
    //          $id_art - id of the user
    // Returns : $res / Void
    // Description : return path to the image like /images/mod_art/120/1162648375_0.jpg
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 06.11.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetImgFullPath($img = NULL, $id_art = NULL) {
        return ArticleImg_Full_Path . $id_art . '/' . $img;
    }

//end of function GetImgFullPath()
    // ================================================================================================
    // Function : GetImgPath
    // Date : 01.03.2011
    // Parms :  $img - name of the image
    //          $id_art - id of the article
    // Returns : $res / Void
    // Description : return path to the image like /images/mod_article/120/1162648375_0.jpg
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetImgPath($img = NULL, $id_art = NULL) {
        if (!isset($this->settings))
            $this->settings = $this->GetSettings();
        return $this->settings['img_path'] . '/' . $id_art . '/' . $img; // like /uploads/45;
    }

//end of function GetImgPath()
    // ================================================================================================
    // Function : GetPictureData
    // Version : 1.0.0
    // Date : 03.04.2006
    //
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return array with path to the pictures of current product
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 03.04.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetPictureData($id_img) {
        $tmp_db = DBs::getInstance();

        $q = "SELECT `name`,`descr` FROM `" . TblModArticleImgSpr . "` WHERE `cod`='" . $id_img . "' and `lang_id`='" . $this->lang_id . "'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$tmp_db->result)
            return false;
        $row = $tmp_db->db_FetchAssoc();
        //echo "<br> row['id_prop']=".$row['id_prop'];
        return $row;
    }

// end of function GetPictureData()
    // ================================================================================================
    // Function : GetPictureAlt
    // Version : 1.0.0
    // Date : 19.05.2006
    //
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return alt for this image
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date :  19.05.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetPictureAlt($img, $show_name = true) {

        if (strstr($img, '.')) {
            $id_img = $this->GetImgIdByPath($img);
        } else {
            $id_img = $img;
        }

        // echo "<br>id_img=".$id_img;
        $alt = $this->Spr->GetNameByCod(TblModArticleImgSpr, $id_img, _LANG_ID, 1);
        // echo '<br>$alt='.$alt;
        if (empty($alt) and $show_name) {
            $tmp_db = DBs::getInstance();
            $q = "SELECT `id_art` FROM `" . TblModArticleImg . "` WHERE `id`='" . $id_img . "'";
            $res = $tmp_db->db_Query($q);
            // echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            if (!$res OR !$tmp_db->result)
                return false;
            $row = $tmp_db->db_FetchAssoc();

            $alt = $this->Spr->GetNameByCod(TblModArticleImgSpr, $row['id_art'], _LANG_ID, 1);
            //$id_cat = $this->GetCategory($row['id_prop']);
            //echo '<br>$id_cat='.$id_cat;
            //$name_ind = $this->Spr->GetNameByCod(TblModCatalogSprNameInd, $id_cat, $this->lang_id, 1 );
            // $alt = $name_ind.' '.$alt;
        }

        //  echo '<br> $alt='.$alt;
        return htmlspecialchars($alt);
    }

    function GetMainImageDataAll($id_art, $part = 'front') {
        $q = "SELECT * FROM `" . TblModArticleImg . "` WHERE 1 AND `id_art`='" . $id_art . "'";
        if ($part == 'front')
            $q = $q . " AND `show`=1";
        $q = $q . " order by `move`";
        $res = $this->db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if (!$res or !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        return $rows;
    }

// end of function GetPictureAlt()
    // ================================================================================================
    // Function : GetPictureTitle
    // Version : 1.0.0
    // Date : 19.05.2006
    //
    // Parms :  $id_img - id of the image
    // Returns : $res / Void
    // Description : return title for this image
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date :  19.05.2006
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function GetPictureTitle($img) {
        if (strstr($img, '.')) {
            $id_img = $this->GetImgIdByPath($img);
        } else {
            $id_img = $img;
        }
        $tmp_db = DBs::getInstance();

        $q = "SELECT `descr` FROM `" . TblModArticleImgSpr . "` WHERE `cod`='" . $id_img . "' and `lang_id`='" . $this->lang_id . "'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$tmp_db->result)
            return false;
        $row = $tmp_db->db_FetchAssoc();
        $alt = htmlspecialchars($row['descr']);
        //echo '<br>$alt='.$alt;
        if (empty($alt)) {
            $alt = $this->GetPictureAlt($id_img);
        }
        // echo '<br> $title='.$alt;
        return $alt;
    }

// end of function GetPictureTitle()
    // ================================================================================================
    // Function : GetImgTitleByPath
    // Date : 06.11.2006
    // Parms :  $img - name of the picture
    // Returns : $res / Void
    // Description : return title for image
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetImgIdByPath($img) {
        $tmp_db = DBs::getInstance();

        $q = "SELECT * FROM `" . TblModArticleImg . "` WHERE 1 AND `path`='$img'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
        if (!$res or !$tmp_db->result)
            return false;
        //$rows = $tmp_db->db_GetNumRows();
        // echo '<br>$rows='.$rows;
        $row = $tmp_db->db_FetchAssoc();
        $id = $row['id'];
        return $id;
    }

//end of function GetImgTitleByPath()
    // ================================================================================================
    // Function : upImg()
    // Date : 4.04.2007
    // Returns :      true,false / Void
    // Description :  Up position
    // Programmer :  Andriy Lykhodid
    // ================================================================================================
    function upImg($table, $level_name = NULL, $level_val = NULL) {
        $tmp_db = DBs::getInstance();
        $q = "select `move`,`id` from `$table` where `move`='$this->move'";
        if (!empty($level_name))
            $q = $q . " AND `$level_name`='$level_val'";
        $res = $tmp_db->db_Query($q);
//        echo '<br>q='.$q.' res='.$res; // $this->Right->result='.$this->db->rest;
        if (!$res)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        $move_down = $row['move'];
        $id_down = $row['id'];

        $q = "select `move`,`id` from `$table` where `move`<'$this->move'";
        if (!empty($level_name))
            $q = $q . " AND `$level_name`='$level_val'";
        $q = $q . " order by `move` desc";
        $res = $tmp_db->db_Query($q);
//        echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        $move_up = $row['move'];
        $id_up = $row['id'];

        //echo '<br> $move_down='.$move_down.' $id_down ='.$id_down.' $move_up ='.$move_up.' $id_up ='.$id_up;
        if ($move_down != 0 AND $move_up != 0) {
            $q = "update `$table` set
             `move`='$move_down' where id='$id_up'";
            $res = $tmp_db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

            $q = "update `$table` set
             `move`='$move_up' where id='$id_down'";
            $res = $tmp_db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        }
    }

// end of function up()
    // ================================================================================================
    // Function : downImg()
    // Date : 4.04.2007
    // Returns :      true,false / Void
    // Description :  Down position
    // Programmer :  Andriy Lykhodid
    // ================================================================================================
    function downImg($table, $level_name = NULL, $level_val = NULL) {
        $tmp_db = DBs::getInstance();
        $q = "select * from `$table` where `move`='$this->move'";
        if (!empty($level_name))
            $q = $q . " AND `$level_name`='$level_val'";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        $move_up = $row['move'];
        $id_up = $row['id'];


        $q = "select * from `$table` where `move`>'$this->move'";
        if (!empty($level_name))
            $q = $q . " AND `$level_name`='$level_val'";
        $q = $q . " order by `move` asc";
        $res = $tmp_db->db_Query($q);
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if (!$res)
            return false;
        $rows = $tmp_db->db_GetNumRows();
        $row = $tmp_db->db_FetchAssoc();
        $move_down = $row['move'];
        $id_down = $row['id'];

        if ($move_down != 0 AND $move_up != 0) {
            $q = "update `$table` set
             `move`='$move_down' where id='$id_up'";
            $res = $tmp_db->db_Query($q);
            // echo '<br>q='.$q.' res='.$res;
            $q = "update `$table` set
             `move`='$move_up' where id='$id_down'";
            $res = $tmp_db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res;
        }
    }

// end of function down()
    // ================================================================================================
    // Function : GetArticleData()
    // Date : 20.09.2009
    // Returns :      true,false / Void
    // Description :  Return news data
    // Programmer :  Ihor Trokhymchuk
    // ================================================================================================
    function GetArticleData($art_id = NULL) {
        if (!$art_id)
            return true;
        //$q = "select * from ".TblModNews." where id='$news_id'";
        $q = "SELECT `" . TblModArticle . "`.*, `" . TblModArticleCat . "`.name AS `cat_name`, `" . TblModArticleTxt . "`.name AS `sbj`, `" . TblModArticleTxt . "`.short AS `shrt_art`, `" . TblModArticleTxt . "`.full AS `full_art`
              FROM `" . TblModArticle . "`, `" . TblModArticleCat . "`, `" . TblModArticleTxt . "`
              WHERE `" . TblModArticle . "`.id='" . $art_id . "'
              AND `" . TblModArticle . "`.category=`" . TblModArticleCat . "`.cod
              AND `" . TblModArticleCat . "`.lang_id='" . $this->lang_id . "'
              AND `" . TblModArticle . "`.id=`" . TblModArticleTxt . "`.cod
              AND `" . TblModArticleTxt . "`.lang_id='" . $this->lang_id . "'
             ";
        if (!empty($this->fltr)) {
            $q .= $this->fltr;
        }
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
        if (!$res OR !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        return $rows;
    }

//end of fuinction GetNewsData()
    // ================================================================================================
    // Function : ConvertDate()
    // Date : 07.02.2005
    // Returns :      true,false / Void
    // Description :  Convert Date to nidle format
    // Programmer :  Ihor Trokhymchuk
    // ================================================================================================
    function ConvertDate($date_to_convert, $time_only = false) {
        $settings = $this->settings;

        //print_r($tmp = explode("-", $date_to_convert));
        $tmp = explode("-", $date_to_convert);
        $tmp2 = explode(" ", $tmp[2]);
        $m_word = NULL;
        $month = NULL;
        $day = NULL;
        $year = NULL;
        $time = NULL;

        if ($time_only)
            return $tmp2[1];

        //$month = $this->Spr->GetShortNameByCod(TblSysSprMonth, intval($tmp[1]), $this->lang_id, 1);
        $month = $tmp[1];
        $day = intval($tmp2[0]);
        $year = $tmp[0];
        $time = $tmp2[1];

        if (isset($settings['dt']) AND $settings['dt'] == '0') {
            return $day . " " . $month . ", " . $year . " г.";
        }
        return $day . "." . $month . "." . $year;
    }

// end of function ConvertDate
    // ================================================================================================
    // Function : Link()
    // Date : 12.01.2011
    // Description : Return Link
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function Link($cat = NULL, $id = NULL, $lang_id=NULL) {
        if (!isset($this->settings)){
            $this->settings = $this->GetSettings();
        }
        if (!defined("_LINK")) {
            $Lang = new SysLang(NULL, "front");
            $tmp_lang = $Lang->GetDefFrontLangID();
            if (($Lang->GetCountLang('front') > 1 OR isset($_GET['lang_st'])) AND $this->lang_id != $tmp_lang) {
                define("_LINK", "/" . $Lang->GetLangShortName($this->lang_id) . "/");
            } else {
                define("_LINK", "/");
            }
            $prefLink = _LINK;
        }

        if(empty($lang_id)){
            $lang_id = $this->lang_id;
            $prefLink = _LINK;
        }else{
            $Lang = check_init('SysLang', 'SysLang', 'NULL, "front"');
            if($lang_id!=$Lang->GetDefFrontLangID()){
                $prefLink = "/".$Lang->GetLangShortName($lang_id)."/";
            }else{
                $prefLink = "/";
            }
        }

        if (!empty($cat)) {
            $str_cat = $this->Spr->GetTranslitByCod(TblModArticleCat, $cat, $lang_id);
        } elseif (!empty($id)) {
            $str_cat = $this->Spr->GetTranslitByCod(TblModArticleCat, $this->GetIdCatByIdArt($id), $lang_id);
        } else {
            $str_cat = NULL;
        }
        $str_art = $this->GetLink($id);
        //echo '<br>$cat='.$cat.' $id='.$id;

        /*
          if($id!=NULL and $str_art==''){
          $str_art=$this->SetLink($id, true);
          }
         */
        if ($id == null) {
            if (!empty($str_cat))
                $link = $prefLink . 'articles/' . $str_cat . '/';
            else {
                if ($this->task == 'showa')
                    $link = $prefLink . 'articles/last/';
                elseif ($this->task == 'showall')
                    $link = $prefLink . 'articles/all/';
                elseif ($this->task == 'arch')
                    $link = $prefLink . 'articles/arch/';
                else
                    $link = $prefLink . 'articles/';
            }
        }
        else
            $link = $prefLink . 'articles/' . $str_cat . '/' . $str_art . '.html';
        return $link;
    }
    // end of function Link()

    /**
     * Article::makeMultiUrls()
     * make array with current URL fo different languages versions
     * @return array
     *        Format: array('en'=>'/en/catalog/tv/',
     *                      'ua'=>'/ua/catalog/televizory',
     *                      'ru'=>'/catalog/televizori');
     */
    function getMultiUrls(){
        $multiUrls = NULL;
        $SysLang = check_init('SysLang', 'SysLang', 'NULL, "front"');
        $mas_lang = $SysLang->GetLangData($this->lang_id, 'front');
        foreach($mas_lang as $k=>$v){
            if($v['cod']==$this->lang_id){
                $multiUrls[$SysLang->GetLangShortName($v['cod'])] = $this->Link($this->category, $this->id);
            }else{
                //создавать нужно именно новый обьект, чтобы сформировать в нем новые массивы с данными на нужной языковой версии.
                $Article = new Article($v['cod']);
                $multiUrls[$SysLang->GetLangShortName($v['cod'])] = $Article->Link($this->category, $this->id, $v['cod']);
                unset($Article);
            }
        }
        //var_dump($multiUrls);
        return $multiUrls;
    }

    // ================================================================================================
    // Function : GetRowsByName
    // Date : 04.02.2005
    // Parms :   $Table - name of table, from which will be select data
    //           $cod - cod of the record in the table where the name is searched
    //           $lang_id - id of the language
    //           $my_ret_val - parameter for returned value .( 1- return '' for empty records)
    // Returns : $res / Void
    // Description : Get the name from table by its cod on needed language
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetRowsByName($Table, $fiels_name, $cod, $lang_id = _LANG_ID) {
        if (empty($cod)) {
            if (!empty($my_ret_val))
                return '';
            else
                return $this->Msg->show_text('_VALUE_NOT_SET');
        }
        $tmp_db = DBs::getInstance();
        $array = false;
        if (is_array($fiels_name)) {
            $array = true;
            $fiels_name = implode(',', $fiels_name);
        }
        $q = "SELECT $fiels_name FROM `" . $Table . "` WHERE `cod`='" . addslashes($cod) . "' AND `lang_id`='" . $lang_id . "'";
        $res = $tmp_db->db_Query($q);
        //echo '<br> $q='.$q.'  $tmp_db->result='.$tmp_db->result;
        if (!$res OR !$tmp_db->result)
            return false;
        $row_res = $tmp_db->db_FetchAssoc();
        //echo '<br> $row_res[name]='.$row_res['name'];
        if ($array)
            return $row_res;
        else
            return $row_res[$fiels_name];
    }

//end of fuinction GetNameByCod

    /**
     * Article::IsLink()
     * Check existing of link in table
     * @param string $link - string with link
     * @return integer count of selected rows
     * @author Ihor Trokhymchuk
     * @version 1.0, 24.04.2012
     */
    function IsLink($link) {
        $q = "SELECT `" . TblModArticleLinks . "`.`id` FROM `" . TblModArticleLinks . "` WHERE `link`='" . $link . "'";
        $res = $this->db->db_Query($q);
        if (!$res OR !$this->db->result)
            return false;
        return $this->db->db_GetNumRows();
    }

// end of function IsLink()
    // ================================================================================================
    // Function : SetLink()
    // Date : 12.01.2009
    // Parms : $link - str for link, $cod - id position
    // Description : Set Link
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function SetLink($cod, $ret = false) {
        $Crypt = new Crypt();
        $db = DBs::getInstance();

        $cat_link = stripslashes($this->GetRowsByName(TblModArticleTxt, 'name', $cod));
        if ($cat_link == "") {
            $cat_link = stripslashes($this->GetRowsByName(TblModArticleTxt, 'name', $cod));
            if ($cat_link == "") {
                $ln_sys = new SysLang();
                $ln_arr = $ln_sys->LangArray(_LANG_ID);
                while ($el = each($ln_arr)) {
                    $lang_id = $el['key'];
                    $cat_link = stripslashes($this->GetRowsByName(TblModArticleTxt, 'name', $cod, $lang_id));
                    //echo '<br>$cat_link='.$cat_link.' $lang_id='.$lang_id;
                    if (!empty($cat_link))
                        break;
                }
            }
        }

        $link = $Crypt->GetTranslitStr(trim($cat_link));
        if ($this->IsLink($link))
            $link .= '-' . $cod;
        $q = "INSERT INTO `" . TblModArticleLinks . "` values(NULL,'" . $cod . "','" . $link . "')";
        $res = $db->db_Query($q);
        if (!$res)
            return false;
        if ($ret)
            return $link;
    }

// end of function SetLink
    // ================================================================================================
    // Function : GetLink()
    // Date : 12.01.2009
    // Description : Get link
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetLink($cod) {
        $tmp_db = DBs::getInstance();
        $q = "select `link` from " . TblModArticleLinks . " where `cod`='" . $cod . "'";
        $res = $tmp_db->db_Query($q);
        $rows = $tmp_db->db_GetNumRows();
        //echo "<br> q=".$q." res=".$res." rows=".$rows;
        $row = $tmp_db->db_FetchAssoc();
        return $row['link'];
    }

// end of function GetLink
    // ================================================================================================
    // Function : GetIdCatByIdArt()
    // Date : 13.05.2007
    // Parms :
    // Returns :
    // Description :
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetIdCatByIdArt($id) {
        $q = "select * from " . TblModArticle . " where 1 and `id`='" . $id . "'";
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        //echo "<br> q=".$q." res=".$res." rows=".$rows;
        $row = $this->db->db_FetchAssoc();
        return $row['category'];
    }

// end of function GetIdCatByIdArt
    // ================================================================================================
    // Function : GetIdArtByStrArt()
    // Date : 13.05.2007
    // Programmer : Ihor Trokhymchuk
    // ================================================================================================
    function GetIdArtByStrArt($str_art) {
        $q = "SELECT `" . TblModArticleLinks . "`.`cod`
              FROM `" . TblModArticleLinks . "`, `" . TblModArticle . "`, `" . TblModArticleCat . "`
              WHERE BINARY `" . TblModArticleLinks . "`.`link` = BINARY '" . $str_art . "'
              AND `" . TblModArticleLinks . "`.cod=`" . TblModArticle . "`.`id`
              AND `" . TblModArticle . "`.`category`=`" . TblModArticleCat . "`.`cod`
              AND `" . TblModArticleCat . "`.`cod`='" . $this->category . "'
              AND `" . TblModArticleCat . "`.`lang_id`='" . $this->lang_id . "'
             ";
        $res = $this->db->db_Query($q);
        $rows = $this->db->db_GetNumRows();
        //echo "<br>GetIdArtByStrArt  q=".$q." res=".$res." rows=".$rows;
        $row = $this->db->db_FetchAssoc();
        //echo "<br>ART q=".$q." res=".$res.' rows='.$rows.' cod='.$row['cod'];
        return $row['cod'];
    }

// end of function GetIdArtByStrArt
    //------------------------------------------------------------------------------------------------------------
    //---------------------------- FUNCTION FOR SETTINGS OF ARTICLES START ---------------------------------------
    //------------------------------------------------------------------------------------------------------------
    // ================================================================================================
    // Function : GetSettings()
    // Date : 27.03.2006
    // Returns : true,false / Void
    // Description : return all settings of Gatalogue
    // Programmer : Igor Trokhymchuk
    // ================================================================================================
    function GetSettings($front = true) {
        $db = DBs::getInstance();
        $q = "select * from `" . TblModArticleSet . "` where 1";
        $res = $db->db_Query($q);
        //echo "<br /> q = ".$q." res = ".$res;
        if (!$db->result)
            return false;
        $row = $db->db_FetchAssoc();
        if ($front) {
            $q1 = "select * from `" . TblModArticleSetSprMeta . "` where `lang_id`='$this->lang_id' ";
            $res1 = $db->db_Query($q1);
            //echo "<br /> q = ".$q." res = ".$res;
            if (!$db->result)
                return false;
            $row1 = $db->db_FetchAssoc();
            $row['title'] = $row1['title'];
            $row['keywords'] = $row1['keywords'];
            $row['description'] = $row1['description'];
        }
        return $row;
    }

// end of function GetSettings()

    /**
     * Articles::SetMetaData()
     * @author Yaroslav
     * @param mixed $page
     * @return
     */
    function SetMetaData($page) {
        if (!isset($this->FrontendPages))
            $this->FrontendPages = Singleton::getInstance('FrontendPages');
        $this->FrontendPages->page_txt = $this->FrontendPages->GetPageTxt($page);
        $title = $this->FrontendPages->GetTitle();;
        $keywords = $this->FrontendPages->GetDescription();
        $description = $this->FrontendPages->GetKeywords();
        $name = $this->FrontendPages->page_txt['pname'];
        //echo '$name='.$name;
        if(!empty($this->id)){
            $q = "SELECT `".TblModArticleTxt."`.`name`,
                `".TblModArticleTxt."`.`title`,
                `".TblModArticleTxt."`.`keywords`,
                `".TblModArticleTxt."`.`description`,
                `".TblModArticleCat."`.name as cat_name
              FROM `".TblModArticleTxt."`,`".TblModArticle."`,`".TblModArticleCat."`
              WHERE `".TblModArticleTxt."`.cod='".$this->id."'
              AND `".TblModArticleTxt."`.lang_id='".$this->lang_id."'
              AND `".TblModArticle."`.id = `".TblModArticleTxt."`.cod
              AND `".TblModArticleCat."`.cod = `".TblModArticle."`.category
              AND `".TblModArticleCat."`.lang_id='".$this->lang_id."'";
            $res = $this->db->db_Query($q);
            //echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
            if (!$res OR !$this->db->result) return false;
            $row = $this->db->db_FetchAssoc();
            if(!empty($row['title'])) $title = $row['title'];
            else $title = $row['name'].' | '.$row['cat_name'].' | '.$name;

            if(!empty($row['keywords'])) $keywords = $row['keywords'];
            else $keywords = '';

            if(!empty($row['description'])) $description = $row['description'];
            else $description = '';
        }elseif(!empty($this->category)){
            $q = "SELECT `".TblModArticleCat."`.name ,
                `".TblModArticleCat."`.`mtitle`,
                `".TblModArticleCat."`.`mkeywords`,
                `".TblModArticleCat."`.`mdescr`
              FROM `".TblModArticleCat."`
              WHERE `".TblModArticleCat."`.cod = '".$this->category."'
              AND `".TblModArticleCat."`.lang_id='".$this->lang_id."'";
            $res = $this->db->db_Query($q);
    //        echo '<br>'.$q.' $res='.$res.' $this->db->result='.$this->db->result;
            if (!$res OR !$this->db->result) return false;
            $row = $this->db->db_FetchAssoc();
            if(!empty($row['mtitle'])) $title = $row['mtitle'];
            else $title = $row['name'].' | '.$name;

            if(!empty($row['mkeywords'])) $keywords = $row['mkeywords'];
            else $keywords = '';

            if(!empty($row['mdescription'])) $description = $row['mdescription'];
            else $description = '';
        }elseif($this->task=='last' || $this->task=='arch'){
            switch ($this->task) {
                case 'last': $title = $this->multi['TXT_META_TITLE_LAST'].' '.$name;;
                    break;
                case 'arch': $title = $this->multi['TXT_META_TITLE_ARCH'].' '.$name;;
                    break;
            }
        }else{
            if(empty($title)) $title = $name;
        }
        if ($this->page > 1) $title .= ' | Page'.$this->page;
        $this->title = $title;
        //echo '$this->title='.$this->title;
        $this->keywords = $keywords;
        $this->description = $description;
    }

//end of function  SetMetaData()
    // ================================================================================================
    // Function : GetNameArticle()
    // Date : 27.03.2008
    // Returns : true,false / Void
    // ================================================================================================
    function GetNameArticle($id) {
        $name = stripslashes($this->GetRowsByName(TblModArticleTxt, 'name', $id));
        return $name;
    }

    // ================================================================================================
    // Function : QuickSearch()
    // Date : 27.03.2008
    // Returns : true,false / Void
    // Description :
    // Programmer : Alex Kerest
    // ================================================================================================
    function QuickSearch($search_keywords) {
        $search_keywords = stripslashes($search_keywords);
        $sel_table = NULL;
        $str_like = NULL;
        $filter_cr = ' OR ';

        $str_like = $this->build_str_like_for_full(TblModArticleTxt . '.name', $search_keywords);
        $str_like .= $filter_cr . $this->build_str_like_for_full(TblModArticleTxt . '.full', $search_keywords);
        $sel_table = "`" . TblModArticle . "`, `" . TblModArticleTxt . "`, `" . TblModArticleCat . "`, `" . TblModArticleLinks . "` ";

        $q = "SELECT
                 `" . TblModArticle . "`.id,
                 `" . TblModArticle . "`.img,
                 `" . TblModArticle . "`.dttm,
                 `" . TblModArticle . "`.category as id_category,
                 `" . TblModArticleTxt . "`.name,
                 `" . TblModArticleTxt . "`.short,
                 `" . TblModArticleCat . "`.name as cat,
                 `" . TblModArticleCat . "`.translit as cat_translit,
                 `" . TblModArticle . "`.position,
                 `" . TblModArticleLinks . "`.link as art_link,
        (
            MATCH(`" . TblModArticleTxt . "`.`name`) AGAINST('*".$search_keywords."*' IN BOOLEAN MODE) * 10 +
            MATCH(`" . TblModArticleTxt . "`.`full`) AGAINST('*".$search_keywords."*' IN BOOLEAN MODE)
        ) AS `relev`
            FROM " . $sel_table . "
            WHERE (" . $str_like . ")
            AND `" . TblModArticle . "`.category = `" . TblModArticleCat . "`.cod
                  AND `" . TblModArticle . "`.id = `" . TblModArticleTxt . "`.cod
                  AND `" . TblModArticleTxt . "`.lang_id='" . $this->lang_id . "'
                  AND `" . TblModArticleCat . "`.lang_id='" . $this->lang_id . "'
                  AND `" . TblModArticleTxt . "`.name!=''
                  AND `" . TblModArticle . "`.id = `" . TblModArticleLinks . "`.cod
            ORDER BY  `" . TblModArticle . "`.dttm desc";

        $res = $this->db->db_Query($q);
//           echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$this->db->result;
        if (!$res || !$this->db->result)
            return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for($i = 0; $i < $rows; $i++) {
            $array[] = $this->db->db_FetchAssoc();
        }
        for ($i = 0; $i < $rows; $i++) {
            $row = $array[$i];
            $row['dttm'] = $this->ConvertDate($row['dttm']);
            $row['name'] = strip_tags(stripslashes($row['name']));
            $row['spec_name'] = htmlspecialchars($row['name']);
            $row['link'] = $this->Link($row['id_category'],$row['id']);
            $array[$i] = $row;
        }
        return $array;
    }

// end of function QuickSearch
    // ================================================================================================
    // Function : build_str_like
    // Version : 1.0.0
    // Date : 19.01.2005
    //
       // Parms : $find_field_name - name of the field by which we want to do search
    //         $field_value - value of the field
    // Returns : str_like_filter - builded string with special format;
    // Description : create the string for SQL-command SELECT for search in the text field by any word
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 19.01.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function build_str_like($find_field_name, $field_value) {
        $str_like_filter = NULL;
        // cut unnormal symbols
        $field_value = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $field_value);
        // delete double spacebars
        $field_value = str_replace(" +", " ", $field_value);
        $wordmas = explode(" ", $field_value);

        for ($i = 0; $i < count($wordmas); $i++) {
            $wordmas[$i] = trim($wordmas[$i]);
            if (EMPTY($wordmas[$i]))
                continue;
            if (!EMPTY($str_like_filter))
                $str_like_filter = $str_like_filter . " AND " . $find_field_name . " LIKE '%" . $wordmas[$i] . "%'";
            else
                $str_like_filter = $find_field_name . " LIKE '%" . $wordmas[$i] . "%'";
        }
        if ($i > 1)
            $str_like_filter = "(" . $str_like_filter . ")";
        //echo '<br>$str_like_filter='.$str_like_filter;
        return $str_like_filter;
    }

//end offunction build_str_like()

    // ================================================================================================
    // Function : build_str_like_for_full
    // Version : 1.0.0
    // Date : 28.05.2014
    //
    // Parms : $find_field_name - name of the field by which we want to do search
    //         $field_value - value of the field
    // Returns : str_like_filter - builded string with special format;
    // Description : create the string for SQL-command SELECT for search in the text field by any word
    // Programmer : Bogdan Iglinsky
    // ================================================================================================
    function build_str_like_for_full($find_field_name, $field_value){
        return " MATCH($find_field_name) AGAINST('*".$field_value."*' IN BOOLEAN MODE) ";
    }


    // ================================================================================================
    // Function : GetAllArticlesIdCat()
    // Date :    26.05.2011
    // Returns : true/false
    // Description : Get all Id articles for $cat
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetAllArticlesIdCat( $id_cat=1, $idModule = null)
    {
        $q = "
            SELECT
                `".TblModArticle."`.id,
                `".TblModArticle."`.dttm as start_date,
                `".TblModArticle."`.position
            FROM
                `".TblModArticle."`
            WHERE
                `".TblModArticle."`.category ='".$id_cat."' and
               `".TblModArticle."`.status='a'
            ORDER BY
                `position` desc
        ";

        $res = $this->db->db_Query( $q );
        //echo '<br>'.$q.' <br/>$res='.$res.' $this->db->result='.$this->db->result;
        if ( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $row =  $this->db->db_FetchAssoc($res);
            $dateId = strtotime ($row['start_date']);
            $array[$dateId]['id'] = $row['id'];
            //$array[$dateId]['start_date'] = $row['start_date'];
            $array[$dateId]['id_module'] = $idModule;
        }
        return $array;
    } //end of function GetAllArticlesIdCat()


    // ================================================================================================
    // Function : GetArticlesForRSS()
    // Date :    17.06.2011
    // Returns : true/false
    // Description : Get Articles for Rss
    // Programmer : Yaroslav Gyryn
    // ================================================================================================
    function GetArticlesForRSS( $idNews=null, $idModule = 83)
    {
        $q = "SELECT
                `".TblModArticle."`.id,
                `".TblModArticle."`.category as id_category,
                `".TblModArticle."`.dttm as start_date,
                `".TblModArticleTxt."`.name as sbj,
                `".TblModArticleTxt."`.short,
                `".TblModArticleTxt."`.full
            FROM
                `".TblModArticle."`, `".TblModArticleTxt."`
            WHERE
                `".TblModArticle."`.status = 'a'
                AND
                `".TblModArticle."`.id = `".TblModArticleTxt."`.cod
                AND
                `".TblModArticleTxt."`.lang_id = '"._LANG_ID."'
                AND
                `".TblModArticle."`.id  IN (".$idNews.")
            ORDER BY
                `".TblModArticle."`.id desc
        ";
        $res = $this->Right->db_Query($q);
        //echo '<br/>'.$q.'<br/> $res = '.$res;
        $rows = $this->Right->db_GetNumRows($res);
        $array = array();
        for( $i = 0; $i <$rows; $i++ ){
             $row = $this->Right->db_FetchAssoc();
             $array[$row['id']] = $row;
             $array[$row['id']]['module'] = $idModule;
             $array[$row['id']]['link'] = $this->Link($row['id_category'],$row['id']);
        }
        return $array;
    }

    // ================================================================================================
    // Function : GetAllArticlesIdRSS()
    // Date :    26.05.2011
    // Returns : true/false
    // Description : Get all Id articles for $cat
    // Programmer :  Yaroslav Gyryn
    // ================================================================================================
    function GetAllArticlesIdRSS($idModule = null, $limit = 20)
    {
        $q = "
            SELECT
                `".TblModArticle."`.id,
                `".TblModArticle."`.dttm as start_date,
                `".TblModArticle."`.position
            FROM
                `".TblModArticle."`
            WHERE
               `".TblModArticle."`.status='a'
            ORDER BY
                `position` desc
            LIMIT ".$limit."
        ";

        $res = $this->Right->db_Query( $q );
        //echo '<br>'.$q.' <br/>$res='.$res.'$this->Right->result='.$this->Right->result;
        if ( !$res OR !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();
        $array = array();
        for( $i=0; $i<$rows; $i++ )  {
            $row =  $this->Right->db_FetchAssoc($res);
            $dateId = strtotime ($row['start_date']);
            $array[$dateId]['id'] = $row['id'];
            $array[$dateId]['id_module'] = $idModule;
        }
        return $array;
    } //end of function GetAllArticlesIdRSS()


}

//--- end of class