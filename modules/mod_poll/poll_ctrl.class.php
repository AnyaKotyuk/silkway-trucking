<?
// ================================================================================================
//    System     : CMS
//    Module     : POLL
//    Date       : 11.02.2011
//    Licensed To: Yaroslav Gyryn
//    Purpose    : POLL - module
// ================================================================================================

// ================================================================================================
//    Class             : PollCtrl
//    Date              : 11.02.2011
//    Constructor       : Yes
//    Parms             : no
//    Returns           : None
//    Description       : Poll Control Class Definition of POLL Module
//    Programmer        :  Yaroslav Gyryn
// ================================================================================================
 class PollCtrl extends Poll
 {
   var $Right;
   var $Form;
   var $Msg;
   var $Spr;

   var $display;
   var $sort;
   var $start;

   var $user_id;
   var $module;

   var $fltr_status;    // filter Status
   var $fltr_type;      // filter Status
   var $sel = NULL;
   var $Err = NULL;

  // ================================================================================================
  //    Function          : PollCtrl (Constructor)
  //    Version           : 1.0.0
  //    Date              : 11.02.2011
  //    Parms             : no
  //    Returns           : true/false
  //    Description       : Constructor of Poll Control Class Definition
  // ================================================================================================
  function PollCtrl($user_id, $module)
  {
     ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
     ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );


    $this->Right =  new Rights($this->user_id, $this->module);                   /* create Rights obect as a property of this class */
    $this->Form = new Form( 'form_poll' );        /* create Form object as a property of this class */
    $this->Msg = new ShowMsg();                   /* create ShowMsg object as a property of this class */
    $this->Spr = new SysSpr( $this->user_id, $this->module ); /* create SysSpr object as a property of this class */
    $this->Msg->SetShowTable( TblModPollSprTxt );
    $this->CheckStatus();
    if(empty($this->multi)) $this->multi = check_init_txt('TblBackMulti', TblBackMulti);
  }

  // ================================================================================================
  // Function : show()
  // Date : 11.02.2011
  // Returns :     true,false / Void
  // Description : Show POLLs
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function show()
  {
    $row_arr = NULL;

    $script1 = 'module='.$this->module;
    $script1 = $_SERVER['PHP_SELF']."?$script1";

    $script = $script1.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
    $script2 = $script;
    //$script = $script.'&fltr_status='.$this->fltr_status.'&fltr_type='.$this->fltr_type;
    if( !$this->sort ) $this->sort = 'id';
    if( $this->sort == 'id' ) $this->sort = $this->sort.' desc';
    $q = "SELECT * FROM ".TblModPoll." where 1 ";
    if( $this->fltr_status ) $q = $q." and $this->fltr_status";
    if( $this->fltr_type ) $q = $q." and $this->fltr_type";
    $q = $q." order by $this->sort";

    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$res )return false;

    $rows = $this->Right->db_GetNumRows();

    /* Write Table Part */
    AdminHTML::TablePartH();

    echo '<TR><TD COLSPAN=11>';
    /* Write Links on Pages */
    $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

    echo '<TR><TD COLSPAN=7>';
    /* Write Form Header */
    $this->Form->WriteHeader( $script );
    $this->Form->WriteTopPanel( $script );

    echo '<TD>';
    $arr[''] = ' ';
    $arr["`status`='a'"] = $this->Msg->show_text('STATUS_ACTIVE');;
    $arr["`status`='e'"] = $this->Msg->show_text('STATUS_EXPIRED');;
    $arr["`status`='i'"] = $this->Msg->show_text('STATUS_INACTIVE');;
    $this->Form->SelectAct( $arr, 'status', $this->fltr_status, "onChange=\"location='$script2'+'&fltr_status='+this.value\"" );

    /*
    echo '<TD>';
    $arr = NULL;
    $arr[''] = ' ';
    $arr["`type`='sys'"] = $this->Msg->show_text('TYPE_SYS');
    $arr["`type`='users'"] = $this->Msg->show_text('TYPE_USERS');
    $this->Form->SelectAct( $arr, 'type', $this->fltr_type, "onChange=\"location='$script2'+'&fltr_type='+this.value\"" );
    */
 if ($this->sel==1){
 $scr = 2;
 }
 else $scr = 1;
?>
 <TR>
 <td class="THead"><a href="<?=$script2?>&amp;sel=<?=$scr?>">*</a></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=id><?=$this->Msg->show_text('FLD_ID')?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=question><?=$this->Msg->show_text('FLD_QUESTION')?></A></Th>
 <td class="THead"><?=$this->Msg->show_text('FLD_ALTERNATIVE')?></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=vote><?=$this->Msg->show_text('FLD_VOTE')?></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=start_date><?=$this->Msg->show_text('FLD_START_DATE')?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=end_date><?=$this->Msg->show_text('FLD_END_DATE')?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=status><?=$this->Msg->show_text('FLD_STATUS')?></A></Th>
 <?/*<td class="THead"><A HREF=<?=$script?>&sort=type><?=$this->Msg->show_text('FLD_TYPE')?></A></Th>*/?>
 <td class="THead" colspan=2><A HREF=<?=$script?>&sort=users_answers><?=$this->Msg->show_text('FLD_USER_ANSWERS')?></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=multy><?=$this->Msg->show_text('FLD_MULTY')?></a></Th>
 <td class="THead"><?=$this->Msg->show_text('FLD_IP')?></Th>
 <?
  $j = 0;
  for( $i = 0; $i < $rows; $i++ )
  {
   $row = $this->Right->db_FetchAssoc();
   if( $i >= $this->start && $i < ( $this->start + $this->display ) )
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

    echo '<TD>';
   $this->Form->CheckBox( "id_del[]", $row['id'], $this->sel);;

    echo '<TD>';
    $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('_TXT_EDIT_DATA') );

    echo '<TD align=center>'.$this->Spr->GetNameByCod( TblModPollSprQ, $row['id'] ).'</TD>';

    echo '<TD align=center> ';
    $this->Form->Link( $script."&task=show_alt&poll_id=".$row['id'], ''.$this->GetCountAlternatives($row['id']).'', $this->Msg->show_text('_TXT_EDIT_DATA') );

    $sm = $this->GetSUMVote( $row['id'] );
    $cn = $this->GetCountIP( $row['id'] );
    echo '<TD align=center> '.$sm.'/'.$cn;

    echo '<TD align=center>'.stripslashes( $row['start_date'] ).'</TD>';

    echo '<TD align=center>'.stripslashes( $row['end_date'] ).'</TD>';

    echo '<TD align=center>';
    if( $row['status'] =='i' ) echo $this->Msg->show_text('STATUS_INACTIVE');
    if( $row['status'] =='e' ) echo $this->Msg->show_text('STATUS_EXPIRED');
    if( $row['status'] =='a' ) echo $this->Msg->show_text('STATUS_ACTIVE');

    /*
    echo '<TD align=center>';
    if( $row['type'] =='sys' ) echo $this->Msg->show_text('TYPE_SYS');
    if( $row['type'] =='users' ) echo $this->Msg->show_text('TYPE_USERS');
    */

    echo '<TD align=center>';
    if( trim( $row['users_answers'] ) == 'on' ) $this->Form->ButtonCheck();
    $cn = $this->GetCountUsersAnswers( $row['id'] );
    echo '<TD align=center>';
    if( $cn > 0 )
      $this->Form->Link( $script."&task=show_answer&poll_id=".$row['id'], $cn, $this->Msg->show_text('_TXT_EDIT_DATA') );
    else echo $cn;

    echo '<TD align=center>';
    if( trim( $row['multy'] ) == 'on' ) $this->Form->ButtonCheck();

    echo '<td align=center>';
    $this->Form->Link( $script."&task=show_ip&poll_id=".$row['id'], ' >> ', $this->Msg->show_text('_TXT_EDIT_DATA') );
   } //--- end for

    AdminHTML::TablePartF();
  } //--- end of show



  // ================================================================================================
  // Function : edit()
  // Date : 11.02.2011
  // Parms :
  //                 $id   / id of editing record / Void
  //                 $mas  / array of form values
  // Returns : true,false / Void
  // Description : edit/add records in FAQ module
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function edit( $id, $mas = NULL )
  {
   $Panel = new Panel();
   $ln_sys = new SysLang();
   $fl = NULL;
   $arr_alt = NULL;           //--- Array of Alternatives
   $calendar = new DHTML_Calendar(false, 'en');
   echo $calendar->load_files();

   if( $mas ){$fl = 1;}
   $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;
   //.'&fltr_status='.$this->fltr_status.'&fltr_type='.$this->fltr_type;
   $script = $_SERVER['PHP_SELF']."?$script";
   if( $id != NULL and ( $mas==NULL ) )
   {
     $q = "SELECT * FROM ".TblModPoll." where `id`='".$id."'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res ) return false;
     $mas = $this->Right->db_FetchAssoc();
   }
   if( $id != NULL ) $txt = $this->multi['_TXT_EDIT_DATA'];
   else $txt = $this->multi['_TXT_ADD_DATA'];

   /* Write Form Header */
   $this->Form->WriteHeader( $script );

   AdminHTML::PanelSubH( $txt );
   /* Write Simple Panel*/
   AdminHTML::PanelSimpleH();
   ?>
<table class="EditTable" width="100%" border="0">
 <tr>
  <td width="20%" class="var_name"><?=$this->Msg->show_text('FLD_ID')?>:
   <?
   if( $id != NULL )
   {
    echo $mas['id'];
    $this->Form->Hidden( 'id', $mas['id'] );
   }else $this->Form->Hidden( 'id', '' );
   ?>
  </td>
  <td></td>
 </tr>
 <tr>
  <td class="var_name"><?=$this->Msg->show_text('FLD_START_DATE');?>:</td>
  <td>

   <?
      if( $this->id!=NULL ) $this->Err!=NULL ? $start_date_val=$this->start_date : $start_date_val=$mas['start_date'];
      else  $start_date_val=strftime('%Y-%m-%d %H:%M', strtotime('+30 days'));
      $a1 = array('name'        => 'start_date',
                  'value'       => $start_date_val );
      $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                  'name'        => 'start_date',
                  'value'       => $start_date_val );
      $calendar->make_input_field($a1,$a2);
   ?>
  </td>
 </tr>
 <tr>
  <td class="var_name"><?=$this->Msg->show_text('FLD_END_DATE');?>:</td>
  <td>
   <?
      if( $this->id!=NULL ) $this->Err!=NULL ? $end_date_val=$this->end_date : $end_date_val=$mas['end_date'];
      else $end_date_val=strftime('%Y-%m-%d %H:%M', strtotime('+30 days'));

      $a1 = array('name'        => 'end_date',
                  'value'       => $end_date_val );
       $a2 = array('style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',
                  'name'        => 'end_date',
                  'value'       => $end_date_val );
      $calendar->make_input_field( $a1,$a2);

      //if( !isset( $mas['end_date'] ) ) $mas['end_date'] = date('Y-m-d H:i:s');
      //$this->Form->TextBox( 'end_date', $mas['end_date'], 20 );
   ?>
  </td>
 </tr>
 <tr>
  <td class="var_name"><?=$this->multi['FLD_STATUS'];?>:</td>
  <td>
   <?
   $arr = NULL;
   $arr['a'] = $this->multi['STATUS_ACTIVE'];
   $arr['e'] = $this->Msg->show_text('STATUS_EXPIRED');
   $arr['i'] = $this->Msg->show_text('STATUS_INACTIVE');
   if( !isset( $mas['status'] ) ) $mas['status'] = 'i';
   $this->Form->Select( $arr, 'status', $mas['status'], NULL );
   ?>
  </td>
 </tr>
 <?/*
 <tr>
  <td class="var_name"><?=$this->Msg->show_text('TYPE');?>:</td>
  <td>
   <?
   $arr = NULL;
   $arr['sys'] = $this->Msg->show_text('TYPE_SYS');
   $arr['users'] = $this->Msg->show_text('TYPE_USERS');
   if( !isset( $mas['type'] ) ) $mas['type'] = 'sys';
   $this->Form->Select( $arr, 'type', $mas['type'], NULL );
   $this->Form->Hidden('type', 'sys');
   ?>
  </td>
 </tr>
 */?>
 <?$this->Form->Hidden('type', 'sys');?>
 <tr>
  <td class="var_name"><?=$this->multi['FLD_USER_ANSWERS'];?>:</td>
  <td>
   <?
   $arr = NULL;
   $arr['on'] = 'On';
   $arr['off'] = 'Off';
   if( !isset( $mas['users_answers'] ) ) $mas['users_answers'] = 'off';
   $this->Form->SelectPolls( $arr, 'users_answers', $mas['users_answers'], NULL );
   ?>
  </td>
 </tr>
 <tr>
  <td class="var_name"><?=$this->Msg->show_text('MULTY');?>:</td>
  <td>
   <?
   $arr = NULL;
   $arr['on'] = 'On';
   $arr['off'] = 'Off';
   if( !isset( $mas['multy'] ) ) $mas['multy'] = 'off';
   $this->Form->SelectPolls( $arr, 'multy', $mas['multy'], NULL );
   ?>
  </td>
 </tr>
 <tr>
  <td class="var_name"><?=$this->Msg->show_text('VOTE');?>:</td>
  <td>
   <?
   if( !isset( $mas['vote'] ) ) $mas['vote'] = '-';
   echo $mas['vote'];
   ?>
  </td>
 </tr>
 <tr>
  <td colspan="2">
   <?
   $Panel->WritePanelHead( "SubPanel_" );

   $ln_arr = $ln_sys->LangArray( _LANG_ID );
   while( $el = each( $ln_arr ) )
   {
     $lang_id = $el['key'];
     $lang = $el['value'];
     $mas_s[$lang_id] = $lang;

     $Panel->WriteItemHeader( $lang );
        echo "\n <table border=0 class='EditTable'>";
        echo "\n <tr>";
        echo "\n <td><b>".$this->multi['FLD_QUESTION'].":</b>";
        echo "\n <td>";

        $row = NULL;
        if( isset( $id) ) $row = $this->Spr->GetByCod( TblModPollSprQ, $id, $lang_id );
        //print_r($row);
        if( $id ) $this->Form->TextBox( 'question['.$lang_id.']', $row[$lang_id], 80 );
        else $this->Form->TextBox( 'question['.$lang_id.']', $this->qusetion, 80 );

	    /* Alternatives */
        $kol = 10;
        if( !$id )
        {
          for( $i = 0; $i < $kol; $i++ )
          {
           echo '<tr><td  width=120>'.$this->multi['FLD_ALTERNATIVE'].' '.( $i + 1 ).':';
           echo '<td>';
           $this->Form->TextBox( 'alternative['.$i.']['.$lang_id.']', '', 30 );
          }
        }

        echo   "\n </table>";
        $Panel->WriteItemFooter();
   }
   $Panel->WritePanelFooter();
   ?>
  </td>
 </tr>
</table>
  <?
  /* Write Simple Panel Footer*/
  AdminHTML::PanelSimpleF();

$this->Form->WriteSaveAndReturnPanel( $script );?>&nbsp;<?
$this->Form->WriteSavePanel( $script );?>&nbsp;<?
$this->Form->WriteCancelPanel( $script );?>&nbsp;<?
if( !empty($this->id) ) $this->Form->WritePreviewPanel( "http://".NAME_SERVER."/modules/mod_poll/poll.preview.php?lang_pg="._LANG_ID."&id=".$this->id );


  AdminHTML::PanelSubF();
  $this->Form->WriteFooter();
  return true;
  } //--- end of edit




  // ================================================================================================
  // Function : save()
  // Date : 11.02.2011
  // Returns : true,false / Void
  // Description : Store data to the table poll
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function save()
  {
   $ln_sys = new SysLang();

   $question = $_REQUEST['question'];
   if( empty( $question[_LANG_ID] ) )
   {
     $this->Msg->show_msg('MSG_QUESTION_EMPTY');
     $this->edit( $_REQUEST['id'], $_REQUEST );
     return false;
   }

   $id = $_REQUEST['id'];
   $start_date = $_REQUEST['start_date'];
   $end_date = $_REQUEST['end_date'];
   $status = $_REQUEST['status'];
   $type = $_REQUEST['type'];
   $users_answers = $_REQUEST['users_answers'];
   $multy = $_REQUEST['multy'];
   if( isset( $_REQUEST['alternative'] ) ) $alternative = $_REQUEST['alternative'];
   if( isset( $_REQUEST['question'] ) ) $question = $_REQUEST['question'];
   $ln_arr = $ln_sys->LangArray( _LANG_ID );

   $q = "SELECT * FROM ".TblModPoll." WHERE `id`='$id'";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   //echo  $del = '<br>q='.$q.' res='.$res.' this->Rights->result='.$this->Right->result;
   if( !$res ) return false;
   $rows = $this->Right->db_GetNumRows();
   if( $rows > 0 )   //--- update
   {
      $q = "update `".TblModPoll."` set
           `start_date`='$start_date',
           `end_date`='$end_date',
           `status`='$status',
           `type`='$type',
           `users_answers`='$users_answers',
           `multy`='$multy'
            where id='$id'";
      $poll_id = $id;
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
//echo  $del = '<br>UPDATE $q='.$q.' $res='.$res.' $$this->Rights->result='.$this->Rights->result;

      if( !$res ) return false;

   }else  //--- insert
   {
       /* Insert Poll */
       $q = "insert into `".TblModPoll."` values(NULL, '0', '$start_date','$end_date','$status','$type','$users_answers','$multy','0' )";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
//echo    $del = '<br>INSERT $q='.$q.' $res='.$res.' $$this->Rights->result='.$this->Rights->result;
       if( !$res ) return false;
       $poll_id = $this->Right->db_GetInsertID();

       $ln_arr = $ln_sys->LangArray( _LANG_ID );
       while( $alt_arr = each( $alternative ) )
       {
           $fl = 0;
           while( $a = each( $alt_arr['value'] ) )
           {
             $lang_id = $a['key'];
             $value = $a['value'];
             if( trim( $value ) )
             {
               if( $fl == 0 )
               {
                // Save Question on different languages
                $q = "insert into `".TblModPollAlt."` values(NULL, '$poll_id', '0', '".$this->GetNewDisplay()."' )";
                $res = $this->Right->Query( $q, $this->user_id, $this->module );
                if( !$res ) return false;
                $id_alt = $this->Right->db_GetInsertID();
                $fl = 1;
               }

               $res = $this->Spr->SaveToSpr( TblModPollSprA, $id_alt, $lang_id, $value );
               if( !$res ) return false;
             }
           }
       }
   }

   // Save Question on different languages
   $ln_arr = $ln_sys->LangArray( _LANG_ID );
   while( $el = each( $ln_arr ) )
   {
      $quest = addslashes( $question[ $el['key'] ] );
      $lang_id = $el['key'];
      //$q = "insert into `".TblModPollSprQ."` set "
      $res = $this->Spr->SaveToSpr( TblModPollSprQ, $poll_id, $lang_id, $quest );
//      if( !$res ) return false;
   } //--- end while

  return true;
  }



  // ================================================================================================
  // Function : del()
  // Date : 11.02.2011
  // Parms :    $id_del - array with ID for deleting
  // Returns :  true,false / Void
  // Description :  Delete data from the table
  // Programmer :  Yaroslav Gyryn
  // ================================================================================================
  function del( $id_del )
  {
    $db = new DB();

    $kol = count( $id_del );
    $del = 0;
    for( $i = 0; $i < $kol; $i++ )
    {
     $u = $id_del[$i];

     $q = "DELETE FROM `".TblModPoll."` WHERE id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     $res = $this->Spr->DelFromSpr( TblModPollSprQ, $u );


     $q = "select * from ".TblModPollAlt." where poll_id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     $rows = $this->Right->db_GetNumRows();
     for( $j = 0; $j < $rows; $j++ )
     {
       $row = $this->Right->db_FetchAssoc();

       $q = "DELETE FROM `".TblModPollAlt."` WHERE id='".$row['id']."'";
       $res = $db->db_Query( $q );
       $res = $this->Spr->DelFromSpr( TblModPollSprA, $row['id'] );
     }

     $q = "DELETE FROM `".TblModPollAnswers."` WHERE poll_id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );

     $q = "DELETE FROM `".TblModPollIP."` WHERE poll_id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );

     if( !$res ) return false;
     if ( $res )
      $del = $del + 1;
     else
      return false;
    }
   return $del;
  } //--- end of del




  // ================================================================================================
  // Function : show_alt()
  // Version : 1.0.0
  // Date : 11.02.2011
  // Parms :       no
  // Returns :     true,false / Void
  // Description : Show POLLs Alternatives
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function show_alt()
  {
    $row_arr = NULL;  //--- Array Of Rows

    $script1 = 'module='.$this->module.'&alt=1';
    if( $this->poll_id ) $script1 = $script1.'&poll_id='.$this->poll_id;
    $script1 = $_SERVER['PHP_SELF']."?$script1";

    $script = $script1.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;


    if( !$this->sort ) $this->sort = 'display';

    $q = "SELECT * FROM ".TblModPollAlt." where 1 ";
    if( $this->poll_id ) $q = $q." and poll_id=$this->poll_id";
    //if( $this->fltr ) $q = $q." and $this->fltr";
    $q = $q." order by $this->sort";

    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$res )return false;
    //echo 'q='.$q.'res='.$res;
    $rows = $this->Right->db_GetNumRows();

    $up=0;
    $down=0;
    $j = 0;

    for( $i = 0; $i < $rows; $i++ )
    {
        $row = $this->Right->db_FetchAssoc();
        if( $i >= $this->start && $i < ( $this->start + $this->display ) )
        {
            $row_arr[$j] = $row;
            $j = $j + 1;
        }
    }

    /* Write Table Part */
    AdminHTML::TablePartH();

    echo '<TR><TD COLSPAN=11>';
    /* Write Links on Pages */
    $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

    echo '<TR><TD COLSPAN=4>';
    /* Write Form Header */
    $this->Form->WriteHeader( $script );
    $this->Form->WriteTopPanel( $script );
    AdminHTML::TablePartF();

    AdminHTML::TablePartH();
    if( $this->poll_id ) echo '<tr><td>'.$this->poll_id.' - '.$this->Spr->GetNameByCod( TblModPollSprQ, $this->poll_id );
    AdminHTML::TablePartF();

    AdminHTML::TablePartH();
?>
 <TR>
 <td class="THead">*</Th>
 <td class="THead"><A HREF=<?=$script?>&sort=id><?=$this->multi['FLD_ID']?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=question><?=$this->multi['FLD_ALTERNATIVE']?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=vote><?=$this->multi['VOTE']?></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=vote><?=$this->multi['FLD_DISPLAY']?></Th>

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

    echo '<TD align="center">';
    $this->Form->CheckBox( "id_del[]", $row['id'] );

    echo '<TD align="center">';
    $this->Form->Link( $script."&task=edit_alt&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('_TXT_EDIT_DATA') );

    echo '<TD align=center>'.$this->Spr->GetNameByCod( TblModPollSprA, $row['id'] ).'</TD>';

    echo '<TD align=center> '.$row['votes'];

    echo '<TD align=center>';
    if( $up != 0 )
    {
    ?>
    <a href=<?=$script?>&task=up&move=<?=$row['display']?>
    <?=$this->Form->ButtonUp( $row['id'] );?>
    </a>
    <?
    }

    if( $i != ( $rows - 1 ) )
    {
    ?>
     <a href=<?=$script?>&task=down&move=<?=$row['display']?>
     <?=$this->Form->ButtonDown( $row['id'] );?>
     </a>
    <?
    }

   $up=$row['id'];


   } //--- end for

    AdminHTML::TablePartF();
  } //--- end of show_alt




  // ================================================================================================
  // Function : edit_alt()
  // Date : 11.02.2011
  // Parms :
  //                 $id   / id of editing record / Void
  //                 $mas  / array of form values
  // Returns : true,false / Void
  // Description : edit/add records in Poll module
  // Programmer : Yaroslav Gyryn
  // ================================================================================================

  function edit_alt( $id, $mas = NULL )
  {
   $Panel = new Panel();
   $ln_sys = new SysLang();
   $fl = NULL;
   $arr_alt = NULL;           //--- Array of Alternatives

   if( $mas )
        $fl = 1;
   $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;

   if( $this->poll_id ) $script = $script.'&poll_id='.$this->poll_id;

   $script = $_SERVER['PHP_SELF']."?$script";
   if( $id != NULL and ( $mas==NULL ) )
   {
     $q = "SELECT * FROM ".TblModPollAlt." where id='$id'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res ) return false;
     $mas = $this->Right->db_FetchAssoc();
   }
  //print_r($mas);
   if( $id != NULL ) $txt = $this->multi['_TXT_EDIT_DATA'];
   else $txt = $this->multi['_TXT_ADD_DATA'];

   /* Write Form Header */
   $this->Form->WriteHeader( $script );

   AdminHTML::PanelSubH( $txt );
   echo '<table border=0 width=100%><tr><td>';

   /* Write Simple Panel*/
   AdminHTML::PanelSimpleH();
   echo '<table border=0 width=100%><tr><td>';
   if( $this->poll_id ) echo '<tr><td>'.$this->poll_id.' - '.$this->Spr->GetNameByCod( TblModPollSprQ, $this->poll_id );
   echo '</table>';
   AdminHTML::PanelSimpleF();

   echo '<tr><td>';

   /* Write Simple Panel*/
   AdminHTML::PanelSimpleH();
   ?>
<table class="EditTable" border=0>
 <TR><TD><?=$this->Msg->show_text('_FLD_ID')?>
 <TD>
<?
   if( $id != NULL )
   {
    echo $mas['id'];
    $this->Form->Hidden( 'id', $mas['id'] );
   }else $this->Form->Hidden( 'id', '' );

  echo '<tr><td>'.$this->Msg->show_text('MOD_POLL_VOTE');
  if(isset($mas['votes'])) $vote = $mas['votes'];
  else $vote = 0;
  ?>
  <td><?$this->Form->TextBox( 'count', $vote, 80 );?></td>
  <?
  //echo '<td>'.$mas['vote'];


  echo '<tr><td colspan=2>';

    $Panel->WritePanelHead( "SubPanel_" );

    $ln_arr = $ln_sys->LangArray( _LANG_ID );
    while( $el = each( $ln_arr ) )
    {
     $lang_id = $el['key'];
     $lang = $el['value'];
     $mas_s[$lang_id] = $lang;

     $Panel->WriteItemHeader( $lang );
        echo "\n <table border=0 class='EditTable'>";
        echo "\n <tr>";
        echo "\n <td><b>".$this->multi['FLD_ALTERNATIVE'].":</b>";
        echo "\n <td>";

        $row = NULL;
        if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModPollSprA, $mas['id'], $lang_id );
        if( $fl ) $this->Form->TextBox( 'alt['.$lang_id.']', $mas['alt'][$lang_id], 80 );
        else $this->Form->TextBox( 'alt['.$lang_id.']', $row[$lang_id], 80 );
        echo   "\n </table>";
        $Panel->WriteItemFooter();
      }
    $Panel->WritePanelFooter();

  echo '<tr><td colspan=2>';
  echo '<table  border=0><tr><td valign=middle>';
  $this->Form->WriteSavePanel( $script );
  echo '</table>';
  echo '</table>';
  /* Write Simple Panel Footer*/
  AdminHTML::PanelSimpleF();
  echo '</table>';
  AdminHTML::PanelSubF();
  $this->Form->WriteFooter();
  return true;
  } //--- end of edit



  // ================================================================================================
  // Function : save_alt()
  // Version : 1.0.0
  // Date : 11.02.2011
  // Returns : true,false / Void
  // Description : Store data to the table poll
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function save_alt()
  {
   $ln_sys = new SysLang();
   $rows = NULL;

   $id = $_REQUEST['id'];
   $alt = $_REQUEST['alt'];
   $poll_id = $_REQUEST['poll_id'];

   if( empty( $alt[_LANG_ID] ) )
   {
     $this->Msg->show_msg('MSG_ALT_EMPTY');
     $this->edit_alt( $_REQUEST['id'], $_REQUEST );
     return false;
   }

   $ln_arr = $ln_sys->LangArray( _LANG_ID );

   if( $id )
   {
     $q = "SELECT * FROM ".TblModPollAlt." WHERE `id`='$id'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if( !$res ) return false;
     $rows = $this->Right->db_GetNumRows();
   }

   if( $rows > 0 )   //--- update
   {
      $q = "update `".TblModPollAlt."` set
            poll_id='$poll_id',
            votes = '".$this->count."'
            where id='$id'";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
      if( !$res ) return false;

   }else  //--- insert
   {
       /* Insert Poll */
       $q = "insert into `".TblModPollAlt."` values(NULL, '$poll_id', '".$this->count."', '".$this->GetNewDisplay()."' )";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       if( !$res ) return false;
       $id = $this->Right->db_GetInsertID();
   }

   // Save Question on different languages
   $ln_arr = $ln_sys->LangArray( _LANG_ID );
   while( $el = each( $ln_arr ) )
   {
      $a = addslashes( $alt[ $el['key'] ] );
      $lang_id = $el['key'];
      $res = $this->Spr->SaveToSpr( TblModPollSprA, $id, $lang_id, $a );
      //if( !$res ) return false;
   } //--- end while

  return true;
  }



  // ================================================================================================
  // Function : del_alt()
  // Date : 11.02.2011
  // Parms :    $id_del - array with ID for deleting
  // Returns :  true,false / Void
  // Description :  Delete data from the table
  // Programmer :  Yaroslav Gyryn
  // ================================================================================================
  function del_alt( $id_del )
  {
    $kol = count( $id_del );
    $del = 0;
    for( $i = 0; $i < $kol; $i++ )
    {
     $u = $id_del[$i];
     //$q = "select * from ".TblModPollAlt." where id='$u'";
     //$res = $this->Right->Query( $q, $this->user_id, $this->module );
     //$row = $this->Right->db_FetchAssoc();

     $q = "DELETE FROM `".TblModPollAlt."` WHERE id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     $res = $this->Spr->DelFromSpr( TblModPollSprA, $u );

     if ( $res )
      $del = $del + 1;
     else
      return false;
    }
   return $del;
  } //--- end of del



  // ================================================================================================
  // Function : down()
  // Date : 11.02.2011
  // Returns :      true,false / Void
  // Description :  Down Poll-Alternative
  // Programmer :  Yaroslav Gyryn
  // ================================================================================================
  function down( $move )
  {
   $q = "select * from ".TblModPollAlt." where display='$move'";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   if( !$res )return false;
   $rows = $this->Right->db_GetNumRows();
   $row = $this->Right->db_FetchAssoc();
   $move_down = $row['display'];
   $id_down = $row['id'];


   $q = "select * from ".TblModPollAlt." where display>'$move' order by display";
   $res = $this->Right->Query( $q, $this->user_id, $this->module );
   if( !$res )return false;
   $rows = $this->Right->db_GetNumRows();
   $row = $this->Right->db_FetchAssoc();
   $move_up = $row['display'];
   $id_up = $row['id'];


   if( $move_down!=0 AND $move_up!=0 )
   {
     $q = "update ".TblModPollAlt." set
           display='$move_down' where id='$id_up'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     //echo $q;
     $q = "update ".TblModPollAlt." set
           display='$move_up' where id='$id_down'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
   }
  } //--- end of up



  // ================================================================================================
  // Function : up()
  // Date : 11.02.2011
  // Returns :      true,false / Void
  // Description :  Up Poll-Alternatives
  // Programmer :  Yaroslav Gyryn
  // ================================================================================================
  function up( $move )
  {
    $q = "select * from ".TblModPollAlt." where display='$move'";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$res )return false;
    $rows = $this->Right->db_GetNumRows();
    $row = $this->Right->db_FetchAssoc();
    $move_up = $row['display'];
    $id_up = $row['id'];

    $q = "select * from ".TblModPollAlt." where display<'$move' order by display desc";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$res )return false;
    $rows = $this->Right->db_GetNumRows();
    $row = $this->Right->db_FetchAssoc();
    $move_down = $row['display'];
    $id_down = $row['id'];

    if( $move_down!=0 AND $move_up!=0 )
    {
      $q = "update ".TblModPollAlt." set
            display='$move_down' where id='$id_up'";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );

      $q = "update ".TblModPollAlt." set
            display='$move_up' where id='$id_down'";
      $res = $this->Right->Query( $q, $this->user_id, $this->module );
    }
  } //--- end of down()




  // ================================================================================================
  // Function : show_answer()
  // Date : 11.02.2011
  // Parms :       no
  // Returns :     true,false / Void
  // Description : Show POLLs Answers
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function show_answer()
  {
    $row_arr = NULL;  //--- Array Of Rows

    $script1 = 'module='.$this->module.'&answ=1';
    if( $this->poll_id ) $script1 = $script1.'&poll_id='.$this->poll_id;
    $script1 = $_SERVER['PHP_SELF']."?$script1";

    $script = $script1.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;


    if( !$this->sort ) $this->sort = 'id';

    $q = "SELECT * FROM ".TblModPollAnswers." where 1 ";
    if( $this->poll_id ) $q = $q." and poll_id=$this->poll_id";
    $q = $q." order by $this->sort";

    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$res )return false;

    $rows = $this->Right->db_GetNumRows();

    $j = 0;
  for( $i = 0; $i < $rows; $i++ )
  {
   $row = $this->Right->db_FetchAssoc();
   if( $i >= $this->start && $i < ( $this->start + $this->display ) )
   {
     $row_arr[$j] = $row;
     $j = $j + 1;
   }
  }

    /* Write Table Part */
    AdminHTML::TablePartH();

    echo '<TR><TD COLSPAN=11>';
    /* Write Links on Pages */
    $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

    echo '<TR><TD COLSPAN=4>';
    /* Write Form Header */
    $this->Form->WriteHeader( $script );
    $this->Form->WriteTopPanel( $script, 2 );
    AdminHTML::TablePartF();

    AdminHTML::TablePartH();
    if( $this->poll_id ) echo '<tr><td>'.$this->poll_id.' - '.$this->Spr->GetNameByCod( TblModPollSprQ, $this->poll_id );
    AdminHTML::TablePartF();

    AdminHTML::TablePartH();
?>
 <TR>
 <td class="THead">*</Th>
 <td class="THead"><A HREF=<?=$script?>&sort=id><?=$this->multi['FLD_ID'];?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=answer><?=$this->Msg->show_text('FLD_ALTERNATIVE')?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=lang_id><?=$this->Msg->show_text('FLD_LANGUAGE')?></Th>

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
    echo '<TD align=center>';
    $this->Form->CheckBox( "id_del[]", $row['id'] );
    echo '<TD align=center>'.stripslashes( $row['id'] );
    echo '<TD align=center>'.$row['answer'];
    echo '<TD align=center> '.$this->Spr->GetNameByCod( TblSysLang, $row['lang_id'] );
   } //--- end for

    AdminHTML::TablePartF();
  } //--- end of show_answer





  // ================================================================================================
  // Function : del_answer()
  // Date : 11.02.2011
  // Parms :    $id_del - array with ID for deleting
  // Returns :  true,false / Void
  // Description :  Delete data from the table
  // Programmer :  Yaroslav Gyryn
  // ================================================================================================
  function del_answer( $id_del )
  {
    $kol = count( $id_del );
    $del = 0;
    for( $i = 0; $i < $kol; $i++ )
    {
     $u = $id_del[$i];
     $q = "DELETE FROM `".TblModPollAnswers."` WHERE id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if ( $res )
      $del = $del + 1;
     else
      return false;
    }
   return $del;
  } //--- end of del_answer




  // ================================================================================================
  // Function : show_ip()
  // Date : 11.02.2011
  // Parms :       no
  // Returns :     true,false / Void
  // Description : Show POLLs IP
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function show_ip()
  {
    $row_arr = NULL;  //--- Array Of Rows

    $script1 = 'module='.$this->module.'&ip=1';
    if( $this->poll_id ) $script1 = $script1.'&poll_id='.$this->poll_id;
    $script1 = $_SERVER['PHP_SELF']."?$script1";

    $script = $script1.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort;

    if( !$this->sort ) $this->sort = 'id';

    $q = "SELECT * FROM ".TblModPollIP." where 1 ";
    if( $this->poll_id ) $q = $q." and poll_id=$this->poll_id";
    $q = $q." order by $this->sort";

    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$res )return false;

    $rows = $this->Right->db_GetNumRows();

    /* Write Table Part */
    AdminHTML::TablePartH();

    echo '<TR><TD COLSPAN=11>';
    /* Write Links on Pages */
    $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );

    echo '<TR><TD COLSPAN=4>';
    /* Write Form Header */
    $this->Form->WriteHeader( $script );
    $this->Form->WriteTopPanel( $script, 2 );
    AdminHTML::TablePartF();

    AdminHTML::TablePartH();
    if( $this->poll_id ) echo '<tr><td>'.$this->poll_id.' - '.$this->Spr->GetNameByCod( TblModPollSprQ, $this->poll_id );
    AdminHTML::TablePartF();

    AdminHTML::TablePartH();
?>
 <TR>
 <td class="THead">*</Th>
 <td class="THead"><A HREF=<?=$script?>&sort=id><?=$this->Msg->show_text('FLD_ID')?></A></Th>
 <td class="THead"><A HREF=<?=$script?>&sort=ip><?=$this->Msg->show_text('FLD_IP')?></A></Th>
 <?
  $j = 0;
  for( $i = 0; $i < $rows; $i++ )
  {
   $row = $this->Right->db_FetchAssoc();
   if( $i >= $this->start && $i < ( $this->start + $this->display ) )
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
    echo '<TD width=40>';
    $this->Form->CheckBox( "id_del[]", $row['id'] );
    echo '<TD width=50>'.stripslashes( $row['id'] );
    echo '<TD align=left>'.$row['ip'];
   } //--- end for

    AdminHTML::TablePartF();
  } //--- end of show_answer



  // ================================================================================================
  // Function : del_ip()
  // Date : 11.02.2011
  // Parms :    $id_del - array with ID for deleting
  // Returns :  true,false / Void
  // Description :  Delete data from the table
  // Programmer :  Yaroslav Gyryn
  // ================================================================================================
  function del_ip( $id_del )
  {
    $kol = count( $id_del );
    $del = 0;
    for( $i = 0; $i < $kol; $i++ )
    {
     $u = $id_del[$i];
     $q = "DELETE FROM `".TblModPollIP."` WHERE id='$u'";
     $res = $this->Right->Query( $q, $this->user_id, $this->module );
     if ( $res )
      $del = $del + 1;
     else
      return false;
    }
   return $del;
  } //--- end of del_answer

 } //--- end of class Poll
?>