# Mobile Application Subscription Managment
This solution developed using PHP / Lumen (Laravel) / MySQL. So it is splitted in three projects implementing the separation of concerns.
 
## Solution Structure
* **API Project:** A RESTFul API Services supplied by authentication with Bearer Token. It provide the functionality of the mobile application subscriptions.
* **Mocking Project:** A RESTFul API Services provide some endpoints that mocking the IOS/Google verification of purchasing.
* **Worker Project** It provide two laravel commands and two background Queue jobs, that take care of expiration of subscriptions and the failed purchases varifications.

## Setup and Run:
> **Note:** Make sure your server meets the following requirements:
>-   PHP >= 7.3
>-   OpenSSL PHP Extension
>-   PDO PHP Extension
>-   Mbstring PHP Extension
>-   MariaDB 10.4.17
> _If you face any problem with the setup or the run processes, do not hesitate to contact me._

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


## The usage:
### API Project:
1. Register new device endpoint:
`POST {{ _.base_url }}/api/devices`
Request body
```json
{
	"device_uid": "254076de-2020-3374-1233-7be7f9e17e9f",
	"app_id": "2d4b1da1-2020-3544-1233-428b56185593",
	"language": "tr",
	"os": 1,
	"username": "test.user@example.com",
	"password": "123456789",
	"name": "Test User"
}
```
Response body
```json
{
  "status": true,
  "data": {
    "client_id": 2,
    "client_secret": "4pDp6sdLyHXnezitXaYKEfeYJ3JBrJQESpffyThe",
    "grant_type": "password"
  },
  "message": "Device registered successfully.",
  "errors": null
}
```
2. Login endpoint:
`POST {{ _.base_url }}/api/oauth/token`
Request body
```json
{
	"client_id": "2",
	"client_secret": "4pDp6sdLyHXnezitXaYKEfeYJ3JBrJQESpffyThe",
	"grant_type": "password",
	"username": "thea73@example.org",
	"password": "123456789"
}
```
Response body
```json
{
  "token_type": "Bearer",
  "expires_in": 31535999,
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIyIiwianRpIjoiZTYzMTFiYjkyYzdkYWM3NTUzODc0MmUzZDdlZTBiN2E2NDc2MDg1YTI0MTRjYjYzYzc1NGI5YWFhY2I3ZTI2ZjE2MmUxYzE3YmFmOGMxODYiLCJpYXQiOjE2MzQ5MjQwNTQuODQ1NzA4LCJuYmYiOjE2MzQ5MjQwNTQuODQ1NzUyLCJleHAiOjE2NjY0NjAwNTMuOTUyNTA4LCJzdWIiOiIxIiwic2NvcGVzIjpbXX0.L5FuDsofZTAeBhepHAgsoRKSfVNg_Lyyc0TFJqMdxkEZM7MsLolFjk_HnUC9wwpOWaI2hMGqwzEgROD5kdus_d_vurwOiOrTiJCkx1VhjkShgoxDuvRDtcZTuGoLX6au0cVqPRYJlHcYOnrBeNsyxek63QgKPyVWN6nF8rlXvXqfXdLy3d7Ekny34lvIfUlyfSRJcQlFfFUo68QDJlZbv26zuB_TfJqtFvb2KMLTaaNgqbRfEmHw6S5aPqfCwDWZlwa6DmO2OYyDiv0WfGSatHE76Nbfm3CQoaC8SW072nrASmEgBXk-8k-U4wnDxu1be8xPowBJbH6pqUog8ZuizhFK9VSm49d99QS8Nk693prGwASxqma4SuY85qJn-xHhuhYL4MnCjWvtgja3btmfxZ4SAlcB-hnLgJVmZfJj2XdPTimjRpPY5Ry1PPtHNkRj2QdVATZgCndyrwuQZkSXmXodfetf-huR9GqApQQhHv1UcwlW8Xj72aPJIs5Y8wX9RSgkchajfOP5jqSwF7A_JKY1A-JOw71tZzBtL0lfi_9l0c0vaVCqQt0jy0pWHK5MODtSZosOaP-LnHsgP9LGKSGxpKEdWUK-rtEk2qsvQ4jyciX020kR7bfgviBHUtFdjvJ4ITw0rStgsLDfYp285gtON1qGBWT4swl7-_awMQk",
  "refresh_token": "def50200793d3e487b780c847996b768dda419a416995878afd598556c8185db0c6109f0d209f10ea4302992bc00e2df880e95c351b7933608046df91b47ff78be18ebf96bd77905e6f2f8ecdd8ff0bb8f38671a03b722e88dcc93d95ae471ad06104d3bd0c2c6d319f86929cc5f0293ad92b63dd38003a4750c46f153870edab86bcff33b43127f23f96b85f46127cbd4a1c220e7887cbd6858c44a48ef53671ae00d2a6125d8c950e1382cc7c14fb1ccd010ba32addc3a446def914cc41f7dda21591869dbb8c4f9ee139cb08fb6118b9339861d40d2a1786005ef029d1faa0dec51f5ab6efe21b557aee1d2af650405f139b3c51aeebd3dd9c373922c7368a58cbd3fe7378dd94296ae825766a1e0328fa2b8a350e4b474b76dd36c83801225c922817525bfe27d5c020f4a465fb7ae5e89e98e71855b1bf042b378df7f81639418b37d111550f7f20c791206d8f98675148cde11c0da766cc3d969d7606ed4"
}
```
3. Logout endpoint:
`POST {{ _.base_url }}/api/oauth/logout`
Header: Authorization: Bearer {{access_token}}

Response body
```json
{
  "status": false,
  "data": null,
  "message": "The user logged out successfully.",
  "errors": null
}
```
4. Making a purchase endpoint:
`POST {{ _.base_url }}/api/purchases`
Header: Authorization: Bearer {{access_token}}
Request body
```json
{
	"receipt": "254076de-7686-3374-8098-7be7f9e17e9f"
}
```
Response body
```json
{
  "status": true,
  "data": null,
  "message": "Purchase saved successfully.",
  "errors": null
}
```
5. Checking the purchase status endpoint:
You have two choices:
`GET {{ _.base_url }}/api/purchases/{id:[0-9]+}`
Header: Authorization: Bearer {{access_token}}

Response body
```json
{
  "status": true,
  "data": {
    "id": 1000,
    "receipt": "4474db24-d051-364c-bdea-a79b66f88bcd",
    "state": 2,
    "last_check_at": "2021-10-18 17:27:34",
    "status": 1,
    "expire_date": "2021-12-22 15:18:44",
    "created_at": "2021-10-22T09:03:50.000000Z",
    "updated_at": "2021-10-22T09:03:50.000000Z",
    "deleted_at": null,
    "user_id": 997,
    "is_rate_limit": 0
  },
  "message": "",
  "errors": null
}
```
Or 
`GET {{ _.base_url }}/api/purchases/check`
Header: Authorization: Bearer {{access_token}}
with the same response body.

**Note:** I supplied the insomniaREST.json collection.
* If you want more details about available api endpoints run the command: `php artisan route:list`

## License

Licensed under the [MIT license](LICENSE.md).
