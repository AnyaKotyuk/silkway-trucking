<?
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/include/defines.php' );

 if( !isset( $_REQUEST['id'] ) ) $id = NULL;
 else $id = $_REQUEST['id'];

?>
<html>
 <head>
 <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
 <LINK href="include/prod.css" type="text/css" rel="stylesheet">
 <LINK href="include/forus.css" type="text/css" rel="stylesheet">
 <title>Position available</title>
 </head>
 <body>
<?
 JobOpPrintVersion( $id );
?>
 </body>
</html>

<?
 function JobOpPrintVersion( $id = NULL )
 {
  if(!db_connect())
  {
   echo '<p><strong>No connect. Please, try again!';
   return 0;
  }

  $q = "select * from ".TableJobOpp." where id='$id'";
  $res = mysql_query( $q );

  echo '<table border=0 valign=top align=center>';
  echo '<tr><td class="User" align=center>';
  echo 'Position available';
  if( $res )
  {
    $m = mysql_fetch_array( $res );
    echo '<tr><td valign=top>';
    echo '<table width="560" align="center" cellpadding="5" cellspacing="1" class="user_background">';
    echo '<tr><td width=150 class="User_detail">Position available<td CLASS="tr_2_user">'.$m['position'];
    echo '<tr><td class="User_detail">Education requirements<td CLASS="tr_2_user"> '.$m['education'];
    echo '<tr><td class="User_detail">Experience<td CLASS="tr_2_user">'.$m['experience'];
    echo '<tr><td class="User_detail">Description<td CLASS="tr_2_user">'.$m['description'];
    echo '<tr><td class="User_detail">Contact Information<td CLASS="tr_2_user">'.$m['contactinf'];
    echo '<tr><td class="tr_2_user" colspan=2>'.$m['date'];
    echo '<tr><td class="tr_2_user" colspan=2 align=center><center><a class="prod" href="javascript:void(0);" onClick="window.close()">Close</a> <a class="prod" href="javascript:void(0);" onClick="window.print(); return false">Print</a></center>';
    echo '</table>';
  }
  echo '</table>';

 }
?>
