<?php

abstract class RecipeCan_Row_Abstract extends RecipeCan_Abstract {

    public $data = array();
    private $_model = null;

    public function  __construct($data = array(), $model = null) {

        $this->data = $data;

        if (isset($model)) {
            $this->_model = $model;
        } 
        
    }

    public function __get($key) {
		return isset($this->data[$key]) ? $this->data[$key] : NULL;
	}

	public function __isset($key) {
		return isset($this->data[$key]);
    }

    public function set_array($a) {
        foreach ($a as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function set($name, $value) {
        $this->data[$name] = $value;
    }

    public function get($data) {
        if (isset($this->data[$data])) {
            return $this->data[$data];
        } else {
            return '';
        }
    }

    public function data_to_a($field) {
        $text = $this->get($field);
        $text = trim($text);
        $text = preg_replace("/[\r\n]{2,}/", "\n", $text);
        $as_a = split("\n", $text);

        return $as_a;
    }

    private function _ensure_model() {
        if ($this->_model == null) {
            $name = "RecipeCan_Models_" . ucfirst($this->_name);
            $model = new $name();
            $model->options = $this->options;
            //$model->api = $this->make_api();
            $this->_model = $model;
        }
    }

    public function save() {
        $this->_ensure_model();
        
        if (isset($this->data['id'])) {
            $this->_model->save($this->data, array('id' => $this->data['id']));
        } else {
            $this->_model->save($this->data, array());
        }
    }

}

?>
