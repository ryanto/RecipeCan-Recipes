<?php

class RecipeCan_Models_Abstract {

    public $options;
    public $api;

    public function add_option($name, $value) {
        add_option($this->options['prefix'] . $name, $value);
    }

    public function get_option($name) {
        return get_option($this->options['prefix'] . $name);
    }

    public function table_name() {
        global $wpdb;
        return $wpdb->prefix . $this->options['prefix'] . $this->_table_name;
    }

    public function ensure_table() {
        if (!$this->table_upto_date()) {
            $this->make_table();
        }
    }

    public function table_upto_date() {
        return ($this->get_option($this->table_name() . '_table_version') == $this->options['plugin_version']);
    }

    public function make_table() {
        global $wpdb;

        $sql = "CREATE TABLE " . $this->table_name() . " (\n";
        $columns = array();
        $columns[] = "id mediumint(9) NOT NULL AUTO_INCREMENT";
        foreach ($this->_table_columns as $column) {
            $columns[] = $column;
        }
        $columns[] = "UNIQUE KEY id (id)\n";

        $sql .= implode(",\n", $columns);
        $sql .= ");";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $this->add_option($this->table_name() . '_table_version', $this->options['plugin_version']);
    }

    public function save($data, $where) {
        global $wpdb;
        $obj = $this->find($where);

        if ($obj) {
            $this->_update($data, $where);
        } else {
            $this->_insert($data);
        }
    }

    private function _update($data, $where) {
        global $wpdb;
        $wpdb->update($this->table_name(), $data, $where);
    }

    private function _insert($data) {
        global $wpdb;
        $wpdb->insert($this->table_name(), $data);
    }

    public function find($where) {
        global $wpdb;

        $sql = array();
        foreach ($where as $column => $value) {
            $sql[] = mysql_real_escape_string($column) . " = " . mysql_real_escape_string($value);
        }
        $where_string = implode(", ", $sql);

        $obj = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * FROM " . mysql_real_escape_string($this->table_name()) .
                                " WHERE " . $where_string
                        )
        );
        if (isset($obj->id)) {
            return $obj;
        } else {
            return null;
        }
    }

    public function all() {
        global $wpdb;
        return $wpdb->get_results('select * from ' . mysql_real_escape_string($this->table_name()));
    }

}

?>