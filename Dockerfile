FROM ubuntu:focal
ENV DEBIAN_FRONTEND=noninteractive
#apt installs
RUN apt-get update -y
RUN apt-get upgrade -y
RUN apt-get install -y software-properties-common
RUN add-apt-repository ppa:ondrej/php
RUN apt-get install -y php8.0
RUN apt-get install -y php8.0-bcmath
RUN apt-get install -y php8.0-curl
RUN apt-get install -y php8.0-common
RUN apt-get install -y php8.0-mbstring
RUN apt-get install -y php8.0-zip
RUN apt-get install -y php8.0-cli
RUN apt-get -y install cron
RUN apt-get install -y unzip
RUN apt-get install -y golang-go
RUN apt-get install -y git

WORKDIR /
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
WORKDIR /app/

ENTRYPOINT sh /app/dockerfiles/entrypoint.sh