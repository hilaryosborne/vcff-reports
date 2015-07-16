<?php

class VCFF_Reports_Admin {

    public function __construct() {
    
        $this->_Load_Pages(); 
        
        $this->_Load_AJAX();
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
    
    protected function _Load_AJAX() { 
        // Load each of the form shortcodes
        foreach (new DirectoryIterator(VCFF_REPORTS_DIR.'/ajax') as $FileInfo) {
            // If this is a directory dot
            if($FileInfo->isDot()) { continue; }
            // If this is a directory
            if($FileInfo->isDir()) { continue; }
            // If this is not false
            if (stripos($FileInfo->getFilename(),'.tpl') !== false) { continue; }
            // Include the file
            require_once(VCFF_REPORTS_DIR.'/ajax/'.$FileInfo->getFilename());
        }
    }

}

new VCFF_Reports_Admin();
