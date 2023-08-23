psql.exe -d dsts -h localhost -p 5432 -U dsts -W -F p -E < scheme_data.sql
psql.exe -h localhost -p 5432 -U postgres < scheme_grant.sql