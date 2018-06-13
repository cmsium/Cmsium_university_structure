<?php


function getTwigs(){
    checkAuth();
    $handler = new RelationsHandler();
    echo json_encode($handler->getTwigs());

}

function addWorkplace(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_POST,'addWorkplaceMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $handler = new DataModel('workplaces');
    $id = generateId($handler->id_info['CHARACTER_MAXIMUM_LENGTH'],$data['position_id'],$handler->table_name);
    $struc = $data['structure_node_id'];
    unset($data['structure_node_id']);
    if (!$handler->add($data,$id)){
        throwException(CREATE_WORKPLACE_ERROR);
    }
    $ref_handler = new RelationsHandler(1);
    if (!$ref_handler->add(2,$id,$struc)){
        throwException(CREATE_WORKPLACE_ERROR);
    }
}

//TODO make it work
function showWorkplace(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'showWorkplaceMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $rel_handler = new RelationsHandler();
    $obj_data = $rel_handler->read($data['node_id'],false);
    $obj_handler = new DataModel('workplaces');
    $result = $obj_handler->read($obj_data['ent_id'],['fkeys'=>['position_id'=>'positions']]);
    echo json_encode(array_merge($result,$obj_data));
}

function getWorkplaces(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'getWorkplacesMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    if (!isset($data['start'])){
        $start = 0;
    } else {
        $start =$data['start'];
    }
    if (!isset($data['limit'])){
        $limit = 100000;
    } else {
        $limit = $data['limit'];
    }
    $obj_handler = new RelationsHandler(2);
    $data['kind'] = 2;
    $objects = $obj_handler->getNodesByFilter($data,$start,$limit);
    $entity_handler = new DataModel('workplaces');
    $result=[];
    foreach ($objects as $object){
        if (isset($object['node_id'])) {
            $data = $entity_handler->read($object['ent_id'],['fkeys' => ['position_id' => 'positions']]);
            $result[] = array_merge($object, $data);
        }
    }
    $result['obj_count'] = $objects['obj_count'];
    //TODO send enumerative array, not json
    echo json_encode($result);
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
    $type_handler = new DataModel('structure_types');
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
    $type_handler = new DataModel('structure_types');
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
    $obj_handler = new DataModel('structure_object');
    $id = generateId($obj_handler->id_info['CHARACTER_MAXIMUM_LENGTH'],$data['type_name'],$obj_handler->table_name);
    $ref_tree_data = $data;
    unset($data['parent_id']);
    unset($data['twig']);
    if (!$obj_handler->add($data,$id)){
        throwException(ADD_STRUCTURE_OBJECT_ERROR);
    }
    $ref_handler = new RelationsHandler($ref_tree_data['twig']);
    if (!$ref_handler->add(1,$id,$ref_tree_data['parent_id'])){
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
    $id = $data['node_id'];
    unset($data['node_id']);
    $handler = new RelationsHandler();
    if (!$handler->update($id,$data)){
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
    $ref_handler = new RelationsHandler();
    if (!$ref_handler->delete($data['node_id'])){
        throwException(DELETE_STRUCTURE_OBJECT_ERROR);
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
    $obj_handler = new RelationsHandler($data['twig']);
    $objects = $obj_handler->getNodesByFilter($data,$start,$limit);
    $result=[];
    foreach ($objects as $object){
        if (isset($object['node_id'])) {
            $entity_handler = new DataModel($object['source_table']);
            switch ($object['source_table']) {
                case 'structure_object':
                    $data = $entity_handler->read($object['ent_id'], ['fkeys' => ['type_id' => 'structure_types']]);
                    break;
                case 'workplaces':
                    $data = $entity_handler->read($object['ent_id'], ['fkeys' => ['position_id' => 'positions']]);
                    break;
            }
            $result[] = array_merge($object, $data);
        }
    }
    $result['obj_count'] = $objects['obj_count'];
    //TODO send enumerative array, not json
    echo json_encode($result);
}

function getTypes(){
    checkAuth();
    $type_handler = new DataModel('structure_types');
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
    $rel_handler = new RelationsHandler();
    $obj_data = $rel_handler->read($data['node_id'],false);
    $obj_handler = new DataModel('structure_object');
    if (!($result = $obj_handler->read($obj_data['ent_id'],['fkeys'=>['type_id'=>'structure_types']]))){
        throwException(GET_STRUCTURE_OBJECT_ERROR);
    }
    echo json_encode(array_merge($result,$obj_data));
}

function getCrossTwigNodes(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'showCrossTwigNodesMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $obj_handler = new RelationsHandler();
    $result = $obj_handler->getConnectedNodes($data['node_id']);
    echo json_encode($result);
}

function addToStructure(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_POST,'addToStructureMask');
    if ($data === false){
        throwException(DATA_FORMAT_ERROR);
    }
    $handler = new RelationsHandler();
    if (!$handler->connect($data['id_up'],$data['id_down'])){
        throwException(UPDATE_STRUCTURE_OBJECT_ERROR);
    }
}
