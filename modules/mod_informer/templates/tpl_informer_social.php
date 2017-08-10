<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.04.14
 * Time: 18:41
 */
?>
    <ul >
       <?
$width = (1/count($arr))*100;
foreach($arr as $row):
    //var_dump($arr);
        ?><li style="width: <?=$width?>%"><?
        if(!empty($row['href'])){
            ?><a href="<?=$row['href']?>"<?if($row['target']){?> target="_blank"<?}?>title="<?=$row['name']?>"><?
        }
        ?><img src="<?=$row['path']?>" alt="<?=$row['name']?>"><?
        if(!empty($row['href'])):
            ?></a><?
            endif;
    ?></li><?
        endforeach;?>
    </ul><?