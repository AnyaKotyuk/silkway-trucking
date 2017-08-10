<?
// ================================================================================================
//    System     : CMS
//    Module     : Poll
//    Date       : 17.02.2011
//    Licensed to: Yaroslav Gyryn
//    Purpose    : Front-end block for POLLs
// ================================================================================================
if(!defined("SITE_PATH")) define("SITE_PATH", $_SERVER['DOCUMENT_ROOT']);
include_once( SITE_PATH.'/modules/mod_poll/poll.defines.php' );

$Page = check_init("PageUser","PageUser");
if(!is_ajax){
    $Page->Set_404();
}
if( !isset( $_REQUEST['cd'] ) ) $cd = NULL;
else $cd = $_REQUEST['cd'];

if( !isset ( $_REQUEST['task'] ) ) $task = 'show';
else $task = $_REQUEST['task'];

if(!isset($_REQUEST['module'])) $module = NULL;
else $module = $_REQUEST['module'];

if(!isset($_REQUEST['show_in'])) $show_in = NULL;
else $show_in = $_REQUEST['show_in'];

if( !isset ( $_REQUEST['alt'] ) ) $alt = NULL;
else $alt = $_REQUEST['alt'];

if( !isset ( $_REQUEST['answer'] ) ) $answer = NULL;
else $answer = $_REQUEST['answer'];

if( !isset ( $_POST['answer'] ) ) $answer = NULL;
else $answer = $_REQUEST['answer'];

if( !isset ( $_POST['alt'] ) )$alt = NULL;
else $alt = $_POST['alt'];

$PollSeo = new PollSEO;
$PollUse = new PollUse();

$title = NULL;
$Description = NULL;
$Keywords = NULL;
$title = $PollUse->multi['FLD_TITLE'];
$Description = $PollUse->Msg->show_text('_TXT_META_DESCRIPTION');// $PollUse->multi['_DESCRIPTION'];
$Keywords = $PollUse->multi['_TXT_META_KEYWORDS'];

if( $cd ){
    $T = trim( $PollSeo->GetTitle( $cd ) );
    $D = trim( $PollSeo->GetDescription( $cd ) );
    $K = trim( $PollSeo->GetKeywords( $cd ) );
    if( $T != '' ) $title = $T.' | '.$title;
    if( $D != '' ) $Description = $D.'. '.$Description;
    if( $K != '' ) $Keywords = $K.', '.$Keywords;
}

$PollUse->alt = $alt;
$PollUse->module = $module;
$PollUse->show_in = $show_in;
ob_start();
switch( $task ){
    case 'show':    $PollUse->ShowPoll();
                    break;

    case 'vote':    //setcookie('vote', "", time() - 60 * 60 * 24 * 31, '/');
                    //setcookie('vote', $cd, time() + 60 * 60 * 24 * 31, '/');

                    $PollUse->VotePoll( $cd, $alt, $_SERVER['REMOTE_ADDR'], $answer );
                    head_( $title, $Description, $Keywords );
                    $PollUse->ShowResult( $cd);

                    footer_();

                    break;
    case 'result':
                     head_( $title, $Description, $Keywords );
                     $PollUse->ShowResult( $cd );
                     footer_();
                     break;

    case 'answer':
                    head_( $title, $Description, $Keywords );
//                    echo '$cd='.$cd;
//                    var_dump($_REQUEST);
                    $PollUse->ShowAnswer( $cd );
                    footer_();
                    break;

    default:
                    $PollUse->ShowPoll();

                    break;
} //--- end switch
$Page->content = ob_get_clean();
$Page->out();


// ================================================================================================
// Function : head_
// Date :     26.02.2011
// Parms :    $title
// Returns :  true,false / Void
// Description : Show Head Of Page
// Programmer : Yaroslav Gyryn
// ================================================================================================
  function head_( $title, $Description, $Keywords )
  {
    ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" >
    <html>
    <head>
        <title><? if( $title ) echo $title; else echo _TITLE;?></title>
        <meta name="Description" content="<? if( $Description ) echo $Description;
           else echo _DESCRIPTION;
        ?>">
        <meta name="Keywords" content="<? if( $Keywords ) echo $Keywords;
          else echo _KEYWORDS;
        ?>">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <?/*<link href="http://<?=NAME_SERVER?>/iamges/design/style.css" rel="stylesheet" type="text/css">*/?>
        <style>
             body {background: #F7FCFF;}
             .question {font-weight: bold; color:#037dcd; font-size: 14px; padding: 10px 0px;}
             a, h3, td  { color: #2E5C9A; font-size: 12px;}
             table { background-color: #FFF;  border: 1px solid #c7d9f0;  border-collapse: collapse;}
             tr, td { background-color :#FFF;  border: 1px dotted #c7d9f0; padding: 2px;}
        </style>
    </head>
    <body>
    <?
  } //--- end of function head()


// ================================================================================================
// Function : footer_
// Version :  1.0.0
// Date :     26.11.2011
// Returns :  true,false / Void
// Description : Show Footer Of Page
// Programmer : Yaroslav Gyryn
// ================================================================================================
  function footer_( $login=NULL, $pass=NULL )
  {
    ?>
    </body>
    </html>
    <?
  } //--- end of function

?>