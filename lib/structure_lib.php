<?php


function addStructureType($name){
    $conn = DBConnection::getInstance();
    $query = "INSERT INTO structure_types (type_name) VALUES ('$name');";
    return $conn->performQuery($query);
}

function deleteStructureType($id){
    $conn = DBConnection::getInstance();
    $query = "DELETE FROM structure_types WHERE type_id='$id';";
    return $conn->performQuery($query);
}

function generateId($table_name){
    $conn = DBConnection::getInstance();
    $query ="SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' and COLUMN_KEY = 'PRI';";
    $id = $conn->performQueryFetch($query);
    do {
        switch($id['DATA_TYPE']){
            case 'varchar':
                $generated_id = randomString($id['CHARACTER_MAXIMUM_LENGTH']);
                break;
        }
        $query = "SELECT * FROM $table_name WHERE {$id['COLUMN_NAME']} = '$generated_id';";
        $result = $conn->performQueryFetch($query);
    } while (!empty($result));
    return $generated_id;
}

function randomString($length, $string = 'abcdef0123456789'){
    $result='';
    for($i=0;$i<=$length;$i++){
        $result .= $string[mt_rand(0,strlen($string) - 1)];
    }
    return $result;
}


function addStructureObject($data){
    $conn = DBConnection::getInstance();
    $id = generateId('structure_object');
    $query = "INSERT INTO structure_object (obj_id,obj_name,type_id,parent_id) VALUES ('$id','{$data['obj_name']}','{$data['type_id']}','{$data['parent_id']}');";
    var_dump($query);
    return $conn->performQuery($query);
}


function deleteStructureObject($id){
    $conn = DBConnection::getInstance();
    $query = "DELETE FROM structure_object WHERE obj_id='$id'";
    return $conn->performQuery($query);
}


function getStructureObject($id){
    $conn = DBConnection::getInstance();
    $query = "SELECT obj.obj_id, obj.obj_name, obj.type_id, types.type_name, obj.parent_id, p_obj.obj_name as parent_name FROM structure_object AS obj 
              JOIN structure_types AS types ON obj.type_id = types.type_id
              JOIN structure_object AS p_obj ON obj.parent_id = p_obj.obj_id 
              WHERE obj.obj_id = '$id'";
    return $conn->performQueryFetch($query);
}


function getStructureObjectsByFilter($search_data,$start,$limit){
    $search_arr=[];
    foreach ($search_data as $column => $value){
        $search_arr[] = "obj.$column = '$value'";
    }
    if (empty($search_arr))
        $search_str = "";
    else
        $search_str = "WHERE ".implode(' AND ',$search_arr);
    $conn = DBConnection::getInstance();
    $query = "SELECT SQL_CALC_FOUND_ROWS obj.obj_id, obj.obj_name, obj.type_id, types.type_name,p_obj.obj_name as parent_name FROM structure_object AS obj 
              JOIN structure_types AS types ON obj.type_id = types.type_id
              JOIN structure_object AS p_obj ON obj.parent_id = p_obj.obj_id $search_str 
              LIMIT $start,$limit;";
    $objects = $conn->performQueryFetchAll($query);
    if (!$objects)
        $objects = [];
    $query = "SELECT FOUND_ROWS() as obj_count";
    $count = $conn->performQueryFetch($query);
    return array_merge($count,$objects);
}


function updateStructureObject($data){
    $id = $data['obj_id'];
    unset($data['obj_id']);
    if (!empty($data)) {
        $update_arr = [];
        foreach ($data as $key => $value) {
            $update_arr[] = "$key = '$value'";
        }
        $update_str = implode(',',$update_arr);
    } else {
        return true;
    }
    $conn = DBConnection::getInstance();
    $query = "UPDATE structure_object SET $update_str WHERE obj_id = '$id'";
    return $conn->performQuery($query);
}

function getStructureTypes(){
    $conn = DBConnection::getInstance();
    $query = "SELECT * FROM structure_types;";
    return $conn->performQueryFetchALL($query);
}
