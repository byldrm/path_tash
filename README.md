# task


Docker Desktop uygulaması açın.

Aşıdaki satırları sırayla komut satırında çalıştırın.

    git clone https://github.com/byldrm/path_task.git
    
    cd path_task
    
    docker compose up -d --build
    
    docker exec -it app-shared-php8 bash
    
    cd abc_company
    
    composer update

    php bin/console doctrine:migrations:migrate
    
    php bin/console doctrine:fixtures:load


    
