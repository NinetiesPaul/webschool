DROP TABLE IF EXISTS disciplina;
CREATE TABLE disciplina(
    id SERIAL PRIMARY KEY,
    nome VARCHAR NOT NULL
);

DROP TABLE IF EXISTS turma;
CREATE TABLE turma(
    id SERIAL PRIMARY KEY,
    serie INT NOT NULL,
    nome VARCHAR
);

DROP TABLE IF EXISTS estado;
CREATE TABLE estado (
    id SERIAL PRIMARY KEY,
    nome VARCHAR NOT NULL,
    sigla VARCHAR NOT NULL
);

DROP TABLE IF EXISTS endereco;
CREATE TABLE endereco (
    id SERIAL PRIMARY KEY,
    bairro VARCHAR,
    cep VARCHAR,
    cidade VARCHAR,
    complemento VARCHAR,
    numero INT,
    rua VARCHAR,
    estado INTEGER,
    FOREIGN KEY (estado) REFERENCES estado(id)
);

DROP TABLE IF EXISTS usuario;
CREATE TABLE usuario(
    id SERIAL PRIMARY KEY,
    nome VARCHAR NOT NULL,
    email VARCHAR NOT NULL,
    pass VARCHAR NOT NULL,
    telefone1 VARCHAR,
    telefone2 VARCHAR,
    endereco INT,
    salt VARCHAR,
    is_deleted BOOL,
    FOREIGN KEY (endereco) REFERENCES endereco(id) 
);

DROP TABLE IF EXISTS professor;
CREATE TABLE professor(
    id SERIAL PRIMARY KEY,
    usuario INT,
    FOREIGN KEY (usuario) REFERENCES usuario(id) 
);

DROP TABLE IF EXISTS responsavel;
CREATE TABLE responsavel(
    id SERIAL PRIMARY KEY,
    usuario INT,
    FOREIGN KEY (usuario) REFERENCES usuario(id)
);

DROP TABLE IF EXISTS admin;
CREATE TABLE admin(
    id SERIAL PRIMARY KEY,
    usuario INT,
    FOREIGN KEY (id) REFERENCES usuario(id)
);

DROP TABLE IF EXISTS aluno;
CREATE TABLE aluno(
    id SERIAL PRIMARY KEY,
    usuario INT,
    turma INT,
    FOREIGN KEY (usuario) REFERENCES usuario(id),
    FOREIGN KEY (turma) REFERENCES turma(id)
);

DROP TABLE IF EXISTS disciplina_por_professor;
CREATE TABLE disciplina_por_professor(
    id SERIAL PRIMARY KEY,
    professor INT,
    disciplina INT,
    turma INT,
    FOREIGN KEY (professor) REFERENCES professor(id),
    FOREIGN KEY (disciplina) REFERENCES disciplina(id),
    FOREIGN KEY (turma ) REFERENCES turma(id)
);

DROP TABLE IF EXISTS responsavel_por_aluno;
CREATE TABLE responsavel_por_aluno(
    id SERIAL PRIMARY KEY,
    responsavel INT,
    aluno INT,
    FOREIGN KEY (responsavel) REFERENCES responsavel(id),
    FOREIGN KEY (aluno) REFERENCES aluno(id)
);

DROP TABLE IF EXISTS nota_por_aluno;
CREATE TABLE nota_por_aluno(
    id SERIAL PRIMARY KEY,
    aluno INT,
    disciplina INT,
    turma INT,
    nota1 FLOAT,
    nota2 FLOAT,
    nota3 FLOAT,
    nota4 FLOAT,
    rec1 FLOAT,
    rec2 FLOAT,
    rec3 FLOAT,
    rec4 FLOAT,
    FOREIGN KEY (aluno) REFERENCES aluno(id),
    FOREIGN KEY (disciplina) REFERENCES disciplina(id),
    FOREIGN KEY (turma) REFERENCES turma(id)
);

DROP TABLE IF EXISTS diario_de_classe;
CREATE TABLE diario_de_classe (
    id SERIAL PRIMARY KEY,
    aluno INT,
    disciplina INT,
    turma INT,
    data DATE,
    contexto VARCHAR, -- nota ou observacao
    presenca BOOL NULL,
    professor INT NULL,
    observacao VARCHAR NULL,
    FOREIGN KEY (aluno) REFERENCES aluno(id),
    FOREIGN KEY (disciplina) REFERENCES disciplina(id),
    FOREIGN KEY (turma) REFERENCES turma(id),
    FOREIGN KEY (professor) REFERENCES professor(id)
);

DROP TABLE IF EXISTS fotos_de_avatar;
CREATE TABLE fotos_de_avatar (
    id SERIAL PRIMARY KEY,
    endereco_thumb VARCHAR,
    endereco VARCHAR,
    usuario INT,
    FOREIGN KEY (usuario) REFERENCES usuario(id)
);

DROP TABLE IF EXISTS arquivos;
CREATE TABLE arquivos (
    id SERIAL PRIMARY KEY,
    nome VARCHAR,
    endereco_thumb VARCHAR,
    endereco VARCHAR,
    contexto VARCHAR,
    diario INT,
    conversa INT,
    descricao VARCHAR (255),
    DATA DATE,
    FOREIGN KEY (diario) REFERENCES diario_de_classe(id)
);