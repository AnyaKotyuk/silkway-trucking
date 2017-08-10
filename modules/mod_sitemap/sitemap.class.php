<?php
/**
* sitemap.class.php
* Class definition for all actions managment of sitemap XML on back-end
* @package Sitemap Package of SEOCMS
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 28.07.2011
* @copyright (c) 2010+ by SEOTM
*/

/**
* Class SiteMap
* parent class of sitemap XML module
* @author Igor Trokhymchuk  <ihor@seotm.com>
* @version 1.1, 28.07.2011
*/
class SiteMap
{
    var $user_id = NULL;
    var $module = NULL;
    var $lang_id = NULL;
    var $Err = NULL;

    var $Rights = NULL;
    var $Form = NULL;
    var $multi = NULL;

    /**
    * Constructor SiteMap
    * Init variables
    * @param integer $user_id - id of the user
    * @param integer $module - id of the module
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.1, 28.07.2011
    */
    function __construct($user_id=NULL, $module=NULL) {
        //Check if Constants are overrulled
        ( $user_id   !="" ? $this->user_id = $user_id  : $this->user_id = NULL );
        ( $module   !="" ? $this->module  = $module   : $this->module  = NULL );

        if(defined("_LANG_ID")) $this->lang_id = _LANG_ID;

        if(empty($this->Rights)) $this->Rights = check_init('RightsSitemap', 'Rights', "'$this->user_id', '$this->module'");
        if(empty($this->Form)) $this->Form = check_init('FormSitemap', 'Form', "'frm_sitemap'");
        if(empty($this->multi)) $this->multi = check_init_txt('TblBackMulti',TblBackMulti);

    } // End of Sitemap Constructor

    /**
    * Class method Show
    * function for show form for generate sitemap XML
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 28.07.2011
    */
    function Show()
    {
       $script = '/modules/mod_sitemap/sitemap.backend.php';

       //Write Form Header
       $this->Form->WriteHeader( $script );
       AdminHTML::PanelSimpleH();
       ?>
       <table border=0 cellspacing=1 cellpading=0 width="100%" class="EditTable">
        <tr>
         <td><b><?/*=$this->Msg->show_text('TXT_EXPORT_DATA')*/?></b></td>
        </tr>
        <tr>
          <td>
            <div><?=$this->multi['TXT_SITEMAP_TITLE'];?>:&nbsp;&nbsp; <a href="#" onclick="GenerateMap('<?=$script?>', 'module=<?=$this->module;?>&task=save_xml', 'result'); return false;"><?=$this->multi['TXT_SITEMAP_GENERATE'];?></a></div>
          </td>
        </tr>
        <tr>
          <td><div id="result"></div></td>
        </tr>
       </table>
       <script>
        //<![CDATA[
        function GenerateMap(uri, data, div_id){
            Did = "#"+div_id;
            $.ajax
            ({
                url: uri,
                type: 'POST',
                data: data,
                success:function(msg){
                    $(Did).html(msg);
                },
                beforeSend: function() {
                    $(Did).html('<img src="/admin/images/ajax-loader.gif" align="left" />');
                }
            });
        }
        //]]>
       </script>
       <?
       AdminHTML::PanelSimpleF();
       //$this->Form->WriteSavePanel( $script );
       //$this->Form->WriteCancelPanel( $script );
       //AdminHTML::PanelSubF();
       $this->Form->WriteFooter();
       return;
    } // end of function Show()


    /**
    * Class method MAP_XML
    * function for generate sitemap XML
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 28.07.2011
    */
    function MAP_XML()
    {
        //$ln_sys = check_init('SysLang', 'SysLang');
        $ln_sys = new SysLang();
        $def_lang = $ln_sys->GetDefFrontLangID();
        $ln_arr = $ln_sys->LangArray( $def_lang , 'def_front' );
        $str =
        '<?xml version="1.0" encoding="UTF-8"?>
        <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        ';
        //print_r($ln_arr);
        while( $el = each( $ln_arr ) ){
            $lang_id = $el['key'];
            $lang = $el['value'];
//            echo '<br>$def_lang='.$def_lang.' $lang_id='.$lang_id;
            if( $def_lang == $lang_id){
                //$lang_prefix = '/';
                $lang_prefix = '';
            }else{
                //$lang_prefix = '/'.$ln_sys->GetLangShortName($lang_id).'/';
                $lang_prefix = '/'.$ln_sys->GetLangShortName($lang_id);
            }
            $this->href ="http://".NAME_SERVER.$lang_prefix;
//            echo '<br />$this->href='.$this->href;
            if(defined("MOD_PAGES") AND MOD_PAGES ) $str .= $this->BuildDynamicPageMap(0, $lang_id);

            if(defined("MOD_CATALOG") AND MOD_CATALOG ) $str .= $this->BuildCatalogPageMap(0, $lang_id, $lang_prefix);

            if(defined("MOD_NEWS") AND MOD_NEWS ) $str .= $this->BuildNewsPageMap( $lang_id);
            if(defined("MOD_ARTICLE") AND MOD_ARTICLE ) $str .= $this->BuildArticlePageMap( $lang_id);
            if(defined("MOD_VIDEO") AND PAGE_VIDEO) $str.=$this->BuildVideoPageMap($lang_id);
            if(defined("MOD_GALLERY") AND PAGE_GALLERY)$str.=$this->BuildGalleryPageMap($lang_id);

            //echo '<br>$str='.$str;
        }
        $str .='</urlset>';
        //echo $str;
        $filename = SITE_PATH."/sitemap.xml";
        $hhh = fopen($filename, "w");
        if(fwrite($hhh, $str)) echo "<br/><a href='/sitemap.xml' target='_blank'>sitemap.xml</a> ".$this->multi['TXT_SITEMAP_GENERATED_OK'];
        fclose($hhh);
        return true;
    } // end of function  MAP_XML()

    /**
    * Class method bildUrl
    * function for build URL for every page for sitemap XML
    * @param $href - link to the page
    * @param $priority - priority of page
    * @param $date - date of page creation or change
    * @param $changefreq - frequency of page changes
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 28.07.2011
    */
    function bildUrl($href,$priority='0.8',$date,$changefreq='daily')
    {
        if($href==$this->href.'/'){
            $priority = '1';
        }
        return
         '<url>
            <loc>'.$href.'</loc>
            <priority>'.$priority.'</priority>
            <lastmod>'.$date.'</lastmod>
            <changefreq>'.$changefreq.'</changefreq>
         </url>
         ';
    }// end of function bildUrl()

    /**
    * Class method BuildDynamicPageMap
    * function for generate links to all Dynamic Pages for sitemap XML
    * @param $level - level of the page
    * @param $lang_id - id of the language
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 28.07.2011
    */
    function BuildDynamicPageMap($level=0, $lang_id)
    {
        $Pg = check_init('FrontendPages', 'FrontendPages');
        $Pg->lang_id = $lang_id;
        $priority = '0.8';
        $str = NULL;

        $q = "SELECT `".TblModPages."`.*
        FROM `".TblModPages."`, `".TblModPagesTxt."`
        WHERE `".TblModPages."`.`level`='".$level."'
        AND `".TblModPages."`.`visible`='1'
        AND `".TblModPages."`.`publish`='1'
        AND `".TblModPages."`.`id`=`".TblModPagesTxt."`.`cod`
        AND `".TblModPagesTxt."`.`lang_id`='".$Pg->lang_id."'
        AND `".TblModPagesTxt."`.`pname`!=''
        ORDER BY `".TblModPages."`.`move` asc";
        $res = $this->Rights->Query($q, $this->user_id, $this->module);
        //echo '<br>$q='.$q.' $res='.$res.' $this->Rights->result='.$this->Rights->result;
        if ( !$res OR !$this->Rights->result ) return false;
        $rows = $this->Rights->db_GetNumRows();
        //echo '<br>$rows='.$rows;
        $row0 = array();
        for($i=0; $i<$rows; $i++){
            $row0[$i] = $this->Rights->db_FetchAssoc();
        }
        $cnt = count($row0);
        for($i=0; $i<$cnt; $i++){
            $row = $row0[$i];
            $link = $Pg->Link($row['id'], 0);
            //echo '<br />$link='.$link;
            if(!strstr($link,'http')) $href = $this->href.$link;
            else $href = $link;
            //echo '<br />$href='.$href;
            $str .= $this->bildUrl($href,$priority,date("c"));
            $str .= $this->BuildDynamicPageMap($row['id'], $lang_id);
        } //end for

        return $str;
    } //end of function BuildDynamicPageMap()

    /**
     * Class method BuildDynamicPageMap
     * function for generate links to all Catalog pages for sitemap XML
     * @param $level - level of the page
     * @param $lang_id - id of the language
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     * @version 1.0, 19.11.2015
     */
    function BuildCatalogPageMap($level=0, $lang_id, $lang_prefix)
    {
        //echo '<br>$lang_id='.$lang_id;
        $Catalog = check_init('CatalogLayout', 'CatalogLayout');
        $Catalog->lang_id = $lang_id;
        $Catalog->treeCatData='';
        $Catalog->treeCatLevels='';

        $Catalog->loadTree();
        $priority = '0.8';
        $str_result_xml = NULL;


        $arr_cat = $Catalog->GetTreeCatLevel($level);
        /*?><pre><?var_dump($Catalog->treeCatData);?></pre><?*/
        if( !is_array($arr_cat) ) return false;
        $rows = count($arr_cat);
        //echo '<br>$level='.$level.' $rows='.$rows.' $arr_cat=';print_r($arr_cat);
        if ( $rows==0) return false;
        $keys = array_keys($arr_cat);
        for ($i=0; $i<$rows; $i++) {
            $row_cat = $Catalog->treeCatData[$keys[$i]];
            if($row_cat['id']==0){
                continue;
            }
            //echo '<br>$row_cat[path]='.$row_cat['path'];
            $link = $Catalog->getUrlByTranslit($row_cat['path'], NULL, $this->lang_id, $lang_prefix ); // $Catalog->Link($row_cat['id']);
            //echo '<br>$link='.$link;

            $href = $this->href.$link;
            //echo '<br>$href='.$href;

            $str_item = $this->bildUrl($href,$priority,date("c"));
            //echo '<br>$str_item='.$str_item;

            $str_result_xml .= $str_item;
            //echo '<br><pre>$str_result_xml='.$str_result_xml.'</pre>';

            //----------------- show subcategory ----------------------------
            $str_result_xml .=$this->BuildCatalogPageMap($row_cat['id'], $lang_id, $lang_prefix);
            //---------------------------------------------------------------
            //echo '<br>$str='.$str;

            //----------------- show content of the level ----------------------
            $arr_prop = $Catalog->GetListPositionsSortByDate($row_cat['id'], 'nolimit',  'move', 'asc', false, NULL);
            //var_dump($arr_prop);
            $j_rows = count($arr_prop);

            //echo '<br>$lang_id='.$lang_id;
            //echo '<br>----------------------------------';
            for ($j=0; $j<$j_rows; $j++) {
                $row_prop = $arr_prop[$j];
                //             var_dump($row_cat['path']);
                $link = $Catalog->getUrlByTranslit($row_cat['path'], $row_prop['translit'], $this->lang_id, $lang_prefix);
                //             echo '<br/>$link='.$link;
                $href = $this->href.$link;
                $str_result_xml .= $this->bildUrl($href,$priority,date("c"));
            }

        }//end for

        return $str_result_xml;
    } // end of function BuildCatalogPageMap

    /**
    * Class method BuildNewsPageMap
    * function for generate links to all News pages for sitemap XML
    * @param $lang_id - id of the language
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 28.07.2011
    */
    function BuildNewsPageMap($lang_id)
    {
         $News = check_init('NewsLayout', 'NewsLayout');
         $News->lang_id = $lang_id;
         $priority='0.8';
         $str = NULL;

         $href = $this->href."/news/";
         $str .= $this->bildUrl($href,$priority,date("c"));

         $q = "SELECT *
         FROM `".TblModNewsCat."`
         WHERE `lang_id`='".$lang_id."'
         AND `name`!=''
         ORDER BY `move` ASC ";
         $res = $this->Rights->Query( $q, $this->user_id, $this->module );
         $rows = $this->Rights->db_GetNumRows();
         //echo "<br> q=".$q." res=".$res." rows=".$rows;
         for( $i = 0; $i < $rows; $i++ )
         {
            $arr[$i] = $this->Rights->db_FetchAssoc();
         }
         for( $i = 0; $i < $rows; $i++ )
         {
               $row = $arr[$i];
               $name = $row['name'];
               $q1 = "SELECT `".TblModNews."`.* ,`".TblModNewsNames."`.*
               FROM `".TblModNews."`, `".TblModNewsNames."`
               WHERE `".TblModNews."`.`id_cat`='".$row['cod']."'
               AND `".TblModNews."`.`status`!='i'
               AND `".TblModNews."`.`id`=`".TblModNewsNames."`.`id_news`
               AND `".TblModNewsNames."`.`lang_id`='".$News->lang_id."'
               AND `".TblModNewsNames."`.`name`!=''
               ORDER BY `".TblModNews."`.`display` asc";
               $res1 = $News->db->db_Query( $q1 );
               $rows1 = $News->db->db_GetNumRows();
               //echo "<br> q=".$q1." res=".$res1." rows=".$rows1;
               if( $rows1 )
               {
                    $link = $News->Link($row['cod'],NULL);
                    $href = $this->href.$link;
                    $str .= $this->bildUrl($href,$priority,date("c"));

                    for( $j = 0; $j < $rows1; $j++ )
                    {
                        $arr1[$j] = $News->db->db_FetchAssoc();
                    }

                    for( $j = 0; $j < $rows1; $j++ )
                    {
                        $row1 = $arr1[$j];
                        $link = $News->Link($row1['id_cat'],$row1['link']);
                        $href = $this->href.$link;
                        $str .= $this->bildUrl($href,$priority,date("c"));
                    }
               }
         }
         return $str;
    }// end of function BuildNewsPageMap()

    /**
    * Class method BuildArticlePageMap
    * function for generate links to all Article pages for sitemap XML
    * @param $lang_id - id of the language
    * @author Igor Trokhymchuk  <ihor@seotm.com>
    * @version 1.0, 28.07.2011
    */
    function BuildArticlePageMap($lang_id)
    {
        $Article = check_init('ArticleLayout', 'ArticleLayout');
        $Article->lang_id = $lang_id;
        $priority='0.8';
        $str = NULL;

        $href =$this->href."/article/";
        $str .= $this->bildUrl($href,$priority,date("c"));

        $q = "SELECT *
        FROM `".TblModArticleCat."`
        WHERE `lang_id`='"._LANG_ID."'
        AND `name`!=''
        ORDER BY `move` ASC ";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        $rows = $this->Rights->db_GetNumRows();

        for( $i = 0; $i < $rows; $i++ )
        {
            $arr[$i] = $this->Rights->db_FetchAssoc();
        }

        for( $i = 0; $i < $rows; $i++ )
        {
            $row = $arr[$i];
            $name = $row['name'];
            //echo '<br>$name='.$name;
            $q1 = "SELECT `".TblModArticle."`.*
            FROM `".TblModArticle."`, `".TblModArticleTxt."`
            WHERE `".TblModArticle."`.`category`='".$row['cod']."'
            AND `".TblModArticle."`.`status`!='i'
            AND `".TblModArticle."`.`id`=`".TblModArticleTxt."`.`cod`
            AND `".TblModArticleTxt."`.`lang_id`='".$Article->lang_id."'
            AND `".TblModArticleTxt."`.`name`!=''
            ORDER BY `".TblModArticle."`.`position` asc";
            $res1 = $Article->db->db_Query( $q1 );
            $rows1 = $Article->db->db_GetNumRows();
            for( $j = 0; $j < $rows1; $j++ )
            {
                $arr1[$j] = $Article->db->db_FetchAssoc();
            }
            //echo '<br>q1='.$q1.' $res1='.$res1.' $Article->db->result='.$Article->db->result.' $rows1='.$rows1;
            if( $rows1 )
            {
                $link = $Article->Link($row['cod']);
                $href = $this->href.$link;
                $str .= $this->bildUrl($href,$priority,date("c"));

                for( $j = 0; $j < $rows1; $j++ )
                {
                  $row1 = $arr1[$j];
                  //echo '<br>$row1[id]='.$row1['id'];
                  $link = $Article->Link($row1['category'], $row1['id']);
                  $href = $this->href.$link;
                  $str .= $this->bildUrl($href,$priority,date("c"));
                }
             }
        }
        return $str;
    }// end of function BuildArticlePageMap()

    /**
     * Class method BuildVideoPageMap
     * function for generate links to all Clients pages for sitemap XML
     * @param $lang_id - id of the language
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @version 1.0, 17.07.2012
     */
    function BuildVideoPageMap($lang_id)
    {
        $Video= check_init('VideoLayout', 'VideoLayout');
        $Video->lang_id = $lang_id;
        $priority='0.8';
        $str = NULL;

        $href =$this->href."/video/";
        $str .= $this->bildUrl($href,$priority,date("c"));

        $q = "SELECT *
        FROM `".TblModVideoCat."`
        WHERE `lang_id`='".$Video->lang_id."'
        AND `name`!=''
        ORDER BY `move` ASC ";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        $rows = $this->Rights->db_GetNumRows();

        for( $i = 0; $i < $rows; $i++ )
        {
            $arr[$i] = $this->Rights->db_FetchAssoc();
        }

        for( $i = 0; $i < $rows; $i++ )
        {
            $row = $arr[$i];
            $name = $row['name'];
            //echo '<br>$name='.$name;
            $q1 = "SELECT `".TblModVideoTxt."`.*
            FROM  `".TblModVideoTxt."`,`".TblModVideo."`
            WHERE `".TblModVideoTxt."`.`cod`=`".TblModVideo."`.id
            AND `".TblModVideoTxt."`.`lang_id`='".$Video->lang_id."'
            AND `".TblModVideoTxt."`.`name`!=''
            AND `".TblModVideo."`.category='".$row['cod']."'
            AND (`".TblModVideo."`.status='a'
            or `".TblModVideo."`.status='e')
            ORDER BY `".TblModVideo."`.`position` asc";
            $res1 = $Video->db->db_Query( $q1 );
            $rows1 = $Video->db->db_GetNumRows();
            for( $j = 0; $j < $rows1; $j++ )
            {
                $arr1[$j] = $Video->db->db_FetchAssoc();
            }
            //echo '<br>q1='.$q1.' $res1='.$res1.' $Article->db->result='.$Article->db->result.' $rows1='.$rows1;
            if( $rows1 )
            {
                //$link = $Clients->Link($row['cod']);
                $link =  '/video/'.$row['translit'].'/';
                //$link = $Video->Link( $row['translit']);
                $href = $this->href.$link;
                $str .= $this->bildUrl($href,$priority,date("c"));

                for( $j = 0; $j < $rows1; $j++ )
                {
                    $row1 = $arr1[$j];
                    //echo '<br>$row1[id]='.$row1['id'];
                    //$link = $Article->Link($row1['category'], $row1['id']);
                    $link =  '/video/'.$row['translit'].'/'.$row1['translit'].'.html';
                    //$link = $Video->Link( $row['translit'], $row1['translit']);
                    $href = $this->href.$link;
                    $str .= $this->bildUrl($href,$priority,date("c"));
                }
            }
        }
        return $str;
    }// end of function BuildVideoPageMap()

    /**
     * Class method BuildVideoPageMap
     * function for generate links to all Clients pages for sitemap XML
     * @param $lang_id - id of the language
     * @author Bogdan Iglinsky  <bi@seotm.com>
     * @version 1.0, 17.07.2012
     */
    function BuildGalleryPageMap($lang_id)
    {
        $Gallery= check_init('GalleryLayout', 'GalleryLayout');
        $Gallery->lang_id = $lang_id;
        $priority='0.8';
        $str = NULL;

        $href =$this->href."/gallery/";
        $str .= $this->bildUrl($href,$priority,date("c"));

        $q = "SELECT *
        FROM `".TblModGalleryCat."`
        WHERE `lang_id`='".$Gallery->lang_id."'
        AND `name`!=''
        ORDER BY `move` ASC ";
        $res = $this->Rights->Query( $q, $this->user_id, $this->module );
        $rows = $this->Rights->db_GetNumRows();

        for( $i = 0; $i < $rows; $i++ )
        {
            $arr[$i] = $this->Rights->db_FetchAssoc();
        }

        for( $i = 0; $i < $rows; $i++ )
        {
            $row = $arr[$i];
            $name = $row['name'];
            //echo '<br>$name='.$name;
            $q1 = "SELECT `".TblModGalleryTxt."`.*
            FROM  `".TblModGalleryTxt."`,`".TblModGallery."`
            WHERE `".TblModGalleryTxt."`.`cod`=`".TblModGallery."`.id
            AND `".TblModGalleryTxt."`.`lang_id`='".$Gallery->lang_id."'
            AND `".TblModGalleryTxt."`.`name`!=''
            AND `".TblModGallery."`.category='".$row['cod']."'
            AND ( `".TblModGallery."`.status='a'
            or `".TblModGallery."`.status='e' )
            ORDER BY `".TblModGallery."`.`position` asc";
            $res1 = $Gallery->db->db_Query( $q1 );
            $rows1 = $Gallery->db->db_GetNumRows();
            for( $j = 0; $j < $rows1; $j++ )
            {
                $arr1[$j] = $Gallery->db->db_FetchAssoc();
            }
            //echo '<br>q1='.$q1.' $res1='.$res1.' $Article->db->result='.$Article->db->result.' $rows1='.$rows1;
            if( $rows1 )
            {
                //$link = $Clients->Link($row['cod']);
                $link =  '/gallery/'.$row['translit'].'/';
                //$link = $Video->Link( $row['translit']);
                $href = $this->href.$link;
                $str .= $this->bildUrl($href,$priority,date("c"));

                for( $j = 0; $j < $rows1; $j++ )
                {
                    $row1 = $arr1[$j];
                    //echo '<br>$row1[id]='.$row1['id'];
                    //$link = $Article->Link($row1['category'], $row1['id']);
                    $link =  '/gallery/'.$row['translit'].'/'.$row1['translit'].'.html';
                    //$link = $Video->Link( $row['translit'], $row1['translit']);
                    $href = $this->href.$link;
                    $str .= $this->bildUrl($href,$priority,date("c"));
                }
            }
        }
        return $str;
    }// end of function BuildVideoPageMap()


 }// End of class SitaeMap
?>