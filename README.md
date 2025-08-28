# Sistema Guarda-Volumes

Um sistema web simples desenvolvido em PHP para gerenciar a entrada e saída de itens em um serviço de guarda-volumes. A aplicação permite registrar novos volumes, gerar recibos e consultar os detalhes dos itens armazenados.

## Tecnologias Utilizadas

*   **Backend:** PHP
*   **Servidor:** Apache (via XAMPP)
*   **Banco de Dados:** MySQL/MariaDB (gerenciado com phpMyAdmin)
*   **Frontend:** HTML/CSS

## Estrutura do Projeto

```
/
├── assets/         # Arquivos estáticos como CSS
│   └── css/
│       └── style.css
├── core/           # Lógica principal da aplicação
│   ├── db.php      # Configuração da conexão com o banco de dados
│   ├── processa.php# Script para processar os dados dos formulários
│   └── setup.php   # Script para configuração inicial do banco de dados
├── views/          # Arquivos de visualização (frontend)
│   ├── detalhes_volume.php
│   ├── entrada.php
│   └── recibo.php
└── index.php       # Página inicial da aplicação
```

## Pré-requisitos

Antes de começar, você precisará ter o [XAMPP](https://www.apachefriends.org/pt_br/index.html) instalado em sua máquina, que inclui Apache, MySQL e PHP.

## Como Iniciar (Guia de Instalação)

Siga os passos abaixo para configurar e executar o projeto em seu ambiente local.

### 1. Clone o Repositório

Primeiro, clone este repositório para o diretório `htdocs` da sua instalação do XAMPP.

```bash
# Navegue até a pasta htdocs do seu XAMPP
cd C:/xampp/htdocs/

# Clone o repositório (substitua pela URL do seu repositório no GitHub)
git clone https://github.com/seu-usuario/seu-repositorio.git guardaVolume
```

Se você não estiver usando Git, pode simplesmente baixar o código e colocar a pasta do projeto dentro de `C:/xampp/htdocs/`.

### 2. Inicie o XAMPP

Abra o painel de controle do XAMPP e inicie os módulos **Apache** e **MySQL**.

### 3. Crie o Banco de Dados

1.  Abra seu navegador e acesse `http://localhost/phpmyadmin/`.
2.  Clique em **"Novo"** no menu lateral esquerdo para criar um novo banco de dados.
3.  Dê ao banco de dados o nome de `guarda_volume` e clique em **"Criar"**.

### 4. Configure a Estrutura do Banco

O projeto contém um script para criar a tabela necessária automaticamente.

1.  Acesse o seguinte URL no seu navegador:
    `http://localhost/guardaVolume/core/setup.php`
2.  Isso executará o script que cria a tabela `volumes` no banco de dados `guarda_volume`. Você deverá ver uma mensagem de sucesso.

### 5. Configure a Conexão com o Banco

O arquivo `core/db.php` contém as credenciais de acesso ao banco de dados. Por padrão, o XAMPP usa o usuário `root` sem senha. O arquivo já deve estar configurado corretamente para este ambiente, mas caso precise, verifique se ele está assim:

```php
<?php
// core/db.php

$host = 'localhost';
$dbname = 'guarda_volume';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
```

### 6. Acesse a Aplicação

Pronto! Agora você pode acessar a aplicação principal em seu navegador através do link:
`http://localhost/guardaVolume/`

## Uso

*   **Página Inicial (`index.php`):** Lista todos os volumes atualmente armazenados.
*   **Registrar Entrada (`views/entrada.php`):** Formulário para registrar um novo item, informando dados do proprietário e descrição do volume.
*   **Gerar Recibo (`views/recibo.php`):** Após o registro, um recibo com um código único é gerado para o cliente.
*   **Ver Detalhes (`views/detalhes_volume.php`):** Permite visualizar as informações de um volume específico.
