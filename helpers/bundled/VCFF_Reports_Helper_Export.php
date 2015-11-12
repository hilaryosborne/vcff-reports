<?php

class VCFF_Reports_Helper_Export extends VCFF_Helper {
    
    public $ids;
    
    public $settings;
    
    public $export;
    
    public function Get_Data() { 
        // Return the export data
        return $this->export;
    }
    
    public function Export($ids,$settings) {
        // Retrieve the list of ids
        $this->ids = $ids;
        // Populate the settings
        $this->settings = $settings;
        // If we want to export form data
        if (isset($this->settings['export_forms'])) {
            // Run the get forms function
            $this->_Get_Forms();
        } 
        // Do any other actions
        do_action('vcff_form_import_export_do',$this); 
        // Return for chaining
        return $this;
    }
    
    protected function _Get_Forms() {
        // Retrieve the global wordpress database layer
        global $wpdb;
        // Check the vcff_form post type exists
        if (!post_type_exists('vcff_form')){ return; } 
        // Retrieve a list of all the published vv forms
        $form_posts = $wpdb->get_results("SELECT ID, post_title 
	        FROM $wpdb->posts
	        WHERE post_status = 'publish' AND post_type = 'vcff_form'");
        // If no published posts were returned
        if (!$form_posts) { return; } 
        // Loop through each published post
        foreach ($form_posts as $k => $row) {
            // If the form is not one of the forms we want to export
            if (!in_array($row->ID, $this->ids)) { continue; }
            // Retrieve the post object
            $post = get_post($row->ID);
            // If no post could be found then continue
            if (!$post || !is_object($post)) { continue; }
            // Retrieve the form type from meta
            $meta_form_uuid = get_post_meta($post->ID, 'form_uuid', true);
            // If no post could be found then continue
            if (!$meta_form_uuid) { continue; }
            // Retrieve the form type from meta
            $meta_form_type = get_post_meta($post->ID, 'form_type', true);
            // If no post could be found then continue
            if (!$meta_form_type) { continue; }
            // Build the export array
            $export_post = array(
                'form_uuid' => $meta_form_uuid,
                'form_type' => $meta_form_type,
                'post_title' => $post->post_title,
                'post_content' => base64_encode($post->post_content),
                'post_author' => $post->post_author,
                'post_date' => $post->post_date,
                'post_date_gmt' => $post->post_date_gmt,
            ); 
            // Pass the data array through the setup filter
            $export_post = apply_filters('vcff_forms_export_form_data', $export_post);
            // Add the form to the forms list
            $this->export['forms'][$meta_form_uuid] = $export_post;
        } 
        // Return for chaining
        return $this;
    }

}