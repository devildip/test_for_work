FROM ubuntu:16.04

ENV TERM=xterm
RUN apt-get update
RUN apt-get -y upgrade

RUN DEBIAN_FRONTEND=noninteractive apt-get -y --fix-missing install php7.0 \
      php7.0-cli \
      php-fpm \
      php7.0-gd \
      php7.0-json \
      php7.0-mbstring \
      php7.0-xml \
      php7.0-xsl \
      php7.0-zip \
      php7.0-soap \
      php-pear \
      curl \
      php7.0-curl \
      apt-transport-https \
      git \
      apt-transport-https \
      nano \
      lynx-cur
RUN apt-get install -y openssh-server
RUN mkdir /var/run/sshd
RUN echo 'root:12345' | chpasswd
RUN sed -i 's/PermitRootLogin prohibit-password/PermitRootLogin yes/' /etc/ssh/sshd_config
RUN sed 's@session\s*required\s*pam_loginuid.so@session optional pam_loginuid.so@g' -i /etc/pam.d/sshd
ENV NOTVISIBLE "in users profile"
RUN echo "export VISIBLE=now" >> /etc/profile

RUN DEBIAN_FRONTEND=noninteractive apt-get -y --fix-missing install supervisor
RUN mkdir -p /var/log/supervisor

RUN DEBIAN_FRONTEND="noninteractive" apt-get install -y nginx-full

RUN curl -sS https://getcomposer.org/installer | php
RUN mv composer.phar /usr/local/bin/composer


ADD config/nginx/nginx-site.conf /etc/nginx/sites-available/default
ADD config/nginx/nginx.conf /etc/nginx/nginx.conf
ADD config/php/php.ini /etc/php/7.0/fpm/php.ini
ADD config/fpm/www.conf /etc/php/7.0/fpm/pool.d/www.conf
ADD config/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
ADD config/php/index.php /var/www/html/index.php

WORKDIR /var/www/html/


RUN mkdir -p /run/php


VOLUME /var/www/html


EXPOSE 80
EXPOSE 22


CMD ["/usr/bin/supervisord"]