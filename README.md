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
│   ├── install.sql           # Instalação completa em arquivo único (sem terminal)
│   ├── migrate.php           # Runner de migrations (CLI, opcional)
│   ├── seed_superadmin.php   # Criação do SUPERADMIN (usado pelo runner)
│   └── migrations/           # 001_*.sql ... 008_*.sql
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

## 5. Banco de dados

O banco pode ser instalado de **duas formas**. Escolha a que preferir — ambas chegam ao mesmo resultado.

### Opção A — Importar um único arquivo `.sql` (recomendada, sem terminal)

Importe o arquivo **`database/install.sql`** usando sua ferramenta de banco preferida (phpMyAdmin, MySQL Workbench, Adminer, HeidiSQL). Ele:

- cria o banco `dmr_site` e todas as tabelas;
- insere perfis, permissões e as configurações padrão;
- **cria o usuário SUPERADMIN já pronto** (ver seção 6).

É **idempotente**: pode ser reimportado sem duplicar dados.

> No phpMyAdmin: aba **Importar** → selecione `database/install.sql` → **Executar**.

### Opção B — Runner de migrations (opcional, via terminal)

Para quem preferir aplicar as migrations incrementais pela linha de comando:

```bash
php database/migrate.php            # aplica as migrations pendentes
php database/migrate.php --fresh    # recria o banco do zero (CUIDADO: apaga tudo)
```

### Arquivos `.sql`

As migrations versionadas ficam em `database/migrations/` (`001_*.sql` … `008_*.sql`).
O `database/install.sql` é a consolidação de todas elas em um único arquivo.

### Regra obrigatória das migrations

**Nunca edite uma migration já criada/aplicada.** Para qualquer alteração de schema, crie um **novo** arquivo com o próximo número:

- ✅ Correto: criar `009_add_coluna_x.sql`
- ❌ Errado: editar `001_create_users_table.sql`

Ao criar uma nova migration, lembre-se de também acrescentá-la ao `database/install.sql` para manter a instalação de arquivo único em dia.

---

## 6. SUPERADMIN e acesso ao painel

O superadmin já é criado pela instalação do banco (tanto pelo `install.sql` quanto pelas migrations).

- **E-mail:** `admin@dmrassessoria.com.br`
- **Senha inicial:** `   `

> ⚠️ **Troque a senha imediatamente no primeiro acesso**, em **Minha conta** (`/admin/perfil`). Em produção, recomenda-se também alterar o e-mail do superadmin.

Acesse o painel em: **`/admin/login`** (também acessível pelo link discreto **"Área Restrita"** no rodapé do site).

> **Opcional:** se usar o runner (Opção B), é possível definir a senha inicial por variável de ambiente `DMR_SUPERADMIN_PASSWORD` e o e-mail por `DMR_SUPERADMIN_EMAIL` antes de rodar `php database/migrate.php`. Sem essas variáveis, o runner gera uma senha aleatória e a exibe no terminal.

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
- **Logos de bancos:** a seção de parcerias lista os nomes (Caixa, Banco do Brasil, Santander, Bradesco, Itaú, BRB) com `data-logo` preparado para receber os logos oficiais, desde que licenciados. Caixa e Banco do Brasil foram confirmados via site oficial arquivado (correspondente bancário); **a lista final de instituições deve ser confirmada pela DMR**.
- **Serviços:** a seção "Serviços" reflete os serviços levantados no site oficial arquivado (Crédito Imobiliário, Análise de Crédito e Risco, Análise Jurídica, Assessoria Imobiliária, Saque de FGTS, Secretaria de Vendas e Repasse). Confirme se a lista está atualizada.
- **Endereço e CNPJ:** não foram incluídos por não terem sido confirmados com segurança na pesquisa. Forneça os dados oficiais para habilitar SEO local (schema `LocalBusiness` com endereço).
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
- **Instagram:** @dmrassessoria
- **Website:** www.dmrassessoria.com.br

Todos esses valores podem ser alterados em **Configurações → Geral**.
