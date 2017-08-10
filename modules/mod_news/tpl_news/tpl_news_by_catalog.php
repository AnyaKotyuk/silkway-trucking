<?php
for($i=0;$i<$rows;$i++){
    $row = $arr[$i];
    $name = $row['name'];
    $link = $row['link'];
    //var_dump($row);
    ?><div class="item-news-for-catalog">
    <?if(!empty($row['img']) && !empty($row['src'])){?>
    <div class="img-news-for-catalog">
        <?if(!empty($link)){?>
            <a href="<?=$link?>" title="<?=$name?>">
                <img src="<?=$row['src']?>" alt="<?=$row['img_alt']?>" title="<?=$row['img_title']?>" />
            </a>
        <?}?>
    </div>
    <?}?>
    <div class="news-name-for-catalog">
        <?if(!empty($link)){?>
            <a href="<?=$link?>" title="<?=$name?>"><?=$name?></a>
        <?}else{echo $name;}?>
    </div>
    </div><?
}?>