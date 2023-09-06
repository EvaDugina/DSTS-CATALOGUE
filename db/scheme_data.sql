CREATE TABLE articles	(
	id serial,
	article_name text, -- --> название артукула с пробелами
	producer_id integer, -- --> идентификатор производителя
	CONSTRAINT articles_pkey PRIMARY KEY (id)
);

CREATE TABLE producers ( -- таблица наименований производителя
	id serial, -- --> идентификатор производителя
	producer_name text, -- --> навзание производителя c пробелами
	CONSTRAINT producers_pkey PRIMARY KEY (id)
);

CREATE TABLE articles_comparison ( -- таблица кросс-референса артикулов
	id serial,
	group_id integer, -- --> идентификатор группы аналогов
	article_id integer UNIQUE, -- --> идентификатор артукула
	catalogue_name text, -- --> название каталога, установившего аналог
	CONSTRAINT articles_comparison_pkey PRIMARY KEY (id)
);

CREATE TABLE producers_name_variations ( -- таблица альтернативных наименований артикула
	id serial,
	producer_id integer, -- --> идентификатор производителя
	producer_name text, -- --> навзание производителя
	catalogue_name text, -- --> название каталога, для которого актуальна данная вариация
	CONSTRAINT producer_name_variations_pkey PRIMARY KEY (id)
);

CREATE TABLE articles_details ( -- таблица информации о товаре
	id serial,
	article_id integer, -- --> идентификатор артикула
	catalogue_name text, -- --> навзание каталога, из которого взята информация
	json text, -- --> строка информации в формате json
	CONSTRAINT articles_details_pkey PRIMARY KEY (id)
);

CREATE TABLE producers_dsts_names	(
	id serial,
	producer_id integer UNIQUE, -- --> Идентификатор производителя
	producer_name text, -- --> название производителя в системе ДСТС
	CONSTRAINT producers_dsts_name_pkey PRIMARY KEY (id)
);


CREATE TABLE producers_comparison	(
	id serial,
	main_producer_id integer, -- --> идентификатор группы производителей
	secondary_producer_id integer UNIQUE, -- --> идентификатор артукула
	CONSTRAINT producers_comparison_pkey PRIMARY KEY (id)
);


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