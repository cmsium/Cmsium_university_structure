create table structure_object
(
  obj_id varchar(5) not null
    primary key,
  obj_name varchar(255) not null,
  type_id VARCHAR(3) not null,
  parent_id varchar(5) not null
);

create table structure_types
(
	type_id VARCHAR(3)
		primary key,
	type_name varchar(255) not null
);

CREATE TABLE logic_object
(
  obj_id    VARCHAR(5)   NOT NULL
    PRIMARY KEY,
  obj_name  VARCHAR(255) NOT NULL,
  type_id   VARCHAR(3)       NOT NULL,
  parent_id VARCHAR(5)   NOT NULL
);

CREATE TABLE logic_types
(
  type_id   VARCHAR(3)
    PRIMARY KEY,
  type_name VARCHAR(255) NOT NULL
);

create table structure_in_logic
(
  logic_obj_id varchar(5) not null,
  structure_id varchar(5) not null,
  constraint structure_in_logic_logic_obj_id_uindex
  unique (logic_obj_id, structure_id)
);

create table role_in_logic
(
  logic_obj_id varchar(5) not null,
  role_id      int        not null,
  constraint role_in_logic_logic_obj_id_uindex
  unique (logic_obj_id, role_id)
);

create table user_in_logic
(
  logic_obj_id varchar(5)  not null,
  user_id      varchar(32) not null,
  constraint user_in_logic_logic_obj_id_uindex
  unique (logic_obj_id, user_id)
);

CREATE PROCEDURE getTableStructureData(IN tableName VARCHAR(255))
  BEGIN
    SELECT COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH,COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = tableName;
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

  CREATE PROCEDURE addDataToEntity(IN tableName       VARCHAR(255), IN insertNamesStr VARCHAR(255),
                                 IN insertValuesStr VARCHAR(255))
  BEGIN
    START TRANSACTION;
    SET @sql = CONCAT('INSERT INTO ',tableName,' (',insertNamesStr,') VALUES (',insertValuesStr,');');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    COMMIT;
  END;

  CREATE PROCEDURE getAllEntities(IN tableName VARCHAR(255), IN queryWhere VARCHAR(255), IN inStart INT(6),
                                IN inOffset  INT(6))
  BEGIN
    START TRANSACTION;
    SET @sql = CONCAT('SELECT SQL_CALC_FOUND_ROWS * FROM ',tableName,' ',queryWhere,' LIMIT ',inStart,',',inOffset,';');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    COMMIT;
  END;
