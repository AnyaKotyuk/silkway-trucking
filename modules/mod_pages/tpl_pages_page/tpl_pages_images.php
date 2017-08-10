<!--<div class="leftBlockHead">--><?//=$multi['_TXT_FILES_TO_PAGE']?><!--:</div>-->
<!---->
<!---->
<!---->
<!---->
<!--<div class="leftBlockHead">--><?//= $multi['SYS_IMAGE_GALLERY'];?><!--</div>-->
<!--<div class="image-block " align="center">-->
<!--    <ul id="carouselLeft" class="vhidden jcarousel-skin-menu">--><?//
?>

<div class="images">


<?
        for ($j = 0; $j < $items_count; $j++) {
            $alt = $items[$items_keys[$j]]['name'][$lang_id]; // Заголовок
            $title = $items[$items_keys[$j]]['text'][$lang_id]; // Описание
            $path = $items[$items_keys[$j]]['path']; // Путь уменьшенной копии
            $path_org = $items[$items_keys[$j]]['path_original']; // Путь оригинального изображения
            ?>
<!--            <li>-->
                <a href="<?=$title;?>" rel="page-image" title="<?=$title;?>" target="_blank">
                    <img src="<?=$path;?>" alt="<?=$alt?>" title="<?=$title;?>">
                </a>
<!--            </li>--><?//
        }
//        ?><!--</ul>-->
</div>