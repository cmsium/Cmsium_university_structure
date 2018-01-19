create table structure_object
(
  obj_id varchar(5) not null
    primary key,
  obj_name varchar(255) not null,
  type_id int(3) not null,
  parent_id varchar(5) not null
)
;

create table structure_types
(
	type_id int(3) auto_increment
		primary key,
	type_name varchar(255) not null
)
;

