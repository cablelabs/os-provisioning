# NMS Prime

DOCSIS/Cable Open Source Provisioning and Management Software

---

## Getting Started

NMS Prime is a open source provisioning and management web application for network operators. At the time we are highly focused on DOCSIS. The system is designed technology neutral – so other technologies (like FTTH or TR-69) will be implemented in the feature. NMS Prime provides a user friendly GUI for:

- ISC DHCP Server,
- TFTP – to serve config files,
- NTP – to provide time,
- config of Nagios and Cacti,

NMS Prime is written with PHP 5.6, Laravel 5 framework and a modern, cool and responsive bootstrap topic.

Our goal is: "changing the way software for ISPs works nowadays!"

### Addressed Topics

#### Provisioning
- [DOCSIS provisioning and customer management](https://www.youtube.com/watch?v=t-PFsy42cI0)
- [VoIP provisioning (SIP)](http://todo)
- [Quality Analysis](https://www.youtube.com/watch?v=6UoxwtlcAkA)
- [administration of DHCP, Config Files, IP-Pool, CMTS, ..](http://todo)
- [Billing](http://todo)

#### Network Management
- [Topographic Map and Entitiy Relation Diagram](https://www.youtube.com/watch?v=_urtVRYNuys)
- [Generic GUI for cacti and nagios](http://todo)
- [SNMP GUI for device controlling](http://todo)


For more details checkout: [Overview Page](https://devel.roetzer-engineering.com:3128/confluence/display/NMS/Overview)

---


### NMS Prime Supporters

We thanks to the following supporters for helping and funding NMS Prime development. If you are interested in becoming a supporter, please visit the NMS Prime [NMS Prime page](https://nmsprime.com):

- **[Rötzer Engineering](https://roetzer-engineering.com)**
- **[ERZNET](http://erznet.tv)**
- **[MEK Cable](http://mek-cable.de)**
- **[KM3](https://km3.de)**



### Further Documentation

Checkout our Wiki under [NMS Prime](https://devel.roetzer-engineering.com:3128/confluence/display/NMS)

---

## Installing

There are two ways to get NMS PRIME running: You can either use our RPM package or clone the git repository: `git clone https://github.com/schmto/nms-prime.git`

TODO - PR: some general notes ..


### Prerequisites

TODO - PR: topics to addressed:

- Linux Versions Tested: CentOS 7
- PHP >= 5.5.9 (which is not part of the standard repo but can be installed via IUS repo)
- other needed software (mostly installed as dependency if installing via the RPM):
  * Apache webserver
  * MariaDB
  * ISC DHCP server
  * TFTP server
  * Nagios
  * Cacti
  * RRD tool
  * DOCSIS tool

### Set up the basic system

#### Install PHP

```bash
# add IUS repo
wget https://centos7.iuscommunity.org/ius-release.rpm
rpm -Uvh ius-release.rpm

# update php version with yum replace plugin
yum install yum-plugin-replace
yum replace php --replace-with php56u
```

### Set up NMS Prime framework

TODO - PR: review and rewrite or link to confluence ..

```
Install Commands
```

### Set up Apache, DHCP, TFTP, ..

TODO - PR: ..


### Next Steps

Link: to confluence

topics to be addressed:

- setup CMTS config
- setup switch
- setup default config files


### Testing

TODO - PR:

- Explain what these tests test and why

```
Give an example
```

---

## Usage

TODO:

Link to confluence space ..

---

## Deployment

Add additional notes about how to deploy

TODO - PR: reference to our RPM build stuff

---

## Built With

* [Laravel](http://..) - ..
* [Bootstrap](http://..) - ..
* [Color Admin](https://wrapbootstrap.com/theme/color-admin-admin-template-front-end-WB0N89JMK) - Bootstrap - Color Admin - Admin Template - Front End

TODO - PR: add more main projects

---

## Contributing

Please read [CONTRIBUTING.md] for details on our code of conduct, and the process for submitting pull requests to us.

### How to contribute a bug

TODO - PR: ..

### TODOs

Link to JIRA or to a public Ticket / TODO system

---

## Versioning

We use .. for versioning. For the versions available, see the ..

---

## Authors

* **Torsten Schmidt** - *Initial work* - [torsten](https://github.com/schmto)
* **Nino Ryschawy** - *Maintainer/Developer* - [nino](https://github.com/todo)
* **Ole Ernst** - *Maintainer/Developer* - [ole](https://github.com/todo)
* **Patrick Reichel** - *Sub-Maintainer/Developer for ..* - [patrick](https://github.com/todo)
* **Christian Schramm** - *Developer for ..* - [christian](https://github.com/todo)
* **Sven Arndt** - *Developer for ..* - [sven](https://github.com/todo)

See also the list of [contributors] (https://github.com/schmto/nms-prime/contributors) who participated in this project.

---

## License

This project is licensed under the GPLv3 License - see the [LICENSE.md](LICENSE.md) file for details.

The code under public/components/assets-admin has a special license – see:
* [Color Admin](https://wrapbootstrap.com/theme/color-admin-admin-template-front-end-WB0N89JMK) [LICENSE.md](/public/components/assets-admin/LICENSE.md) for license informations
