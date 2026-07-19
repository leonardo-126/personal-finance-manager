# Personal Finance Manager — API (backend)

API em **Laravel 12** para um gerenciador de finanças pessoais: receitas, caixas,
gastos, importação de faturas de cartão (Nubank), divisão de despesas por pessoa,
compartilhamento de fatura por link e exportação.

É a parte de servidor do projeto. O frontend (React) fica em
[`personal-finance-manager-web`](../personal-finance-manager-web).

---

## Stack

- **PHP** 8.2 · **Laravel** 12
- **Autenticação:** Laravel Sanctum (SPA, baseada em sessão/cookie)
- **Banco:** SQLite (arquivo em `database/database.sqlite`)
- **Fila:** driver `database` (worker para importação de fatura)
- **Storage:** disco `public` (upload de avatar)
- **Docker:** serviços `backend` + `queue` via `docker-compose`

---

## Como rodar

### Opção 1 — Docker (recomendado)

Sobe a API em `http://localhost:8000` e um worker de fila. O
[`docker-entrypoint.sh`](docker-entrypoint.sh) cuida sozinho de: criar o `.env`,
gerar a `APP_KEY`, criar o arquivo SQLite, rodar as migrations e o `storage:link`.

```bash
docker compose up -d --build

# logs
docker compose logs -f backend

# comandos artisan dentro do container
docker compose exec backend php artisan migrate
docker compose exec backend php artisan tinker
```

### Opção 2 — Local (sem Docker)

Requer PHP 8.2+, Composer e a extensão `pdo_sqlite`.

```bash
composer install
cp .env.example .env
php artisan key:generate

# banco SQLite
touch database/database.sqlite
php artisan migrate

# link público para os uploads (avatares)
php artisan storage:link

# em terminais separados:
php artisan serve            # API em http://localhost:8000
php artisan queue:work       # worker da fila (importação de fatura)
```

---

## Variáveis de ambiente principais

| Variável | Descrição | Valor típico (dev) |
|---|---|---|
| `APP_URL` | URL pública da API (usada para montar links de arquivos) | `http://localhost:8000` |
| `DB_CONNECTION` | Driver do banco | `sqlite` |
| `QUEUE_CONNECTION` | Driver da fila | `database` |
| `FILESYSTEM_DISK` | Disco de arquivos | `local` |
| `SANCTUM_STATEFUL_DOMAINS` | Domínios do frontend que recebem sessão | `localhost:5173,127.0.0.1:5173` |

> **CORS / Sanctum:** o frontend acessa via cookie/sessão. Mantenha a origem do
> frontend em `config/cors.php` (`allowed_origins`) e em `SANCTUM_STATEFUL_DOMAINS`.
> `supports_credentials` deve ser `true`.

---

## Módulos e endpoints

Todas as rotas abaixo de `/api`. Salvo indicação, exigem autenticação
(`auth:sanctum`).

### Autenticação
| Método | Rota | Descrição |
|---|---|---|
| POST | `/auth/register` | Cadastro |
| POST | `/auth/login` | Login |
| POST | `/auth/logout` | Logout |
| GET | `/auth/me` | Usuário autenticado (com perfil) |

### Perfil
`GET/POST/PUT /profile` — dados do perfil e upload de avatar.

### Finanças
- **Fontes de renda:** `GET/POST/PUT/DELETE /fontes-renda`
- **Rendas:** `GET/POST/PUT/DELETE /rendas`
- **Caixas financeiras:** `GET/POST/PUT/DELETE /caixas-financeiras`
- **Movimentações de caixa:** `GET/POST/PUT/DELETE /movimentacoes-caixas`
- **Categorias de gastos:** `GET/POST/PUT/DELETE /categorias-gastos`
- **Gastos:** `GET/POST/PUT/DELETE /gastos` · `GET /gastos/{id}` (com itens)
- **Itens de gasto:** `GET/POST/PUT/DELETE /gastos-itens` · `PATCH /gastos-itens/{id}/pessoa` (atribui pessoa)
- **Pessoas:** `GET/POST/PUT/DELETE /pessoas` (quem participa da divisão)

### Faturas de cartão
| Método | Rota | Descrição |
|---|---|---|
| GET | `/faturas` | Lista as faturas (gastos marcados como fatura) |
| POST | `/faturas/preview` | Lê o arquivo e devolve as transações, sem salvar |
| POST | `/faturas/importar` | Importa a fatura (1 item por transação) |

Parser: [`app/Services/NubankFaturaParser.php`](app/Services/NubankFaturaParser.php).

### Compartilhamento de fatura
O dono gera **um link único por pessoa**; a pessoa abre sem login e marca os itens
que são dela.

| Método | Rota | Auth | Descrição |
|---|---|---|---|
| GET | `/gastos/{id}/compartilhamentos` | sim | Lista os links da fatura |
| POST | `/gastos/{id}/compartilhamentos` | sim | Cria/retorna o link de uma pessoa |
| DELETE | `/gastos/{id}/compartilhamentos/{pessoaId}` | sim | Revoga o link |
| GET | `/fatura-compartilhada/{token}` | **não** | Fatura acessada pelo token |
| PATCH | `/fatura-compartilhada/{token}/itens/{itemId}` | **não** | Marca/desmarca item como da pessoa |

---

## Estrutura

```
app/
├── Actions/           # regras de negócio (uma ação por caso de uso)
├── Http/
│   ├── Controllers/   # controllers de ação única (__invoke)
│   ├── Requests/      # validação (FormRequest)
│   └── Resources/     # serialização das respostas JSON
├── Models/            # Eloquent
└── Services/          # ex.: parser de fatura Nubank
database/migrations/   # schema
routes/api.php         # todas as rotas da API
```

Padrão do projeto: **controller fino** (`__invoke`) → **Action** (lógica) →
**Resource** (resposta). A validação fica em **FormRequest**.

---

## Testes

```bash
php artisan test
# ou dentro do Docker:
docker compose exec backend php artisan test
```
