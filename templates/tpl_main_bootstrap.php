<?
/**
 * @uses: /include/classes/PageUser.class.php
 */

?>
<!DOCTYPE html>
<html lang="<?=$PageUser->LangShortName;?>">
<head>
    <?
    if (defined("MOD_CATALOG") AND MOD_CATALOG) {
        if (!isset($PageUser->Catalog->isContent))
            $PageUser->Catalog->isContent = $PageUser->Catalog->IsContent($PageUser->Catalog->id_cat);
        if ($PageUser->FrontendPages->page == PAGE_CATALOG && $PageUser->Catalog->isContent > 0) {
            $PageUser->Catalog->checHash();
        }
    }
    ?>
    <meta charset="<?=$PageUser->page_encode;?>" />
    <title><?=htmlspecialchars($PageUser->title);?></title>
    <meta name="Description" content="<? if( $PageUser->Description ) echo htmlspecialchars($PageUser->Description);else echo '';?>" />
    <meta name="Keywords" content="<? if( $PageUser->Keywords ) echo htmlspecialchars($PageUser->Keywords);else echo '';?>" />
    <meta name="SKYPE_TOOLBAR" content="SKYPE_TOOLBAR_PARSER_COMPATIBLE" />
    <?

    //Если это страница каталога с фмльтрами, то для гугла указывем дополнительные параметры
    //if( strstr($_SERVER["QUERY_STRING"], "parcod")){
    //более того, проверяем, есть ли любые дополнительные параметры в УРЛ,
    //и если есть, то будем закрыать от индексации и прописыать каноникал.
    if( strstr($_SERVER['REQUEST_URI'], '?')){
        //закрываем от индексации страницы результатов работы фильтров каталога товаров
        ?>
        <meta name="robots" content="noindex, nofollow, noarchive"/>
        <?

        if(!isset($_SERVER['REDIRECT_URL'])) {
            $link = substr($_SERVER['REQUEST_URI'], 0, strrpos($_SERVER['REQUEST_URI'], '/')+1);
        }
        else{ $link = $_SERVER['REDIRECT_URL']; }
        $canonical = 'http://'.NAME_SERVER.$link;
        //echo '<br>$canonical='.$canonical;
        //Добавление этой ссылки и атрибута позволяет владельцам сайтов определять наборы идентичного содержания и сообщать Google:
        //"Из всех страниц с идентичным содержанием эта является наиболее полезной.
        //Установите для нее наивысший приоритет в результатах поиска."
        ?>
        <link rel="canonical" href="<?=$canonical;?>"/>
    <?
    }
    ?>

    <link rel="icon" type="image/vnd.microsoft.icon"  href="/images/design/favicon.ico" />
    <link rel="SHORTCUT ICON" href="/images/design/favicon.ico" />


    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <!-- Bootstrap -->
<!--    <link href="/include/bootstrap-3.3.5-dist/css/bootstrap.min.css" type="text/css" rel="stylesheet" />-->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<!--    <link href="/include/css/main_bootstrap.css" type="text/css" rel="stylesheet" />-->

    <!--[if IE ]>
    <link href="/include/css/browsers/ie.css" rel="stylesheet" type="text/css" media="screen" />
    <![endif]-->
    <!--[if lt IE 8]>
    <link href="/include/css/browsers/ie7.css" rel="stylesheet" type="text/css" media="screen" />
    <![endif]-->

    <script type="" src="http://code.jquery.com/jquery-latest.js"></script>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
    <script type="text/javascript" src='http://<?=NAME_SERVER."/sys/js/jQuery/jquery.form.js";?>'></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
<!--    <script src="/include/bootstrap-3.3.5-dist/js/bootstrap.min.js"></script>-->

    <script src="/include/js/cms_lib/lib.js" type="text/javascript"></script>

    <!-- optionally include helper plugins -->
    <script type="text/javascript" language="javascript" src="/include/js/carouFredSel/jquery.mousewheel.min.js"></script>
    <script type="text/javascript" language="javascript" src="/include/js/carouFredSel/jquery.touchSwipe.min.js"></script>
    <script type="text/javascript" language="javascript" src="/include/js/gallery.js"></script>
    <script type="text/javascript" language="javascript" src="/include/js/carouFredSel/jquery.carouFredSel-6.2.1-packed.js"></script>

    <!-- Enable HTML5 tags for old browsers -->
    <script type="text/javascript" src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>

    <!-- Старт валидации -->
    <script type="text/javascript" src="/include/js/validator/js/jquery.validationEngine.js"></script>
    <script type="text/javascript" src="/include/js/validator/js/languages/jquery.validationEngine-ru.js"></script>
    <script type="text/javascript" src="/include/js/validator/js/languages/jquery.validationEngine-en.js"></script>
    <link href="/include/js/validator/css/validationEngine.jquery.css" type="text/css" rel="stylesheet" media="screen"/>
    <!-- Конец валидации -->

    <?if (defined("MOD_GALLERY") AND MOD_GALLERY){?>
        <!--Photo Gallery-->
        <link rel="stylesheet" type="text/css" href="/include/css/jquery.jcarousel.css" media="screen" />
        <link rel="stylesheet" type="text/css" href="/include/css/jcarousel.gallery.css" media="screen" />
        <script type="text/javascript" src="/include/js/jquery.jcarousel.pack.js"></script>
        <!--End Photo Gallery-->
    <?}?>

    <?if (defined("MOD_VIDEO") AND MOD_VIDEO){?>
        <script type="text/javascript" src="/modules/mod_video/player/flowplayer-3.2.6.min.js"></script>
    <?}?>

    <!-- Комментарий вконтакте В тег <head> на странице Вашего сайта необходимо добавить следующий код: -->
    <!--<script src="http://userapi.com/js/api/openapi.js" type="text/javascript"></script>-->
    <script src="/include/js/cms_lib/popup.js" type="text/javascript"></script>
    <script src="/include/js/cms_lib/comments.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="/include/css/comments.css" media="screen" />

    <?if (defined("MOD_PUBLIC") AND MOD_PUBLIC){?>
        <!-- для публичних обявлений -->
        <script src="/include/js/public.js" type="text/javascript"></script>
    <?}?>

    <?if (defined("MOD_POLL") AND MOD_POLL){?>
        <!-- для публичних обявлений -->
        <script src="/include/js/poll.js" type="text/javascript"></script>
    <?}?>

    <?if (defined("MOD_ORDER") AND MOD_ORDER){?>
        <!-- для публичних обявлений -->
        <script src="/include/js/cms_lib/order.js" type="text/javascript"></script>
    <?}?>

    <!-- увеличалка -->
    <script src="/include/js/fancybox/jquery.fancybox.js" type="text/javascript"></script>
    <link href="/include/js/fancybox/jquery.fancybox.css" type="text/css" rel="stylesheet" media="screen"/>

    <script type="text/javascript">
        var _JS_LANG_ID = <?=_LANG_ID?>;
    </script>

    <script type="text/javascript" src="/include/js/jquery-ui-1.10.0/jquery-ui-1.10.0.custom.js"></script>
    <link href="/include/js/jquery-ui-1.10.0/jquery-ui-1.10.0.custom.css" type="text/css" rel="stylesheet" media="screen"/>
    <script src="/include/js/compare.js" type="text/javascript"></script>
    <script src="/include/js/filter.js" type="text/javascript"></script>
    <?

    //=== IMPORTANT!!! Do not delete this block! ===
    //Insert codes from Site Settings
    echo $PageUser->Settings['site_codes_head']."\n";
    //==============================================
    ?>
    <link rel="stylesheet" href="/include/css/bootstrap.min.css">
    <link rel="stylesheet" href="/include/css/style.css">
    <script type="text/javascript" src="/include/js/main.js"></script>
    <script src="/include/js/swipe.js"></script>
    <script src="/include/js/bootstrap.min.js"></script>
<!--    <script src="/include/js/less.min.js"></script>-->

    <!-- Optional theme -->
    <!-- Latest compiled and minified JavaScript -->
    <link rel="stylesheet" href="/include/css/bootstrap-theme.min.css">

</head>

<body>
<div class="bg-wt"></div>
<?
//=== IMPORTANT!!! Do not delete this block! ===
//Insert codes from Site Settings
echo $PageUser->Settings['site_codes_body_start']."\n";
//==============================================
?>

<!--[if lt IE 8]>
<div style=" margin:10px auto 0px auto; padding:20px; background:#DDDDDD; border:1px solid gray; width:980px; font-size:14px;">
    Уважаемый Пользователь!</br>
    Вы используете <span class="red">устаревший WEB-браузер</span>.</br>
    Предлагаем Вам установить и использовать последние версии WEB-браузеров, например:<br/>
    <ul>
        <li>Google Chrome <a href="https://www.google.com/chrome">https://www.google.com/chrome</a></li>
        <li>Mozilla Firefox <a href="http://www.mozilla.org/ru/firefox/new/">http://www.mozilla.org/ru/firefox/new/</a></li>
        <li>Opera <a href="http://www.opera.com/download/">http://www.opera.com/download/</a></li>
    </ul>
    Последние версии WEB-браузеров доступны для установки на сайтах разработчиков и содержат улучшенные свойства безопасности, повышенную скорость работы, меньшее количество ошибок. Эти простые действия помогут Вам максимально использовать функциональность сайта, избежать ошибок в работе, повысить уровень безопасности.
</div>
<![endif]-->
<!---->
<!--<header class="container">-->
<!---->
<!--    <div class="row">-->
<!--        <div class="col-md-4 header-slogan">-->
<!--            <a class="navbar-brand" href="--><?php //echo _LINK;?><!--" title=""><img src="/images/cmsLogo.png" alt="" title="" ></a>-->
<!--            <strong>--><?//=$PageUser->FrontendPages->getNameSpecContentByCod(2);?><!--</strong>-->
<!--            --><?//=$PageUser->FrontendPages->getSpecContentByCod(2, false);?>
<!--        </div>-->
<!--        <div class="col-md-4 header-tel">-->
<!--            <strong>--><?//=$PageUser->FrontendPages->getNameSpecContentByCod(1);?><!--</strong>-->
<!--            --><?//=$PageUser->FrontendPages->getSpecContentByCod(1, false);?>
<!--        </div>-->
<!--<!--        <div class="col-md-4">-->
<!--<!--            <div>-->
<!--<!--                --><?php ////echo $PageUser->Lang->WriteLangPanelShort($PageUser->getMultiUrls());?>
<!--<!--            </div>-->
<!--<!--           -->
<!--<!--        </div>-->
<!--    </div>-->
<!---->
<!--    <div class="navbar navbar-default" role="navigation">-->
<!--        <div class="container">-->
<!--<!--            <div class="navbar-header">-->
<!--<!--                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">-->
<!--<!--                    <span class="sr-only">Toggle navigation</span>-->
<!--<!--                    <span class="icon-bar"></span>-->
<!--<!--                    <span class="icon-bar"></span>-->
<!--<!--                    <span class="icon-bar"></span>-->
<!--<!--                </button>-->
<!--<!--            </div>-->
<!--            <div class="navbar-collapse collapse">-->
<!--                --><?php //$PageUser->FrontendPages->ShowHorisontalMenu();?>
<!--            </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</header> <!-- /container -->
<header>
    <div class="container ">
        <? if (defined("MOD_CATALOG") AND MOD_CATALOG) { ?>
            <!-- SEO for Internet Magazine-->
            <? $upSEOMsg = $PageUser->Catalog->ShowHeaderSEO() ?>
            <div class="col-md-12 header-seo"><?php echo $upSEOMsg; ?></div>
            <!-- End SEO for Internet Magazine-->
        <? } ?>
        <div class="row ">
            <div class="col-md-3 logo"><a href="/"><img src="/images/design/logo.png"></a></div>
            <div class="col-md-9 menu">
                <?php $PageUser->FrontendPages->ShowHorisontalMenu();?>
<!--                <div class=" item"><a href="#"><span>THE COMPANY</span></a></div>-->
<!--                <div class=" item"><a href="#"><span>OUR MISSION</span></a></div>-->
<!--                <div class=" item"><a href="#"><span>OUR SERVICES</span></a></div>-->
<!--                <div class=" item"><a href="#"><span>PARTNERS</span></a></div>-->
<!--                <div class=" item"><a href="#"><span>NEWS</span></a></div>-->
<!--                <div class=" item"><a href="#"><span>CONTACT</span></a></div>-->
            </div>
            <button type="button" class="navbar-toggle menu-close" data-toggle="collapse" data-target=".navbar-collapse">
                <span><img src="/images/design/menu-toggle.png"></span>

            </button>

        </div>
    </div>
</header>
    <?php
    echo $contentHtml;
    ?>
    <footer>
        <?php
        $PageUser->FrontendPages->ShowFooterMenu(); ?>
    </footer>
<?
//=== IMPORTANT!!! Do not delete this block! ===
//Insert codes from Site Settings
echo $PageUser->Settings['site_codes_body_end']."\n";
//==============================================
?>
</body>
</html>
<?
if( defined("MAKE_DEBUG") AND MAKE_DEBUG==1 ){
    /*
    $PageUser->time_end = $PageUser->getmicrotime();
    ?><div style="font-size:9px; color:#797979;"><?
    printf ("<br/>TIME:%2.3f", $PageUser->time_end - $PageUser->time_start);
    if( isset($_SESSION['cnt_db_queries'])) echo '<br/>QUERIES: '.$_SESSION['cnt_db_queries'];
    ?></div><?
    */
}
?>
