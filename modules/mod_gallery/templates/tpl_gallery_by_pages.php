<div class="atricles-by-pages-box">
    <?php
        if (count($gallerys) == 0):
    ?>
            <div class="err"><?= $multi['MSG_NO_GALLERY']; ?></div>
    <?php
        return;
        endif;
    ?><div class="gallery-fon"><?php
    foreach ($gallerys as $gallery){
       //echo View::factory('/modules/mod_gallery/templates/tpl_gallery_by_pages_single.php')
       //    ->bind('multi',$multi)
       //    ->bind('gallery',$gallery);
       ?>
        <div class="gallery-one-item<?=$gallery['class']?>">
            <div class="gallery-img">
                <div class="gallery-img-table">
                    <a href="<?=$gallery['link']?>" title="<?=$gallery['name']?>">
                        <img src="<?=$gallery['img']['path']?>" alt="<?=$gallery['img']['alt']?>" title="<?=$gallery['img']['title']?>" />
                    </a>
                </div>
            </div>
            <div class="gallery-name">
                <a href="<?=$gallery['link']?>" title="<?=$gallery['name']?>"><?=$gallery['name']?></a>
            </div>
        </div>
       <?
    }
    ?></div>

</div>



<?php if (!empty($pages)): ?>
<div class="page-navi-class">
    <?php echo $pages; ?>
</div>
<?php endif; ?>