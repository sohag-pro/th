<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About REST key-val store

Create an API with the following endpoints to create a key-value store. The purpose of the API is to store any arbitrary length value in a persistent store with respect to a key and later fetch these values by keys. These values will have a TTL (for example 5 minutes) and after the TTL is over, the values will be removed from the store.

- GET /api/values
- GET /api/values?keys=key1,key2
- POST /api/values
- PATCH /api/values

## Follow

- Clone
- composer install
- php artisan key:generate
- Create DB && Add to .env
- php artisan migrate
- php artisan serve


## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
