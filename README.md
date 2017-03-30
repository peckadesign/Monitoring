# Monitoring

Aplikace umožňuje snadné sledování funkčnosti webových projektů - jejich dostupnosti, fungování různých podsystémů a závislostí (HTTPS, DNS, RabbitMQ, aktuálnost souborů atd.). Další informace, prezentace a [video z představení aplikace na Posobotě](https://youtu.be/ClEAFA7Wpyk) jsou k dispozici na [Peckovním blogu](http://www.peckadesign.cz/blog/monitoring-open-source-nastroj-pro-sledovani-webu-z-dilny-peckadesign).

Pro přihlášení je vyžadováno mít aplikaci na GitHubu a uživatelé se přihlašují výhradně přes tuto aplikaci. Vhodné je tak mít aplikaci v rámci účtu organizace a tím externě spravovat přístupy do aplikace. 


## Systémové a softwarové požadavky

Závislosti aplikace jsou zamknuty v `composer.json`, případně jsou popsány v instalačním skriptu Vagrantu (`/vagrant/server/bootstrap.sh`). Tam jsou také popsány požadavky na software na serveru.

Hardwarové požadavky se odvíjí od počtu kontrol a přístupu uživatelů. Při použití kolem cca 20 projektů, kdy každý ma do 50 kontrol je experimentálně ověřeno, že stačí server s 1 GB RAM a 1 CPU.


## Spuštění aplikace

Po naklonování repozitáře je potřeba zkopírovat lokální nastavení neonu a vyplnit údaje:

```
cp config/config.local.example.neon app/config/config.local.neon 
```

Příklad konfigurace démona `supervisord` pro běh RabbitMQ consumerů je v `/config/supervisor.conf`.

## Vývoj ve Vagrantu

Po stažení aplikace je k dispozici Vagrant, jako vývojové prostředí. Před použitím je třeba nainstalovat:

 - VirtualBox - https://www.virtualbox.org/wiki/Downloads
 - Vagrant - https://www.vagrantup.com/downloads.html

Po nainstalování z terminálu ze složky, kde je umístěn projektový `Vagrantfile` (root složka projektu) provést následující příkazy:
 1. `vagrant plugin install vagrant-hostmanager`
 	- doporučené, ale nepovinné - stará se o automatické doplňování `/etc/hosts`
 	- při nepoužití lze na stránky přistoupit pomocí IP adresy serveru, případně `/etc/hosts` vyplnit ručně

 2. `vagrant plugin install vagrant-vbguest`
	- plugin pro aktualizaci VirtualBox Guest Additions nutné pro správný chod NFS

 3. `vagrant up`
 	- stáhne obraz linuxového serveru (jednou pro všechny projekty)
 	- z obrazu založí virtuální mašinu, kterou následně nakonfiguruje, tak jak je pro naše projekty třeba

 4. Navštívit a prozkoumat `http://%project%.v.peckadesign.com`


### Zrušení vyžadování hesla


#### OS X
 1. pro NFS mapovaní
  - `/etc/sudoers.d/vagrant` https://docs.vagrantup.com/v2/synced-folders/nfs.html

```bash
Cmnd_Alias VAGRANT_EXPORTS_ADD = /usr/bin/tee -a /etc/exports
Cmnd_Alias VAGRANT_NFSD = /sbin/nfsd restart
Cmnd_Alias VAGRANT_EXPORTS_REMOVE = /usr/bin/sed -E -e /*/ d -ibak /etc/exports
%admin ALL=(root) NOPASSWD: VAGRANT_EXPORTS_ADD, VAGRANT_NFSD, VAGRANT_EXPORTS_REMOVE
```

 2. pro úpravu souboru `/etc/hosts` (je třeba správně uvést jméno uživatele v cestě)
  - `/etc/sudoers.d/vagrant_hostmanager` https://github.com/smdahlen/vagrant-hostmanager#passwordless-sudo

```bash
Cmnd_Alias VAGRANT_HOSTMANAGER_UPDATE = /bin/cp /Users/jmenouzivatele/.vagrant.d/tmp/hosts.local /etc/hosts
%admin ALL=(root) NOPASSWD: VAGRANT_HOSTMANAGER_UPDATE
```


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
