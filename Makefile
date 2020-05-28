SHELL:=/bin/bash
.ONESHELL:

.PHONY: bash
bash:
	@if [[ ! -f /.dockerenv ]]; then
		docker exec -ti -u dev edifact_parser_php bash
	else
		echo "You are already into the docker bash.";
	fi

.PHONY: csfix
csfix:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/edifact-parser && vendor/bin/php-cs-fixer fix
	else
		docker exec -ti -u dev edifact_parser_php sh \
			-c "cd /srv/edifact-parser && vendor/bin/php-cs-fixer fix"
	fi

# make tests ARGS="--filter AppTest"
.PHONY: tests
tests:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/edifact-parser && vendor/bin/phpunit ${ARGS} --coverage-html coverage;
	else
		docker exec -ti -u dev edifact_parser_php sh \
			-c "cd /srv/edifact-parser && vendor/bin/phpunit $(ARGS) --coverage-html coverage"
	fi

# make composer ARGS="require phpunit/phpunit"
.PHONY: composer
composer:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/edifact-parser && composer ${ARGS}
	else
		docker exec -ti -u dev edifact_parser_php sh \
			-c "cd /srv/edifact-parser && composer $(ARGS)"
	fi

.PHONY: psalm
psalm:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/edifact-parser && vendor/bin/psalm ${ARGS}
	else
		docker exec -ti -u dev edifact_parser_php sh \
			-c "cd /srv/edifact-parser && vendor/bin/psalm ${ARGS}"
	fi

.PHONY: psalm-log
psalm-log:
	@if [[ -f /.dockerenv ]]; then
		cd /srv/edifact-parser && vendor/bin/psalm --output-format=text --show-info=true > psalm.log
	else
		docker exec -ti -u dev edifact_parser_php sh \
			-c "cd /srv/edifact-parser && vendor/bin/psalm --output-format=text --show-info=true > psalm.log"
	fi
