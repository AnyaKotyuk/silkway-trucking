<?php
/**
 * Created by PhpStorm.
 * User: bogdan
 * Date: 30.07.14
 * Time: 12:02
 */
?>
    <!-- FORM Choose excursions to visit -->
    <form class="form choose-excursion" action="<?=_LINK?>vip/" method="post">
        <i class="down-pointer"></i>
        <h3 class="block-title"><?=$vipLabel?></h3>
        <div class="inputs-group">
            <div class="input select">
                <label for="city_cod"><?=$labelCity?></label>
                <select id="city_cod" name="city_cod">
                    <option value=""></option>
                    <? foreach($arrCity as $key=>$row):?>
                    <option value="<?=$arrCityTranslit[$key]?>"<?
                    if($city_cod==$key){?> selected="selected" <?}
                    ?>><?=$row?></option>
                    <? endforeach;?>
                </select>
            </div>
            <div class="input">
                <input type="submit" class="btn btn-pink btn-submit" value="<?=$go?>" >
            </div>
        </div>
        <script type="text/javascript">
            $(window).load(function(){
                $(".btn-submit").click(function(){
                    $(".choose-excursion").attr("action","<?=_LINK?>vip/"+$("#city_cod").val().toLowerCase());
                    $(".choose-excursion").submit();
                })
            })
        </script>
    </form>
    <!-- END FORM Choose excursions to visit -->
<?