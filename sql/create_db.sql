CREATE TABLE records (
	id INTEGER NOT NULL AUTO_INCREMENT,
	time INTEGER,
	type INTEGER,
	title TEXT,
	category INTEGER,
	price INTEGER,
	account_id INTEGER,
	currency TEXT,
	user_id INTEGER,
	PRIMARY KEY (id)
);
	
CREATE TABLE users (
	id INTEGER NOT NULL AUTO_INCREMENT,
	created_at INTEGER,
	full_name TEXT,
	email TEXT,
	password TEXT,
	PRIMARY KEY (id)
);
