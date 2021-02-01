# PIKOBAR Tes Masif COVID-19

[![Build Status](https://travis-ci.org/jabardigitalservice/pikobar-rapidtest-api.svg?branch=master)](https://travis-ci.org/jabardigitalservice/pikobar-rapidtest-api)
[![Maintainability](https://api.codeclimate.com/v1/badges/fae1475c143f7a532884/maintainability)](https://codeclimate.com/github/jabardigitalservice/pikobar-rapidtest-api/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/fae1475c143f7a532884/test_coverage)](https://codeclimate.com/github/jabardigitalservice/pikobar-rapidtest-api/test_coverage)

Backend API untuk Aplikasi Pendaftaran dan Undangan Tes Masif COVID-19 PIKOBAR. 

## Petunjuk development
1. Ikuti Development Guides https://github.com/jabardigitalservice/development-guides
2. Clean Code, ikuti standard style FIG PSR-12 dengan menggunakan PHP Code Sniffer.
3. Clean Architecture, ikuti Laravel Best practices https://github.com/alexeymezenin/laravel-best-practices
4. Maksimalkan fitur-fitur built-in Laravel. Minimum dependencies.
5. Thin Controller. Gunakan Single Action Controller.
6. Tulis script Unit dan Feature Test.
7. Horizontal scalable, perhatikan 12-factor https://12factor.net
8. Log, Log, Log!

## Arsitektur Stack
1. PHP 7.4, Laravel
2. MySQL 5.7
3. Keycloak Identity & Access Management
4. Postman

## Bagaimana cara memulai development?
Clone Repository terlebih dahulu:
```
$ git clone https://github.com/jabardigitalservice/pikobar-rapidtest-api
```

Copy file config dan sesuaikan konfigurasinya
```
$ copy .env-example .env
```
Generate APP_KEY to .env
```
$ php artisan key:generate

Install dependencies menggunakan Composer"
```
$ composer install
```
Publish config UrlSigner
```
$ php artisan vendor:publish --provider="Spatie\UrlSigner\Laravel\UrlSignerServiceProvider"

Clear and confid cache
```
$ php artisan config:cache

Jalankan Artisan untuk migrasi dan seed database:
```
$ php artisan migrate:fresh --seed
```

Jalan Artisan Local Development Server:
```
$ php artisan serve
```

### Run Code Style check
```
$ ./vendor/bin/phpcs
```

### Run Unit & Feature Test
```
$ ./vendor/bin/phpunit
```

## Bagaimana cara deployment ke server?
Proses deployment menggunakan CI/CD AWS CodePipeline, CodeBuild, dan AWS Elastic Container Service (ECS).

## Kontributor
Terima kasih banyak untuk rekan-rekan volunteers (hire them!),
- Oky Saputra https://github.com/oky31
- Yazid Nurfadil https://github.com/yazidnurfadil
