<?
// ================================================================================================
//    System     : CMS
//    Module     : POLL
//    Date       : 15.04.2011
//    Licensed To: Igor  Trokhymchuk  ihoru@mail.ru
//    Purpose    : POLL - module (Front-End)
//
// ================================================================================================


// ================================================================================================
//    Class             : PollUse
//    Date              : 15.04.2011
//    Constructor       : Yes
//    Parms             : no
//    Returns           : None
//    Description       : Poll Class For Front-end
//    Programmer      :  Yaroslav Gyryn
// ================================================================================================
include_once( SITE_PATH.'/include/classes/FrontForm.class.php' );

  function cmp_votes($a,$b){
    if ($a['votes'] == $b['votes'])
        return 0;
    return ($a['votes'] > $b['votes']) ? -1 : 1;
}

 class PollUse extends Poll
 {
   var $Msg;
   var $Spr;
   var $Form;

   var $module;
   var $show_in;


  // ================================================================================================
  //    Function          : PollUse (Constructor)
  //    Version           : 1.0.0
  //    Date              : 15.04.2011
  //    Parms             : no
  //    Returns           : true/false
  //    Description       : Constructor of Poll Class Definition for Front End
  // ================================================================================================
  function PollUse()
  {
     if( defined( '_LANG_ID' ) )
        $this->Msg = new ShowMsg(_LANG_ID);
     else
        $this->Msg = new ShowMsg();                   /* create ShowMsg object as a property of this class */
    $this->Spr = new SysSpr( NULL,NULL,NULL,NULL,NULL,NULL,NULL ); /* create SysSpr object as a property of this class */
    $this->Msg->SetShowTable( TblModPollSprTxt );
    if (empty($this->Form)) $this->Form = new FrontForm('form_mod_poll');
    if(empty($this->multi)) $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
    //if(empty($this->multi)) $this->multi = $this->Spr->GetMulti(TblModPollSprTxt);
    $this->CheckStatus();
  }

  // ================================================================================================
  // Function : ShowPoll()
  // Date :    15.04.2011
  // Returns : Write Active Poll
  // Description : Show Active Poll
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function ShowPoll()
  {
   $Poll = $this->GetActivePoll();
   //print_r($Poll);
   if( $Poll ){
       ?>
    <div id="leftpoll">
        <div class="contenttitle">
            <?=$this->multi['TXT_POLL'];?>
        </div>
        <div id="toppoll"></div>
        <div id="bodypoll">
            <div id="poll">
                   <?$isMulty=$this->IsMulty( $Poll['id'] );?>
                  <form id="pollform" method="post" name="pollform" action="/polls.php">
                  <?
                    $this->Form->Hidden('task','ajax_result');
                    $this->Form->Hidden('cd',$Poll['id']);
                  ?>
                  <div class="question"><?=$this->Spr->GetNameByCod( TblModPollSprQ, $Poll['id'] );?></div>
                  <div class="ans <?=($isMulty?'':'radio-group');?>"><?
                  $n = count( $Poll['alternatives'] );
                  for( $i = 0; $i < $n; $i++ )
                  {
                      ?>
                      <div class="alt <?=($isMulty?'check':'radio');?>">
                          <input type="hidden" value="0" name="alt[<?=$Poll['alternatives'][$i]?>]">
                          <?=$this->Spr->GetNameByCod( TblModPollSprA, $Poll['alternatives'][$i] );?>
                      </div>
                      <?
                  }
                  if($this->IsAnswer($Poll['id']))
                  {
                      ?>
                      <div class="alt <?=($isMulty?'check':'radio');?>">
                          <input type="hidden" value="0" name="alt[-1]">
                          <input type="text" title="Ваш вариант" class="textfield black" name="answer" value="">
                      </div>
                  <?
                  }
                  ?>
                      <div align="center">
                        <a href="javascript: save_poll_get_result();" name="votebtn" class="button2" title="<?=$this->multi['_TXT_VOTE']?>"><?=$this->multi['_TXT_VOTE']?></a>
                      </div>

                      <div><a href="<?=_LINK.'polls_'.$Poll['id'].'.html';?>"><?=$this->multi['TXT_SEE_RESULTS']?></a> | <a href="<?=_LINK.'polls.html';?>"><?=$this->multi['TXT_ARCHIVE']?></a></div>
                      <?/*<a href="javascript: save_poll_get_result();" class="redbutton f"><b>голосовать</b></a>*/?>
                      <br>
                  </div>
                  </form>
                  <?
                  $this->show_JS();
            ?>
        </div>
        </div>
        <div id="bottompoll"></div>
    </div>
   <?
    } //-- end if

  } //--- end of ShowPoll()


  // ================================================================================================
  // Function : ShowPolls()
  // Date :    20.11.2008
  // Returns : Write Active Polls
  // Description : Show Active Polls
  // Programmer : Ihor Trokhumchuk
  // ================================================================================================
  function ShowPolls($title = null,$id_poll = NULL)
  {
   $FrontForm = new FrontForm();


   if( !isset( $_REQUEST['module'] ) ) $module = NULL;
   else $module = $_REQUEST['module'];

   if( !isset( $_REQUEST['show_in'] ) ) $show_in = NULL;
   else $show_in = $_REQUEST['show_in'];


   $script = $_SERVER['PHP_SELF'].'?module='.$module.'&amp;show_in='.$show_in;
     // var_dump($script);
   $Polls = $this->GetActivePolls($id_poll);
   //echo '<br>count($Polls)='.count($Polls);
   //print_r($Polls);
//var_dump($Polls);
   if( !is_array($Polls) ) return false;

   if(count($Polls>0)){
       $link = _LINK.'polls.html';

       ?><div <?if(!empty($title)){?>id="pollsBlock"<?}else{?>style="background-position:475px bottom;" <?}?> class="polls-block">
       <?if(!empty($title)){?>
        <div class="title"><?
            if (!empty($title)) {
                ?><span class="poll-href-arch"><a href="<?=_LINK?>polls.html"><?=$this->multi['_TXT_ARCHIVE_POLLS']?></a></span><?
            }
            ?><span><?echo $title;?></span><?
            ?></div>
    <?}

    $rand = rand(1, count($Polls));

    $incr = 0;
//   var_dump($Polls);
   foreach($Polls as $key=>$Poll){
//       var_dump($Poll);
       $incr++;
       if(!empty($title) && $incr != $rand){
           continue;
       }
       if($rand)
       if(!empty($title) && !$this->checkVote($key, $_SERVER['REMOTE_ADDR'])){

       if( $Poll ){
        ?><div class="item" id="itemPoll"><?
          //$param = "onsubmit=\"window.open('about:blank','vr','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,fullscreen=no,channelmode=no,width=480px,height=260px');\" target='vr'";

           $FrontForm->WriteFrontHeader( 'pollform', '/modules/mod_poll/poll.result.php?cd='.$key.'&amp;lang_pg='._LANG_ID, 'vote'," ");// $param );

           echo '<div class="pTitle">'.strip_tags($Poll[0]['question']).'</div>';

          ?><div class="sel">
           <input type="hidden" name="cd" value="<?=$key;?>"/>
           <input type="hidden" name="lang_pg" value="<?=_LANG_ID;?>"/>
           <?

          //var_dump($Poll);
          //echo ' count='.count( $Poll );
          for( $i = 0; $i < count( $Poll ) -1; $i++ )
          {

           //--- If Poll Multy-Selected
           ?><div class="item_alt"><?
           if( $Poll[$i]['multy'] == 'on'  )
           {
              ?><fieldset class="check-wr"><?
             $FrontForm->CheckBox( 'alt[]' , $Poll[$i]['id_altr'], NULL, NULL, NULL, "alt".$i);
               echo '<label for="alt'.$i.'" class="altern">'.$Poll[$i]['altr'].'</label>';
           }else
           {
               ?><fieldset class="radio-wr"><?
             $FrontForm->Radio( 'alt' , $Poll[$i]['id_altr'], NULL, NULL, NULL, "alt".$i);
             echo '<label for="alt'.$i.'" class="altern">'.$Poll[$i]['altr'].'</label>';
           }
           ?></fieldset></div><?
          }


          //--- If user can leave his own answer
          if( $Poll[0]['users_answers'] == 'on' )
          {
            //--- If Poll Multy-Selected
            ?><div class="yourChoise"><?
            $params = 'onclick="getElementById('."'own".$key."'".').value='."''".'"';
            if( $Poll[0]['multy'] == 'on' )
            {
                ?> <fieldset class="check-wr"><?
             $FrontForm->CheckBox( 'alt[]' , '-1', '', $params);
              $FrontForm->TextBox( 'answer', $this->Msg->show_text('TXT_FRONT_YOUR_CHOISE'), 'size="30" id="own'.$key.'" onclick="this.value='."''".'"' ).'<br>';
                ?></fieldset><?
            }else
            {
              //$txt = '<input class="textbox" type="text" size="10" value="" name="answer"/>';
               ?> <fieldset class="radio-wr"><?
              $FrontForm->Radio( 'alt' , '-1', '', '', $params );
              $FrontForm->TextBox( 'answer',$this->Msg->show_text('TXT_FRONT_YOUR_CHOISE'), 'size="30" id="own'.$key.'" onclick="this.value='."''".'"' ).'<br>';

            ?><span class="hidden-radio"></span></fieldset><?
            }
            ?></div> <?
          }
          $FrontForm->Hidden( 'idc', $key );
          ?>
          <div class="poll-submit"><input class="submit1" type="submit" name="submit" onclick="sendPoll();return false;" value="<?=$this->multi['_TXT_VOTE']?>"/></div>
           <div id="formpollRes" class="formpoll-res" style="position: absolute;"></div>
           <?
           //if(count($Polls)==1) $link = _LINK.'polls_'.$Poll['id'].'.html';
           //else
          $FrontForm->WriteFooter();
          ?></div></div><?
        } //-- end if
        ?><br/><?
       }
   else{

           $this->ShowResult($key ,true, $title);
       }
   }

   ?></div><?
   }



  } //--- end of ShowPolls()






function ShowPolls2()
  {
   $ModPlug = new ModulesPlug();
   $FrontForm = new FrontForm();

   if( !isset( $_REQUEST['module'] ) ) $module = NULL;
   else $module = $_REQUEST['module'];

   if( !isset( $_REQUEST['show_in'] ) ) $show_in = NULL;
   else $show_in = $_REQUEST['show_in'];

   $script = $_SERVER['PHP_SELF'].'?module='.$module.'&amp;show_in='.$show_in;
   $Polls = $this->GetActivePolls();
   //echo '<br>count($Polls)='.count($Polls);
   //print_r($Polls);
   if( !is_array($Polls) ) return false;
   if(count($Polls>0)){
    ?><div id="pollsBlock">
        <div class="title"><?=$this->multi['FLD_POLL'];?></div>
    <?
   foreach($Polls as $key=>$Poll){
       if( $Poll ){
        ?><div class="item"><?
          $param = "onsubmit=\"window.open('about:blank','vr','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,fullscreen=no,channelmode=no,width=480px,height=260px');\" target='vr'";
          $FrontForm->WriteFrontHeader( 'pollform', '/modules/mod_poll/poll.result.php?cd='.$Poll['id'].'&amp;lang_pg='._LANG_ID, 'vote', $param );
          echo '<div class="pTitle">'.strip_tags($this->Spr->GetNameByCod( TblModPollSprQ, $Poll['id'] )).'</div>';

          ?><div class="sel"><?
          for( $i = 0; $i < count( $Poll['alternatives'] ); $i++ )
          {
           //--- If Poll Multy-Selected
           ?><div class="item_alt"><?
           if( $this->IsMulty( $Poll['id'] ) )
           {
             $FrontForm->CheckBox( 'alt[]' , $Poll['alternatives'][$i], "");
             echo '<span class="altern">'.$this->Spr->GetNameByCod( TblModPollSprA, $Poll['alternatives'][$i] ).'</span>';
           }else
           {
             $FrontForm->Radio( 'alt' , $Poll['alternatives'][$i], '', '' );
             echo '<span class="altern">'.$this->Spr->GetNameByCod( TblModPollSprA, $Poll['alternatives'][$i] ).'</span>';
           }
           ?></div><?
          }
          ?></div><?

          //--- If user can leave his own answer
          if( $this->IsAnswer( $Poll['id'] ) )
          {
            //--- If Poll Multy-Selected
            ?><div class="yourChoise"><?
            $params = 'onclick="getElementById('."'own".$Poll['id']."'".').value='."''".'"';
            if( $this->IsMulty( $Poll['id'] ) )
            {
              $FrontForm->CheckBox( 'alt[]' , '-1', '', $params);
              $FrontForm->TextBox( 'answer', $this->Msg->show_text('TXT_FRONT_YOUR_CHOISE'), 'size="30" id="own'.$Poll['id'].'" onclick="this.value='."''".'"' ).'<br>';
            }else
            {
              //$txt = '<input class="textbox" type="text" size="10" value="" name="answer"/>';
              $FrontForm->Radio( 'alt' , '-1', '', '', $params );
              $FrontForm->TextBox( 'answer',$this->Msg->show_text('TXT_FRONT_YOUR_CHOISE'), 'size="30" id="own'.$Poll['id'].'" onclick="this.value='."''".'"' ).'<br>';
            }
            ?></div><?
          }
          $FrontForm->Hidden( 'idc', $Poll['id'] );
          ?>
          <div class="pollSubmit"><input class="submit1" type="submit" name="submit" value="<?=$this->Msg->show_text('_TXT_VOTE')?>"/></div>
           <?
           //if(count($Polls)==1) $link = _LINK.'polls_'.$Poll['id'].'.html';
           //else
           $link = _LINK.'polls.html';
          $FrontForm->WriteFooter();
          ?></div><?
        } //-- end if
        ?><br/><?
   }

   ?><a href="<?=$link;?>" class="sumb1"><?=$this->Msg->show_text('_TXT_ALL_POLLS')?>→</a><?
   ?></div><?
   }
  } //--- end of ShowPolls()




  function ShowAllResult()
  {
    ?>
    <div id="content2Box">
    <div class="hleb">
        <a href="/">Главная</a> / архив опросов
    </div>
    <div id="content3Box">
    <?
    $arr_cd = $this->GetActivePolls();
    foreach($arr_cd as $cd=>$Poll){
        $this->ShowResult( $cd );
        ?>
            <div class="hr"></div>
        <?
    }
    ?>
        </div>
    </div>
    <?
  }
  // ================================================================================================
  // Function : VotePoll()
  // Date :    15.04.2011
  // Parms :   $od - poll id
  // Returns : true/false
  // Description : Vote Poll
  // Programmer : Yaroslav Gyryn
  // ================================================================================================

  function VotePoll( $id, $alt, $REMOTE_ADDR, $answer )
  {
    $this->Vote( $id, $alt, $REMOTE_ADDR, $answer );
  } //--- end of VotePoll()


  // ================================================================================================
  // Function : GetColor()
  // Date :    15.04.2011
  // Parms :  $colorFirst, $colorSecond. $point
  // Returns : true/false
  // Description : Get Color
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GetColor($colorFirst=0x000000,$colorSecond=0xffffff,$point=0.5)
  {
    $redF=($colorFirst&0xff0000)>>16;
    $greenF=($colorFirst&0x00ff00)>>8;
    $blueF=($colorFirst&0x0000ff);
    $redS=($colorSecond&0xff0000)>>16;
    $greenS=($colorSecond&0x00ff00)>>8;
    $blueS=($colorSecond&0x0000ff);
    $red=((int)$redF+($redS-$redF)*$point)&0xff;
    $green=((int)$greenF+($greenS-$greenF)*$point)&0xff;
    $blue=((int)$blueF+($blueS-$blueF)*$point)&0xff;
    //echo '<hr>';
    //printf ("point=%f<br>1 =%06x ",$point,$colorFirst);
    //printf ("<br>2 =%06x ",$colorSecond);
    //printf ("<br>3 =%06x ",($red<<16|$green<<8|$blue));
    //printf ("<br>1 red=%02x green=%02x blue=%02x ",$redF,$greenF,$blueF);
    //printf ("<br>2 red=%02x green=%02x blue=%02x ",$redS,$greenS,$blueS);
    //printf ("<br>3 red=%02x green=%02x blue=%02x ",$red,$green,$blue);
    return ($red<<16|$green<<8|$blue);
  }

  // ================================================================================================
  // Function : ShowResult()
  // Date :    22.04.2011
  // Parms :   $id - poll id
  // Returns : true/false
  // Description : Show Result
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function ShowResult_( $id )
  {
    $Poll = $this->GetPollResult( $id );

    $script = $_SERVER['PHP_SELF'].'?task=answer';
    if( isset( $Poll['id'] ) )
    {
     $script = $script.'&amp;cd='.$Poll['id'];
     if( $this->module ) $script = $script.'&amp;module='.$this->module;
     if( $this->show_in ) $script = $script.'&amp;show_in='.$this->show_in;
     if( isset( $Poll['id'] ) ) echo '<h3>'.$this->Spr->GetNameByCod( TblModPollSprQ, $Poll['id'] ).' </h3>';

     echo '<p><table border=0 width=100% align="center" cellpadding=2 cellspacing=1>';
     for( $i = 0; $i < count( $Poll['alternatives'] ); $i++ ){
        $mas = $Poll['alternatives'][$i];
        if( $Poll['count'] != 0 ) $percent = round( ( 100*$mas['votes']/$Poll['count'] ), 2 );
        else $percent = 0;

        echo '<tr><td width="10%" align="right" nowrap="nowrap"><p>('.$mas['votes'].') '.$percent.' % </p>';
        echo '<td width="50%" align="left">';
          echo '<table border=0 width="'.$percent.'%"  cellpadding="0" cellspacing="0" ><tr><td style="background-color:#FF5401;" height="15">';
          echo '</td></tr></table>';
        echo '<td width="40%" align="left"><p>'.$this->Spr->GetNameByCod( TblModPollSprA, $mas['id'] ).'</p>';
     }
     if( $this->IsAnswer( $id ) ){
        $answer_cnt = $this->AnswerCnt( $id );
        if( $Poll['count'] != 0 ) $percent = round( ( 100*$answer_cnt/$Poll['count'] ), 2 );
        else $percent = 0;
        echo '<tr><td width="10%" align="right"><p>('.$answer_cnt.') '.$percent.' % </p>';
        echo '<td width="50%" align="left">';
          echo '<table border=0 width="'.$percent.'%"  cellpadding="0" cellspacing="0" ><tr><td style=" background-color:#FF5401;">';
          if( $percent>0 )echo '<font size=1>&nbsp;';
          echo '</td></tr></table>';
        echo '<td width="40%" align="left"><a href="http://'.NAME_SERVER.'/pollsresultanswer_'.$Poll['id'].'.html">'.$this->Msg->show_text('TXT_FRONT_USER_ANSWERS').'</a>';

     }
     echo '<tr><td colspan=3 align="left"><p>'.$this->Msg->show_text('_TXT_COUNT_ANSWERS').': '.$Poll['count'].' <br> '.date('d.m.Y G:i').'</p>';
     echo '</table>';

    } //--- end if
  } //--- end of VotePoll()



  // ================================================================================================
  // Function : ShowResultPage()
  // Date :    10.02.2011
  // Parms :   $id - poll id
  // Returns : true/false
  // Description : Show Result Page
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function ShowResultPage($id,$showarch=false )
  {

      ?>
        <div id="contenttxt">
        <div class="contenttitle1">
            <span><?=$this->multi['TXT_POLL_RESULTS'];?></span>
        </div>
        <div class="xdetails">
            <div class="topxdetails"></div>
            <div class="bodyxdetails"><?$this->ShowResult($id, $showarch);?></div>
            <div class="bottomxdetails"></div>
            </div>
      </div>
      <?
  } //--- end of ShowResultPage


  // ================================================================================================
  // Function : ShowResult()
  // Date :    10.02.2011
  // Parms :   $id - poll id
  // Returns : true/false
  // Description : Show Result
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function ShowResult( $id, $showarch=false, $title = null)
  {
    $Poll = $this->GetPollResult( $id );
    if( isset( $Poll['id'] ) )
    {
        $isAnsw=$this->IsAnswer( $id );
        $maxPercent=100;
        $countAnsw=null;
        uasort($Poll['alternatives'],'cmp_votes');
        if($isAnsw)
            $countAnsw=$this->AnswerCnt( $id );
        $max=null;
        ?><div class="result-poll-wrapper">
        <div class="question"><?= $Poll['name'];?> </div>
        <table class="poll-diagram" border=0 width=100% align="center" cellpadding="0" cellspacing="">
        <?
        $count=count($Poll['alternatives']);
        if($isAnsw)$count++;
        $j=0;
        $arrColor[0] = 'e06b4b';
        $arrColor[1] = 'bb6da0';
        $arrColor[2] = '8ebf4f';
        $arrColor[3] = '73acd6';
        $arrColor[4] = 'efa860';
//        var_dump($Poll);
        $allVotes = 0;
        foreach( $Poll['alternatives'] as $mas ){
            $allVotes += $mas['votes'];
        }
        foreach( $Poll['alternatives'] as $mas ){
            if($max==null)
            {
                $max=$mas['votes'];
                if($countAnsw>$max)
                    $max=$countAnsw;
            }

            $color = $arrColor[$j%5];
            $procent = round(($mas['votes']/$allVotes)*100,2);
            ?>
            <tr>
                <td>
                    <?= $mas['name'];?>
                    (<?=$mas['votes'];?>) <?=$procent?>%
                </td>
            </tr>
            <tr>
                <td>
                    <? if($mas['votes']>0){?>
                    <div style="background: #<?=$color;?>;  width:<?printf('%d',$procent)?>%;">&nbsp;</div>
                    <?}
                    else {?>
                    <div style="background: #<?=$color;?>;  width:1px;">&nbsp;</div>
                    <?}
                    ?>
                </td>
            </tr>
            <?
            $j++;
        }
        if($isAnsw){

            $color=$arrColor[$j%5];

            if($countAnsw!=0)
                $text='<a href="/pollsresultanswer_'.$Poll['id'].'.html" class="iframe">'.$this->Msg->show_text('TXT_FRONT_YOUR_CHOISE').'</a>';
            else
                $text=$this->Msg->show_text('TXT_FRONT_YOUR_CHOISE');
            ?>
            <tr>
                <td class="text"><?=$text;?>(<?=$countAnsw;?>)</td>
            </tr>
            <tr>
                <td>
                    <? if($countAnsw>0){?>
                    <div style="background: #<?=$color;?>;  width:<?printf('%d',$maxPercent*$countAnsw/$max);?>%;">&nbsp;</div>
                    <?}
                    else {?>
                    <div style="background: #<?=$color;?>;  width:1px;">&nbsp;</div>
                    <?}?>
                </td>
            </tr>
            <?
        }
        if($showarch)
        {
            ?>
            <tr>
                <td class="text">
                 </td>
            </tr>
            <?
        }
        ?>
        </table></div>
        <?
    }
  }


  function ShowResult_never_used( $id )
  {
    $Poll = $this->GetPollResult( $id );
    if( isset( $Poll['id'] ) )
    {
        $isAnsw=$this->IsAnswer( $id );
        $maxPercent=60;
        $textPercent=40;
        $intPercent=5;
        function cmp_votes($a,$b){
            if ($a['votes'] == $b['votes'])
                return 0;
            return ($a['votes'] > $b['votes']) ? -1 : 1;
        }
        uasort($Poll['alternatives'],'cmp_votes');
        ?>
        <p class="q"><?=$this->Spr->GetNameByCod( TblModPollSprQ, $Poll['id'] );?> </p>
        <table class="diagram" border=0 width=100% align="center" cellpadding="0" cellspacing="6">
        <?
        $count=count($Poll['alternatives']);
        if($this->IsAnswer( $id ))
            $count++;
        $countSameVate=0;
        $lastVate=-1;
        $countAnsw=null;
        if($isAnsw)
        {
            $countAnsw=$this->AnswerCnt( $id );
        }
        $isWasCountAnsw=false;
        foreach( $Poll['alternatives'] as $mas ){
            if($lastVate!=$mas['votes'])
            {
                $lastVate= $mas['votes'];
                $countSameVate++;
                if($mas['votes']==$countAnsw)
                {
                    //echo '$countAnsw='.$countAnsw;
                    //echo '$mas[\'votes\']='.$mas['votes'];
                    $isWasCountAnsw=true ;
                }
            }
        }
        //ECHO '<BR>$countSameVate='.$countSameVate;
        if(!$isWasCountAnsw)
        {
            $countSameVate++;
        }
        //ECHO '<BR>$countSameVate='.$countSameVate;

        $iAns=null;
        $lastVate=null;
        $max=null;
        $i=0;
        $j=0;
        foreach( $Poll['alternatives'] as $mas ){
            if($lastVate==null)
            {
                $lastVate=$mas['votes'];
                $max=$mas['votes'];
                if($countAnsw>$max)
                    $max=$countAnsw;
             }
            if($iAns==null)
            {
                if($countAnsw >= $mas['votes'])
                {
                    $i++;
                    $iAns=$i;
                    $lastVate=$countAnsw;
                }
            }
            if($lastVate!=$mas['votes'])
            {
                $lastVate=$mas['votes'];
                $i++;
            }
            if($j<$count/2)
                $color=sprintf('%06x',$this->GetColor(0x194e75,0x4493c8,2*$j/$count));
            else
                $color=sprintf('%06x',$this->GetColor(0x91989d,0xc7c7c7,2*($j-$count/2)/$count));
            ?>
            <tr>
                <td class="text" width="<?=$textPercent;?>%"><?=$this->Spr->GetNameByCod( TblModPollSprA, $mas['id'] )?></td>
                <td class="line" style="background: #<?=$color;?>;" width="<?printf('%d',$maxPercent*$mas['votes']/$max);?>%"; colspan="<?=$countSameVate-$i;?> ">&nbsp;<?/*printf('%d',$maxPercent*$mas['votes']/$max);*/?></td>
                <td class="vote" colspan="<?=$i+1;?>" width="<?printf('%d',$intPercent+$maxPercent-$maxPercent*$mas['votes']/$max);?>%"; ><?=$mas['votes'];?></td>
            </tr>
            <?
            $j++;
        }
        if($iAns==null)
        {
           $iAns=$i+1;
        }
        if($isAnsw)
        {
            if($j<$count/2)
                $color=sprintf('%06x',$this->GetColor(0x194e75,0x4493c8,2*$j/$count));
            else
                $color=sprintf('%06x',$this->GetColor(0x91989d,0xc7c7c7,2*($j-$count/2)/$count));

            if($countAnsw!=0)
                $text='<a href="http://'.NAME_SERVER.'/pollsresultanswer_'.$Poll['id'].'.html">'.$this->Msg->show_text('TXT_FRONT_USER_ANSWERS').'</a>';
            else
                $text=$this->Msg->show_text('TXT_FRONT_USER_ANSWERS');
            ?>
            <tr>
                <td class="text" width="<?=$textPercent;?>%"><?=$text;?></td>
                <td class="line" style="background: #<?=$color;?>;" width="<?printf('%d',$maxPercent*$countAnsw/$max);?>%"; colspan="<?=$countSameVate-$iAns;?> ">&nbsp;<?/*printf('%d',$maxPercent*$mas['votes']/$max);*/?></td>
                <td class="vote" colspan="<?=$iAns+1;?>" width="<?printf('%d',$intPercent+$maxPercent-$maxPercent*$countAnsw/$max);?>%"; ><?=$countAnsw;?></td>
            </tr>
            <?
        }
    } //--- end if
  } //--- end of VotePoll()



  // ================================================================================================
  // Function : ShowAnswer()
  // Date :    23.04.2011
  // Parms :   $id - poll id
  // Returns : true/false
  // Description : Show Answer
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function ShowAnswer( $id )
  {
      $Poll = $this->GetPollResult( $id );
    $script = $_SERVER['PHP_SELF'].'?task=result&amp;cd='.$id;
    if( $this->module ) $script = $script.'&module='.$this->module;
    if( $this->show_in ) $script = $script.'&show_in='.$this->show_in;
    ?><div class="question"><?=$this->multi['TXT_FRONT_USER_ANSWERS'];?></div><?
    echo '<table border=0 width=100%  align="center" cellpadding=4 cellspacing=1>';
     echo '<tr ><td align=center><a href="pollsresult_'.$id.'.html"> '.$this->multi['TXT_BACK'].' </a>';
     echo '<td >';
     if(isset($Poll['id']))echo '<b>'. $Poll['name'] .' </b>';
     $mas = $this->GetAnswer( $id );
     $count=count( $mas )+2;
     $n = count( $mas );
     for( $i = 0; $i < $n; $i++ )
     {
       $color=sprintf('%06x',$this->GetColor(0x194e75,0x4493c8,($i+1)/$count));
       $m = $mas[$i];
       $item = ( $i + 1 );
       echo '<tr><td width=15% align=center>'.$item;
       echo '<td width=85% >'.$m['answer'];
     }
    echo '</table>';

  } //--- end of ShowAnswer



  // ================================================================================================
  // Function : ShowArchive()
  // Date :    25.04.2011
  // Parms :   $id - poll id
  // Returns : true/false
  // Description : Show Archive
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function ShowArchive( $id = NULL )
  {
      $Page = check_init("PageUser","PageUser");
      $Page->h1 = $this->multi['_TXT_ARCHIVE_POLLS'];
      ?>
      <div id="contenttxt">
          <div class="xdetails">
              <div class="pollsArch">
                  <?
                  $mas = $this->GetArchive( $id );
//                  var_dump($mas);
                  $n = count( $mas );
                  if($n==0 ){
                      ?><div class="err"><?=$this->multi['TXT_NO_POLLS']?></div><?
                  }else{
                      for( $i = 0; $i < $n; $i++ ) {
                          $m = $mas[$i];
                          ?><div class="fon_arhive_poll"><?$this->ShowPolls(null,$m['id']);?></div><?
                      }
                  }
                  ?>
              </div>
              <div class="bottomxdetails"></div>
          </div>
      </div>
      <?
  } //--- end of ShowArchive


    function show_JS()
    {
       ?>
       <script type="text/javascript">
            function save_poll_get_result( )
            {
                removeTitle('#pollform');
                val=$("#pollform input[name='alt[-1]']").val();
                ans=$("#pollform input[name='answer']").val();
                ans = ans.replace(/^\s+/, '');
                ans = ans.replace(/\s+$/, '');
                if (val=='1'  & ans == "") {
                    alert('Пожалуйста, введите Ваш вариант ответа');
                    document.pollform.answer.focus();
                    //return false;
                }
                else {
                    $.ajax({
                    type: "POST",
                    data: $("#pollform").serialize() ,
                    url: "/polls.php",
                    success: function(msg){
                    //alert(msg);
                    $("#poll").html( msg );
                    },
                    beforeSend : function(){
                        //$("#sss").html("");
                        //alert('befor');
                        //$("#f_order_wrap").html('<div style="text-align:center;"><img src="/images/style/ajax-load.gif" alt="" title="" /></div>');
                    }
                    });
                }
            }


    function removeTitle(obj){
        $(obj).find('.textfield').each(function(i){
            if ($(this).attr("value") == $(this).attr("title"))
            $(this).attr("value", "");
        });
        return false;
    }

    function UpdCheck(check){
         var value=$(check).find('input').eq(0).val();
         if(value==1)
         {
            $(check).addClass('check-yes');
         }
         else
         {
            $(check).removeClass('check-yes');
         }
    }
    function UpdRadio(radio)
    {
        var parentGroup=$(radio).parents('.radio-group').get(0);
        $(parentGroup).find('.radio').each(function(){
            if(this!=radio)
            {
                $(this).find('input').eq(0).val(0);
                $(this).removeClass('radio-yes');
            }
        });
        $(radio).find('input').eq(0).val(1);
        $(radio).addClass('radio-yes');
    }

    $(document).ready(function(){
        $(".id_subj").change(function(){
            if($(this).find('option:selected').val()==""){
                $(this).removeClass('black');
            }
            else{
                $(this).addClass('black');
            }

        });


        //////////////////////////
         $(".textfield").focus(function(){
            if ($(this).val() == $(this).attr("title"))
            {
                $(this).val("");
                $(this).addClass("black");
            }
        });
        $(".textfield").blur(function(){
            if ($(this).val() == "" || $(this).val() == undefined)
            {
                $(this).val( $(this).attr("title"));
                $(this).removeClass("black");
            }
        });
        $(".textfield").each(function(){
        if ($(this).val() == "" || $(this).val() == undefined){
            $(this).val($(this).attr("title"));
            $(this).removeClass("black");
        }});
        ////////////////////
        $('.check').each(function(){
            UpdCheck(this);
        });
        $('.check').click(function(){
            var value=$(this).find('input').eq(0).val();
            if(value==1)
            {
                value=0;
            }
            else
            {
                value=1;
            }
            $(this).find('input').eq(0).val(value);
            UpdCheck(this);
        });
        ////////////////////////
        //$('.radio-group').each(function(){
        //    UpdRadioGroup(this);
        //});
        $('.radio-group .radio').click(function(){
            var value=$(this).find('input').eq(0).val();
            if(value==0)
            {

                UpdRadio(this);
            }
        });
    });


       </script>
       <?
    } //end of function showJS()


 } //--- end of class




 class PollSEO extends PollUse
 {
  var $id;
  var $title;

  function GetTitle( $id = NULL )
  {
      if(!isset($this->title))
            $this->title =  $this->Spr->GetNameByCod( TblModPollSprQ, $id );
       return $this->title;
  }

  function GetDescription( $id = NULL )
  {
      if(!isset($this->title))
            $this->title =  $this->Spr->GetNameByCod( TblModPollSprQ, $id );
       return $this->title;
  }

  function GetKeywords( $id = NULL )
  {
      if(!isset($this->title))
            $this->title =  $this->Spr->GetNameByCod( TblModPollSprQ, $id );
       return $this->title;
  }

 } //--- end of class PollSEO
?>
