<?php

class RecipeCan_Models_Abstract extends RecipeCan_Abstract {

    public function name() {
        return $this->_name();
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

    public function save($data, $where = array()) {
        global $wpdb;
        $find = $this->find($where);

        if ($find) {
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

        if (count($where) == 0) {
            return null;
        }

        global $wpdb;

        $sql = array();
        foreach ($where as $column => $value) {
            $sql[] = mysql_real_escape_string($column) . " = " . mysql_real_escape_string($value);
        }
        $where_string = implode(", ", $sql);

        $data = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * FROM " . mysql_real_escape_string($this->table_name()) .
                                " WHERE " . $where_string
                        ), ARRAY_A
        );

        if (isset($data['id'])) {
            return $this->make_row($data);
        } else {
            return null;
        }
    }

    public function find_by_id($id) {
        return $this->find(array('id' => $id));
    }

    public function all_data() {
        global $wpdb;
        return $wpdb->get_results(
                'select * from ' .
                mysql_real_escape_string($this->table_name()) .
                ' order by recipecan_id desc',
                ARRAY_A
        );
    }

    public function all() {
        $all = array();
        foreach ($this->all_data() as $data) {
            $all[] = $this->make_row($data);
        }
        return $all;
    }

    public function make_row($data) {
        $name = "RecipeCan_Row_" . ucfirst($this->_name);
        $row = new $name($data, $this);
        $row->options = $this->options;
        return $row;
    }

}

?>