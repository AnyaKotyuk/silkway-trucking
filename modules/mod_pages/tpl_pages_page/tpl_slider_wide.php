<?//Call from function ShowSliderWide(){?>
<script type="text/javascript" src='/include/js/slider-wide/jquery.cycle.all.min.js'></script>
<script type="text/javascript" src='/include/js/slider-wide/jquery.easing.1.1.1.js'></script>
<script type="text/javascript" src='/include/js/slider-wide/homepageSlider.js'></script>
<div class="slider">
    <div class="homepage-slider">
        <a href="#" id="prev"></a>
        <a href="#" id="next"></a>
        <div id="nav"></div>
        <div id="homepageSlider">
            <?
            for($i=0;$i<$count;$i++){
                $img = $array[$i]['rel_path_img'];
                if(empty($img))
                    continue;
                $name = stripslashes($array[$i]['name']);
                $descr = strip_tags(stripslashes($array[$i]['descr']),'<a><br><strong><p>');
                $href = stripslashes($array[$i]['href']);
                if(!empty($href)){
                   ?><a href="<?=$href;?>" title="<?=htmlspecialchars($name);?>"><?
                }
                ?>
                <img src="<?=$img;?>" alt="<?=htmlspecialchars($name);?>" title="<?=htmlspecialchars($name);?>">
                <?
                if(!empty($href)){
                    ?></a><?
                }
            }
            ?>
        </div>
    </div>
</div>