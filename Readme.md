# Project Setup Instructions

**Follow these steps to set up and run the project:**

1. **Go to the Parent Directory**:
   Navigate to the root directory where your `docker-compose.yml` file is located.

2. **Start the Docker Containers**:
   Run the following command if not already executed to start the services in detached mode:

   ```bash
   docker-compose up -d
   
3. **Create the database**:
Run following command to create the database
    
    ```bash
   docker exec -it ecom-db mysql -u root -prootpasswd -e "CREATE DATABASE ecom_media_backend;CREATE DATABASE media_ecom"
   
4. **Access the API Container**:
Execute the following command to enter the ecom-media-player-api container:
    ```bash
   docker exec -it ecomapi bash
   composer install
   composer update
   
5. **Migrate the Database**:
Run this command inside the container to migrate the database files:

    ```bash
    php artisan migrate --database=ecomBackend

6. **Seed the Database**:
To seed the database entries, run the following command:

    ```bash
    php artisan db:seed
