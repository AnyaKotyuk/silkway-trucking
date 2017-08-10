<?php
include_once( SITE_PATH.'/modules/mod_dealers/dealers.defines.php' );

 class DealerLayout extends Dealer {

     /**
      * @programmer Bogdan Iglinsky
      * @param null $user_id
      * @param null $module
      * @param null $display
      * @param null $sort
      * @param null $start
      * @param null $width
      */
     function DealerLayout($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                //parent::Dealer();
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 10   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;

                if (empty($this->Spr)) $this->Spr = new  SysSpr();
                if (empty($this->db)) $this->db =  DBs::getInstance();
                //if (empty($this->Form)) $this->Form = new Form('form_dealer');
                $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
       } // End of DealerLayout Constructor


     /**
      * @programmer Bogdan Iglinsky
      * @return bool
      */
     function ShowAllDealers()
       {
        $arr=$this->GetDealers();
//           var_dump($arr);
        if($this->lang_id==2) $lang='_ua';
        else $lang='';
         $count=count($arr); ?>
         <script type="text/javascript">
            //<![CDATA[
            function showhover(i){
                name='#a_mapin'+i;
                url="/images/design/map/map_"+i+"_hover<?=$lang?>.png";
                $(name).css('backgroundImage', 'url('+url+')');
                name_a='#href_fo_region'+i;
                $(name_a).css('textDecoration','underline');

            }
            function showRealHover(i){
                name='#a_mapin'+i;
                //element=document.getElementById(name);
                url="/images/design/map/map_"+i+"<?=$lang?>.png";
                //element.style.backgroundImage.url=url;
                $(name).css('backgroundImage', 'url('+url+')');
                name_a='#href_fo_region'+i;
                $(name_a).css('textDecoration','none');
            }
        //]]>
        </script>
        <?$this->ShowJS();?>
<!--         <div id="backgraundMap">-->
<!--         <img border="0" style="position: absolute;z-index: 2;" id="dealersMaps" src="/images/design/ukrain_map_epmty.png" alt="" title="" usemap="#map_regions"  />-->
<!--         <map id="mapRegions" name="map_regions" style="background: black;">-->
            <?
            //print_r($arr);
            $arr_param=array();
            $arr_param=$this->GetArrCoor();
            $array_max_w=array();

            for( $i = 0; $i < $count; $i++ ){
                $href=$this->GetHref($arr[$i]['translit']);
                if($arr[$i]['fon_img']!=NULL){?>
                  <area shape="poly" coords="<?
                  $obl_i=$arr[$i]['fon_img'];
                  $count_coo=count($arr_param[$obl_i]);
                  $min_x=9999;
                  $max_x=0;
                  for($j=0;$j<$count_coo;$j++){
                    if($j>0)echo ',';
                    echo $arr_param[$obl_i][$j]['x'].','.$arr_param[$obl_i][$j]['y'];
                    if($min_x>$arr_param[$obl_i][$j]['x']) $min_x=$arr_param[$obl_i][$j]['x'];
                    if($max_x<$arr_param[$obl_i][$j]['x']) $max_x=$arr_param[$obl_i][$j]['x'];
                  }
                  $array_max_w[$obl_i]=$max_x-$min_x
                  ?>" href="<?=$href?>" alt="<?=$arr[$i]['name']?>"
                  onmouseout="showRealHover(<?=$obl_i?>);" onmouseover="showhover(<?=$obl_i?>);" /><?
                }
            }
            ?>
         </map>
         <?
            $arr_param=array();
            $arr_param=$this->GetArrAHref();
            //print_r($arr);
            for( $i = 0; $i < $count; $i++ ){
                $obl_i=$arr[$i]['fon_img'];
                if(!empty($arr_param[$obl_i])){
                  ?>
<!--                  <div class="map" id="a_mapin--><?//=$obl_i?><!--" style=" background-image: url('/images/design/map/map_--><?//=$obl_i.$lang/*.png'); */<?//
//                  if($array_max_w[$obl_i]>150){/*width: 300px;*/<?//}/*top:*/<?//=$arr_param[$obl_i]['y']/*px;left:*/<?//=$arr_param[$obl_i]['x']/*px;*/<?//
//                  ?><!--" title="--><?//=$arr[$i]['name']?><!--">-->
<!--                    --><?//
//                    $count_obj=count($arr[$i]['obj']);
//                    if($count_obj>0)
//                    for($j=0;$j<$count_obj;$j++){
//                        if($arr[$i]['obj'][$j]['ko_y']!=0 || $arr[$i]['obj'][$j]['ko_x']!=0){
//                            ?>
<!--<!--                            <img class="img-absolute"  style="top: --><?////=$arr[$i]['obj'][$j]['ko_y']?><!--/*px;left: */--><?////=$arr[$i]['obj'][$j]['ko_x']?><!--/*px;"*/--><?////
//                            if($arr[$i]['obj'][$j]['group_d']==1){?><!-- src="/images/design/ofis.png" style=""--><?//}
//                            if($arr[$i]['obj'][$j]['group_d']==2){?><!-- src="/images/design/sklad.png" style=""--><?//}?><!-- alt="" title="" />--><?//
//                        }
//                    }
//                    ?>
<!--                  </div>--><?//
                }
            }
            ?>
            </div>
         <div class="container contacts">

         <?
         $arr=$this->GetDealersContent();
         //print_r($arr);die;
         $count=count($arr);
//         var_dump($arr);

         for( $i = 0; $i < $count; $i++ )
         {
//             echo $i;
//             var_dump($arr);
             if($i%3 == 0 || $i == 0) echo '<div class="row cont">';
             ?>
             <div class="col-md-4 ">
             <div class="contact">
             <?
              $href=$this->GetHref($arr[$i]['text']['translit']);
              if($arr[$i]['text']['cenntral_ofis']==1)$name = $this->multi['_TXT_CENTRAL_OFIS'].' '.$arr[$i]['text']['city_name'];
              else $name= $arr[$i]['text']['city_name'];
              $obl_i=$arr[$i]['text']['fon_img'];
              //$name=$arr[$i]['text']['city_name'];
              ?>
<!--              <div class="dealer-name">-->
<!--                <a id="href_fo_region--><?//=$obl_i?><!--" onmouseout="showRealHover(--><?//=$obl_i//);" onmouseover="showhover(<?//=$obl_i//);" href="<?//=$href?><!--">--><?//=$name?><!--</a>-->
<!--              </div>-->
              <?
              $count_od=count($arr[$i]['arr']);
              for($j=0;$j<$count_od;$j++){

                    $grup=$arr[$i]['arr'][$j]['grup'];
                    $name=$arr[$i]['arr'][$j]['name'];
//                  var_dump($arr[$i]['arr'][$j]);
                    $name2=$arr[$i]['arr'][$j]['name2'];
//                  var_dump($name2);
                    $adr=strip_tags($arr[$i]['arr'][$j]['adr']);
                    $tel=$arr[$i]['arr'][$j]['tel'];
                    $email=$arr[$i]['arr'][$j]['email'];
                    $img=$arr[$i]['arr'][$j]['img'];

              ?>
              <h2><?=$name;?></h2>
                  <h4><?=$name2;?></h4>
                  <? if($adr){ ?><div class="location"><?=$adr;?></div><?}?>
                  <? if($tel){ ?><div class="phone"><?=$tel;?></div><?}?>
                  <? if($email){ ?><div class="email"><?=$email;?></div><?}?>

              <?

//                ?><!--<div class="pid-li-dealer">--><?//
//                if( !empty($img) ) {
//                    //echo 'img='.$img;
//                    ?><!--<div class="dealer-map">-->
<!--                    <a class="fancybox" rel="itemImg" href="--><?//=Dealer_Img_Path.$img;?><!--" title="--><?//=htmlspecialchars($adr)?><!--">--><?//
//                    $this->ShowImage($img, 'size_width=136', 100, NULL, "border=0");
//                    ?><!--</a>-->
<!--                    </div>--><?//
//                }
//                else{
//                  ?>
<!--                  <div class="dealer-map">-->
<!--                    --><?//=$arr[$i]['arr'][$j]['full']?>
<!--                  </div>-->
<!--                --><?//}?>
<!--                  <div class="dealer-content">-->
<!--                    --><?//
//                    if(!empty($name)){
//                        ?>
<!--                        <div class="name-ofis">-->
<!--                           --><?//if($grup==1){
//                            ?><!--<img src="/images/design/ofis.png"  alt="" title=""/>--><?//
//                           }else{
//                            ?><!--<img src="/images/design/sklad.png"  alt="" title=""/>--><?//
//                           }
//                           echo $name;?>
<!--                        </div>--><?//
//                    }
//
//                    if(!empty($name2)){
//                        ?>
<!--                        <div class="name-ofis">-->
<!---->
<!--                           --><?//=$name2;?>
<!--                        </div>--><?//
//                    }
//                    ?>
<!--                    <div class="dealer-content-text">--><?//
//                        if(!empty($adr)){?>
<!--                        <div class="adr_ofis">-->
<!--                            <b>--><?//=$this->multi['FLD_ADR']?><!--:</b>-->
<!--                            <span>--><?//=$adr?><!--</span>-->
<!--                        </div>-->
<!--                        --><?//}
//                        if(!empty($tel)){?>
<!--                        <div class="tel_ofis">-->
<!--                            <b>--><?//=$this->multi['FLD_PHONE']?><!--:</b>-->
<!--                            <span>--><?//=$tel?><!--</span>-->
<!--                        </div>-->
<!--                        --><?//}
//                        if(!empty($emal)){?>
<!--                        <div class="email_ofis">-->
<!--                            <b>--><?//=$this->multi['FLD_EMAIL']?><!--:</b>-->
<!--                            <span>--><?//=$emal?><!--</span>-->
<!--                        </div>-->
<!--                        --><?//}?>
<!--                    </div>-->
<!--                  </div>-->
<!--                </div>-->
                <?
              }?>
               </div></div>
              <?
             if($i%3 == 2 || ($count_od-$j)/3 >= 1) echo '</div>';
         }
         ?>
            </div>


         <?
         return true;
       } //end of function ShowAllDealers()


     /**
      * @programmer Bogdan Iglinsky
      *
      */
     function ShowJS(){
        ?>
        <script type="text/javascript">
            //<![CDATA[

        jQuery(document).ready(function() {
            jQuery('.fancybox').fancybox({
                'transitionIn'	:	'elastic',
                'transitionOut'	:	'elastic',
                'titlePosition'  : 'over',
                'overlayColor' : '#2a2a2a'
            });
                $("#dealer_content embed").attr({width:136, height:126});
                $("#dealer_content object").attr({width:136, height:126});
                $('#dealer_content iframe').attr({width:136, height:126});

        });
        //]]>
    </script>
        <?
       }

     /**
      * @programmer Bogdan Iglinsky
      * @return array
      */
     function GetArrAHref(){
        $arr=array();
        $i=0;
        $arr[$i]['x']=391;
        $arr[$i]['y']=144;
        $i=1;
        $arr[$i]['x']=259;
        $arr[$i]['y']=92;
        $i=2;
        $arr[$i]['x']=237;
        $arr[$i]['y']=35;
        $i=3;
        $arr[$i]['x']=68;
        $arr[$i]['y']=-6;
        $i=4;
        $arr[$i]['x']=68;
        $arr[$i]['y']=80;
        $i=5;
        $arr[$i]['x']=314;
        $arr[$i]['y']=75;
        $i=6;
        $arr[$i]['x']=339;
        $arr[$i]['y']=230;
        return $arr;
       }

     /**
      * @programmer Bogdan Iglinsky
      * @return array
      */
     function GetArrCoor(){
        $arr_param=array();
        /*dnepropetrovsk*/
            $i=0;
            $arr_param[$i][0]['x']='396';
            $arr_param[$i][0]['y']='256';
            $arr_param[$i][1]['x']='413';
            $arr_param[$i][1]['y']='207';
            $arr_param[$i][2]['x']='450';
            $arr_param[$i][2]['y']='175';
            $arr_param[$i][3]['x']='535';
            $arr_param[$i][3]['y']='209';
            $arr_param[$i][4]['x']='524';
            $arr_param[$i][4]['y']='241';
            $arr_param[$i][5]['x']='509';
            $arr_param[$i][5]['y']='240';
            $arr_param[$i][6]['x']='470';
            $arr_param[$i][6]['y']='229';
            $arr_param[$i][7]['x']='468';
            $arr_param[$i][7]['y']='257';
            $arr_param[$i][8]['x']='447';
            $arr_param[$i][8]['y']='265';
        /*chercasu*/
            $i=1;
            $arr_param[$i][0]['x']='270';
            $arr_param[$i][0]['y']='187';
            $arr_param[$i][1]['x']='320';
            $arr_param[$i][1]['y']='169';
            $arr_param[$i][2]['x']='331';
            $arr_param[$i][2]['y']='155';
            $arr_param[$i][3]['x']='341';
            $arr_param[$i][3]['y']='155';
            $arr_param[$i][4]['x']='341';
            $arr_param[$i][4]['y']='145';
            $arr_param[$i][5]['x']='359';
            $arr_param[$i][5]['y']='122';
            $arr_param[$i][6]['x']='379';
            $arr_param[$i][6]['y']='148';
            $arr_param[$i][7]['x']='383';
            $arr_param[$i][7]['y']='186';
            $arr_param[$i][8]['x']='399';
            $arr_param[$i][8]['y']='176';
            $arr_param[$i][9]['x']='286';
            $arr_param[$i][9]['y']='214';
        /*kuev*/
            $i=2;
            $arr_param[$i][0]['x']='265';
            $arr_param[$i][0]['y']='56';
            $arr_param[$i][1]['x']='281';
            $arr_param[$i][1]['y']='50';
            $arr_param[$i][2]['x']='299';
            $arr_param[$i][2]['y']='51';
            $arr_param[$i][3]['x']='329';
            $arr_param[$i][3]['y']='92';
            $arr_param[$i][4]['x']='337';
            $arr_param[$i][4]['y']='103';
            $arr_param[$i][5]['x']='355';
            $arr_param[$i][5]['y']='99';
            $arr_param[$i][6]['x']='359';
            $arr_param[$i][6]['y']='116';
            $arr_param[$i][7]['x']='342';
            $arr_param[$i][7]['y']='141';
            $arr_param[$i][8]['x']='330';
            $arr_param[$i][8]['y']='145';
            $arr_param[$i][9]['x']='317';
            $arr_param[$i][9]['y']='165';
            $arr_param[$i][10]['x']='275';
            $arr_param[$i][10]['y']='174';
            $arr_param[$i][11]['x']='267';
            $arr_param[$i][11]['y']='140';

            $arr_param[$i][12]['x']='276';
            $arr_param[$i][12]['y']='134';
        /*luck && rovno*/
        $i=3;
            $arr_param[$i][0]['x']='74';
            $arr_param[$i][0]['y']='28';
            $arr_param[$i][1]['x']='85';
            $arr_param[$i][1]['y']='31';
            $arr_param[$i][2]['x']='102';
            $arr_param[$i][2]['y']='19';
            $arr_param[$i][3]['x']='143';
            $arr_param[$i][3]['y']='19';
            $arr_param[$i][4]['x']='141';
            $arr_param[$i][4]['y']='42';
            $arr_param[$i][5]['x']='196';
            $arr_param[$i][5]['y']='31';
            $arr_param[$i][6]['x']='201';
            $arr_param[$i][6]['y']='41';
            $arr_param[$i][7]['x']='214';
            $arr_param[$i][7]['y']='41';
            $arr_param[$i][8]['x']='191';
            $arr_param[$i][8]['y']='95';
            $arr_param[$i][9]['x']='161';
            $arr_param[$i][9]['y']='112';
            $arr_param[$i][10]['x']='154';
            $arr_param[$i][10]['y']='108';
            $arr_param[$i][11]['x']='124';
            $arr_param[$i][11]['y']='120';
            $arr_param[$i][12]['x']='120';
            $arr_param[$i][12]['y']='116';
            $arr_param[$i][13]['x']='120';
            $arr_param[$i][13]['y']='108';
            $arr_param[$i][14]['x']='107';
            $arr_param[$i][14]['y']='104';
            $arr_param[$i][15]['x']='100';
            $arr_param[$i][15]['y']='94';
            $arr_param[$i][16]['x']='83';
            $arr_param[$i][16]['y']='83';
            $arr_param[$i][17]['x']='82';
            $arr_param[$i][17]['y']='68';
        /*ternopol*/
            $i=4;
            $arr_param[$i][0]['x']='102';
            $arr_param[$i][0]['y']='146';
            $arr_param[$i][1]['x']='127';
            $arr_param[$i][1]['y']='120';
            $arr_param[$i][2]['x']='157';
            $arr_param[$i][2]['y']='111';
            $arr_param[$i][3]['x']='150';
            $arr_param[$i][3]['y']='130';
            $arr_param[$i][4]['x']='152';
            $arr_param[$i][4]['y']='191';
            $arr_param[$i][5]['x']='134';
            $arr_param[$i][5]['y']='198';
            $arr_param[$i][6]['x']='106';
            $arr_param[$i][6]['y']='167';
        /*harkov && poltava*/
            $i=5;
            $arr_param[$i][0]['x']='458';
            $arr_param[$i][0]['y']='119';
            $arr_param[$i][1]['x']='495';
            $arr_param[$i][1]['y']='99';
            $arr_param[$i][2]['x']='517';
            $arr_param[$i][2]['y']='111';
            $arr_param[$i][3]['x']='544';
            $arr_param[$i][3]['y']='97';
            $arr_param[$i][4]['x']='568';
            $arr_param[$i][4]['y']='123';
            $arr_param[$i][5]['x']='569';
            $arr_param[$i][5]['y']='159';
            $arr_param[$i][6]['x']='514';
            $arr_param[$i][6]['y']='203';
            $arr_param[$i][7]['x']='499';
            $arr_param[$i][7]['y']='182';
            $arr_param[$i][8]['x']='461';
            $arr_param[$i][8]['y']='169';
            $arr_param[$i][9]['x']='448';
            $arr_param[$i][9]['y']='177';
            $arr_param[$i][10]['x']='440';
            $arr_param[$i][10]['y']='195';
            $arr_param[$i][11]['x']='420';
            $arr_param[$i][11]['y']='195';
            $arr_param[$i][12]['x']='420';
            $arr_param[$i][12]['y']='195';
            $arr_param[$i][13]['x']='401';
            $arr_param[$i][13]['y']='175';
            $arr_param[$i][14]['x']='384';
            $arr_param[$i][14]['y']='169';
            $arr_param[$i][15]['x']='382';
            $arr_param[$i][15]['y']='144';
            $arr_param[$i][16]['x']='372';
            $arr_param[$i][16]['y']='129';
            $arr_param[$i][17]['x']='362';
            $arr_param[$i][17]['y']='116';
            $arr_param[$i][18]['x']='371';
            $arr_param[$i][18]['y']='108';
            $arr_param[$i][19]['x']='383';
            $arr_param[$i][19]['y']='111';
            $arr_param[$i][20]['x']='433';
            $arr_param[$i][20]['y']='101';
            $arr_param[$i][21]['x']='448';
            $arr_param[$i][21]['y']='120';
        /*harkov*/
            $i=6;
            $arr_param[$i][0]['x']='399';
            $arr_param[$i][0]['y']='263';
            $arr_param[$i][1]['x']='445';
            $arr_param[$i][1]['y']='266';
            $arr_param[$i][2]['x']='475';
            $arr_param[$i][2]['y']='326';
            $arr_param[$i][3]['x']='454';
            $arr_param[$i][3]['y']='348';
            $arr_param[$i][4]['x']='375';
            $arr_param[$i][4]['y']='344';
            $arr_param[$i][5]['x']='352';
            $arr_param[$i][5]['y']='332';
            $arr_param[$i][6]['x']='364';
            $arr_param[$i][6]['y']='304';
            $arr_param[$i][7]['x']='392';
            $arr_param[$i][7]['y']='302';
            $arr_param[$i][8]['x']='393';
            $arr_param[$i][8]['y']='283';
            $arr_param[$i][9]['x']='400';
            $arr_param[$i][9]['y']='277';
        return $arr_param;
       }

     /**
      * @programmer Bogdan Iglinsky
      * @return bool
      */
     function ShowDetailDealer()
       {
         $arr=$this->GetDealersFullMap();
         $arr_c=$this->GetDealersFullCoord();
         //print_r($arr);
         if( !empty($arr ) ){?>
         <div id="imgCity">
            <?/*?><img src="/images/spr/mod_dealers_spr_city/<?=$this->lang_id?>/<?=$arr['img']?>" alt="" title="" /><?*/?>
            <div id="coord"><?
                $count=count($arr_c);
                for($i=0;$i<$count;$i++){
                 if($arr_c[$i]['ko_y']!=0 && $arr_c[$i]['ko_x']!=0){
                   if($arr_c[$i]['group_d']==1){
                    ?>
                    <img class="img-absolute" style="top: <?=$arr_c[$i]['ko_y']?>px;left: <?=$arr_c[$i]['ko_x']?>px;"
                    src="/images/design/ofis.png" alt="" title="" /><?
                   }
                   if($arr_c[$i]['group_d']==2){
                    ?>
                    <img class="img-absolute"  style="top: <?=$arr_c[$i]['ko_y']?>px;left: <?=$arr_c[$i]['ko_x']?>px;"
                    src="/images/design/sklad.png" alt="" title="" /><?
                   }
                 }
                }?>
            </div>
            <div id="blockOfImg">
                <?=$this->ShowImageCity($arr, 'size_width=500', 100, NULL, "border=0 alt='' title=''");?>
            </div>
         </div>
         <? }
         $arr=$this->GetDealersContent();
         $i=0;
              if($arr[$i]['text']['cenntral_ofis']==1)$name = $this->multi['_TXT_CENTRAL_OFIS'].' '.$arr[$i]['text']['city_name'];
              else $name= $this->multi['TXT_PREDSTAV'].' '.$arr[$i]['text']['city_name'];
              $obl_i=$arr[$i]['text']['fon_img'];
              //$name=$arr[$i]['text']['city_name'];
              $this->ShowJS();?>
              <div id="dealerContent">
              <div class="dealer-name"><?=$name?></div>
              <?
              $count_od=count($arr[$i]['arr']);
              for($j=0;$j<$count_od;$j++){
                $adr=$arr[$i]['arr'][$j]['adr'];
                $img=$arr[$i]['arr'][$j]['img'];
                $grup=$arr[$i]['arr'][$j]['grup'];
                $name=$arr[$i]['arr'][$j]['name'];
                $tel=$arr[$i]['arr'][$j]['tel'];
                $emal=$arr[$i]['arr'][$j]['email'];
                ?><div class="pid-li-dealer"><?
                if( !empty($img) ) {
                    //echo 'img='.$img;
                    ?><div class="dealer-map">
                    <a class="fancybox" rel="itemImg" href="<?=Dealer_Img_Path.$img;?>" title="<?=htmlspecialchars($adr);?>"><?
                    $this->ShowImage($img, 'size_width=136', 100, NULL, "border=0");
                    ?></a>
                    </div><?
                }
                else{
                  ?>
                  <div class="dealer-map">
                    <?=$arr[$i]['arr'][$j]['full']?>
                  </div>
                <?}?>
                  <div class="dealer-content">
                    <?
                    if(!empty($name)){
                        ?>
                        <div class="name-ofis">
                           <?if($grup==1){
                            ?><img src="/images/design/ofis.png"  alt="" title=""/><?
                           }else{
                            ?><img src="/images/design/sklad.png"  alt="" title=""/><?
                           }
                           echo $name;?>
                        </div><?
                    }
                    ?>
                    <div class="dealer-content-text"><?
                        if(!empty($adr)){?>
                        <div class="adr_ofis">
                            <b><?=$this->multi['FLD_ADR']?>:</b>
                            <span><?=$adr?></span>
                        </div>
                        <?}
                        if(!empty($tel)){?>
                        <div class="tel_ofis">
                            <b><?=$this->multi['FLD_PHONE']?>:</b>
                            <span><?=$tel?></span>
                        </div>
                        <?}
                        if(!empty($emal)){?>
                        <div class="email_ofis">
                            <b><?=$this->multi['FLD_EMAIL']?>:</b>
                            <span><?=$emal?></span>
                        </div>
                        <?}?>
                    </div>
                  </div>
                </div>

                <?
              } ?>
              <div style="margin-top: 10px;"><a href="<?=_LINK?>dealers/"><?=$this->multi['FLD_BACK']?></a></div>
              </div><?
         return true;
       } //end of function ShowDetailDealer()

  } // End of class DealerLayout