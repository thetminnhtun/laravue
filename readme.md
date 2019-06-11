# Laravel 5.8 + Vue 2

Laravel and Vue demo project or boilerplate 

![Demo](https://github.com/thetminnhtun/laravue/blob/master/demo.png)

## Purpose

This is a project to use boilterplate in Laravel and Vue using projects.

## Reference

[Code Inspire](https://github.com/Hujjat/laravStart)

## Dependencies and Plugins

- AdminLte 3
- Font Awesome
- Vue
- Vue Router
- Vue Form
- Vue Pagination
- Vue Progressbar
- Sweet Alert
- Laravel Passport
- ACL in laravel

## Installation

- Clone the repo `git clone https://github.com/thetminnhtun/laravue.git`
- `cd` into project folder
- Run `composer install`
- Create database
- Copy `.env.example` to `.env` or run command
    - In Linux `cp .env.example .env`
    - In Window `copy .env.example .env` and set database connection
- Run `php artisan key:generate` to generate the app key
- Run `php artisan passport:keys` to generate the passport key
- Run `php artisan migrate --seed` to create table and to seed data
- Done. 

## Login

username - `admin@demo.com`  
password - `12345678`

