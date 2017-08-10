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