# README #

This README would normally document whatever steps are necessary to get your application up and running.

### Funktionsumfang ###

* Automatischer und regelmäßiger Export von Produkten an CHECK24 mit der Möglichkeit auf Kategorien, Attribute und
  Subshops zu filtern.
* Automatischer und regelmäßiger Import von Bestellungen aus CHECK24 und nahtlose Integration in den Bestellablauf von
  Magento.
* Übertragung der Sendungsnummer und des Versandstatus an CHECK24 
* Übergragung des Stornostatus von und an CHECK24
* Übertragung des Retourestatus an CHECK24


### Installation über Composer ###

`composer require check24-shopping/magento2-plugin`

### Module registieren und aktivieren. ###

```
$ bin/magento module:enable Check24Shopping_OrderImport
$ bin/magento setup:upgrade
$ bin/magento cache:flush
```

### Updates ###

Stehen Updates zur Verfügung, können diese über den normalen Magento Update Prozess durchgeführt werden. Die
individuelle Magento Version wird dabei automatisch berücksichtigt.

*Wir empfehlen vor jedem Update eine Sicherung durchzuführen.*

```
$ composer update
$ bin/magento setup:upgrade
$ bin/magento cache:flush
```

### Feedback / Fragen ###

[Hier finden Sie die aktuelle Dokumentation zum Magento2-Plugin](Documentation/CHECK24-Shopping_Magento-2-Plugin_Dokumentation_1.4.pdf)

ecommerce-plugins-shopping@check24.de
