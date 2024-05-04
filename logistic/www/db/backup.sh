CURRENT_DATETIME=$(date +"%Y%m%d%H%M%S")
FILENAME="backup_${CURRENT_DATETIME}.sql"

docker exec -i 40d5e03c949a sh -c 'exec mysqldump -u root -p"$MYSQL_ROOT_PASSWORD" db' >$FILENAME
