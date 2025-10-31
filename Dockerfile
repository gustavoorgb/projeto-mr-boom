# syntax=docker/dockerfile:1
# Versões
FROM dunglas/frankenphp:1-php8.4 AS frankenphp_upstream

# A imagem base que inclui PHP e Node.js
FROM frankenphp_upstream AS frankenphp_base

WORKDIR /app

# Instala as dependências do sistema, incluindo Node.js e NPM
RUN apt-get update && apt-get install -y --no-install-recommends \
    acl \
    file \
    gettext \
    git \
    nodejs \
    npm \
    && rm -rf /var/lib/apt/lists/*

# Instala as extensões PHP
RUN set -eux; \
    install-php-extensions \
    @composer \
    apcu \
    intl \
    opcache \
    pdo_mysql \
    zip \
    ;

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV MERCURE_TRANSPORT_URL=bolt:///data/mercure.db
ENV PHP_INI_SCAN_DIR=":$PHP_INI_DIR/app.conf.d"

COPY --link frankenphp/conf.d/10-app.ini $PHP_INI_DIR/app.conf.d/
COPY --link --chmod=755 frankenphp/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
COPY --link frankenphp/Caddyfile /etc/frankenphp/Caddyfile

ENTRYPOINT ["docker-entrypoint"]
HEALTHCHECK --start-period=60s CMD curl -f http://localhost:2019/metrics || exit 1
CMD [ "frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile" ]

# Imagem de Desenvolvimento
FROM frankenphp_base AS frankenphp_dev

ENV APP_ENV=dev
ENV XDEBUG_MODE=off
ENV FRANKENPHP_WORKER_CONFIG=watch

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN set -eux; \
    install-php-extensions \
    xdebug \
    ;

COPY --link frankenphp/conf.d/20-app.dev.ini $PHP_INI_DIR/app.conf.d/

CMD [ "frankenphp", "run", "--config", "/etc/frankenphp/Caddyfile", "--watch" ]

# Imagem de Produção
FROM frankenphp_base AS frankenphp_prod

ENV APP_ENV=prod

# Estágio de compilação dos assets
FROM frankenphp_base AS assets_build
WORKDIR /app
COPY --link package.json yarn.lock ./
RUN npm install
COPY --link . ./
RUN npm run build
RUN rm -Rf frankenphp/
RUN rm -Rf node_modules/

# Estágio de produção final
FROM frankenphp_base AS final_prod
WORKDIR /app
COPY --link --from=assets_build /app/public/build ./public/build
COPY --link --from=assets_build /app/composer.* ./
COPY --link --from=assets_build /app/vendor ./vendor
COPY --link --from=assets_build /app/symfony.* ./
COPY --link --from=assets_build /app/bin ./bin
COPY --link --from=assets_build /app/src ./src
COPY --link --from=assets_build /app/templates ./templates
COPY --link --from=assets_build /app/config ./config
COPY --link --from=assets_build /app/migrations ./migrations
COPY --link --from=assets_build /app/public/index.php ./public/index.php
COPY --link --from=assets_build /app/.env.prod ./


RUN rm -Rf frankenphp/
RUN mkdir -p var/cache var/log; \
    chmod +x bin/console; sync;
