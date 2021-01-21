# SimpleRoutePhp

<center>
  
[Demo do Projeto](https://teste-route.herokuapp.com/teste/)

As rotas existentes no Demo do Projeto são:
<pre>
  GET     -   https://teste-route.herokuapp.com/teste/
  POST    -   https://teste-route.herokuapp.com/teste/
  PUT     -   https://teste-route.herokuapp.com/teste/{id}/{name}
  DELETE  -   https://teste-route.herokuapp.com/teste/{id}
</pre>

Onde GET é a url que a **Demo do projeto** mostra e os locais com <code> {}</code>  são parametros dinâmicos da url.
É possível testar essas rotas com softwares como <b>Postman e Insomnia</b> ou atraves de conexões providas por bibliiotecas, como por exemplo, AJAX do JQuery ou axios.

![GitHub](https://img.shields.io/github/license/EvelynGitHub/assets-readme)

</center>

# Sobre o Projeto:

Este projeto tem por objetivo prover um sistema de rotas simples para projetos PHP. É focado nas funções básicas que um sistema de rotas deveria ter, além mostrar erros de forma mais amigável ao desenvolver. Tudo isso para a implementação, uso e debug possa ser realizado de maneira rápida e fácil.

### Principais funções:

1 - Suporte para os verbos GET, POST, PUT, PATCH e DELETE;

2 - Execução de um função ou metodo de classe direto pelo SimpleRoutePhp;

3 - Função getError() com erros amigavél e mais expecíficos;


# Tecnologias utilizadas:

- PHP
- Composer

# Por onde começar:

## Back end

Pré-requisitos: PHP7, composer. 

Clone o repositório dentro da pasta que contém o backend do projeto.

```bash
# clonar repositório 
git clone https://github.com/EvelynGitHub/SimpleRoutePhp.git
```

Abra o arquivo <code>Route.php</code> dentro de SimpleRoutePhp e altere o namespace como achar necessário para ter compatibilidade com o composer.json, por exemplo:

Para um <code>composer.json</code> onde:
```json
  "autoload": {
        "psr-4": {
            "App\\": "src"
        }
    }
```
O <code>namespace</code> de Route.php deve ser:
```php
  namespace App\SimpleRoutePhp;
```

##IMPORTANTE
Para a execução correta do seu projeto junto com o SimpleRoutePhp, é importante ter na raiz de seu projeto um arquivo <code> .htaccess</code> com um conteúdo semelhante à:

```apache
  RewriteEngine On

  # Para o SimpleCrudPhp
  # ROUTER URL Rewrite
  RewriteCond %{SCRIPT_FILENAME} !-f
  RewriteCond %{SCRIPT_FILENAME} !-d
  RewriteRule ^(.*)$ index.php?route=/$1 [L,QSA]
```

Para o <code> .htaccess</code> acima, coloque um <code>index.php</code> em sua raiz também. Esse será o arquivo onde as rotas serão definidas.


# Como começar a usar:

Aqui pode temos um pequeno manual de como se usa o sistema para as configurações citadas anteriormente.

<details>
  <summary><b>Iniciando</b></summary>
  
  A <code>URL_BASE</code> contem o valor string da Url da sua aplicação, por exemplo, "localhost:8080/meu_projeto". É importante NÃO colocar o / no final dessa url.
  
  ```php
<?php

use App\SimpleRoutePhp\Route;

require __DIR__ . "/vendor/autoload.php";

$route = new Route(URL_BASE);

// Local onde ficarão as definições das rotas
// Exemplo com GET
$route->get("/", function () {
     echo "<h1>GET</h1>";
});

$route->dispatch();
```
  O metodo dispatch() é obrigatorio ser chamado para as rotas funcionarem.
  
</details>

<details>
  <summary><b>Criando Rotas</b></summary>
  Rotas que chamam um função diretamente.
  
 ```php
 $route->get("/", function () {
    echo "<h1>GET</h1>";
});

$route->post("/", function ($data) {
    echo json_encode(array(
        "data" => $data
    ));
});

$route->put("/{id}/{name}", function ($id, $name, $data) {
    echo json_encode(array(
        "id" => $id,
        "name" => $name,
        "data" => $data
    ));
});

$route->delete("/{id}", function ($id) {
    echo json_encode($id);
});

 
 ```
   Rotas que chamam um metodo de classe.
 ```php
$route->get("/", "Controller:index");

$route->post("/", "Controller:create");

$route->put("/{id}/{name}", "Controller:update");

$route->delete("/{id}", "Controller:delete");
```
 
 
</details>

<details>
  <summary><b>Usando Grupos</b></summary>
  
  Todas as rotas abaixo do <code>->group()</code> usarão ele como base, por isso, caso use mais de um gropo nas rotas, certifique-se que a rota referente a ele esta abaixo do <code>->group()</code> correspondente.
  
```php
  $route->group("teste");
```
  
</details>

<details>
  <summary><b>Usando namespace</b></summary>
  É importante colocar \\ de acordo com o arquivo de Classe que deseja chamar. 
  
  Todas as rotas abaixo do <code>->namespace()</code> usarão ele como base, por isso, caso use mais de um namespace nas rotas, certifique-se que a rota referente a ele esta abaixo do <code>->namespace()</code> correspondente.
  
```php
  $route->namespace("App\\Controller");
```
</details>

<details>
  <summary><b>Vizualizando erros</b></summary>
  
  Pode ser colocado logo abaixo do <code>$route->dispatch();</code>.
  
```php
  
$error = $route->getError();

if ($error["error"]) {
    echo json_encode($error);
}
```
  
</details>

# Autor(es)

**Evelyn Francisco Brandão**

https://www.linkedin.com/in/evelyn-brandão

**Rodrigo Yuri Veloso**

https://www.linkedin.com/in/rodrigo-yuri
github.com/rodrigo-yuri/
