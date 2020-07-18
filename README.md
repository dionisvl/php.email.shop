<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

# E-commerce catalog/cart API

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


## Немного документации по проекту:
- Для тестирования методов используйте Postman
- по адресу /demo доступен функционал JS демонстрирующий работу API


#### Метод для получения корзины:
GET http://THIS_SITE/api/cart/get/?email={email}

#### Упрощенные роуты работы с корзиной:
- добавляет указанный товар и возвращает актуальную корзину    
POST http://THIS_SITE/api/cart/add/{itemId}/email/{email}  
- удаляет указанный товар и возвращает актуальную корзину  
POST http://THIS_SITE/api/cart/del/{itemId}/email/{email}

#### Роуты работы с корзиной:
(с возможностью добавлять/удалять сразу множество товаров)
- GET https://THIS_SITE/api/products - возвращает список товаров весь что есть (там я создал 4 тестовых)  
- POST https://THIS_SITE/api/cart/add - добавляет указанный товар и возвращает актуальную корзину  
- POST https://THIS_SITE/api/cart/del - удаляет указанный товар и возвращает актуальную корзину  
  
у последних двух одинаковое тело запроса:  
```
{
   "userEmail":"test@email.com",
   "product":{
      "id":3,
      "count":1
   }
}
```
