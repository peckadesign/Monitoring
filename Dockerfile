FROM peckadesign/php:7.1

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

RUN mkdir temp/ log/
RUN chmod -R 0777 temp/ log/
