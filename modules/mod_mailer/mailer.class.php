<?
/**
 * mailer.class.php
 * Class definition for all actions with sending created letters
 * @package Mailer Package of SEOCMS
 * @author Igor Trokhymchuk  <ihor@seotm.com>
 * @version 1.1, 29.08.2013
 * @copyright (c) 2010+ by SEOTM
 */
class Mailer {
    public $Right;
    public $Form;
    public $Msg;
    public $Spr;
    public $db = NULL;

    public $user_id;
    public $module;
    public $display;
    public $sort;
    public $start;
    public $fltr;    // filter of group news
    public $width;
    public $lang_id = NULL;
    public $Err = NULL;
    public $pfiles = NULL;
    public $subscr_start = 0;
    public $subscr_cnt = 45;
    public $showResult = false;

    // ================================================================================================
    //    Function          : Mailer (Constructor)
    //    Version           : 1.0.0
    //    Date              : 04.03.2009
    //    Parms             :
    //    Returns           :
    //    Description       : News
    // ================================================================================================
    function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
        //Check if Constants are overrulled
        ( $user_id  !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
        ( $display  !="" ? $this->display = $display  : $this->display = 20   );
        ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
        ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
        ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

        if (DEFINED("_LANG_ID")) $this->lang_id = _LANG_ID;

        $this->Right =  new Rights($this->user_id, $this->module);                   /* create Rights obect as a property of this class */
        if (empty($this->db))
            $this->db = DBs::getInstance();
        if (empty($this->Msg))
            $this->Msg = check_init('ShowMsg', 'ShowMsg');
        if (empty($this->Form))
            $this->Form = check_init('FormCatalog', 'Form', '"form_mod_catalog"');
        if (empty($this->Spr))
            $this->Spr = check_init('SysSpr', 'SysSpr', '"' . $this->user_id . '", "' . $this->module . '"');
        $this->width = '750';

        $this->dispatch_statuses['']='Статус не определен';
        $this->dispatch_statuses[0]='Еще не рассылалось';
        $this->dispatch_statuses[1]='В рассылке';
        $this->dispatch_statuses[2]='Разослано';
        $this->dispatch_statuses[3]='Рассылка принудительно остановлена';
        $this->dispatch_statuses[4]='На паузе';
    }

    // ================================================================================================
    // Function : ShowErrBackEnd()
    // Version : 1.0.0
    // Date : 31.05.2008
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Show errors
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 31.05.2008
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowErrBackEnd()
    {
     if ($this->Err){
       echo '
        <fieldset class="err" title="ОШИБКИ"> <legend>ОШИБКИ</legend>
        <div class="err_text">'.$this->Err.'</div>
        </fieldset>';
     }
    } //end of fuinction ShowErrBackEnd()

    // ================================================================================================
    // Function : ShowDispatch()
    // Version : 1.0.0
    // Date : 04.03.2009
    //
    // Parms :
    // Returns :     true,false / Void
    // Description : Show Dispatch
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 04.03.2009
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ShowDispatch()
    {
     $frm = new Form('fltr');
     $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
     $script = $_SERVER['PHP_SELF']."?$script";

     if( !$this->sort ) $this->sort='dt';
     //if( strstr( $this->sort, 'display' ) )$this->sort = $this->sort.' desc';
     $q = "SELECT * FROM ".TblModMailerDispatch." WHERE 1";
     if( $this->fltr ) $q = $q." AND $this->fltr";
     $q = $q." order by `".$this->sort."` desc, `id` desc ";

     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res )return false;

     $rows = $this->Right->db_GetNumRows();
     for( $i = 0; $i < $rows; $i++ ){
         $arrData[]=$this->Right->db_FetchAssoc();
     }

     /* Write Table Part */
     AdminHTML::TablePartH();
     $this->ShowErrBackEnd();
     /* Write Form Header */
     $this->Form->WriteHeader( $script );
     //$this->Form->Hidden('id', '');


     /* Write Links on Pages */
     echo '<TR><TD COLSPAN=8>';
     $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
     $script1 = $_SERVER['PHP_SELF']."?$script1";
     $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

     ?><tr><td colspan="4"><?
     $this->Form->WriteTopPanel( $script, 'edit_subscr', NULL );

     $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr.'&task=show_subscr';
     $script2 = $_SERVER['PHP_SELF']."?$script2";
    ?>
     <TR>
     <td class="THead" width="30">*</Th>
     <td class="THead" width="30"><?=$this->Msg->show_text('_FLD_ID')?></Th>
     <td class="THead">Тема письма</Th>
     <td class="THead">Содержание письма</Th>
     <td class="THead">Прикрепленные файлы</Th>
     <td class="THead"><A HREF=<?=$script2?>&sort=status>Статус</A></Th>
     <td class="THead"><A HREF=<?=$script2?>&sort=dt>Дата создания</A></Th>
     <td class="THead">Тестовая отправка письма</Th>
     <?
     /*
     $q = "SELECT `email` FROM ".TblModUser." WHERE `id` IN (".$this->test_partners_id.")";
     $tmp_res = $db->db_Query( $q );
     $tmp_rows = $db->db_GetNumRows();
     //     echo '<br>$q='.$q.' $tmp_res='.$tmp_res.' $tmp_rows='.$tmp_rows;
     for($jjj=0;$jjj<$tmp_rows;$jjj++){
         $tmp_row = $db->db_FetchAssoc();
         if( empty($txt_emails) ) $txt_emails = $tmp_row['email'];
         else $txt_emails .= ', '.$tmp_row['email'];
     }
     */
     $SysSet = new SysSettings();
     $sett = $SysSet->GetGlobalSettings();
     if( !empty($sett['mail_auto_emails'])){
         $hosts = explode(";", $sett['mail_auto_emails']);
         for($i=0;$i<count($hosts);$i++){
             if( empty($txt_emails) ) $txt_emails = $hosts[$i];
             else $txt_emails .= ', '.$hosts[$i];
         }//end for
     }

     $j = 0;
     $row_arr = NULL;
     for( $i = 0; $i < $rows; $i++ )
     {
       $row = $arrData[$i];
       if( $i >= $this->start && $i < ( $this->start+$this->display ) )
       {
         $row_arr[$j] = $row;
         $j = $j + 1;
       }
     }

     $style1 = 'TR1';
     $style2 = 'TR2';
     for( $i = 0; $i < count( $row_arr ); $i++ )
     {
       $row = $row_arr[$i];

       if ( (float)$i/2 == round( $i/2 ) )
       {
        echo '<TR CLASS="'.$style1.'">';
       }
       else echo '<TR CLASS="'.$style2.'">';

       echo '<td>';
       $this->Form->CheckBox( "id_del[]", $row['id'] );

       echo '<td>';
       $params = '';
       if($row['status']==1) $params = "onclick=\"alert('Редактирование запрещенно, пока пиcьмо находится в рассылке'); return false;\"";
       $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('_TXT_EDIT_DATA'), $params );
       //echo $row['id'];
       ?>
       <td align=center><?=stripslashes($row['sbj']);?>
       <td align=center><?if( !empty($row['body']) ){ echo $this->Form->ButtonCheck();}?></td>
       <td><?=$this->GetCntFilesDispatch($row['id']);?></td>
       <td><div id="wndw<?=$row['id'];?>"><?=$this->ControlDispatchStatus($row['id'], $row['status']);?></div></td>
       <td align=center><?=$row['dt'];?></td>
       <td align=center>
        <input type="button" value="Тест" onclick="if( !window.confirm('Письмо будет отправленно на E-mail: <?=$txt_emails;?>. Вы точно хотите ПРОТЕСТИРОВАТЬ отправку этого письма?') ) return false; else <?=$this->Form->name?>.id.value='<?=$row['id'];?>';<?=$this->Form->name?>.task.value='test_send';<?=$this->Form->name?>.submit();"/>
       </td>
       <?
     } //-- end for

     AdminHTML::TablePartF();
     $this->Form->WriteFooter();

    ?>
    <script language="JavaScript">
     function StartDispatch(id){
         myurl = '/modules/mod_mailer/mailer.backend.php';
         did = "#mailerLoader"+id;
         $.ajax({
            type: "POST",
            data: '<?=$this->script_ajax;?>&task=is_can_start&id='+id,
            url: myurl,
            success: function(res){
                //alert(res);
                if(res!='1'){
                    alert('Рассылку запускать нельзя. Уже есть запущенная рассылка.');
                    $(did).empty();
                    return false;
                }else{
                    if(!window.confirm('Вы точно хотите ЗАПУСТИТЬ рассылку этого письма?')){
                        return false;
                    }else{
                        url = '/modules/mod_mailer/mailer.backend.php?<?=$this->script_ajax;?>&task=start&id='+id;
                        ChangeStatus(url, id);
                    }
                }
                //$(did).html( msg );
            },
            beforeSend : function(){
                $(did).html('<div style="text-align:center;"><img src="/admin/images/icons/loading_animation_liferay.gif" alt="" title="" /></div>');
            }
            });
         /*

         */
     }
     function ChangeStatus(myurl, id){
         /*
         $.post(url, '',
             function(data){
                 $("#"+div_id).empty();
                 $("#"+div_id).append(data);
             }
         );
         */
         did = "#mailerLoader"+id;
         $.ajax({
                type: "POST",
                url: myurl,
                success: function(msg){
                    $(did).empty();
                    $("#wndw"+id).html( msg );
                },
                beforeSend : function(){
                    $(did).html('<div style="text-align:center;"><img src="/admin/images/icons/loading_animation_liferay.gif" alt="" title="" /></div>');
                }
        });
     }
    </script>
    <?

     return true;
    } // end of function ShowDispatch()

    // ================================================================================================
    // Function : EditDispatch()
    // Version : 1.0.0
    // Date : 04.03.2009
    // Parms :
    // Returns :      true,false / Void
    // Description :  edit subscriber
    // ================================================================================================
    // Programmer :  Ihor Trokhymchuk
    // Date : 04.03.2009
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function EditDispatch($lang_id=3)
    {
        $q = "SELECT * FROM `".TblModMailerDispatch."` where `id`='".$this->id."'";
        $res = $this->Right->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res ) return false;
        $mas = $this->Right->db_FetchAssoc();


        /* Write Form Header */
        $this->Form->WriteHeaderFormImg( $this->script );
        $this->Form->Hidden( 'id_file', '' );
        if( $this->id!=NULL ) $txt = $this->Msg->show_text('_TXT_EDIT_DATA');
        else $txt = $this->Msg->show_text('_TXT_ADD_DATA');
        AdminHTML::PanelSubH( $txt );

        $this->ShowErrBackEnd();

        AdminHTML::PanelSimpleH();
        ?>
         <tr>
          <td width="150"><strong><?=$this->Msg->show_text('_FLD_ID')?>:</strong>
           <?
           if( $this->id!=NULL )
           {
            echo $mas['id'];
            $this->Form->Hidden( 'id', $mas['id'] );
           }
           else $this->Form->Hidden( 'id', '' );
           ?>
          </td>
         </tr>
         </tr>
         <tr>
          <td>
           <div>
            <strong>Статус письма:</strong>&nbsp;
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->status : $val=$mas['status'];
           else $val=$this->status;

           if(empty($val)) $val = 0;
           $this->Form->Hidden( 'status', $val );
          ?>
          </td>
          <td><?=$this->dispatch_statuses[$val];?></td>
         </tr>
         <tr>
          <td>&nbsp;&nbsp;<strong>Дата создания письма:</strong>&nbsp;</td>
          <td>
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->dt : $val=$mas['dt'];
           else $val=$this->dt;
           if(empty($val)) $val = date("Y-m-d");
           $this->Form->TextBox( 'dt', stripslashes($val), 10 );
           ?>
          </td>
         </tr>
         <tr>
          <td><strong>Тема письма:</strong>
          <td>
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->sbj : $val=$mas['sbj'];
           else $val=$this->sbj;
           $this->Form->TextBox( 'sbj', stripslashes($val), 80 );
           ?>
          </td>
         </tr>
         <tr><td height="10"></td></tr>
         <tr>
          <td valign="top"><strong>Письмо:</strong>
          <td>
           <?
           if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->body : $val=$mas['body'];
           else $val=$this->body;
           $settings=SysSettings::GetGlobalSettings();
           $this->Form->textarea_editor = $settings['editer']; //'tinyMCE';
           $this->Form->IncludeSpecialTextArea( $this->Form->textarea_editor );
           $this->Form->SpecialTextArea( $this->Form->textarea_editor, 'body', stripslashes($val), 40, 70, 'style="width:90%;"', $lang_id);
           ?>
          </td>
         </tr>
         <?
        //-------------------- Files Start ---------------------
        ?>
        <tr>
         <td colspan="2">
          <fieldset title="Прикрепленные файлы"><legend><img src='images/icons/files.png' alt="Прикрепленные файлы" title="Прикрепленные файлы" border="0" /> Прикрепленные файлы</legend><?
             //echo '<br>$this->id='.$this->id;
             $files_arr = $this->GetFiles($this->id);
             //print_r($files_arr);
             if ( is_array($files_arr) AND count($files_arr)>0  ){
                ?><table class="EditTable"><?
                 for($jjj=0;$jjj<count($files_arr);$jjj++){
                     ?><tr><td><?=($jjj+1);?>.</td><td><a href="<?=$files_arr[$jjj]['pdf_url'];?>"><?=$files_arr[$jjj]['pdf_filename'];?></a></td><td><input type="button" value="Удалить" onClick="<?=$this->Form->name?>.task.value='del_file';<?=$this->Form->name?>.id_file.value='<?=$files_arr[$jjj]['id'];?>';<?=$this->Form->name?>.submit();"></td></tr><?
                 }
                 ?></table><?
             }

             ?>
             <input type="hidden" name="MAX_UPLOAD_FILES_COUNT" value="<?=MAX_UPLOAD_FILES_COUNT;?>">
             <?
             for($i=0;$i<MAILER_UPLOAD_FILES_COUNT; $i++){
                ?><INPUT TYPE="file" NAME="pfiles[]" size="80" VALUE="<?=$this->pfiles['name'][$i]?>"><br/><?
             }
             ?>
          </fieldset>
         </td>
        </tr>
        <?
        //-------------------- Files End ---------------------
        ?>
        <?
        AdminHTML::PanelSimpleF();
        $this->Form->WriteSavePanel( $this->script );
        /*
        ?>
        <a class="toolbar" href="javascript:<?=$this->Form->name?>.task.value='save';<?=$this->Form->name?>.submit();" onmouseout="MM_swapImgRestore();"  onmouseover="MM_swapImage('save','','images/icons/save_f2.png',1);">
         <img src="images/icons/save.png" alt="Save" title="Save" align="center" name="save" border="0" /> <?=$this->Msg->show_text('TXT_SAVE') ?>
        </a>
        <?
         *
         */
        $this->Form->WriteCancelPanel( $this->script );
        $this->Form->WriteFooter();

        AdminHTML::PanelSubF();
    }//end of function EditDispatch()

    // ================================================================================================
    // Function : CheckDispatch()
    // Version : 1.0.0
    // Date : 04.03.2009
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Checking all fields for filling and validation
    // ================================================================================================
    // Programmer :  Igor Trokhymchuk
    // Date : 04.03.2009
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function CheckDispatch()
    {
        $this->Err=NULL;

        if (empty( $this->sbj )) {
            $this->Err=$this->Err.'Укажите Тему письма <br>';
        }
        if (empty( $this->body )) {
            $this->Err=$this->Err.'Письмо пустое <br>';
        }

        $q = "SELECT * FROM `".TblModMailerDispatch."` WHERE `sbj`='".stripslashes($this->sbj)."' AND `body`='".$this->body."'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res )return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        if($rows>0 and $this->id!=$row['id']) $this->Err = $this->Err."Письмо с темой <u><i>".stripslashes($this->sbj)."</i></u> и таким соержанием уже существует. Укажите другие данные, пожалуйста <br/>";
        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
        $this->CheckFiles();
        return $this->Err;
    } //end of fuinction CheckDispatch()

   // ================================================================================================
   // Function : CheckFiles()
   // Version : 1.0.0
   // Date : 27.01.2009
   //
   // Parms :
   // Returns : true,false / Void
   // Description : Check files
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 27.01.2009
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function CheckFiles()
   {
       if (!isset($_FILES["pfiles"])) return false;
       $cols = count($_FILES["pfiles"]["name"]);
       $arr_keys = array_keys($_FILES["pfiles"]["name"]);
       $cnt0 = count($arr_keys);
       for ($j=0; $j<$cnt0; $j++) {
           $i = $arr_keys[$j];
           if( !empty($_FILES["pfiles"]["name"][$i]) ) {
               if( $_FILES["pfiles"]["error"][$i]!=0 ) $this->Err = $this->Err.' Файл  <u><i>'.$_FILES['pfiles']['name'][$i].'</i></u> поврежден. Использование невозможно.<br/>';
               if ( !is_uploaded_file($_FILES["pfiles"]["tmp_name"][$i]) OR !$_FILES["pfiles"]["size"][$i] ) $this->Err = $this->Err.'Файл <u><i>'.$_FILES['pfiles']['name'][$i].'</i></u> содержит ошибки. Невозможно закачать.<br>';

               $q = "SELECT COUNT(`id`) FROM `".TblModMailerDispatchFiles."` WHERE `pdf_filename`='".$_FILES["pfiles"]["name"][$i]."' AND `id_dispatch`='".$this->id."'";
               $res = $this->Right->Query( $q, $this->user_id, $this->module );
               //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
               if( !$res OR !$this->Right->result ) return false;
               $row = $this->Right->db_FetchAssoc();
               if($row['COUNT(`id`)']>0) $this->Err = $this->Err.'Файл <u><i>'.$_FILES['pfiles']['name'][$i].'</i></u> уже существует<br>';
           }
       }
       return true;
   }//end of function CheckFiles()

   // ================================================================================================
   // Function : AddFiles()
   // Version : 1.0.0
   // Date : 26.01.2009
   //
   // Parms :
   // Returns : true,false / Void
   // Description : Store data to the table
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 26.01.2009
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function AddFiles()
   {
       if( !isset($_FILES["pfiles"]) ) return true;
       $arr_keys = array_keys($_FILES["pfiles"]["name"]);
       $cnt0 = count($arr_keys);
       for ($j=0; $j<$cnt0; $j++) {
           $i = $arr_keys[$j];
           //echo '<br>$i='.$i;
           if( !empty($_FILES["pfiles"]["name"][$i]) ) {
               $tmp_f_name = $_FILES["pfiles"]["tmp_name"][$i];
               $this->pf_filename = $_FILES["pfiles"]["name"][$i];
               $this->pf_url = MAILER_UPLOAD_FILES_PATH.'/'.$this->pf_filename;
               $this->pf_path = SITE_PATH.MAILER_UPLOAD_FILES_PATH.'/'.$this->pf_filename;
               //echo '<br>$this->pf_filename='.$this->pf_filename.'<br>$this->pf_url='.$this->pf_url.'<br>$this->pf_path='.$this->pf_path;
               $uploaddir = SITE_PATH.MAILER_UPLOAD_FILES_PATH;
               if ( !file_exists ($uploaddir) ) mkdir($uploaddir,0777);
               else @chmod($uploaddir,0777);
               if ( copy($tmp_f_name, $this->pf_path) ) {
                   @chmod($uploaddir,0755);
                   $q = "INSERT INTO `".TblModMailerDispatchFiles."` SET
                        `pdf_url`='".$this->pf_url."',
                        `pdf_path`='".$this->pf_path."',
                        `pdf_filename`='".$this->pf_filename."',
                        `id_dispatch`='".$this->id."'
                       ";
                  $res = $this->Right->Query( $q, $this->user_id, $this->module );
                  //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                  if( !$res OR !$this->Right->result ) return false;

               }
               else{
                   @chmod($uploaddir,0755);
                   $this->Err = $this->Err.'Ошибка при сохранении файла <u><i>'.$this->pf_filename.'</i></u><br/>';
                   return false;
               }

           }
       //}
       }
       return true;
   }//end of function AddFiles()

   // ================================================================================================
   // Function : GetFiles()
   // Version : 1.0.0
   // Date : 12.03.2009
   //
   // Parms :
   // Returns : true,false / Void
   // Description : get fiels for dispatch
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 12.03.2009
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetFiles($id_dispatch=NULL)
   {
       if( empty($id_dispatch) ) return false;
       $q = "SELECT * FROM `".TblModMailerDispatchFiles."` WHERE `id_dispatch`='".$id_dispatch."'";
       $res = $this->db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
       if( !$res OR !$this->db->result ) return false;
       $rows = $this->db->db_GetNumRows();
       $arr = NULL;
       for($i=0;$i<$rows;$i++){
           $row = $this->db->db_FetchAssoc();
           $arr[$i]=$row;
       }
       return $arr;
   } //end of function GetFiles()

   // ================================================================================================
   // Function : GetCntFilesDispatch()
   // Version : 1.0.0
   // Date : 12.03.2009
   //
   // Parms :
   // Returns : true,false / Void
   // Description : get count of fiels for dispatch
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 12.03.2009
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetCntFilesDispatch($id_dispatch=NULL)
   {
       $q = "SELECT COUNT(`id`) FROM `".TblModMailerDispatchFiles."` WHERE 1";
       if( !empty($id_dispatch) ) $q = $q." AND `id_dispatch`='".$id_dispatch."'";
       $res = $this->db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
       if( !$res OR !$this->db->result ) return false;
       $row = $this->db->db_FetchAssoc();
       return $row['COUNT(`id`)'];
   } //end of function GetCntFilesDispatch()

   // ================================================================================================
   // Function : DelFile()
   // Version : 1.0.0
   // Date : 12.03.2009
   //
   // Parms :
   // Returns : true,false / Void
   // Description : delete file from dispatch
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 12.03.2009
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function DelFile($id=NULL)
   {
       $q = "DELETE FROM `".TblModMailerDispatchFiles."` WHERE `id`='".$id."'";
       $res = $this->db->db_Query( $q );
       //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
       if( !$res OR !$this->db->result ) return false;
       return true;
   } //end of function DelFile()


    // ================================================================================================
    // Function : SaveDispatch()
    // Version : 1.0.0
    // Date : 04.03.2009
    //
    // Parms :
    // Returns : true,false / Void
    // Description : Store data to the table
    // ================================================================================================
    // Programmer : Igor Trokhymchuk
    // Date : 04.03.2009
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function SaveDispatch()
    {
        $q="SELECT * FROM `".TblModMailerDispatch."` WHERE `id`='".$this->id."'";
        $res = $this->Right->db_Query( $q );
        if( !$res OR !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();

        if($rows>0)
        {
            $q = "UPDATE `".TblModMailerDispatch."` SET
                 `sbj`='".$this->sbj."',
                 `body`='".$this->body."',
                 `status`='".$this->status."',
                 `dt`='".$this->dt."'
                 WHERE `id`='".$this->id."'";
        }
        else{
            $q = "INSERT INTO `".TblModMailerDispatch."` SET
                 `sbj`='".$this->sbj."',
                 `body`='".$this->body."',
                 `status`='".$this->status."',
                 `dt`='".$this->dt."'
                 ";
        }
        $res = $this->Right->db_Query( $q );
        //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res OR !$this->Right->result ) return false;

        if ( empty($this->id) ){
            $this->id = $this->Right->db_GetInsertID();
        }

        //=== add files start ===
        $res = $this->AddFiles();
        //echo '<br>$res='.$res;
        if( !$res ) return false;
        //=== add files end ===

    }//end of fuinction SaveDispatch()

    // ================================================================================================
    // Function : DelDispatch()
    // Version : 1.0.0
    // Date : 04.03.2009
    //
    // Parms :
    // Returns :      true,false / Void
    // Description :  Remove data from the table
    // ================================================================================================
    // Programmer :  Ihor Trokhymchuk
    // Date : 04.02.2005
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function DelDispatch( $id_del )
    {
        $tmp_db = new DB();
        $kol = count( $id_del );
        $del = 0;
        for( $i=0; $i<$kol; $i++ )
        {
         $u = $id_del[$i];

         // delete attached files
         $q="SELECT * FROM `".TblModMailerDispatchFiles."` WHERE `id_dispatch`='".$u."'";
         $res = $tmp_db->db_Query( $q );
         //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
         if( !$res OR !$tmp_db->result ) return false;
         $rows = $tmp_db->db_GetNumRows();
         for($j=0; $j<$rows; $j++){
             $row = $tmp_db->db_FetchAssoc();
             $path = $row['pdf_path'];
             // delete file which store in the database
             if (file_exists($path)) {
                $res = unlink ($path);
                if( !$res ) return false;
             }
         }
         $q = "DELETE FROM `".TblModMailerDispatchFiles."` WHERE `id_dispatch`='".$u."'";
         $res = $this->db->db_Query( $q );
         //echo '<br>q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
         if( !$res OR !$this->db->result ) return false;



         $q = "DELETE FROM `".TblModMailerDispatch."` WHERE `id`='".$u."'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
         if ( $res )
          $del=$del+1;
         else
          return false;
        }
      return $del;
    } // end of function DelDispatch()

    /**
     * check if can start new dispatch
     * @return boolean
     */
    function isCanStart(){
        $q = "SELECT `id` FROM `".TblModMailerDispatch."` WHERE `status`='1' OR `status`='4'";
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.' <br>res='.$res.' $this->db->result='.$this->db->result;
        if(!$res or !$this->db->result) return false;
        $rows_emails = $this->db->db_GetNumRows();
        //echo '<br>$rows_emails='.$rows_emails;
        if($rows_emails>0){
            return false;
        }else{
            return true;
        }
    }

    /**
     * get list of emails from user dataabase who has subscriber staus
     * @param $is_not_send - filter to select emailsto which was not sending current letter.
     * @return array with list of emails
     */
    function getListOfEmails($is_not_send=true, $limit=true){
        $arr_row = array();
        $q = "SELECT
              `".TblModUser."`.*,
              `".TblSysUser."`.`email`
              FROM
              `".TblModUser."`, `".TblSysUser."`
              WHERE
              `".TblModUser."`.`subscr`='1'
              AND `".TblModUser."`.`user_status`='3'
              AND `".TblModUser."`.`sys_user_id`=`".TblSysUser."`.`id`
             ";
        if($is_not_send){
            $q .= " AND `".TblModUser."`.`subscr_is_send_email` = '0'";
        }
        $q .= " GROUP BY `email`
                ORDER BY `id` ASC
              ";
        if($limit){
            $q .= " LIMIT ".$this->subscr_start.",".$this->subscr_cnt;
        }
        $res = $this->db->db_Query($q);
        //echo '<br>'.$q.' <br>res='.$res.' $this->db->result='.$this->db->result;
        if(!$res or !$this->db->result) return false;
        $rows_emails = $this->db->db_GetNumRows();
        //echo '<br>$rows_emails='.$rows_emails;
        for($i=0;$i<$rows_emails;$i++){
            $arr_row[$i] = $this->db->db_FetchAssoc();
        }
        return $arr_row;
    }

    // ================================================================================================
    // Function : ControlDispatchStatus()
    // Version : 1.0.0
    // Date : 04.03.2009
    //
    // Parms :
    // Returns :     true,false / Void
    // Description : Show Dispatch
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 04.03.2009
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ControlDispatchStatus($id=NULL, $status=NULL)
    {
         $tmp_db = new DB();
         if(empty($status)){
             $q = "SELECT `status` FROM `".TblModMailerDispatch."` WHERE `id`='".$id."'";
             $res = $tmp_db->db_Query($q);
             $row = $tmp_db->db_FetchAssoc();
             if(isset($row['status'])){
                 $status = $row['status'];
             }
         }
         echo $this->dispatch_statuses[$status];
         //$q = "SELECT * FROM `".TblModUser."` WHERE `subscr`='1' AND `user_status`='3'";
         //$res = $tmp_db->db_Query($q);
         //$cnt_subscr = $tmp_db->db_GetNumRows();
         $cnt_subscr = count($this->getListOfEmails(false, false));

         //$q = "SELECT * FROM `".TblModUser."` WHERE `subscr`='1' AND `subscr_is_send_email` = '0' AND `user_status`='3'";
         //$res = $tmp_db->db_Query($q);
         //$cnt_not_send = $tmp_db->db_GetNumRows();
         $cnt_not_send = count($this->getListOfEmails(true, false));
         if($status==1 OR $status==4){ ?><br/>Отослано к <?=($cnt_subscr-$cnt_not_send);?> из <?=$cnt_subscr;}

         $q = "SELECT `id` FROM `".TblModMailerDispatch."` WHERE `status`='1' OR `status`='4'";
         $res = $tmp_db->db_Query($q);
         $rows = $tmp_db->db_GetNumRows();
         $row = $tmp_db->db_FetchAssoc();

         if($status==0 OR $status==2 OR $status==3){
             if($rows==0 OR $row['id']==$id){
                 $url = '/modules/mod_mailer/mailer.backend.php?'.$this->script_ajax.'&task=start&id='.$id;
                 ?><br/><input type="button" id="btn<?=$status;?>_<?=$id;?>" value="Старт рассылки" onclick="StartDispatch(<?=$id;?>);"><?
             }
         }
         elseif($status==1){
             $url = '/modules/mod_mailer/mailer.backend.php?'.$this->script_ajax.'&task=pause&id='.$id;
             ?><br/><input type="button" value="Пауза" onclick="if( !window.confirm('Вы точно хотите поставить на ПАУЗУ рассылку этого письма?') ) return false; else ChangeStatus('<?=$url;?>', '<?=$id;?>');">
             <?$url = '/modules/mod_mailer/mailer.backend.php?'.$this->script_ajax.'&task=stop&id='.$id?>
             &nbsp;<input type="button" value="Стоп" onclick="if( !window.confirm('Вы точно хотите ОСТАНОВИТЬ рассылку этого письма?') ) return false; else ChangeStatus('<?=$url;?>', '<?=$id;?>');"><?
         }
         elseif($status==4){
             $url = '/modules/mod_mailer/mailer.backend.php?'.$this->script_ajax.'&task=continue&id='.$id;
             ?><br/><input type="button" value="Продожить рассылку" onclick="if( !window.confirm('Вы точно хотите ПРОДЛЖИТЬ рассылку этого письма?') ) return false; else ChangeStatus('<?=$url;?>', '<?=$id;?>');">
             <?$url = '/modules/mod_mailer/mailer.backend.php?'.$this->script_ajax.'&task=stop&id='.$id?>
             &nbsp;<input type="button" value="Стоп" onclick="if( !window.confirm('Вы точно хотите ОСТАНОВИТЬ рассылку этого письма?') ) return false; else ChangeStatus('<?=$url;?>', '<?=$id;?>');"><?
         }
         ?><div id="mailerLoader<?=$id;?>"></div><?
    }// end of function ControlDispatchStatus()

    // ================================================================================================
    // Function : ChangeDispatchStatus()
    // Version : 1.0.0
    // Date : 04.03.2009
    //
    // Parms :
    // Returns :     true,false / Void
    // Description : Show Dispatch
    // ================================================================================================
    // Programmer : Ihor Trokhymchuk
    // Date : 04.03.2009
    // Reason for change : Creation
    // Change Request Nbr:
    // ================================================================================================
    function ChangeDispatchStatus($id=NULL, $status=NULL)
    {
        if(!$id) return false;
        $q = "UPDATE `".TblModMailerDispatch."` SET `status`='".$status."' WHERE `id`='".$id."'";
        $res = $this->db->db_Query($q);
        //echo '<br>$q='.$q.' res='.$res.' $this->db->result='.$this->db->result;
        if (!$res or !$this->db->result) return false;
        if($status==3){
            $q = "UPDATE `".TblModUser."` SET `subscr_is_send_email`='0'";
            $res = $this->db->db_Query($q);
        }
        return true;
    }//end of function ChangeDispatchStatus()

     // ================================================================================================
     // Function : MakeDispatch()
     // Version : 1.0.0
     // Date : 05.05.2008
     // Parms :
     // Returns :      true,false / Void
     // Description :  create dispatch
     // ================================================================================================
     // Programmer :  Igor Trokhymchuk
     // Date : 05.05.2008
     // Reason for change : Creation
     // Change Request Nbr:
     // ================================================================================================
     function MakeDispatch()
     {
         $tmp_db = new DB();
         $mail = new Mail();

         //only for programm testing
         //$this->subscr_cnt=1;

         $q = "SELECT * FROM `".TblModMailerDispatch."` WHERE 1";
//         if( $this->task=='test_send' ) $q = $q." AND `id`='".$this->id."'";
         //else
         if( $this->task != 'test_send' ) $q = $q." AND `status`=1";
         else $q = $q." AND `id`='".$this->id."'";
         $res = $tmp_db->db_Query($q);
//         echo '<br>$q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
         if (!$res OR !$tmp_db->result) return false;
         $rows_dispatch = $tmp_db->db_GetNumRows();
         //echo '<br>$rows_dispatch='.$rows_dispatch;
         //if now records to make dispatch then go out.
         if($rows_dispatch==0) return false;


         if( $this->task=='test_send' ){
             $SysSet = new SysSettings();
             $sett = $SysSet->GetGlobalSettings();
             if( !empty($sett['mail_auto_emails'])){
                 $hosts = explode(";", $sett['mail_auto_emails']);
                 $rows_emails = count($hosts);
                 for($i=0;$i<$rows_emails;$i++){
                     $arr_row[$i]['email'] = $hosts[$i];
                 }//end for
             }

         }
         else{
             $arr_row = $this->getListOfEmails();
             $rows_emails = count($arr_row);
         }

         $arr=array();
         for($j=0;$j<$rows_dispatch;$j++){
             $row_dispatch = $tmp_db->db_FetchAssoc();
             //echo '<br>$i='.$i;
             $body = stripslashes($row_dispatch['body']);
             /*
             $arr_html_img = array();
             $arr_html_img = array_merge($arr_html_img, $mail->ConvertHtmlWithImagesForSend($body));
             //print_r($arr_html_img);
             $body = $arr_html_img['content'];
              *
              */
             for($i=0;$i<$rows_emails;$i++){
                $row = $arr_row[$i];
                $mail = new Mail();

                //=== add images from email body as attachment ===
                if(isset($arr_html_img)){
                    foreach($arr_html_img as $key=>$value){
                        //echo '<br>$key='.$key;
                        if( $key!='content') $mail->AddAttachment($key);
                    }
                }
                //=== add files as attachment ===
                $files_arr = $this->GetFiles($row_dispatch['id']);
                for($jjj=0;$jjj<count($files_arr);$jjj++){
                    $mail->AddAttachment($files_arr[$jjj]['pdf_path']);
                }

                $email = addslashes(trim($row['email']));
                $mail->AddAddress($email);
                $mail->WordWrap = 500;
                $mail->IsHTML( true );
                //$mail->IsMail( true );
                $mail->Subject = stripslashes($row_dispatch['sbj']);
                $mail->Body = $body;
                //$mail->insert_header = false;
                //$mail->insert_footer = false;
                $res = $mail->SendMail();
                if($this->showResult){
                    if($res){
                        echo '<br>$email='.$email.' - OK!';
                    }else{
                        echo '<br>$email='.$email.' - ОШИБКА!';
                    }
                }

                //независимо от результата отправки в любом случае прописываем в базу, что письмо отправлено. что бы не стопорилась рассылка
                //из-за неправильных емейлов
                if( $this->task!='test_send') {
                    $q = "UPDATE `".TblModUser."` SET `subscr_is_send_email`='1' WHERE `id`='".$row['id']."'";
                    //echo '<br>$q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
                    $res = $tmp_db->db_Query($q);
                }

             }//end for

             //check if email send to all subscribers
             $rows = count($this->getListOfEmails(true, false));
             if($rows==0){
                $q = "UPDATE `".TblModMailerDispatch."` SET `status`='2' WHERE `id`='".$row_dispatch['id']."'";
                $res = $this->db->db_Query($q);
                //echo '$q='.$q.'<br>res='.$res.' $this->db->result='.$this->db->result;

                $q = "UPDATE `".TblModUser."` SET `subscr_is_send_email`='0'";
                $res = $this->db->db_Query($q);
                //echo '$q='.$q.'<br>res='.$res.' $this->db->result='.$this->db->result;
             }

         }
         return true;
     }//end of function MakeDispatch()


   // ================================================================================================
   // Function : GetRequestData()
   // Version : 1.0.0
   // Date : 18.01.2009
   //
   // Parms :   $str - string from $_REQUEST array
   // Returns : true,false / formated string
   // Description : get ruquest data
   // ================================================================================================
   // Programmer : Igor Trokhymchuk
   // Date : 18.01.2009
   // Reason for change : Creation
   // Change Request Nbr:
   // ================================================================================================
   function GetRequestData($str=NULL)
   {
       return addslashes(trim($str));
   } //end of function GetRequestData()

} //end of class Partners