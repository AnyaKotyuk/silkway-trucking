<?php
// ================================================================================================
// System : SEOCMS
// Module : shop_settings.class.php
// Version : 1.0.0
// Date : 30.09.2008
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose : Class definition for all actions with settings of ShopExport
//
// ================================================================================================

include_once( SITE_PATH.'/modules/mod_shop_export/shop.defines.php' );

// ================================================================================================
//    Class             : shop_settings
//    Version           : 1.0.0
//    Date              : 30.09.2008
//
//    Constructor       : Yes
//    Parms             : session_id / session id
//                        usre_id    / UserID
//                        user_      /
//                        user_type  / id of group of user
//    Returns           : None
//    Description       : Class definition for all actions with managment of ShopExport
// ================================================================================================
//    Programmer        :  Ihor Trokhymchuk
//    Date              :  30.09.2008
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================
class Shop_settings extends ShopExport {
   var $name;
   var $company;
   var $url;
   var $delivery;
   var $currency;

   var $meta;
   var $bigmir;
   var $price_ua;
   var $marketgid;

   // ================================================================================================
   //    Function          : Shop_settings (Constructor)
   //    Version           : 1.0.0
   //    Date              : 21.03.2006
   //    Parms             : usre_id   / User ID
   //                        module    / module ID
   //                        sort      / field by whith data will be sorted
   //                        display   / count of records for show
   //                        start     / first records for show
   //                        width     / width of the table in with all data show
   //    Returns           : Error Indicator
   //
   //    Description       : Opens and selects a dabase
   // ================================================================================================
   function Shop_settings ($user_id=NULL, $module=NULL)
   {
        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );

        if (empty($this->db)) $this->db = DBs::getInstance();
        if (empty($this->Form)) $this->Form = check_init('FormCatalog', 'FrontForm', '"form_mod_market_export_settings"');
        if (empty($this->Right)) $this->Right = check_init('RightsOrder', 'Rights', '"' . $this->user_id . '", "' . $this->module . '"');
        if (empty($this->Msg)) $this->Msg = check_init('ShowMsg', 'ShowMsg');
        if (empty($this->Spr)) $this->Spr = check_init('SysSpr', 'SysSpr', '"' . $this->user_id . '", "' . $this->module . '"');

   } // End of Catalog_settings Constructor

   // ================================================================================================
   // Function : ShowSettings()
   // Returns : true,false / Void
   // Description : show setting of ShopExport
   // Programmer : Alex Kerest
   // Date : 1.10.2008
   // ================================================================================================
   function ShowSettings()
   {
       //  $Panel = new Panel();
       $ln_sys = new SysLang();

       $script = $_SERVER['PHP_SELF'].'?module='.$this->module;

       $q="select * from `".TblModShopSet."` where 1";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       if( !$this->Right->result ) return false;
       $row = $this->Right->db_FetchAssoc();

       // Write Form Header
       $this->Form->WriteHeader( $script );
       AdminHTML::PanelSimpleH();

       $q_spr1 = "select * from sys_func order by id desc";
       $res_spr1 = $this->Right->Query($q_spr1, $this->user_id, $this->module);
       $rows_spr1 = $this->Right->db_GetNumRows();
       $mas1['']='';
       for($i=0; $i<$rows_spr1; $i++){
           $row_spr1=$this->Right->db_FetchAssoc();
           $mas1[$row_spr1['id']] = $this->Spr->GetNameByCod( TblSysSprFunc, $row_spr1['id'] );
           if (!empty($row_spr1['name']))
                $mas1[$row_spr1['id']]=$mas1[$row_spr1['id']].' ('.$row_spr1['name'].')';
       }

       ?>
         <TR valign="top">
          <TD width="200">
           <table border="0" cellspacing="1" cellpading="0" width="200" class="EditTable">
            <tr>
             <td colspan="2"><b><?=$this->Msg->show_text('TXT_MARKET_USED_PROPS')?></b></td>
            </tr>
            <tr class=tr1>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_DELIVERY');?>
             <td><?$this->Form->CheckBox( "delivery", '', $row['delivery'] );?>
            </tr>
            <tr class=tr2>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_CURRENCY');?>
             <td><?$this->Form->CheckBox( "currency", '', $row['currency'] );?>
            </tr>
            <tr class=tr1>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_NADAVI');?>
             <td><?$this->Form->CheckBox( "nadavi", '', $row['nadavi'] );?>
            </tr>
            <tr class=tr2>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_META');?>
             <td><?$this->Form->CheckBox( "meta", '', $row['meta'] );?>
            </tr>
            <tr class=tr1>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_BIGMIR');?>
             <td><?$this->Form->CheckBox( "bigmir", '', $row['bigmir'] );?>
            </tr>
            <tr class=tr2>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_EKATALOG');?>
             <td><?$this->Form->CheckBox( "e-katalog", '', $row['e-katalog'] );?>
            </tr>
            <tr class=tr1>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_HOTPRICE');?>
             <td><?$this->Form->CheckBox( "hotprice", '', $row['hotprice'] );?>
            </tr>
            <tr class=tr2>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_HOTLINE');?>
             <td><?$this->Form->CheckBox( "hotline", '', $row['hotline'] );?>
            </tr>
            <tr class=tr1>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_PAY_UA');?>
             <td><?$this->Form->CheckBox( "pay_ua", '', $row['pay_ua'] );?>
            </tr>
            <tr class=tr2>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_PRICE_UA');?>
             <td><?$this->Form->CheckBox( "price_ua", '', $row['price_ua'] );?>
            </tr>
            <tr class=tr1>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_YANDEX');?>
             <td><?$this->Form->CheckBox( "yandex", '', $row['yandex'] );?>
            </tr>
            <tr class=tr2>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_I_UA');?>
             <td><?$this->Form->CheckBox( "iua", '', $row['iua'] );?>
            </tr>
            <tr class=tr1>
             <td align="left"><?=$this->Msg->show_text('TXT_MARKET_MARKETGID');?>
             <td><?$this->Form->CheckBox( "marketgid", '', $row['marketgid'] );?>
            </tr>

           </table>
          </TD>
          <TD></TD>
          <TD>
           <table border=0 cellspacing=1 cellpading=0 class="EditTable">
            <tr>
             <td colspan=2><b><?=$this->Msg->show_text('TXT_MARKET_SHOP_DATA')?>:</b></td>
            </tr>
            <tr>
             <td>
              <?
                echo "\n <table border=0 class='EditTable' width='100%'>";

                  echo "\n<tr><td><b>".$this->Msg->show_text('TXT_MARKET_FLD_NAME').":</b></td>";
                  echo "\n<td>";
                   $this->Form->TextBox( 'name', stripslashes($row['name']), 30 );
                  echo "\n<tr><td><b>".$this->Msg->show_text('TXT_MARKET_FLD_COMPANY').":</b></td>";
                  echo "\n<td>";
                  $this->Form->TextBox( 'company', stripslashes($row['company']), 30 );
                  echo "\n<tr><td><b>".$this->Msg->show_text('TXT_MARKET_FLD_URL').":</b></td>";
                  echo "\n<td>";
                  $this->Form->TextBox( 'url', stripslashes($row['url']), 30 );
                  echo "\n</table>";
                 ?>
             </td>
            </tr>
           </table>
          </TD>
         </TR>
       <?
       AdminHTML::PanelSimpleF();
       $this->Form->WriteSavePanel( $script );
       //$this->Form->WriteCancelPanel( $script );

       //AdminHTML::PanelSubF();

       $this->Form->WriteFooter();
       return true;
   } //end of function ShowSettings()

   // ================================================================================================
   // Function : SaveSettings()
   // Version : 1.0.0
   // Date : 1.10.2008
   // Parms :
   // Returns : true,false / Void
   // Description : show setting of ShopExport
   // Programmer : Alex Kerest
   // ================================================================================================
   function SaveSettings()
   {
       $q="SELECT * FROM `".TblModShopSet."` WHERE 1";
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       if( !$this->Right->result ) return false;
       $rows = $this->Right->db_GetNumRows();

       if($rows>0){
           $q="UPDATE `".TblModShopSet."` SET
              `name`='".$this->name."',
              `company`='".$this->company."',
              `url`='".$this->url."',
              `delivery`='".$this->delivery."',
              `currency`='".$this->currency."',
              `nadavi`='".$this->nadavi."',
              `meta`='".$this->meta."',
              `bigmir`='".$this->bigmir."',
              `price_ua`='".$this->price_ua."',
              `marketgid`='".$this->marketgid."',
              `yandex`='".$this->yandex."',
              `hotline`='".$this->hotline."',
              `e-katalog`='".$this->ekatalog."',
              `hotprice`='".$this->hotprice."',
              `pay_ua`='".$this->pay_ua."',
              `iua`='".$this->iua."'
              ";
       }
       else{
           $q="INSERT INTO `".TblModShopSet."` SET
              `name`='".$this->name."',
              `company`='".$this->company."',
              `url`='".$this->url."',
              `delivery`='".$this->delivery."',
              `currency`='".$this->currency."',
              `nadavi`='".$this->nadavi."',
              `meta`='".$this->meta."',
              `bigmir`='".$this->bigmir."',
              `price_ua`='".$this->price_ua."',
              `marketgid`='".$this->marketgid."',
              `yandex`='".$this->yandex."',
              `hotline`='".$this->hotline."',
              `e-katalog`='".$this->ekatalog."',
              `hotprice`='".$this->hotprice."',
              `pay_ua`='".$this->pay_ua."',
              `iua`='".$this->iua."'
              ";
       }
       $res = $this->Right->Query( $q, $this->user_id, $this->module );
       //echo '<br>q='.$q.' res='.$res.' $this->Right->result='.$this->Right->result;
       if( !$res OR !$this->Right->result ) return false;

       return true;
   } // end of function SaveSettings()

} //end of class Catalog_settings
