FROM php:8.2-apache

# Definir un argumento
ARG USER

# Create user with same permissions as host and some useful stuff
RUN echo $USER
RUN adduser -u 1000 $USER
RUN mkdir /home/$USER/.ssh
RUN chown -R $USER:$USER /home/$USER/
RUN echo "StrictHostKeyChecking no" >> /home/$USER/.ssh/config
RUN echo "alias sf=/appdata/www/bin/console" >> /home/$USER/.bashrc

# Install packages and PHP extensions
RUN apt update \
    # common libraries and extensions
    && apt install -y git acl openssl openssh-client wget zip \
    && apt install -y libpng-dev zlib1g-dev libzip-dev libxml2-dev libicu-dev \
    && docker-php-ext-install intl pdo zip \
    # for MySQL
    && docker-php-ext-install pdo_mysql \
    # XDEBUG and APCu
    && pecl install xdebug apcu \
    # enable Docker extensions
    && docker-php-ext-enable --ini-name 05-opcache.ini opcache xdebug apcu

# Install and update composer
RUN curl https://getcomposer.org/composer.phar -o /usr/bin/composer && chmod +x /usr/bin/composer
RUN composer self-update

## Install Symfony binary
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli

RUN mkdir -p /var/www/html

# Config XDEBUG
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Update Apache config
COPY ./default.conf /etc/apache2/sites-available/default.conf
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && a2enmod rewrite \
    && a2dissite 000-default \
    && a2ensite default \
    && service apache2 restart

# Modify upload file size
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html
