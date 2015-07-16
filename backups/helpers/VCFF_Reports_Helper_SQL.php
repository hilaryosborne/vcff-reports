<?php

// Require the wp upgrade library
require_once( ABSPATH.'wp-admin/includes/upgrade.php' );

class VCFF_Reports_Helper_SQL {

    protected $sql_version = '';
    
    public function SQL_Check(){
        // Setup the main entries table
        $this->_SQL_Entries_Table();
        $this->_SQL_Entries_Tags_Table();
    }
    
    public function Get_Entries_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        return $wpdb->prefix."vcff_report_entries"; 
    }
    
    protected function _SQL_Entries_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table(); 
        // Charset string
		$charset_collate = $wpdb->get_charset_collate();
        // SQL for the entries table
        $entries_table_sql = "CREATE TABLE $entries_table (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			uuid varchar(255) NOT NULL,
            form_uuid varchar(255) NOT NULL,
            form_key varchar(255) NOT NULL,
            event_id varchar(255) NOT NULL,
            event_code varchar(255) NOT NULL,
            data_fields longtext NOT NULL,
            data_additional longtext NULL,
            time_created bigint(20) UNSIGNED NOT NULL,
            time_modified bigint(20) UNSIGNED NULL,
            date_created date NOT NULL,
            date_modifed date NULL,
			submitted_ip varchar(32) NOT NULL,
			PRIMARY KEY (id)
		) ".$charset_collate.";"; 
        // Use the dbdelta to compare and upgrade
        dbDelta($entries_table_sql);
    }
    
    public function Get_Entries_Tags_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        return $wpdb->prefix."vcff_report_entries_tags"; 
    }
    
    protected function _SQL_Entries_Tags_Table() {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_tags_table = $this->Get_Entries_Tags_Table(); 
        // Charset string
		$charset_collate = $wpdb->get_charset_collate();
        // SQL for the entries table
        $entries_tags_table_sql = "CREATE TABLE $entries_tags_table (
			id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            uuid varchar(255) NOT NULL,
			entry_id varchar(255) NOT NULL,
            tag_code varchar(255) NOT NULL,
            tag_data longtext NOT NULL,
            time_created bigint(20) UNSIGNED NOT NULL,
            time_modified bigint(20) UNSIGNED NULL,
            date_created date NOT NULL,
            date_modifed date NULL,
			PRIMARY KEY (id)
		) ".$charset_collate.";"; 
        // Use the dbdelta to compare and upgrade
        dbDelta($entries_tags_table_sql);
    }
    
    public function Create_Entry($data) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table(); 
        // If no uuid, create one
        if (!isset($data['uuid'])) { $data['uuid'] = uniqid(); }
        // Insert the new entry
        $wpdb->insert($entries_table, $data);
        // Create a new entry helper
        $reports_helper_entry = new VCFF_Reports_Helper_Entry();
        // Set the entry id and retrieve
        $entry_instance = $reports_helper_entry
            ->Set_Entry_ID($wpdb->insert_id)
            ->Retrieve();
        // Return the entry instance
        return $entry_instance;
    }
    
    public function Create_Entry_Tag($data) {
        // Create the database table
		global $wpdb; 
        // If no uuid, create one
        if (!isset($data['uuid'])) { $data['uuid'] = uniqid(); }
        // Submission table name (including wp prefix)
        $entries_tags_table = $this->Get_Entries_Tags_Table(); 
        // Insert the new entry
        $wpdb->insert($entries_tags_table, $data);
    }
    
    public function Delete_Entry_Tag($data) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_tags_table = $this->Get_Entries_Tags_Table(); 
        // Insert the new entry
        $wpdb->delete($entries_tags_table, $data);
    }
    
    public function Select_Entry_Tags($entry_id) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_tags_table = $this->Get_Entries_Tags_Table();
        // The primary select statement
        $sql_string = "SELECT * FROM $entries_tags_table AS Tag WHERE Tag.entry_id = %d;";
        // Populate the prepare values
        $sql_prepare[] = $entry_id;
        // Retrieve the results
        $tags = $wpdb->get_results($wpdb->prepare($sql_string,$sql_prepare));
        // Retrieve a list of all the published vv forms
        return $tags;
    }
    
    public function Select_Entry_Tag($entry_id,$tag_code) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_tags_table = $this->Get_Entries_Tags_Table();
        // The primary select statement
        $sql_string = "SELECT * FROM $entries_tags_table AS Tag WHERE Tag.entry_id = %d AND Tag.tag_code = %s;";
        // Populate the prepare values
        $sql_prepare[] = $entry_id;
        $sql_prepare[] = $tag_code;
        // Retrieve the results
        $tags = $wpdb->get_results($wpdb->prepare($sql_string,$sql_prepare));
        // Retrieve a list of all the published vv forms
        return $tags[0];
    }
    
    public function Select_Tag_By_UUID($uuid) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_tags_table = $this->Get_Entries_Tags_Table();
        // The primary select statement
        $sql_string = "SELECT * FROM $entries_tags_table AS Tag WHERE Tag.uuid = %s;";
        // Populate the prepare values
        $sql_prepare[] = $uuid;
        // Retrieve the results
        $tags = $wpdb->get_results($wpdb->prepare($sql_string,$sql_prepare));
        // Retrieve a list of all the published vv forms
        return $tags[0];
    }
    
    public function Select_Entries_By_Form_UUID($form_uuid) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // Retrieve the results
        $entries = $wpdb->get_results($wpdb->prepare("SELECT * FROM $entries_table AS Entry WHERE form_uuid = %s",array($form_uuid)));
        // Retrieve a list of all the published vv forms
        return $entries;
    }
    
    public function Select_Entries_By_Report($params) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // The primary select statement
        $sql_string = "SELECT * FROM $entries_table AS Entry WHERE";

        $sql_prepare = array();
        
        if (isset($params['filter_event_id'])) {
            
            $sql_string .= " Entry.event_id = %d";
            
            $sql_prepare[] = $params['filter_event_id'];
        } 
        else { return; }
        
        if (isset($params['tags_required']) && is_array($params['tags_required'])) {

            foreach ($params['tags_required'] as $k => $tag_code) {
                
                $sql_string .= $sql_string ? " AND " : "";

                $sql_string .= "EXISTS ( SELECT * FROM ".$this->Get_Entries_Tags_Table()." AS Tags WHERE Tags.entry_id = Entry.id AND Tags.tag_code = %s )";

                $sql_prepare[] = $tag_code;
            }
        }
        
        if (isset($params['tags_exclude']) && is_array($params['tags_exclude'])) {

            foreach ($params['tags_exclude'] as $k => $tag_code) {
            
                $sql_string .= $sql_string ? " AND " : "";

                $sql_string .= "NOT EXISTS ( SELECT * FROM ".$this->Get_Entries_Tags_Table()." AS Tags WHERE Tags.entry_id = Entry.id AND Tags.tag_code = %s )";

                $sql_prepare[] = $tag_code;
            }
        }

        if (isset($params['filter_date']) && is_array($params['filter_date'])) {
            
            $sql_string .= " AND YEAR(Entry.date_created) = %d AND MONTH(Entry.date_created) = %d";
            
            $sql_prepare[] = $params['filter_date'][0];
            $sql_prepare[] = $params['filter_date'][1];
        }

        if (isset($params['orderby']) && is_array($params['orderby'])) {

            if (in_array($params['orderby'][0],array('time_created','time_modified','date_created','date_modifed'))) {
                
                $order_rule = $params['orderby'][1] == 'ASC' ? 'ASC' : 'DESC';
                
                $sql_string .= " ORDER BY Entry.".$params['orderby'][0]." ".$order_rule;     
            }

            
        }
        
        if (isset($params['limit']) && is_array($params['limit'])) {
            
            $sql_string .= " LIMIT %d , %d";
            
            $sql_prepare[] = $params['limit'][0];
            $sql_prepare[] = $params['limit'][1];
        } 
        // Retrieve the results
        $entries = $wpdb->get_results($wpdb->prepare($sql_string,$sql_prepare));
        // Retrieve a list of all the published vv forms
        return $entries;
    }
    
    public function Count_Entries_By_Report($params) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        
        $sql_string = "SELECT COUNT(ID) FROM $entries_table AS Entry WHERE";
        $sql_prepare = array();
        
        if (isset($params['filter_event_id'])) {
            
            $sql_string .= " Entry.event_id = %d";
            
            $sql_prepare[] = $params['filter_event_id'];
        } 
        else { return; }
        
        if (isset($params['tags_required']) && is_array($params['tags_required'])) {

            foreach ($params['tags_required'] as $k => $tag_code) {
                
                $sql_string .= $sql_string ? " AND " : "";

                $sql_string .= "EXISTS ( SELECT * FROM ".$this->Get_Entries_Tags_Table()." AS Tags WHERE Tags.entry_id = Entry.id AND Tags.tag_code = %s )";

                $sql_prepare[] = $tag_code;
            }
        }
        
        if (isset($params['tags_exclude']) && is_array($params['tags_exclude'])) {

            foreach ($params['tags_exclude'] as $k => $tag_code) {
            
                $sql_string .= $sql_string ? " AND " : "";

                $sql_string .= "NOT EXISTS ( SELECT * FROM ".$this->Get_Entries_Tags_Table()." AS Tags WHERE Tags.entry_id = Entry.id AND Tags.tag_code = %s )";

                $sql_prepare[] = $tag_code;
            }
        }
        
        if (isset($params['filter_date']) && is_array($params['filter_date'])) {
            
            $sql_string .= " AND YEAR(Entry.date_created) = %d AND MONTH(Entry.date_created) = %d";
            
            $sql_prepare[] = $params['filter_date'][0];
            $sql_prepare[] = $params['filter_date'][1];
        }
        // Retrieve the results
        $entries = $wpdb->get_var($wpdb->prepare($sql_string,$sql_prepare));
        // Retrieve a list of all the published vv forms
        return $entries;
    }
    
    public function Get_Entry_Of_Status_Count($event_id,$status) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // Retrieve the count
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) 
                FROM $entries_table 
                WHERE event_id = %s
                AND status = %s", $event_id, $status));
        // Return the count
        return $count;
    }
    
    public function Select_Entry_By_UUID($uuid) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // Insert the new entry
        $entries = $wpdb->get_results($wpdb->prepare("SELECT * 
                FROM $entries_table
                WHERE uuid = %d
                LIMIT 0 , 1",$uuid));
        // Retrieve a list of all the published vv forms
        return $entries[0];
    }
    
    public function Select_Entry($id) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // Insert the new entry
        $entries = $wpdb->get_results($wpdb->prepare("SELECT * 
                FROM $entries_table
                WHERE id = %d
                LIMIT 0 , 1",$id));
        // Retrieve a list of all the published vv forms
        return $entries[0];
    }
    
    public function Get_Last_Entry($event_id) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // Retrieve the count
        $last_submission = $wpdb->get_results($wpdb->prepare("SELECT * 
                FROM $entries_table 
                WHERE event_id = %s 
                ORDER BY time_created DESC
                LIMIT 0 , 1", $event_id));
        // Return the count
        return $last_submission[0];
    }
    
    public function Flag_Entry($event_instance,$flag) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // Retrieve the count
        return $wpdb->update($entries_table,array('flagged' => $flag, 'time_modified' => time()),array('id' => $event_instance->Get_ID(), 'event_id' => $event_instance->Get_Action_ID()));
    }
    
    public function Update_Entry_Status($event_id,$record_id,$status) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // Retrieve the count
        return $wpdb->update($entries_table,array('status' => $status, 'time_modified' => time()),array('id' => $record_id, 'event_id' => $event_id));
    }
    
    public function Get_Entry_Count($event_id) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // Retrieve the count
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) 
                FROM $entries_table 
                WHERE event_id = %s", $event_id));
        // Return the count
        return $count;
    }
    
    public function Get_Flagged_Entry_Count($event_id) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // Retrieve the count
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) 
                FROM $entries_table 
                WHERE event_id = %s
                AND flagged = 1", $event_id));
        // Return the count
        return $count;
    }
    
    public function Get_Entry_Count_Of_Status($event_id,$status) {
        // Create the database table
		global $wpdb; 
        // Submission table name (including wp prefix)
        $entries_table = $this->Get_Entries_Table();
        // Retrieve the count
        $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) 
                FROM $entries_table 
                WHERE event_id = %s
                AND status = %s", $event_id, $status));
        // Return the count
        return $count;
    }
}