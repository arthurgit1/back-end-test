O sistema pode ser "rodado" de duas formas:
    1. Usando o XAMPP para simular o ambiente;
    2. Usando a extensão "Live server" no proprio VSCode.
        a.Rodando o index.html você ira para a pagina de um formulario de cadastro de usuario
        b.Esse formulario tem conexões com banco de dados e github, onde você tem a opção de logar com o github
            .Há a conexão com a API do git

obs. Optei por não separar os arquivos em pastas


-> Codigo para criação do banco de dados MySQL
    
    -- Criar DB
    CREATE DATABASE db_test;
    -- Usar o banco de dados
    USE db_test;

    -- Criar a tabela
    CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(30) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(30) NOT NULL,
        lastName VARCHAR(30),
        profileImageUrl VARCHAR(255),
        bio VARCHAR(30),
        gender ENUM('Male', 'Female', 'Not Specified') DEFAULT 'Not Specified'
    );


    

    