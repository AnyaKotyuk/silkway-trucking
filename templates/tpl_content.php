<?php
/**
 * @uses: /include/classes/PageUser.class.php
 */
if(!is_ajax){?>
<!--<div class="body-right">-->
<!--    --><?//if (defined("MOD_CATALOG") AND MOD_CATALOG) {?>
<!--    <div class="catalog-filter" id="showFilterRes">-->
<!--        <div class="hide-elem" id="showFilterReload"></div>-->
<!--        <div id="showFilterHtml" class="show-filter-html">--><?//
//    $PageUser->Catalog->showFilter();
//            ?><!--</div></div>--><?//
//    }
//    if (defined("MOD_SEARCH") AND MOD_SEARCH)$PageUser->Search->formSearchSmall();

//    if($PageUser->FrontendPages->page>0){
//        ?>
<!--        <menu>-->
<!--            --><?//$PageUser->FrontendPages->ShowVerticalMenu($PageUser->FrontendPages->page, 2);?>
<!--        </menu>-->
<!--        --><?//
//    }
//    if (defined("MOD_ORDER") AND MOD_ORDER){
//        ?>
<!--        <div id="cart" class="cart">-->
<!--            --><?//
//            $PageUser->Order->Cart();
//            ?>
<!--        </div>-->
<!--        --><?//
//    }
//    if (defined("MOD_USER") AND MOD_USER)$PageUser->Logon->LoginForm();
//    if (defined("MOD_NEWS") AND MOD_NEWS)$PageUser->News->showNewsLastColumn(3);
//    if (defined("MOD_ARTICLE") AND MOD_ARTICLE)$PageUser->Article->showLastArticlesColumn(5);
//    if (defined("MOD_VIDEO") AND MOD_VIDEO)$PageUser->Video->VideoLast();  // Відео
//    if (defined("MOD_POLL") AND MOD_POLL)$PageUser->Poll->ShowPolls();
//    if (defined("MOD_GALLERY") AND MOD_GALLERY)$PageUser->Gallery->GalleryLast();  // Фото
//    if (defined("MOD_ASKED") AND MOD_ASKED)$PageUser->Asked->Category();   // Питання / відповіді

//    ?>
<!--</div>-->
<div class="body-center">

<!--    <div class="hide-elem" id="reloadOrder"></div>-->
<!--    <div id="my_d_basket" class="my_d_basket">-->
    <?}?>
        <div class="<?=$showContent2Box?>">
<!--            --><?//
//            if(!empty($h1)):?>
<!--                <h1>--><?php //echo $h1; ?><!--</h1>-->
<!--            --><?php //endif;?>

            <?php
            if(!empty($breadcrumb)):?>
                <div class="path breadcrumb"><div class="container"><?= $breadcrumb; ?></div></div>
            <?php endif; ?>

            <?php
            echo $content;

            ?>
        </div>
<?if(!is_ajax){?>
    </div>
</div><?}
