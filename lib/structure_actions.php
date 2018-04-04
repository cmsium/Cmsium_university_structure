<?php


function addType(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_POST,'addStructureTypeMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $type_handler = new DataModel($data['table'].'_types');
    unset($data['table']);
    $id = generateId($type_handler->id_info['CHARACTER_MAXIMUM_LENGTH'],$data['type_name'],$type_handler->table_name);
    if (!$type_handler->add($data,$id)) {
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
    $type_handler = new DataModel($data['table'].'_types');
    unset($data['table']);
    if (!$type_handler->delete($data['id'])) {
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
    $obj_handler = new DataModel($data['table'].'_object');
    $id = generateId($obj_handler->id_info['CHARACTER_MAXIMUM_LENGTH'],$data['type_name'],$obj_handler->table_name);
    unset($data['table']);
    if (!$obj_handler->add($data,$id)){
        throwException(ADD_STRUCTURE_OBJECT_ERROR);
    }
}

function updateObject(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_POST,'updateStructureObjectMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $obj_handler = new DataModel($data['table'].'_object');
    $id = $data['obj_id'];
    unset($data['obj_id']);
    unset($data['table']);
    if ($obj_handler->update($id,$data) === false){
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
    $obj_handler = new DataModel($data['table'].'_object');
    if (!$obj_handler->delete($data['obj_id'])){
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
    $obj_handler = new DataModel($data['table'].'_object');
    $obj = $obj_handler->read($data['obj_id'],['fkeys'=>['type_id'=>$data['table'].'_types']]);
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
    if (isset($data['table'])){
        $table = $data['table'];
        unset($data['table']);
    }
    switch ($table){
        case 'structure':
            $obj_handler = new StructureHandler();
            $objects = $obj_handler->getStructureObjectsByFilter($data,$start,$limit);
            break;
        case 'logic':
            $obj_handler = new LogicHandler();
            $objects = $obj_handler->getLogicObjectsByFilter($data,$start,$limit);
            break;
        default:
            $obj_handler = new StructureHandler();
            $objects = $obj_handler->getStructureObjectsByFilter($data,$start,$limit);
            break;
    }
    //TODO send enumerative array, not json
    echo json_encode($objects);
}

function getTypes(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'getTypesMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $type_handler = new DataModel($data['table'].'_types');
    unset($data['table']);
    $types = $type_handler->getAll(['start'=>0,'offset'=>10000]);
    if ($types){
        //TODO send enumerative array, not json
        echo json_encode($types);
    } else {
        throwException(GET_STRUCTURE_TYPE_ERROR);
    }
}

function showObject(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'showObjectMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $obj_handler = new DataModel($data['table'].'_object');
    if (!($result = $obj_handler->read($data['obj_id']))){
        throwException(DELETE_STRUCTURE_OBJECT_ERROR);
    }
    var_dump($result);
}

function addToStructure(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_POST,'addToStructureMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $handler = new NPDataModel($data['entity_type'].'_in_logic');
    $column = $data['entity_type'].'_id';
    if (!$handler->add([$column => $data['entity_id'],'logic_obj_id'=>$data['obj_id']])){
        throwException(UPDATE_STRUCTURE_OBJECT_ERROR);
    }
}
