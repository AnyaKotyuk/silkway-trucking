<?
    include_once( $_SERVER['DOCUMENT_ROOT'].'/include/defines.php' );
    include_once( SITE_PATH.'/modules/mod_dictionary/defines.php' );

    $Page = check_init('PageUser', 'PageUser');
    $Dictionary = check_init('Dictionary', 'Dictionary');

    if( !isset( $_REQUEST['task'] ) ) $Dictionary->task = '';
    else $Dictionary->task = $_REQUEST['task'];

    if( !isset( $_REQUEST['letter_cod'] ) ) $Dictionary->cur_word = '';
    else $Dictionary->cur_word = $_REQUEST['letter_cod'];

    if( !isset( $_REQUEST['termin_cod'] ) ) $Dictionary->termin_cod = '';
    else $Dictionary->termin_cod = $_REQUEST['termin_cod'];

    if(isset ($Page->FrontendPages)) $FrontendPages = &$Page->FrontendPages;
    else $Page->FrontendPages = check_init('FrontendPages', 'FrontendPages');

    $Page->FrontendPages->lang_id = _LANG_ID;
    $Dictionary->lang_id =  _LANG_ID;
    $Page->FrontendPages->page = PAGE_DICTIONARY;

    $Dictionary->SetMetaData($Page->FrontendPages->page);

    if ( empty($Dictionary->title) ) $Title = $Page->multi["TXT_DICTIONARY"];
    else $Title = $Dictionary->title;
    if ( empty($Dictionary->description) ) $Description =  $Page->multi["TXT_DICTIONARY"];
    else $Description = $Dictionary->description;
    if ( empty($Dictionary->keywords) ) $Keywords = $Page->multi["TXT_DICTIONARY"];
    else $Keywords = $Dictionary->keywords;

    $Page->SetTitle( $Title );
    $Page->SetDescription( $Description );
    $Page->SetKeywords( $Keywords );

ob_start();
    $title_content = $Page->multi["TXT_DICTIONARY"];
    $Page->Form->WriteContentHeader($title_content, 'icoDictionary',false);
    switch( $Dictionary->task ){
        case 'cur_word':
            $Dictionary->ShowWords($Dictionary->alphabet[$Dictionary->cur_word]);
            break;

        case 'show_termin':
            $Dictionary->ShowTermin($Dictionary->termin_cod);
            break;

        default:
	        $Dictionary->ShowWords();
            break;
    }
    $Page->Form->WriteContentFooter();
$Page->content = ob_get_clean();
$Page->out();
?>
