[![Crowdin](https://d322cqt584bo4o.cloudfront.net/nmsprime/localized.svg)](https://crowdin.com/project/nmsprime)
[![StyleCI](https://github.styleci.io/repos/109520753/shield?branch=dev)](https://github.styleci.io/repos/109520753)


# NMS Prime

Open Source Provisioning and Management Software for network operators:

*Provisioning: DOCSIS*
- At the time we are highly focused on DOCSIS provisioning - including VoIP

*IT Maintenance*
- Show your *IT infrastructure in real-time* in topography MAP and ERD - Entity Relation Diagram
- Auto configuration of Nagios and Cacti from one database
- Generic SNMP GUI creator
- CMTS Management
- Billing for cable networks and ticket-System
- Cable ingress detection

For more information head over to our [Official Documentation](https://devel.roetzer-engineering.com/confluence/display/NMS/NMS+PRIME)


## Architectual Concepts

NMS Prime is written with PHP 7.1, [Laravel 5.3](https://laravel.com/) framework and a modern and responsive [Bootstrap](http://getbootstrap.com/) theme. It is tested and developed under CentOS 7 (RHEL 7).

NMS Prime is build with standard Linux tools, like
- [ISC DHCP](https://www.isc.org/downloads/dhcp/)
- [Named](https://linux.die.net/man/8/named)
- [Nagios](https://www.nagios.org/)
- [Cacti](https://www.cacti.net/index.php)

These tools are worldwide developed, approved and used. See [Design Architecture](https://devel.roetzer-engineering.com/confluence/display/NMS/Architecture+Guidelines) for more information


## Installation

### From RPM

For CentOS 7 (RHEL 7):

```bash
curl -vsL https://raw.githubusercontent.com/nmsprime/nmsprime/dev/INSTALL-REPO.sh | bash
yum install nmsprime-*
```

### From source code:

```bash
curl -vsL https://raw.githubusercontent.com/nmsprime/nmsprime/dev/INSTALL-REPO.sh | bash
sed -i 's/\[nmsprime\]/\[nmsprime\]\nexclude=nmsprime*/' /etc/yum.repos.d/nmsprime.repo
yum clean all && yum update -y
yum install git composer
git clone https://github.com/nmsprime/nmsprime.git /var/www/nmsprime
cd /var/www/nmsprime
./install-from-git.sh -y
```

For more Information have a look at the [Installation Process](https://devel.roetzer-engineering.com/confluence/display/NMS/Installation)


---

## How to contribute

Please read [CONTRIBUTING](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

* [Language Translation](https://crowdin.com/project/nmsprime)
* [Report a bug](https://devel.roetzer-engineering.com/confluence/display/NMS/Report+a+Bug)
* [Ticket-System](https://devel.roetzer-engineering.com/confluence/display/NMS/Open+Tickets+Overview)
* [Versioning](https://devel.roetzer-engineering.com/confluence/display/NMS/Versioning+Schema)


---

## Supporters

We thank the following supporters for helping and funding NMS Prime development. If you are interested in becoming a supporter, please read [here](https://devel.roetzer-engineering.com/confluence/pages/viewpage.action?pageId=6554183):

- **[Rötzer Engineering](https://roetzer-engineering.com)**
- **[ERZNET](http://erznet.tv)**
- **[MEK Cable](http://mek-cable.de)**
- **[KM3](https://km3.de)**

## Contributors

See the list of [contributors](https://github.com/nmsprime/nmsprime/graphs/contributors) who participated in this project.

---

## License

This project is licensed under the GPLv3 License - see the [LICENSE](LICENSE.md) file for details. For more informations: [License Article](https://devel.roetzer-engineering.com/confluence/display/NMS/License)
