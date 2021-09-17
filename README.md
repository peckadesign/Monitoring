# Monitoring

Aplikace umožňuje snadné sledování funkčnosti webových projektů - jejich dostupnosti, fungování různých podsystémů a závislostí (HTTPS, DNS, RabbitMQ, aktuálnost souborů atd.). Další informace, prezentace a [video z představení aplikace na Posobotě](https://youtu.be/ClEAFA7Wpyk) jsou k dispozici na [Peckovním blogu](http://www.peckadesign.cz/blog/monitoring-open-source-nastroj-pro-sledovani-webu-z-dilny-peckadesign).

Pro přihlášení je vyžadováno mít aplikaci na GitHubu a uživatelé se přihlašují výhradně přes tuto aplikaci. Vhodné je tak mít aplikaci v rámci účtu organizace a tím externě spravovat přístupy do aplikace. 


## Systémové a softwarové požadavky

Závislosti aplikace jsou zamknuty v `composer.json`, případně jsou popsány v instalačním skriptu Vagrantu (`/vagrant/server/bootstrap.sh`). Tam jsou také popsány požadavky na software na serveru.

Hardwarové požadavky se odvíjí od počtu kontrol a přístupu uživatelů. Při použití kolem cca 20 projektů, kdy každý ma do 50 kontrol je experimentálně ověřeno, že stačí server s 1 GB RAM a 1 CPU.


## Spuštění aplikace z Dockeru

Z repozitáře se překopírují soubory `docker-compose.full.yml`, `.env` a `monitoring-start.sh` do nového adresáře na počítači, na kterém má běžet Monitoring. Do souboru `.env` se doplní do klíče `MONITORING_URL` URL, na které má běžet Monitoring v prohlížeči ve tvaru https://monitoring.example.com. V tomto adresáři po spuštění budou uložená data Monitoringu.

Celý Monitoring se poté spustí příkazem:

```
bash monitoring-start.sh
```

Po doběhnutí všech výstupů je k dipozici na URL http://localhost:8080 (port je možné si změnit v `docker-compose.full.yml`).

Vlastní hezkou URL je nutné vyřešit pomocí proxy serveru (např. pomocí [Apache](https://httpd.apache.org/docs/current/mod/mod_proxy.html) nebo [nginx](https://docs.nginx.com/nginx/admin-guide/web-server/reverse-proxy/)), stejně tak zabezpečení přístupu pro/proti veřejnosti a HTTPS certifikát.

Výchozí přihlašovací e-mailová adresa a heslo administrátorského účtu je: `admin@localhost.local` a `admin`.


## Spuštění aplikace z repozitáře

Po naklonování repozitáře je potřeba zkopírovat lokální nastavení neonu a vyplnit údaje:

```
cp config/config.local.example.neon app/config/config.local.neon 
```

Příklad konfigurace démona `supervisord` pro běh RabbitMQ consumerů je v `/config/supervisor.conf`.

Vzorový `crontab` pro plnění front RabbitMQ je v `/config/crontab`.


## Rabbit consumers


### Url a API
Url je přímo adresa API rabbitu nebo skriptu (viz remote/rabbitConsumer.php), který přepošle informace z API, pokud není možný vzdálený přístup.

V obou případech je očekáván stejný výstup a to json s informacemi o frontách - /api/queues[/vhost]
Viz http://hg.rabbitmq.com/rabbitmq-management/raw-file/3646dee55e02/priv/www-api/help.html

Pro volání API je možné doplnit heslo a login.


### Fronty a minimálni počet
K jedné kontrole je možnost zadat víc front, které se mají kontrolovat. Počet front musí odpovídat počty kontrolovaných consumerů. Jako odělovač slouží čárka. Př. `aliveCheck,dnsCheck` a `1,2`.


### Kontrola
Pokud je mezi posledními hodnotami -1, znamená to, že je problém v komunikací s api nebo fronta neexistuje.
