
# Mini Loan Application API

It is a simple REST API's for the private loan application. Authenticated users can able to request loan from lenders through loan application. Based on the admin approval, loan will get sanction and repayment will start as per the term which customer mentioned in the loan request

## Tech Stack

**PHP:** Laravel ,Mysql , Laravel Passport 
**Routes:** Admin route has been protected using passport policy


## Prerequisites

- Basic knowlegde of PHP
- Basic knowlegde of running test cases
- Postman tool to run API.
- Laravel passport API Auth.
- Default Admin user after database seeding :
   Email: admin@admin.com
   password: admin@123
- Default passport for users: demo@123 
- [Postman API JSON Link](https://www.getpostman.com/collections/08a0e5e3a94c9924bc00)


## Installation

- Clone or download this repository
```bash
  git clone https://github.com/sanojsharma/mini-loan-app.git
```
- Run in project folder    
```
composer install
```
- Rename the .env.example to .env (Configure your desired environment variables)



## Email Service Provider

[Mailtrap](https://mailtrap.io/)

-	Create a free account. 
-	Go to inbox and click on SMTP details
-	Select Laravel as option.
-	Copy the details to .env file of the project.

## Migration and Seed

-	Run in project folder
   ```bash  php artisan migrate:fresh --seed```
-	In this demo, have used laravel-passport package, which should install before start.
```bash composer require laravel/passport```
-	Run in project folder
```bash php artisan passport:install```



## API Reference

#### User registration 

```http
  POST api/v1/register
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. |
| `email` | `string` | **Required**.|

#### User Login

```http
  POST api/v1/login
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `email`      | `string` | **Required** |
| `password`      | `string` | **Required** (demo@123)|

#### Create Loan request

```http
  POST api/v1/loans/create
  (header: Authorization: Bearer Token)
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `loan_amount`      | `integer` | **Required** |
| `loan_term`      | `integer` | **Required** |

#### Approve Loan By Admin

```http
  POST api/v1/admin/approve
  (header: Authorization: Bearer Token)
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `user_id`      | `integer` | **Required** |
| `loan_id`      | `integer` | **Required** |


#### Get pending transaction details by Loan ID

```http
  GET api/v1/transaction
  (header: Authorization: Bearer Token)
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `loan_id`      | `integer` | **Required** |

#### Pay Installment

```http
  POST api/v1/transaction/create
  (header: Authorization: Bearer Token)
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `repayment_id`      | `integer` | **Required** |
| `amount`      | `integer` | **Required** (This is for representation purpose)|




## Running Tests

To run tests, run the following command

```bash
  	UserTest :  php artisan test --filter UserTest
   LoanTest: php artisan test --filter LoanTest
   TransactionTest: php artisan test --filter TransactionTest
```

