<?php
class LogicHandler extends  DataModel{
    public function __construct() {
        parent::__construct('logic_object');
    }

    function getLogicObjectsByFilter($search_data,$start,$limit){
        $search_arr=[];
        if (!empty($search_data)) {
            foreach ($search_data as $column => $value) {
                $search_arr[] = "obj.$column = \"$value\"";
            }
        }
        if (empty($search_arr))
            $search_str = " ";
        else
            $search_str = "WHERE ".implode(' AND ',$search_arr);
        $conn = DBConnection::getInstance();
        $query = "call getLogicObjectsByFilter('$search_str', '$start','$limit');";
        $objects = $conn->performQueryFetchAll($query);
        if (!$objects)
            $objects = [];
        $query = "SELECT FOUND_ROWS() as obj_count";
        $count = $conn->performQueryFetch($query);
        return array_merge($count,$objects);
    }
}