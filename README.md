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

#### Load fixtures (if available)
php bin/console doctrine:fixtures:load


## Development server 

Run `symfony server:start` for a dev server. Access the site at `http://127.0.0.1:8000`. To run the server and tests simultaneously : `symfony server:start & php bin/phpunit --testdox`.

## Running Tests

To run tests, run the following command

```bash
  php bin/phpunit --testdox
```

## Backoffice

Access the admin panel at: http://127.0.0.1:8000/admin

Default credentials (if fixtures are loaded):

Email: leakugler2@gmail.com

Password: stubborn


## Stripe Configuration

Configure your Stripe API keys in the .env file :

STRIPE_PUBLIC_KEY=pk_test_51QqbnJRriwcuhjiF5l6Tt2WrxKB5mrOyqSvLhsc6NAaa0QuVyVMOifW9dNTp1hyVW5bH4payACb87mPIiQa6CwWy00tzJ79dCc

STRIPE_SECRET_KEY=sk_test_51QqbnJRriwcuhjiFLJIIoJ4jTwRTgifBueofE9yXsHtqco2wtSx49VQGVAUehcsD8fGxcl7euV76FQKeJE1SCBTw00WgUYd03s


## Documentation

To access the documentation, navigate to : `http://127.0.0.1:8000/docs/namespaces/app.html`

To view the PDF version, run : `start documentation.pdf`.