FROM composer:latest

RUN addgroup -g 1000 saeed && adduser -G saeed -g saeed -s /bin/sh -D saeed
 
USER saeed
 
WORKDIR /var/www/html
 
ENTRYPOINT [ "composer", "--ignore-platform-reqs" ]