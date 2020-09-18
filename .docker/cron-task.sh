/usr/local/bin/php /var/www/html/www/index.php pd:monitoring:check:publish:alive-checks
/usr/local/bin/php /var/www/html/www/index.php pd:monitoring:check:publish:certificate-checks
/usr/local/bin/php /var/www/html/www/index.php pd:monitoring:check:publish:dns-checks
/usr/local/bin/php /var/www/html/www/index.php pd:monitoring:check:publish:http-status-code-checks
/usr/local/bin/php /var/www/html/www/index.php pd:monitoring:check:publish:feed-checks
/usr/local/bin/php /var/www/html/www/index.php pd:monitoring:check:publish:number-value-checks
/usr/local/bin/php /var/www/html/www/index.php pd:monitoring:check:publish:rabbit-consumer-checks
/usr/local/bin/php /var/www/html/www/index.php pd:monitoring:check:publish:rabbit-queue-checks
/usr/local/bin/php /var/www/html/www/index.php pd:monitoring:check:slack-check-statuses

sleep 45
