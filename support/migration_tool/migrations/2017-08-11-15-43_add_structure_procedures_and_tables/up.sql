create table structure_object
(
  obj_id varchar(5) not null
    primary key,
  obj_name varchar(255) not null,
  type_id int(3) not null,
  parent_id varchar(5) not null
);

create table structure_types
(
	type_id int(3) auto_increment
		primary key,
	type_name varchar(255) not null
);

CREATE PROCEDURE addStructureType(IN typeName VARCHAR(255))
  BEGIN
    INSERT INTO structure_types (type_name) VALUES (typeName);
  END;

CREATE PROCEDURE deleteStructureType(IN idType INT(3))
  BEGIN
    DELETE FROM structure_types WHERE type_id=idType;
  END;

CREATE PROCEDURE getTableStructureData(IN tableName VARCHAR(255))
  BEGIN
    SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = tableName and COLUMN_KEY = 'PRI';
  END;

CREATE PROCEDURE addStructureObject(IN idObj VARCHAR(5),IN nameObj VARCHAR(255),IN idType INT(3),IN idParent VARCHAR(5))
  BEGIN
    INSERT INTO structure_object (obj_id,obj_name,type_id,parent_id) VALUES (idObj, nameObj, idType, idParent);
  END;

CREATE PROCEDURE deleteStructureObject(IN idObj VARCHAR(5))
  BEGIN
    DELETE FROM structure_object WHERE obj_id=idObj;
  END;

CREATE PROCEDURE getStructureObject(IN idObj VARCHAR(5))
  BEGIN
    SELECT obj.obj_id, obj.obj_name, obj.type_id, types.type_name, obj.parent_id, p_obj.obj_name as parent_name FROM structure_object AS obj
      JOIN structure_types AS types ON obj.type_id = types.type_id
      JOIN structure_object AS p_obj ON obj.parent_id = p_obj.obj_id
    WHERE obj.obj_id = idObj;
  END;

CREATE PROCEDURE getStructureObjectsByFilter(IN queryWhere VARCHAR(255), IN inStart INT(6), IN inOffset INT(6))
  BEGIN
    START TRANSACTION;
    SET @sql = CONCAT('SELECT SQL_CALC_FOUND_ROWS obj.obj_id, obj.obj_name, obj.type_id, types.type_name,p_obj.obj_name as parent_name FROM structure_object AS obj
              JOIN structure_types AS types ON obj.type_id = types.type_id
              JOIN structure_object AS p_obj ON obj.parent_id = p_obj.obj_id ',queryWhere,' LIMIT ',inStart,',',inOffset,';');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    COMMIT;
  END;

CREATE PROCEDURE updateStructureObject(IN updateStr VARCHAR(255), IN idObj VARCHAR(5))
  BEGIN
    START TRANSACTION;
    SET @sql = CONCAT('UPDATE structure_object SET ',updateStr,' WHERE obj_id = "',idObj,'";');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    COMMIT;
  END;

CREATE PROCEDURE getStructureTypes()
  BEGIN
    SELECT * FROM structure_types;
  END;
