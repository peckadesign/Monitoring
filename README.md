# Monitoring


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
