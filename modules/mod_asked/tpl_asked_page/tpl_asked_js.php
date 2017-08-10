<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bogdan
 * Date: 13.09.13
 * Time: 16:00
 * To change this template use File | Settings | File Templates.
 */
?><script type="text/javascript">
    $(document).ready(function() {
        $("#form_mod_asked").validationEngine('attach', {promptPosition : "topLeft", scroll: false});
    });
    function showFormAsked(){
        $('#formAskedFon').show();
    }
    function closeAskedForm(){
        $('#formAskedFon').hide();
    }
    function verify(){
        if(!$("#form_mod_asked").validationEngine('validate')) return false;
        save_asked();
        return false;
    }
    function save_asked(){
        $.ajax({
            type: "POST",
            url: '/ask/add/',
            data: $("#form_mod_asked").serialize() ,
            success: function(msg){
                //alert(msg);
                $("#container_asked").html( msg );
                showFormAsked();
                setTimeout("closeAskedForm()",3000);
            },
            beforeSend : function(){
                showFormAsked();
                $("#container_asked").html('<img src="/images/design/popup/ajax-loader.gif" alt="" title="" />');
                showFormAsked();
            }
        });
    }
    var itemRatingSel = -1;
    var itemSelReal = -1;
    function chengMoveRating(item){
        if(item!=itemRatingSel && itemSelReal==-1){
            $('#resResize').html('');
            var imgPlus = $('#imgPlus').html();
            var imgMinus = $('#imgMinus').html();
            for(var i=0;i<item;i++){
                $('#resResize').append(imgPlus);
            }
            if(item<5){
                for(var i=0;i<5-item;i++){
                    $('#resResize').append(imgMinus);
                }
            }
        }
    }
    function selRating(item){
        itemSelReal = item;
        document.getElementById('rating').value = item;
//        $('#rating').val(item);
//        alert(document.getElementById('rating').value);
        $('.ratingformError').click();
    }
    function killRatingSel(){
        if(itemSelReal==-1){
            chengMoveRating(5);
            document.getElementById('rating').value = '';
        }
    }
    $(document).click(function(e) {
//        alert($(e.target).attr('class'));
        if ($(e.target).attr('id')!='formAskedFonBig'
            && $(e.target).parents().attr('id')!='formAskedFonBig'
            && $(e.target).parents().parents().attr('id')!='formAskedFonBig'
            && $(e.target).parents().parents().parents().attr('id')!='formAskedFonBig'
            && $(e.target).attr('id')!='formAskedFon'
            && $(e.target).parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().attr('id')!='formAskedFon'
            && $(e.target).parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().parents().attr('id')!='formAskedFon') {
            closeAskedForm();
        }
        //alert('click');
    });
</script><?