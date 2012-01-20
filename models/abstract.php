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

        $option_name = $this->table_name() . '_table_version';
        $option_value = $this->options['plugin_version'];

        if ($this->get_option($option_name)) {
            $this->update_option($option_name, $option_value);
        } else {
            $this->add_option($option_name, $option_value);
        }

        $this->after_make_table();
    }

    public function save($data, $where = array()) {
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

    public function delete($where) {

        // guard to make sure they don't delete everything
        if (count($where) == 0) {
            return false;
        }

        global $wpdb;

        $sql = array();
        foreach ($where as $column => $value) {
            $sql[] = "`" . $wpdb->escape($column) . "` = '" . $wpdb->escape($value) . "'";
        }
        $where_string = implode(" and ", $sql);

        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM `" . $wpdb->escape($this->table_name()) . "`" .
                " WHERE " . $where_string
            ), ARRAY_A
        );

    }

    public function find($where, $other = array()) {

        if (count($where) == 0) {
            return null;
        }

        global $wpdb;

        $sql = array();
        foreach ($where as $column => $value) {
            $sql[] = "`" . mysql_real_escape_string($column) . "` = '" . mysql_real_escape_string($value) . "'";
        }
        $where_string = implode(" and ", $sql);

        $data = $wpdb->get_row(
                        $wpdb->prepare(
                                "SELECT * FROM `" . mysql_real_escape_string($this->table_name()) . "`" .
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

    // make this more orm/oo 
    public function all_data($limit = null) {
        global $wpdb;

        $limit_string = "";
        if ($limit != null) {
            $limit_string = "limit " . mysql_real_escape_string((int) $limit);
        }

        return $wpdb->get_results(
                'select * from `' . mysql_real_escape_string($this->table_name()) . '`' .
                ' order by recipecan_id desc ' . $limit_string,
                ARRAY_A
        );
    }

    public function all($limit = null) {
        $all = array();
        foreach ($this->all_data($limit) as $data) {
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
