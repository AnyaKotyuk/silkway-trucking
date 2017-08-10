<?php
// ================================================================================================
// System : SEOCMS
// Module : publicLayout.class.php
// Version : 1.0.0
// Date : 05.02.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with publications Laouyt
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_public/public.defines.php' );

// ================================================================================================
//    Class             : PublicLayout
//    Version           : 1.0.0
//    Date              : 05.02.2007
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with publications Laouyt
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  05.02.2007
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class PublicLayout extends Publications {

       var $Err = NULL;
       var $lang_id = NULL;

       var $id = NULL;
       var $categ = NULL;
       var $group = NULL;
       var $title = NULL;
       var $text = NULL;
       var $contact = NULL;
       var $dt = NULL;
       var $status = NULL;

       // ================================================================================================
       //    Function          : PublicLayout (Constructor)
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
       function PublicLayout ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id  !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 20   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                if(defined("_LANG_ID")) $this-> lang_id = _LANG_ID;

                if (empty($this->db))
                    $this->db = DBs::getInstance();
                //if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);

                //if (empty($this->Form)) $this->Form = new FrontForm('form_mod_public_layout');
                $this->Form = check_init('FrontForm', 'FrontForm', "'form_mod_public_layout'");
                //if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
                if (empty($this->Spr))
                    $this->Spr = check_init('SysSpr', 'SysSpr');
                if (empty($this->Crypt))
                    $this->Crypt = check_init('Crypt', 'Crypt');
                if (empty($this->multi))
                    $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
       } // End of PublicLayout Constructor


       // ================================================================================================
       // Function : ShowPublications
       // Version : 1.0.0
       // Date : 05.02.2007
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show all publications
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 05.02.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowPublications()
       {
           $arr = $this->GetAllPublic();
           $rows = count($arr);
           $this->PageUser->h1 = $this->PageUser->FrontendPages->page_txt['pname'];
           if ($rows==0) {
               echo View::factory('/modules/mod_public/templates/tpl_public_empty.php')
                ->bind('multi', $this->multi);
               return false;
           }

           $arrReal = array();
           for($i=0;$i<$rows;$i++){
               $row = $arr[$i];
               if($i>=$this->start && $i<($this->start+$this->display)){
                $arrReal[] = $row;
               }
           }
           $count = count($arrReal);
           if(empty($arrReal)) return false;

           for($i=0;$i<$count;$i++){
               $row = $arrReal[$i];
               $id = $row['id'];
               $date = $row['dt'];
               $name = strip_tags(stripslashes($row['title']));
               $text = strip_tags(stripslashes($row['text']));
               $contact = strip_tags(stripslashes($row['contact']));
               $img = $this->showImg($row['img'],$id,200,200,true);

               $publics[$i]['id'] = $id;
               $publics[$i]['name'] = $name;
               $publics[$i]['date'] = $date;
               $publics[$i]['text'] = $text;
               $publics[$i]['contact'] = $contact;
               $publics[$i]['img'] = $img;
            }

            $link = _LINK.'public/';
            $pages =  $this->Form->WriteLinkPagesStatic($link, $rows, $this->display, $this->start, $this->sort, $this->page);
            echo View::factory('/modules/mod_public/templates/tpl_public_by_pages.php')
            ->bind('publics', $publics)
            ->bind('multi', $this->multi)
            ->bind('pages', $pages);
            return true;

       } // End of function ShowPublications

       // ================================================================================================
       // Function : ShowCategories
       // Version : 1.0.0
       // Date : 05.02.2007
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show categories
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 05.02.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowCategories()
       {
           $arr = $this->Spr->GetListName( TblModPublicSprCateg, $this->lang_id, 'array' );
           //print_r($arr);
           if ( empty($this->categ) ) $str = '<span class="ml1">'.$this->Msg->show_text('TXT_ALL_CATEGORIES').'</span>';
           else $str = "<a href='".$_SERVER['PHP_SELF']."?group=$this->group'>".$this->Msg->show_text('TXT_ALL_CATEGORIES')."</a>";

           foreach($arr as $key=>$value){
               if ($this->categ==$key) $str = $str.' | <span class="ml1">'.$value['name']."</span>";
               else $str = $str." | <a href='".$_SERVER['PHP_SELF']."?categ=$key&amp;group=$this->group'>".$value['name']."</a>";

           }
           ?><div class="public_category"><?=$str;?></div><?
       } // End of function ShowCategories

       // ================================================================================================
       // Function : ShowGroups
       // Version : 1.0.0
       // Date : 05.02.2007
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show categories
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 05.02.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowGroups()
       {
           $arr = $this->Spr->GetListName( TblModPublicSprGroup, $this->lang_id, 'array' );
           //print_r($arr);
           if ( empty($this->group) ) $str = '<span class="ml1">'.$this->Msg->show_text('TXT_ALL_GROUPS').'</span>';
           else $str = "<a href='".$_SERVER['PHP_SELF']."?categ=$this->categ' class='public_group'>".$this->Msg->show_text('TXT_ALL_GROUPS')."</a>";

           foreach($arr as $key=>$value){
               if ($this->group==$key) $str = $str.' | <span class="ml1">'.$value['name']."</span>";
               else $str = $str." | <a href='".$_SERVER['PHP_SELF']."?categ=$this->categ&amp;group=$key' class='public_group'>".$value['name']."</a>";

           }
           ?><div class="public_group"><?=$str;?></div><?
       } // End of function ShowGroups

       // ================================================================================================
       // Function : ShowSearchForm
       // Version : 1.0.0
       // Date : 05.02.2007
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show filters and search form
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 05.02.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowSearchForm()
       {
         $this->Form->WriteFrontHeader( 'public_searchform', 'public.php', NULL, NULL );
         ?>
         <table border=0 cellpadding=3 cellspacing=0 width="100%" class="formArea">
          <tr valign=top>
           <td class="formAreaTitle"><?=$this->Msg->show_text('_BUTTON_SEARCH', TblSysTxt);?></td>
          </tr>
          <tr>
           <td><?=$this->Msg->show_text('FLD_TITLE_OR_TEXT');?></td>
           <td><?$this->Form->TextBox('srch', $this->srch, 'size=50');?><?$this->Form->Button( 'submit', $this->Msg->show_text('_BUTTON_SEARCH', TblSysTxt), 50 );?></td>
          <tr>
         </table>
         <br>
         <?
         $this->Form->WriteFrontFooter();

       } //end of fuinction ShowSearchForm()

       // ================================================================================================
       // Function : ShowAddForm
       // Version : 1.0.0
       // Date : 05.02.2007
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show form to add publication
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 05.02.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowAddForm()
       {
           $this->PageUser->h1 = $this->multi['TXT_CREATE_PUBLICATION'];
           $this->PageUser->breadcrumb .= '&nbsp;•&nbsp;<span class="spanShareName">'.
                   $this->multi['TXT_CREATE_PUBLICATION'].'</span>';
           ?>
         <script type="text/javascript">
            $(document).ready(function() {
                $("#public_addform").validationEngine();
            });
        </script>
         <div id="rez" onload="ShowForm();"><?
           $this->Form->WriteFrontHeader( 'public_addform', _LINK.'public/send/', 'addPublication', NULL );
           if (!empty($this->Err)) $this->showErr();
           /*?>
        <TR><TD><b><?echo $this->Msg->show_text('FLD_CATEGORY')?>:</b>
            <TD width="100%">
            <?
            $this->Spr->ShowInComboBox( TblModPublicSprCateg, 'categ', $this->categ, 50 );
            ?>

        <TR><TD><b><?echo $this->Msg->show_text('FLD_GROUP')?>:</b>
            <TD>
            <?
            $this->Spr->ShowInComboBox( TblModPublicSprGroup, 'group', $this->group, 50 );
            */?>
         <div class="input-one-item">
             <div class="input-label"><?=$this->multi['TXT_TITLE_PUBLIC']?> <span class="red">*</span></div>
            <div class="input-text"><?
               echo $this->Form->TextBox( 'title', stripslashes($this->title), 'size=60 class="validate[required]" ' );
            ?></div>
         </div>
         <div class="input-one-item">
            <div class="input-label"><?=$this->multi['TXT_TEXT_PUBLIC']?> <span class="red">*</span></div>
            <div class="input-text"><?
            echo $this->Form->TextArea( 'text', stripslashes($this->text), 8, 10 ,' class="validate[required]" ');
            ?></div>
         </div>
             <div class="input-one-item" style="overflow: visible;height: 45px;">
            <div class="input-one-item-left">
                <div class="input-label"><?=$this->multi['FLD_CONTACTS']?> <span class="red">*</span></div>
                <div class="input-text"><?
                   echo $this->Form->TextBox( 'contact', stripslashes($this->contact), 'size=20 style="width: 175px;" class="validate[required]" ');
                ?></div>
            </div>
             <div class="input-one-item-left">
                <div class="input-label"><?=$this->multi['TXT_IMAGE']?></div>
                <div style="overflow: hidden;">
                    <ul id="files" class="file-public"></ul>
                    <div class="input-text type-file" id="upload">
                        <input type="file" name="filename" size="40" class="input-file"
                               onchange='document.getElementById("fileText").value=this.value' />
                        <div class="fon-type-file button">Выберите файл</div>
                        <input type="text" value="Файл не выбран" class="input-file-val" readonly="readonly" id="fileText" name="fileName" />
                        <div class="fon-type-file-pointer" onclick="$('.input-file').click();"></div>
                        <div class="status-public" id="status"></div>
                    </div>
                </div>
            </div>
         </div>
         <div><?
            echo $this->Form->Button( 'submit', $this->multi['_TXT_SEND'], 50, ' onclick="sendPublic();return false;" ' );
         ?></div>
           <?
           $this->Form->WriteFrontFooter();
           ?></div>
        <script type="text/javascript" >
	$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		new AjaxUpload(btnUpload, {
			action: '/public/seveImg/',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){
                    // extension is not allowed
					status.text('Only JPG, PNG or GIF files are allowed');
					return false;
				}
				status.text('Uploading...');
			},
			onComplete: function(file, response){
                            //alert(parseInt(response)==0);
                            //On completion clear the status
                            status.text('');
                            //Add uploaded file to list
                            if(parseInt(response)==1){
                                    //$('<li></li>').appendTo('#files').val('<img src="<?=NAME_SERVER?>/tmpImg/'+file+'" alt="" /><br />'+file).addClass('success');
                                    $('#files').html('<li>'+file+'</li>').addClass('error');
                            } else{
                                    //$('<li></li>').appendTo('#files').text(file).addClass('error');
                                    $('#files').html('<li>'+response+'</li>');
                            }
                            $('#fileText').val(file);
			}
		});

	});
</script><?
       } // End of function ShowAddForm()

       // ================================================================================================
       // Function : ShowErr()
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
       function showErr()
       {
         if ($this->Err){
             ?><h2><?=$this->Msg->show_text('MSG_ERR', TblSysTxt);?></h2>
               <div class="err">
               <span class="errorBox"><?=$this->Err;?></span>
               </div>
             <?
         }
       } //end of fuinction ShowErr()

       function ShowSearchRes($arr = NULL){
           $count = count($arr);
           $publics = array();
           for($i=0;$i<$count;$i++){
               $row = $arr[$i];
               $id = $row['id'];
               $date = $row['dt'];
               $name = strip_tags(stripslashes($row['title']));
               $text = strip_tags(stripslashes($row['text']));
               $contact = strip_tags(stripslashes($row['contact']));
               $img = $this->showImg($row['img'],$id,200,200,true);

               $publics[$i]['id'] = $id;
               $publics[$i]['name'] = $name;
               $publics[$i]['date'] = $date;
               $publics[$i]['text'] = $text;
               $publics[$i]['contact'] = $contact;
               $publics[$i]['img'] = $img;
            }

            $link = _LINK.'public/';
            $pages =  '';
            //print_r($publics);
            echo View::factory('/modules/mod_public/templates/tpl_public_by_pages.php')
            ->bind('publics', $publics)
            ->bind('multi', $this->multi)
            ->bind('pages', $pages);
            return true;
       }

 } //end of class Public_ctrl
