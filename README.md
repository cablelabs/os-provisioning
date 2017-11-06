# NMS Prime

Open Source Provisioning and Management Software for network operators:

Provisioning: DOCSIS
- At the time we are highly focused on DOCSIS provisioning - including VoIP

IT Maintenance
- Show your IT infrastructure in real-time in topography MAP and ERD - Entity Relation Diagram
- Auto configuration of Nagios and Cacti from one database
- Generic SNMP GUI creator 

For more informations: [Official Documentation](https://devel.roetzer-engineering.com:3128/confluence/display/NMS/NMS+PRIME)


## Architectual Concepts

NMS Prime is written with PHP 5.6, [Laravel 5](https://laravel.com/) framework and a modern, cool and responsive [Bootstrap](http://getbootstrap.com/) topic. It is tested and developed under CentOS 7 (RHEL 7).

NMS Prime is build by standard Linux tools, like
- [ISC DHCP](https://www.isc.org/downloads/dhcp/)
- [Named](https://linux.die.net/man/8/named)
- [Nagios](https://www.nagios.org/)
- [Cacti](https://www.cacti.net/index.php)

These tools are worldwide developed, approved and used. See [Design Architecture](https://devel.roetzer-engineering.com:3128/confluence/display/NMS/Architecture+Guidelines) for more information


## Installation

### From RPM

For CentOS 7 (RHEL 7):

```
curl -vsL https://raw.githubusercontent.com/schmto/nmsprime/dev/INSTALL-REPO.sh | bash
yum install nmsprime-*
```

### From source code:

```
git clone https://github.com/schmto/nmsprime.git /var/www/nmsprime

cd /var/www/nmsprime
php artisan install
```

For more Informations [Installation](https://devel.roetzer-engineering.com:3128/confluence/display/NMS/Installation)


---

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

* [Report a bug](https://devel.roetzer-engineering.com:3128/confluence/display/NMS/Report+a+Bug)
* [Ticket-System](https://devel.roetzer-engineering.com:3128/confluence/display/NMS/Open+Tickets+Overview)
* [Versioning](https://devel.roetzer-engineering.com:3128/confluence/display/NMS/Versioning+Schema)


---

## Supporters

We thanks to the following supporters for helping and funding NMS Prime development. If you are interested in becoming a supporter, please read [here](https://devel.roetzer-engineering.com:3128/confluence/pages/viewpage.action?pageId=6554183):

- **[Rötzer Engineering](https://roetzer-engineering.com)**
- **[ERZNET](http://erznet.tv)**
- **[MEK Cable](http://mek-cable.de)**
- **[KM3](https://km3.de)**

## Authors

* **Torsten Schmidt** - *Initial work* - [torsten](https://github.com/schmto)
* **Nino Ryschawy** - *Maintainer/Developer* - [nino](https://github.com/todo)
* **Ole Ernst** - *Maintainer/Developer* - [ole](https://github.com/todo)
* **Patrick Reichel** - *Sub-Maintainer/Developer for ..* - [patrick](https://github.com/todo)
* **Christian Schramm** - *Developer for ..* - [christian](https://github.com/todo)
* **Sven Arndt** - *Developer for ..* - [sven](https://github.com/todo)

See also the list of [contributors](https://github.com/schmto/nms-prime/contributors) who participated in this project.

---

## License

This project is licensed under the GPLv3 License - see the [LICENSE.md](LICENSE.md) file for details. For more informations: [License Article](https://devel.roetzer-engineering.com:3128/confluence/display/NMS/License)
