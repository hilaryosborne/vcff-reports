var Event_Simple_Report = function(form_obj) {

    var $ = window.jQuery;
    
    var element = $(form_obj).find('.event-simple-report');
    
    var Update_Value = function() {
    
        var selected_options = $(element).find('.selected-field-list option');
         
        $(element).find('.selected-fields').val($(selected_options).map(function(){ return this.value }).get().join(","));
    }
    
    $(element).find('.field-add').click(function(e){
        // Prevent the default action
        e.preventDefault();
        // Retrieve the selected options
        var selected_options = $(element).find('.available-field-list option:selected');
        // Loop through each of the found options
        $(selected_options).each(function(){
            // Retrieve the item label
            var item_label = $(this).text() ? $(this).text() : $(this).val() ;
            // Retrieve the item value
            var item_value = $(this).val();
            // Append the new option to the selected list
            $(element).find('.selected-field-list').append('<option value="'+item_value+'">'+item_label+'</option>');
            // Remove the existing option
            $(this).remove();
        });
        
        Update_Value();
    });

    $(element).find('.field-remove').click(function(e){
        // Prevent the default action
        e.preventDefault();
        // Retrieve the selected options
        var selected_options = $(element).find('.selected-field-list option:selected');
        // Loop through each of the found options
        $(selected_options).each(function(){
            // Retrieve the item label
            var item_label = $(this).text() ? $(this).text() : $(this).val() ;
            // Retrieve the item value
            var item_value = $(this).val();
            // Append the new option to the selected list
            $(element).find('.available-field-list').append('<option value="'+item_value+'">'+item_label+'</option>');
            // Remove the existing option
            $(this).remove();
        });
        
        Update_Value();
    });
    
    $(element).find('.field-up').click(function(e){
        // Prevent the default action
        e.preventDefault();
        // Retrieve the selected options
        var selected_options = $(element).find('.selected-field-list option:selected');
        // Loop through each of the found options
        $(selected_options).each(function(){
            // Retrieve the previous item
            var previous_item = $(this).prev();
            // Insert the item before the previous item
            $(this).insertBefore(previous_item);
        });
        
        Update_Value();
    });
    
    $(element).find('.field-down').click(function(e){
        // Prevent the default action
        e.preventDefault();
        // Retrieve the selected options
        var selected_options = $(element).find('.selected-field-list option:selected');
        // Loop through each of the found options
        $(selected_options).each(function(){
            // Retrieve the previous item
            var next_item = $(this).next();
            // Insert the item before the previous item
            $(this).insertAfter(next_item);
        });
        
        Update_Value();
    });
};

vcff_add_action('form_event_form_after_display',function(args){
    
    var form_el = args.form;
    
    Event_Simple_Report(form_el);
});

