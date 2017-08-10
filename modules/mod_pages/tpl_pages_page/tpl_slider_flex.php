<?//Call from function ShowFlexSlider(){?>
<link rel="stylesheet" type="text/css" href="/include/js/flexslider/flexslider.css" />
<script type="text/javascript" src="/include/js/flexslider/flexslider.js"></script>
<script type="text/javascript">
    $(window).load(function () {
        $('.flexslider').flexslider({
            animation: "slide",
            start: function (slider) {
            $('body').removeClass('loading');
            }
        });
    });
</script>

<!-- SLIDER - START -->
<h2>Flex Slider For IPAD</h2>
<div class="flexslider">
    <ul class="slides">
        <?
        for($i=0;$i<$count;$i++){
            $img = $array[$i]['rel_path_img'];
            if(empty($img))
                continue;
            $id = $array[$i]['id'];
            $name = stripslashes($array[$i]['name']);
            $descr = strip_tags(stripslashes($array[$i]['descr']),'<a><br><strong><p>');
            $href = stripslashes($array[$i]['href']);
            $h1 = $name;
            ?><li><?
            if(!empty($href)) {
                ?><a href="<?=$href?>" title="<?=htmlspecialchars($name);?>"><?
            }
            ?>
            <img src="<?=$img;?>" alt="<?=htmlspecialchars($name);?>" title="<?=htmlspecialchars($name);?>" />
            <?
            if(!empty($href)){
                ?></a><?
            }
            ?></li><?
        }
        /*?>
        <li><img src='/images/mod_article/42/44_13603159130.jpg' alt="" /></li>
        <li><img src='/uploads/images/gallery/2/galery1.jpg' alt="" /></li>
        <li><img src='/uploads/images/gallery/1/010004.jpg' alt="" /></li>*/?>
    </ul>
</div>
<!-- SLIDER - END-->