<?
// ================================================================================================
// System : PrCSM05
// Module : sysAuthorization.class.php
// Version : 1.0.0
// Date : 09.02.2005
// Licensed To:
// Igor Trokhymchuk ihoru@mail.ru
// Andriy Lykhodid las_zt@mail.ru
//
// Purpose :
//
// ================================================================================================

// ================================================================================================
//    Class             : Panel
//    Version           : 1.0.0
//    Date              : 09.02.2005
//    Constructor       : Yes
//    Returns           : None
//    Description       : Panel
// ================================================================================================
//    Programmer        :  Andriy Lykhodid
//    Date              :  09.02.2005
//    Reason for change :  Creation
//    Change Request Nbr:  N/A
// ================================================================================================

class PanelLTE{


    function Panel()
    {
        /*?>
        <link id="luna-tab-style-sheet" type="text/css" rel="stylesheet" href="http://<?=NAME_SERVER?>/sys/js/tabpane/css/luna/tab.css" />
        <script type="text/javascript" src="http://<?=NAME_SERVER?>/sys/js/tabpane/tabpane.js"></script>
        <?*/
    }


    // ================================================================================================
    // Function : WritePanelHead()
    // Version : 1.0.0
    // Date : 09.02.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Write Panels
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 09.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================


    function WritePanelHead( $id_prefix = NULL, $arr_tabs_title=NULL, $active_tab_id=NULL )
    {
        $this->id_prefix = $id_prefix;
        ?>
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
        <?php
        if(is_array($arr_tabs_title)){
            ?><ul class="nav nav-tabs"><?php
            foreach($arr_tabs_title as $key => $title){
                if($key==$active_tab_id){
                    $class = 'class="active"';
                }else{
                    $class = '';
                }
                ?>
                <li <?php echo $class;?>>
                    <a href="#<?php echo $this->id_prefix.$key;?>" data-toggle="tab" ><?php echo $title;?></a>
                </li>
            <?php
            }
            ?></ul><?php
        }
        ?><div class="tab-content"><?php

    }


    // ================================================================================================
    // Function : WritePanelFooter()
    // Version : 1.0.0
    // Date : 09.02.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Write Panel Footer
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 09.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================


    function WritePanelFooter()
    {
        ?>
        </div>
        </div>
        <!-- nav-tabs-custom -->
    <?
    }


    // ================================================================================================
    // Function : WriteItemHeader
    // Version : 1.0.0
    // Date : 09.02.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Write Item Footer
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 09.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================


    function WriteItemHeader( $name_tab, $id=NULL, $active_tab_id=NULL )
    {
        //echo '<br>$id='.$id;
        if($id == $active_tab_id){
            $class = 'active';
        }else{
            $class = '';
        }
        ?>
        <div class="tab-pane <?php echo $class;?>" id="<?php echo $this->id_prefix.$id;?>">
    <?
    }


    // ================================================================================================
    // Function : WriteItemFooter
    // Version : 1.0.0
    // Date : 09.02.2005
    // Parms :
    // Returns : true,false / Void
    // Description : Write Item Footer
    // ================================================================================================
    // Programmer : Andriy Lykhodid
    // Date : 09.02.2005
    // Reason for change : Reason Description / Creation
    // Change Request Nbr:
    // ================================================================================================


    function WriteItemFooter()
    {
        ?>
        </div>
        <!-- /.tab-content -->
    <?
    }

} // end of class
?>