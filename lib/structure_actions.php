<?php


function addType(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_POST,'addStructureTypeMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    if (!addStructureType($data['type_name'])) {
        throwException(CREATE_STRUCTURE_TYPE_ERROR);
    }

}

function deleteType(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'deleteStructureTypeMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    if (!deleteStructureType($data['id'])) {
        throwException(DELETE_STRUCTURE_TYPE_ERROR);
    }
}

function addObject(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'addStructureObjectMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    if (!addStructureObject($data)){
        throwException(ADD_STRUCTURE_OBJECT_ERROR);
    }
}

function updateObject(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'updateStructureObjectMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    if (updateStructureObject($data) === false){
        throwException(UPDATE_STRUCTURE_OBJECT_ERROR);
    }
}

function deleteObject(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'deleteStructureObjectMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    if (!deleteStructureObject($data['obj_id'])){
        throwException(DELETE_STRUCTURE_OBJECT_ERROR);
    }
}


function getObject(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'getStructureObjectMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $obj = getStructureObject($data['obj_id']);
    if ($obj){
        //TODO send enumerative array, not json
        echo json_encode($obj);
    } else {
        throwException(GET_STRUCTURE_OBJECT_ERROR);
    }
}

function getObjects(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'getStructureObjectsMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    if (isset($data['start'])) {
        $start = $data['start'];
        unset($data['start']);
    } else {
        $start = 0;
    }
    if (isset($data['limit'])) {
        $limit = $data['limit'];
        unset($data['limit']);
    } else {
        $limit = 1000000;
    }
    $objects = getStructureObjectsByFilter($data,$start,$limit);
    //TODO send enumerative array, not json
    echo json_encode($objects);
}

function getTypes(){
    checkAuth();
    $types = getStructureTypes();
    if ($types){
        //TODO send enumerative array, not json
        echo json_encode($types);
    } else {
        throwException(GET_STRUCTURE_TYPE_ERROR);
    }
}
