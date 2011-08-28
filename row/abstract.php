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

    public function get($data) {
        if (isset($this->data[$data])) {
            return $this->data[$data];
        } else {
            return '';
        }
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
