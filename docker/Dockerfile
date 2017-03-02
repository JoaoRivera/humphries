FROM php:7.0-fpm
MAINTAINER pedromdspereira.93@gmail.com

COPY ./php.ini /usr/local/etc/php/

# Update Package List
RUN apt-get update

# Basic packages
RUN apt-get install --fix-missing -y sudo software-properties-common nano curl \
    build-essential dos2unix gcc git git-flow libmcrypt4 libpcre3-dev apt-utils \
    make python2.7-dev python-pip re2c supervisor unattended-upgrades whois vim zip unzip \
    libpng-dev libpq-dev libzip-dev libicu-dev zlib1g-dev libz-dev libmemcached-dev \
    libjpeg-dev libfreetype6-dev libjpeg62-turbo-dev

# Composer install
RUN curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer && \
    chmod +x /usr/local/bin/composer

ENV PATH ~/.composer/vendor/bin:$PATH

# Basic Extensions
RUN docker-php-ext-install exif mbstring pdo_mysql opcache pcntl zip pdo gd \
    pdo_pgsql pgsql intl

# GD Extension
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ && \
    docker-php-ext-install gd

# Redis Extension
RUN git clone -b php7 https://github.com/phpredis/phpredis.git /usr/src/php/ext/redis && \
    docker-php-ext-install redis

# Memcached Extension
RUN git clone -b php7 https://github.com/php-memcached-dev/php-memcached.git /usr/src/php/ext/memcached && \
    docker-php-ext-install memcached

# Phalcon Extension
RUN git clone --depth=1 git://github.com/phalcon/cphalcon.git /phalcon && \
    cd /phalcon/build && \
    ./install && \
    echo "extension=phalcon.so" >> /usr/local/etc/php/conf.d/30phalcon.ini

# Phalcon Devtools
RUN git clone https://github.com/phalcon/phalcon-devtools.git /phalcon-devtools && \
    chmod ugo+x /phalcon-devtools/phalcon.php && \
    ln -s /phalcon-devtools/phalcon.php /usr/bin/phalcon


ADD ./entrypoint.sh /
WORKDIR /app
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]