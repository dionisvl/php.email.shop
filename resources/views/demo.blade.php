<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
</head>
<body>

<label for="email">Ваш емайл</label>
<input type="email" value="test@test.ru" id="email" name="email">

<br>
<button id="productsLoad" onclick="productsLoad()">Подгрузить товары</button>
<br>
<br>
<div>Товары тут:</div>
<div id="products" style="border: 1px solid magenta"></div>
<br>
<br>
<div>Ваша корзина тут:</div>
<div id="cart" style="border: 1px solid green"></div>
<span>Итого на сумму: </span>
<div id="total"></div>

<script>
    //Метод для загрузки каталога из сервера
    function productsLoad() {
        let response = request(window.location.origin + '/api/products');

        response
            .then(data => {
                console.log(data.status);
                if (data.status === 'ok') {
                    let items = data.data;

                    Object.keys(items).forEach(key => {
                        console.log(items[key]);
                    });

                    let totalItems = '';
                    for (let itemId in items) {
                        let id = items[itemId].id;
                        let itemPrice = new Intl.NumberFormat('ru-RU').format(items[itemId].price);
                        let itemTitle = items[itemId].title;
                        let itemImg = items[itemId].image;
                        totalItems += `
<div style="border: 1px solid mediumseagreen">
    <span>Название</span><div>${itemTitle}</div>
    <br>
    <span>Цена</span><div>${itemPrice}</div>
    <br>
    <button data-id="${id}" onclick='Cart.add(this,event)'>Добавить в корзину</button>
</div>`;
                    }

                    let products = document.getElementById('products');
                    products.innerHTML = totalItems;


                } else if (data.status === 'error') {
                    console.log(data)
                } else {
                    console.log(data)
                }
            })
    }


    let Cart = class Cart {
        //Добавление товара в корзину
        static add(element, event) {
            event.preventDefault();
            let email = document.getElementById('email').value;
            if (email === '') {
                alert('fill EMAIL field before!');
                return false;
            }

            let itemId = element.dataset.id;
            console.log(itemId);
            let response = request(window.location.origin + '/api/cart/add/' + itemId + '/email/' + email, 'POST', {})

            response
                .then(data => {
                    if (data.status === 'ok') {
                        this.fillCart(data);
                    } else if (data.status === 'error') {
                        console.log(data)
                    } else {
                        console.log(data)
                    }
                })
        }

        //Удаление товара из корзины
        static del(element, event) {
            event.preventDefault();
            let email = document.getElementById('email').value;
            if (email === '') {
                alert('fill EMAIL field before!');
                return false;
            }

            let itemId = element.dataset.id;
            console.log(itemId);
            let response = request(window.location.origin + '/api/cart/del/' + itemId + '/email/' + email, 'POST', {})

            response
                .then(data => {
                    if (data.status === 'ok') {
                        this.fillCart(data);
                    } else if (data.status === 'error') {
                        console.log(data)
                    } else {
                        console.log(data)
                    }
                })
        }


        //Общий метод для заполнения корзины ответом с сервера
        static fillCart(data) {
            console.log(data);
            let items = JSON.parse(data.contents_json);
            console.log(items);
            let totalItems = '';
            for (let itemId in items) {
                let count = items[itemId].count;
                let item = items[itemId].data;
                let id = item.id;
                let itemPrice = new Intl.NumberFormat('ru-RU').format(item.price);
                let itemTitle = item.title;
                totalItems += `
<div style="border: 1px solid aquamarine">
    <span>Название</span><div>${itemTitle}</div>
    <br>
    <span>Цена</span><div>${itemPrice}</div>
    <br>
    <span>Кол-во</span><div>${count}</div>
    <br>
    <button data-id="${id}" onclick='Cart.del(this,event)'>Убрать из корзины</button>
</div>`;
            }

            let cart = document.getElementById('cart');
            cart.innerHTML = totalItems;

            let total = document.getElementById('total');
            total.innerHTML = new Intl.NumberFormat('ru-RU').format(data.total_price);
        }

    };


    function request(url, method, payload) {
        return fetch(url, {
            method: method,
            body: JSON.stringify(payload),
            headers: new Headers({
                'Accept': 'application/json',
                'Content-type': 'application/json',
                //'X-CSRF-Token': document.querySelector("meta[name='_token']").getAttribute('content')
            })
        })
            .then(r => r.json())
            .catch(error => console.error(error))
    }
</script>
</body>
</html>
