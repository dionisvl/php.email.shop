<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
          integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-12 ">
            <blockquote class="blockquote">
                <p class="mb-0">Hello! It is demo version of fronted for use e-commerce cart API.</p>
                <ul>
                    <li>First, you can press button "Load products to catalogue"</li>
                    <li>Second, you can press button "Load contents of cart"</li>
                </ul>
                <footer class="blockquote-footer">Fill e-mail field is <cite title="Source Title">Required</cite>
                </footer>
            </blockquote>


            <label for="email">Your e-mail: </label>
            <input type="email" value="test@test.ru" id="email" name="email">
            <br>
            <h2>Products:</h2>
            <button class='btn btn-primary' id="productsLoad" onclick="productsLoad()">Load products to catalogue
            </button>
            <div id="products" style="border: 1px solid magenta"></div>
            <br>
            <h2>Your cart content:</h2>
            <button class='btn btn-primary' id="productsLoad" onclick="Cart.load()">Load contents of cart</button>
            <div id="cart" style="border: 1px solid green"></div>
            <span>Total: </span><span id="total"></span>
        </div>
    </div>

</div>


<script>
    const THIS_SITE = window.location.origin;

    //Метод для загрузки каталога из сервера
    function productsLoad() {
        let response = request(window.location.origin + '/api/products');

        response
            .then(data => {
                // console.log(data.status);
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
    <span>Title: </span><span>${itemTitle}</span>
    <br>
    <span>Price: </span><span>${itemPrice}</span>
    <button data-id="${id}" onclick='Cart.add(this,event)'>Add to cart</button>
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
        //Подгрузка товаров в корзину
        static load() {
            let email = Cart.getEmail();
            let response = request(THIS_SITE + '/api/cart/get?email=' + email, 'GET', null)

            Cart.processResponse(response);
        }

        //Добавление товара в корзину
        static add(element, event) {
            event.preventDefault();
            let email = Cart.getEmail();

            let itemId = element.dataset.id;
            // console.log(itemId);
            let response = request(THIS_SITE + '/api/cart/add/' + itemId + '/email/' + email, 'POST', {})

            Cart.processResponse(response);
        }

        //Удаление товара из корзины
        static del(element, event) {
            event.preventDefault();
            let email = Cart.getEmail();

            let itemId = element.dataset.id;
            // console.log(itemId);
            let response = request(THIS_SITE + '/api/cart/del/' + itemId + '/email/' + email, 'POST', {})

            Cart.processResponse(response);
        }


        //Общий метод для заполнения корзины ответом с сервера
        static fillCart(data) {
            let items = data.items;
            console.log(items);
            let totalItems = '';
            for (let itemId in items) {
                let id = items[itemId].id;
                let itemPrice = new Intl.NumberFormat('ru-RU').format(items[itemId].price);
                let itemTitle = items[itemId].title;
                let count = items[itemId].count;
                totalItems += `
<div style="border: 1px solid aquamarine">
    <span>Title: </span><span>${itemTitle}</span>
    <br>
    <span>Price: </span><span>${itemPrice}</span>
    <br>
    <span>Count: </span><span>${count}</span>
    <button data-id="${id}" onclick='Cart.del(this,event)'>Remove from cart</button>
</div>`;
            }

            let cart = document.getElementById('cart');
            cart.innerHTML = totalItems;

            let total = document.getElementById('total');
            total.innerHTML = new Intl.NumberFormat('ru-RU').format(data.totalPrice);
        }

        static processResponse(response) {
            response
                .then(data => {
                    console.log(data);
                    if (data.status === 'ok') {
                        this.fillCart(data);
                    } else if (data.status === 'error') {
                        console.log(data)
                    } else {
                        console.log(data)
                    }
                })
        }

        static getEmail() {
            let email = document.getElementById('email').value;
            if (email === '') {
                alert('fill EMAIL field before!');
                window.stop();
            }
            return email;
        }
    };

    //universal GET/POST request method
    function request(url, method, payload) {
        console.log(url);
        let params = {
            method: method,
        };
        if (payload !== null) {
            let postParams = {
                body: JSON.stringify(payload),
                headers: new Headers({
                    'Accept': 'application/json',
                    'Content-type': 'application/json',
                    //'X-CSRF-Token': document.querySelector("meta[name='_token']").getAttribute('content')
                })
            }
            params = {...params, ...postParams};
        }

        return fetch(url, params)
            .then(r => r.json())
            .catch(error => console.error(error))
    }
</script>
</body>
</html>
