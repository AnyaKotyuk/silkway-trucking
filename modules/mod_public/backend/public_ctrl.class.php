<?php
// ================================================================================================
// System : SEOCMS
// Module : public_ctrl.class.php
// Version : 1.0.0
// Date : 01.02.2007
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with managment of publications
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_public/public.defines.php' );

// ================================================================================================
//    Class             : Public_ctrl
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
 class Public_ctrl extends Publications {

       var $user_id = NULL;
       var $module = NULL;
       var $Err = NULL;
       var $lang_id = NULL;
       var $sel = NULL;

       // ================================================================================================
       //    Function          : Public_ctrl (Constructor)
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
       function Public_ctrl ($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
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
                if (empty($this->Form)) $this->Form = new Form('form_mod_public_ctrl');
                if (empty($this->Spr)) $this->Spr = new  SysSpr($this->user_id, $this->module);
                $this->multi = check_init_txt('TblBackMulti',TblBackMulti);
       } // End of Public_ctrl Constructor


       // ================================================================================================
       // Function : show
       // Version : 1.0.0
       // Date : 01.02.2007
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show data from $module table
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 01.02.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function show()
       {
        //if( !$this->sort )
        $this->sort='dt desc';
        $q = "SELECT * FROM `".TblModPublic."` WHERE 1";
        if ( !empty($this->fltr)) $q = $q." AND `categ`='$this->fltr'";
        if ( !empty($this->fltr2)) $q = $q." AND `group`='$this->fltr2'";
        if ( !empty($this->fltr3)) $q = $q." AND `status`='$this->fltr3'";
        if ( !empty($this->srch)) $q = $q." AND `title` LIKE '%$this->srch%'";
        if ( !empty($this->srch2)) $q = $q." AND (`text` LIKE '%$this->srch2%' OR `contact` LIKE '%$this->srch2%')";
        $q = $q." order by $this->sort";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res.'$this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
        if( !$res )return false;

        $rows = $this->Right->db_GetNumRows();

        $j = 0;
        $row_arr = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
          $row = $this->Right->db_FetchAssoc();
          if( $i >= $this->start && $i < ( $this->start+$this->display ) )
          {
            $row_arr[$j] = $row;
            $j = $j + 1;
          }
        }

        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );

        $this->ShowContentFilters();

        /* Write Table Part */
        AdminHTML::TablePartH();

        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=10>';
        $script1 = 'module='.$this->module.'&fltr='.$this->fltr;
        $script1 = $_SERVER['PHP_SELF']."?$script1";
        //echo '$this->sort='.$this->sort;
        $this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );

        echo '<TR><TD COLSPAN=9>';
        $this->Form->WriteTopPanel( $this->script );

        $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
        $script2 = $_SERVER['PHP_SELF']."?$script2";

        // for selection
        if ($this->sel==1)$scr = 2;
        else $scr = 1;
       ?>
        <TR>
        <Th class="THead"><a href="<?=$script2?>&amp;sel=<?=$scr?>">*</a></Th>
        <Th class="THead"><A HREF=<?=$script2?>&sort=id><?=$this->multi['FLD_ID']?></A></Th>
        <?/*?><Th class="THead"><A HREF=<?=$script2?>&sort=categ><?=$this->Msg->show_text('FLD_CATEGORY')?></A></Th>
        <Th class="THead"><A HREF=<?=$script2?>&sort=group><?=$this->Msg->show_text('FLD_GROUP')?></A></Th>
        <?*/?><Th class="THead"><A HREF=<?=$script2?>&sort=title><?=$this->multi['TXT_TITLE']?></A></Th>
        <Th class="THead"><?=$this->multi['TXT_PREVIEW_PICTURES']?></Th>
        <Th class="THead"><?=$this->multi['FLD_TEXT']?></Th>
        <Th class="THead"><?=$this->multi['FLD_PHONE']?></Th>
        <Th class="THead"><A HREF=<?=$script2?>&sort=dt><?=$this->multi['FLD_DT']?></A></Th>
        <Th class="THead"><A HREF=<?=$script2?>&sort=status><?=$this->multi['FLD_STATUS']?></A></Th>
       <?

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

          echo '<TD>';
          $this->Form->CheckBox( "id_del[]", $row['id'], $this->sel);

          echo '<TD>';
          $this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->multi['_TXT_EDIT_DATA'] );

          //echo '<TD>'; echo stripslashes($this->Spr->GetNameByCod( TblModPublicSprCateg, $row['categ'] )); echo '</TD>';

          //echo '<TD>'; echo stripslashes($this->Spr->GetNameByCod( TblModPublicSprGroup, $row['group'] )); echo '</TD>';

          echo '<TD>'.$row['title'];
          echo '<TD>'.$this->showImg($row['img'],$row['id'],100,100);
          echo '<TD>'.$row['text'];
          echo '<TD>'.$row['contact'];
          echo '<TD>'.$row['dt'];
          echo '<TD>'; echo stripslashes($this->Spr->GetNameByCod( TblModPublicSprStatus, $row['status'] )); echo '</TD>';
        } //-- end for

        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;

       } //end of fuinction show()

       // ================================================================================================
       // Function : edit()
       // Version : 1.0.0
       // Date : 01.02.2007
       //
       // Parms : id/id of the record
       // Returns : true,false / Void
       // Description : Show data from $spr table for editing
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 01.02.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================

       function edit( $id=NULL )
       {
        $Panel = new Panel();
        $ln_sys = new SysLang();
        $calendar = new DHTML_Calendar(false, 'en', 'calendar-win2k-2', false);
        $calendar->load_files();
        $mas=NULL;

        $fl = NULL;

        /* set action page-adress with parameters */
        //$script = $_SERVER['PHP_SELF'].'?module='.$this->module.'&display='.$_REQUEST['display'].'&start='.$_REQUEST['start'].'&sort='.$_REQUEST['sort'];
        //$script = $_SERVER['PHP_SELF'].'?module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fltr2='.$this->fltr2.'&fln='.$this->fln.'&level='.$this->level;

        if( $id!=NULL and ( $mas==NULL ) )
        {
         $q="select * from `".TblModPublic."` where id='$id'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         if( !$this->Right->result ) return false;
         $mas = $this->Right->db_FetchAssoc();
        }

        /* Write Form Header */
        $this->Form->WriteHeaderFormImg( $this->script );

        if( $id!=NULL ) $txt = $this->multi['_TXT_EDIT_DATA'];
        else $txt = $this->multi['_TXT_ADD_DATA'];

        AdminHTML::PanelSubH( $txt );

        //-------- Show Error text for validation fields --------------
        $this->ShowErrBackEnd();
        //-------------------------------------------------------------

        AdminHTML::PanelSimpleH();
       ?>
        <TABLE BORDER=0 class="EditTable">
        <TR><TD><b><?echo $this->Msg->show_text('FLD_ID')?>:</b>
        <TD width="100%">
       <?
          if( $id!=NULL )
          {
           echo $mas['id'];
           $this->Form->Hidden( 'id', $mas['id'] );
          }
          else $this->Form->Hidden( 'id', '' );
       //echo '<br>$this->level='.$this->level;
        ?><TR><TD><b><?echo $this->Msg->show_text('FLD_STATUS')?>:</b>
            <TD>
            <?
            if( $id!=NULL ) $this->Err!=NULL ? $val=$this->status : $val=$mas['status'];
            else $val=$this->status;
            if (empty($val)) $val=1;
            echo $this->Spr->ShowInComboBox( TblModPublicSprStatus, 'status', $val, 50 );
            ?></TD>
        </TR>

        <TR><TD><b><?echo $this->Msg->show_text('FLD_DT')?>:</b>
            <TD>
            <?
            if( $id!=NULL ) $this->Err!=NULL ? $val=$this->dt : $val=$mas['dt'];
            else $val=date("Y-m-d H:i:s");
            //echo $this->Form->TextBox( 'dt', stripslashes($val), 20 );

                /*if( $this->id!=NULL ) $this->Err!=NULL ? $val=$this->dttm : $val=$mas['dttm'];
                else $this->Err!=NULL ? $val=$this->dttm : $val = strftime('%Y-%m-%d %H:%M', strtotime('now'));*/

            $a1 = array('firstDay'       => 1, // show Monday first
                         'showsTime'      => true,
                         'showOthers'     => true,
                         'ifFormat'       => '%Y-%m-%d %H:%M',
                         'timeFormat'     => '12');
              $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                          'name'        => 'dt',
                          'value'       => $val );
              //echo '<br>$a1='.$a1.' $a2='.$a2.' $val='.$val;
              $calendar->make_input_field( $a2, $a1 );

            ?>
        <TR style="display: none;"><TD><b><?echo $this->Msg->show_text('FLD_CATEGORY')?>:</b>
            <TD>
            <?
            //if( $id!=NULL ) $this->Err!=NULL ? $val=$this->categ : $val=$mas['categ'];
            //else $val=$this->categ;
            $val = 1;
            echo $this->Spr->ShowInComboBox( TblModPublicSprCateg, 'categ', $val, 50 );
            ?>

        <TR style="display: none;"><TD><b><?echo $this->Msg->show_text('FLD_GROUP')?>:</b>
            <TD>
            <?
            //if( $id!=NULL ) $this->Err!=NULL ? $val=$this->group : $val=$mas['group'];
            //else $val=$this->group;
            $val = 1;
            echo $this->Spr->ShowInComboBox( TblModPublicSprGroup, 'group', $val, 50 );
            ?></td></tr>
        <tr>
            <td><b><?=$this->multi['_FLD_IMG'];?>:</b></td>
                <?
               // echo $img;
                ?>
            <td><input type="file" name="filename[]" size="40" value="<?=$this->img;?>"/></td>
        </tr>
        <?if($id!=NULL && !empty($mas['img'])){?>
        <tr>
            <td><b><?=$this->multi['TXT_PREVIEW_PICTURES']?>:</b></td>
            <td><?=$this->showImg($mas['img'],$id,150,150);?></td>
        </tr>
        <?}?>
        <tr><td colspan="2"><?
        $Panel->WritePanelHead( "SubPanel_" );
        $ln_arr = $ln_sys->LangArray( _LANG_ID );
        //echo 'lll=';print_r($ln_arr);
        while( $el = each( $ln_arr ) )
        {
            $lang_id = $el['key'];
            $lang = $el['value'];
            $Panel->WriteItemHeader( $lang );?>
            <TABLE class='EditTable'>
            <TR><TD colspan=2><b><?echo $this->Msg->show_text('TXT_TITLE')?>:</b>
            <TR><TD colspan=2>
                <?
                if( $id!=NULL ) $this->Err!=NULL ? $val=$this->title : $val=$mas['title'];
                else $val=$this->title;
                $this->Form->TextBox( 'title', stripslashes($val), 80 );
                ?>

            <TR><TD colspan=2><b><?echo $this->Msg->show_text('FLD_TEXT')?>:</b>
            <TR><TD colspan=2>
                <?
                if( $id!=NULL ) $this->Err!=NULL ? $val=$this->text : $val=$mas['text'];
                else $val=$this->text;
                $this->Form->TextArea( 'text',stripslashes($val), 5, 70 );
                //$this->Form->HTMLTextArea( 'text', stripslashes($val), 8, 70 );
                ?>

            <TR><TD colspan=2><b><?echo $this->Msg->show_text('FLD_PHONE')?>:</b>
            <TR><TD colspan=2>
                <?
                if( $id!=NULL ) $this->Err!=NULL ? $val=$this->contact : $val=$mas['contact'];
                else $val=$this->contact;
                $this->Form->TextBox( 'contact', stripslashes($val), 70 );
                ?></TABLE><?
                $Panel->WriteItemFooter();
        }
        $Panel->WritePanelFooter();
        echo '<TR><TD COLSPAN=2 ALIGN=left>';
        $this->Form->WriteSaveAndReturnPanel( $this->script );
        $this->Form->WriteSavePanel( $this->script );
        $this->Form->WriteCancelPanel( $this->script );
        echo '</table>';
        AdminHTML::PanelSimpleF();
        AdminHTML::PanelSubF();

        $this->Form->WriteFooter();
        return true;
       } // end of function edit()

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
       function save( $id )
       {
        $q="select * from `".TblModPublic."` where id='$id'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$this->Right->result ) return false;
        $rows = $this->Right->db_GetNumRows();
        $row = $this->Right->db_FetchAssoc();
        //phpinfo();

        if($rows>0)
        {
          $q="update `".TblModPublic."` set
              `categ`='$this->categ',
              `group`='$this->group',
              `title`='$this->title',
              `text`='$this->text',
              `contact`='$this->contact',
              `dt`='$this->dt',
              `status`='$this->status',
              `time` = '$this->time'
              where id='$id'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
          if( !$res ) return false;
          if( !$this->Right->result ) return false;
        }
        else
        {
          $q="insert into `".TblModPublic."` set
              `categ`='$this->categ',
              `group`='$this->group',
              `title`='$this->title',
              `text`='$this->text',
              `contact`='$this->contact',
              `dt`='$this->dt',
              `status`='$this->status',
              `time` = '$this->time' ";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
          if( !$this->Right->result) return false;
        }
        if( $rows == 0 ) $this->id = $this->Right->db_GetInsertID();
        $res = $this->SavePicture();
        return true;
       } // end of function save()



       // ================================================================================================
       // Function : del()
       // Version : 1.0.0
       // Date : 01.02.2007
       // Parms :   $user_id, $module_id, $id_del
       // Returns : true,false / Void
       // Description :  Remove data from the table
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 01.02.2007
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================

       function del( $id_del )
       {
        $tmpdb = new DB();

        $del = 0;
        $kol = count( $id_del );
        for( $i=0; $i<$kol; $i++ )
        {
         $u=$id_del[$i];
         $res = $this->DelPicture( $u);
         if (!$res) return false;
         $q = "DELETE FROM ".TblModPublic." WHERE id='$u'";
         $res = $tmpdb->db_Query( $q );
         //echo '<br>q='.$q.' res='.$res.' $tmpdb->result='.$tmpdb->result;

         if ( $res ) $del=$del+1;
         else return false;
        }
        return $del;
       } //end of function del()


       // ================================================================================================
       // Function : ShowContentFilters
       // Version : 1.0.0
       // Date : 27.03.2006
       //
       // Parms :
       // Returns : true,false / Void
       // Description : Show filters and search form
       // ================================================================================================
       // Programmer : Igor Trokhymchuk
       // Date : 27.03.2006
       // Reason for change : Creation
       // Change Request Nbr:
       // ================================================================================================
       function ShowContentFilters()
       {
         /* Write Table Part */
         AdminHTML::TablePartH();
           //phpinfo();
         ?>
         <table border=0 cellpadding=0 cellspacing=0>
          <tr valign=top>
           <td>
             <table border=0 cellpadding=2 cellspacing=1>
              <tr><td><h4><?=$this->Msg->show_text('TXT_FILTERS');?></h4></td></tr>
              <?/*?>
              <tr class=tr1>
               <td align=left><?=$this->Msg->show_text('FLD_CATEGORY');?></td>
               <td align=left><?$this->Spr->ShowActSprInCombo(TblModPublicSprCateg, 'fltr', $this->fltr, $_SERVER["QUERY_STRING"]);?></td>
              </tr>
              <tr class=tr2>
               <td align=left><?=$this->Msg->show_text('FLD_GROUP');?></td>
               <td align=left><?$this->Spr->ShowActSprInCombo(TblModPublicSprGroup, 'fltr2', $this->fltr2, $_SERVER["QUERY_STRING"]);?></td>
              </tr>
              <?*/?>
              <tr class=tr1>
               <td align=left><?=$this->Msg->show_text('FLD_STATUS');?></td>
               <td align=left><?$this->Spr->ShowActSprInCombo(TblModPublicSprStatus, 'fltr3', $this->fltr3, $_SERVER["QUERY_STRING"]);?></td>
              </tr>
             </table>
           </td>
           <td width=30></td>

           <td>
             <table border=0 cellpadding=2 cellspacing=1>
              <tr><td><h4><?=$this->multi['TXT_SEARCH']?></h4></td></tr>
              <tr class=tr1>
               <td><?=$this->multi['FLD_PROD_ID'];?></td>
               <td><?$this->Form->TextBox('srch', $this->srch, 40);?></td>
              <tr class=tr1>
               <td><?=$this->multi['TXT_TEXT_PUBLIC'];?></td>
               <td><?$this->Form->TextBox('srch2', $this->srch2, 40);?></td>
              <tr class=tr2>
               <td colspan=2><?$this->Form->Button( '', $this->multi['TXT_SEARCH'], 50 );?></td>
              <tr>
              </tr>
             </table>
           </td>
          </tr>
         </table>
         <?
         AdminHTML::TablePartF();

       } //end of fuinction ShowContentFilters()

       function showSet(){
        $q = "SELECT * FROM `".TblModPublicSet."` WHERE 1";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>$q='.$q.' $res='.$res.'$this->Right->result='.$this->Right->result.' $this->user_id='.$this->user_id;
        if( !$res )return false;

        $row = $this->Right->db_FetchAssoc();

        $this->script = $_SERVER['PHP_SELF'].'?module=104';
        /* Write Form Header */
        //echo '$this->script='.$this->script;
        $this->Form->WriteHeader( $this->script,' ','setSave' );
        /* Write Table Part */
        AdminHTML::TablePartH();

        ?><tr>
            <td>Количество дней жизни объявлений:</td>
            <td><input type="text" name="day" value="<?=$row['day']?>" /></td>
            <td style="width: 70%;"></td>
        </tr><?

        AdminHTML::TablePartF();
        $this->Form->WriteSavePanel( $this->script ,'setSave');
        $this->Form->WriteFooter();

       }

       function saveSet(){
            if(isset($_REQUEST['day']) && !empty($_REQUEST['day'])){
                $day = $_REQUEST['day'];
                $q="update `".TblModPublicSet."` set day='".$day."';";
                $res = $this->Right->Query( $q, $this->user_id, $this->module );
                //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                if( !$res || !$this->Right->result ) return false;
                return true;
            }else return false;
       }

 } //end of class Public_ctrl
