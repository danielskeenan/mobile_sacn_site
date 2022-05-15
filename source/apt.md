---
title: Apt Repository
extends: _layouts.documentation
section: content
---

# Apt Repository

An apt repository is available at [https://apt.mobilesacn.dankeenan.org](https://apt.mobilesacn.dankeenan.org). Releases
are signed with [this key](/mobilesacn-archive-keyring.gpg).

## Setup

Install the [mobile-sacn-repo package](/mobile-sacn-repo_1.0.0_all.deb), or run:

```shell
wget -O- https://mobilesacn.dankeenan.org/mobilesacn-archive-keyring.gpg | sudo tee /usr/share/keyrings/mobilesacn-archive-keyring.gpg`
echo deb [arch=amd64 signed-by=/usr/share/keyrings/mobilesacn-archive-keyring.gpg] https://apt.mobilesacn.dankeenan.org stable main | tee /etc/apt/sources.list.d/mobile-sacn.list
```

Then install the `mobilesacn` package:
```shell
sudo apt-get update
sudo apt-get install mobilesacn
```
