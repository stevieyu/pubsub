# pub sub

## env

```bash
url="https://www.ghproxy.cn/https://github.com/dunglas/frankenphp/releases/latest/download/frankenphp-linux-x86_64" && \
binpath="/root/.local/bin" && \
curl -L "$url" -o "$binpath"/frankenphp && \
chmod +x "$binpath"/frankenphp && \
alias php-cli='frankenphp php-cli' && \
echo '#!/usr/bin/env bash\nfrankenphp php-cli "$@"' >> "$binpath"/php && \
chmod +x "$binpath"/php && \
curl -L https://mirrors.aliyun.com/composer/composer.phar -o "$binpath"/composer && \
chmod +x "$binpath"/composer && \
composer config -g repos.packagist composer https://mirrors.tencent.com/composer/ && \
frankenphp php-server
```