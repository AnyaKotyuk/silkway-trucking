<?
// ================================================================================================
//    System     : CMS
//    Module     : POLL
//    Date       : 12.02.2011
//    Licensed To:
//                 Igor  Trokhymchuk  ihoru@mail.ru
//                 Yaroslav Gyryn    las_zt@mail.ru
//    Purpose    : POLL - module
// ================================================================================================
//    Class             : Poll
//    Date              : 12.02.2011
//    Constructor       : Yes
//    Returns           : None
//    Description       : POLL Module
//    Programmer        :  Yaroslav Gyryn
// ================================================================================================

 class Poll
 {
   var $id;
   var $question;
   var $votes;
   var $start_date;
   var $end_date;
   var $status;
   var $type;
   var $users_answers;
   var $multy;
   var $display;

   /* alternatives */
   var $poll_id;
   var $sel = NULL;
   var $qusetion = NULL;

  // ================================================================================================
  //    Function          : Poll (Constructor)
  //    Date              : 12.02.2011
  //    Returns           : true/false
  //    Description       : Constructor of Poll Class Definition
  // ================================================================================================
  function Poll()
  {
    $this->CheckStatus();
  }



  // ================================================================================================
  // Function : GetCountAlternatives()
  // Date : 12.02.2011
  // Parms :   $poll_id
  // Returns : true,false / Void
  // Description : Get Alternatives
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GetCountAlternatives( $poll_id )
  {
   $db = new DB();
   $Count = NULL;
   $q = "select * from ".TblModPollAlt." where poll_id='$poll_id'";
   $res = $db->db_Query( $q );
   if( !$res )  return $Row;

   $Count = $db->db_GetNumRows();
   return $Count;
  } //--- end of GetCountAlternatives


  // ================================================================================================
  // Function : GeAlternativestByID()
  // Date : 12.02.2011
  // Parms :   $poll_id
  // Returns : array of Alternatives ( $Arr[ lang_id ][ cod ] )
  // Description : Get Alternatives By Poll ID from Alternatives-table
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GeAlternativestByID( $poll_id )
  {
   $db = new DB();
   $Arr = NULL;

   $q = "SELECT * FROM ".TblModPollAlt." where poll_id='$poll_id'";

   $res = $db->db_Query( $q );
   if( !$res )  return $Row;
   $rows = $db->db_GetNumRows();
   for( $i = 0; $i < $rows; $i++ )
   {
     $Row = $db->db_FetchAssoc();
     $Arr[ $Row['lang_id'] ][ $Row['cod'] ] = $Row['alternative'];
   }

   return $Arr;
  } //--- end of GetRowByCODandLANGID



  // ================================================================================================
  // Function : GetNewDisplay()
  // Date : 12.02.2011
  // Returns : New Display Value
  // Description : Get New Display Value
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GetNewDisplay()
  {
    $db = new DB();
    $q = "select * from ".TblModPollAlt." order by `display` desc";
    $res = $db->db_Query( $q );
    $tmp = $db->db_FetchAssoc();
    return  ( $tmp['display'] + 1 );
  } //--- end of GetNewDisplay


  // ================================================================================================
  // Function : GetSUMVote()
  // Date : 12.02.2011
  // Parms :   $poll_id
  // Returns : Count Vote
  // Description : Get Summ Votes of Poll
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GetSUMVote( $poll_id )
  {
    $db = new DB();
    $q = "select sum(votes) as cnt from ".TblModPollAlt." where poll_id=$poll_id";
    $res = $db->db_Query( $q );
    $tmp = $db->db_FetchAssoc();
    return  $tmp['cnt'];
  } //--- end of GetSUMVote



  // ================================================================================================
  // Function : GetCountIP()
  // Date : 12.02.2011
  // Parms :   $poll_id
  // Returns : Count IP
  // Description : Get Count IP-Adress of Poll
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GetCountIP( $poll_id )
  {
    $db = new DB();
    $q = "select count(ip) as cnt from ".TblModPollIP." where poll_id=$poll_id";
    $res = $db->db_Query( $q );
    $tmp = $db->db_FetchAssoc();
    return  $tmp['cnt'];
  } //--- end of GetCountVote



  // ================================================================================================
  // Function : GetCountUsersAnswers()
  // Date : 12.02.2011
  // Parms :   $poll_id
  // Returns : Count Count Users Answers
  // Description : Get Count Users Answers of Poll
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GetCountUsersAnswers( $poll_id )
  {
    $db = new DB();
    $q = "select count(answer) as cnt from ".TblModPollAnswers." where poll_id=$poll_id";
    $res = $db->db_Query( $q );
    $tmp = $db->db_FetchAssoc();
    return  $tmp['cnt'];
  } //--- end of GetCountUsersAnswers


  // ================================================================================================
  // Function : GetActivePoll()
  // Date : 12.02.2011
  // Returns : Array with fields of Active Poll
  // Description : Get Active Poll
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GetActivePoll()
  {
    $db = new DB();
    $APoll = NULL;
    $tmp_arr = NULL;

    $q = "select * from ".TblModPoll." where `status`='a' and `type`='sys' order by start_date desc";
    $res = $db->db_Query( $q );
    $kol = $db->db_GetNumRows();
    if( $kol < 1 ) return false;

    $APoll = $db->db_FetchAssoc();

    $q = "select * from ".TblModPollAlt." where poll_id=".$APoll['id']." order by display";
    $res = $db->db_Query( $q );
    $rows = $db->db_GetNumRows();

    for( $i = 0,$j = 0; $i < $rows; $i++,$j++ )
    {
      $tmp = $db->db_FetchAssoc();
      $tmp_arr[$j] = $tmp['id'];
    }

    $APoll['alternatives'] = $tmp_arr;

    return  $APoll;
  } //--- end of GetActivePoll


  // ================================================================================================
  // Function : GetActivePolls()
  // Date : 12.02.2011
  // Returns : Array with fields of Active Polls
  // Description : Get Active Polls
  // Programmer : Ihor Trokhymchuk
  // ================================================================================================
  function GetActivePolls2()
  {
    $db = new DB();
    $db2 = new DB();
    $APoll = NULL;
    $ArrPolls = NULL;
    $tmp_arr = NULL;

    $q = "SELECT * FROM `".TblModPoll."` WHERE `status`='a' and `type`='sys' ORDER BY start_date desc";
    $res = $db->db_Query( $q );
    $kol = $db->db_GetNumRows();
    //echo '<br>$kol='.$kol;
    if( $kol < 1 ) return false;
    for($ikk=0;$ikk<$kol;$ikk++){
        $APoll = $db->db_FetchAssoc();
        $q = "SELECT * FROM `".TblModPollAlt."` WHERE `poll_id`=".$APoll['id']." ORDER BY display";
        $res = $db2->db_Query( $q );
        $rows = $db2->db_GetNumRows();

        $j = 0;
        $tmp_arr = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
          $tmp = $db2->db_FetchAssoc();
          $tmp_arr[$j] = $tmp['id'];
          $j = $j + 1;
        }
        $ArrPolls[$APoll['id']]=$APoll;
        $ArrPolls[$APoll['id']]['alternatives'] = $tmp_arr;
    }
    //echo '<br>';print_r($ArrPolls);echo '<br><br>';
    return  $ArrPolls;
    $q = "SELECT * FROM `".TblModPoll."` LEFT JOIN `".TblModPollAlt."` ON(`".TblModPoll."`.id = `".TblModPollAlt."`.poll_id)
                 WHERE  `".TblModPoll."`.`status`='a' 
                    and `".TblModPoll."`.`type`='sys' 
                 ORDER BY `".TblModPoll."`.start_date,`".TblModPoll."`.id desc";
    $res = $db->db_Query( $q );
  } // end of GetActivePolls()  
  
  function GetActivePolls($id_poll)
  {
    $db = new DB();
    $db2 = new DB();
    $APoll = NULL;
    $ArrPolls = NULL;
    $tmp_arr = NULL;

     $q = "SELECT 
                 `".TblModPoll."`.id as id_poll,
                 `".TblModPoll."`.multy,
                 `".TblModPoll."`.users_answers,
                 `".TblModPollAlt."`.id as id_altr,
                 `".TblModPollSprQ."`.name as question,
                 `".TblModPollSprA."`.name as altr   
                 FROM   `".TblModPoll."` LEFT JOIN `".TblModPollAlt."` ON(`".TblModPoll."`.id = `".TblModPollAlt."`.poll_id),
                        `".TblModPollSprQ."`,`".TblModPollSprA."`
                 WHERE ";
      if(empty($id_poll))$q .= " `".TblModPoll."`.`status`='a'  and ";
      else{
          $q .= " `".TblModPoll."`.`id`='$id_poll'  and ";
      }
      $q .= " `".TblModPoll."`.`type`='sys'
                    and `".TblModPollSprQ."`.cod=`".TblModPoll."`.id
                    and `".TblModPollSprQ."`.lang_id='"._LANG_ID."'
                    and `".TblModPollSprA."`.cod=`".TblModPollAlt."`.id
                    and `".TblModPollSprA."`.lang_id='"._LANG_ID."'
                 ORDER BY `".TblModPoll."`.start_date,`".TblModPoll."`.id desc";
    $res = $db->db_Query( $q );
    $kol = $db->db_GetNumRows();
//    echo '<br>$kol='.$kol.' res='.$res.' q='.$q;
    if( $kol < 1 ) return false;
    $temp_poll=null;
    $tmp_arr = array();
    for($ikk=0;$ikk<$kol;$ikk++){
        $row = $db->db_FetchAssoc();
        
        if($ikk==0) $temp_poll=$row['id_poll']; 
        
        if($temp_poll!=$row['id_poll'] || $ikk==$kol-1){
            if($ikk==$kol-1){
                $tmp_arr[] = $row;
            }
            $ArrPolls[$temp_poll] = $tmp_arr;
            if(!empty($ArrPolls[$temp_poll]['question']))$ArrPolls[$temp_poll]['question'] = $row['question'];
            $tmp_arr = array();
            $temp_poll = $row['id_poll'];
        }
        $tmp_arr[] = $row;
    }
    //echo '<br>';print_r($ArrPolls);echo '<br><br>';
    return  $ArrPolls;
  } // end of GetActivePolls()  

     function checkVote($id, $REMOTE_ADDR){
         $db = new DB();

         $q = "SELECT * FROM `".TblModPollIP."` WHERE `ip`='".$REMOTE_ADDR."' AND `poll_id`='".$id."'";
         $res_ip = $db->db_Query( $q );
         $kol_ip = $db->db_GetNumRows();
         //echo '<br>$q='.$q.' $res_ip='.$res_ip.' $kol_ip='.$kol_ip.'<br/>';

         if( $kol_ip > 0 ) {
            return 1;
         }else{
             return 0;
         }

     }

  // ================================================================================================
  // Function : Vote()
  // Date : 12.02.2011
  // Parms :   $id, $alt, $REMOTE_ADDR, $answer
  // Returns : true/false
  // Description : Vote
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function Vote( $id, $alt, $REMOTE_ADDR, $answer )
  {
    $db = new DB();

    $q = "SELECT * FROM `".TblModPollIP."` WHERE `ip`='".$REMOTE_ADDR."' AND `poll_id`='".$id."'";
    $res_ip = $db->db_Query( $q );
    $kol_ip = $db->db_GetNumRows();
    //echo '<br>$q='.$q.' $res_ip='.$res_ip.' $kol_ip='.$kol_ip.'<br/>';
    if( $kol_ip > 0 ) {
        echo '<div class="err">К сожалению вы не можете проголосовать повторно, так как с вашего IP-адреса: '.$REMOTE_ADDR.' уже было проведено голосование.</div>';
         return;
    }
    
    $q = "SELECT * FROM ".TblModPoll." WHERE `id`='".$id."'";
    $res = $db->db_Query( $q );
    $row = $db->db_FetchAssoc();
    //print_r($row);
    $multy = $row['multy'];
    $users_answers = $row['users_answers'];
    if( $multy != 'on' )
    {
      /*$selAlt=null;  
      foreach($alt as $key=>$val)
      {
         if($val==1)
            $selAlt=$key;
      } */
      $selAlt = $this->alt;
       
      $q = "select * from ".TblModPoll." where `id`='".$id."'";
      $res = $db->db_Query( $q );
      $row = $db->db_FetchAssoc();
      $votes1 = $row['votes'] + 1;

      $q = "select * from ".TblModPollAlt." where `id`='".$selAlt."'";
      $res = $db->db_Query( $q );
      $row = $db->db_FetchAssoc();
      $votes = $row['votes'] + 1;

      if( $selAlt > 0 )
      {
        $q = "update ".TblModPoll." set votes='".$votes1."' where id='".$id."'";
        $res = $db->db_Query( $q );

        $q = "update ".TblModPollAlt." set votes='".$votes."' where id='".$selAlt."'";
        $res = $db->db_Query( $q );

        $q = "insert into ".TblModPollIP." values(NULL,'".$REMOTE_ADDR."','".$id."')";
        $res = $db->db_Query( $q );
        //echo 'insert fd';
        
      }
      elseif( $selAlt == -1 and $answer!='' )
      {
        //--- If Set User Variant Options
        if( $users_answers == 'on' )
        {
            $q = "INSERT INTO ".TblModPollAnswers." VALUES(NULL,'1','"._LANG_ID."','".$id."','".$answer."')";
           // echo '<br>$q='.$q.' $res='.$res;
            $res = $db->db_Query( $q );
        } //--- end if
        $q = "INSERT INTO ".TblModPollIP." VALUES(NULL,'".$REMOTE_ADDR."','".$id."')";
        //echo '<br>$q='.$q.' $res='.$res;
        $res = $db->db_Query( $q );
      } //--- end if
    }
    else{
      $alt = $this->alt;
      $countAlt=0; 
      foreach($alt as $key=>$val)
      { 
//        echo ' vote='.$val.'<br/>';
        //if($val==1)
        //{
            $countAlt++;                   
            $q = "select * from ".TblModPoll." where id='".$id."'";
            $res = $db->db_Query( $q );
            $row = $db->db_FetchAssoc();
            $votes = $row['votes'] + 1;
            
            $q = "update ".TblModPoll." set votes='".$votes."' where id='".$id."'";
            $res = $db->db_Query( $q );
            //echo '<br>$alt[$i]='.$key;
            if( $val > 0 )
            {
              $q = "select * from ".TblModPollAlt." where id='".$val."'";
              $res = $db->db_Query( $q );
              $row = $db->db_FetchAssoc();
              $votes = $row['votes'] + 1;
              $q = "update ".TblModPollAlt." set votes='".$votes."' where id='".$val."'";
              $res = $db->db_Query( $q );
            }
            elseif( $val == -1 && $users_answers == 'on' )  //--- else Save User Variant
            {
                $q = "INSERT INTO ".TblModPollAnswers." VALUES(NULL,'1','". _LANG_ID."','".$id."','".$answer."')";
                //echo '<br>$q='.$q.' $res='.$res;
                $res = $db->db_Query( $q );
            } //--- end if
         //}
      } //--- end for
      if( $countAlt > 0 )
      {
        $q = "insert into ".TblModPollIP." values(NULL,'".$REMOTE_ADDR."','".$id."')";
        $res = $db->db_Query( $q );
      }
    }

  } //--- end of Vote

  
  

  // ================================================================================================
  // Function : GetPollResult()
  // Date : 12.02.2011
  // Parms :   $id - poll id
  // Returns : Array with fields of Active Poll
  // Description : Get Poll Result
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GetPollResult( $id = NULL )
  {
    $db = new DB();
    $Poll = NULL;
    $tmp_arr = NULL;
    $cnt = NULL;      //--- all votes count

    $q = "select ".TblModPoll.".*,
    ".TblModPollSprQ.".`name`
    from ".TblModPoll.", ".TblModPollSprQ."
    where ".TblModPoll.".`id`='$id'
     and ".TblModPollSprQ.".cod = ".TblModPoll.".`id`
     and ".TblModPollSprQ.".lang_id = '"._LANG_ID."' ";
    $res = $db->db_Query( $q );
//      echo '<br>$q ='.$q.' $res='.$res;
    $Poll = $db->db_FetchAssoc();
    $q = "select ".TblModPollAlt.".* ,
    `".TblModPollSprA."`.`name`
    from ".TblModPollAlt.",`".TblModPollSprA."`
    where ".TblModPollAlt.".poll_id=".$Poll['id']."
    and ".TblModPollAlt.".id  = `".TblModPollSprA."`.cod
    and `".TblModPollSprA."`.lang_id = '"._LANG_ID."'
    order by display";
    $res = $db->db_Query( $q );
    $rows = $db->db_GetNumRows();

    $j = 0;
    for( $i = 0; $i < $rows; $i++ )
    {
      $tmp = $db->db_FetchAssoc();
      $tmp_arr[$j] = $tmp;
      $cnt = $cnt + $tmp['votes'];
      $j = $j + 1;
    }

    $Poll['alternatives'] = $tmp_arr;
    $Poll['count'] = $cnt;

    if( $this->IsAnswer( $id ) )
    {
      $Poll['count'] = $Poll['count'] + $this->AnswerCnt( $id );
    }

    return  $Poll;
  } //--- end of GetPollResult


  // ================================================================================================
  // Function : AnswerCnt()
  // Date : 12.02.2011
  // Parms :   $id - poll id
  // Returns : Answer's Count
  // Description : Get Answer's Count
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function AnswerCnt( $id = NULL )
  {
    $db = new DB();
    $tmp_arr = NULL;
    $cnt = NULL;      //--- all votes count

    $q = "select count(id) as cnt from ".TblModPollAnswers." where `poll_id`='$id'";
    $res = $db->db_Query( $q );
    $tmp_arr = $db->db_FetchAssoc();
    $cnt = $tmp_arr['cnt'];

    return  $cnt;
  } //--- end of AnswerCnt



  // ================================================================================================
  // Function : GetAnswer()
  // Date : 12.02.2011
  // Parms :   $id - poll id
  // Returns : Array with Poll Answer
  // Description : Get Poll Answer's
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GetAnswer( $id = NULL )
  {
    $db = new DB();
    $tmp_arr = NULL;

    $q = "select * from ".TblModPollAnswers." where `poll_id`='$id'";
    $res = $db->db_Query( $q );
    $rows = $db->db_GetNumRows();

    $j = 0;
    for( $i = 0; $i < $rows; $i++ )
    {
      $tmp = $db->db_FetchAssoc();
      $tmp_arr[$j] = $tmp;
      $j = $j + 1;
    }
    return  $tmp_arr;
  } //--- end of GetAnswer




  // ================================================================================================
  // Function : IsAnswer()
  // Date :    12.02.2011
  // Parms :   $id - poll id
  // Returns : true/false
  // Description : Is Poll have a User-Answer-option...
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function IsAnswer( $id = NULL )
  {
    $db = new DB();
    $Poll = NULL;
    $tmp_arr = NULL;
    $cnt = NULL;      //--- all votes count

    $q = "select * from ".TblModPoll." where `id`='$id'";
    $res = $db->db_Query( $q );
    $Poll = $db->db_FetchAssoc();

    if( $Poll['users_answers'] == 'on' ) return true;
    else return false;

    return  $Poll;
  } //--- end of IsAnswer



  // ================================================================================================
  // Function : IsMulty()
  // Date :    12.02.2011
  // Parms :   $id - poll id
  // Returns : true/false
  // Description : Is Poll a Multy-selection...
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function IsMulty( $id = NULL )
  {
    $db = new DB();
    $Poll = NULL;
    $tmp_arr = NULL;
    $cnt = NULL;      //--- all votes count

    $q = "select * from ".TblModPoll." where `id`='$id'";
    $res = $db->db_Query( $q );
    $Poll = $db->db_FetchAssoc();

    if( $Poll['multy'] == 'on' ) return true;
    else return false;

    return  $Poll;
  } //--- end of IsAnswer



  // ================================================================================================
  // Function : GetArchive()
  // Version : 1.0.0
  // Date :    12.02.2011
  // Parms :   $id - poll id
  // Returns : Array with Poll Answer
  // Description : Get Poll's Archive
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function GetArchive( $id = NULL )
  {
    $db = new DB();
    $tmp_arr = NULL;

    $q = "SELECT `".TblModPoll."`.*,
     `".TblModPollSprQ."`.name as question
     FROM `".TblModPoll."`, `".TblModPollSprQ."`
     WHERE `".TblModPollSprQ."`.`cod` = ".TblModPoll.".`id` and
      `".TblModPollSprQ."`.`lang_id` = '"._LANG_ID."' ";
    if( $id )     $q = $q." AND `id`!='".$id."' ";
    $q = $q." AND `status`='e' ORDER BY start_date desc";

    $res = $db->db_Query( $q );
    $rows = $db->db_GetNumRows();
    $j = 0;
    for( $i = 0; $i < $rows; $i++ )
    {
      $tmp = $db->db_FetchAssoc();
      $tmp_arr[$j] = $tmp;
      $j = $j + 1;
    }
    return  $tmp_arr;
  } //--- end of GetArchive


  // ================================================================================================
  // Function : CheckStatus()
  // Date :    12.02.2011
  // Parms :   $id - poll id
  // Returns : true/false
  // Description : Check Poll Status
  // Programmer : Yaroslav Gyryn
  // ================================================================================================
  function CheckStatus( $id = NULL )
  {
    $db = new DB();
    $db2 = new DB();
    $dt = date('YmdHi');
    $q = "select * from ".TblModPoll." where `status`='a'";
    $res = $db->db_Query( $q );
    $rows = $db->db_GetNumRows();

    for( $i = 0; $i < $rows; $i++ )
    {
      $tmp = $db->db_FetchAssoc();
      $m = explode( '-', $tmp['end_date'] );
      //echo '<br>$m='.$m;print_r($m);
      $m1 = explode( ' ', $m[2] );
      $m2 = explode( ':', $m1[1] );
      $dt2 = $m[0].$m[1].$m1[0].$m2[0].$m2[1];
      if( $dt > $dt2 )
      {
          echo 'update';
        $q = "update ".TblModPoll." set `status`='e' where `id`='".$tmp['id']."'";
        $res = $db2->db_Query( $q );
      }
    }
    return  true;

  } //--- end of CheckStatus

 } //--- end of class Poll
?>