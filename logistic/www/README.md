## Install docker
```
curl -s https://get.docker.com | bash
```

## Install docker-compose
```
sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

## Run
```
docker-compose up -d --build --force-recreate
```

## Note 
- Change ssl keys in: web/keys
- Change domain in: web/conf.d/app.conf
- Rerun