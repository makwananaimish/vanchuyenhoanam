docker network inspect nginx-network >/dev/null 2>&1 ||
    docker network create nginx-network
docker-compose up -d --build --force-recreate --remove-orphans
docker-compose -f './www/docker-compose.yml' up -d --build --force-recreate --remove-orphans