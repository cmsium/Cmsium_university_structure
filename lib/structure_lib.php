<?php


function addStructureType($name){
    $conn = DBConnection::getInstance();
    $query = "call addStructureType('$name');";
    return $conn->performQuery($query);
}

function deleteStructureType($id){
    $conn = DBConnection::getInstance();
    $query = "call deleteStructureType('$id');";
    return $conn->performQuery($query);
}

//function generateId($table_name){
//    $conn = DBConnection::getInstance();
//    $query ="call getTableStructureData('$table_name')";
//    $id = $conn->performQueryFetch($query);
//    do {
//        switch($id['DATA_TYPE']){
//            case 'varchar':
//                $generated_id = randomString($id['CHARACTER_MAXIMUM_LENGTH']);
//                break;
//        }
//        $query = "SELECT * FROM $table_name WHERE {$id['COLUMN_NAME']} = '$generated_id';";
//        $result = $conn->performQueryFetch($query);
//    } while (!empty($result));
//    return $generated_id;
//}

function randomString($length, $string = 'abcdef0123456789'){
    $result='';
    for($i=0;$i<$length;$i++){
        $result .= $string[mt_rand(0,strlen($string) - 1)];
    }
    return $result;
}


function addStructureObject($data){
    $conn = DBConnection::getInstance();
    $id = generateId('structure_object');
    $query = "call addStructureObject('$id','{$data['obj_name']}','{$data['type_id']}','{$data['parent_id']}');";
    return $conn->performQuery($query);
}


function deleteStructureObject($id){
    $conn = DBConnection::getInstance();
    $query = "call deleteStructureObject('$id')";
    return $conn->performQuery($query);
}


function getStructureObject($id){
    $conn = DBConnection::getInstance();
    $query = "call getStructureObject('$id');";
    return $conn->performQueryFetch($query);
}


function getStructureObjectsByFilter($search_data,$start,$limit){
    $search_arr=[];
    foreach ($search_data as $column => $value){
        $search_arr[] = "obj.$column = \"$value\"";
    }
    if (empty($search_arr))
        $search_str = "";
    else
        $search_str = "WHERE ".implode(' AND ',$search_arr);
    $conn = DBConnection::getInstance();
    $query = "call getStructureObjectsByFilter('$search_str', '$start','$limit');";
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
            $update_arr[] = "$key = \"$value\"";
        }
        $update_str = implode(',',$update_arr);
    } else {
        return true;
    }
    $conn = DBConnection::getInstance();
    $query = "call updateStructureObject('$update_str','$id')";
    return $conn->performQuery($query);
}

function getStructureTypes(){
    $conn = DBConnection::getInstance();
    $query = "call getStructureTypes();";
    return $conn->performQueryFetchALL($query);
}
