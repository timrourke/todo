FROM nginx:1.15.3-alpine

ADD nginx.conf /etc/nginx/nginx.conf
ADD sites-enabled/app.conf /etc/nginx/sites-enabled/app.conf

# Delete the default server config at localhost
RUN rm /etc/nginx/conf.d/default.conf

RUN echo "upstream php-upstream { server php-fpm:9001; }" > /etc/nginx/conf.d/upstream.conf

RUN addgroup -g 1000 -S www-data \
	&& adduser -u 1000 -D -S -G www-data www-data

EXPOSE 80
