FROM webdevops/php-nginx:7.4

ENV WEB_DOCUMENT_ROOT=/app/public \
    RUN_COMPOSER_INSTALL="false" \
    RUN_CRON="true" \
    RUN_MIGRATION="true" \
    RUN_QUEUE="true" \
    APP_KEY="00000000000000000000000000000000" \
    APP_NAME=parser.atlasacademy.io \
    APP_URL=http://parser.test.atlasacademy.io \
    DB_CONNECTION=mysql \
    DB_HOST=db \
    DB_PORT=3306 \
    DB_DATABASE=parser \
    DB_USERNAME=root \
    DB_PASSWORD=password \
    LOG_CHANNEL=syslog \
    QUEUE_CONNECTION=database \
    QUEUE_TABLE=jobs \
    SUBMISSION_HOST="https://submissions.atlasacademy.io" \
    SUBMISSION_KEY="00000000000000000000000000000000"

WORKDIR /app

COPY ./build/setup.sh /opt/docker/provision/entrypoint.d/30-setup.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/30-setup.sh
COPY build/queue.conf /opt/docker/etc/supervisor.d/queue.conf

# Disable Cron Syslog output
RUN sed -i "s|not facility(auth, authpriv);|not facility(auth, authpriv, cron);|g" /opt/docker/etc/syslog-ng/syslog-ng.conf

# Enable real ip passthrough from nginx proxy
COPY ./build/nginx.conf /opt/docker/etc/nginx/vhost.common.d/00-real-ip.conf

COPY --chown=application . /app
RUN su application -c "composer install --no-dev"
