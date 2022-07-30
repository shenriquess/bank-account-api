# bank-account-api

### API RESTful, desenvolvida em Laravel, que simula operações bancárias para diferentes tipos de moedas. 

- Laravel Framework 9.22.1
- PHP 8.1.8
- Composer 2.3.5
- MySQL 8.0.29

### **As operações consitem em**:
##### (1) Depósito
##### (2) Saque 
##### (3) Saldo.

### **As moedas empregadas são**:
##### (1) Dólar Autraliano - AUD
##### (2) Dólar Canadense - CAD
##### (3) Franco Suíço - CHF
##### (4) Coroa Dinamarquesa - DDK
##### (5) Euro - EUR
##### (6) Libra Esterlina - GBP
##### (7) Iene - JPY
##### (8) Coroa Norueguesa - NOK
##### (9) Coroa Sueca - SEK
##### (10) Dólar dos Estados Unidos - USD
##### (11) Real Brasileiro - BRL

### **Pré-requisitos**:

- PHP 8.1.8 ou superior
- Composer
- MySQL
- [Postman](https://www.postman.com/)

### **Orientações para execução**:

1. Crie um novo banco de dados para a aplicação.

2. Após baixar o repositório, altere o nome do arquivo _.env.example_ para _.env_ e insira as credenciais do banco de dados criado.
###### DB_CONNECTION=mysql
###### DB_HOST=127.0.0.1
###### DB_PORT=3306
###### DB_DATABASE=_your-database-name_
###### DB_USERNAME=_your-database-username_
###### DB_PASSWORD=_your-database-password_ 

3. Acesse a pasta **bank-account-project** do projeto e execute o comando abaixo para executar a migração das tabelas da aplicação para o banco de dados:

- php artisan migrate

4. Ainda dentro da pasta **bank-account-project**, execute o comando abaixo para iniciar o servidor local do Laravel:
- php artisan serve

5. Para testar os _endpoints_ sugere-se utilizar o [Postman](https://www.postman.com/). 

##### Os principais _endpoints_ estão listados abaixo: 

1. POST http://localhost:8000/api/operacao (Cadastra operações de depósitos e saques)
- id_conta: Número da conta para depósito
- tipo: (1) Depósito - (2) Saque
- moeda: Número que representa a moeda com a qual deseja-se fazer a operação (1 a 11)
- valor: Valor da operação

2. GET http://localhost:8000/api/conta/{id} (Retorna as informações de uma conta)

3. POST http://localhost:8000/api/conta (Cadastra uma nova conta)

4. GET http://localhost:8000/api/saldo/{id_conta}/{moeda?}  (Retorna o saldo de todas as moedas de uma conta ou de uma moeda específica)

##### Exemplo: Cadastro de uma operação de Saque:


