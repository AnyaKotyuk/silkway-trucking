<?
$keys = array_keys($arr);
for ($i = 0; $i < count($keys); $i++) :
    if ($keys[$i] == _CURR_ID):
        ?>
    <span class="valutaSelected paddingLeft8px"><?=$arr[$keys[$i]]?></span>
    <? else:
        ?>
    <a class="valuta paddingLeft8px" href="<?=$url?>curr_ch=<?=$keys[$i]?>"><?=$arr[$keys[$i]]?></a>
    <?
    endif;
endfor;
?>