# NMS Prime

DOCSIS/Cable Open Source Provisioning and Management Software

---

## Getting Started

TODO - TS:

- main goals and purpose of the project
- short summary of the included projects like L5, bootstrap, ..

---

## Documentation

TODO - TS:
- link to confluence

---

## Installing

There are two ways to get NMS PRIME running: You can either use our RPM package or clone the git repository: `git clone https://github.com/schmto/nms-prime.git`

TODO - PR: some general notes ..


### Prerequisites

TODO - PR: topics to addressed:

- Linux Versions Tested: CentOS 7 (but should run on other distributions as well)
- PHP >= 5.5.9 (which is not part of the standard repo but can be installed via IUS repo; see installation notes below)
- other needed software (mostly installed as dependency if installing via the RPM):
  * Apache webserver
  * MariaDB
  * ISC DHCP server
  * TFTP server
  * Nameserver (BIND)
  * Nagios
  * Cacti
  * RRD tool
  * DOCSIS tool

### Set up the basic system

#### Install PHP

```bash
# add the IUS repo containing a more recent version of PHP
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

* **Torsten Schmidt** - *Initial work* - [schmto](https://github.com/schmto)
* **Nino Ryschawy** - *Maintainer/Developer* - [nino](https://github.com/todo)
* **Ole Ernst** - *Maintainer/Developer* - [ole](https://github.com/todo)
* **Patrick Reichel** - *Sub-Maintainer/Developer for ..* - [patrick](https://github.com/todo)
* **Christian Schramm** - *Developer for ..* - [christian](https://github.com/todo)

TODO: add other main developers..

See also the list of [contributors] (https://github.com/schmto/nms-prime/contributors) who participated in this project.

---

## License

This project is licensed under the GPLv3 License - see the [LICENSE.md](LICENSE.md) file for details.

The code under public/components/assets-admin has a special license – see:
* [Color Admin](https://wrapbootstrap.com/theme/color-admin-admin-template-front-end-WB0N89JMK) [LICENSE.md](/public/components/assets-admin/LICENSE.md) for license informations
