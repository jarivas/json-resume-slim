FROM php:8.4-cli

RUN apt-get -y update
RUN apt-get -y upgrade
RUN apt-get -y install git zip curl

COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions zip pdo_mysql
RUN git config --global --add safe.directory /app

RUN adduser dev

WORKDIR /app

USER dev

CMD ["php", "-a"]