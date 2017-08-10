<?php
/**
 * Created by PhpStorm.
 * User: Ihor Trokhymchuk
 * Date: 11.01.2016
 * Time: 17:40
 */
class BackendLang extends SysLang{
    function __construct($lang_id = NULL, $back_front='back' ){
        parent::__construct($lang_id, $back_front );
    }

    /**
     * BackendLang::WriteLangPanel()
     * Write the language in the text string
     * @param integer $lang_id - id of the language
     * @param string $back_front
     * @return void
     * @author Igor Trokhymchuk  <ihor@seotm.com>
     */
    function WriteLangPanel($lang_id=NULL, $back_front='back_lang_menu')
    {
        //echo '<br> $back_front='.$back_front;
        if (!empty($lang_id)) $this->lang_id = $lang_id;
        else $this->lang_id = $this->GetDefBackLangID();

        $mas=$this->LangArray($this->lang_id, $back_front);

        if (empty($mas)){
            $_SESSION["lang_pg"]=1;
            $msg = new ShowMsg(1);
            //echo '<br> LANG='.$pg->lang.' $_SESSION["lang_pg"]='.$_SESSION["lang_pg"].' _LANG_ID='._LANG_ID;
            $msg->show_msg('_ERR_NO_TRANSLATE_ON_THIS_LANG');
            $mas=$this->LangArray($_SESSION["lang_pg"], $back_front);
        }
        if ( empty($mas) ) return false;
        $script = NULL;
        $tmp = $_SERVER["REQUEST_URI"];
        $s1 = explode( "lang_pg=", $tmp );
        $script = $s1[0];
        if( isset( $s1[1] ) ) {
            $s2 = explode( '&', $s1[1] );
            if( !empty( $s2[1] ) ) $script = $script.'&amp;'.$s2[1];
        }
        if (strstr($script,"?")){
            $script = trim( $script );
            if ( substr( $script, (strlen($script)-1), strlen($script) ) == '&' ) $strlink=$script."lang_pg=";
            else $strlink=$script."&amp;lang_pg=";
        }
        else $strlink=$script."?lang_pg=";

        $list_lang = '<div class="btn-group">';
//        print_r($mas);
        //echo '<br> _LANG_ID='._LANG_ID;
        $count=count($mas);
        $i=1;
        while( $el = each( $mas ) )
        {
            $strs='';
            if($count!=$i)  $strs='&nbsp;|&nbsp;';
            if( _LANG_ID != $el['key'] ) {
                //$list_lang = $list_lang.$this->Form->Link2($strlink.$el['key'], $el['value']).$strs;
                $list_lang .= '<button type="button" class="btn btn-default"><a href="'.$strlink.$el['key'].'">'.$el['value'].'</a></button>';
            }else{
                //$list_lang = $list_lang.$el['value'].$strs;
                $list_lang .= '<button type="button" class="btn btn-default">'.$el['value'].'</button>';
            }
            $i++;
        }
        $list_lang .= '</div>';
//        echo '<br>$list_lang='.$list_lang;
        return $list_lang;
    } // end of function WriteLangPanel();
}