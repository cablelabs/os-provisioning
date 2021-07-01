<p align="center">
<a target="_blank" href="https://nmsprime.com"><img src="https://github.com/nmsprime/nmsprime/raw/master/public/images/nmsprime-logo.png" alt="NMS Prime Logo" title="NMS Prime - Open Source Provisioning Tool for Cable-, DOCSIS- and Broadband-Networks" width="250"/></a> <b>hosted</b> by
<a target="_blank" href="https://cablelabs.com"><img src="http://www.displaysummit.com/wp-content/uploads/2019/07/Cable-Labs-Logo-Red.png" alt="CableLabs Logo" width="250"/></a>
</p>
<br>

[![Crowdin](https://d322cqt584bo4o.cloudfront.net/nmsprime/localized.svg)](https://crowdin.com/project/nmsprime)
[![StyleCI](https://github.styleci.io/repos/109520753/shield?branch=dev)](https://github.styleci.io/repos/109520753)

# OS Provisioning Community Version

[NMS PRIME](https://nmsprime.com) is an Open Source Network **Provisioning Tool** and **Network Management Platform** that enables access across multiple access technologies/domains, like **DOCSIS**, **FTTH**, FTTx, **DSL** and WiFi. It allows a seamless user experience across multiple connectivity services. It reduces complexity for network operators through a simple and easy to adapt **application marketplace**.

<div align="center"><a href="https://nmsprime.com"><img src="https://github.com/nmsprime/nmsprime/raw/i18n/public/images/apps_row.png" alt="NMS Prime Marketplace" title="NMS Prime Marketplace"/></a></div><br>

## **Community** Applications
- **Provisioning**
- **VoIP Provisioning**
- **Control**, [.. and more](https://devel.nmsprime.com/confluence/display/NMS/Applications)

## Functionality
**Provisioning Tool**
- **DOCSIS** 1.0, 1.1, 2.0, **3.0, 3.1**
- **FTTH**, **DSL**, WiFi Provisioning, via **TR-069** and **RADIUS**
- IPv4 / IPv6<br>

**Network Management Platform**
- **CMTS**, OLT, **Router** and Switch Management via SNMP or TR-069
- **Cable ingress detection**
- Show and manage your IT infrastructure in real-time in **topographic maps** and entity relation diagrams
- Auto configuration of **[Icinga2](https://icinga.com/)** and **[Cacti](https://www.cacti.net/index.php)** from one database
- **Ticket System**
- Generic **SNMP GUI** creator
- Basic billing functionality
- [more informations..](https://devel.nmsprime.com/confluence/display/NMS/Applications)

For more information head over to the NMS Prime [Official Documentation](https://devel.nmsprime.com/confluence/display/NMS/NMS+PRIME)


## Architectural Concepts

NMS Prime is based on the [Laravel](https://laravel.com/) framework and uses [PHP 7](https://php.net) for the back end and a modern and responsive [Bootstrap](http://getbootstrap.com/) theme for the front end.

It is tested and developed under CentOS 7 (RHEL 7).

NMS Prime is build with standard Linux tools, like
- [ISC DHCP](https://www.isc.org/downloads/dhcp/) for IPv4
- [Kea](https://www.isc.org/kea/) for IPv6
- [BIND](https://linux.die.net/man/8/named)
- [Icinga2](https://icinga.com/)
- [Cacti](https://www.cacti.net/index.php)

These tools are actively developed, approved and used. See [Design Architecture](https://devel.nmsprime.com/confluence/display/NMS/Architecture+Guidelines) for more information.


## Installation

### Community Version

**From RPM for CentOS 7 (RHEL 7)**

```bash
curl -vsL https://raw.githubusercontent.com/nmsprime/nmsprime/master/INSTALL-REPO.sh | bash
yum install nmsprime-*
```
### Enterprise Version

Select your applications and run them in the **Cloud** or **On-Prem** here: [Enterprise Installation](https://www.nmsprime.com/trial/?m=osprov--)

### Developers only (source-code installation)

In order to track and install all NMS Prime dependencies, the workflow for getting a source code installation up and running starts like described above. You can use both variants (community or enterprise) to do so.

Afterwards the NMS Prime RPM packages are replaced with the GIT repository by issuing the following commands:

```bash
for module in $(rpm -qa "nmsprime-*" | grep -v '^nmsprime-repos'); do rpm -e --justdb --noscripts --nodeps "$module"; done
  
yum install composer git
  
cd /var/www
git clone https://github.com/cablelabs/os-provisioning nmsprimeGit
mv nmsprimeGit/.git/ nmsprime/
rm -rf nmsprimeGit/
cd nmsprime
  
git checkout -- .

# move enterprise apps into /root folder for reference, they are not needed for the community git version
for module in $(ls -1 modules | grep -v '^HfcReq$\|^HfcSnmp$\|^NmsMail$\|^ProvBase$\|^ProvVoip$'); do mv "$module" /root/; done

COMPOSER_MEMORY_LIMIT=-1 composer update

/opt/rh/rh-php73/root/usr/bin/php artisan config:cache
/opt/rh/rh-php73/root/usr/bin/php artisan clear-compiled
/opt/rh/rh-php73/root/usr/bin/php artisan optimize
/opt/rh/rh-php73/root/usr/bin/php artisan migrate
/opt/rh/rh-php73/root/usr/bin/php artisan module:migrate
/opt/rh/rh-php73/root/usr/bin/php artisan module:publish
/opt/rh/rh-php73/root/usr/bin/php artisan queue:restart
/opt/rh/rh-php73/root/usr/bin/php artisan bouncer:clean
/opt/rh/rh-php73/root/usr/bin/php artisan auth:nms
/opt/rh/rh-php73/root/usr/bin/php artisan route:cache
/opt/rh/rh-php73/root/usr/bin/php artisan view:clear
```

---
## Contributors

**How to contribute**

Please read [CONTRIBUTING](https://github.com/cablelabs/os-provisioning/blob/dev/CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

**Write your own Application**

If you want to develop your own open-source or proprietary application(s), please refer to [Write your own Application](https://devel.nmsprime.com/confluence/x/qYJJ)

**History & Motivation**

NMS Prime started as an German initiative from ISP's for ISP's with the goal in mind to build an open source reference implementation for an technology and vendor agnostic provisioning solution (DOCSIS, FTTH, WiFi, ..). Get part of our movement and roll up your sleves by participating in our development.

**Roadmap**

See [Upcoming Developments](https://github.com/cablelabs/os-provisioning/wiki)

**License**

This project is licensed under the [Apache-2.0](https://github.com/cablelabs/os-provisioning/blob/dev/LICENSE) file for details. For more information: [License Article](https://devel.nmsprime.com/confluence/display/NMS/License)
