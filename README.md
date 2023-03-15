# udm
Web databáze studijních materiálů

# Instalace

Před spuštěním aplikace je potřeba mít nainstalovaný web server, PHP v8.0+, databázový server MariaDB a composer.

V kořenovém adresáři aplikace `app` zadejte příkaz pro instalaci závislostí:

`composer install`

V případě potřeby změny adresy databázového serveru, uživatelského jména nebo hesla pro připojení k databázi z výchozích hodnot, změňte hodnoty konstant `DB_HOST`, `DB_USER` nebo `DB_PASS` v souboru `config.inc.php` umístěném v kořenovém adresáři aplikace.

Nechte databázi provést SQL skript `install.sql` umístěný v krořenovém adresáři aplikace.

V kořenovém adresáři aplikace vytvořte adresář `data` a umožněte webovému serveru přístup pro čtení a zápis.

Přesuňte obsah kořenového adresáře aplikace na místo web serveru.
