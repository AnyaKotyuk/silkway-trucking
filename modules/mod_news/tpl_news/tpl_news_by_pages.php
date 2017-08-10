

<div class="container text-center news news-list padd">
        <h1>NEWS</h1>
        <div class=" row news-list">

            <?

//            var_dump($arr);
            for($i=0;$i<$rows;$i++){?>


<!--                --><?// if($i%3 == 0 || $i == 0) echo  '<div class="row">'; ?>
                <?
                $row = $arr[$i];
                $name = $row['name'];
                $link = $row['link'];
//                var_dump($link);
                $short = $row['short'];
//                ?>
            <div class="col-md-4 news-item">
<!--                --><?//if(!empty($row['img'])){?>
<!--                    <div class="news-img-fon">-->
<!--                        --><?//if(!empty($link) && !empty($row['src'])){?>
<!--                            <div class="news-img">-->
<!--                                <a href="--><?//=$link?><!--" title="--><?//=$name?><!--">-->
<!--                                    <img src="--><?//=$row['src']?><!--" alt="--><?//=$row['img_alt']?><!--" title="--><?//=$row['img_title']?><!--" />-->
<!--                                </a>-->
<!--                            </div>-->
<!--                        --><?//}else{?>
<!--                            --><?//if(!empty($row['src'])){?>
<!--                                <div class="news-img">-->
<!--                                    <img src="--><?//=$row['src']?><!--" alt="--><?//=$row['img_alt']?><!--" title="--><?//=$row['img_title']?><!--" />-->
<!--                                </div>-->
<!--                            --><?//}?>
<!--                        --><?//
//                        }?>
<!--                    </div>-->
<!--                --><?//}?>
<!--                <div class="news-text">-->
<!--                    --><?//if(!empty($row['date'])){?><!--<div class="news-date">--><?//=$data?><!-- --><?//=$row['date']?><!--</div>--><?//}?>
                    <?if(!empty($row['date'])){?>
                        <div class="date">
                            <span><?=$row['date']['day']?></span>
                            <span><?=$row['date']['month']?></span>
                            <?if(!empty($link)){?>
                                <a href="<?=$link?>" title="<?=$name?>">< Read</a>
                            <?}?>
                        </div>
                    <?}?>
<!--                    <?//if(!empty($row['Name_cat'])){?><!--<div class="news-cat"><a href="--><?//=$row['link_cat']?><!--" title="--><?//=$row['Name_cat']?><!--">--><?//=$row['Name_cat']?><!--</a></div>--><?//}?>

                <div class="info">
                    <h5><?=$name;?></h5>
                    <div style="border-top: solid 1px #315086"><?=$short;?></div>
                </div>
<!--                </div>-->
                </div><?
//             if($i%3 == 2 || (($rows - $i) < 3 && $rows - $i == 1)) echo  '</div>';
            }
            if(!empty($pages))
                echo $pages;
            ?>


            </div>

        </div>
    </div>




