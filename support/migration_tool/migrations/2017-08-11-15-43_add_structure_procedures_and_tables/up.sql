create table structure_object
(
  obj_id varchar(5) not null primary key,
  obj_name varchar(255) not null,
  type_id VARCHAR(3) not null
);

create table structure_types
(
	type_id VARCHAR(3)
		primary key,
	type_name varchar(255) not null
);

create table workplaces
(
  workplace_id varchar(6) not null
    primary key,
  position_id  varchar(3) null
);

create table twigs
(
  twig_id   int(4)       not null
    primary key,
  twig_name varchar(255) null
);

create table positions
(
  position_id   varchar(3)   not null
    primary key,
  position_name varchar(255) null
);

create table relations_tree
(
  node_id varchar(32)   not null
    primary key,
  twig    int(4)        null,
  kind    int(3)        null,
  ent_id  varbinary(32) null
);

create table cross_twig_relations
(
  rel_id  varchar(32) not null
    primary key,
  id_up   varchar(32) null,
  id_down varchar(32) null,
  constraint cross_twig_relations_id_up_uindex
  unique (id_up, id_down)
);

create table entity_kinds
(
  kind_id      int(3)       not null
    primary key,
  kind_name    varchar(255) null,
  source_table varchar(255) null
);

create procedure readFromRelations(IN idNode varchar(32))
  BEGIN
    select * from relations_tree as tree
    JOIN twigs ON tree.twig = twigs.twig_id
    JOIN entity_kinds as kinds ON tree.kind = kinds.kind_id
    WHERE  tree.node_id = idNode;
  END;


create procedure getTwigs()
  BEGIN
    SELECT * FROM twigs;
  END;

create procedure getConnectedNodes(IN idNode varchar(32))
  BEGIN
    SELECT * FROM cross_twig_relations WHERE id_up = idNode OR id_down = idNode;
  END;

create procedure detachNode(IN idNode varchar(32))
  BEGIN
    DELETE FROM cross_twig_relations WHERE id_up = idNode OR id_down = idNode;
  END;

create procedure connectNodes(IN idRel varchar(32), IN idUp varchar(32), IN idDown varchar(32))
  BEGIN
    INSERT INTO cross_twig_relations (rel_id, id_up, id_down) VALUES (idRel, idUp, idDown);
  END;

create procedure addRelEntity(IN idNode varchar(32), IN twigId int(4), IN kindId int(3), IN entId varbinary(32))
  BEGIN
    INSERT INTO relations_tree (node_id, twig, kind, ent_id) values (idNode, twigId, kindId, entId);
  END;


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

create procedure addDataToEntity(IN tableName       varchar(255), IN insertNamesStr varchar(255),
                                 IN insertValuesStr varchar(255))
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

  create procedure getAllWorkplaces(IN tableName varchar(255), IN queryWhere varchar(255), IN inStart int(6),
                                  IN inOffset  int(6))
  BEGIN
    START TRANSACTION;
    SET @sql = CONCAT('SELECT SQL_CALC_FOUND_ROWS * FROM workplaces
    JOIN positions AS pos ON workplaces.position_id = pos.position_id
    JOIN structure_object AS struc ON workplaces.structure_id = struc.obj_id ',queryWhere,' LIMIT ',inStart,',',inOffset,';');
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
    COMMIT;
  END;


