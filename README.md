# Create a small to-do list REST API using Laravel

### Problem Statment

- A to-do item must contain a title, body, due date (optional), media/attachment, reminders (see below), and a complete/incomplete flag
- Must be able to fetch items with a complete/incomplete filter
- Must be able to create to-do items
- Must be able to edit to-do items
- Must be able to delete to-do items
- Must be able to mark a to-do item as done (so it is returned in the done list)
- To-do items should be ordered by the due date (if set)
- Creators can opt-in to reminders (e.g. 1 day before, 2 weeks before, etc), at which point an email is sent to the item owner
- User registration not required, sample user(s) can be seeded


### Demo Video:

### Prerequisites
Here's a basic setup:

* Apache2
* PHP 7 - All the code has been tested against PHP 7.4
* Laravel (8.0) (Laravel Components ^8.0)
* Mysql (5.x), running locally
* Composer 2.x

### Execution

1. Clone the repository:
    ```shell script
    git clone https://github.com/armashfankar/todo-list-larvel-8

    ```

2. Install the requirements for the repository using the `composer`:
   ```shell script
    cd todo-list-larvel-8/
    composer install
    
    ```

3. Copy `.env.example` to create `.env` file:
    ```shell script
    cp .env.example .env
    
    ```

4. Configure Database & Queue Drive in `.env` file:
    
    1. Database
    ```
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=todo
    DB_USERNAME=root
    DB_PASSWORD=
    ```
    
    2. Queue
    ```    
    QUEUE_CONNECTION=database
    ```

    3. SMTP
    ```
    MAIL_MAILER=smtp
    MAIL_HOST=mailhog
    MAIL_PORT=1025
    MAIL_USERNAME=null
    MAIL_PASSWORD=null
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=null
    ```

5. Create MySQL Database:
     ```shell script
    mysql -u root -p
    create database todo;
    
    ```

6. Migrate database:
    ```shell script
    php artisan migrate
    php artisan db:seed
    ```   

7. Run Lumen in terminal:
    ```shell script
    php artisan serve
    ``` 

8. Run Queue in another terminal: 
    ```shell script
    php artisan queue:work --queue=default
    ```


9. Open browser:
    http://localhost:8000