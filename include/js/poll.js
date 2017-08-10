/**
 * Created by bogdan on 17.06.14.
 */
function sendPoll(){
    $.ajax({
        type: "POST",
        data: $("#pollform").serialize() ,
        url: '/modules/mod_poll/poll.result.php',
        success: function(msg){
            $("#formpollRes").css('display','none');
            $("#itemPoll").html(msg);
            //$("#pollform").html( msg );
        },
        beforeSend : function(){
            $("#formpollRes").css('display','block');
            $("#formpollRes").html('<div class="res-cont-wrap"><div style="text-align:center;"><img src="/images/design/popup/ajax-loader.gif" alt="" title="" /></div></div>');
        }
    });
}