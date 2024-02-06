## WebSchool
WebSchool é uma aplicação web para gerenciamento de escolas.

## Requisitos
Para executar essa aplicação você deve ter instalado no seu ambiente o Docker para configuração e execução dos containers do projeto.

Esse projeto utiliza containers com:

- PHP 7.4 com Apache e Composer
- MySQL 5.7

Quando em desenvolvimento, sugiro a instalação de algum cliente de bancos de dados cliente, como MySQL Workbench ou DBeaver

## Instalação e Configuração
Tendo Docker instalado no seu ambiente, execute os seguintes comandos para rodar a aplicação:

1) Instala os containers e prepara as imagens
```
docker-compose build
```

2) Executa o container em plano de fundo
```
docker-compose up -d
```

3) Instala as bibliotecas do composer
```
docker-compose exec php composer install
```

4) Crie cópias locais dos arquivos de configuração de ambiente do projeto
```
cp .env.dist .env
cp phinx.yml.dist phinx.yml
```

5) Configure o arquivo `.env` criado com as credenciais do banco de dados do container MySQL. Por exemplo
```
DB_HOST=webschool_mysql
DB_NAME=webschool
DB_USER=root
DB_PASSWORD=root
```

6) Configure o arquivo `phinx.yml` criado com as credenciais do banco de dados do container MySQL. Por exemplo
```
    development:
        adapter: mysql
        host: webschool_mysql
        name: webschool
        user: root
        pass: 'root'
        port: 3306
        charset: utf8
```

7) Executar o comando de migração para gerar as tabelas e dados iniciais do projeto
```
docker-compose exec php vendor/bin/phinx migrate
```

8) Tudo pronto! Se nenhum dos comandos acima apresentaram erros de nenhum tipo o projeto está pronto para ser executado. Acesse através do endereço `http://localhost:8015`. Em dev, o software já vem com uma conta admin padrão, usuario `admin` e senha `admin`