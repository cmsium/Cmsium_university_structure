<?php

function addWorkplace(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_POST,'addWorkplaceMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $handler = new DataModel('workplaces');
    $id = generateId($handler->id_info['CHARACTER_MAXIMUM_LENGTH'],$data['position_id'].$data['structure_id'],$handler->table_name);
    if (!$handler->add($data,$id)){
        throwException(CREATE_WORKPLACE_ERROR);
    }
}

function showWorkplace(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'showWorkplaceMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $handler = new WorkplacesHandler();
    $result = $handler->read($data['workplace_id'],['fkeys'=>['position_id'=>'positions','structure_id'=>'structure_object']]);
    echo json_encode($result);
}

function getWorkplaces(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'getWorkplacesMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    if (!isset($data['start'])){
        $data['start'] = 0;
    }
    if (!isset($data['limit'])){
        $data['limit'] = 100000;
    }
    $handler = new WorkplacesHandler();
    $result = $handler->getAll($data);
    $result['count'] = $handler->count;
    echo json_encode($result);
}

function deleteWorkplace(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'deleteWorkplaceMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $handler = new DataModel('workplaces');
    if ($handler->delete($data['workplace_id']) === false){
        throwException(DELETE_WORKPLACE_ERROR);
    }
}

function addPosition() {
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_POST, 'addPositionMask');
    if ($data === false) {
        throwException(DATA_FORMAT_ERROR);
    }
    $handler = new DataModel('positions');
    $id = generateId($handler->id_info['CHARACTER_MAXIMUM_LENGTH'], $data['position_name'], $handler->table_name);
    if (!$handler->add($data, $id)) {
        throwException(CREATE_POSITION_ERROR);
    }
}

function deletePosition(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'deletePositionMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $handler = new DataModel('positions');
    if (!$handler->delete($data['position_id'])){
        throwException(DELETE_POSITION_ERROR);
    }
}

function getPositions(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'getPositionsMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $handler = new DataModel('positions');
    $result = $handler->getAll(['start'=>0,'limit'=>10000]);
    echo json_encode($result);
}

function showPosition(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'showPositionMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $handler = new DataModel('positions');
    $result = $handler->read($data['position_id']);
    echo json_encode($result);
}

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
    $types = $type_handler->getAll(['start'=>0,'limit'=>10000]);
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
    if (!($result = $obj_handler->read($data['obj_id'],['fkeys'=>['type_id'=>$data['table'].'_types']]))){
        throwException(GET_STRUCTURE_OBJECT_ERROR);
    }
    echo json_encode($result);
}

function showObjectChildNodes(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'showObjectNodesMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $obj_handler = new NPDataModel($data['table'].'_in_logic');
    unset($data['table']);
    $result = $obj_handler->read($data);
    echo json_encode($result);
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
