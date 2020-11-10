function allfieldsSelecter(){
    var errMsg = [];
    $('#errMsgContainerModal').empty();
    if($('#outcome').val() == "" || $('#outcome').val() == undefined){
        errMsg.push("Please select a value for the <strong>Outcome</strong>.");
    }
    if($('#condition1').val() == "" || $('#condition1').val() == undefined){
        errMsg.push("Please select a value for the <strong>First Condition</strong>.");
    }
    if($('#condition2').val() == "" || $('#condition2').val() == undefined){
        errMsg.push("Please select a value for the <strong>Second Condition</strong>.");
    }

    if (errMsg.length > 0) {
        $.each(errMsg, function (i, e) {
            $('#errMsgContainerModal').append('<div>' + e + '</div>');
        });
        $('#errMsgContainerModal').show();
        return false;
    }
    return true;
}
function loadTable(url){
    if(allfieldsSelecter()) {
        var outcome = "&outcomevar=" + $('#outcome option:selected').attr('name') + "&outcomeval=" + $('#outcome').val();
        var filterArray = $('#filter li input:checked').map(function () {
            return this.value;
        }).get().join(",");
        if($('#filter li input:checked').attr('name') == undefined){
            var filter = "";
        }else{
            var filter = "&filtervar=" + $('#filter li input:checked').attr('name') + "&filterval=" + filterArray;
        }

        var condition1 = "&condition1var=" + $('#condition1 option:selected').attr('name') + "&condition1val=" + $('#condition1').val();
        var condition2 = "&condition2var=" + $('#condition2 option:selected').attr('name') + "&condition2val=" + $('#condition2').val();
        var multiple1 = "&multiple1=" + $('#condition1 option:selected').attr('multiple1');
        var multiple2 = "&multiple2=" + $('#condition2 option:selected').attr('multiple2');
        var data = outcome + filter + condition1 + condition2 + multiple1 + multiple2;

        $('#loadTablebtn').prop('disabled', true);
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            error: function (xhr, status, error) {
                alert(xhr.responseText);
            },
            success: function (result) {
                paramValue = jQuery.parseJSON(result);
                var Newulr = getParamUrl(window.location.href, paramValue);
                window.location.href = Newulr;
            }
        });
    }
}

function getParamUrl(url, newParam){
    if (url.substring(url.length-1) == "#")
    {
        url = url.substring(0, url.length-1);
    }

    if(url.match(/(&dash=)/)){
        var oldParam = url.split("&dash=")[1];
        url = url.replace( oldParam, newParam );
    }else{
        url = url + "&dash="+newParam;
    }
    return url;
}

function selectOnlyOneGroup(element){
    var selectedName = $(element).attr('name');
    if($('#filter input[type=checkbox]:checked').length > 1) {
        $('#filter input[type=checkbox]:checked').each(function () {
            if(selectedName != $(this).attr('name')){
                $(element).prop('checked',false);
            }
        });
    }
}