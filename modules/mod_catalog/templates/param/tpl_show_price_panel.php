<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 21.08.13
 * Time: 17:40
 * To change this template use File | Settings | File Templates.
 */
?><div class="paramBlock">
    <div class="paramName"><?=$Catalog->multi['FLD_PRICE']?>:</div>
    <div class="param-key-one-item">
        <div class="param-key-fon-fo-slider">
            <div id="slider<?=$id_elem?>"></div>
        </div>
        <div class="param-key-input-type1">
            <label form="<?=$id_elem?>_0"><?=$Catalog->multi['FLD_FROM']?></label>
            <input type="text" name="from" id="<?=$id_elem?>_0" value="<?=$val[0]?>"/>

            <label form="<?=$id_elem?>_1"><?=$Catalog->multi['TXT_TO']?></label>
            <input type="text" name="to" id="<?=$id_elem?>_1" value="<?=$val[1]?>"/>

            <label>грн.</label>
        </div>
        <div class="param-key-submit">
            <input type="submit" value="Ок" name="paramType1"
                   onclick="return gelPropConetntByPrice('<?=$Catalog->catLink?>','<?=$paramLink?>','<?=$id_elem?>');" />
        </div>
        <script type="text/javascript">
            //<![CDATA[
            jQuery("#slider<?=$id_elem?>").slider({
                min: <?=$valStart[0]?>,
                max: <?=$valStart[1]?>,
                values: [<?=$val[0]?>,<?=$val[1]?>],
                range: true,
                stop: function(event, ui) {
                    jQuery("input#<?=$id_elem?>_0").val(jQuery("#slider<?=$id_elem?>").slider("values",0));
                    jQuery("input#<?=$id_elem?>_1").val(jQuery("#slider<?=$id_elem?>").slider("values",1));
                },
                slide: function(event, ui){
                    jQuery("input#<?=$id_elem?>_0").val(jQuery("#slider<?=$id_elem?>").slider("values",0));
                    jQuery("input#<?=$id_elem?>_1").val(jQuery("#slider<?=$id_elem?>").slider("values",1));
                }
            });
            //]]>
        </script>
    </div>
    <div class="filters-off">
        <a href="<?=$Catalog->catLink.$paramLink?>" title="<?=$Catalog->multi['TXT_KILL_FILTER']?>"
           onclick="return gelPropConetnt('<?=$Catalog->catLink?>','<?=$paramLink?>');"><?=$Catalog->multi['TXT_KILL_FILTER']?></a>
    </div>
</div><?