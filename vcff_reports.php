<?php

/*
* Plugin Name: VC Form Framework - Reports Addon
* Plugin URI: http://theblockquote.com/
* Description: Reports and entries addon for the visual composer form framework core plugin
* Version: 0.1.0
* Author: Hilary Osborne - BlockQuote
* Author URI: http://theblockquote.com/
* License: License GNU General Public License version 2 or later;
* Copyright 2015 theblockquote
*/

// Require the wp upgrade library
require_once(ABSPATH.'wp-admin/includes/upgrade.php');

if (!defined('VCFF_REPORTS_DIR'))
{ define('VCFF_REPORTS_DIR',untrailingslashit( plugin_dir_path(__FILE__ ) )); }

if (!defined('VCFF_REPORTS_URL'))
{ define('VCFF_REPORTS_URL',untrailingslashit( plugins_url( '/', __FILE__ ) )); }

define('VCFF_REPORTS_SQL_VERSION','0.0.1');
define('VCFF_REPORTS_SQL_ENTRY_TBL','vcff_report_entry');
define('VCFF_REPORTS_SQL_ENTRY_META_TBL','vcff_report_entry_meta');
define('VCFF_REPORTS_SQL_ENTRY_FIELDS_TBL','vcff_report_entry_fields');
define('VCFF_REPORTS_SQL_FLAGS_TBL','vcff_report_entry_flags');
define('VCFF_REPORTS_SQL_NOTES_TBL','vcff_report_entry_notes');

class VCFF_Reports {
    
    public $contexts = array();
    
    public $tags = array();
    
    public function Init() {
        // Fire the shortcode init action
        do_action('vcff_reports_before_init',$this);
        // Include the admin class
        require_once(VCFF_REPORTS_DIR.'/functions.php');
        // Load helper classes
        $this->_Load_Helpers();
        // Load the event classes
        $this->_Load_Context();
        // Fire the shortcode init action
        do_action('vcff_reports_init',$this);
        // Include the admin class
        require_once(VCFF_REPORTS_DIR.'/vcff_reports_admin.php');
        // Otherwise if this is being viewed by the client 
        require_once(VCFF_REPORTS_DIR.'/vcff_reports_public.php');
        // Fire the shortcode init action
        do_action('vcff_reports_after_init',$this);
        
        return $this;
    }
    
    protected function _Load_Helpers() {
        // Load each of the form shortcodes
        foreach (new DirectoryIterator(VCFF_REPORTS_DIR.'/helpers') as $FileInfo) {
            // If this is a directory dot
            if($FileInfo->isDot()) { continue; }
            // If this is a directory
            if($FileInfo->isDir()) { continue; }
            // Include the file
            require_once(VCFF_REPORTS_DIR.'/helpers/'.$FileInfo->getFilename());
        }
        // Fire the shortcode init action
        do_action('vcff_reports_helper_init',$this);
    }
    
    protected function _Load_Context() {
        // Load each of the form shortcodes
        foreach (new DirectoryIterator(VCFF_REPORTS_DIR.'/context/events') as $FileInfo) {
            // If this is a directory dot
            if($FileInfo->isDot()) { continue; }
            // If this is a directory
            if($FileInfo->isDir()) { continue; }
            // If this is not false
            if (stripos($FileInfo->getFilename(),'.tpl') !== false) { continue; }
            // Include the file
            require_once(VCFF_REPORTS_DIR.'/context/events/'.$FileInfo->getFilename());
        }
        // Fire the shortcode init action
        do_action('vcff_reports_context_init',$this);
    }
    
}

$vcff_reports = new VCFF_Reports(); 

vcff_register_library('vcff_reports',$vcff_reports);

$vcff_reports->Init();