.PHONY: phpcs-fix
phpcs-fix:
	vendor/bin/php-cs-fixer fix .

.PHONY: phpcbf
phpcbf:
	vendor/bin/phpcbf

.PHONY: qa
qa: phpcs psalm test-coverage

.PHONY: phpcs
phpcs:
	vendor/bin/phpcs

.PHONY: psalm
psalm:
	vendor/bin/psalm

.PHONY: test
test:
	vendor/bin/phpunit

.PHONY: test-coverage
test-coverage:
	XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-text --coverage-filter src
