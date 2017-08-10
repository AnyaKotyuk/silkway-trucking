<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 14.04.14
 * Time: 15:11
 */
?>
    <div class="main-category">
        <?
        foreach ($arr as $row) :
            ?>
            <div class="list-item-one-item">
            <div class="list-item-img">
                <div class="list-item-img-table">
                    <a href="<?= $row['link']; ?>" title="<?= $row['name_spec']; ?>"><?= $row['path']; ?></a>
                </div>
            </div>
            <div class="list-item-text">
                <div class="list-item-name">
                    <a href="<?= $row['link']; ?>" title="<?= $row['name_spec']; ?>"><?= $row['name']; ?></a>
                </div>
                <div class="list-item-description"><?= $row['descr'] ?></div>
            </div>
            </div><?
        endforeach;
        ?>
    </div>
<?