<?php


include_once( SITE_PATH.'/modules/mod_dealers/dealers.defines.php' );


 class Dealer {

       var $user_id = NULL;
       var $module = NULL;
       var $lang_id = NULL;
       var $Err = NULL;

       var $sort = NULL;
       var $display = 10;
       var $start = 0;
       var $width = 500;
       var $fln = NULL;
       var $fltr = NULL;
       var $fltr2 = NULL;
       var $srch = NULL;

       var $Msg = NULL;
       var $Right = NULL;
       var $Form = NULL;

       var $is_city = NULL;
       var $is_full = NULL;
       var $full = NULL;
       var $is_goup = NULL;
       var $is_image = NULL;
       var $lang = NULL;
       public $cat =NULL;
     public $name2 = NULL;


       function __construct($user_id=NULL, $module=NULL, $display=NULL, $sort=NULL, $start=NULL, $width = NULL) {
                //Check if Constants are overrulled
                ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
                ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );
                ( $display  !="" ? $this->display = $display  : $this->display = 10   );
                ( $sort     !="" ? $this->sort    = $sort     : $this->sort    = NULL );
                ( $start    !="" ? $this->start   = $start    : $this->start   = 0    );
                ( $width    !="" ? $this->width   = $width    : $this->width   = 750  );

                if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;

                //if (empty($this->Right)) $this->Right = new Rights($this->user_id, $this->module);
                if (empty($this->db)) $this->db =  DBs::getInstance();
                $this->Right =  check_init('RightsDealer', 'Rights', "'".$this->user_id."','".$this->module."'");
                $this->Spr = check_init('SysSpr', 'SysSpr'); /* create SysSpr object as a property of this class */
                $this->Form = check_init('FormDealer', 'Form', "'form_dealer'");
                $this->multi = check_init_txt('TblBackMulti',TblBackMulti);
                $this->is_city = true;
                $this->is_goup = true;
                $this->is_image = true;
                $this->is_full = true;
       } // End of Dealer Constructor


     /**
      * @programmer Bogdan Iglinsky
      * @return bool
      */
     function show()
       {
        // search in name
        if( $this->srch ) {
            $q = "SELECT `cod` FROM `".TblModDealerSprName."` WHERE `name` LIKE '%$this->srch%'";
            $tmp_res = $this->Right->db_Query($q);
            //echo '<br>'.$q.'<br/> $tmp_res='.$tmp_res.' $this->Right->result='.$this->Right->result;
            $tmp_rows = $this->Right->db_GetNumRows();
            $srch_str = NULL;
            for( $i = 0; $i < $tmp_rows; $i++ )
            {
                $row = $this->Right->db_FetchAssoc();
                if ( empty($srch_str) ) $srch_str = "'".$row['cod']."'";
                else $srch_str = $srch_str.",'".$row['cod']."'";
            }
        }
        // search in description
        if( $this->srch2) {
            $q = "SELECT `cod` FROM `".TblModDealerSprName."` WHERE `descr` LIKE '%$this->srch2%'";
            $tmp_res = $this->Right->db_Query($q);
            //echo '<br>'.$q.'<br/> $tmp_res='.$tmp_res.' $this->Right->result='.$this->Right->result;
            $tmp_rows = $this->Right->db_GetNumRows();
            $srch_str2 = NULL;
            for( $i = 0; $i < $tmp_rows; $i++ )
            {
                $row = $this->Right->db_FetchAssoc();
                if ( empty($srch_str2) ) $srch_str2 = "'".$row['cod']."'";
                else $srch_str2 = $srch_str2.",'".$row['cod']."'";
            }
        }
        if( !$this->sort ) $this->sort='move';
        $q = "SELECT `".TblModDealers."`.*,`".TblModDealerSprName."`.name,`".TblModDealerSprName."`.descr,`".TblModDealerSprName."`.full
         FROM `".TblModDealers."`,`".TblModDealerSprName."` where 1 ";
        if( $this->srch ) $q = $q." AND `".TblModDealers."`.`id` IN ($srch_str)";
        if( $this->srch2 ) $q = $q." AND `".TblModDealers."`.`id` IN ($srch_str2)";
        if( $this->fltr ) $q = $q." and `".TblModDealers."`.`group_d`=$this->fltr";
        if( $this->fltr2 ) $q = $q." and `".TblModDealers."`.`city_d`=$this->fltr2";

        $q = $q." AND `".TblModDealers."`.id=`".TblModDealerSprName."`.cod";
        $q = $q." AND `".TblModDealerSprName."`.lang_id='".$this->lang_id."'";
        $q = $q." order by `".TblModDealers."`.$this->sort ";

        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        //echo '<br>'.$q.'<br/> $res='.$res.' $this->Right->result='.$this->Right->result;
        if( !$res )return false;

        $rows = $this->Right->db_GetNumRows();
        $a = $rows;
        $j = 0;
        $up = 0;
        $down = 0;
        $row_arr = NULL;
        for( $i = 0; $i < $rows; $i++ )
        {
          $row = $this->Right->db_FetchAssoc();
          if( $i >= $this->start && $i < ( $this->start+$this->display ) )
          {
            $row_arr[$j] = $row;
            $j = $j + 1;
          }
        }
        // echo '<br> this->srch ='.$this->srch.' $script='.$script;

        /* Write Form Header */
        $this->Form->WriteHeader( $this->script );

        $this->ShowContentFilters();

        /* Write Table Part */
        AdminHTML::TablePartH();

        /* Write Links on Pages */
        echo '<TR><TD COLSPAN=9>';
        //$script1 = 'module='.$this->module.'&fltr='.$this->fltr.'&fltr2='.$this->fltr2;
        //$script1 = $_SERVER['PHP_SELF']."?$script1";
        $this->Form->WriteLinkPages( $this->script, $rows, $this->display, $this->start, $this->sort );

        echo '<TR><TD COLSPAN=4>';
        $this->Form->WriteTopPanel( $this->script );

        /*
        echo '<td colspan=3>';
        echo $this->Form->TextBox('srch', $this->srch, 25);
        echo '<input type=submit value='.$this->Msg->show_text('_BUTTON_SEARCH', TblSysTxt).'>';
        */

        ?><td align=center><?
        //echo '<br>$_SERVER["QUERY_STRING"]='.$_SERVER["QUERY_STRING"];
        if($this->is_goup)
        {
            echo '<b>'.$this->multi['FLD_GROUP'].':</b>';
            $arr_group=$this->GetStructureInArray(TblModDealerSprGroup);
//            $this->Spr->ShowActSprInCombo(TblModDealerSprGroup, 'fltr', $this->fltr, $_SERVER["QUERY_STRING"]."&start=$this->start&display=$this->display&sort=$this->sort&srch=$this->srch&srch2=$this->srch2");
            $scriplink=$_SERVER['PHP_SELF']."?".$_SERVER["QUERY_STRING"]."&start=$this->start&display=$this->display&sort=$this->sort&srch=$this->srch&srch2=$this->srch2";
            $this->Form->SelectAct( $arr_group, 'fltr', $this->fltr, "onChange=\"location='$scriplink&fltr='+this.value\"" );
        }

        ?><td align=center><?
//         if($this->is_city)
//         {
//            echo '<b>'.$this->multi['TXT_REGION'].':</b>';
//            $arr_city=$this->GetStructureInArray(TblModDealerSprCity);
//            $scriplink=$_SERVER['PHP_SELF']."?".$_SERVER["QUERY_STRING"]."&start=$this->start&display=$this->display&sort=$this->sort&srch=$this->srch&srch2=$this->srch2";
//            $this->Form->SelectAct( $arr_city, 'fltr2', $this->fltr2, "onChange=\"location='$scriplink&fltr2='+this.value\"" );
//         }
        //$script2 = 'module='.$this->module.'&display='.$this->display.'&start='.$this->start.'&task=show&fltr='.$this->fltr;
        //$script2 = $_SERVER['PHP_SELF']."?$script2";
       ?>
        <TR>
        <td class="THead">*</Th>
        <td class="THead"><A HREF=<?=$this->script?>&sort=id><?=$this->multi['FLD_ID']?></A></Th>
        <td class="THead"><?=$this->multi['FLD_NAME']?></Th>
<!--        --><?// if($this->is_image){?>
<!--        <td class="THead"><A HREF=--><?//=$this->script?><!--&sort=img>--><?//=$this->multi['FLD_IMG']?><!--</A></Th>--><?//}?>
<!--        --><?// if($this->is_goup){?>
<!--        <td class="THead"><A HREF=--><?//=$this->script?><!--&sort=group_d>--><?//=$this->multi['FLD_GROUP']?><!--</A></Th>--><?//}?>
<!--        --><?// if($this->is_city){?>
<!--        <td class="THead"><A HREF=--><?//=$this->script?><!--&sort=city_d>--><?//=$this->multi['TXT_REGION']?><!--</A></Th>--><?//}?>
        <? if($this->is_city){?><td class="THead"><?=$this->multi['FLD_ADR']?></Th><?}?>
        <td class="THead"><?=$this->multi['_FLD_MAPS']?></Th>
        <Th class="THead"><?=$this->multi['FLD_VISIBLE']?></Th>
        <Th class="THead"><?=$this->multi['FLD_DISPLAY']?></Th>
        <?

        $style1 = 'TR1';
        $style2 = 'TR2';
        for( $i = 0; $i < count( $row_arr ); $i++ )
        {
          $row = $row_arr[$i];

          if ( (float)$i/2 == round( $i/2 ) )
          {
           echo '<TR CLASS="'.$style1.'">';
          }
          else echo '<TR CLASS="'.$style2.'">';

          echo '<TD>';
          $this->Form->CheckBox( "id_del[]", $row['id'] );

          echo '<TD>';
          $this->Form->Link( $this->script."&task=edit&id=".$row['id'], stripslashes( $row['id'] ), $this->multi['_TXT_EDIT_DATA'] );
          echo '<TD align=center>'.stripslashes($row['name']).'</TD>';
//          if($this->is_image){
//          echo '<TD align=center>';
//          if ( !empty($row['img']) ){
//            ?><!--<a href="--><?//=Dealer_Img_Path.$row['img'];?><!--" target="_blank" onmouseover="return overlib('--><?//=$this->multi['TXT_ZOOM_IMG'];//',WRAP);" onmouseout="nd();" alt="<?//=$this->lang['TXT_ZOOM_IMG'];?><!--" title="--><?//=$this->lang['TXT_ZOOM_IMG'];?><!--">--><?//
//            $this->ShowImage($row['img'], 'size_width=75', 100, NULL, "border=0");
//            ?><!--</a><br>--><?//
//          }
//          }
            //if( !empty($row['img']) ) $this->Form->ButtonCheck();
//          if($this->is_goup){
//          echo '<TD align=center>'.$arr_group[$row['group_d']].'</TD>';
//          }
//           if($this->is_city){
//          if (empty($arr_city[$row['city_d']]) || $row['city_d']=='0') echo '<TD align=center>'.$this->multi['_FLD_NOT_ESTABLISHED'].'</TD>';
//          else echo '<TD align=center>'.$arr_city[$row['city_d']].'</TD>';
//           }
          echo '<TD align=center>';
          if( trim($row['descr'])!='' ) //$this->Form->ButtonCheck();
          echo trim($row['descr']).'</TD>';
          if($this->is_full){
              echo '<TD align=center>';
              if( trim($row['full'])!='' ) $this->Form->ButtonCheck();
           }
          echo '<TD align="center">';
          if( $row['visible'] == 0 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_x.png', $this->multi['TXT_UNVISIBLE'], 'border=0' );
          if( $row['visible'] == 1 ) $this->Form->Img( 'http://'.NAME_SERVER.'/admin/images/icons/publish_g.png', $this->multi['TXT_VISIBLE'], 'border=0' );

           echo '<TD align=center>';
           if( $i!=0 )
           {
           ?>
            <a href=<?=$this->script?>&task=up&move=<?=$row['move']?>><?=$this->Form->ButtonUp( $row['id'] );?></a>
           <?
           }

           if( $i!=($rows-1) )
           {
           ?>
             <a href=<?=$this->script?>&task=down&move=<?=$row['move']?>><?=$this->Form->ButtonDown( $row['id'] );?></a>
           <?
           }

           $up=$row['id'];
           $a=$a-1;


        } //-- end for

        AdminHTML::TablePartF();
        $this->Form->WriteFooter();
        return true;

       } //end of fuinction show


     /**
      * @programmer Bogdan Iglinsky
      * @param $spr
      * @return bool
      */
     function GetStructureInArray( $spr )
       {
            $q = "SELECT * FROM `".$spr."` WHERE 1";
            //if($level_start>0) {
            //    $sub_levels = $this->GetSubLevelsInStr($spr, $level_start);
            //    $q = $q." AND `level` IN (".$sub_levels.")";
            //}
            if( !empty($lang_id) ) $q = $q." AND `lang_id`='".$this->lang_id."'";
            //echo " tar=".$front_back;
            //if ( $front_back=='front' ) $q = $q." AND `visible`='2'";
            $q = $q." GROUP BY `cod` ";
            $res = $this->db->db_Query( $q );
            //echo '<br>q='.$q.' res='.$res.' $db->result='.$db->result;
            if( !$res )return false;
            $rows = $this->db->db_GetNumRows();
            //echo '<br> $rows='.$rows;
            //echo '<br> $show_content='.$show_content;
            $mas[''] = '';
            for( $i = 0; $i < $rows; $i++ )
            {
                $row=$this->db->db_FetchAssoc();
                $output_str = stripslashes($row['name']);
                $mas[$row['cod']] = $output_str;
                //------------------------------------------------------------------
            }
            $this->Right->db_FreeResult();
            //echo '<br>mas='; print_r($mas);
            return $mas;
       }// end of function GetStructureInArray()


     /**
      * @programmer Bogdan Iglinsky
      *
      */
     function ShowContentFilters()
       {
         /* Write Table Part */
         AdminHTML::TablePartH();
           //phpinfo();?>
             <table border=0 cellpadding=2 cellspacing=1>
              <tr><td><h4><?=$this->multi['TXT_SEARCH'];?></h4></td></tr>
              <tr class=tr1>
               <td><?=$this->multi['FLD_NAME'];?></td>
               <td><?$this->Form->TextBox('srch', $this->srch, 20);?></td>
              <tr class=tr1>
               <td><?=$this->multi['FLD_ADR'];?></td>
               <td><?$this->Form->TextBox('srch2', $this->srch2, 20);?></td>
              <tr class=tr2>
               <td colspan=2><?$this->Form->Button( '', $this->multi['TXT_SEARCH'], 50 );?></td>
              <tr>
              </tr>
             </table>
          <?/* </td>
          </tr>
         </table>
         <? */
         AdminHTML::TablePartF();

       } //end of fuinction ShowContentFilters()


     /**
      * @programmer Bogdan Iglinsky
      * @return bool
      */
 function edit()
       {
        $Panel = new Panel();
        $ln_sys = new SysLang();
        $mas=NULL;
        if( $this->id!=NULL)
        {
          $q = "SELECT
              `".TblModDealers."`.*,
              `".TblModDealerSprName."`.name,
              `".TblModDealerSprName."`.name2,
              `".TblModDealerSprName."`.lang_id,
              `".TblModDealerSprName."`.descr,
              `".TblModDealerSprName."`.full,
              `".TblModDealerSprName."`.tel,
              `".TblModDealerSprName."`.email
             FROM `".TblModDealers."`,`".TblModDealerSprName."` where 1
             AND `".TblModDealers."`.id='$this->id'
             AND `".TblModDealers."`.id=`".TblModDealerSprName."`.cod";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
          if( !$res ) return false;
          $num = $this->Right->db_GetNumRows();
          for($i=0;$i<$num;$i++)
          {
            $row = $this->Right->db_FetchAssoc();
            $mas[$row['lang_id']]=$row;
          }
        }


        /* Write Form Header */
        $this->Form->WriteHeaderFormImg( $this->script );
        $settings = SysSettings::GetGlobalSettings();
        $this->Form->textarea_editor = $settings['editer']; //'tinyMCE';
        $this->Form->IncludeSpecialTextArea( $this->Form->textarea_editor );
        $this->Form->Hidden( 'display', $this->display );
        $this->Form->Hidden( 'start', $this->start );
        $this->Form->Hidden( 'sort', $this->sort );
        $this->Form->Hidden( 'srch', $this->srch );
        $this->Form->Hidden( 'srch2', $this->srch2 );
        $this->Form->Hidden( 'fltr', $this->fltr );
        $this->Form->Hidden( 'fltr2', $this->fltr2 );
        $this->Form->Hidden( 'fln', $this->fln );
        $this->Form->Hidden( 'delimg', "" );

        //$this->Form->IncludeHTMLTextArea();

        if( $this->id!=NULL ) $txt = $this->multi['_TXT_EDIT_DATA'];
        else $txt = $this->multi['_TXT_ADD_DATA'];

        AdminHTML::PanelSubH( $txt );
        //-------- Show Error text for validation fields --------------
        $this->ShowErrBackEnd();
        //-------------------------------------------------------------
        AdminHTML::PanelSimpleH();

       ?>
        <table border="0" class="EditTable">
        <TR><TD><b><?echo $this->multi['_FLD_ID']?>:</b>
        <TD width="90%">
       <?
          if( $this->id!=NULL )
          {
           echo $mas[$this->lang_id]['id'];
           $this->Form->Hidden( 'id', $mas[$this->lang_id]['id'] );
          }
          else $this->Form->Hidden( 'id', '' );
       ?>
        <TR><TD><b><?echo $this->multi['FLD_VISIBLE'];?>:</b>
            <TD>
            <?
            $arr_v[0]=$this->multi['TXT_UNVISIBLE'];
            $arr_v[1]=$this->multi['TXT_VISIBLE'];

        if( $this->id!=NULL ) $this->Err!=NULL ? $visible=$this->visible : $visible=$mas[$this->lang_id]['visible'];
        else $visible=$this->visible;
        $this->Form->Select( $arr_v, 'visible', $visible );
//        if($this->is_image)
//           {
//            ?>
<!--            <TR><TD><b>--><?//echo $this->multi['_FLD_IMAGE']?><!--:</b>-->
<!--                <TD>-->
<!--                 <table border="0" cellpadding="0" cellspacing="1" class="EditTable">-->
<!--                  <tr>-->
<!--                   <td>--><?//
//                   if( $this->id!=NULL ) $this->Err!=NULL ? $img=$this->img : $img=$mas[$this->lang_id]['img'];
//                   else $img=$this->img;
//                    if( !empty($img) ) {
//                        //echo 'img='.$img;
//                        ?><!--<table border="0" cellpadding="0" cellspacing="5">-->
<!--                           <tr>-->
<!--                            <td class='EditTable'>--><?//
//                        $this->Form->Hidden( 'img', $img);
//                        ?><!--<a href="--><?//=Dealer_Img_Path.$img;?><!--" target="_blank" title="--><?//=$this->multi['TXT_ZOOM_IMG'];?><!--">--><?//
//                        $this->ShowImage($img, 'size_width=150', 100, NULL, "border=0");
//                        ?><!--</a><br>-->
<!--                        <td class='EditTable'>--><?//
//                        echo Dealer_Img_Full_Path.$img.'<br>';
//                        ?><!--<a href="javascript:document.getElementById('form_dealer').delimg.value='--><?//=$img;?><!--';document.getElementById('form_dealer').submit();">--><?//=$this->multi['_TXT_DELETE_IMG'];?><!--</a>--><?//
//                       ?><!--</table>--><?//
//                       echo '<tr><td><b>'.$this->multi['_TXT_REPLACE_IMG'].':</b>';
//                    }
//                        ?><!--<input type="file" name="filename" size="40" value=""/>-->
<!--                   </td>-->
<!--                  </tr>-->
<!--                 </table>-->
<!--            --><?//
//           }
//        ?>
<!--        <TR><TD><b>--><?//echo $this->multi['_FLD_CENTRAL_OFIS']?><!--:</b>-->
<!--        <TD width="90%">-->
<!--       --><?//
//          if($mas[$this->lang_id]['cenntral_ofis']==1)$check=true;
//          else $check=false;
//          //echo '$check='.$check;
//          $this->Form->CheckBox( 'cenntral_ofis',"",$check );
//       if($this->is_goup)
//       {
//       ?>
<!--        <TR><TD><b>--><?//echo $this->multi['_FLD_GROUP']?><!--:</b>-->
<!--            <TD>-->
<!--            --><?//
//        if( $this->id!=NULL ) $this->Err!=NULL ? $group_d=$this->group_d : $group_d=$mas[$this->lang_id]['group_d'];
//        else $group_d=$this->group_d;
//        $this->Spr->ShowInComboBox( TblModDealerSprGroup, 'group_d', $group_d, 40 );
//       }
//       if($this->is_city)
//       {
//       ?>
<!--        <TR><TD><b>--><?//echo $this->multi['_FLD_OBLAST']?><!--:</b>-->
<!--            <TD>-->
<!--            --><?//
//        if( $this->id!=NULL ) $this->Err!=NULL ? $city_d=$this->city_d : $city_d=$mas[$this->lang_id]['city_d'];
//        else $city_d=$this->city_d;
//        $this->Spr->ShowInComboBox( TblModDealerSprCity, 'city_d', $city_d, 40 );
//       }
       ?>
<!--       <tr><td><b>--><?//=$this->multi['_FLD_COORDINAT_MAIN']?><!--:</b></td></tr>-->
<!--        <tr>-->
<!--            <td><b>x:</b></td>-->
<!--            <td>-->
<!--                --><?//
//            if( $this->id!=NULL ) $this->Err!=NULL ? $main_x=$this->main_x : $main_x=$mas[$this->lang_id]['main_x'];
//            else $main_x=$this->main_x;
//            $this->Form->TextBox( 'main_x', stripslashes($main_x), 80 );
//           ?>
<!--           </td>-->
<!--       </tr>-->
<!--       <tr>-->
<!--            <td><b>y:</b></td>-->
<!--            <td>-->
<!--            --><?//
//        if( $this->id!=NULL ) $this->Err!=NULL ? $main_y=$this->main_y : $main_y=$mas[$this->lang_id]['main_y'];
//        else $main_y=$this->main_y;
//        $this->Form->TextBox( 'main_y', stripslashes($main_y), 80 );
//          ?><!--</td>-->
<!--        </tr>-->
<!--       <tr><td><b>--><?//=$this->multi['_FLD_KOORDUNATY']?><!--:</b></td></tr>-->
<!--        <tr>-->
<!--            <td><b>x:</b></td>-->
<!--            <td>-->
<!--                --><?//
//            if( $this->id!=NULL ) $this->Err!=NULL ? $ko_x=$this->ko_x : $ko_x=$mas[$this->lang_id]['ko_x'];
//            else $ko_x=$this->ko_x;
//            $this->Form->TextBox( 'ko_x', stripslashes($ko_x), 80 );
//           ?>
<!--           </td>-->
<!--       </tr>-->
<!--       <tr>-->
<!--            <td><b>y:</b></td>-->
<!--            <td>-->
<!--            --><?//
//        if( $this->id!=NULL ) $this->Err!=NULL ? $ko_y=$this->ko_y : $ko_y=$mas[$this->lang_id]['ko_y'];
//        else $ko_y=$this->ko_y;
//        $this->Form->TextBox( 'ko_y', stripslashes($ko_y), 80 );
//          ?><!--</td></tr>-->
        <TR><TD colspan=2>
        <?
        $Panel->WritePanelHead( "SubPanel_" );

        $ln_arr = $ln_sys->LangArray( $this->lang_id );


        while( $el = each( $ln_arr ) )
        {
             $lang_id = $el['key'];
             $lang = $el['value'];
             $mas_s[$lang_id] = $lang;

             $Panel->WriteItemHeader( $lang );
             echo "\n <table border=0 class='EditTable'>";
             echo "\n <tr>";
             echo "\n <td><b>".$this->multi['_FLD_NAME'].":</b>";
             echo "\n <tr><td>";
             if (!isset($rez_arr[$lang_id]['name']))  $rez_arr[$lang_id]['name']='';
             if( $this->id!=NULL )
                if($this->Err!=NULL)
                    $val=$this->name[$lang_id];
                else
                    if(!empty($mas[$lang_id]['name'])){ $val=$mas[$lang_id]['name'];}else $val='';
             else $val=$this->name[$lang_id];
             $this->Form->TextBox( 'name['.$lang_id.']', stripslashes($val), 80 );
             echo "\n <tr>";


            echo "\n <tr>";
             echo "\n <td><b>".$this->multi['_FLD_NAME2'].":</b>";
             echo "\n <tr><td>";
             if (!isset($rez_arr[$lang_id]['name2']))  $rez_arr[$lang_id]['name2']='';
             if( $this->id!=NULL )
                if($this->Err!=NULL)
                    $val=$this->name[$lang_id];
                else
                    if(!empty($mas[$lang_id]['name2'])){ $val=$mas[$lang_id]['name2'];}else $val='';
             else $val=$this->name[$lang_id];
             $this->Form->TextBox( 'name2['.$lang_id.']', stripslashes($val), 80 );
             echo "\n <tr>";


             echo "\n <td><b>".$this->multi['FLD_ADR'].":</b>";
             echo "\n <tr><td>";
             if (!isset($rez_arr[$lang_id]['descr']))  $rez_arr[$lang_id]['descr']='';
             if( $this->id!=NULL ){ $this->Err!=NULL ? $val=$this->content[$lang_id] :
             !empty($mas[$lang_id]['descr'])? $val=$mas[$lang_id]['descr'] :$val='';
             }
             else $val=$this->content[$lang_id];
             $this->Form->SpecialTextArea(NULL, 'content['.$lang_id.']', stripslashes($val), 5, 80, NULL, $lang_id ,'content' );
             echo "\n <tr>";
             echo "\n <td><b>".$this->multi['FLD_PHONE'].":</b>";
             echo "\n <tr><td>";
             if (!isset($rez_arr[$lang_id]['tel']))  $rez_arr[$lang_id]['tel']='';
             if( $this->id!=NULL )
                if($this->Err!=NULL)
                    $val=$this->tel[$lang_id];
                else
                    if(!empty($mas[$lang_id]['tel'])){ $val=$mas[$lang_id]['tel'];}else $val='';
             else $val=$this->tel[$lang_id];

             $this->Form->SpecialTextArea(NULL, 'tel['.$lang_id.']', stripslashes($val), 5, 80, NULL, $lang_id ,'tel' );

             echo "\n <tr>";
             echo "\n <td><b>".$this->multi['SYS_SET_MAIL'].":</b>";
             echo "\n <tr><td>";
             if (!isset($rez_arr[$lang_id]['email']))  $rez_arr[$lang_id]['email']='';
             if( $this->id!=NULL )
                if($this->Err!=NULL)
                    $val=$this->email[$lang_id];
                else
                    if(!empty($mas[$lang_id]['email'])){ $val=$mas[$lang_id]['email'];}else $val='';
             else $val=$this->email[$lang_id];
             $this->Form->TextBox( 'email['.$lang_id.']', stripslashes($val), 80 );

             if($this->is_full)
             {
                 echo "\n <tr>";
                 echo "\n <td><b>".$this->multi['_FLD_MAPS'].":</b>";
                 echo "\n <tr><td>";
                 if (!isset($rez_arr[$lang_id]['descr']))  $rez_arr[$lang_id]['descr']='';
                 if( $this->id!=NULL ){ $this->Err!=NULL ? $val=$this->content[$lang_id] :
                 !empty($mas[$lang_id]['full'])? $val=$mas[$lang_id]['full'] :$val='';
                 }
                 else $val=$this->content[$lang_id];
                 //$this->Form->HTMLTextArea( 'full['.$lang_id.']', stripslashes($val), 10, 70 );
                 $this->Form->SpecialTextArea(NULL, 'full['.$lang_id.']', stripslashes($val), 10, 80, NULL, $lang_id ,'full');
             }

             echo "\n <td rowspan=3>";
             echo   "\n </table>";
             $Panel->WriteItemFooter();
        }
        $Panel->WritePanelFooter();

        if ($this->id==NULL) {
         $arr = NULL;
         $arr['']='';
         //$tmp_db = $this->Right;
         $tmp_q = "select `move` from `".TblModDealers."` order by move desc";
         $res = $this->Right->db_Query( $tmp_q );
         if( !$res )return false;
         $tmp_row = $this->Right->db_FetchAssoc();
         $move = $tmp_row['move'];
         $move=$move+1;
         $this->Form->Hidden( 'move', $move );
        }
        else $move=$mas[$this->lang_id]['move'];
        $this->Form->Hidden( 'move', $move );

        echo '<TR><TD COLSPAN=2 ALIGN=left>';
        $this->Form->WriteSaveAndReturnPanel( $this->script );
        $this->Form->WriteSavePanel( $this->script );
        $this->Form->WriteCancelPanel( $this->script );
        echo '</table>';
        AdminHTML::PanelSimpleF();
        AdminHTML::PanelSubF();

        $this->Form->WriteFooter();
        return true;
       } //end of fuinction edit


     /**
      * @programmer Bogdan Iglinsky
      * @return bool
      */
     function save()
       {
        $this->Form->Hidden( 'display', $this->display );
        $this->Form->Hidden( 'start', $this->start );
        $this->Form->Hidden( 'sort', $this->sort );
        $this->Form->Hidden( 'srch', $this->srch );
        $this->Form->Hidden( 'srch', $this->srch2 );
        $this->Form->Hidden( 'fltr', $this->fltr );
        $this->Form->Hidden( 'fltr2', $this->fltr2 );
        $this->Form->Hidden( 'fln', $this->fln );

        $q = "SELECT * FROM ".TblModDealers." WHERE `id`='$this->id'";
        $res = $this->Right->Query( $q, $this->user_id, $this->module );
        if( !$res OR !$this->Right->result) return false;
        $rows = $this->Right->db_GetNumRows();
        //echo '<br>$q='.$q.'$rows='.$rows;
        if($this->ko_x==0 || $this->ko_x==NULL){
            $this->ko_x=0;
        }
        if($this->ko_y==0 || $this->ko_y==NULL){
            $this->ko_y=0;
        }
        if( $rows>0 )   //--- update
        {
             $row = $this->Right->db_FetchAssoc();
             //Delete old image
//             echo '<br>$row[img]='.$row['img'].' $this->img='.$this->img;
             if ( !empty($row['img']) AND $row['img']!=$this->img) {
                $this->DelItemImage($row['img']);
             }

             $q = "update `".TblModDealers."` set
                  `group_d`='$this->group_d',
                  `city_d`='$this->city_d',
                  `img` = '$this->img',
                  `visible`='$this->visible',
                  `ko_x`='$this->ko_x',
                  `ko_y`='$this->ko_y',
                  `main_x`='$this->main_x',
                  `main_y`='$this->main_y',
                  `cenntral_ofis`='".$this->cenntral_ofis."',
                  `move`='$this->move' WHERE `id` = '$this->id'";
             $res = $this->Right->Query( $q, $this->user_id, $this->module );
 //            echo '<br>$q='.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
             if( !$res OR !$this->Right->result) return false;
        }
        else   //--- insert
        {
            $q = "insert into `".TblModDealers."`
            values(
            NULL,
            '$this->group_d',
            '$this->city_d',
            '$this->img',
            '$this->visible',
            '$this->cenntral_ofis',
            '$this->ko_x',
            '$this->ko_y',
            '$this->main_x',
            '$this->main_y',
            '$this->move')";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            //echo '<br>'.$q.' $res='.$res.' $this->Right->result='.$this->Right->result;
            if( !$res OR !$this->Right->result) return false;
        }

        if ( empty($this->id) ){
          $this->id = $this->Right->db_GetInsertID();
        }

          $q="select * from `".TblModDealerSprName."` where `cod`='$this->id'";
    $res = $this->Right->Query( $q, $this->user_id, $this->module );
    if( !$this->Right->result ) return false;
    $rows = $this->Right->db_GetNumRows();
    $_txst= array();
     for($i=0; $i<$rows;$i++)
     {
        $row = $this->Right->db_FetchAssoc();
        $_txst[$row['lang_id']]='1';
     }
     $ln_sys = new SysLang();
      $ln_arr = $ln_sys->LangArray( _LANG_ID );
      while( $el = each( $ln_arr ) )
      {
        //echo'$this->tel=';print_r($this->tel);
       $name_ = addslashes( strip_tags(trim($this->name[ $el['key'] ])) );
       if(empty($name_)){
        if($el['key']==2){
            if($this->group_d==1) $name_=addslashes('Офіс');
        if($this->group_d==2) $name_=addslashes('Склад');
        }
        if($el['key']==3){
            if($this->group_d==1) $name_=addslashes('Офис');
        if($this->group_d==2) $name_=addslashes('Склад');
        }
       }



          $name2_ = addslashes( strip_tags(trim($this->name2[ $el['key'] ])) );
       if(empty($name2_)){
        if($el['key']==2){
            if($this->group_d==1) $name2_=addslashes('Офіс');
        if($this->group_d==2) $name2_=addslashes('Склад');
        }
        if($el['key']==3){
            if($this->group_d==1) $name2_=addslashes('Офис');
        if($this->group_d==2) $name2_=addslashes('Склад');
        }
       }
//          var_dump($name2_);
       $tel_ = addslashes( trim($this->tel[ _LANG_ID ]));
       //echo '$tel='.$tel_;
       $email_ = addslashes( strip_tags(trim($this->email[ _LANG_ID ])) );
       $descr_ = addslashes( $this->content[ $el['key'] ] );
       $full_ = addslashes( $this->full[ $el['key'] ] );
       $lang_id = $el['key'];
       if (isset($_txst[$lang_id]))
              {

                 $q="update `".TblModDealerSprName."` set
                  `name`='".$name_."',
                  `name2`='".$name2_."',
                  `descr`='".$descr_."',
                  `tel`='".$tel_."',
                  `email`='".$email_."',
                  `full`='".$full_."'
                  WHERE `lang_id`='$lang_id' and `cod`='$this->id'";
                  $res = $this->Right->Query( $q, $this->user_id, $this->module );
                  //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
                  if( !$res ) return false;
                  if( !$this->Right->result ) return false;
              }
        else
        {
          $q="insert into `".TblModDealerSprName."`  set
                  `name`='".$name_."',
                  `name2`='".$name2_."',
                  `descr`='".$descr_."',
                  `tel`='".$tel_."',
                  `email`='".$email_."',
                  `full`='".$full_."',
                  `lang_id`='$lang_id',
                  `cod`='$this->id'";
          $res = $this->Right->Query( $q, $this->user_id, $this->module );
       //   echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
          if( !$this->Right->result) return false;
          }
      }
        return true;
       } //end of fuinction save()


     /**
      * @programmer Bogdan Iglinsky
      * @param $id_del
      * @return bool|int
      */
     function del( $id_del )
       {
           $kol = count( $id_del );
           $del = 0;
           for( $i=0; $i<$kol; $i++ )
           {
            $u = $id_del[$i];
            $q = "select * from ".TblModDealers." where id='$u'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            $row = $this->Right->db_FetchAssoc();
            if ( !empty($row['img']) ){
                if ( !$this->DelItemImage($row['img']) ) return false;
            }
            $q="DELETE FROM `".TblModDealers."` WHERE id='$u'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            if (!$res) return false;
            $q="DELETE FROM `".TblModDealerSprName."` WHERE cod='$u'";
            $res = $this->Right->Query( $q, $this->user_id, $this->module );
            if (!$res) return false;
            if ( $res )
             $del=$del+1;
            else
             return false;
           }
         return $del;
       } //end of function del()


     /**
      * @programmer Bogdan Iglinsky
      * @param $img
      * @return bool
      */
     function DelItemImage($img)
       {
           $q = "SELECT * FROM `".TblModDealers."` WHERE `img`='$img'";
           $res = $this->Right->Query( $q, $this->user_id, $this->module );
           //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;die;
           if( !$this->Right->result ) return false;
           $rows = $this->Right->db_GetNumRows();
           //echo '$rows='.$rows;
           if ($rows == 0) return false;
           $row = $this->Right->db_FetchAssoc();
           //echo '<br>$row'; print_r($row);die;


           $path = Dealer_Img_Full_Path;
           $path_file = $path.'/'.$row['img'];
           //echo '<br>$path='.$path.'<br>$path_file='.$path_file;
           // delete file which store in the database
           if (file_exists($path_file)) {
              $res = unlink ($path_file);
              if( !$res ) return false;
           }

           //echo '<br> $path='.$path;die;
           $handle = @opendir($path);
           //echo '<br> $handle='.$handle;//die;
           $cols_files = 0;
           while ( ($file = @readdir($handle)) !=false ) {
               //echo '<br> $file='.$file.' readdir($handle)='.readdir($handle);die;
               $mas_file=explode(".",$file);
               $mas_img_name=explode(".",$row['img']);
               if ( strstr($mas_file[0], $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT) and $mas_file[1]==$mas_img_name[1] ) {
                  $res = unlink ($path.'/'.$file);
                  if( !$res ) return false;
               }
               if ($file == "." || $file == ".." ) {
                   $cols_files++;
               }
           }
           //if ($cols_files==2) rmdir($path);
           @closedir($handle);

           $q = "UPDATE `".TblModDealers."` SET `img`=NULL WHERE `img`='$img'";
           $res = $this->Right->Query( $q, $this->user_id, $this->module );
           //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;die;
           if( !$this->Right->result ) return false;

           return true;
      } //end of function DelItemImage()


     /**
      * @programmer Bogdan Iglinsky
      * @param $table
      * @return bool
      */
     function up($table)
        {
         $q="select * from `$table` where `move`='$this->move'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;die;
         if( !$res )return false;
         $rows = $this->Right->db_GetNumRows();
         $row = $this->Right->db_FetchAssoc();
         $move_down = $row['move'];
         $id_down = $row['id'];


         $q="select * from `$table` where `move`>'$this->move'";
         if($this->fltr2!=NULL) $q.=" and `city_d`='$this->fltr2'";
         if($this->fltr!=NULL) $q.=" and `group_d`='$this->fltr'";
         $q.=" order by `move`";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;die;
         if( !$res )return false;
         $rows = $this->Right->db_GetNumRows();
         $row = $this->Right->db_FetchAssoc();
         $move_up = $row['move'];
         $id_up = $row['id'];

         //echo '<br> $move_down='.$move_down.' $move_up ='.$move_up;die;
         if( $move_down!=0 AND $move_up!=0 )
         {
         $q="update `$table` set
             `move`='$move_down' where `id`='$id_up'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;

         $q="update `$table` set
             `move`='$move_up' where `id`='$id_down'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;

         }
        } // end of function up()


     /**
      * @programmer Bogdan Iglinsky
      * @param $table
      * @return bool
      */
     function down($table)
        {
         $q="select * from `$table` where `move`='$this->move'";
         if($this->fltr2!=NULL) $q.=" and `city_d`='$this->fltr2'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;die;
         if( !$res )return false;
         $rows = $this->Right->db_GetNumRows();
         $row = $this->Right->db_FetchAssoc();
         $move_up = $row['move'];
         $id_up = $row['id'];


         $q="select * from `$table` where `move`<'$this->move'";
         if($this->fltr2!=NULL) $q.=" and `city_d`='$this->fltr2'";
         if($this->fltr!=NULL) $q.=" and `group_d`='$this->fltr'";
         $q.=" order by `move` desc";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
         if( !$res )return false;
         $rows = $this->Right->db_GetNumRows();
         $row = $this->Right->db_FetchAssoc();
         $move_down = $row['move'];
         $id_down = $row['id'];

         if( $move_down!=0 AND $move_up!=0 )
         {
         $q="update `$table` set
             `move`='$move_down' where `id`='$id_up'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;

         $q="update `$table` set
             `move`='$move_up' where `id`='$id_down'";
         $res = $this->Right->Query( $q, $this->user_id, $this->module );
         //echo '<br>q='.$q.' res='.$res.' $this->Rights->result='.$this->Rights->result;
         }
        } // end of function down()


     /**
      * @programmer Bogdan Iglinsky
      * @param null $id
      * @return null|string
      */
     function CheckFields($id = NULL)
       {
        $this->Err=NULL;
        if($this->is_goup)
        if (empty( $this->group_d)) {
            $this->Err = $this->Err.$this->multi['FLD_SELECT_GROUP'].'<br>';
        }
        if($this->is_city)
        if (empty( $this->city_d)) {
            $this->Err = $this->Err.$this->multi['MSG_FLD_CITY_EMPTY'].'<br>';
        }
/*
        if (empty( $this->name[$this->lang_id] )) {
            $this->Err = $this->Err.$this->lang['MSG_FLD_NAME_EMPTY'].'<br>';
        }*/

        //echo '<br>$this->Err='.$this->Err.' $this->Msg->table='.$this->Msg->table;
        return $this->Err;
       } //end of function CheckFields()


     /**
      * @programmer Bogdan Iglinsky
      *
      */
     function ShowErrBackEnd()
       {
         if ($this->Err){
           echo '
            <table border=0 cellspacing=0 cellpadding=0 class="err" align="center">
             <tr><td align="left">'.$this->Err.'</td></tr>
            </table>';
         }
       } //end of fuinction ShowErrBackEnd()


     /**
      * @programmer Bogdan Iglinsky
      * @param $img
      * @return bool
      */
     function GetImgTitle( $img )
        {
            $q = "SELECT * FROM `".TblModDealers."` WHERE `img`='$img'";
            $res = $this->db->db_Query($q);
            //echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$tmp_db->result;
            if ( !$res OR !$this->db->result ) return false;
            $row = $this->db->db_FetchAssoc();
            return $this->Spr->GetNameByCod(TblModDealerSprName, $row['id']);
        } //end of function GetImgTitle()


     /**
      * @programmer Bogdan Iglinsky
      * @param null $img
      * @param null $size
      * @param null $quality
      * @param null $wtm
      * @param null $parameters
      * @return bool
      */
     function ShowImageCity($img = NULL, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL)
        {
         $size_auto = NULL;
         $size_width = NULL;
         $size_height = NULL;
         if ( strstr($size,'size_auto') ) $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
         if ( strstr($size,'size_width') ) $size_width = substr( $size, strrpos($size,'=')+1, strlen($size) );
         if ( strstr($size,'size_height') ) $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );

         if (empty($quality)) $quality=100;

         $img_with_path = Dealer_Img_Path_City.$this->lang_id.'/'.$img;
         $img_full_path = Dealer_Img_Full_Path_City.$this->lang_id.'/'.$img;

         //$img_full_path = Spr_Img_Path.$spr.'/'.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
         //echo '<br> $img_full_path='.$img_full_path.'<br> $size_auto='.$size_auto;
         if ( !file_exists($img_full_path) ) return false;

         $thumb = new Thumbnail($img_full_path);

         if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
         if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
         if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height);
         if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto);		            // [OPTIONAL] set the biggest width and height for thumbnail

         $thumb->quality=$quality;                  //default 75 , only for JPG format
         //echo '<br>$wtm='.$wtm;
         if ( $wtm == 'img' ) {
            $thumb->img_watermark = SITE_PATH.'/images/design/m01.png';	    // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
            $thumb->img_watermark_Valing='CENTER';   	    // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
            $thumb->img_watermark_Haling='CENTER';   	    // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
         }
         if ( $wtm == 'txt' ) {
             if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=SPR_WATERMARK_TEXT;	    // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
             else $thumb->txt_watermark='';
             $thumb->txt_watermark_color='000000';	    // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
             $thumb->txt_watermark_font=5;	            // [OPTIONAL] set watermark text font: 1,2,3,4,5
             $thumb->txt_watermark_Valing='TOP';   	    // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
             $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
             $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
             $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels
         }

         $thumb->process();   	// generate image

         //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg
         $mas_img_name=explode(".",$img_with_path);
         $img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
         $img_src = $img_name_new;
         $img_full_path_new = SITE_PATH.$img_name_new;
         $uploaddir = SITE_PATH.substr($img_with_path, 0, strrpos($img_with_path,'/')).'/';


         $alt = $this->GetImgTitle( $img);
         $title = $this->GetImgTitle( $img);
         //echo '<br>$img_name_new='.$img_name_new;
         //echo '<br>$img_full_path_new='.$img_full_path_new;
         //echo '<br>$img_src='.$img_src;
         if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
         if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';

         if ( !file_exists($img_full_path_new) ){
             //echo '<br>$uploaddir='.$uploaddir.'<br>$img_full_path_new='.$img_full_path_new;
             chmod($uploaddir,0777);
             $thumb->save($img_full_path_new);
             chmod($uploaddir,0755);
             $params = "img=$img&amp;$size&amp;quality=$quality";
             //echo '<br> $params='.$params;

             /*?><img src="http://<?=NAME_SERVER.Dealer_Path;?>thumb_dealer.php?<?=$params;?>" <?=$parameters;?> ><?*/
             ?><img src="<?=$img_src;?>" <?=$parameters?>><?
         }
         else {
             ?><img src="<?=$img_src;?>" <?=$parameters?>><?
         }
         return;
        } // end of function ShowImage()


     /**
      * @programmer Bogdan Iglinsky
      * @param null $img
      * @param null $size
      * @param null $quality
      * @param null $wtm
      * @param null $parameters
      * @return bool
      */
     function ShowImage($img = NULL, $size = NULL, $quality = NULL, $wtm = NULL, $parameters = NULL)
        {
         $size_auto = NULL;
         $size_width = NULL;
         $size_height = NULL;
         if ( strstr($size,'size_auto') ) $size_auto = substr( $size, strrpos($size,'=')+1, strlen($size) );
         if ( strstr($size,'size_width') ) $size_width = substr( $size, strrpos($size,'=')+1, strlen($size) );
         if ( strstr($size,'size_height') ) $size_height = substr( $size, strrpos($size,'=')+1, strlen($size) );

         if (empty($quality)) $quality=100;

         $img_with_path = Dealer_Img_Path.$img;
         $img_full_path = Dealer_Img_Full_Path.$img;

         //$img_full_path = Spr_Img_Path.$spr.'/'.$img_with_path; // like z:/home/speakers/www/uploads/45/R1800TII_big.jpg
         //echo '<br> $img_full_path='.$img_full_path.'<br> $size_auto='.$size_auto;
         if ( !file_exists($img_full_path) ) return false;

         $thumb = new Thumbnail($img_full_path);

         if ( !empty($size_width ) and empty($size_height) ) $thumb->size_width($size_width);
         if ( !empty($size_height) and empty($size_width) ) $thumb->size_height($size_height);
         if ( !empty($size_width) and !empty($size_height) ) $thumb->size($size_width,$size_height);
         if ( !$size_width and !$size_height and $size_auto ) $thumb->size_auto($size_auto);		            // [OPTIONAL] set the biggest width and height for thumbnail

         $thumb->quality=$quality;                  //default 75 , only for JPG format
         //echo '<br>$wtm='.$wtm;
         if ( $wtm == 'img' ) {
            $thumb->img_watermark = SITE_PATH.'/images/design/m01.png';	    // [OPTIONAL] set watermark source file, only PNG format [RECOMENDED ONLY WITH GD 2 ]
            $thumb->img_watermark_Valing='CENTER';   	    // [OPTIONAL] set watermark vertical position, TOP | CENTER | BOTTOM
            $thumb->img_watermark_Haling='CENTER';   	    // [OPTIONAL] set watermark horizonatal position, LEFT | CENTER | RIGHT
         }
         if ( $wtm == 'txt' ) {
             if ( defined('WATERMARK_TEXT') ) $thumb->txt_watermark=SPR_WATERMARK_TEXT;	    // [OPTIONAL] set watermark text [RECOMENDED ONLY WITH GD 2 ]
             else $thumb->txt_watermark='';
             $thumb->txt_watermark_color='000000';	    // [OPTIONAL] set watermark text color , RGB Hexadecimal[RECOMENDED ONLY WITH GD 2 ]
             $thumb->txt_watermark_font=5;	            // [OPTIONAL] set watermark text font: 1,2,3,4,5
             $thumb->txt_watermark_Valing='TOP';   	    // [OPTIONAL] set watermark text vertical position, TOP | CENTER | BOTTOM
             $thumb->txt_watermark_Haling='LEFT';       // [OPTIONAL] set watermark text horizonatal position, LEFT | CENTER | RIGHT
             $thumb->txt_watermark_Hmargin=10;          // [OPTIONAL] set watermark text horizonatal margin in pixels
             $thumb->txt_watermark_Vmargin=10;           // [OPTIONAL] set watermark text vertical margin in pixels
         }

         $thumb->process();   	// generate image

         //make new image like R1800TII_big.jpg -> R1800TII_big_autozoom_100x84.jpg
         $mas_img_name=explode(".",$img_with_path);
         $img_name_new = $mas_img_name[0].SPR_ADDITIONAL_FILES_TEXT.intval($thumb->img['x_thumb']).'x'.intval($thumb->img['y_thumb']).'.'.$mas_img_name[1];
         $img_src = $img_name_new;
         $img_full_path_new = SITE_PATH.$img_name_new;
         $uploaddir = SITE_PATH.substr($img_with_path, 0, strrpos($img_with_path,'/')).'/';


         $alt = $this->GetImgTitle( $img);
         $title = $this->GetImgTitle( $img);
         //echo '<br>$img_name_new='.$img_name_new;
         //echo '<br>$img_full_path_new='.$img_full_path_new;
         //echo '<br>$img_src='.$img_src;
         if ( !strstr($parameters, 'alt') )  $parameters = $parameters.' alt="'.$alt.'"';
         if ( !strstr($parameters, 'title') ) $parameters = $parameters.' title=" '.$title.' "';

         if ( !file_exists($img_full_path_new) ){
             //echo '<br>$uploaddir='.$uploaddir.'<br>$img_full_path_new='.$img_full_path_new;
             chmod($uploaddir,0777);
             $thumb->save($img_full_path_new);
             chmod($uploaddir,0755);
             $params = "img=$img&amp;$size&amp;quality=$quality";
             //echo '<br> $params='.$params;

             /*?><img src="http://<?=NAME_SERVER.Dealer_Path;?>thumb_dealer.php?<?=$params;?>" <?=$parameters;?> ><?*/
             ?><img src="<?=$img_src;?>" <?=$parameters?>><?
         }
         else {
             ?><img src="<?=$img_src;?>" <?=$parameters?>><?
         }
         return;
        } // end of function ShowImage()


     /**
      * @programmer Bogdan Iglinsky
      * @param $translit
      * @return bool
      */
     function GetCodFoTranslit($translit){
         $dbr = new Rights;
         $q = "SELECT
         `".TblModDealerSprCity."`.*
         FROM
         `".TblModDealerSprCity."`
         WHERE
         `".TblModDealerSprCity."`.lang_id='".$this->lang_id."'
         and `".TblModDealerSprCity."`.translit='".$translit."'";
         $res = $dbr->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
         if ( !$res or !$dbr->result) return false;
         $rows = $dbr->db_GetNumRows();
         //echo '<br>rows='.$rows;
         $row=$dbr->db_FetchAssoc();
         return $row['cod'];
       }


     /**
      * @programmer Bogdan Iglinsky
      * @return bool|null
      */
     function GetDealers()
       {
         $dbr = new Rights;

         $q = "SELECT
         `".TblModDealerSprCity."`.*,
         `".TblModDealerSprCityFonImg."`.fon_img,
         `".TblModDealers."`.main_x,
         `".TblModDealers."`.main_y,
         `".TblModDealers."`.img,
         `".TblModDealers."`.group_d
         FROM
         `".TblModDealerSprCity."`
         left join
         `".TblModDealerSprCityFonImg."`
         on (`".TblModDealerSprCityFonImg."`.cod=`".TblModDealerSprCity."`.cod)
         left join
         `".TblModDealers."`
         on (`".TblModDealers."`.city_d=`".TblModDealerSprCity."`.cod)
         WHERE
         `".TblModDealerSprCity."`.lang_id='".$this->lang_id."'
         and `".TblModDealers."`.visible='1'
         ORDER BY `".TblModDealerSprCity."`.`move`,`".TblModDealers."`.`group_d` desc";
         $res = $dbr->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
         if ( !$res or !$dbr->result) return false;
         $rows = $dbr->db_GetNumRows();
         //echo '<br>rows='.$rows;
         $arr = NULL;
         $city=-1;
         $cnt=-1;
         $obekt=0;

         for( $i = 0; $i < $rows; $i++ )
         {
            $row=$dbr->db_FetchAssoc();
//             var_dump($row);
            if($row['cod']!=$city){
                $city=$row['cod'];
                $cnt++;
                $arr[$cnt]['cod'] =$row['cod'];
                $arr[$cnt]['name'] =$row['name'];
//                $arr[$cnt]['name2'] =$row['name2'];
                $arr[$cnt]['translit'] =$row['translit'];
                $arr[$cnt]['fon_img'] =$row['fon_img'];
                $obekt=0;
            }
            $arr[$cnt]['obj'][$obekt]['ko_x'] =($row['main_x']);
            $arr[$cnt]['obj'][$obekt]['ko_y'] =($row['main_y']);
            $arr[$cnt]['obj'][$obekt]['group_d'] =$row['group_d'];
            $arr[$cnt]['obj'][$obekt]['img'] =$row['img'];
            $obekt++;
         }

         return $arr;
       } //end of function GetDealers()


     /**
      * @programmer Bogdan Iglinsky
      * @return bool
      */
     function GetDealersFullMap(){
         $dbr = new Rights;
         $q = "SELECT
         `".TblModDealerSprCity."`.*
         FROM
         `".TblModDealerSprCity."`
         WHERE
         `".TblModDealerSprCity."`.lang_id='".$this->lang_id."'
          and `".TblModDealerSprCity."`.cod='".$this->cat."'";
         $res = $dbr->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
         if ( !$res or !$dbr->result) return false;
         $rows = $dbr->db_GetNumRows();
         //echo '<br>rows='.$rows;
         $arr = NULL;
         $row=$dbr->db_FetchAssoc();
         return $row['img'];
       }


     /**
      * @programmer Bogdan Iglinsky
      * @return bool|null
      */
     function GetDealersFullCoord()
       {
         $dbr = new Rights;

//         $q = "SELECT * FROM `".TblModDealers."` WHERE 1 AND `visible`='1' ORDER BY `move`";
         $q = "SELECT
         `".TblModDealers."`.*
         FROM
         `".TblModDealers."`
         WHERE
         `".TblModDealers."`.city_d='".$this->cat."'";

         $res = $dbr->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
         if ( !$res or !$dbr->result) return false;
         $rows = $dbr->db_GetNumRows();
         //echo '<br>rows='.$rows;
         $arr = NULL;
         for( $i = 0; $i < $rows; $i++ )
         {
            $row=$dbr->db_FetchAssoc();
            $arr[$i] = $row;
         }
         return $arr;
       } //end of fuinction GetDealers()


     /**
      * @programmer Bogdan Iglinsky
      * @return bool|null
      */
     function GetDealersContent( )
       {
         $dbr = new Rights;
         $q="SELECT
         `".TblModDealers."`.*,
         `".TblModDealerSprName."`.name as ofis_name,
         `".TblModDealerSprName."`.name2 as name2,
         `".TblModDealerSprName."`.descr,
         `".TblModDealerSprName."`.tel,
         `".TblModDealerSprName."`.email,
         `".TblModDealerSprName."`.full,
         `".TblModDealerSprCity."`.name as city_name,
         `".TblModDealerSprCity."`.translit as city_translit,
         `".TblModDealerSprCityFonImg."`.fon_img
         FROM
         `".TblModDealerSprName."`,
         `".TblModDealers."`
         left join
         `".TblModDealerSprCity."`
         left join
         `".TblModDealerSprCityFonImg."`
         on (`".TblModDealerSprCityFonImg."`.cod=`".TblModDealerSprCity."`.cod)
          on(`".TblModDealers."`.city_d=`".TblModDealerSprCity."`.cod and `".TblModDealerSprCity."`.lang_id='".$this->lang_id."')
         WHERE `".TblModDealerSprName."`.name!=''
         and `".TblModDealers."`.`visible`='1'
         and `".TblModDealerSprName."`.cod=`".TblModDealers."`.id
         and `".TblModDealerSprName."`.lang_id='".$this->lang_id."'";
         if($this->cat!=NULL)$q.=" and `".TblModDealerSprCity."`.cod='".$this->cat."'";
         $q.="ORDER BY ";
         if($this->cat==NULL)$q.="  `".TblModDealerSprCity."`.move,";
         $q.=" `".TblModDealers."`.group_d,`".TblModDealers."`.move";
         $res = $dbr->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
         if ( !$res or !$dbr->result) return false;
         $rows = $dbr->db_GetNumRows();
         //echo '<br>rows='.$rows;
         $arr = NULL;
         $city=-1;
         $cnt=-1;
         $obekt=0;
         for( $i = 0; $i < $rows; $i++ )
         {
            $row=$dbr->db_FetchAssoc();
            if($city!=$row['city_d'] || 1) {
                $city=$row['city_d'];
                $cnt++;
                $obekt=0;
                $arr[$cnt]['text']['cenntral_ofis']= $row['cenntral_ofis'];
                $arr[$cnt]['text']['city_name']= $row['city_name'];
                $arr[$cnt]['text']['translit']=$row['city_translit'];
                $arr[$cnt]['text']['cod']=$row['city_d'];
                $arr[$cnt]['text']['fon_img']=$row['fon_img'];
            }
            $arr[$cnt]['arr'][$obekt]['grup'] = $row['group_d'];
            $arr[$cnt]['arr'][$obekt]['name'] = $row['ofis_name'];
            $arr[$cnt]['arr'][$obekt]['name2'] = $row['name2'];
            $arr[$cnt]['arr'][$obekt]['adr'] = $row['descr'];
            $arr[$cnt]['arr'][$obekt]['tel'] = $row['tel'];
            $arr[$cnt]['arr'][$obekt]['email'] = $row['email'];
            $arr[$cnt]['arr'][$obekt]['full'] = $row['full'];
            $arr[$cnt]['arr'][$obekt]['img'] = $row['img'];
            $obekt++;
         }
         //print_r($arr);
         return $arr;
       } //end of fuinction GetDealers()
     /**
      * @programmer Bogdan Iglinsky
      * @param $translit
      * @return string
      */
     function GetHref($translit){
        $str=_LINK.'dealers/';
        $str.=$translit.'/';
        return $str;
       }


     /**
      * @programmer Bogdan Iglinsky
      * @return bool|null
      */
     function GetDealersShort( )
       {
         $dbr = new Rights;

//         $q = "SELECT * FROM `".TblModDealers."` WHERE 1 AND `visible`='1' ORDER BY `move`";
         $q = "SELECT `".TblModDealers."`.*,`".TblModDealerSprName."`.name,`".TblModDealerSprName."`.descr
         FROM `".TblModDealers."`,`".TblModDealerSprName."` WHERE
         `".TblModDealers."`.id='$id'
          AND `".TblModDealers."`.id=`".TblModDealerSprName."`.cod
          AND `".TblModDealerSprName."`.lang_id='".$this->lang_id."'
          AND `visible`='1' ORDER BY `move`";
         $res = $dbr->db_Query( $q );
         //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
         if ( !$res or !$dbr->result) return false;
         $rows = $dbr->db_GetNumRows();
         //echo '<br>rows='.$rows;
         $arr = NULL;
         for( $i = 0; $i < $rows; $i++ )
         {
            $row=$dbr->db_FetchAssoc();
            //echo '<br>$i = '.$i.' arr='.$row['city_d'];
             //$name = $this->Spr->GetNameByCod(TblModDealers, $row['name'], $this->lang_id, 1);
             $arr[$i]['id'] = $row['id'];
             $arr[$i]['name'] = stripcslashes($row['name']);
             $arr[$i]['descr'] = stripcslashes($row['descr']);
             $arr[$i]['full'] = stripcslashes($row['full']);
             $arr[$i]['img'] = $row['img'];
             $arr[$i]['city_d'] = $row['city_d'];
             $arr[$i]['group_d'] = $row['group_d'];
         }
         return $arr;
       } //end of fuinction GetDealersShort()


     /**
      * @programmer Bogdan Iglinsky
      * @param null $id
      * @param bool $more_parametrs
      * @return bool|null
      */
     function GetDataOfDealer($id=NULL,$more_parametrs=false)
       {
         $q = "SELECT `".TblModDealers."`.*,`".TblModDealerSprName."`.name,`".TblModDealerSprName."`.descr ,`".TblModDealerSprName."`.full
         FROM `".TblModDealers."`,`".TblModDealerSprName."` WHERE
         `".TblModDealers."`.id='$id'
          AND `".TblModDealers."`.id=`".TblModDealerSprName."`.cod
          AND `".TblModDealerSprName."`.lang_id='".$this->lang_id."'
          ";
         $res = $this->db->db_Query( $q );
//         echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
         if ( !$res or !$this->db->result) return false;
         $rows = $this->db->db_GetNumRows();
         //echo '<br>rows='.$rows;
         $arr = NULL;
         $row=$this->db->db_FetchAssoc();
         $arr['id'] = $row['id'];
         $arr['name'] = stripcslashes($row['name']);
         $arr['descr'] = stripcslashes($row['descr']);
         $arr['full'] = stripcslashes($row['full']);
         $arr['img'] = $row['img'];
         if ($more_parametrs)
         {
            if($this->is_goup) $arr['group_d'] = $this->Spr->GetNameByCod(TblModDealerSprGroup, $row['group_d'], $this->lang_id, 1);
            if($this->is_city) $arr['city_d'] = $this->Spr->GetNameByCod(TblModDealerSprCity, $row['city_d'], $this->lang_id, 1);
         }
         //print_r($arr);
         return $arr;
       } //end of fuinction GetDataOfDealer()


     /**
      * @programmer Bogdan Iglinsky
      * @param null $id
      * @param $q
      * @return null
      */
     function GetDataToArr( $id=NULL, $q )
       {
        $dbr = new Rights();
        $Spr = new SysSpr();
        $out_arr = NULL;

        if( $id ) $q = $q." and id='$id'";
        $res = $dbr->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
        if( !$res ) return $out_arr;

        $rows = $dbr->db_GetNumRows();
        //echo '<br>rows='.$rows;
        for( $i = 0; $i < $rows; $i++ )
        {
           $row=$dbr->db_FetchAssoc();
           //echo '<br>$i = '.$i.' arr='.$row['city_d'];
           //$out_arr[$i] = $row['city_d'];
           $out_arr[$i] = $Spr->GetNameByCod(TblModDealerSprCity, $row['city_d']);
        }
        //echo '<br> '.$out_arr['1'];
        return $out_arr;
       }


     /**
      * @programmer Bogdan Iglinsky
      * @param $id
      * @return bool
      */
     function GetNameOfDealer($id)
      {
        $q = "SELECT `".TblModDealerSprName."`.name
         FROM `".TblModDealers."`,`".TblModDealerSprName."` WHERE
         `".TblModDealers."`.id='$id'
          AND `".TblModDealers."`.id=`".TblModDealerSprName."`.cod
          AND `".TblModDealerSprName."`.lang_id='".$this->lang_id."'
          ";
         $res = $this->db->db_Query( $q );
        //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
        if( !$res ) return false;

        //$rows = $dbr->db_GetNumRows();
        $row=$this->db->db_FetchAssoc();
        return $row['name'];
      } // end of function GetNameOfDealer()


     /**
      * @param $title
      * @param null $cat
      * @return bool|string
      */
     function GetPathFoidCat($title,$cat=NULL){
        //echo '$ti='.$title.' $cst='.$cat;
        if($cat!=NULL){
            $q = "SELECT `".TblModDealerSprCity."`.name
             FROM `".TblModDealerSprCity."` WHERE
             `".TblModDealerSprCity."`.cod='".$cat."'
              AND `".TblModDealerSprCity."`.lang_id='".$this->lang_id."'";
             $res = $this->db->db_Query( $q );
             //echo '<br>$q='.$q.' $res='.$res.' $dbr->result='.$dbr->result;
             if( !$res ) return false;
             $row=$this->db->db_FetchAssoc();
             return '<a href="'._LINK.'dealers/">'.$title.'</a> / '.$row['name'];
        }
        else{
            return $title;
        }
      }

     /**
      * @programmer Bogdan Iglinsky
      * @param string $search_keywords
      * @return bool
      */
     function QuickSearch($search_keywords = ''){
         $search_keywords = stripslashes($search_keywords);

         $sel_table = NULL;
         $str_like = NULL;
         $filter_cr = ' OR ';

         $str_like = $this->build_str_like(TblModDealerSprName.'.name', $search_keywords);
         $str_like .= $filter_cr.$this->build_str_like(TblModDealerSprName.'.descr', $search_keywords);
         $str_like .= $filter_cr.$this->build_str_like(TblModDealerSprName.'.full', $search_keywords);
         $sel_table = "`".TblModDealers."`, `".TblModDealerSprName."`";

         $q ="SELECT
             `".TblModDealers."`.*,
             `".TblModDealerSprName."`.name,
             `".TblModDealerSprName."`.descr,
             `".TblModDealerSprName."`.full,
             `".TblModDealerSprName."`.link,
        (
            MATCH(`" . TblModDealerSprName . "`.`name`) AGAINST('*".$search_keywords."*' IN BOOLEAN MODE) * 10 +
            MATCH(`" . TblModDealerSprName . "`.`descr`) AGAINST('*".$search_keywords."*' IN BOOLEAN MODE) +
            MATCH(`" . TblModDealerSprName . "`.`full`) AGAINST('*".$search_keywords."*' IN BOOLEAN MODE)
        ) as `relev`
        FROM ".$sel_table."
        WHERE (".$str_like.")
        AND `".TblModDealerSprName."`.name!=''
             and `".TblModDealers."`.`visible`='1'
             and `".TblModDealerSprName."`.cod=`".TblModDealers."`.id
             and `".TblModDealerSprName."`.lang_id='".$this->lang_id."'
        ORDER BY `".TblModDealers."`.id, `".TblModDealers."`.move";

         $res = $this->db->db_Query( $q );
//   echo '<br>q='.$q.' res='.$res.' $tmp_db->result='.$this->db->result;
         if ( !$res) return false;
         if( !$this->db->result ) return false;
         $rows = $this->db->db_GetNumRows();
         if($rows==0) return false;
         // echo "<br> rows = ";
         //  print_r($rows);
         for($i=0; $i<$rows; $i++ ){
             $arr[$i] = $this->db->db_FetchAssoc();
         }
         return $arr;
     }

     /**
      * @programmer Bogdan Iglinsky
      * @param $find_field_name
      * @param $field_value
      * @return string
      */
     function build_str_like($find_field_name, $field_value){
         return " MATCH($find_field_name) AGAINST('*".$field_value."*' IN BOOLEAN MODE) ";
     } //end of function build_str_like()
 } // End of class Dealer