CREATE TABLE disciplina(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);

CREATE TABLE turma(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    serie INT(11) NOT NULL,
    nome VARCHAR(255)
);

CREATE TABLE estado (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    sigla VARCHAR(255) NOT NULL
);

CREATE TABLE endereco (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    bairro VARCHAR(255),
    cep VARCHAR(255),
    cidade VARCHAR(255),
    complemento VARCHAR(255),
    numero INT(11),
    rua VARCHAR(255),
    estado INTEGER,
    FOREIGN KEY (estado) REFERENCES estado(id)
);

CREATE TABLE usuario(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    pass VARCHAR(255) NOT NULL,
    telefone1 VARCHAR(255),
    telefone2 VARCHAR(255),
    endereco INT(11),
    salt VARCHAR(255),
    is_deleted BOOL,
    FOREIGN KEY (endereco) REFERENCES endereco(id) 
);

CREATE TABLE professor(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    usuario INT(11),
    FOREIGN KEY (usuario) REFERENCES usuario(id) 
);

CREATE TABLE responsavel(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    usuario INT(11),
    FOREIGN KEY (usuario) REFERENCES usuario(id)
);

CREATE TABLE admin(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    usuario INT(11),
    FOREIGN KEY (id) REFERENCES usuario(id)
);

CREATE TABLE aluno(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    usuario INT(11),
    turma INT(11),
    FOREIGN KEY (usuario) REFERENCES usuario(id),
    FOREIGN KEY (turma) REFERENCES turma(id)
);

CREATE TABLE disciplina_por_professor(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    professor INT(11),
    disciplina INT(11),
    turma INT(11),
    FOREIGN KEY (professor) REFERENCES professor(id),
    FOREIGN KEY (disciplina) REFERENCES disciplina(id),
    FOREIGN KEY (turma ) REFERENCES turma(id)
);

CREATE TABLE responsavel_por_aluno(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    responsavel INT(11),
    aluno INT(11),
    FOREIGN KEY (responsavel) REFERENCES responsavel(id),
    FOREIGN KEY (aluno) REFERENCES aluno(id)
);

CREATE TABLE nota_por_aluno(
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    aluno INT(11),
    disciplina INT(11),
    turma INT(11),
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

CREATE TABLE diario_de_classe (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    aluno INT(11),
    disciplina INT(11),
    turma INT(11),
    data DATE,
    contexto VARCHAR(255), -- nota ou observacao
    presenca BOOL NULL,
    professor INT(11) NULL,
    observacao VARCHAR(255) NULL,
    FOREIGN KEY (aluno) REFERENCES aluno(id),
    FOREIGN KEY (disciplina) REFERENCES disciplina(id),
    FOREIGN KEY (turma) REFERENCES turma(id),
    FOREIGN KEY (professor) REFERENCES professor(id)
);

CREATE TABLE fotos_de_avatar (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    endereco_thumb VARCHAR(255),
    endereco VARCHAR(255),
    usuario INT(11),
    FOREIGN KEY (usuario) REFERENCES usuario(id)
);

CREATE TABLE arquivos (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255),
    endereco_thumb VARCHAR(255),
    endereco VARCHAR(255),
    contexto VARCHAR(255),
    diario INT(11),
    conversa INT(11),
    descricao VARCHAR (255),
    DATA DATE,
    FOREIGN KEY (diario) REFERENCES diario_de_classe(id)
);