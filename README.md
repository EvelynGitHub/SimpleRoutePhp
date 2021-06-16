# SimpleRoutePhp


  
[Demo do Projeto](https://teste-route.herokuapp.com/teste/)

As rotas existentes no Demo do Projeto são:
<pre>
  GET     -   https://teste-route.herokuapp.com/teste/
  POST    -   https://teste-route.herokuapp.com/teste/
  PUT     -   https://teste-route.herokuapp.com/teste/{id}/{name}
  DELETE  -   https://teste-route.herokuapp.com/teste/{id}
</pre>

Onde GET é a url que a **Demo do projeto** mostra e os locais com <code> {}</code>  são parâmetros dinâmicos da url.
É possível testar essas rotas com softwares como <b>Postman e Insomnia</b> ou através de conexões providas por bibliotecas, como por exemplo, AJAX do JQuery ou axios.

<p style="font-size:12px;"> <b>Obs.:</b> A demo refere-se a uma versão mais antiga do SimpleRoutePhp. </p>

<center>

![GitHub](https://img.shields.io/github/license/EvelynGitHub/assets-readme)

</center>

# Sobre o Projeto:

Este projeto tem por objetivo prover um sistema de rotas simples para projetos PHP. É focado nas funções básicas que um sistema de rotas deveria ter, como GET, POST, PUT e DELETE. Tudo isso para que a implementação e uso possa ser realizado de maneira rápida e fácil.

## Principais funções:

1. Suporte para os verbos GET, POST, PUT, PATCH e DELETE;

1. Execução de um função ou método de classe direto pelo SimpleRoutePhp;

1. Função <code>group()</code> para agrupar suas URLs com base num pre fixo comum;


# Tecnologias utilizadas:

- PHP 7^
- Composer

# Por onde começar:

Para fins de exemplo a seguinte estrutura será usada como base, entretanto nada impede de modificar como achar melhor, bastando apenas ter atenção aos caminhos dos arquivos:

```
> modules
|   > SimpleRoutePhp
> public
|   index.php
> src
|   > Controller
|   |   Teste.php
> vendor
  .htaccess
  composer.json
  env.php
```

## Back end

Pré-requisitos: PHP ^7 ou ^8.0 e composer. 

Clone o repositório dentro da pasta que contém o backend do projeto (no nosso exemplo, pasta `modules`).

```bash
# clonar repositório 
git clone https://github.com/EvelynGitHub/SimpleRoutePhp.git
```

Abra o arquivo <code>composer.json</code> do seu projeto para adicionar o SimpleRoutePhp, modificando da seguinte forma:
```json
  {
    "name": "...",
    "authors": [
        ...
    ],
    "repositories": [
        {
            "type": "path",
            "url": "./modules/SimpleRoutePhp"
        }
    ],
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        },
        "files": [
            "./env.php"   
        ]
    },
    "require": {
        "simplephp/simple-route": "dev-main"
    }
}
```

**OBS.:** Atente-se para a chave "repositories" e "require", isso pode mudar de acordo com a versão e local onde clonou o SimpleRoutePhp.

### **IMPORTANTE**
Para a execução correta do seu projeto junto com o SimpleRoutePhp, é importante ter na raiz de seu projeto um arquivo <code> .htaccess</code>. No exemplo usado de base o conteúdo é semelhante à:

```apache
  RewriteEngine On
  RewriteBase /

  RewriteCond %{SCRIPT_FILENAME} !-f
  RewriteCond %{SCRIPT_FILENAME} !-d

  RewriteCond %{THE_REQUEST} public/([^\s?]*) [NC]
  RewriteRule ^ $1 [L,NE,R=302]
  RewriteRule ^((?!public/).*)$ seu_projeto/public/index.php?route=/$1 [L,NC]
```

Para o <code> .htaccess</code> acima, coloque um <code>index.php</code> em sua raiz também. Esse será o arquivo onde as rotas serão definidas. No exemplo o <code> .htaccess</code> aponta para o index.php dentro de "public"


# Como começar a usar:

Aqui pode temos um pequeno manual de como se usa o sistema para as configurações citadas anteriormente.

<details>
  <summary><b>Iniciando</b></summary>
  
  A <code>URL_BASE</code> contem o valor string da Url da sua aplicação, por exemplo, "localhost:8080/meu_projeto". É importante NÃO colocar o / no final dessa url.
  
  ```php
<?php

use SimplePhp\SimpleRoute\Route;

require __DIR__ . "/vendor/autoload.php";

$route = new Route(URL_BASE);

// Local onde ficarão as definições das rotas
// Exemplo com GET
$route->get("/", function () {
     echo "<h1>GET</h1>";
});

$route->execute();
```
  O método execute() é obrigatório ser chamado ao final das rotas para que elas funcionem.
  
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
   Rotas que chamam um método de classe.
 ```php
$route->get("/", "Controller:index");

$route->post("/", "Controller:create");

$route->put("/{id}/{name}", "Controller:update");

$route->delete("/{id}", "Controller:delete");
```
 
 
</details>

<details>
  <summary><b>Usando Grupos</b></summary>
  
  Todas as rotas abaixo do <code>->group()</code> usarão ele como base, por isso, caso use mais de um grupo nas rotas, certifique-se que a rota referente a ele esta abaixo do <code>->group()</code> correspondente.
  
```php
  //URL_BASE/teste
  $route->group("teste");

  //URL_BASE/teste/produto
  $route->group("teste/produto");

  //URL_BASE/caixa
  $route->group("caixa");

  //URL_BASE/caixa/teste
  $route->group("caix/teste");
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

___

# Autor(es)

**Evelyn Francisco Brandão**

https://www.linkedin.com/in/evelyn-brandão

**Rodrigo Yuri Veloso**

https://www.linkedin.com/in/rodrigo-yuri

https://github.com/rodrigo-yuri/
