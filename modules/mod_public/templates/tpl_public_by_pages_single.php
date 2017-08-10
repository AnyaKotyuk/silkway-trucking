<div class="public-one-item">
    <div class="public-img" id="blockImg<?=$id?>">
        <?if(!empty($public['img'])){?>
            <img src="<?=$public['img']?>" alt="<?=$public['name'];?>" title="<?=$public['name'];?>"/>
        <?}?>
    </div>
    <div class="public-content" id="blockContent<?=$id?>">
     <div class="public-date"><?=$public['date']?></div>
     <div class="public-name"><?=$public['name']?></div>
     <div class="public-text" id="publicText<?=$id?>"><?=$public['text']?></div>
     <div class="public-phone-detel">
         <input type="hidden" name="heightStart" id="publicTextHeight<?=$id?>" value="175" />
         <input type="hidden" name="heightStart" id="publicTextHeightReal<?=$id?>" value="175" />
          <div class="public-phone"><?=$public['contact']?></div>
          <div class="public-detel" id="blockDetail<?=$id?>">
              <span onclick="showText(<?=$id?>)">Подробнее →</span>
          </div>
     </div>
     <script type="text/javascript">
         <?if(!empty($public['img'])){
             ?>$('#blockImg img').load(function(){<?
         }?>
            checkBlock(<?=$id?>);
         <?if(!empty($public['img'])){
             ?>});<?
         }?>                        
     </script>
    </div>
</div>
