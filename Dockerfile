# Use a imagem PHP 8.3
FROM php:8.3-cli

# Instale as dependências necessárias para o Swoole
RUN pecl install swoole && docker-php-ext-enable swoole \
    && apt-get update && apt-get install -y \
    unzip \
    libzip-dev \
    git \
    && docker-php-ext-install zip

# Configure a extensão Swoole
RUN echo "extension=swoole.so" > /usr/local/etc/php/conf.d/docker-php-ext-swoole.ini

# Copie os arquivos de configuração
COPY ./composer.json /app/composer.json
COPY ./composer.lock /app/composer.lock

# Instale as dependências usando o Composer
WORKDIR /app
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-scripts --no-autoloader \
    && composer clear-cache

# Copie o código do aplicativo para o contêiner
COPY . /app

# Exponha a porta 9501 para o Swoole
EXPOSE 9501

# Comando padrão para iniciar o servidor Swoole
CMD ["php", "index.php"]