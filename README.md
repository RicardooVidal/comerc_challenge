# Pastelaria - Backend Challenge

## Índice
1. [Visão Geral](#visão-geral)
2. [Tecnologias Utilizadas](#tecnologias-utilizadas)
3. [Arquitetura do Sistema](#arquitetura-do-sistema)
4. [Instalação](#instalação)
5. [Autenticação](#autenticação)
6. [Testes](#testes)
7. [Endpoints](#endpoints)

---

## Visão Geral
Este projeto é um sistema desenvolvido para o desafio da Comerc, no qual simula um sistema para uma pastelaria. Permitindo que estes façam manutenções em cadastro de clientes, de produtos e pedidos deste projeto.

## Tecnologias Utilizadas
- **PHP 8.2**
- **Laravel 11**
- **MySQL 8.0**
- **Composer**
- **PHPUnit**

## Arquitetura do Sistema
O projeto foi desenvolvido utilizando os princípios DDD (Domain-Driven Design) mantendo cada domínio completo e separado em camada. Em sua arquitetura, o domínio de clientes, de produtos e pedidos são separados em suas respectivas camadas, cada uma com suas respectivas regras de negócio e persistência.

## Instalação

### Requisitos
- **PHP >= ^8.2**
- **MySQL >= 8.0**
- **Composer**
- **Docker**
- **docker-compose**

### Passo a Passo

1. **Clone o repositório**:
   ```bash
   git clone git@github.com:RicardooVidal/pastelaria-api.git
   cd pastelaria-api

2. **Buildar as imagens e subir os containers com docker compose**:  
   ```bash
   docker compose build
   docker compose up -d

3. **Executar migrations**:  
   ```bash
    docker compose exec app_challenge php artisan migrate

4. **Executar seeders**:  
   ```bash
    docker compose exec app_challenge php artisan db:seed

5. **Autenticação**:  
   Este projeto utiliza Laravel Sanctum para autenticação. Após o [login](http://localhost:8085/api-doc#/Login/post_api_login), copiar o token gerado e utilizar no header **Authorization** antecipado por "Bearer". Exemplo: **"Bearer 2|tEIYo01732uizYM50..."**.

   No Swagger utilize o botão **"Authorize"** para usar o token gerado e para ter acesso as rotas protegidas.

   Usuário para teste (somente após rodar o comando db:seed):
   ```bash
    E-mail: "test@test.com"
    Senha: "123456"

### Testes

1. **Sobre os testes:**  
   Os testes foram desenvolvidos utilizando o framework [PHPUnit](https://phpunit.readthedocs.io/en/latest/). E em casos mais complexos, foram utilizados [Mockery](https://github.com/mockery/mockery) como o OrderServiceTest.

   Foram desenvolvidos testes unitários que verificam toda a funcionalidade das principais rotinas da API sem nenhuma integração com banco de dados ou qualquer outro serviço externo. E para testar a integração com o banco de dados, foram desenvolvidos testes de integração.

   Todos os testes implementam o padrão AAA (Arrange, Act, Assert).

2. **Para executar os testes:**:  
   ```bash
    docker compose app_challenge php artisan test 
    ou php artisan test

3. **Para executar um teste em específico:**  
   ```bash
    docker compose app_challenge php artisan test --filter=test_create_order
    ou php artisan test --filter=test_create_order

### Email
   Há a possibilidade de configurar um servidor de email no arquivo **config/mail.php** e utilizar o service de envio de emails do Laravel **Mail::send()**. 

   Um email é disparado a cada pedido criado, conforme o código abaixo:

   ```php

    Mail::send(new OrderCreated($order));

   ```
   No entanto, por padrão foi implementado o driver padrão do Laravel **log::info()** para o envio de emails. Portanto, caso o serviço de email não seja configurado, os emails não serão enviados.

### Fotos
    No cadastro de produtos há a possibilidade de cadastrar uma foto para o mesmo, utilizando o campo **photo** no modelo **Product**. A imagem será armazenada no diretório **public/produtos** e o campo **photo** será atualizado com o nome da imagem.

### Endpoints
Todos os endpoints estão documentados no [Swagger](http://localhost:8085/api-doc)