<div class="atricles-by-pages-box">
    <?php
        if (count($articles) == 0):
    ?>
            <div class="err"><?= $this->multi['MSG_NO_ARTICLES']; ?></div>
    <?php
        return;
        endif;
    ?>

    <?php
    foreach ($articles as $article){
        //echo View::factory('/modules/mod_article/templates/tpl_article_by_pages_single.php')
        //    ->bind('multi',$multi)
        //    ->bind('article',$article);
        ?>
        <div class="item">
            <div class="image">
                <img src='<?=$article['image']?>' atl='' title=""/>
            </div>
            <div class="data">
                    <div class="dateArticles"><?= $article['dttm']; ?> - <a href="<?= $article['linkCat']; ?>"><?= $article['cat']; ?></a></div>
                    <a class="name" href="<?= $article['link']; ?>"><?= $article['name']; ?></a>
                    <div class="short"><?= $article['short']; ?></div>
                    <a class="detail" href="<?= $article['link']; ; ?>"><?= $multi['TXT_DETAILS']; ?>→</a>
            </div>
        </div>
        <?
    }
    ?>

</div>



<?php if (!empty($pages)): ?>
<div class="page-navi-class">
    <?php echo $pages; ?>
</div>
<?php endif; ?>