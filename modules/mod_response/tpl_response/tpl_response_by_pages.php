<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 31.05.13
 * Time: 12:54
 * To change this template use File | Settings | File Templates.
 */

for($i=0;$i<$rows;$i++){
    $row = $arr[$i];
    $name = htmlspecialchars($row['name']);
    $link = $row['link']
    ?><div class="element-one-item">
        <div class="element-ramka">
            <div class="response-head">
                <div class="response-name">
                    <a href="<?=$link?>" title="<?=$name?>"><?=$name?></a>
                </div>
                <div class="response-autor"><?=$row['autor']?></div>
            </div>
            <?/*if( !empty($value['img2'])){
                    $path = response_Img_Path.$value['img2'];
                    ?><div align="center">
                    <a href="<?=$path;?>" class="highslide" onclick="return hs.expand(this);"><?=$this->ShowImage($value['img2'], "size_height=300", 85, NULL, null);?></a>
                    </div><?
                }*/?>
            <div class="response-center"><?=$row['short']?></div>
            <div class="response-deteil">
                <a href="<?=$link?>" title="<?=$name?>"><?=$row['name_dateil']?></a>
            </div>
        </div>
        <div class="reaponse-footer"></div>
    </div><?
}
echo $page_navi;