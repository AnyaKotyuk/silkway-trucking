/**
 * Created with JetBrains PhpStorm.
 * User: bogdan
 * Date: 22.07.13
 * Time: 17:34
 * To change this template use File | Settings | File Templates.
 */

function gelPropConetnt(linkCat,linkParam){
//    alert('linkCat='+linkCat+' linkParam='+linkParam);
    if(linkParam.length != 0){
        location.hash = linkParam;
    }else{
        location.hash = 'kill';
    }
    $.ajax({
        type: "POST",
        url: linkCat + linkParam,
        success: function(msg){
//            alert('linkCat='+linkCat+' linkParam='+linkParam);
            $("#my_d_basket").html( msg );
            $("#reloadOrder").hide();
            showFilter(linkCat,linkParam);
        },
        beforeSend : function(){
            $("#reloadOrder").show();
            $('#showFilterReload').show();
        }
    });
    return false;
}

function showFilter(linkCat,linkParam){
//    alert('linkCat='+linkCat+' linkParam='+linkParam);
    if(linkParam.length != 0){
        location.hash = linkParam;
    }else{
        location.hash = 'kill';
    }
//    alert(linkParam[0]);
    if(linkParam.length>1){
        if(linkParam[linkParam.length-1]!='/'){
            linkParam += '&';
        }else{
            linkParam += '?';
        }
    }else{
        linkParam = '?';
    }
    var link_url = linkCat + linkParam+'onlyFilter=1'
//    alert(link_url);
    $.ajax({
        type: "POST",
        url: link_url,
        success: function(msg){
//            alert(msg);
            $("#showFilterReload").hide();
            $("#showFilterHtml").html( msg );
        }
    });
}

function gelPropConetntByTypeParam5(linkCat,linkParam,id){
    var val = $('#'+id).val();
    if(val!=''){
        if(linkParam.length>0){
            linkParam = linkParam + '&';
        }else{
            linkParam = linkParam + '?';
        }
        linkParam = linkParam +  id + '=' + val;
    //    alert(linkParam);
        gelPropConetnt(linkCat,linkParam);
    }else{
        alert('Введите корректное значение!');
    }
    return false;
}

function gelPropConetntByTypeParam1(linkCat,linkParam,id){
    var val0 = $('#'+id+'_0').val();
    var val1 = $('#'+id+'_1').val();

//    alert('val0=' + val0 + 'val1=' + val1);
    if(val0=='' || val1==''){
        showErr();
        return false;
    }

//    alert('parseFloat(val0)=' + parseFloat(val0) + 'parseFloat(val1)=' + parseFloat(val1));
    if(parseFloat(val0) > parseFloat(val1)){
        showErr();
        return false;
    }

    if(linkParam.length>0){
        linkParam = linkParam + '&';
    }else{
        linkParam = linkParam + '?';
    }
    linkParam = linkParam +  id + '=' + val0 + '|' + val1;
    //    alert(linkParam);
    gelPropConetnt(linkCat,linkParam);

    return false;
}

function showErr(){
    alert('Введите корректное значение!');
}

function gelPropConetntByPrice(linkCat,linkParam,id){
    var val0 = $('#'+id+'_0').val();
    var val1 = $('#'+id+'_1').val();

//    alert('val0=' + val0 + 'val1=' + val1);
    if(val0=='' || val1==''){
        showErr();
        return false;
    }

//    alert('parseFloat(val0)=' + parseFloat(val0) + 'parseFloat(val1)=' + parseFloat(val1));
    if(parseFloat(val0) > parseFloat(val1)){
        showErr();
        return false;
    }

    if(linkParam.length>0){
        linkParam = linkParam + '&';
    }else{
        linkParam = linkParam + '?';
    }
    linkParam = linkParam +  'from=' + val0 + '&to=' + val1;
    //    alert(linkParam);
    gelPropConetnt(linkCat,linkParam);

    return false;
}