# Meu Bairro Melhor

## Nome do grupo

**Meu Bairro Melhor (MBM)**

O projeto é uma aplicação web para registro e acompanhamento de problemas do
bairro, com perfis de administrador e usuário comum.

## Requisitos

- PHP com as extensões PDO e `pdo_mysql` habilitadas;
- MySQL ou MariaDB;
- Apache (por exemplo, o Apache do XAMPP);
- Composer, caso seja necessário reinstalar as dependências.

As dependências PHP já estão disponíveis no diretório `vendor/`. Para
reinstalá-las, execute:

```powershell
composer install
```

## Como importar o banco de dados

O script completo está em [`database/MBM.sql`](database/MBM.sql). Ele recria o
banco `MBM`, portanto apaga um banco existente com esse mesmo nome.

### Opção 1: phpMyAdmin

1. Inicie o Apache e o MySQL no XAMPP.
2. Acesse `http://localhost/phpmyadmin`.
3. Abra a opção **Importar**.
4. Selecione o arquivo `database/MBM.sql`.
5. Clique em **Importar** e aguarde a conclusão.

### Opção 2: terminal

Com o cliente MySQL instalado, execute na raiz do projeto:

```powershell
mysql -u root -p -P 3307 < database\MBM.sql
```

Quando o usuário `root` não tiver senha, pressione `Enter` quando solicitado.
Se o MySQL estiver usando a porta padrão `3306`, troque `3307` pelo número da
porta configurada no seu ambiente.

## Configuração da conexão

Por padrão, a aplicação tenta conectar usando:

| Configuração | Valor padrão |
| --- | --- |
| Host | `localhost` |
| Porta | `3306` |
| Banco | `MBM` |
| Usuário | `root` |
| Senha | vazia |

Esses valores podem ser substituídos pelas variáveis de ambiente
`DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER` e `DB_PASS`.

## Como executar

Com o projeto dentro da pasta `htdocs` do XAMPP, inicie Apache e MySQL e
acesse:

```text
http://localhost/MEU-BAIRRO-MELHOR/
```

Se preferir usar o servidor embutido do PHP, execute na raiz do projeto:

```powershell
php -S localhost:8000
```

Depois, acesse `http://localhost:8000`.

## Logins de teste

O dump já insere as contas abaixo. O nível `0` corresponde a administrador e o
nível `1` corresponde a usuário comum.

| Perfil | E-mail | Senha |
| --- | --- | --- |
| Administrador | `admin@meubairromelhor.com` | `Admin@2026` |
| Usuário comum | `leitor@meubairromelhor.com` | `Leitor@2026` |

Por segurança, altere essas senhas em ambientes que não sejam de teste.

