CREATE TABLE users (
	id serial,
	first_name text,	-- имя
	middle_name text,	-- фамилия
	last_name text,		-- отчество (не верь глазам своим блин)
	login text,		
	role integer,		-- 1 - администратор, 2 - обычный пользователь
	password text,	
	CONSTRAINT users_pkey PRIMARY KEY (id)
);
--ALTER TABLE users OWNER TO postgres;

INSERT INTO users(id, first_name, middle_name, last_name, login, role, password) VALUES 
(1, 'Иван', 'Дугин', 'Андреевич', 'ivan', 1, 123);