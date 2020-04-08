# Let's take php 7.4 as a base image
FROM php:7.4-cli
# We'll also install wget and PHP mysqli extension
RUN apt-get update \
&& apt-get -y install wget \
&& docker-php-source extract \
&& docker-php-ext-install mysqli \
&& docker-php-source delete
# We'll use load.php, so we need to copy it to the image
COPY load.php /usr/src/myapp/
# And let's change the working dir
WORKDIR /usr/src/myapp
