!function($) {

$(document).ready(function(){

    $('#n_1,#n_2').click(function(){  

        if ($(this).is(':checked')) {
            
            $('input[type="checkbox"].tag-id').prop('checked',true);

            $('#n_1,#n_2').prop('checked',true);

        } else {
        
            $('input[type="checkbox"].tag-id').prop('checked',false);

            $('#n_1,#n_2').prop('checked',false);
        }
        
    });
    
    $('.select-by-tag li a').each(function(){
    
        var _self = $(this);
        var _code = $(_self).attr('data-tag');
        
        $(_self).click(function(e){
        
            e.preventDefault();
            
            $('input[type="checkbox"].tag-id').prop('checked',false);

            $('input[type="checkbox"].tag-id').each(function(){

                if (!$(this).hasClass('tag-'+_code)) { return true; }

                $(this).prop('checked',true);

            });
        
        });
        
        
    });

});

}(window.jQuery);