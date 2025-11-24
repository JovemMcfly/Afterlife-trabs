PROJETO FINAL: AFTERLIFE MUSIC HALL - SISTEMA FULLSTACK DE E-COMMERCE

Professor: Msc. Jeffson Celeiro Sousa

OBJETIVO GERAL:
Apresentar um sistema de comércio eletrônico completo e operacional, demonstrando a integração de todas as tecnologias exigidas (HTML5, CSS3, JavaScript, PHP e MySQL/PDO), incluindo as operações básicas de CRUD e o fluxo de compra/registro de pedidos.

---

I. STACK E ARQUITETURA

O sistema utiliza uma arquitetura Fullstack (SPA) com os seguintes componentes:

* Frontend (HTML/CSS/JS): Responsável pelo layout, dinamicidade, lógica de carrinho e comunicação assíncrona (Fetch API/AJAX).
* Backend (PHP): Responsável pela lógica de negócio e API de comunicação com o banco. Utiliza a classe PDO para conexão segura.
* Banco de Dados (MySQL): Gerencia duas tabelas principais: 'eventos' (catálogo de produtos) e 'pedidos' (registro de compras).

---

II. ARQUIVOS ESSENCIAIS

O projeto deve ser instalado em um servidor local (XAMPP/WAMP) dentro da pasta 'afterlife/' e contém os seguintes arquivos:

1.  index.php: Frontend principal, lógica JS de carrinho/roteamento e exibição de dados.
2.  api.php: Centraliza todas as operações de CRUD e registro de pedidos (backend PHP).
3.  conexao.php: Configuração da conexão segura ao MySQL usando PDO.
4.  banco.sql: Script para criação do DB 'afterlife_db', tabelas 'eventos' e 'pedidos'.

---

III. FUNCIONALIDADES CHAVE (CRUD E E-COMMERCE)

O sistema cumpre integralmente as orientações do trabalho final:

1.  CRUD Operacional:
    * Criação e Exclusão (Admin): A interface de administração permite cadastrar e remover eventos, atualizando o catálogo em tempo real.
    * Leitura (Agenda): O catálogo de shows é carregado diretamente da tabela 'eventos' do MySQL via requisição PHP.
2.  Segurança: Todas as interações com o banco de dados utilizam **Prepared Statements** em PHP (PDO), garantindo boas práticas de segurança contra SQL Injection.
3.  Fluxo de Compra e Registro de Pedidos:
    * O JavaScript gerencia o carrinho e o cálculo total.
    * Ao finalizar a compra, o sistema executa um `INSERT` na tabela **'pedidos'**, registrando a transação no banco de dados.

---

IV. INSTRUÇÕES DE EXECUÇÃO

1.  **SETUP:** Iniciar Apache e MySQL no XAMPP.
2.  **BD:** Criar o banco `afterlife_db` e executar o script `banco.sql`.
3.  **ACESSO:** Acessar o sistema via `http://localhost/afterlife/index.php`.
4.  **TESTE:** Testar as abas **Admin** (para CRUD) e **Carrinho** (para registro de pedidos) para validar a integração com o banco.
