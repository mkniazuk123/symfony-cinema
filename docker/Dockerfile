FROM php:8.4-alpine

RUN apk update && apk add git bash coreutils openssh-client postgresql-client postgresql-dev rabbitmq-c-dev

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions bcmath sockets pcntl pdo_pgsql @composer xdebug amqp-beta \
    && php -m \
    && composer -V

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN echo "zend.exception_ignore_args = Off" >> ${PHP_INI_DIR}/php.ini
RUN echo "zend.exception_string_param_max_len = 1024" >> ${PHP_INI_DIR}/php.ini
RUN echo "memory_limit = 512m" >> ${PHP_INI_DIR}/php.ini

COPY docker/php/conf.d $PHP_INI_DIR/conf.d/

ENV COMPOSER_ALLOW_SUPERUSER 1 # To allow plugins

COPY --link \
    --from=ghcr.io/symfony-cli/symfony-cli:latest \
    /usr/local/bin/symfony /usr/local/bin/symfony
