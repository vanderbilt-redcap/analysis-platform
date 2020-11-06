function allfieldsSelecter(){
    var errMsg = [];
    $('#errMsgContainerModal').empty();
    if($('#outcome').val() == "" || $('#outcome').val() == undefined){
        errMsg.push("Please select a value for the <strong>Outcome</strong>.");
    }
    // if($('#filter li input:checked').val() == "" || $('#filter li input:checked').val() == undefined){
    //     errMsg.push("Please select a value for the <strong>Filter</strong>.");
    // }
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
        var filter = "&filtervar=" + $('#filter li input:checked').attr('name') + "&filterval=" + filterArray;
        // var condition1Array = $('#condition1 li input:checked').map(function () {
        //     return this.value;
        // }).get().join(",");
        // var condition1 = "&condition1var="+$('#condition1 li input:checked').attr('name')+"&condition1val="+condition1Array;
        var condition1 = "&condition1var=" + $('#condition1 option:selected').attr('name') + "&condition1val=" + $('#condition1').val();
        var condition2 = "&condition2var=" + $('#condition2 option:selected').attr('name') + "&condition2val=" + $('#condition2').val();

        var data = outcome + filter + condition1 + condition2;
        console.log(data)

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