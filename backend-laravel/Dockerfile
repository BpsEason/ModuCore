FROM php:8.2-fpm-alpine # Changed to alpine for smaller image size

# 安裝系統依賴
RUN apk add --no-cache \
    git \
    zip \
    unzip \
    nginx \
    supervisor \
    redis \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    mysql-client \
    autoconf \
    g++ \
    make \
    pcre-dev \
    zlib-dev \
    libgcc \
    linux-headers && \
    # 安裝 PHP 擴展
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) gd pdo pdo_mysql bcmath sockets zip && \
    # 安裝 Swoole
    pecl install swoole && docker-php-ext-enable swoole

# 安裝 Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 複製專案檔案
WORKDIR /var/www/html
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html

# 安裝 Laravel 依賴
# 這一步會在容器內部執行 composer install，確保所有 PHP 依賴都已存在
RUN composer install --no-dev --optimize-autoloader

# 複製 Nginx 和 Supervisor 配置
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf # Nginx config for this container
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php-fpm/www.conf /usr/local/etc/php-fpm.d/www.conf

# 暴露端口 (Laravel Octane 默認端口)
EXPOSE 8000

# 啟動 Supervisor 來管理 Nginx, PHP-FPM, Octane
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
