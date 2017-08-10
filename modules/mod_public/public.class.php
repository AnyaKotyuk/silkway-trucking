<?php
// ================================================================================================
// System : SEOCMS
// Module : public.class.php
// Version : 1.0.0
// Date : 01.02.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with managment of publications
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_catalog/catalog.defines.php' );

// ================================================================================================
//    Class             : Publications
//    Version           : 1.0.0
//    Date              : 01.02.2007
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment of publications
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  01.02.2007
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class Publications {

       var $user_id = NULL;
       var $module = NULL;
       var $Err = NULL;
       var $lang_id = NULL;
       var $sel = NULL;

       var $sort = NULL;
       var $display = 20;
       var $start = 0;
       var $fln = NULL;
       var $width = 500;
       var $srch = NULL;
       var $fltr = NULL;
       var $fltr2 = NULL;
       var $script = NULL;
       var $parent_script = NULL;

       var $db = NULL;
       var $Msg = NULL;
       var $Right = NULL;
       var $Form = NULL;
       var $Spr = NULL;

       // variables
       var $id = NULL;
       var $categ = NULL;
       var $group = NULL;
       var $title = NULL;
       var $text = NULL;
       var $contact = NULL;
       var $dt = NULL;
       var $status = NULL;

       // ================================================================================================
       //    Function          : Publications (Constructor)
       //    Version           : 1.0.0
       //    Date              : 01.02.2007
       //    Parms             : usre_id   / User ID
       //                        module    / module ID
       //                        sort      / field by whith data will be sorted
       //                        display   / count of records for show
       //                        start     / first records for show
       //                        width     / width of the table in with all data show
       //    Returns           : Error Indicator
       //
       //    Description       : Opens and selects a dabase
       // ================================================================================================
       function Publications ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                if(defined("_LANG_ID")) $this-> lang_id = _LANG_ID;

                if (empty($this->db)) $this->db = new DB();
                if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                if (empty($this->Msg)) $this->Msg = new ShowMsg();
                $this->Msg->SetShowTable(TblModPublicSprTxt);
                if (empty($this->Form)) $this->Form = new Form('form_mod_public');
                if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
       } // End of Publications Constructor

       // ================================================================================================
       // Function : ShowErrBackEnd()
       // Version : 1.0.0
       // Date : 01.02.2007
       //
       // Parms :
       // Returns :      true,false / Void
       // Description :  Show errors
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 01.02.2007
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
       // Function : CheckFields()
       // Version : 1.0.0
       // Date : 01.02.2007
       //
       // Parms :        $id - id of the record in the table
       // Returns :      true,false / Void
       // Description :  Checking all fields for filling and validation
       // ================================================================================================
       // Programmer :  Igor Trokhymchuk
       // Date : 01.02.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function CheckFields($id = NULL)
       {
        $this->Err=NULL;
/*
        if (empty( $this->categ )) {
            $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_CATEGORY_EMPTY').'<br>';
        }

        if (empty( $this->group )) {
            $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_GROUP_EMPTY').'<br>';
        }       */

        if (empty( $this->title )) {
            $this->Err=$this->Err.$this->multi['MSG_FLD_TITLE_EMPTY'].'<br>';
        }

        if (empty( $this->text )) {
            $this->Err=$this->Err.$this->multi['MSG_FLD_TEXT_EMPTY'].'<br>';
        }

        if (empty( $this->contact )) {
            $this->Err=$this->Err.$this->Msg->show_text('MSG_FLD_CONTACT_EMPTY').'<br>';
        }


        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
        return $this->Err;
       } //end of fuinction CheckFields()

       // ================================================================================================
       // Function : save()
       // Version : 1.0.0
       // Date : 01.02.2007
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Store data to the table
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 01.02.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function save($id)
       {
        $q="SELECT *
            FROM `".TblModPublic."`
            WHERE";
        if(!$id){
            $q .= " `categ`='$this->categ' AND `group`='$this->group' AND `title`='$this->title' AND `text`='$this->text' AND `contact`='$this->contact' AND `status`='$this->status'";
        }else{
            $q .= " `id`='$id'";
        }
        $res = $this->db->db_Query( $q );
        if( !$res OR !$this->db->result ) return false;
        $rows = $this->db->db_GetNumRows();

        //phpinfo();

        if($rows>0)
        {
          $row = $this->db->db_FetchAssoc();
          $q="update `".TblModPublic."` set
              `categ`='$this->categ',
              `group`='$this->group',
              `title`='$this->title',
              `text`='$this->text',
              `contact`='$this->contact',
              `dt`='$this->dt',
              `status`='$this->status',
              `time` = '$this->time'";
          $q=$q." where id='".$row['id']."'";
          $res = $this->db->db_Query( $q );
          //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
          if( !$res OR !$this->db->result ) return false;
        }
        else
        {
          $q="insert into `".TblModPublic."` values(NULL,'$this->categ','$this->group','$this->title','$this->text','$this->contact','$this->dt','$this->status', '$this->time')";
          $res = $this->db->db_Query( $q );
          //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
          if( !$res OR !$this->db->result) return false;
        }
        return true;
       } // end of function save()

       function SavePicture()
        {
         $tmp_db = DBs::getInstance();
         $this->Err = NULL;
         $max_image_width= ARTICLE_MAX_IMAGE_WIDTH;
         $max_image_height= ARTICLE_MAX_IMAGE_HEIGHT;
         $max_image_size= ARTICLE_MAX_IMAGE_SIZE;
         $valid_types =  array("gif", "GIF", "jpg", "JPG", "png", "PNG", "jpeg", "JPEG");
         //echo '<br>file=';print_r($_FILES);
         //echo '<br>req=';print_r($_REQUEST);
         //echo $this->img;
         if (!isset($_FILES["filename"])) return false;
         $cols = count($_FILES["filename"]);

         if(empty($_FILES['filename']['name'][0])) return false;
         $res = $this->DelPicture( $this->id);
         if (!$res) return false;
         //echo '<br><br>$cols='.$cols;
         for ($i=0; $i<$cols; $i++) {
             //echo '<br>$_FILES["image"]='.$_FILES["image"].' $_FILES["image"]["tmp_name"]["'.$i.'"]='.$_FILES["image"]["tmp_name"]["$i"].' $_FILES["image"]["size"]["'.$i.'"]='.$_FILES["image"]["size"]["$i"];
             //echo '<br>$_FILES["image"]["name"][$i]='.$_FILES["image"]["name"][$i];
             if ( !empty($_FILES["filename"]["name"][$i]) ) {
                 //echo '1='.is_uploaded_file($_FILES["filename"]["tmp_name"][$i]);
                 //echo '2='.$_FILES["filename"]["size"][$i];
               if ( isset($_FILES["filename"]) && $_FILES["filename"]["size"][$i] ){
                $filename = $_FILES['filename']['tmp_name'][$i];
                $ext = substr($_FILES['filename']['name'][$i],1 + strrpos($_FILES['filename']['name'][$i], "."));
                $name_no_ext = substr($_FILES['filename']['name'][$i], 0, strrpos($_FILES['filename']['name'][$i], "."));
                //echo '<br>filesize($filename)='.filesize($filename).' $max_image_size='.$max_image_size;
                if (filesize($filename) > $max_image_size) {
                    $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_SIZE').' ('.$_FILES['filename']['name']["$i"].')<br>';
                    continue;
                }
                if (!in_array($ext, $valid_types)) {
                    $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_TYPE').' ('.$_FILES['filename']['name']["$i"].')<br>';
                }
                else {
                  $size = GetImageSize($filename);
                  //echo '<br>$size='.$size.'$size[0]='.$size[0].' $max_image_width='.$max_image_width.' $size[1]='.$size[1].' $max_image_height='.$max_image_height;
                  //if (($size) && ($size[0] < $max_image_width) && ($size[1] < $max_image_height)) {
                     //$settings = $this->GetSettings();
                    // $uploaddir = NewsImg_Full_Path.$this->id;
                     $alias = $this->id;
                     $uploaddir = SITE_PATH.'/images/mod_public/'.$alias;
                     if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                    // if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
                     else @chmod($uploaddir,0777);

                     //$uploaddir2 = $name_no_ext.'_'.time().$i.'.'.$ext;
                     $uploaddir2 = time().'_'.$i.'_'.$alias.'.'.$ext;
                     $uploaddir = $uploaddir."/".$uploaddir2;
                     $this->fpath = $uploaddir2;
                     $this->uploaddir = $uploaddir;
                     //echo '<br>$filename='.$filename.'<br> $uploaddir='.$uploaddir.'<br> $uploaddir2='.$uploaddir2;
                     //if (@move_uploaded_file($filename, $uploaddir)) {
                     if ( copy($filename,$uploaddir) ) {
                         $q="UPDATE `".TblModPublic."` SET
                             `img`='".$uploaddir2."'
                            WHERE `id` = '".$this->id."'";
                         $res = $tmp_db->db_Query( $q );
                         if( !$res OR !$tmp_db->result ) $this->Err = $this->Err.$this->multi['MSG_ERR_SAVE_FILE_TO_DB'].' ('.$_FILES['image']['name']["$i"].')<br>';
                         //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                         if (($size) AND (($size[0] > $max_image_width) OR ($size[1] > $max_image_height)) ){
                             //============= resize original image to size from settings =============
                             $thumb = new Thumbnail($uploaddir);
                             if($max_image_width==$max_image_height) $thumb->size_auto($max_image_width);
                             else{
                                $thumb->size_width($max_image_width);
                                $thumb->size_height($max_image_height);
                             }
                             $thumb->quality = $max_image_quantity;
                             $thumb->process();       // generate image
                             $thumb->save($uploaddir); //make new image
                             //=======================================================================
                         }

                     }
                     else {
                         $this->Err = $this->Err.$this->multi['MSG_ERR_FILE_MOVE'].' ('.$_FILES['filename']['name']["$i"].')<br>';
                     }
                  //}
                  //else {
                  //   $this->Err = $this->Err.$this->Msg->show_text('MSG_ERR_FILE_PROPERTIES').' ['.$max_image_width.'x'.$max_image_height.'] ('.$_FILES['filename']['name']["$i"].')<br>';
                  //}
                }
               }
               else $this->Err = $this->Err.$this->multi['MSG_ERR_FILE'].' ('.$_FILES['filename']['name']["$i"].')<br>';
             }
             //echo '<br>$i='.$i;
         } // end for
         return $this->Err;

        }  // end of function SavePicture()


       function DelPicture($id_public = NULL){
            if(empty($id_public) && !empty($this->id)) $id_public = $this->id;
            $tmp_db = DBs::getInstance();
            $q="SELECT * FROM `".TblModPublic."` WHERE `id`='".$id_public."'";
            $res = $tmp_db->db_Query( $q );
            //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
            if( !$res OR !$tmp_db->result ) return false;
            $row = $tmp_db->db_FetchAssoc();
            if(!empty($row['img'])){
                $path = SITE_PATH.'/images/mod_public/'.$id_public;
                $this->removeDirRec($path);
            }
            return true;
         } // end of function DelPicture()

        function removeDirRec($dir){
            if ($objs = glob($dir."/*")) {
                foreach($objs as $obj) {
                    is_dir($obj) ? removeDirRec($obj) : unlink($obj);
                }
            }
            rmdir($dir);
        }

       function savePublic(){
            $q="insert into `".TblModPublic."` set
              `categ` = '1',
              `group` = '1',
              `title`='$this->title',
              `text`='$this->text',
              `contact`='$this->contact',
              `dt`='$this->dt',
              `status`='$this->status',
              `img`='$this->img',
              `time` = '$this->time'";
            $res = $this->db->db_Query( $q );
            //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
            if( !$res OR !$this->db->result) return false;
            $this->id = $this->db->db_GetInsertID();
            return true;
       }
       function sendPublic(){
            $subject = $this->multi['TXT_FOTM_PUBLIC'].' :: '.$_SERVER['SERVER_NAME'].', '.$this->multi['FLD_TITLE'].': '.$this->title;

            $body = $this->multi['TXT_FOTM_PUBLIC'].':
            <style>
             td{ font-family:Arial,Verdana,sans-serif; font-size:11px;}
            </style>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr><td width="100">'.$this->multi['FLD_TITLE'].':</td><td>'.stripslashes($this->title).'</td></tr>
            <tr><td>'.$this->multi['TXT_TEXT_PUBLIC'].':</td><td>'.stripslashes($this->text).'</td></tr>
            <tr><td>'.$this->multi['FLD_CONTACTS'].':</td><td>'.stripslashes($this->contact).'</td></tr>
            </table>';

            //================ send by class Mail START =========================
            $massage = $body;
            $mail = new Mail($this->lang_id);

            $SysSet = new SysSettings();
                $sett = $SysSet->GetGlobalSettings();
                if( !empty($sett['mail_auto_emails'])){
                    $hosts = explode(";", $sett['mail_auto_emails']);
                    for($i=0;$i<count($hosts);$i++){
                        //$arr_emails[$i]=$hosts[$i];
                        $mail->AddAddress($hosts[$i]);
                    }//end for
                }
            if( isset($this->fpath) && !empty($this->fpath) ){
                $fpath = $this->uploaddir;
                $mail->AddAttachment($fpath);
            }
            $mail->Subject = $subject;
            $mail->Body = $massage;
            $res = $mail->SendMail();
            if(!$res) return false;
            //================ send by class Mail END =========================
            return true;
       }


       function QuickSearch($search_keywords)
        {
            $search_keywords = stripslashes($search_keywords);
            $sel_table = NULL;
            $str_like = NULL;
            $filter_cr = ' OR ';

            $str_like = $this->build_str_like(TblModPublic.'.title', $search_keywords);
            $str_like .= $filter_cr.$this->build_str_like(TblModPublic.'.text', $search_keywords);
            $str_like .= $filter_cr.$this->build_str_like(TblModPublic.'.contact', $search_keywords);

            $q ="SELECT *
                FROM `".TblModPublic."`
                WHERE (".$str_like.")
                AND `".TblModPublic."`.status = '2'
                ORDER BY `".TblModPublic."`.dt";
            $res =  $this->db->db_Query( $q );
            //echo '<br>q='.$q.' res='.$res.'  $this->db->result='. $this->db->result;
            if ( !$res OR ! $this->db->result ) return false;
            $rows = $this->db->db_GetNumRows();
            $arr_res = array();
            for($i=0;$i<$rows;$i++){
                $arr_res[$i] = $this->db->db_FetchAssoc();
            }
            return $arr_res;
        } // end of function QuickSearch

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
        } //end of function build_str_like()

       function GetAllPublic(){
           $q ="SELECT * FROM `".TblModPublic."` WHERE status = '2' and title != '' ORDER BY `dt` desc";
           $res = $this->db->db_Query( $q );
           //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
           if ( !$res OR !$this->db->result ) return false;
           $rows = $this->db->db_GetNumRows();
           if($rows==0) return false;
           $arr = array();
           for($i=0;$i<$rows;$i++){
               $row = $this->db->db_FetchAssoc();
               $arr[$i] = $row;
           }
           return $arr;
       }

        // ================================================================================================
        // Function : CotvertDataToOutputArray
        // Version : 1.0.0
        // Date : 19.05.2006
        //
        // Parms :  $rows - count if founded records stored in object $this->db
        //          $sort - type of sortaion returned array
        //                  (move - default value, name)
        //          $asc_desc - sortation Asc or Desc
        // Returns : $arr
        // Description : return arr of content for selected category
        // ================================================================================================
        // Programmer : Igor Trokhymchuk
        // Date : 19.05.2006
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function CotvertDataToOutputArray ($rows, $sort = "move", $asc_desc = "asc")
        {
           $arr0 = NULL;
           for ($i=0;$i<$rows;$i++){
               $row = $this->db->db_FetchAssoc();
               switch($sort){
                case 'id':
                    $index_sort = $row['id'];
                    break;
                case 'categ':
                    $index_sort = $this->Spr->GetNameByCod(TblModPublicSprCateg, $row['categ'], $this->lang_id, 1).'_'.$row['id'];
                    break;
                case 'group':
                    $index_sort = $this->Spr->GetNameByCod(TblModPublicSprGroup, $row['group'], $this->lang_id, 1).'_'.$row['id'];
                    break;
                case 'title':
                    $index_sort = $row['title'].'_'.$row['id'];
                    break;
                case 'dt':
                    $index_sort = $row['dt'].'_'.$row['id'];
                default:
                    $index_sort=$row['id'];
                    break;
               }
               //echo '<br> $index_sort='.$index_sort;

               $arr0[$index_sort]["id"] = $row['id'];
               $arr0[$index_sort]["categ"] = $row['categ'];
               $arr0[$index_sort]["group"] = $row['group'];
               $arr0[$index_sort]["status"] = $row['status'];
               $arr0[$index_sort]["dt"] = $row['dt'];

               $short_descr = strip_tags($row['title']);
               $short_descr = str_replace ( '&nbsp;', ' ', $short_descr );
               $string = str_replace ( '&amp;', ' ', $short_descr );
               $string = str_replace ( '&#039;', '\'', $short_descr );
               $string = str_replace ( '&quot;', '\"', $short_descr );
               $arr0[$index_sort]["title"] = $short_descr;

               $short_descr = strip_tags($row['text']);
               $short_descr = str_replace ( '&nbsp;', ' ', $short_descr );
               $string = str_replace ( '&amp;', ' ', $short_descr );
               $string = str_replace ( '&#039;', '\'', $short_descr );
               $string = str_replace ( '&quot;', '\"', $short_descr );
               $arr0[$index_sort]["text"] = $short_descr;

               $short_descr = strip_tags($row['contact']);
               $short_descr = str_replace ( '&nbsp;', ' ', $short_descr );
               $string = str_replace ( '&amp;', ' ', $short_descr );
               $string = str_replace ( '&#039;', '\'', $short_descr );
               $string = str_replace ( '&quot;', '\"', $short_descr );
               $arr0[$index_sort]["contact"] = $short_descr;

               //$arr = array_merge($arr, $arr0);
               //echo '<br>Arr:<br>'; print_r($arr); echo '<br><br>';
           }
           //echo '<br><br>'; print_r($arr); echo '<br><br>';
           //sort($arr); reset($arr); print_r($arr); echo '<br><br>';
           //if ($sort == 'id') sort($arr);
           //if ($sort == 'move') sort($arr);
           //if ($sort == 'name') asort($arr);
           if (is_array($arr0)) {
               if ( $asc_desc == 'desc' ) krsort($arr0);
               else ksort($arr0);
               reset($arr0);
           }
           //echo '<br>Arr:<br>'; print_r($arr); echo '<br><br>';
           return $arr0;
        } //end of function CotvertDataToOutputArray()

       // ================================================================================================
       // Function : AutoDeletingPublications()
       // Version : 1.0.0
       // Date : 07.02.2007
       // Parms : $days - period in seconds after which publicatin will be delete
       // Returns : true,false / Void
       // Description : automaticly delete all publication with p eriod of publication more than $days
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 07.02.2007
       // Reason for change : Reason Description / Creation
       // Change Request Nbr:
       // ================================================================================================
       function AutoDeletingPublications( $days=NULL )
       {
           if ( empty($days) ){
                $q = "SELECT * FROM `".TblModPublicSet."` WHERE 1";
                $res = $this->db->db_Query( $q);
                //echo '<br>$q='.$q.' $res='.$res.'$this->db->result='.$this->db->result;
                if( !$res )return false;
                $row = $this->db->db_FetchAssoc();
                $days = $row['day'];
                if(empty($days)) $days=LIFE_PERIOD;
           }
           $tmp_db = $this->db;
           $Date_Calc = new Date_Calc();

           $dddays = $Date_Calc->dateToDays(Date_Calc::dateNow("%d"),Date_Calc::dateNow("%m"),$year = Date_Calc::dateNow("%Y"));
           $dt = $Date_Calc->daysToDate($dddays-$days,"%Y-%m-%d");

           $dt = $dt." 00:00:00";

           $q = "DELETE FROM `".TblModPublic."` WHERE `dt` < '$dt'";
           $res = $tmp_db->db_Query($q);
           //echo '<br>$q='.$q.' $res='.$res.' $tmp_db->result'.$tmp_db->result;
           if (!$res OR !$tmp_db->result) return false;
           return true;
       } //end of function AutoDeletingPublications()

       function showImg($img = NULL,$id = NULL,$width = 0,$height = 0,$return_src = false,$param = NULL){
            if(empty($img)) return false;
            //echo '$img='.strstr($img,'/');
            if(strstr($img,'/')){
                $img_path = SITE_PATH.$img;
                $img_arr = explode('/',$img);
                $count = count($img_arr);
                $img_dir = '/tmpImg/';
                $img = $img_arr[$count-1];
                //echo '<br>$img='.$img;
            }else{
                //echo '/===true';
                $img_dir = '/images/mod_public/'.$id;
                $img_path = SITE_PATH.$img_dir.'/'.$img;
            }
            if(is_file($img_path)){
                //echo 'sdfsdfsdf';
                $ext = substr($img,1 + strrpos($img, "."));
                $name_without_ext = substr($img,0, strrpos($img, "."));
                $resize_path = $img_dir.'/'.$name_without_ext.ADDITIONAL_FILES_TEXT.'width_'.$width.'_height_'.$height.'.'.$ext;
                $img_path_resize = SITE_PATH.$resize_path;

                if(!is_file($img_path_resize)){
                    $res = ImageK::factory($img_path)->resize($width, $height)->save($img_path_resize);
                }
                $img_path = $resize_path;
            }
            if(empty($param)){
                $param = ' alt="" title="" ';
            }
            if($return_src) return $img_path;
            else{
                return '<img src="'.$img_path.'" '.$param.' />';
            }
       }

       function buildFileArray(){
           if(empty($_FILES)){
               $_FILES['filename']['name'][0] = $_REQUEST['fileName'];
               $_FILES['filename']['tmp_name'][0] = SITE_PATH.'/tmpImg/'.$_REQUEST['fileName'];
               $_FILES['filename']['error'][0] = 0;
               $_FILES['filename']['size'][0] = filesize(SITE_PATH.'/tmpImg/'.$_REQUEST['fileName']);

           }
       }

 } //end of Publications
