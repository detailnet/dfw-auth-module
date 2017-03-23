# Zend Framework Module for authentication and authorization

[![Build Status](https://travis-ci.org/detailnet/dfw-auth-module.svg?branch=master)](https://travis-ci.org/detailnet/dfw-auth-module)
[![Coverage Status](https://img.shields.io/coveralls/detailnet/dfw-auth-module.svg)](https://coveralls.io/r/detailnet/dfw-auth-module)
[![Latest Stable Version](https://poser.pugx.org/detailnet/dfw-auth-module/v/stable.svg)](https://packagist.org/packages/detailnet/dfw-auth-module)
[![Latest Unstable Version](https://poser.pugx.org/detailnet/dfw-auth-module/v/unstable.svg)](https://packagist.org/packages/detailnet/dfw-auth-module)

## Introduction
This module contains tools for authentication (based on the [Authentication component for ZF](https://github.com/zendframework/zend-authentication)) and authorization (based on [ZfcRbac](https://github.com/ZF-Commons/zfc-rbac)).

## Requirements
[Zend Framework Skeleton Application](http://www.github.com/zendframework/ZendSkeletonApplication) (or compatible architecture)

## Installation
Install the module through [Composer](http://getcomposer.org/) using the following steps:

  1. `cd my/project/directory`
  
  2. Create a `composer.json` file with following contents (or update your existing file accordingly):

     ```json
     {
         "require": {
             "detailnet/dfw-auth-module": "^1.0"
         }
     }
     ```
  3. Install Composer via `curl -s http://getcomposer.org/installer | php` (on Windows, download
     the [installer](http://getcomposer.org/installer) and execute it with PHP)
     
  4. Run `php composer.phar self-update`
     
  5. Run `php composer.phar install`
  
  6. Open `configs/application.config.php` and add following key to your `modules`:

     ```php
     'Detail\Auth',
     ```

  7. Copy `vendor/detailnet/dfw-auth-module/config/detail_auth.local.php.dist` into your application's
     `config/autoload` directory, rename it to `detail_auth.local.php` and make the appropriate changes.

## Usage
tbd
