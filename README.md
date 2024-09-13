ПЕРЕД ЗАПУСКОМ:

1. СКАЧАТЬ:
Xampp 8.2.4 - https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.4/
Git - https://git-scm.com/download/win
Python 3.11.4 - https://www.python.org/downloads/release/python-3114/
PostgreSQL 14.9 - https://www.enterprisedb.com/downloads/postgres-postgresql-downloads

2. git submodule update --init

3. СКАЧАТЬ БИБЛИОТЕКИ ДЛЯ РАБОТЫ PYTHON-БОТА:
pip install -r DSTS-SCRAPPER-MODULE/requirements.txt
playwright install

3. ВЫПОЛНИТЬ BAT-СКРИПТЫ ИЗ ПАПКИ db/


4. наcтроить php.ini:
раскомментировать pgsql

max_execution_time = 500 

[COM_DOT_NET]
extension=php_com_dotnet.dll







