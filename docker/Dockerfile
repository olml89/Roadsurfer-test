FROM php:8.3-alpine

ARG APP_ENV
RUN apk add --no-cache git

# Install PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

RUN install-php-extensions zip bcmath pdo_mysql xdebug @composer; \
    rm /usr/local/bin/install-php-extensions;

# Override XDebug with the environment provided one
ARG XDEBUG_PORT
COPY ./php/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN printf '\nxdebug.client_port=%s\n' ${XDEBUG_PORT} >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /app
