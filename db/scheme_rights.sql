-- run under postgres user on database accelerator
create database dsts;
create user dsts with encrypted password '123456';

GRANT ALL PRIVILEGES ON DATABASE dsts TO dsts;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO dsts;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO dsts;
