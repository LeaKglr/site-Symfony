.PHONY: start test

start:
	symfony server:start

test:
	php bin/phpunit --testdox

start-with-tests: 
	symfony server:start & php bin/phpunit --testdox

doc:
	phpdoc -d src -t docs

pdf:
	wkhtmltopdf http://127.0.0.1:8000/docs/index.html documentation.pdf

all: doc pdf
