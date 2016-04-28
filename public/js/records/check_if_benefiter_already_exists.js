function displayPossibleDuplicatedFolders(data){
    if(data.length <= 0){
        if(!$("#duplicate-folders").is(":hidden")) {
            $("#duplicate-folders").toggle("slow");
        }
    } else {
        if($("#duplicate-folders").is(":hidden")) {
            $("#duplicate-folders").toggle("slow");
        }
        $("#duplicate-folders").html("<div class=\"alert-header\"><strong>" + $("#duplicate-folders").data("title") + "</strong></div>");
        for(var i in data){
            $("#duplicate-folders").append(data[i].name + " " + data[i].lastname + ", " +
            data[i].fathers_name + ", " + data[i].birth_date +
            ", <a style=\"color: black;\" target=\"_blank\" href=\"" +
            $(location).attr("href").replace("-1", data[i].id) + "\">" +
            $("#duplicate-folders").data("display-link") + "</a>" + "<br/>");
        }
    }
}

function checkIfBenefiterAlreadyExists(){
    //collect values
    var data = {};
    $(".uniqueness").each(function(){
        if ($(this).val().length>0){
            data[$(this).attr("name")] = $(this).val();
        }
    });
    data["_token"] = $("input[name='_token']").val();
    //send ajax request
    $.ajax({
        url: $(".personal-info").data("url"),
        data: data,
        method: "post",
        success: function(data){
            displayPossibleDuplicatedFolders(data);
        }
    });
}

$('.uniqueness').on('change changeDate', checkIfBenefiterAlreadyExists);

$(document).ready(init);
