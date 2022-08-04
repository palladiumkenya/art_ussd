FROM php:8.0

WORKDIR /usr/src/myapp

COPY .  /usr/src/myapp

CMD [ "php","-S" ,"0.0.0.0:8000"]

EXPOSE 8000
