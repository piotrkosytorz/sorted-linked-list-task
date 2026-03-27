.PHONY: test analyse cs-fix cs-check check

test:
	vendor/bin/phpunit

analyse:
	vendor/bin/phpstan analyse

cs-fix:
	vendor/bin/php-cs-fixer fix

cs-check:
	vendor/bin/php-cs-fixer fix --dry-run --diff

check: cs-check analyse test
