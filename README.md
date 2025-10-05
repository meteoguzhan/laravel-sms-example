# Laravel Sms Example
## Project Details
#### Prerequisites
1. PHP >= 8.4
2. MySQL
#### Design patterns
1. Service
2. Repository
## Project Setup
Firstly, you need to clone git repo. (Run it in your terminal)
```bash
git clone https://github.com/meteoguzhan/laravel-sms-example.git
```
You need to copy env file and rename it as .env
```bash
cd laravel-sms-example && cp .env.example .env
```
After clone project, you need to install packages. (Make sure your system exists composer)
```bash
composer install
```
Open .env file, Give your updated details of MySQL connection string.
<pre>
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_sms_example
DB_USERNAME=root
DB_PASSWORD=</pre>
If you don't have a database, you can create one and run migrations.
```bash
php artisan migrate
```
You can save message seeders.
```bash
php artisan db:seed
```
You can test the project.
```bash
./vendor/bin/phpunit
```
You can start the project. (Make sure your system exists php)
```bash
php artisan serve
```
To send messages waiting to be sent to a queue that will send 2 messages every 5 seconds
```bash
php artisan schedule:work
```
Queue to send messages that are waiting to be sent
```bash
php artisan queue:work --queue=default
```
## API Documentation
You can see the postman collection in the main directory of the project.
