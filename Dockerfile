FROM peckadesign/php:7.4

COPY ./.docker/web/default.conf /etc/apache2/sites-enabled/000-default.conf

COPY app /var/www/html/app
COPY bin /var/www/html/bin
COPY config/docker.neon /var/www/html/app/config/config.local.neon
COPY vendor /var/www/html/vendor
COPY migrations /var/www/html/migrations
COPY www /var/www/html/www

RUN mkdir /var/www/html/www/images
RUN mkdir /var/www/html/temp
RUN mkdir /var/www/html/log
RUN chmod -R 0777 /var/www/html/temp /var/www/html/log

RUN a2enmod rewrite
