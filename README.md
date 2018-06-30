# easy-route

## Requisitos
* PHP 7.0 >=
* Composer 

## Instalação
```bash
$ composer require easy-route/easy-route "v2.0"
``` 

## Definindo rotas
> Crie uma objeto do tipo EasyRoute e passe um namespace por parâmetro.
```php
use \EasyRoute\EasyRoute;

$route = new EasyRoute('\\Seu\\Namespace\\');
``` 

## Adicionando rotas
> Rotas do tipo **GET**
```php
$route->get('/', 'PessoaController.index');
```

> Rotas do tipo **POST**
```php
$route->post('/', 'PessoaController.index');
```

Abaixo de todas as rotas definidas, chame o método `on()` da classe EasyRoute para a execução das rotas.
```php
$route->on();
```

## Definindo rotas dinâmicas
```php
$route->get('/{exemplo}', function() {
    echo 'Exemplo de funcionamento de rota dinâmica';
});
```
## Trabalhando com callback
> Rota simples
```php
$route->get('/teste/{exemplo}', function() {
    echo 'Exemplo de funcionamento de rota dinâmica';
});
``` 
> Rota dinâmica
```php
Route::get('/home/{teste}', function() {
    echo "rota dinâmica";
});
```
## Obtendo dados da url
```php
$route->get('/teste/{exemplo}', function($dados) {
    var_dump($dados);
});
```
Onde `$dados` é um array de valores dinâmicos da url.
