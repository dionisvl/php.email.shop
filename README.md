<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

# Shop in e-mail

## How to Install

- git clone THIS_REPO
- cp .env.example .env
- composer install
- php artisan key:generate
- create empty DB and config it into .env
- php artisan migrate


  
- Optional:  
    fill some test Products:
```
  php artisan tinker  
  factory(App\Product::class, 4)->create();  
```


Refresh migrations:
```
php artisan migrate:refresh
```
