# site-Symfony

This project was generated with Symfony version 5.4 LTS.

## Installation

### Prerequisites

- PHP 8.1+

- Composer ([installation guide](https://getcomposer.org/download/))

- Symfony CLI ([installation guide](https://symfony.com/download))

### Installation Steps 

Clone the project and install dependencies :

#### Clone the repository
git clone https://github.com/ton-repo/site-symfony.git
cd site-symfony

#### Install PHP dependencies
composer install

#### Create and configure the environment file
cp .env

#### Create the database
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

#### Database test
To run database test, run the following command

```bash
  mysql -u root -p stubborn < database/stubborn_bdd.sql
```

## Development server 

Run `symfony server:start` for a dev server. Access the site at `http://127.0.0.1:8000`. To run the server and tests simultaneously : `symfony server:start & php bin/phpunit --testdox`.

## Running Tests

To run tests, run the following command

```bash
  php bin/phpunit --testdox
```

## Backoffice

Access the admin panel at: http://127.0.0.1:8000/admin

## Documentation

To access the documentation, navigate to : `http://127.0.0.1:8000/docs/namespaces/app.html`

To view the PDF version, run : `start documentation.pdf`.