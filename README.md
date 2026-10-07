# DMR Assessoria Imobiliária

Landing page institucional e área administrativa da **DMR Assessoria Imobiliária**, empresa com mais de 30 anos de experiência em crédito imobiliário e financiamento.

O projeto comunica o posicionamento central da empresa: **"A DMR está ao seu lado do início ao fim"** — proximidade, experiência, confiança, cuidado, agilidade e responsabilidade, com a tecnologia a serviço do atendimento humano.

---

## 1. Stack

- **PHP 8.1+** (desenvolvido e testado com PHP 8.5) — arquitetura **MVC própria**, sem framework.
- **MySQL / MariaDB** — banco estruturado por **migrations `.sql` versionadas**.
- **HTML + CSS + JavaScript puro** no frontend (sem bibliotecas pesadas).
- **Sem WordPress, sem Elementor, sem CMS externo, sem `.env`.**

As configurações sensíveis (SMTP, contatos, nome do site etc.) são **administráveis pelo painel** e ficam armazenadas no banco de dados (tabela `settings`). Credenciais de SMTP são **criptografadas** antes de persistir.

---

## 2. Estrutura de diretórios

```
dmr-site/
├── index.php                 # Entrada raiz (delega para public/index.php)
├── .htaccess                 # Rewrite para o front controller + assets
├── .gitignore
├── README.md
├── config/
│   ├── config.example.php    # Modelo de configuração (versionado)
│   └── config.php            # Configuração local (NÃO versionado)
├── app/
│   ├── Core/                 # Núcleo: Router, Database, View, Session, Csrf, App...
│   ├── Controllers/          # Controllers do site e do admin
│   ├── Models/               # User, Role, Setting, Contact
│   ├── Services/             # AuthService, MailService (SMTP)
│   ├── Middleware/           # Auth, Permission e derivados
│   └── Views/                # site/, admin/, layouts/, errors/
├── routes/
│   └── web.php               # Definição de todas as rotas
├── database/
│   ├── migrate.php           # Runner de migrations (CLI)
│   ├── seed_superadmin.php   # Criação do SUPERADMIN
│   └── migrations/           # 001_*.sql, 002_*.sql, ...
├── public/
│   ├── index.php             # Front controller
│   ├── favicon.ico
│   └── assets/               # css/, js/, img/
└── storage/
    └── logs/
```

---

## 3. Requisitos do servidor

- PHP 8.1 ou superior, com as extensões `pdo_mysql` e `openssl` habilitadas.
- MySQL 5.7+ ou MariaDB 10.3+.
- Apache com `mod_rewrite` habilitado (o projeto usa `.htaccess`). Em Nginx, replicar as regras de rewrite apontando para `public/index.php`.

---

## 4. Instalação

1. **Clone o repositório** e acesse a pasta do projeto.

2. **Crie o arquivo de configuração** a partir do modelo:

   ```bash
   cp config/config.example.php config/config.php
   ```

3. **Edite `config/config.php`** com as credenciais do seu banco e defina um `app_key` aleatório e longo:

   ```php
   'db' => [
       'host'     => '127.0.0.1',
       'port'     => 3306,
       'database' => 'dmr_site',
       'username' => 'seu_usuario',
       'password' => 'sua_senha',
       'charset'  => 'utf8mb4',
   ],
   'app_key' => 'uma-string-aleatoria-bem-longa',
   'env'     => 'production', // 'development' em ambiente local
   ```

   > O `app_key` é usado para criptografar valores sensíveis (ex.: senha de SMTP). **Não altere o `app_key` depois de salvar credenciais no painel**, ou os valores criptografados ficarão ilegíveis.

4. **Execute as migrations** (cria o banco, as tabelas e o superadmin):

   ```bash
   php database/migrate.php
   ```

5. **Configure o servidor web** para apontar o `DocumentRoot` para a raiz do projeto (ou diretamente para `public/`, ambos funcionam graças ao `index.php` da raiz).

---

## 5. Banco de dados e migrations

O banco é criado e evoluído exclusivamente por **arquivos `.sql` versionados** em `database/migrations/`.

### Executar as migrations

```bash
php database/migrate.php
```

O runner:
- cria o banco (se não existir) e a tabela de controle `migrations`;
- aplica, em ordem, apenas os `.sql` ainda não aplicados;
- cria o usuário SUPERADMIN (se ainda não existir).

### Recriar o banco do zero (CUIDADO — apaga tudo)

```bash
php database/migrate.php --fresh
```

### Regra obrigatória das migrations

**Nunca edite uma migration já criada/aplicada.** Para qualquer alteração de schema, crie um **novo** arquivo com o próximo número:

- ✅ Correto: criar `007_add_coluna_x.sql`
- ❌ Errado: editar `001_create_users_table.sql`

As migrations são incrementais e versionadas. Isso garante histórico consistente entre ambientes.

---

## 6. SUPERADMIN e acesso ao painel

O superadmin é criado automaticamente ao rodar as migrations.

- **E-mail padrão:** `admin@dmrassessoria.com.br`
- **Senha:** definida pela variável de ambiente `DMR_SUPERADMIN_PASSWORD` (mínimo 8 caracteres) ou, se não informada, **gerada aleatoriamente e exibida uma única vez no terminal** durante a execução das migrations. Anote-a.

Exemplos:

```bash
# Definindo e-mail e senha explicitamente (Linux/macOS)
DMR_SUPERADMIN_EMAIL="voce@dmrassessoria.com.br" DMR_SUPERADMIN_PASSWORD="UmaSenhaForte123" php database/migrate.php
```

```cmd
:: Windows (cmd)
set DMR_SUPERADMIN_EMAIL=voce@dmrassessoria.com.br
set DMR_SUPERADMIN_PASSWORD=UmaSenhaForte123
php database/migrate.php
```

Acesse o painel em: **`/admin/login`** (também acessível pelo link discreto **"Área Restrita"** no rodapé do site).

Após o primeiro acesso, **troque a senha** em **Minha conta** (`/admin/perfil`).

---

## 7. Configuração de SMTP (envio de e-mails)

Toda a configuração de e-mail é feita pelo painel, **sem credenciais no código**:

1. Acesse **Configurações → SMTP / E-mail**.
2. Preencha host, porta, usuário, senha, criptografia (TLS/SSL/Nenhuma), nome e e-mail do remetente.
3. Salve.
4. Use **Enviar e-mail de teste** para validar as credenciais.

A senha de SMTP é armazenada **criptografada** (AES-256-GCM) no banco. Ao editar, deixe o campo de senha em branco para manter a senha atual.

O formulário de contato do site usa automaticamente esse SMTP para notificar o e-mail de contato configurado em **Configurações → Geral**.

---

## 8. Permissões

O sistema já nasce com uma estrutura de perfis e permissões, pronta para crescer:

- **Perfis (roles):** `superadmin`, `admin`, `editor`.
- **Permissões:** `dashboard.view`, `users.manage`, `settings.manage`, `contacts.view`.

As permissões são sempre verificadas no **backend** (middlewares `CanManageUsers`, `CanManageSettings`), não apenas escondendo botões na interface. O `superadmin` possui todas as permissões implicitamente.

Para adicionar novos perfis/permissões, crie uma nova migration de seed (ex.: `007_add_permissions.sql`).

---

## 9. Rotas principais

**Site:**
- `/` — Landing page
- `/politica-de-privacidade`
- `/termos-de-uso`
- `/robots.txt`, `/sitemap.xml`
- `POST /contato` — Envio do formulário

**Painel (protegido):**
- `/admin/login`, `/admin/logout`
- `/admin` — Dashboard
- `/admin/perfil` — Troca de senha
- `/admin/usuarios` — Gerenciamento de usuários (CRUD)
- `/admin/configuracoes` — Configurações gerais e SMTP

---

## 10. Segurança implementada

- Senhas com `password_hash()` (bcrypt/Argon via `PASSWORD_DEFAULT`) e rehash automático.
- Sessões seguras (cookies HttpOnly, SameSite=Lax, regeneração de ID).
- Proteção **CSRF** em todos os formulários administrativos e no formulário de contato.
- **Prepared statements** (PDO) em todas as consultas — proteção contra SQL Injection.
- **Escaping de saída** (`e()`) nas views — proteção contra XSS.
- Proteção de rotas por autenticação e autorização por permissão (backend).
- Headers de segurança (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` etc.).
- Credenciais sensíveis criptografadas no banco (SMTP).
- Honeypot anti-spam no formulário de contato.

---

## 11. Configuração para produção

- Em `config/config.php`, defina `'env' => 'production'` (desativa a exibição de erros).
- Garanta HTTPS (os cookies de sessão passam a usar a flag `Secure` automaticamente).
- Defina `site_url` em **Configurações → Geral** com a URL pública (usada em links absolutos, sitemap e e-mails).
- Mantenha `config/config.php` fora do versionamento (já está no `.gitignore`).
- Confirme que o diretório `public/` é o único exposto publicamente, se possível.

> **Observação sobre `.htaccess` e `index.php`:** o projeto já inclui o padrão de `.htaccess` e `index.php` da raiz fornecido para o projeto. Caso um padrão oficial atualizado seja fornecido pelo responsável, ele deve ser aplicado **sem ser substituído ou ignorado**.

---

## 12. Conteúdo pendente de preenchimento

Conforme o briefing, **nenhum dado foi inventado**. Os pontos abaixo foram deixados como placeholders claramente identificados e devem ser preenchidos com informações oficiais:

- **Valor em negócios viabilizados:** card de estatística na home (`data-placeholder="valor-negocios"`) exibe "—" até que o número oficial seja fornecido.
- **Depoimentos / cases:** a seção de prova social contém três placeholders (`[Depoimento ... — inserir posteriormente]`). Não publicar conteúdo fictício como se fosse real.
- **Logos de bancos:** a seção de parcerias lista os nomes (Santander, Bradesco, Itaú, BRB, Caixa) com `data-logo` preparado para receber os logos oficiais, desde que licenciados.
- **Política de Privacidade e Termos de Uso:** textos realistas e profissionais já redigidos, mas que **devem ser revisados juridicamente** antes da publicação definitiva (há aviso visível em ambas as páginas).
- **Imagens de pessoas/atendimento:** o design está preparado para receber fotografias (hero, seções) que reforcem o conceito de acompanhamento e proximidade.

---

## 13. Como criar uma nova migration

1. Crie um arquivo em `database/migrations/` com o próximo número sequencial e um nome descritivo:
   `007_descricao_curta.sql`.
2. Escreva o SQL (use `CREATE TABLE IF NOT EXISTS`, `ALTER TABLE`, `INSERT ... ON DUPLICATE KEY UPDATE` conforme o caso).
3. Rode `php database/migrate.php`.
4. **Nunca** altere arquivos de migration anteriores.

---

## 14. Credenciais e dados de contato padrão (ajustáveis no painel)

- **WhatsApp / Telefone:** (11) 98223-1363 — `5511982231363`
- **Instagram:** @dmrassessoriaoficial
- **Website:** www.dmrassessoria.com.br

Todos esses valores podem ser alterados em **Configurações → Geral**.
