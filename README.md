# udm
Web databáze studijních materiálů

# Instalace

Před spuštěním aplikace je potřeba mít nainstalovaný web server, PHP v8.0+, databázový server MariaDB a composer.

V kořenovém adresáři aplikace `app` zadejte příkaz pro instalaci závislostí:

`composer install`

Nechte databázi provést SQL skript `install.sql` umístěný v krořenovém adresáři aplikace.

Přesuňte obsah kořenového adresáře aplikace na místo web serveru.
