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
        // Initalize core logic
        add_action('vcff_init_core',array($this,'__Init_Core'),25);
        // Initalize context logic
        add_action('vcff_init_context',array($this,'__Init_Context'),25);
        // Initalize misc logic
        add_action('vcff_init_misc',array($this,'__Init_Misc'),25);
        // Fire the shortcode init action
        do_action('vcff_reports_init',$this);
        // Fire the shortcode init action
        do_action('vcff_reports_after_init',$this);
        
        return $this;
    }
    
    public function __Init_Core() {
        // Load helper classes
        $this->_Load_Helpers();
        // Load the core classes
        $this->_Load_Core(); 
        // Fire the shortcode init action
        do_action('vcff_reports_init_core',$this);
    }

    public function __Init_Context() {
        // Load the context classes
        $this->_Load_Context();
        // Fire the shortcode init action
        do_action('vcff_reports_init_context',$this);
    }
    
    public function __Init_Misc() {
        // Load the pages
        $this->_Load_Pages();
        // Load AJAX
        $this->_Load_AJAX();
        // Fire the shortcode init action
        do_action('vcff_reports_init_misc',$this);
    }
    
    protected function _Load_Helpers() {
        // Retrieve the context director
        $dir = untrailingslashit( plugin_dir_path(__FILE__ ) );
        // Recurssively load the directory
        $this->_Recusive_Load_Dir($dir.'/helpers');
        // Fire the shortcode init action
        do_action('vcff_challenge_helper_init',$this);
    }
    
    protected function _Load_Core() {
        // Retrieve the context director
        $dir = untrailingslashit( plugin_dir_path(__FILE__ ) );
        // Recurssively load the directory
        $this->_Recusive_Load_Dir($dir.'/core');
        // Fire the shortcode init action
        do_action('vcff_challenge_core_init',$this);
    }
    
    protected function _Load_Context() {
        // Retrieve the context director
        $dir = untrailingslashit( plugin_dir_path(__FILE__ ) );
        // Recurssively load the directory
        $this->_Recusive_Load_Dir($dir.'/context');
        // Fire the shortcode init action
        do_action('vcff_challenge_context_init',$this);
    }
    
    protected function _Load_Pages() {
        // Retrieve the context director
        $dir = untrailingslashit( plugin_dir_path(__FILE__ ) );
        // Recurssively load the directory
        $this->_Recusive_Load_Dir($dir.'/pages');
        // Fire the shortcode init action
        do_action('vcff_challenge_pages_init',$this);
    }
    
    protected function _Load_AJAX() {
        // Retrieve the context director
        $dir = untrailingslashit( plugin_dir_path(__FILE__ ) );
        // Recurssively load the directory
        $this->_Recusive_Load_Dir($dir.'/ajax');
        // Fire the shortcode init action
        do_action('vcff_challenge_ajax_init',$this);
    }
    
    protected function _Recusive_Load_Dir($dir) {
        // If the directory doesn't exist
        if (!is_dir($dir)) { return; }
        // Load each of the field shortcodes
        foreach (new DirectoryIterator($dir) as $FileInfo) {
            // If this is a directory dot
            if ($FileInfo->isDot()) { continue; }
            // If this is a directory
            if ($FileInfo->isDir()) { 
                // Load the directory
                $this->_Recusive_Load_Dir($FileInfo->getPathname());
            } // Otherwise load the file
            else {
                // If this is not false
                if (stripos($FileInfo->getFilename(),'.tpl') !== false) { continue; } 
                // If this is not false
                if (stripos($FileInfo->getFilename(),'.php') === false) { continue; } 
                // Include the file
                require_once($FileInfo->getPathname());
            }
        }
    }
    
}

$vcff_reports = new VCFF_Reports(); 

vcff_register_library('vcff_reports',$vcff_reports);

$vcff_reports->Init();