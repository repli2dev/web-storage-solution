# INSTALLATION

1. Upload all files to the webserver root excluding this folders `resources`.

2. Chmod these dirs to `777`

* `temp/` (and remove its content)
* `log/`
* `stored-files/` (create if this folder do not exists)
* `incoming/` (create if this folder do not exists)

3. Ensure that file `/app/config/config.neon` and `/app/config/config.local.neon` are not accessible from web!

4. Connect to your database and create database with structure you find in `resources/dump.sql` file.

5. Initialize composer dependencies for downloading libraries via `php composer.phar update`

6. Edit `app/config.local.neon`
   Especially edit `variable.key` - put random string there (only a-z and A-Z and numbers)
   Set credentials for DB connection in `dibi` section (see `config.neon` for options).

7. Put entry into crontab - call address http://<your-web-address>/files/maintenance?key=<variable-key-in-config-neon> This ensure removing of expired item!

8. Now, your web is ready.