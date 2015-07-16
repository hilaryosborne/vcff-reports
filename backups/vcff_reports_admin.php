<?php

class VCFF_Reports_Admin {

    public function __construct() {
    
        $this->_Load_Pages(); 
        
        add_action('plugins_loaded',array($this,'_Hook_Plugins_Loaded'));
        add_action('vcff_form_export_form_inputs',array($this,'_Hook_Export_Form_Inputs'));
        add_action('vcff_form_import_export_do',array($this,'_Hook_Export_Do'));
        add_action('vcff_form_import_upload_do',array($this,'_Hook_Import_Do'));
    }

    protected function _Load_Pages() { 
        // Load each of the form shortcodes
        foreach (new DirectoryIterator(VCFF_REPORTS_DIR.'/pages') as $FileInfo) {
            // If this is a directory dot
            if($FileInfo->isDot()) { continue; }
            // If this is a directory
            if($FileInfo->isDir()) { continue; }
            // If this is not false
            if (stripos($FileInfo->getFilename(),'.tpl') !== false) { continue; }
            // Include the file
            require_once(VCFF_REPORTS_DIR.'/pages/'.$FileInfo->getFilename());
        }
    }

    public function _Hook_Plugins_Loaded() {
        $reports_helper_sql = new VCFF_Reports_Helper_SQL();
        $reports_helper_sql->SQL_Check();
    }

    public function _Hook_Export_Form_Inputs($page) {
        // Compile the setting html
        $html = '<div class="checkbox">';
        $html .= '  <label>';
        $html .= '      <input type="checkbox" name="settings[export_reports]" value="y" checked="checked"> Export Report Entries';
        $html .= '  </label>';
        $html .= '</div>';
        // Echo the html
        echo $html;
    }

    public function _Hook_Export_Do($export_helper) {
        // If we want to export the settings
        if (!isset($export_helper->settings['export_reports'])) { return; }
        // Retrieve the selected form ids
        $forms = $export_helper->export['forms'];
        // If there are no forms, return out
        if (!$forms || !is_array($forms)) { return; }
        // Loop through each form
        foreach ($forms as $form_uuid => $export_data) {
            // Create a new reports sql helper
            $reports_helper_sql = new VCFF_Reports_Helper_SQL();
            // Retrieve the report ids
            $entries = $reports_helper_sql
                ->Select_Entries_By_Form_UUID($form_uuid);
            // If there are no entries
            if (!$entries || !is_array($entries)) { continue; }
            // Loop through each entry
            foreach ($entries as $k => $entry_data) {
                // Retrieve the entry tags
                $entry_tags = $reports_helper_sql
                    ->Select_Entry_Tags($entry_data->id);
                // Store the entry and entry tag data
                $export_helper->export['report_entries'][] = array(
                    'entry' => $entry_data,
                    'tags' => $entry_tags
                );
            }
        }
    }

    public function _Hook_Import_Do($import_helper) {
        // Retrieve the selected form ids
        $report_entries = $import_helper->import['report_entries']; 
        // If there are no forms, return out
        if (!$report_entries || !is_array($report_entries)) { return; } 
        // Loop through each form
        foreach ($report_entries as $form_uuid => $import_data) {
            // Retrieve the entry data
            $import_entry_data = $import_data['entry'];
            // Retrieve the tag data
            $import_tag_data = $import_data['tags'];
            // Create a new reports sql helper
            $reports_helper_sql = new VCFF_Reports_Helper_SQL();
            // Retrieve the report ids
            $entry = $reports_helper_sql
                ->Select_Entry_By_UUID($import_entry_data['uuid']);
            // If there is no entry by this uuid    
            if (!$entry) { 
                // Remove the id data
                unset($import_entry_data['id']); 
                // Create the new entry
                $reports_helper_sql
                    ->Create_Entry($import_entry_data);
            }
            // Loop through each tag
            foreach ($import_tag_data as $k => $tag_data) {
                // Retrieve the report ids
                $tag = $reports_helper_sql
                    ->Select_Tag_By_UUID($tag_data['uuid']);
                // If the tag exists, continue on
                if ($tag) { continue; }
                // Remove the id data
                unset($tag_data['id']);
                // Create the new entry
                $reports_helper_sql
                    ->Create_Entry_Tag($tag_data);
            }
        }
    }
}

global $vcff_reports_admin;

$vcff_reports_admin = new VCFF_Reports_Admin();

