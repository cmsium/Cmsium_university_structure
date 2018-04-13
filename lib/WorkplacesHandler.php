<?php

class WorkplacesHandler extends DataModel {

    function __construct() {
        parent::__construct('workplaces');
    }

    function getAll($data){
        if (isset($data['start']) and isset($data['limit'])){
            $start = $data['start'];
            $offset = $data['limit'];
            unset($data['limit']);
            unset($data['start']);
        } else {
            $start=0;
            $offset=10000;
        }
        if (!empty($data)){
            $where_arr=[];
            foreach ($data as $key => $value){
                $where_arr[] = "$key LIKE \"%$value%\"";
            }
            $query_where = "WHERE ".implode(' AND ',$where_arr);
        } else {
            $query_where = '';
        }
        $conn = DBConnection::getInstance();
        if ($query_where){
            $query = "call getAllWorkplaces('$this->table_name','$query_where',$start,$offset);";
        }
        else {
            $query = "call getAllWorkplaces('$this->table_name',' ',$start,$offset);";
        }
        $categories =  $conn->performQueryFetchAll($query);
        if ($categories){
            $query = "SELECT FOUND_ROWS() as count";
            $this->count = $conn->performQueryFetch($query)['count'];
        }
        return $categories;
    }
}