# Install
- Clone project to local folder
- Install dependencies by executing ```composer install``` from project folder
- Create .env file: ```cp .env.example .env```
- Generate application key: ```php artisan key:generate```
- Open .env file and configure database connection.
If you want to use sqlite than replace all records starting with DB_ with following: ```DB_CONNECTION=sqlite``` and create database file: ```touch ./database/database.sqlite```
- Migrate database: ```php artisan migrate --seed```
- Run app: ```php artisan serve```

# Log in
Now you must be able to open http://localhost:8000/ in browser and login using one of the following predefined accounts:
```
email: user@example.com
password: secret
```
```
email: manager@example.com
password: secret
```
```
email: admin@example.com
password: secret
```
