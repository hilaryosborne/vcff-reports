(function ($) {

    $(document).ready(function(){
        new Starred_Flags();
        new Unread_Flags();
        
        $('#n_1,#n_2').click(function(){  

            if ($(this).is(':checked')) {

                $('input[name="_entries[]"]').prop('checked',true);

                $('#n_1,#n_2').prop('checked',true);

            } else {

                $('input[name="_entries[]"]').prop('checked',false);

                $('#n_1,#n_2').prop('checked',false);
            }
        });
    });
    
    var Starred_Flags = function() {
        
        var $ = window.jQuery;
        
        $('.entry-item').each(function(){
            // Cache the this object
            var _self = $(this);
            // If there is no starred icon, move on
            if ($(_self).find('.icon-starred').length == 0) { return true; }
            // Retrieve the entry uuid
            var _uuid = $(this).attr('data-entry-uuid');
            // Retrieve the icon link
            var _link = $(_self).find('.icon-starred');
            // If the icon is clicked
            $(_link).click(function(e){
                // Prevent the default browser action
                e.preventDefault();
                // If the entry is already flagged
                if ($(this).hasClass('is-starred')) {
                    // Remove the starred flag
                    Remove_Starred();
                } // Otherise make the entry starred
                else { Make_Starred(); }
            });
            
            var Make_Starred = function() {
                // Post the data to the webservice
                $.post(ajaxurl,{
                    'action':'reports_flag',
                    'flag_action':'flag_entry',
                    'flag_code':'starred',
                    'flag_entry':_uuid,
                },function(response){ 
                    // If no data was returned
                    if (response.result != 'success') { return false; }
                    // If no data was returned
                    $(_link).removeClass('dashicons-star-empty').addClass('dashicons-star-filled').addClass('is-starred'); 
                },'json');
            }
            
            var Remove_Starred = function() {
                // Post the data to the webservice
                $.post(ajaxurl,{
                    'action':'reports_flag',
                    'flag_action':'unflag_entry',
                    'flag_code':'starred',
                    'flag_entry':_uuid,
                },function(response){ 
                    // If no data was returned
                    if (response.result != 'success') { return false; }
                    // If no data was returned
                    $(_link).removeClass('dashicons-star-filled').removeClass('is-starred').addClass('dashicons-star-empty'); 
                },'json');
            }
        });
    }
    
    var Unread_Flags = function() {
        
        var $ = window.jQuery;
        
        $('.entry-item').each(function(){
            // Cache the this object
            var _self = $(this);
            // If there is no starred icon, move on
            if ($(_self).find('.icon-unread').length == 0) { return true; }
            // Retrieve the entry uuid
            var _uuid = $(this).attr('data-entry-uuid');
            // Retrieve the icon link
            var _link = $(_self).find('.icon-unread');
            // If the icon is clicked
            $(_link).click(function(e){
                // Prevent the default browser action
                e.preventDefault();
                // If the entry is already flagged
                Remove_Unread();
            });
            
            var Remove_Unread = function() {
                // Post the data to the webservice
                $.post(ajaxurl,{
                    'action':'reports_flag',
                    'flag_action':'unflag_entry',
                    'flag_code':'unread',
                    'flag_entry':_uuid,
                },function(response){ 
                    // If no data was returned
                    if (response.result != 'success') { return false; }
                    // If no data was returned
                    $(_link).remove(); 
                },'json');
            }
        });
    }
    
})(window.jQuery);