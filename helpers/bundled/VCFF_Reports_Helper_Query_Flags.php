<?php

class VCFF_Reports_Helper_Query_Flags {
    
    public $report_id;
    
    public $with;
    
    public $without;
    
    public $order;
    
    public $limit;
    
    public function Report($report_id) {
    
        $this->report_id = $report_id;
        
        return $this;
    }
    
    public function With_Flags($flags,$condition) {
    
        $this->with = array($flags,$condition);
        
        return $this;
    }

    public function Without_Flags($flags,$condition) {
    
        $this->without = array($flags,$condition);
        
        return $this;
    }
    
    public function Order($columns) {
        
        $this->order = $columns;
        
        return $this;
    }
    
    public function Limit($to,$offset='0') {
    
        $this->limit = array($to,$offset);
        
        return $this;
    }
    
    public function Find($count=false) {
        // Create a new sql helper
        $reports_helper_sql_entry = new VCFF_Reports_Helper_SQL_Entries();
        // Retrieve the flags table
        $entry_table = $reports_helper_sql_entry->Get_Entry_Table();
        // If we are not wanting to just count
        if (!$count) {
            // Create a regular select statement
            $sql = "SELECT * FROM $entry_table AS Entry WHERE Entry.event_id = %s ";
        } // Otherwise we want a count of the results
        else { $sql = "SELECT COUNT(*) FROM $entry_table AS Entry WHERE Entry.event_id = %s "; }
        // Create the prepare
        $prepare = array($this->report_id);
        // Retrieve the with condition results
        $with_condition = $this->_Get_With_SQL();
        // If there are with condition results
        if (is_array($with_condition)) {
            // Merge the prepare array
            $prepare = array_merge($prepare,$with_condition['prepare']);
            // Build the sql statment
            $sql .= ' AND '.$with_condition['sql']; 
        }
        // Retrieve the with condition results
        $without_condition = $this->_Get_Without_SQL();
        // If there are with condition results
        if (is_array($without_condition)) {
            // Merge the prepare array
            $prepare = array_merge($prepare,$without_condition['prepare']);
            // Build the sql statment
            $sql .= ' AND '.$without_condition['sql'];
        }
        // Retrieve the with condition results
        $ordering = $this->_Get_Order_SQL();
        // If there are with condition results
        if (is_array($ordering)) {
            // Merge the prepare array
            $prepare = array_merge($prepare,$ordering['prepare']);
            // Build the sql statment
            $sql .= $ordering['sql'];
        }
        // Retrieve the with condition results
        $limit = $this->_Get_Limit_SQL();
        // If there are with condition results
        if (is_array($limit)) {
            // Merge the prepare array
            $prepare = array_merge($prepare,$limit['prepare']);
            // Build the sql statment
            $sql .= $limit['sql'];
        }
        // Create the database table
		global $wpdb;
        // If we are not just counting the results
        if (!$count) {
            // Retrieve the results
            return $wpdb->get_results($wpdb->prepare($sql,$prepare));
        } // Otherwise if we just want a count 
        else { 
            // Retrieve the results
            $result = $wpdb->get_row($wpdb->prepare($sql,$prepare),ARRAY_N);
            // Return the resulting count
            return $result[0];
        }
    }
    
    protected function _Get_With_SQL() {
        // Return out if no flags were provided
        if (!$this->with || !is_array($this->with) || count($this->with) < 1) { return false; }
        // The list of flags to look for
        $with_flags = $this->with[0];
        // The condition of the flags
        $with_condition = $this->with[1];
        // Create a new sql helper
        $reports_helper_sql_flags = new VCFF_Reports_Helper_SQL_Flags();
        // Retrieve the flags table
        $flags_table = $reports_helper_sql_flags->Get_Flags_Table();
        // The sql related vars
        $sql = ''; $prepare = array();
        // Loop through each flag
        foreach ($with_flags as $k => $flag) {
            // If we are looking for ANY match
            if ($with_condition == 'any') {
                // Use an OR operator
                $sql .= $sql ? " OR " : "";
            } // Otherwise use an AND operator
            else { $sql .= $sql ? " AND " : ""; }
            // Populate the SQL string
            $sql .= "EXISTS (SELECT * FROM ".$flags_table." AS Flags WHERE Flags.entry_uuid = Entry.uuid AND Flags.flag_code = %s)";
            // Add to the prepare array
            $prepare[] = $flag;
        }
        // Return the resulting string
        return array(
            'sql' => '('.$sql.')',
            'prepare' => $prepare
        );
    }
    
    protected function _Get_Without_SQL() {
        // Return out if no flags were provided
        if (!$this->without || !is_array($this->without) || count($this->without) < 1) { return false; }
        // The list of flags to look for
        $without_flags = $this->without[0];
        // The condition of the flags
        $without_condition = $this->without[1];
        // Create a new sql helper
        $reports_helper_sql_flags = new VCFF_Reports_Helper_SQL_Flags();
        // Retrieve the flags table
        $flags_table = $reports_helper_sql_flags->Get_Flags_Table();
        // The sql related vars
        $sql = ''; $prepare = array();
        // Loop through each flag
        foreach ($without_flags as $k => $flag) {
            // If we are looking for ANY match
            if ($without_condition == 'any') {
                // Use an OR operator
                $sql .= $sql ? " OR " : "";
            } // Otherwise use an AND operator
            else { $sql .= $sql ? " AND " : ""; }
            // Populate the SQL string
            $sql .= "NOT EXISTS (SELECT * FROM ".$flags_table." AS Flags WHERE Flags.entry_uuid = Entry.uuid AND Flags.flag_code = %s)";
            // Add to the prepare array
            $prepare[] = $flag;
        }
        // Return the resulting string
        return array(
            'sql' => '('.$sql.')',
            'prepare' => $prepare
        );
    }
    
    protected function _Get_Order_SQL() {
        // The list of flags to look for
        $order_columns = $this->order;
        // Return out if no flags were provided
        if (!$order_columns || !is_array($order_columns) || count($order_columns) < 1) { return false; }
        // The sql related vars
        $sql = ' ORDER BY '; $prepare = array();
        // The multiple flag
        $multiple = false; 
        // Loop through each flag
        foreach ($order_columns as $k => $v) {
            // Default variables
            $column = ''; $direction = 'DESC';
            // If the key is not a number
            if (!is_numeric($k)) { 
                // The column is k
                $column = $k;
                // The direction is v
                $direction = strtoupper($v);
            } // The column is the value
            else { $column = $v; }
            // Populate the order by statement
            $sql .= $multiple ? ", Entry.".$column." ".$direction : " Entry.".$column." ".$direction;
            // Multiple flag
            $multiple = true;
        } 
        // Return the resulting string
        return array(
            'sql' => $sql,
            'prepare' => $prepare
        );
    }
    
    protected function _Get_Limit_SQL() {
        // If no limit, then return out
        if (!$this->limit || !is_array($this->limit) || count($this->limit) != 2) { return false; }
        // The list of flags to look for
        $limit_to = $this->limit[0];
        // The list of flags to look for
        $limit_from = $this->limit[1];
        // Build the sql string
        $sql .= " LIMIT %d, %d";
        // Populate the prepare to
        $prepare[] = $limit_to;
        // Populate the prepare from
        $prepare[] = $limit_from;
        // Return the resulting string
        return array(
            'sql' => $sql,
            'prepare' => $prepare
        );
    }
}