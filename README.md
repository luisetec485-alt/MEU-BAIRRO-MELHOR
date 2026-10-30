# Meu Bairro Melhor

Aplicação web para registrar e acompanhar problemas urbanos em Franco da Rocha.
Moradores podem enviar denúncias com localização e anexos; administradores
consultam e atualizam o andamento dos registros.

## Requisitos

- PHP com PDO e `pdo_mysql`;
- MySQL ou MariaDB;
- Apache (por exemplo, o XAMPP).

## Instalação

1. Coloque o projeto na pasta `htdocs` do XAMPP.
2. Inicie o Apache e o MySQL.
3. Importe [`database/MBM.sql`](database/MBM.sql) pelo phpMyAdmin ou pelo terminal:

   ```powershell
   mysql -u root -p -P 3307 < database\MBM.sql
   ```

   **Atenção:** o script apaga e recria o banco `MBM`. Não o execute sobre um
   banco que contenha dados que você queira manter.

4. Acesse <http://localhost/MEU-BAIRRO-MELHOR/>.

O Composer é necessário apenas para instalar ou restaurar dependências:

```powershell
composer install
```

## Banco de dados

A aplicação usa estas configurações padrão:

- `DB_HOST`: `localhost`
- `DB_PORT`: `3307`
- `DB_NAME`: `MBM`
- `DB_USER`: `root`
- `DB_PASS`: vazio

Defina as variáveis de ambiente para usar valores diferentes.

## Contas de demonstração

O banco inclui contas para testar os perfis de administrador e morador:

- **Administrador**
  - E-mail: `admin@meubairromelhor.com`
  - Senha: `Admin@2026`
- **Morador**
  - E-mail: `leitor@meubairromelhor.com`
  - Senha: `Leitor@2026`

Essas contas são apenas para desenvolvimento e demonstração. Troque as senhas
antes de disponibilizar o sistema em outro ambiente.
