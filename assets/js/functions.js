function formDataToJson(id){
    var $form = $(`#${id}`);
    var unindexed_array = $form.serializeArray();
    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

function showLoading() {
    $("#showLoading").modal({
        backdrop: "static", //remove abilidfsaty to close modal with click
        keyboard: false, //remove option to close with keyboard
        show: true //Display loader!
    });
}

function closeLoading() {
    setTimeout(function() {
        $("#showLoading").modal("hide");
    }, 500);
}