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

/**
 * 
 * PLEASE NOTE
 * A report is an extension of a form event. A report will share all functionality of a form event
 * and all reports will be mapped to a form event
 * 
 */

if (!defined('VCFF_REPORTS_DIR'))
{ define('VCFF_REPORTS_DIR',untrailingslashit( plugin_dir_path(__FILE__ ) )); }

if (!defined('VCFF_REPORTS_URL'))
{ define('VCFF_REPORTS_URL',untrailingslashit( plugins_url( '/', __FILE__ ) )); }


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
        // Load the core classes
        $this->_Load_Core();
        // Load the tags
        $this->_Load_Tags();
        // Load the context classes
        $this->_Load_Reports();
        // Fire the shortcode init action
        do_action('vcff_reports_init',$this);
        // Include the admin class
        require_once(VCFF_REPORTS_DIR.'/VCFF_Reports_Admin.php');
        // Otherwise if this is being viewed by the client 
        require_once(VCFF_REPORTS_DIR.'/VCFF_Reports_Public.php');
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
    
    protected function _Load_Core() {
        // Load each of the form shortcodes
        foreach (new DirectoryIterator(VCFF_REPORTS_DIR.'/core') as $FileInfo) {
            // If this is a directory dot
            if($FileInfo->isDot()) { continue; }
            // If this is a directory
            if($FileInfo->isDir()) { continue; }
            // If this is not false
            if (stripos($FileInfo->getFilename(),'.tpl') !== false) { continue; }
            // Include the file
            require_once(VCFF_REPORTS_DIR.'/core/'.$FileInfo->getFilename());
        }
        // Fire the shortcode init action
        do_action('vcff_reports_core_init',$this);
    }
    
    protected function _Load_Reports() {
        // Load each of the form shortcodes
        foreach (new DirectoryIterator(VCFF_REPORTS_DIR.'/context/reports') as $FileInfo) {
            // If this is a directory dot
            if($FileInfo->isDot()) { continue; }
            // If this is a directory
            if($FileInfo->isDir()) { continue; }
            // If this is not false
            if (stripos($FileInfo->getFilename(),'.tpl') !== false) { continue; }
            // Include the file
            require_once(VCFF_REPORTS_DIR.'/context/reports/'.$FileInfo->getFilename());
        }
        // Fire the shortcode init action
        do_action('vcff_reports_reports_init',$this);
    }
    
    protected function _Load_Tags() {
        // Load each of the form shortcodes
        foreach (new DirectoryIterator(VCFF_REPORTS_DIR.'/context/tags') as $FileInfo) {
            // If this is a directory dot
            if($FileInfo->isDot()) { continue; }
            // If this is a directory
            if($FileInfo->isDir()) { continue; }
            // If this is not false
            if (stripos($FileInfo->getFilename(),'.tpl') !== false) { continue; }
            // Include the file
            require_once(VCFF_REPORTS_DIR.'/context/tags/'.$FileInfo->getFilename());
        }
        // Fire the shortcode init action
        do_action('vcff_reports_tags_init',$this);
    }
}

$vcff_reports = new VCFF_Reports(); 

vcff_register_library('vcff_reports',$vcff_reports);

$vcff_reports->Init();