FROM php:8.2-cli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --version=2.5.3 --quiet
RUN apt-get update && apt-get install -y unzip \
    # extension required for XDebug
    && pecl install xdebug-3.2.0 && rm -rf /tmp/pear && docker-php-ext-enable xdebug

# PHP configuration
COPY conf.d /usr/local/etc/php/conf.d

# Clean the APT cache
RUN apt-get clean && rm -rf /var/lib/apt/lists

# Create non-root user for container
ARG HOST_USER_UID
ARG HOST_USER_GID
ENV HOST_USER_UID=${HOST_USER_UID}
ENV HOST_USER_GID=${HOST_USER_GID}
RUN addgroup --system --gid ${HOST_USER_GID} nonroot
RUN adduser --system --shell /bin/sh --uid ${HOST_USER_UID} --gid ${HOST_USER_GID} --disabled-password nonroot
USER nonroot

# Change the cache path for Composer to be able to set volume for it
RUN mkdir /tmp/composer-cache
ENV COMPOSER_CACHE_DIR=/tmp/composer-cache

WORKDIR /app
