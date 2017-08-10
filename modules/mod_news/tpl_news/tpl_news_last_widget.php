<div class="news padd">

    <h1>LAST NEWS</h1>
    <div id="carousel-news" class="container text-center"  data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carousel-news" data-slide-to="0" class="active"></li>
            <li data-target="#carousel-news" data-slide-to="1"></li>
            <li data-target="#carousel-news" data-slide-to="2"></li>
        </ol>
        <div class="row ">
            <?
for( $i=0; $i<$rows; $i++ )
{
    $row = $arr[$i];
    $name = $row['name'];
    $link = $row['link'];
    $short = $row['short'];
    ?>

    <div class="col-md-4 item <? if($i == 0) echo 'active'; ?>">
        <div class="date">
            <span><?=$row['date']['day'];?></span>
            <span><?=$row['date']['month'];?></span>
            <a href="<?=$link;?>">< Read</a>
        </div>

        <div class="info">
            <h5><?=$name;?></h5>
            <div style="border-top: solid 1px #315086"><?=$short;?></div>
        </div>
    </div>



<!--        --><?//if(!empty($row['img'])){?>
<!--            <div class="news-last-img">-->
<!--                <a href="--><?//=$link;?><!--" title="--><?//=$name?><!--">-->
<!--                    <img src="--><?//=$row['src'];?><!--" alt="--><?//=$row['img_alt']?><!--"  title="--><?//=$row['img_title']?><!--"/>-->
<!--                </a>-->
<!--            </div>-->
<!--        --><?//}
        ?>
<!--        --><?//if(!empty($row['date'])){?><!--<div class="news-date">--><?//=$row['date'];?><!--</div>--><?//}?>
<!--        <div class="news-last-name">-->
<!--            <a href="--><?//=$link;?><!--" title="--><?//=$name?><!--">--><?//=$name?><!--</a>-->
<!--        </div>-->

<?
}
?>
        <!--<a href="<?/*=_LINK*/?>news/" title="<?/*=$name_datail*/?>"><?/*=$name_datail;*/?></a>-->
    </div>
        </div>
    </div>
<?
//echo $pages;