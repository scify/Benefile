$(document).ready(function(){

    // on form submit, change the form action url so it includes the search parameters
    $("#search-form").on("submit", function(){
        var $url = $(this).attr('action');
        var $folder_number = $("input[name='folder_number']").val();
        var $lastname = $("input[name='lastname']").val();
        var $name = $("input[name='name']").val();
        var $fathers_name = $("input[name='fathers_name']").val();
        var $gender_id = $("input:radio[name='gender_id']:checked").val() == "undefined" ? "" : $("input:radio[name='gender_id']:checked").val();
        var $telephone = $("input[name='telephone']").val();
        var $birth_date = $("input[name='birth_date']").val();
        var $origin_country = $("input[name='origin_country']").val();
        var $medical_location_id = $("select:first").val();
        var $temp = {'folder_number': $folder_number,
                'lastname': $lastname,
                'fname': $name,
                'fathers_name': $fathers_name,
                'gender_id': $gender_id,
                'telephone': $telephone,
                'birth_date': $birth_date,
                'origin_country': $origin_country,
                'medical_location_id': $medical_location_id
            };
        //var $queryUrl = $(this).attr('action') + '?folder_number=' + $("input[name='folder_number']").val() +
        //    '&lastname=' + $("input[name='lastname']").val() + '&name=' + $("input[name='name']").val() +
        //    '&fathers_name=' + $("input[name='fathers_name']").val() + '&gender_id=' +
        //    $("input:radio[name='gender_id']:checked").val() == "undefined" ?  + '&telephone=' +
        //    $("input[name='telephone']").val() + '&birth_date=' + $("input[name='birth_date']").val() +
        //    '&origin_country=' + $("input[name='origin_country']").val() + '&medical_location_id=' +
        //    $("select:first").val();
        //window.history.pushState("", document.title, $queryUrl);
        MakeAjaxSearchCall($url, $temp);
        return false;
    });
});

// make the ajax call to get a response
function MakeAjaxSearchCall($url, $values){
    $.ajax({
        url: $url,
        type: 'get',
        data: {'folder_number': $values.folder_number,
                'lastname': $values.lastname,
                'name': $values.fname,
                'fathers_name': $values.fathers_name,
                'gender_id': $values.gender_id,
                'telephone': $values.telephone,
                'birth_date': $values.birth_date,
                'origin_country': $values.origin_country,
                'medical_location_id': $values.medical_location_id
            },
        beforeSend: function () {
            // spinner start
            //$loader = $("body").faLoadingAdd('fa-cog');
        },
        success: function ($response) {
            DisplayResults($response);
        },
        error: function ($response) {

        },
        complete: function () {
            //$loader.remove(); //stop the loading screen
        }
    });
}

// show the results returned from the ajax call
function DisplayResults($response){
    console.log($response);
}
