cd docker
docker-compose up -d dusk.restart-project.local
docker-compose run --rm php_dusk php artisan dusk $@
docker-compose stop dusk.restart-project.local database_dusk selenium
cd ..
