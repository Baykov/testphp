
var id = 0;

$(document).ready(function() {
    selectRegionList();
    if($.urlParam('id') !== 'undefined'){
        id = $.urlParam('id');
        $('body').find('#user_id').val(id);
        $.ajax({
            type: 'get',
            url: '/api/index.php',
            dataType: 'json',
            async: false,
            data: {'task':'user', 'params':{'id':id}},
            statusCode: {
                200: function(response) {
                    if(response.user){
                        $('body').find('#input_fio').val(response.user.name);
                        $('body').find('#input_email').val(response.user.email);
                    }
                    if(response.city){
                        $('#select_regions').val(response.city.ter_pid).trigger('chosen:updated');
                        selectCityList(response.city.ter_pid);
                        $('#select_cities').val(response.city.ter_id).trigger('chosen:updated');
                        selectDistrictList(response.city.ter_id);
                        $('#select_districts').val(response.user.ter_id).trigger('chosen:updated');
                    } else {
                        $('#select_regions').val(response.user.ter_pid).trigger('chosen:updated');
                        selectCityList(response.user.ter_pid);
                        $('#select_cities').val(response.user.ter_id).trigger('chosen:updated');
                    }
                }
            }
        });
    }
    $('body').on('change', 'select[name=select_regions]', function() {
        selectCityList(this.value);
    });
    $('body').on('change', 'select[name=select_cities]', function() {
        selectDistrictList(this.value);
    });

    $("#regform").validate({
        rules: {
            "email": {
                remote: {
                    url: "/api/index.php",
                    type: "post",
                    data: {
                        'task':'validate',
                        params: {
                            name: function () {
                                return $("#input_fio").val();
                            },
                            email: function () {
                                return $("#input_email").val();
                            }
                        }
                    },
                    dataFilter: function (response) {
                        $("#user_alert").addClass('hidden');
                        var isValid = false;
                        if (response) {
                            var parsedResponse = $.parseJSON(response);
                            if (parsedResponse.valid) {
                                isValid = parsedResponse.valid;
                            }
                            if(id > 0){isValid = true;} else {
                                alertUser(isValid, parsedResponse);
                            }
                        }
                        return isValid;
                    }
                }
            }
        },

        submitHandler: function(form) {
            $.ajax({
                type: 'post',
                url: '/api/index.php',
                dataType: 'json',
                async: false,
                data: {'task':'registration', 'params':$(form).serializeArray()},
                statusCode: {
                    200: function(response) {
                        if(response.reg == true){
                            alert('Регистрация прошла успешно!');
                        } else{
                            alert('Ошибка!');
                        }
                    }
                }
            });
            return false;
        }
    });
});

 function alertUser(isValid, parsedResponse) {
     if(isValid == "false"){
         $("#user_alert").removeClass('alert-success');
         $("#user_alert").addClass('alert-danger');
         $("#user_alert").toggleClass('hidden');
         $("#user_alert").html("Пользователь с email:<b>" + parsedResponse.user.email + "</b> уже зарегистрирован под именем:<b>" + parsedResponse.user.name + " </b>и адресом <b>" + parsedResponse.user.territory+'</b>');
     } else {
         $("#user_alert").removeClass('alert-danger');
         $("#user_alert").addClass('alert-success');
         $("#user_alert").toggleClass('hidden');
         $("#user_alert").text("Email свободен!");
     }

 }

function getApiData(task, select_element, params){
    $.ajax({
        type: 'get',
        url: '/api/index.php',
        dataType: 'json',
        async: false,
        data: {'task':task, 'params':params},
        statusCode: {
            200: function(response) {
                $.each(response, function(key, value) {
                    $('<option>').val(value.ter_id).text(value.ter_name).appendTo($(select_element));
                });
            }
        }
    });
    $('select').chosen();
}


function selectRegionList(){
    $('body').find('#div_city').html('');
    $('body').find('#div_district').html('');
    var select_regions = $('body').find('#select_regions');
    getApiData('regions', select_regions);
}
function selectCityList(ter_id){
    $('body').find('#div_city').html('');
    $('body').find('#div_district').html('');
    $('body').find('#div_city').append('<label for="select_cities">Выберите город</label><select class="form-control required" id="select_cities" name="select_cities" required></select>');
    var select_cities = $('body').find('#select_cities');
    getApiData('cities', select_cities, {'ter_id':ter_id});
}
function selectDistrictList(city_id){
    $('body').find('#div_district').html('');
    $('body').find('#div_district').append('<label for="select_districts">Выберите район</label><select class="form-control required" id="select_districts" name="select_districts" required></select>');
    var select_districts = $('body').find('#select_districts');
    getApiData('districts', select_districts, {'city_id':city_id});
}

$.urlParam = function(name) {
    var results = new RegExp('[\?&amp;]' + name + '=([^&amp;#]*)').exec(window.location.href);
    if (results==null){
        return null;
    } else {
        return results[1] || 0;
    }
}
