!function($) {

$(document).ready(function(){

    $('.entry-item').each(function(){
        // Retrieve the entry id
        var entry_id = $(this).attr('data-entry-id');

        var _self = $(this);
        
        $(_self).find('.flag-toggle').click(function(e){
        
            var _link = $(this);
            // Prevent the default browser action
            e.preventDefault();
            // Post the data to the webservice
            $.post(ajaxurl,{
                'action':'reports_entry_flag',
                'entry_id':entry_id,
            },function(response){ 
                // If no data was returned
                if (response.result != 'success') { return false; }
                // If no data was returned
                if (typeof response.data == 'undefined') { return false; }
                
                if (response.data.is_flagged) { 
                    
                    $(_link).removeClass('dashicons-star-empty').addClass('dashicons-star-filled'); 
                    
                } else { $(_link).addClass('dashicons-star-empty').removeClass('dashicons-star-filled');  }

            },'json');
        });

        
    });

});

}(window.jQuery);