<?php

/**
 * Class informerLayout
 * author bogdan iglinsky
 */
class informerLayout extends informer {

    /**
     *
     * author bogdan iglinsky
     */
    function __construct(){
        if (empty($this->db)) $this->db = DBs::getInstance();
        $this->lang_id = _LANG_ID;
        if (empty($this->multi))
            $this->multi = check_init_txt('TblFrontMulti', TblFrontMulti);
    }

    /**
     * @return bool
     * author bogdan iglinsky
     */
    function showMainSlider(){
        $arr = $this->getMainSlider();
//        var_dump($arr);
        if(empty($arr)) return false;
        echo View::factory('/modules/mod_informer/templates/tpl_informer_main_slider.php')
            ->bind('arr', $arr);
    }

    /**
     * @return bool
     * author bogdan iglinsky
     */
    function showSocial(){
        $arr = $this->getSocial();
        if(empty($arr)) return false;
        echo View::factory('/modules/mod_informer/templates/tpl_informer_social.php')
            ->bind('arr', $arr);
    }
}
?>