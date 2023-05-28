#!make
include .env

local:
	cp .env ./app/.env && \
	docker-compose up -d --build --force-recreate && \
    ./wait-until.sh "docker-compose exec -T -e MYSQL_PWD=${MYSQL_ROOT_PASSWORD} mysql mysql -D ${MYSQL_DATABASE} -e 'select 1'" && \
    ./wait-until.sh "docker-compose exec -T -e MYSQL_PWD=${MYSQL_ROOT_PASSWORD} mysql mysql -D ${MYSQL_TEST_DATABASE} -e 'select 1'" && \
	docker exec -it app_fpm composer i && \
    docker exec -it app_fpm php ./yii migrate --interactive=0 && \
    docker exec -it app_fpm php ./tests/bin/yii migrate --interactive=0 && \
	docker exec -it app_fpm ./vendor/bin/codecept run api