# easy-route

## Requisitos
* PHP 7.0 >=
* Composer 

## Instalação
```json
composer require easy-route/easy-route "v1.0"
``` 

## Definindo rotas
> Declare o namespace de rotas no arquivo desejado.
```json
use EasyRoute\Route;
``` 

## Adicionando rotas
> Rotas do tipo **GET**
```json
Route::get('/', 'HomeController.index');
```

> Rotas do tipo **POST**
```json
Route::post('/', 'HomeController.index');
```

Abaixo de todas as rotas definidas, adicionar a seguinte comando passando o namespace do seu controller:
```json
Route::on('\\Seu\Namespace\\');
```

Caso queira deixar seu arquivo de rotas separado, faça a inclusão do seu arquivo de rotas e depois insira os códigos abaixo:
```json
use EasyRoute\Bootstrap;

new Bootstrap('\\Seu\Namespace\\');
```

## Definindo rotas dinâmicas
```json
Route::get('/php/{word}/functions', 'Teste.index');
```
## Trabalhando com callback
> Rota simples
```json
Route::get('/teste', function () {
    echo "dados";
});
``` 
> Rota dinâmica
```json
Route::get('/home/{teste}', function () {
    echo "rota dinâmica";
});
```
## Obtendo dados da url
```json
Route::get('/home/{teste}', function ($teste) {
    var_dump($teste);
});
```
Onde $teste é um array de valores dinâmicos da url.