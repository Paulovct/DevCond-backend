## Devcond backend

API para um gerenciador de condominios desenvolvida com Laravel 10.

## Requisitos
* PHP 8+
* Composer
* npm

## Instalação

Você pode clonar este repositório ou baixar o zip.

Ao descompactar, é necessário rodar o **composer** pra instalar as dependências e gerar o *autoload*.


Vá até a pasta do projeto, pelo *prompt/terminal* e execute:

> composer install

Depois é só aguardar.


## Configuração

As Configurações Iniciais e as Configurações de Banco de Dados e URL estão no arquivo:

>.env.example -> .env

É importante configurar corretamente as constantes Referentes ao Banco de Dados


## Uso

Após a Instalação das Depêndencias para rodar o projeto Localmente:
Vá até a pasta do projeto, pelo *prompt/terminal* e execute:

> php artisan serve


Se não houver algum erro durante a instalação o projeto irá rodar na URL:

>http://127.0.0.1:8000


As rotas deverão ser acessadas com o prefixo */api* .



## Migrations

Execute os seguintes comandos no Terminal.

>php artisan migrate

Para configurar o banco de dados.



