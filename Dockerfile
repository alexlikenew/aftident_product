FROM php:8.1-apache
RUN apt-get update -y && apt-get install -y sendmail libpng-dev
RUN apt-get update && apt-get install -y \
    zlib1g-dev \
    libzip-dev \
    libwebp-dev \
    libjpeg-dev
RUN docker-php-ext-install zip
RUN docker-php-ext-configure gd --with-webp --with-jpeg && \
    docker-php-ext-install gd
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN sed -i '/<Directory \/var\/www\/html\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN a2enmod rewrite && service apache2 restart

