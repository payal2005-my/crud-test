# crud-test

A PHP and MySQL user-management application for Cantech PaaS.

## Features

- View users
- Add users
- Edit user details
- Delete users
- PDO prepared statements
- CSRF protection
- Environment-based database configuration

## Requirements

- PHP with PDO MySQL support
- Apache web server
- MySQL
- PHP sessions enabled

## Database setup

Create the database using your MySQL administration tool:

    CREATE DATABASE cantech_crud;

Select the database and execute database.sql to create the users table.

Create a dedicated application user and grant it only the
permissions required by this application.

## Environment variables

Configure these in the Cantech PHP application's environment:

- DB_HOST: MySQL internal hostname
- DB_PORT: MySQL port (usually 3306)
- DB_NAME: cantech_crud
- DB_USER: dedicated application username
- DB_PASSWORD: application user's password

Do not commit real passwords or connection strings to GitHub.

## Deployment

Deploy the repository to the PHP web root using Cantech's GitHub
deployment option. Configure the database service and environment
variables before testing the application.

The application does not configure MySQL TLS itself. Follow the
database service's connection requirements.
