<div class="backContainer">
    <div class="dateArticles left">
        <?= $full['dttm']; ?> - <a href="<?= $full['linkCat']; ?>"><?= $full['catName']; ?></a>
    </div>
    <a class="btnBack right" href="javascript:window.history.go(-1);">← <?= $multi['MOD_NEWS_BACK']; ?></a>
    <div style="overflow: hidden;">
    <div class="left"><? 
        if(!empty($arr_img[0]['image_big'])): ?>
        <div class="article-full-big-img" id="ArticleBigImg">
            <a href="<?=$arr_img[0]['image_origin']?>">
                <img src="<?=$arr_img[0]['image_big']?>" alt='' title=""/>
            </a>
        </div>
        <?$count = count($arr_img);
        if($count>1){
            ?><div style="overflow: hidden;"><?
            for($i=0;$i<$count;$i++){
                if($i%3==0 && $i>0){?></div><div style="overflow: hidden;"><?}
                $row = $arr_img[$i];
                $img_small = $row['image_small'];
                $img_big = $row['image_big'];
                $img_org = $row['image_origin'];
                ?><div class="article-full-small-img" onclick="chengBigImg('<?=$img_big?>','<?=$img_org?>');">
                    <div class="article-full-small-img-table">
                        <img src="<?=$img_small?>" alt="" title="" />
                    </div>
                </div><?
            }
            ?></div><?
        }  endif ?>
    </div>
    <script type="text/javascript">
        $('#ArticleBigImg a').fancybox();
        function chengBigImg(img,img_org){
            var content = '<a href="'+img_org+'"><img src="'+img+'" alt="" title=""/></a>';
            $('#ArticleBigImg').html(content);
        }
    </script>
</div>
<h2><?= $full['name']; ?></h2>

<div class="text"><?= $full['full_news']; ?></div>