.PHONY: build
.PHONY: cs
.PHONY: phpstan


build:
	composer install --no-interaction
	yarn
	./node_modules/.bin/gulp


cs:
	git clean -xdf output.cs
	composer install --no-interaction
	- vendor/bin/phpcs app/ --standard=vendor/pd/coding-standard/src/PeckaCodingStandard/ruleset.xml --report-file=output.cs
	- vendor/bin/phpcs app/ --standard=vendor/pd/coding-standard/src/PeckaCodingStandardStrict/ruleset.xml --report-file=output-strict.cs
	- test -f output-strict.cs && cat output-strict.cs >> output.cs && rm output-strict.cs


phpstan:
	git clean -xdf output.phpstan
	composer install --no-interaction
	- ./vendor/bin/phpstan analyse app/ --level 1 --no-progress &> output.phpstan
