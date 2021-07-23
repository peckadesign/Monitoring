FROM peckadesign/monitoring:edge

RUN apt-get update -y \
    && apt-get install -y --no-install-recommends \
    cron \
    && rm -rf /etc/cron.*/* \
    && apt-get clean -y \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* \
    && which cron

COPY entrypoint.sh /entrypoint.sh

COPY crontab /etc/crontab

ENTRYPOINT ["/entrypoint.sh"]

CMD ["cron", "-f", "-l", "2"]
