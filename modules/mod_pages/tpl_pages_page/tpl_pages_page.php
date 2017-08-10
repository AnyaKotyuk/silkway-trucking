<?// var_dump($services);?>

    <div class="dynamic">


    <? if($page_data['img_filename']){ ?>
        <div class="jumbotron ">
            <div class="row">
                <div class="col-md-6 d-img"><?=$page_data['img']?></div>
                <div class="col-md-5">
                    <h1><?=$page_data['pname'];?></h1>
                    <div class="full <? if($services) echo 'full-s'; ?>">
                        <?=$content;?>
                    </div>
                </div>
            </div>
        </div>
    <?} else { ?>
        <div class="container ">
            <div class="row">
                <div class="col-md-12">
                    <h1><?=$page_data['pname'];?></h1>
                    <div class="full <? if($services) echo 'full-s'; ?>"><?= $content ?></div>

                    <? if (!empty($sitemap)) { ?>
                        <?= $sitemap; ?>
                    <? } else { ?>

                        <? if (!empty($sublevels)): ?>
                            <div class="pages-sublevels">
                                <?= $sublevels ?>
                            </div>
                        <? endif ?>

                        <? if (!empty($images)): ?>
                            <div class="pages-gallery-box">
                                <?= $images ?>
                            </div>
                        <? endif; ?>

                        <? if (!empty($files)): ?>
                            <div class="pages-files-box">
                                <?= $files ?>
                            </div>
                        <? endif; ?>

                        <? if (!empty($tags)): ?>
                            <div class="pages-files-box">
                                <?= $multi['TXT_THEMATIC_LINKS'] ?>:
                                <?= $tags ?>
                            </div>
                        <? endif; ?>

                        <? if (!empty($comments)): ?>
                            <?= $comments ?>
                        <? endif;
                    }
                    ?>
                </div>
            </div>
        </div>
<!--        --><?// var_dump($services);
        if($services){ ?>
            <div class="services-list services">
                <? foreach ($services as $key=>$item) { ?>
                    <div class="s-item" <? if(isset($item['bg_img'])) echo 'style = "background-image: url(\''.$item['bg_img'].'\')';?>">
                        <div class="container">
                            <div class="row s-data">
                                <div class="col-md-2 s-header">

                                    <div>
                                        <? if($item['img_icon']) echo '<img src="'.$item['img_icon'].'">';?>

                                        <h4><?=$item['name'];?></h4>
                                    </div>
                                </div>
                                <div class="col-md-10 s-text">
                                    <div><?=$item['shorthtml'];?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?}?>
            </div>


            <?}?>
            <?
    }?>
</div>