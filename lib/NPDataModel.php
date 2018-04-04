<?php
class NPDataModel {

    public function __construct($table_name) {
        $this->table_name = $table_name;
    }

    public function add($data) {
        $conn = DBConnection::getInstance();
        $insert_names_str = implode(',', array_keys($data));
        $insert_values = [];
        foreach ($data as $value){
            $insert_values[] = "\"$value\"";
        }
        $insert_values_str = implode(',', $insert_values);
        $query = "call addDataToEntity('$this->table_name','$insert_names_str','$insert_values_str');";
        return $conn->performQuery($query);
    }
}