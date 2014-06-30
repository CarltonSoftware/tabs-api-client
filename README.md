# Tabs API Client

[![Build Status](http://api-dev.nocc.co.uk:8080/buildStatus/icon?job=tabs-api-client)](http://api-dev.nocc.co.uk:8080/job/tabs-api-client/)

## Getting Started
To get started with this project you can either clone the repo into your project or alternatively install it via composer.

### Installing via git
1. Navigate to the directory you wish to add the project.
2. Clone the repo either via [ssh](git@github.com:CarltonSoftware/tabs-api-client.git), [svn](https://github.com/CarltonSoftware/tabs-api-client) or [zip](https://github.com/CarltonSoftware/tabs-api-client/archive/master.zip).
3. If you're not familiar with git, please see this [helpful guide](http://git-scm.com/book/en/Getting-Started-Git-Basics).

### Installing via composer
1. Create a composer.json where you want to install the project
2. Add the following:

```
{
	"repositories": [
		{
			"type": "vcs",
			"url": "git@github.com:CarltonSoftware/tabs-api-client.git"
		}
	],
	"require": {
		"carltonsoftware/tabs-api-client": "dev-master"	
	}
}
```
3: Download composer and install the repo:

```
curl -sS https://getcomposer.org/installer | php
./composer.phar install
```

For more information about composer, please see the [composer quick start guide](https://getcomposer.org/doc/00-intro.md).

4: Regenerating documentation

```
// cd into root of api client folder
git summodule init
git submodule update
phpdoc -d ./src/ -t ./tabs-api-client-example-docs/docs
```

### Downloading a version
We would recommend a downloading the latest release (v2.01) which can be found in the [releases view](https://github.com/CarltonSoftware/tabs-api-client/releases).
