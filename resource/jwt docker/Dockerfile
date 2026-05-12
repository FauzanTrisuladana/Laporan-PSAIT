FROM php:8.2-apache

RUN docker-php-ext-install pdo_mysql

RUN a2enmod rewrite headers

RUN sed -ri 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

RUN rm -f /var/www/html/index.html

RUN sed -ri 's/Listen 80/Listen 8000/g' /etc/apache2/ports.conf \
    && sed -ri 's/:80>/:8000>/g' /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

COPY . /var/www/html

EXPOSE 8000

CMD ["apache2-foreground"]