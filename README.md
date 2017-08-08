# easy-route

## Requisitos
* PHP 7.0 >=
* Composer 

## Instalação
```bash
$ composer require easy-route/easy-route "v1.0"
``` 

## Definindo rotas
> Declare o namespace de rotas no arquivo desejado.
```php
use EasyRoute\Route;
``` 

## Adicionando rotas
> Rotas do tipo **GET**
```php
Route::get('/', 'HomeController.index');
```

> Rotas do tipo **POST**
```php
Route::post('/', 'HomeController.index');
```

Abaixo de todas as rotas definidas, adicionar a seguinte comando passando o namespace do seu controller:
```php
Route::on('\\Seu\Namespace\\');
```

Caso queira deixar seu arquivo de rotas separado, faça a inclusão do seu arquivo de rotas e depois insira os códigos abaixo:
```php
use EasyRoute\Bootstrap;

new Bootstrap('\\Seu\Namespace\\');
```

## Definindo rotas dinâmicas
```php
Route::get('/php/{word}/functions', 'Teste.index');
```
## Trabalhando com callback
> Rota simples
```php
Route::get('/teste', function () {
    echo "dados";
});
``` 
> Rota dinâmica
```php
Route::get('/home/{teste}', function () {
    echo "rota dinâmica";
});
```
## Obtendo dados da url
```php
Route::get('/home/{teste}', function ($teste) {
    var_dump($teste);
});
```
Onde `$teste` é um array de valores dinâmicos da url.
