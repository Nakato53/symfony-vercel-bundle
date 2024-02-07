# Vercel Symfony Bundle

A Symfony Bundle, that help you to configure your Symfony project running on Vercel

## Installation

Use [composer](https://getcomposer.org/) to install Vercel Symfony Bundle.

```bash
composer require nakato53/symfony-vercel-bundle --dev
```

## Usage

```bash
php bin/console vercel:install
```
This command will setup all required configuration for you.
It will create :
 - vercel.json -> configuration for the runtime
 - api folder -> folder used by vercel to expose the application
 - composer.json scripts -> add vercels scripts

## Customize

### vercel.json
In this file you should add your environnement variables. Becareful, this file is part of your git repository, you should not expose any important datas. Use your Vercel project settings to handle thoses variables.

### composer.json
In the scripts/vercel parts, you can add any script you need to use during the deployment of your application. You can use this part to run migrations.

If using assetmapper, you should add this script to compile your assets :

```bash
"@php bin\/console asset-map:compile --env=prod"
```
            

## Deploy
You just have to create a project on Vercel, and link it to your git repository. On each push , you will trigger an automatic deployment