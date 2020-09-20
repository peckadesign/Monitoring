.PHONY: build
.PHONY: cs
.PHONY: phpstan
.PHONY: assets
.PHONY: build-staging
.PHONY: build-staging-front


build:
	composer install --no-interaction


build-staging:
	composer validate
	composer install --no-interaction


build-staging-front: assets


assets:
	cp vendor/ublaboo/datagrid/assets/datagrid.css www/styles/
	cp vendor/ublaboo/datagrid/assets/datagrid-spinners.css www/styles/
	cp assets/js/lastrefresh.js www/js/
	cp assets/js/main.js www/js/
	cp assets/js/nette.ajax.js www/js/
	cp vendor/nette/forms/src/assets/netteForms.min.js www/js/
	cp vendor/ublaboo/datagrid/assets/datagrid.js www/js/
	cp vendor/ublaboo/datagrid/assets/datagrid-instant-url-refresh.js www/js/
	cp vendor/ublaboo/datagrid/assets/datagrid-spinners.js www/js/


cs:
	git clean -xdf output.cs
	composer install --no-interaction
	- vendor/bin/phpcs app/ --standard=vendor/pd/coding-standard/src/PeckaCodingStandard/ruleset.xml --report-file=output.cs
	- vendor/bin/phpcs app/ --standard=vendor/pd/coding-standard/src/PeckaCodingStandardStrict/ruleset.xml --report-file=output-strict.cs
	- test -f output-strict.cs && cat output-strict.cs >> output.cs && rm output-strict.cs


phpstan:
	git clean -xdf output.phpstan
	composer install --no-interaction
	- ./vendor/bin/phpstan analyse app/ --level 1 -c phpstan.neon --no-progress &> output.phpstan
