<?php


function addType(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_POST,'addStructureTypeMask');
    if ($data === false){
        echo json_encode(['status'=>'error', 'message'=>'Validation error','fields'=>implode(',',$validator->getErrors())]);
        exit;
    }
    var_dump($_POST);
    if (!addStructureType($data['type_name']))
        echo json_encode(['status'=>'error', 'message'=>'Create type error']);
    else
        echo json_encode(['status'=>'ok', 'message'=>'Create type success']);
}

function deleteType(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'deleteStructureTypeMask');
    if ($data === false){
        echo json_encode(['status'=>'error', 'message'=>'Validation error','fields'=>implode(',',$validator->getErrors())]);
        exit;
    }
    deleteStructureType($data['id']);
    echo json_encode(['status'=>'ok', 'message'=>'Delete type success']);
}

function addObject(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'addStructureObjectMask');
    if ($data === false){
        echo json_encode(['status'=>'error', 'message'=>'Validation error','fields'=>implode(',',$validator->getErrors())]);
        exit;
    }
    if (!addStructureObject($data)){
        echo json_encode(['status'=>'error', 'message'=>'Database error']);
        exit;
    }
    echo json_encode(['status'=>'ok', 'message'=>'Create structure node success']);
}

function updateObject(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_POST,'updateStructureObjectMask');
    if ($data === false){
        echo json_encode(['status'=>'error', 'message'=>'Validation error','fields'=>implode(',',$validator->getErrors())]);
        exit;
    }
    if (updateStructureObject($data) === false){
        echo json_encode(['status'=>'error', 'message'=>'Update error']);
        exit;
    } else {
        echo json_encode(['status'=>'ok', 'message'=>'update Success']);
        exit;
    }
}

function deleteObject(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'deleteStructureObjectMask');
    if ($data === false){
        echo json_encode(['status'=>'error', 'message'=>'Validation error','fields'=>implode(',',$validator->getErrors())]);
        exit;
    }
    if (!deleteStructureObject($data['obj_id'])){
        echo json_encode(['status'=>'error', 'message'=>'Database error']);
        exit;
    }
    echo json_encode(['status'=>'ok', 'message'=>'Delete structure node success']);
}


function getObject(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'getStructureObjectMask');
    if ($data === false){
        echo json_encode(['status'=>'error', 'message'=>'Validation error','fields'=>implode(',',$validator->getErrors())]);
        exit;
    }
    $obj = getStructureObject($data['obj_id']);
    if ($obj){
        echo json_encode(array_merge(['status'=>'ok'],$obj));
    } else {
        echo json_encode(['status'=>'error']);
    }
}

function getObjects(){
    checkAuth();
    $validator = Validator::getInstance();
    $data = $validator->validateAllByMask($_GET,'getStructureObjectsMask');
    if ($data === false){
        echo json_encode(['status'=>'error', 'message'=>'Validation error','fields'=>implode(',',$validator->getErrors())]);
        exit;
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
    echo json_encode(array_merge(['status'=>'ok'],$objects));
}

function getTypes(){
    checkAuth();
    $types = getStructureTypes();
    if ($types){
        echo json_encode(array_merge(['status'=>'ok'],$types));
    } else {
        echo json_encode(['status'=>'error']);
    }
}
