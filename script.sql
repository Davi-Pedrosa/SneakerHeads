-- create database sneacker_heads
-- Arquivo: criar_banco_de_dados.sql

-- -----------------------------------------------------
-- Schema LojaOnline
-- -----------------------------------------------------
-- DROP DATABASE IF EXISTS LojaOnline; -- Descomente se quiser recriar o banco de dados do zero
-- CREATE DATABASE IF NOT EXISTS LojaOnline;
-- USE LojaOnline; -- Para MySQL, use esta linha para selecionar o banco de dados

-- No PostgreSQL, você criaria as tabelas dentro de um schema ou no schema público

-- -----------------------------------------------------
-- Table `Categorias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Categorias (
    id_categoria SERIAL PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao TEXT
);

-- -----------------------------------------------------
-- Table `Produtos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Produtos (
    id_produto SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    preco DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL,
    imagem_url VARCHAR(255),
    id_categoria INT NOT NULL,
    CONSTRAINT fk_produto_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES Categorias (id_categoria)
        ON DELETE RESTRICT -- Ou ON DELETE CASCADE, dependendo da sua política
        ON UPDATE CASCADE
);

-- -----------------------------------------------------
-- Table `Usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Usuarios (
    id_usuario SERIAL PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telefone VARCHAR(20),
    tipo_usuario VARCHAR(50) NOT NULL, -- Ex: 'Cliente', 'Admin'
    data_nascimento DATE, -- Aplicável apenas para 'Admin' no seu exemplo, mas pode ser nullable
    senha_hash VARCHAR(255) -- Para armazenar a senha criptografada
);

-- -----------------------------------------------------
-- Table `Enderecos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS Enderecos (
    id_endereco SERIAL PRIMARY KEY,
    rua VARCHAR(255) NOT NULL,
    bairro VARCHAR(100) NOT NULL,
    numero VARCHAR(10) NOT NULL,
    id_usuario INT NOT NULL,
    CONSTRAINT fk_endereco_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES Usuarios (id_usuario)
        ON DELETE CASCADE -- Se o usuário for deletado, o endereço também
        ON UPDATE CASCADE
);

-- -----------------------------------------------------
-- Inserção de Dados (Exemplo baseado nos seus JSONs)
-- Esta parte pode ser executada após a criação das tabelas
-- -----------------------------------------------------

-- Inserir Categorias
INSERT INTO Categorias (nome, descricao) VALUES
('Esportivo', 'Tênis focados em desempenho, ideais para atividades físicas.'),
('Casual', 'Tênis para uso diário, confortável e estiloso.');

-- Inserir Produtos
INSERT INTO Produtos (nome, descricao, preco, status, imagem_url, id_categoria) VALUES
('Nike Air Max 270', 'Tênis de corrida com design moderno.', 499.99, 'disponível', 'https://via.placeholder.com/150', (SELECT id_categoria FROM Categorias WHERE nome = 'Esportivo'));

-- Inserir Usuários
INSERT INTO Usuarios (nome, email, telefone, tipo_usuario, senha_hash) VALUES
('Davi', 'davi@gmail.com', '19909009999', 'Cliente', 'hash_da_senha_davi'); -- Substitua 'hash_da_senha_davi' por um hash real

INSERT INTO Usuarios (nome, email, telefone, tipo_usuario, data_nascimento, senha_hash) VALUES
('PedroAdm', 'pedroadm@gmail.com', '19909009999', 'Admin', '2003-07-29', 'pedro1234_hash'); -- Substitua 'pedro1234_hash' por um hash real

-- Inserir Endereços
INSERT INTO Enderecos (rua, bairro, numero, id_usuario) VALUES
('Rua das Flores', 'Centro', '123', (SELECT id_usuario FROM Usuarios WHERE email = 'davi@gmail.com'));

INSERT INTO Enderecos (rua, bairro, numero, id_usuario) VALUES
('Rua Admin', 'Admin Bairro', '789', (SELECT id_usuario FROM Usuarios WHERE email = 'pedroadm@gmail.com'));

