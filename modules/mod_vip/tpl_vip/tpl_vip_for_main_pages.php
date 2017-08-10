<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 31.07.14
 * Time: 17:02
 */
?>
    <!-- Block Rating -->
    <div class="block-rating">

        <div class="block-title"><?=$title?></div>
        <ul class="left-side">
            <?for($i=0;$i<$rowsCenter;$i++):
                $row = $arrvip[$i];?>
                <li><i class="rating-cicle"><?=$row['rating']?></i><a href="<?=$row['link']?>"><?=$row['name']?></a></li>
            <?endfor;?>
        </ul>
        <?if($rowsEnd>$rowsCenter):?>
        <ul class="right-side">
            <?for($i=$rowsCenter;$i<$rowsEnd;$i++):
                $row = $arrvip[$i];?>
                <li><i class="rating-cicle"><?=$row['rating']?></i><a href="<?=$row['link']?>"><?=$row['name']?></a></li>
            <?endfor;?>
        </ul>
        <?endif;?>
    </div>
    <!-- END Block Rating -->
<?