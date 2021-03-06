# PHPFramework fork
This framework is a fork of [Luzifer's PHPFramework](https://github.com/Luzifer/PHPFramework/).<br>
Since the fork wide parts of the system were rewritten or heavily modified. Here's a (not neccessarily complete) overview of the difference to the original PHPFramework:

* PHP namespaces for [PSR-4](http://www.php-fig.org/psr/psr-4/) autoloading (the former autoloader has been removed)
* Built as [composer](https://getcomposer.org/) package.
* New static classes to access the configuration and database connection everywhere
* Cache built upon [Nette Caching](https://github.com/nette/caching)
* Support for Domain Driven Design, including Domain Model Objects, Repositories, Collections and an [ORM](https://en.wikipedia.org/wiki/Object-relational_mapping) mechanism.

The fork is not compatible to the original framework and not compatible to its own previous versions.
After the next major release (2.0) we'll switch to a more backwards compatible development.

# Requirements

Successfully tested with

- Debian / Ubuntu Linux
- PHP from 5.4.0 up to 5.6.2
- Apache or nginx
- MySQL 5.6

# Setup

The PHPFramework is designed to work with [composer](https://getcomposer.org/).

Your project `composer.json` file might look like this:

    {
      "name": "vendor/myproject",
      "require": {
        "app-zap/phpframework": "dev-develop"
      },
      "autoload": {
        "psr-4": {
          "Vendor\\MyProject\\": "app/Classes/"
        }
      }
    }

1. Set up your project with `$ composer update`
1. create an `app` sub directory for your application
1. Copy the `index.php.example` from the PHPFramework folder (should be `vendor/app-zap/phpframework`) to your root level as `index.php`.

Inside your `app` directory use this structure:

* `Classes/` - Starting point for your [PSR-4](http://www.php-fig.org/psr/psr-4/) autoloadable classes
* `templates/` - Your [twig](http://twig.sensiolabs.org/) templates
* `routes.php` - Returns an array with regular expression routes mapping to controller class names

Start with the following `.gitignore` file:

    vendor/
    settings_local.ini

[![Build Status](https://travis-ci.org/app-zap/PHPFramework.svg?branch=develop)](https://travis-ci.org/app-zap/PHPFramework)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/cc3c64ed-fcd3-4be9-8f6c-ba7ad0adad11/small.png)](https://insight.sensiolabs.com/projects/cc3c64ed-fcd3-4be9-8f6c-ba7ad0adad11)
