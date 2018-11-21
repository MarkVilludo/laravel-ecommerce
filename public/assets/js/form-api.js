$('.form-control').on('focus', function(){
    $(this).closest('.form-group').removeClass('has-error')
    $(this).closest('.form-group').find('small').remove();
})

$("form").submit(function(){
    var selector = $(this);
    var form = new FormData(this);
    // var form = selector.serializeArray();
    // console.log('FORM', form);



    $.ajax({
        
        url: selector.attr("action"),
        type:"POST",
        data:form,
        dataType:"application/json",
        cache: false,
        contentType: false,
        processData: false,
        beforeSend : function(){
            $("input[type=submit]").attr("disabled", true);
        },
        success : function(response){
            console.log('respect',response)
            
        },
        error : function(response){
            console.log('here',response)
            var return_data = JSON.parse(response.responseText);
            console.log(return_data.redirect)
            if(response.status == 201 || response.status == 200){
                window.location.href = return_data.redirect;
            }

            if(response.status == 422){
                $('small').remove();
                $('.has-error').removeClass('has-error');
                
                $.each(return_data.errors, function(key, value){

                    var elem = selector.find("input[name="+value.field+"],textarea[name="+value.field+"],select[name="+value.field+"], input.error");
                    var elemParent = elem.parent().parent();
                    elem.parent().addClass('has-error');
                    $("<small class='error'>"+value.message+"</small>").insertAfter(elem);
                });
                $("input[type=submit]").attr("disabled", false);
            }

            if(response.status == 500){
                $('<div class="form-group control error"><label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i>&nbsp;'+ response.statusText +'</label></div>').insertBefore('.form-group:first');
                $("input[type=submit]").attr("disabled", false);
            }
            
        },
    });

    return false;
});