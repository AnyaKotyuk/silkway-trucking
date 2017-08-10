<?php
// ================================================================================================
//    System     : CMS
//    Module    : Poll
//    Date       : 11.04.2011
//    Licensed to: Yaroslav Gyryn
//    Purpose    : front-end block for POLLs
// ================================================================================================
if (!defined("SITE_PATH")) define( "SITE_PATH", $_SERVER['DOCUMENT_ROOT'] ); 
include_once( SITE_PATH.'/include/defines.php' );
     
if( !isset ( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if( !isset ( $_REQUEST['cd'] ) ) $cd = null;
else $cd = $_REQUEST['cd'];

if( !isset ( $_REQUEST['alt'] ) ) $alt = null;
else $alt = $_REQUEST['alt'];

if( !isset ( $_REQUEST['answer'] ) ) $answer = null;
else $answer = $_REQUEST['answer'];

$Page = check_init("PageUser","PageUser");
if (isset($Page->Poll))
    $Poll = $Page->Poll;
else
    $Poll = check_init("PollUse","PollUse");
$Poll->cd=$cd;


ob_start();
//echo '$task='.$task;
switch ($task){
    case 'ajax_result':
        $Poll->VotePoll( $cd, $alt, $_SERVER['REMOTE_ADDR'], $answer );            
        $Poll->ShowResult( $cd,true);
        break;
    
    case 'result':
        $Poll->ShowResultPage( $cd, false);
        break;

    case 'arch':
        $Poll->ShowArchive();
        break;
    
    default:

        $Poll->ShowAllResult();
        break;
}
$Page->content = ob_get_clean();
$Page->out();

?>