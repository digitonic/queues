# Digitonic Validator

## Overview

This package will add a centralized queue class which can be  altered via envs.

## Getting Started

Install the package (Remember to add the private digitonic satis repo to the composer.json)

```bash
$ composer require digitonic/queues 
```

Install Queue command

```bash
$ php artisan digitonic:queues:install
```

Install Doctrine workers command

```bash
$ php artisan digitonic:queues:publish
```
