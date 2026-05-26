create database MBM;
use MBM;

create table login(
  idUsuario int auto_increment primary key,
  nomeUsuario varchar(50) not null,
  Senha varchar(255) not null,
  Email varchar(100) not null,
  dataCadastro datetime default current_timestamp
);

create table categorias(
	id int auto_increment primary key,
    nome varchar(100) not null
);

create table subcategorias(
	id int auto_increment primary key,
    nome varchar(100) not null,
    
    categoriaId int,
    foreign key (categoriaId) references categorias (id)
);

create table problemas (
    id int auto_increment primary key,
    titulo varchar(255) not null,
    descricao text not null,
    imagem varchar(255),
    categoriaid int not null,
    subcategoriaid int not null,
    usuarioid int not null,
    dataproblema datetime default current_timestamp,
    
    foreign key (categoriaid) references categorias(id),
    foreign key (subcategoriaid) references subcategorias(id),
    foreign key (usuarioid) references login(idusuario)
);

-- drop database MBM;
-- select * from login;