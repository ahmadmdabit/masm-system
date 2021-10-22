# Mobile Application Subscription Managment
This solution developed using PHP / Lumen (Laravel) / MySQL. So it is splitted in three projects implementing the separation of concerns.
 
## Solution Structure
* **API Project:** A RESTFul API Services supplied by authentication with Bearer Token. It provide the functionality of the mobile application subscriptions.
* **Mocking Project:** A RESTFul API Services provide some endpoints that mocking the IOS/Google verification of purchasing.
* **Worker Project** It provide two laravel commands and two background Queue jobs, that take care of expiration of subscriptions and the failed purchases varifications.

## How to use:
### API Project:
1. Open the terminal in the `api` directory.
2. Run the command `composer install`, to install the required packages. 
3. Set up the database info in the file `.env`
4. Import the `masm_system.sql` file to the database server.
Or just run the command `php artisan migrate`, it will create the schema of the database.
5. If you want to seed the database with dummy data, you can run the command `php artisan db:seed` 
6. After that, run the command `php artisan serve`, and the API will be served on the address: http://127.0.0.1:8000
7. But to make this API work right you have to complete setup the **Mocking Project** first,


### Mocking Project:
1. Open the terminal in the `mocking` directory.
2. Run the command `composer install`, to install the required packages. 
3. After that, run the command `php -S localhost:8001 -t public`, and the Mocking API will be served on the address: http://localhost:8001


### Worker Project:
1. Open the terminal in the `worker` directory.
2. Run the command `composer install`, to install the required packages. 
3. Set up the database info in the file `.env` to be the same like the api project.
4. After that, if you want to run the worker hourly, run the command `php artisan schedule:run` [refer to](https://laravel.com/docs/8.x/scheduling#running-the-scheduler) , or you can run the worker manually, run the command `php artisan ExpirationWorker:run` for the first worker, and this `php artisan RateLimitationWorker:run` for the other one.
5. Open another terminal in the `worker` directory.
6. Run the command `php artisan queue:work`, so the queued jobs will be executed sine their queue has been dispatched.

