.PHONY: cs

cs:
	git clean -xdf output.cs
	composer install --no-interaction
	- vendor/bin/phpcs app/ --standard=vendor/pd/coding-standard/src/PeckaCodingStandard/ruleset.xml --report-file=output.cs
	- vendor/bin/phpcs app/ --standard=vendor/pd/coding-standard/src/PeckaCodingStandard/ruleset-strict.xml --report-file=output-strict.cs
	- test -f output-strict.cs && cat output-strict.cs >> output.cs && rm output-strict.cs
