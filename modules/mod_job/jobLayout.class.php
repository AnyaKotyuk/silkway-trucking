<?php
// ================================================================================================
//    System     : LTW
//    Module     : jobLayout.class.php
//    Version    : 1.0.0
//    Date       : 24.10.2006
//
//    Purpose    : Class definition for all actions with Layout for Job opportunity
//
//    Called by  : *ANY
//
// ================================================================================================

// Include needed for the script
include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_job/job.defines.php' );

// ================================================================================================
//    Class             : JobLayout
//    Version           : 1.0.0
//    Date              : 24.10.2006
//
//    Constructor       : Yes
//    Parms             :
//    Returns           : None
//    Description       : Class definition for all actions with Layout for Job opportunity
// ================================================================================================
//    Programmer        :  Igor Trokhymchuk
//    Date              :  24.10.2006
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
 class JobLayout extends Job {

       //Front-End
       var $srch = NULL;

       // ================================================================================================
       //    Function          : JobLayout (Constructor)
       //    Version           : 1.0.0
       //    Date              : 24.10.2006
       //    Parms
       //    Returns           : Error Indicator
       //
       //    Description       :
       // ================================================================================================
       function JobLayout($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL)
       {
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 10   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = '700');

                if (empty($this->Rights)) $this->Rights = new Rights($this->user_id, $this->module);
                 if (empty($this->db)) $this->db = new DB();
                if (empty($this->Form)) $this->Form = new Form('form_job');
                if (empty($this->Spr)) $this->Spr = new SysSpr();
				if (empty($this->Msg)) $this->Msg = new ShowMsg(); $this->Msg->SetShowTable(TblModJobSprTxt);

       } //end of JobLayout Constructor


// ================================================================================================
// Function : Show_jobs
// Version :  1.0.0
// Date :     06.10.2006
// Parms :    $title
// Returns :  true,false / Void
// Description : Show page
// ================================================================================================
// Programmer : Dmitriy Kerest
// Date : 06.10.2006
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function Show_jobs()
{
 $q = "select * from `".TblModJob."` where 1 and `visible`='2'";
 if(isset($this->cat) and $this->cat!='' and $this->cat!=0)
 {$q = $q." and `cat`='".$this->cat."'";}

 if(isset($this->status) and $this->status!='' and $this->status!=0)
 {$q = $q." and `status`='".$this->status."'";}
 ?>
 <table cellspacing="0" cellpadding="2" width="100%" border="0">


 <?
 //echo $q;
 $res = $this->db->db_Query($q);
 $rows = $this->db->db_GetNumRows();

 for($i=0; $i<$rows; $i++)
 {
  $row = $this->db->db_FetchAssoc();
  //echo "<br>".$row['age'];
  ?>
  <tr>
   <td width="80"><strong><?=$this->Msg->show_text('_FLD_VACANCY_NAME')?>:</strong></td>
   <td><?=stripslashes($this->Spr->GetNameByCod( TblModJobSprPosition, $row['id'] ))?></td>
   <td width="120" class="news_date"><?=$this->ConvertDate(trim(stripslashes($row['dt'])))?></td>
   <td width="10"></td>
  </tr>
  <tr>
   <td><strong><?=$this->Msg->show_text('_FLD_AGE')?>:</strong></td>
   <td colspan="2"><?=$row['age']?></td>
  </tr>
  <tr>
   <td><strong><?=$this->Msg->show_text('_FLD_EDUCATION')?>:</strong></td>
   <td colspan="2"><?=stripslashes($this->Spr->GetNameByCod( TblModJobSprEducation, $row['id'] ))?></td>
  </tr>
  <tr>
   <td><strong><?=$this->Msg->show_text('_FLD_EXPERIENCE')?>:</strong></td>
   <td colspan="2"><?=stripslashes($this->Spr->GetNameByCod( TblModJobSprExperience, $row['id'] ))?></td>
  </tr>
  <tr>
   <td><strong><?=$this->Msg->show_text('_FLD_CONTACTS')?>:</strong></td>
   <td colspan="2"><?=stripslashes($this->Spr->GetNameByCod( TblModJobSprContacts, $row['id'] ))?></td>
  </tr>
  <tr>
   <td height="10"></td>
  </tr>

  <?
 }

 ?>
 </table>
 <?
}// end of function Show_jobs


// ================================================================================================
// Function : Show_jobs
// Version :  1.0.0
// Date :     06.10.2006
// Parms :    $title
// Returns :  true,false / Void
// Description : Show page
// ================================================================================================
// Programmer : Dmitriy Kerest
// Date : 06.10.2006
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function show_Navigation()
{

 $q = "select DISTINCT cat from `".TblModJob."` where 1 and `visible`='2' order by cat";
 $res = $this->db->db_Query($q);
 $rows = $this->db->db_GetNumRows();
 ?>
 <table cellspacing="0" cellpadding="0" width="100%">
  <tr>
   <td style="border-top:2px solid #888888; border-bottom:2px solid #888888;" colspan="2"><h3 class="article_name"><?=$this->Msg->show_text('_TXT_CATEGORY')?></h3></td>
  </tr>

 <?
 for($i = 0; $i<$rows; $i++)
 {
  $row = $this->db->db_FetchAssoc();
  ?>
  <tr>
   <td colspan="2" height="3"></td>
  </tr>
  <tr>
   <td><img src="/images/design/arr.gif"></td>
   <td><?=$this->Form->Link("job.php?cat=".$row['cat']."&status=".$this->status, trim(stripslashes($this->Spr->GetNameByCod( TblModJobCategory, $row['cat'] ))), 'class="art"' );?></td>
  </tr>
  <?
 }
?>
  <tr>
   <td height="10"></td>
  </tr>
  <tr>
   <td colspan="2" style="border-top:2px solid #888888; border-bottom:2px solid #888888;"><h3 class="article_name"><?=$this->Msg->show_text('_TXT_ON_TERM')?></h3></td>
  </tr>
  <?
  $q = "select DISTINCT status from `".TblModJob."` where 1 and `visible`='2' order by status";
  $res = $this->db->db_Query($q);
  $rows = $this->db->db_GetNumRows();

  for( $i=0; $i<$rows; $i++ )
  {
   $row = $this->db->db_FetchAssoc();
   ?>
   <tr>
    <td colspan="2" height="3"></td>
   </tr>
   <tr>
    <td><img src="/images/design/arr.gif"></td>
	<td><?=$this->Form->Link("job.php?status=".$row['status']."&cat=".$this->cat, trim(stripslashes($this->Spr->GetNameByCod( TblModJobStatuses, $row['status'] ))), 'class="art"' );?></td>
   </tr>
   <?

  }
  ?>
 </table>
<?
}


// ================================================================================================
// Function : Show_jobs
// Version :  1.0.0
// Date :     06.10.2006
// Parms :    $title
// Returns :  true,false / Void
// Description : Show page
// ================================================================================================
// Programmer : Dmitriy Kerest
// Date : 06.10.2006
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function ConvertDate($date)
{
if(!empty($date))
{

$arr = explode("-", $date);

$m_word = NULL;
$month = NULL;
$day = NULL;
$year = NULL;

$year_determ = $this->Spr->GetNameByCod( TblSysSprMonth, 13, _LANG_ID );

$m_word[1] = $this->Spr->GetNameByCod( TblSysSprMonth, 1, _LANG_ID );
$m_word[2] = $this->Spr->GetNameByCod( TblSysSprMonth, 2, _LANG_ID );
$m_word[3] = $this->Spr->GetNameByCod( TblSysSprMonth, 3, _LANG_ID );
$m_word[4] = $this->Spr->GetNameByCod( TblSysSprMonth, 4, _LANG_ID );
$m_word[5] = $this->Spr->GetNameByCod( TblSysSprMonth, 5, _LANG_ID );
$m_word[6] = $this->Spr->GetNameByCod( TblSysSprMonth, 6, _LANG_ID );
$m_word[7] = $this->Spr->GetNameByCod( TblSysSprMonth, 7, _LANG_ID );
$m_word[8] = $this->Spr->GetNameByCod( TblSysSprMonth, 8, _LANG_ID );
$m_word[9] = $this->Spr->GetNameByCod( TblSysSprMonth, 9, _LANG_ID );
$m_word[10] = $this->Spr->GetNameByCod( TblSysSprMonth, 10, _LANG_ID );
$m_word[11] = $this->Spr->GetNameByCod( TblSysSprMonth, 11, _LANG_ID );
$m_word[12] = $this->Spr->GetNameByCod( TblSysSprMonth, 12, _LANG_ID );

$month = $m_word[intval($arr[1])];

return $month.", ".$arr[2]." ".$arr[0]." ".$year_determ;

} //end if

}


// ================================================================================================
// Function : JobOpp
// Version :  1.0.0
// Date :     06.10.2006
// Parms :    $title
// Returns :  true,false / Void
// Description : Show page
// ================================================================================================
// Programmer : Igor Trohkymchuk
// Date : 06.10.2006
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
        function JobOpp( $id = NULL, $srch = NULL )
        {
          echo '<table border=0 cellspacing="12" align=center class="F">';
          echo '<tr><td><h2> IMPROVE YOUR CAREER WITH GLOBAL OPPORTUNITIES!</h2>
                <tr><td>
                    Join the leading manufacturer of waterproof connectors in Asia with offices and plants in Taiwan, China, USA and Europe. Successful candidates will be based in Subic Bay Industrial Park and in other branches overseas.
                    <br><br><b>We need qualified candidates with:</b>
                    <UL>
                     <li>Positive attitude</li>
                     <li>Highly creative</li>
                     <li>Flexible</li>
                     <li>Excellent communication skills</li>
                     <li>Result oriented and</li>
                     <li>Team player</li>
                     <li>Fluent in Mandarin, Japanese, German, French or Italian is an advantage</li>
                    </UL>
                    <br><b>We offer to all successful candidates:</b>
                    <UL>
                     <li>Extensive Training for AutoCAD and 3D Drawings</li>
                     <li>Housing accommodation for applicants outside Olongapo City, Zambales and Bataan</li>
                     <li>Overseas Training (China, Taiwan, Japan and U.S.A.)</li>
                     <li>Career Advancement</li>
                    </UL>
                <tr><td class="User" align=center>
             </table>';
          return true;
        } // end of function JobOpp()

        // ================================================================================================
        // Function : JobOpSearchForm
        // Version :  1.0.0
        // Date :     06.10.2006
        // Parms :    $title
        // Returns :  true,false / Void
        // Description : Show page
        // ================================================================================================
        // Programmer : Igor Trohkymchuk
        // Date : 06.10.2006
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function JobOpSearchForm()
        {
          echo '<table border=0 cellpadding="0" width=100% cellspacing="0" class="user_background">
                <form action="'.$_SERVER['PHP_SELF'].'?task=srch" method="post"
                <tr><td align=center valign=middle class="tr_2_user">
                Job Opportunity Search: <input type="text" class="textbox_user" name="srch" size="30" value="'.$this->srch.'">&nbsp;&nbsp;
                <input name="Search" class="button_user" type="submit" value="Search">';
          echo '</form>';
         echo '</table>';
        } //--- end of JobOpSearchForm

        // ================================================================================================
        // Function : JobOpSearchResult
        // Version :  1.0.0
        // Date :     06.10.2006
        // Parms :    $title
        // Returns :  true,false / Void
        // Description : Show page
        // ================================================================================================
        // Programmer : Igor Trohkymchuk
        // Date : 06.10.2006
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function JobOpSearchResult( $Arr = NULL )
        {
          echo '<table border=0 cellspacing="12" align=center class="F">
                <tr><td align=center> <h3>';
          if( count( $Arr ) == 1 ) echo ''.count( $Arr ).' position is found';
          if( count( $Arr ) > 1 ) echo ''.count( $Arr ).' positions are found';
          if( count( $Arr ) < 1 ) echo ' not found';
          echo '</h3>';
          if( count( $Arr ) )
          {
           while( $el = each( $Arr ) )
           {
             $m = $el['value'];
            echo '<tr><td align=center>';
            echo '<table width="100%" border=0 align="center" cellpadding="5" cellspacing="1" class="user_background">';
            echo '<tr><td width=150 class="User_detail">Position available<td CLASS="tr_2_user">'.$m['position'];
            echo '<tr><td class="W">'.$m['date'];
            echo '<td CLASS="tr_2_user"> <a class="Prod" href='.$_SERVER['PHP_SELF'].'?task=detail&amp;id='.$m['id'].'>details</a>';
            echo '</table>';
            echo '<tr><td class="line">';
           }
          }
          echo '</table>';
          return true;
        } //--- end of JobOpSearchResult

        // ================================================================================================
        // Function : JobOpDetails
        // Version :  1.0.0
        // Date :     06.10.2006
        // Parms :    $title
        // Returns :  true,false / Void
        // Description : Show page
        // ================================================================================================
        // Programmer : Igor Trohkymchuk
        // Date : 06.10.2006
        // Reason for change : Creation
        // Change Request Nbr:
        // ================================================================================================
        function JobOpDetails()
        {
          $this->JobOpSearchForm( $this->srch );
          if( empty($this->id) ) return false;

          $q = "select * from ".TableJobOpp." where id='$this->id'";
          $res = mysql_query( $q );
          $rows = '';
          echo '<table border=0 width="100%" align="center" cellpadding="5" cellspacing="1">
                 <tr><td align=center>';
          echo '<h3>Position available</h3>';
          if( $res )
          {
            $m = mysql_fetch_array( $res );
            echo '<tr><td valign=top>';
            echo '<table border=0 width="100%" align="center" cellpadding="5" cellspacing="1" class="user_background">';
            echo '<tr><td width=150 class="User_detail">Position available<td CLASS="tr_2_user">'.$m['position'];
            echo '<tr><td class="User_detail">Education requirements<td CLASS="tr_2_user">'.$m['education'];
            echo '<tr><td class="User_detail">Experience<td CLASS="tr_2_user">'.$m['experience'];
            echo '<tr><td class="User_detail">Description<td CLASS="tr_2_user">'.$m['description'];
            echo '<tr><td class="User_detail">Contact Information<td CLASS="tr_2_user">'.$m['contactinf'];
            echo '<tr><td class="W">'.$m['date'];
            echo '<td CLASS="tr_2_user">';
            echo "<a class=\"Prod\" HREF=\"javascript:void(0)\" OnClick='window.open(\"/modules/mod_job/job_print.php?about=5&id=$this->id\", \"\", \"width=600, height=600 \")'>Print Version</A>";
            echo '<br>
            <a class="Prod" href='.$_SERVER['PHP_SELF'].'>All Positions</a>';
            echo '</table>';
          }
          echo '</table>';
          return true;
        } //--- end of JobOpDetails

// ================================================================================================
// Function : GetSeo
// Version :  1.0.0
// Date :     06.10.2006
// Parms :    $title
// Returns :  Seo optimized title, description, keywords
// Description : Show page
// ================================================================================================
// Programmer : Dmitriy Kerest
// Date : 06.10.2006
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================
function GetSeo()
{
 $res['title'] = $this->Msg->show_text('_TXT_JOB_TITLE');
 $res['description'] = $this->Msg->show_text('_TXT_JOB_DESCRIPTION');
 $res['keywords'] = $this->Msg->show_text('_TXT_JOB_KEYWORDS');
 return $res;
}


 } //end of class JobLayout
?>
