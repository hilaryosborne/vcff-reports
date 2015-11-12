<?php

class VCFF_Reports_Helper_Import extends VCFF_Helper {
    
    public $import;
    
    public $settings;
    
    public function Set_Data($import) {
        
        $this->import = $import; 
        
        return $this;
    }
    
    public function Import($settings) {
        // Populate the settings
        $this->settings = $settings;
        // If we want to export form data
        if (isset($this->settings['import_forms'])) {
            // Run the get forms function
            $this->_Import_Forms();
        }
        // Do any other actions
        do_action('vcff_form_import_upload_do',$this);
        // Return for chaining
        return $this;
    }
    
    public function _Import_Forms() {
        // Retrieve the list of forms to be imported
        $imported_forms = $this->import['forms'];
        // If there are no forms to be uploaded, return out
        if (!$imported_forms || !is_array($imported_forms)) { return; }
        // Loop through each of the forms to be imported
        $u=0; $c=0; foreach ($imported_forms as $form_uuid => $form) {
            // Attempt top load a form using that form uuid 
            $post = vcff_get_form_by_uuid($form_uuid);
            // If a post already exists
            if ($post && is_object($post)) {
                // Create the update array
                $update = array(
                    'ID' => $post->ID,
                    'post_title' => $form['post_title'],
                    'post_content' => base64_decode($form['post_content'])
                );
                // Update the post into the database
                wp_update_post($update); $u++;
                // Do any other actions
                do_action('vcff_form_update',$post);
            } // Otherwise if we have to create a new post
            else {   
                // Create the update array
                $create = array(
                    'post_title' => $form['post_title'],
                    'post_type' => 'vcff_form',
                    'post_content' => base64_decode($form['post_content']),
                    'post_status' => 'publish',
                );
                // Update the post into the database
                $form_id = wp_insert_post($create); $c++;
                // If no form id then the form was not created
                if (!$form_id) { continue; }
                // Update the form with a unique id
                update_post_meta($form_id,'form_uuid',$form_uuid);
                // Load the post
                $post = get_post($form_id);
                // Do any other actions
                do_action('vcff_form_update',$post); 
            } 
        }
        
        if ($c > 0) { $this->Add_Alert('<strong>Success!</strong> ... '.$c.' Forms Imported','success'); }
        
        if ($u > 0) { $this->Add_Alert('<strong>Success!</strong> ... '.$u.' Forms Updated','success'); }
    }
}