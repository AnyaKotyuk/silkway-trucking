<?
// ================================================================================================
//    System     : PrCSM05
//    Module     : Pages
//    Version    : 1.0.0
//    Date       : 04.02.2005
//    Licensed To:
//                 Andriy Lykhodid    las_zt@mail.ru
//				   Dmitriy Kerest	  demetrius2006@gmail.com
//
//    Purpose    : Class definition for Pages - moule
//
// ================================================================================================

 include_once( $_SERVER['DOCUMENT_ROOT'].'/modules/mod_job/job.defines.php' );

// ================================================================================================
//    Class             					: Pages
//    Version           				: 1.0.0
//    Date              					: 04.02.2005
//    Constructor       				: Yes
//    Parms             				:
//    Returns           				: None
//    Description       				: Pages Module
// ================================================================================================
//    Programmer        			:  Dmitriy Kerest
//    Date              					:  04.02.2005
//    Reason for change 			:  Creation
//    Change Request Nbr			:  N/A
// ================================================================================================

class JobBackend extends Job
{

    /**
    * Class Constructor
    * Set the variabels
    * @return true/false
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 08.10.2011
    */
    function __construct($user_id = NULL, $module = NULL)
    {
        $this->user_id = $user_id;
        $this->module = $module;

        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;
        $this->db =  DBs::getInstance();
        $this->Right =  check_init('RightsVideo', 'Rights', "'".$this->user_id."','".$this->module."'");
        $this->Form = check_init('FormVideo', 'Form', "'mod_pages'");
        $this->ln_sys = check_init('SysLang', 'SysLang');
        $this->ln_arr = $this->ln_sys->LangArray( $this->lang_id );
        if (empty($this->Msg)) $this->Msg = check_init('ShowMsg', 'ShowMsg');
        if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr');

        $this->multi = check_init_txt('TblBackMulti',TblBackMulti);
    }

// ================================================================================================
// Function : show()
// Version : 1.0.0
// Date : 04.01.2005
//
// Parms :
// Returns :     true,false / Void
// Description : Show News
// ================================================================================================
// Programmer : Andriy Lykhodid
// Date : 04.01.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

function show()
{
 $db = new Rights;
 $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr.'&fln='.$this->fln;
 $script = $_SERVER['PHP_SELF']."?$script";

 $q = "SELECT * FROM ".TblModJob." where 1";
 if( !$this->sort ) $this->sort='move desc';
 $q = $q." order by ".$this->sort;
 //echo $q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;

 $rows = $this->Right->db_GetNumRows($res);

 /* Write Form Header */
 $this->Form->WriteHeader( $script );

 /* Write Table Part */
 AdminHTML::TablePartH();


  /* Write Links on Pages */

 echo '<TR><TD COLSPAN=13>';

 $script1 = 'module='.$this->module.'&fltr='.$this->fltr;

 $script1 = $_SERVER['PHP_SELF']."?$script1";

 $this->Form->WriteLinkPages( $script1, $rows, $this->display, $this->start, $this->sort );



 echo '<TR><TD COLSPAN=3>';

 $this->Form->WriteTopPanel( $script );



 $script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;

 $script2 = $_SERVER['PHP_SELF']."?$script2";



 ?>



<TR>

  <td class="THead">*

    </Th>

  <td class="THead"><A HREF=<?=$script2?>&sort=id>

    <?=$this->Msg->show_text('_FID_ID')?>

    </A>

    </Th>

  <td class="THead"><A HREF=<?=$script2?>&sort=subject>

    <?=$this->Msg->show_text('_FLD_VACANCY_NAME')?>

    </A>

    </Th>

  <td class="THead"><A HREF=<?=$script2?>&sort=age>

    <?=$this->Msg->show_text('_FLD_AGE')?>

    </A>

    </Th>

  <td class="THead"><A HREF=<?=$script2?>&sort=cat>

    <?=$this->Msg->show_text('_FLD_CAT')?>

    </A>

    </Th>

  <td class="THead"><A HREF=<?=$script2?>&sort=vac>

    <?=$this->Msg->show_text('_FLD_VACANCY_PROP')?>

    </a>

    </Th>

  <td class="THead"><A HREF=<?=$script2?>&sort=dt>

    <?=$this->Msg->show_text('_FLD_DATE')?>

    </A>

    </Th>

  <td class="THead"><A HREF=<?=$script2?>&sort=status>

    <?=$this->Msg->show_text('_FLD_STATUS')?>

    </A>

    </Th>

  <td class="THead"><A HREF=<?=$script2?>&sort=visible>

    <?=$this->Msg->show_text('_FLD_VISIBLE')?>

    </A>

    </Th>

  <td class="THead"><A HREF=<?=$script2?>&sort=move>

    <?=$this->Msg->show_text('_FLD_MOVE')?>

    </a>

    </Th>

    <?



 $up = 0;

 $down = 0;

 $id = 0;



 $a = $rows;

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

   $this->Form->CheckBox( "id_del[]", $row['id'] );



   echo '<TD>';

   $this->Form->Link( $script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->Msg->show_text('_TXT_EDIT_DATA') );



   echo '<TD align=center>'.stripslashes($this->Spr->GetNameByCod( TblModJobSprPosition, $row['id'] ));



   echo '<TD align=center>'.$row['age'];



   echo '<TD align=center>'.stripslashes($this->Spr->GetNameByCod( TblModJobCategory, $row['cat'] ));



   echo '<TD align=center>';

   switch($row['vac'])

   {

   case '0': echo $this->Msg->show_text('_FLD_VACANCY_NAME');

   				break;

   case '1': echo $this->Msg->show_text('_FLD_POSIRION');

   				break;

   }



   echo '<TD align=center>'.$row['dt'];



   echo '<TD align=center>'.stripslashes($this->Spr->GetNameByCod( TblModJobStatuses, $row['status'] ));



   echo '<TD align=center>';

   switch($row['visible'])

   {

   case '1': echo $this->Msg->show_text('_FLD_HIDDEN');

   				break;



   case '2': echo $this->Msg->show_text('_FLD_VISIBLE');

   				break;

   }



   echo '<TD align=center>';

    if( $up!=0 )

    {

     ?>

     <a href=<?=$script?>&task=up&move=<?=$row['move']?>>

     <?=$this->Form->ButtonUp( $row['id'] );?>

     </a>

    <?

    }



	if( $i!=($rows-1) )

    {

     ?>

     <a href=<?=$script?>&task=down&move=<?=$row['move']?>>

     <?=$this->Form->ButtonDown( $row['id'] );?>

     </a>

     <?

    }



    $up=$row['id'];

    $a=$a-1;





 }

 AdminHTML::TablePartF();

 $this->Form->WriteFooter();

 return true;

} //end of function show







// ================================================================================================

// Function : edit()

// Version : 1.0.0

// Date : 04.02.2005

//

// Parms :

//                 $id   / id of editing record / Void

//                 $mas  / array of form values

// Returns : true,false / Void

// Description : edit/add records in News module

// ================================================================================================

// Programmer : Dmitriy Kerest

// Date : 04.02.2005

// Reason for change : Creation

// Change Request Nbr:

// ================================================================================================



function edit( $id, $mas=NULL )

{



 $script = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&sort='.$this->sort.'&fltr='.$this->fltr;

 $script = $_SERVER['PHP_SELF']."?$script";

$age = 0;



 $Panel = new Panel();

 $ln_sys = new SysLang();



 $fl = NULL;

 if( $mas )

 {

  $fl = 1;

 }



 if( $id!=NULL and ( $mas==NULL ) )

 {

   $q = "SELECT * FROM ".TblModJob." where id='$id'";

   $res = $this->Right->Query( $q, $this->user_id, $this->module );

   if( !$res ) return false;

   $mas = $this->Right->db_FetchAssoc();

 }



  /* Write Form Header */

 $this->Form->WriteHeader( $script );



 $this->Form->IncludeHTMLTextArea();

 if( $id!=NULL ) $txt = $this->Msg->show_text('_TXT_EDIT_DATA');

 else $txt = $this->Msg->show_text('_TXT_ADD_DATA');

 AdminHTML::PanelSubH( $txt );



 //-------- Show Error text for validation fields --------------

  $this->ShowErrBackEnd();

 //-------------------------------------------------------------



 AdminHTML::PanelSimpleH();

?>

    <table width="100%" class="EditTable">

      <tr>

        <td valign="top"><table width="100%" class="EditTable">

            <tr>

              <td valign="top" width="300"><table class="EditTable" align="left" width="100%" border="0">

                  <tr>

                    <td align="right" width="150"><strong><?=$this->Msg->show_text('_FID_ID')?>

                      </strong>:</td>

                    <td><?

				   if( $id!=NULL )

				   {

				   echo $mas['id'];

				   $this->Form->Hidden( 'id', $mas['id'] );

				   }

				   else $this->Form->Hidden( 'id', '' );

				   ?>

                    </td>

                  </tr>

                  <tr>

                    <td align="right"><strong><?=$this->Msg->show_text('_FLD_JOB_DISPLAY')?></strong>

                      :</td>

                    <td><?

					  if( $id!=NULL )

					  {

					  echo $mas["move"];

					  $this->Form->Hidden( 'move', $mas["move"] );

					  }else

					  {

					   $q="select * from ".TblModJob." order by `move` desc";

					   $res1 = mysql_query( $q );

					   $tmp = mysql_fetch_array( $res1 );

					   echo  ($tmp['move']+1);

					   $this->Form->Hidden( 'move', $tmp['move']+1 );

					  }



					$arr[0] = NULL;

					$arr[1] = $this->Msg->show_text('_FLD_HIDDEN');

					$arr[2] = $this->Msg->show_text('_FLD_VISIBLE');





					$arr1[0] = $this->Msg->show_text('_FLD_VACANCY_NAME');

					$arr1[1] = $this->Msg->show_text('_FLD_POSIRION');



					?>

                    </td>

                  </tr>

                  <tr>

                    <td align="right"><strong><?=$this->Msg->show_text('_FLD_DATE')?></strong>

                      :</td>

                    <td><?

				  $date = date("Y-m-d");

				  if($id!=0)

				  {

				  $this->Form->TextBox( 'date',  $mas['dt'], 12 );

				  }

				  else

				  {

				  $this->Form->TextBox( 'date',  $date, 12 );

				  }

				  ?>

                    </td>

                  </tr>

				  <tr>

				   <td align="right"><strong><?=$this->Msg->show_text('_FLD_AGE')?>:</strong></td>

				   <td>

				   <?

				  $date = $this->age;

				  if($id!=0)

				  {

				  $this->Form->TextBox( 'age',  $mas['age'], 20 );

				  }

				  else

				  {

				  $this->Form->TextBox( 'age',  $age, 20 );

				  }

				  ?>

				   </td>

				  </tr>

                </table>

				</td>

              <td valign="top"><table class="EditTable" width="100%">

                  <tr>

                    <td><strong><?=$this->Msg->show_text('_FLD_CAT')?></strong>

                      :</td>

                    <td><?

	 if( $id!=NULL ) $this->Err!=NULL ? $cat=$this->cat : $cat=$mas['cat'];

     else $cat=$this->cat;

     if ( !empty($cat) )

	 {

         //echo '<b>'.$this->Spr->GetNameByCod( TblModJobCategory, $cat ).' ['.$this->Msg->show_text('_FID_ID').' '.$cat.'] </b>';

         echo $this->Spr->ShowInComboBox( TblModJobCategory, 'cat', $cat, 50 );

		 $this->cat = $cat;

         $this->Form->Hidden( 'cat', $this->cat );

     }

          else echo $this->Spr->ShowInComboBox( TblModJobCategory, 'cat', $cat, 50 );



	/*

    if( $id!=NULL or ( $mas!=NULL ) ) $this->Spr->ShowInComboBox( TblModJobCategory, 'cat', $mas['cat'], 0 );

    else {



    $this->Spr->ShowInComboBox( TblModJobCategory, 'cat', $this->fltr, 0 );

    }

	*/

    ?>

                    </td>

                  </tr>

                  <tr>

                    <td><strong><?=$this->Msg->show_text('_FLD_VISIBLE')?>

                      :</strong></td>

                    <td><?

	if( $id!=NULL ) $this->Err!=NULL ? $visible=$this->visible : $visible=$mas['visible'];

            else $visible=$this->visible;

            //$this->Spr->ShowInComboBox( TblModCatalogSprManufac,'id_manufac', $id_manufac, 50 );

			$this->Form->Select( $arr, 'visible', $visible, 50 );?></td>

                  </tr>

                  <tr>

                    <td><strong><?=$this->Msg->show_text('_FLD_VACANCY_PROP')?>

                      :</strong></td>

                    <td><?

	if( $id!=NULL ) $this->Err!=NULL ? $vac=$this->vac : $vac=$mas['vac'];

            else $vac=$this->vac;

            //$this->Spr->ShowInComboBox( TblModCatalogSprManufac,'id_manufac', $id_manufac, 50 );

			//$this->Form->Select( $arr, 'visible', $visible, 50 );

			$this->Form->Select( $arr1, 'vac', $vac, 50 );?></td>

                  </tr>

                  <tr>

                    <td><strong><?=$this->Msg->show_text('_FLD_STATUS')?>

                      :</strong></td>

                    <td><?

    if( $id!=NULL ) $this->Err!=NULL ? $status=$this->status : $status=$mas['status'];

            else $status=$this->status;

            $this->Spr->ShowInComboBox( TblModJobStatuses,'status', $status, 50 );



	/*if( $id!=NULL or ( $mas!=NULL ) ) $this->Spr->ShowInComboBox( TblModJobStatuses, 'status', $mas['status'], 0 );

    else {



    $this->Spr->ShowInComboBox( TblModJobStatuses, 'status', $this->fltr, 0 );

    }*/

    ?>

                    </td>

                  </tr>

                </table></td>

            </tr>

            <tr>

              <td colspan="2"><?

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

        echo "\n <td><b>".$this->Msg->show_text('_FLD_POSIRION').":</b>";

        echo "\n <br>";



        $row = NULL;



        $row = $this->Spr->GetByCod( TblModJobSprPosition, $mas['id'], $lang_id );

        if( $id!=NULL ) $this->Err!=NULL ? $val=$this->position[$lang_id] : $val=$row[$lang_id];

        else $val=$this->position[$lang_id];

        $this->Form->TextBox( 'position['.$lang_id.']', stripslashes($val), 80 );



		/*if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModJobSprPosition, $mas['id'], $lang_id );

        if( $fl ) $this->Form->TextBox( 'position['.$lang_id.']',  stripslashes($mas['position'][$lang_id]), 60 );

        else $this->Form->TextBox( 'position['.$lang_id.']',  stripslashes($row[$lang_id]), 60 );

		*/

        echo "\n <br>";



        echo "\n <tr>";

        echo "\n <td><b>".$this->Msg->show_text('_FLD_EDUCATION').":</b>";

        echo "\n <br>";

		$row = $this->Spr->GetByCod( TblModJobSprEducation, $mas['id'], $lang_id );

        if( $id!=NULL ) $this->Err!=NULL ? $val=$this->education[$lang_id] : $val=$row[$lang_id];

        else $val=$this->education[$lang_id];

        $this->Form->HTMLTextArea( 'education['.$lang_id.']', stripslashes($val), 5, 80 );

        /*if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModJobSprEducation, $mas['id'], $lang_id );

        if( $fl ) $this->Form->HTMLTextArea( 'education['.$lang_id.']',  stripslashes($mas['education'][$lang_id]), 15, 80 );

        else $this->Form->HTMLTextArea( 'education['.$lang_id.']',  stripslashes($row[$lang_id]), 7, 80 );

        */

		echo "\n <br>";



        echo "\n <tr>";

        echo "\n <td><b>".$this->Msg->show_text('_FLD_EXPERIENCE').":</b>";

        echo "\n <br>";

		$row = $this->Spr->GetByCod( TblModJobSprExperience, $mas['id'], $lang_id );

        if( $id!=NULL ) $this->Err!=NULL ? $val=$this->experience[$lang_id] : $val=$row[$lang_id];

        else $val=$this->experience[$lang_id];

        $this->Form->HTMLTextArea( 'experience['.$lang_id.']', stripslashes($val), 10, 80 );

        /*if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModJobSprExperience, $mas['id'], $lang_id );

        if( $fl ) $this->Form->HTMLTextArea( 'experience['.$lang_id.']',  stripslashes($mas['experience'][$lang_id]), 18, 80 );

        else $this->Form->HTMLTextArea( 'experience['.$lang_id.']',  stripslashes($row[$lang_id]), 6, 80 );

        */

		echo "\n <br>";



		echo "\n <tr>";

        echo "\n <td><b>".$this->Msg->show_text('_FLD_CONTACTS').":</b>";

        echo "\n <br>";

		$row = $this->Spr->GetByCod( TblModJobSprContacts, $mas['id'], $lang_id );

        if( $id!=NULL ) $this->Err!=NULL ? $val=$this->contacts[$lang_id] : $val=$row[$lang_id];

        else $val=$this->contacts[$lang_id];

        $this->Form->HTMLTextArea( 'contacts['.$lang_id.']', stripslashes($val), 10, 80 );



        /*if( isset( $mas['id'] ) ) $row = $this->Spr->GetByCod( TblModJobSprExperience, $mas['id'], $lang_id );

        if( $fl ) $this->Form->HTMLTextArea( 'contacts['.$lang_id.']',  stripslashes($mas['contacts'][$lang_id]), 18, 80 );

        else $this->Form->HTMLTextArea( 'contacts['.$lang_id.']',  stripslashes($row[$lang_id]), 5, 80 );

        */

		echo "\n <br>";



        echo   "\n </table>";

     $Panel->WriteItemFooter();

    }

    $Panel->WritePanelFooter();

 $this->Form->WriteSavePanel( $script );
 $this->Form->WriteCancelPanel( $script );
 $this->Form->WritePreviewPanel( 'http://'.$_SERVER['SERVER_NAME']."/modules/mod_job/job.preview.php" );

 $this->Form->WriteFooter();
 AdminHTML::PanelSimpleF();
 ?>
              </td>
            </tr>
          </table></td>
 <?
 /*
        <td valign="top"><?
  $uploaddir = "/images/mod_job/";
  $Uploads = new Uploads( $this->user_id , $this->module , $uploaddir, 200 );
  // Write Simple Panel
  AdminHTML::PanelSimpleH();
  echo '<table border=0 width=100% height=100% align=center class="EditTable"><tr><td valign=top>';
  $Uploads->ShowDir();
  echo '</table>';
  */
  AdminHTML::PanelSimpleF();

 AdminHTML::PanelSubF();
 ?>
        </td>
      </tr>
    </table>
    <?
} //end of function edit



// ================================================================================================

// Function : save()

// Version : 1.0.0

// Date : 02.02.2005

//

// Parms :

//        $id, $cod, $lang_id, $id_category, $subject, $short, $full, $id_relart, $status,

//        $start_date_year, $start_date_month, $start_date_day,$start_date_hour,$start_date_min,

//        $end_date_year, $end_date_month, $end_date_day, $end_date_hour, $end_date_min, $display

// Returns : true,false / Void

// Description : Store data to the table

// ================================================================================================

// Programmer : Andriy Lykhodid

// Date : 04.02.2005

// Reason for change : Creation

// Change Request Nbr:

// ================================================================================================

function save($id)

{



$q = "select * from `".TblModJob."` where `id`='".$id."'";

$res = $this->Right->Query($q, $this->user_id, $this->module);

//echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

if( !$this->Right->result ) return false;

$rows = $this->Right->db_GetNumRows();



if($rows>0)

{

 $q = "update `".TblModJob."` set

 `dt`='".$this->date."',

 `cat`='".$this->cat."',

 `status`='".$this->status."',

 `vac`='".$this->vac."',

 `age`='".$this->age."',

 `visible`='".$this->visible."',

 `move`='".$this->move."' where `id`='".$id."'";



 $res = $this->Right->Query( $q, $this->user_id, $this->module );

 //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

 if( !$res ) return false;

 if( !$this->Right->result ) return false;

}



else

{

 $q = "insert into `".TblModJob."` values (NULL, '".$this->date."', '".$this->cat."', '".$this->status."', '".$this->vac."', '".$this->age."', '".$this->visible."', '".$this->move."');";

 $res = $this->Right->Query( $q, $this->user_id, $this->module );

 //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;

 if( !$this->Right->result) return false;

}



 if ( empty($id) )

 {

  $id = $this->Right->db_GetInsertID();

 }



 //---- Save fields on different languages ----



 $res=$this->Spr->SaveNameArr( $id, $this->position, TblModJobSprPosition );

   if( !$res ) return false;





 $res=$this->Spr->SaveNameArr( $id, $this->education, TblModJobSprEducation );

   if( !$res ) return false;





 $res=$this->Spr->SaveNameArr( $id, $this->experience, TblModJobSprExperience );

   if( !$res ) return false;





 $res=$this->Spr->SaveNameArr( $id, $this->contacts, TblModJobSprContacts );

   if( !$res ) return false;



//echo $q;



return TRUE;



} // end of fucntion save



// ================================================================================================

// Function : del()

// Version : 1.0.0

// Date : 04.02.2005

//

// Parms :

// Returns :      true,false / Void

// Description :  Remove data from the table

// ================================================================================================

// Programmer :  Andriy Lykhodid

// Date : 04.02.2005

// Reason for change : Creation

// Change Request Nbr:

// ================================================================================================



function del( $id_del )

{

    $kol = count( $id_del );

	$db = new DB();

    $del = 0;

    for( $i=0; $i<$kol; $i++ )

    {

     $u = $id_del[$i];



	 $q = "DELETE FROM `".TblModJob."` WHERE `id`='".$u."'";

     $res = $this->Right->Query( $q, $this->user_id, $this->module );

     $res = $this->Spr->DelFromSpr( TblModJobSprPosition, $u );

     $res = $this->Spr->DelFromSpr( TblModJobSprEducation, $u );

     $res = $this->Spr->DelFromSpr( TblModJobSprExperience, $u );

	 $res = $this->Spr->DelFromSpr( TblModJobSprContacts, $u );

	$del++;

	} //end for

  return $del;

}

      // ================================================================================================
// Function : up()
// Version : 1.0.0
// Date : 04.02.2005
//
// Parms :
// Returns :      true,false / Void
// Description :  Up news
// ================================================================================================
// Programmer :  Andriy Lykhodid
// Date : 04.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

function up($move)
{

 $q="select * from ".TblModJob." where `move`='".$move."'";
 //echo $q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;
 $rows = $this->Right->db_GetNumRows();
 $row = $this->Right->db_FetchAssoc();
 $move_down = $row['move'];
 $id_down = $row['id'];
 $q="select * from ".TblModJob." where `move`<'".$move."' order by move desc";
 //echo $q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;
 $rows = $this->Right->db_GetNumRows();
 $row = $this->Right->db_FetchAssoc();
 $move_up = $row['move'];
 $id_up = $row['id'];
 if( $move_down!=0 AND $move_up!=0 )
 {
 $q="update ".TblModJob." set move='".$move_down."' where id='".$id_up."'";
 //echo '<br>'.$q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 $q="update ".TblModJob." set move='".$move_up."' where id='".$id_down."'";
 //echo '<br>'.$q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 }
}



// ================================================================================================

// Function : down()
// Version : 1.0.0
// Date : 04.02.2005
//
// Parms :
// Returns :      true,false / Void
// Description :  Down news
// ================================================================================================
// Programmer :  Andriy Lykhodid
// Date : 04.02.2005
// Reason for change : Creation
// Change Request Nbr:
// ================================================================================================

function down($move)

{
 $q="select * from ".TblModJob." where move='".$move."'";
// echo $q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;
 $rows = $this->Right->db_GetNumRows();
 $row = $this->Right->db_FetchAssoc();
// echo "<br>move_up = ".$row['move'];
 $move_up = $row['move'];
// echo "<br>id_up = ".$row['id'];
 $id_up = $row['id'];
 $q="select * from ".TblModJob." where move > '".$move."' order by move asc";
// echo $q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 if( !$res )return false;
 $rows = $this->Right->db_GetNumRows();
 $row = $this->Right->db_FetchAssoc();
// echo "<br>move_down = ".$move_down = $row['move'];
 $move_down = $row['move'];
 $id_down = $row['id'];
// echo "<br>id_down = ".$id_down = $row['id'];
 if( $move_down!=0 AND $move_up!=0 )
 {
 $q="update ".TblModJob." set move='$move_down' where id='$id_up'";
// echo '<br>'.$q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 $q="update ".TblModJob." set move='$move_up' where id='$id_down'";
// echo '<br>'.$q;
 $res = $this->Right->Query( $q, $this->user_id, $this->module );
 }
}


// ================================================================================================

// Function : CheckContentFields()

// Version : 1.0.0

// Date : 21.03.2006

//

// Parms :        $id - id of the record in the table

// Returns :      true,false / Void

// Description :  Checking all fields for filling and validation

// ================================================================================================

// Programmer :  Igor Trokhymchuk

// Date : 10.01.2006

// Reason for change : Creation

// Change Request Nbr:

// ================================================================================================

function CheckContentFields($id = NULL)

{

$this->Err=NULL;

if (empty( $this->cat )) {

  $this->Err=$this->Err.$this->Msg->show_text('MSG_CATEGORY_EMPTY').'<br>';

 }



if (empty( $this->visible ))

  {

   $this->Err=$this->Err.$this->Msg->show_text('MSG_VISIBILITY_EMPTY').'<br>';

  }



if (empty( $this->status ))

  {

   $this->Err=$this->Err.$this->Msg->show_text('MSG_STATUS_EMPTY').'<br>';

  }



if (empty( $this->position[_LANG_ID] ))

  {

   $this->Err=$this->Err.$this->Msg->show_text('MSG_POSITION_EMPTY').'<br>';

  }



if (empty( $this->education[_LANG_ID] ))

  {

   $this->Err=$this->Err.$this->Msg->show_text('MSG_EDUCATION_EMPTY').'<br>';

  }



if (empty( $this->experience[_LANG_ID] ))

  {

   $this->Err=$this->Err.$this->Msg->show_text('MSG_EXPERIENCE_EMPTY').'<br>';

  }



if (empty( $this->contacts[_LANG_ID] ))

  {

   $this->Err=$this->Err.$this->Msg->show_text('MSG_CONTACTS_EMPTY').'<br>';

  }





        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;

 return $this->Err;

} //end of fuinction CheckContentFields()











// ================================================================================================

// Function : ShowErrBackEnd()

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

function ShowErrBackEnd()

{

 if ($this->Err){

 echo '

 <table border=0 cellspacing=0 cellpadding=0 class="err" align="center">

 <tr><td align="left">'.$this->Err.'</td></tr>

 </table>';

 }

} //end of fuinction ShowErrBackEnd()

} //end on class JobBackend

?>
