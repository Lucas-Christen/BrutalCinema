# BrutalCinema - Plano do Trabalho 2

Continuação de [PLANO.md](PLANO.md), que registra o Trabalho 1.
Entrega: **03/11/2026**. Este arquivo acompanha o andamento do Trabalho 2.

## Regras gerais

- Um passo por vez. Ao fim de cada passo: testar e aguardar conferência. Os commits são feitos pelo Lucas.
- Nada é adicionado sem estar previsto aqui. Se o plano mudar, atualizar este arquivo primeiro.
- Sem Laravel ou outro framework PHP. Bibliotecas pontuais via Composer são permitidas.
- `schema.sql` e `seed.sql` não mudam, e os dados atuais são preservados.
- Desenvolvimento em branches `feat/...` a partir de `main`. Nada de push ou PR sem aval.

## Requisitos do enunciado

| Requisito obrigatório                        | Situação em 30/10 | Passos  |
|----------------------------------------------|-------------------|---------|
| PHP 8+ e Orientação a Objetos                | Parcial           | 1, 5    |
| Composer com autoload e packages             | Não iniciado      | 1, 3, 4 |
| Banco via PDO                                | Não iniciado      | 2       |
| Rotas com URL transparente e MVC             | Não iniciado      | 4, 5    |
| Documentação de configuração e instalação    | Desatualizada     | 6       |
| Interface com feedback e erros               | Atendido          | 7       |

Opcionais: framework de interface (Bootstrap, já atendido) e API de terceiro.

## Decisões

- **Packages:** `vlucas/phpdotenv` para configuração e `nikic/fast-route` para rotas.
- **`url()`:** mantém a assinatura atual; só a implementação muda, para as views quase não mudarem.
- **Parâmetros de rota:** o front controller coloca `{id}` e `{sessao_id}` em `$_GET`, então os
  métodos `buscarOuRedirecionar()` continuam iguais.

## Rotas planejadas

| URL                                                     | Destino                     |
|---------------------------------------------------------|-----------------------------|
| `/`                                                     | Início                      |
| `/login`, `/registro`, `POST /sair`                     | `AuthController`            |
| `/filmes`, `/filmes/novo`, `/filmes/{id}/editar`        | Lista e formulários         |
| `POST /filmes/salvar`, `/desativar`, `/ativar`          | Ações do admin              |
| Mesmo padrão para `/salas` e `/usuarios`                | Controllers correspondentes |
| `/sessoes`, `/sessoes/novo`, `/sessoes/{id}/editar`     | `SessaoController`          |
| `POST /sessoes/salvar`, `POST /sessoes/cancelar`        | `SessaoController`          |
| `/ingressos`, `/ingressos/meus`, `POST /ingressos/cancelar` | `IngressoController`    |
| `/sessoes/{sessao_id}/vender` e `/comprar` (GET e POST) | Mapa de assentos            |

URL inexistente, ou método HTTP não permitido, mostra a página 404.

---

## Passos

### Passo 0 - Preparação
- [x] Instalar o Composer (`sudo apt install -y composer`; feito pelo Lucas, pois exige `sudo`)
- [x] Criar a branch `feat/trabalho-2`
- [x] Criar este plano e apontar `PLANO.md` para ele
- [ ] Registrar aqui os pontos levantados pelo professor na apresentação do T1, se houver

### Passo 1 - Composer, autoload e namespaces
- [x] `composer.json` com PSR-4 (`App\\` → `app/`) e `app/Core/helpers.php` em `files`
- [x] Versionar `composer.lock`
- [x] `namespace` e `use` em `app/Core`, `app/Controllers` e `app/Models`
- [x] Importar as classes nas 13 views que as usam
- [x] Tratar classes globais (`DateTime`, `RuntimeException`, `Throwable`)
- [x] Trocar os `require` manuais de `public/index.php` por `vendor/autoload.php`
- Pronto quando: todas as telas abrem como antes, ainda com `mysqli` e `?page=`.

### Passo 2 - PDO
- [x] `app/Core/Database.php` com `ERRMODE_EXCEPTION`, `FETCH_ASSOC` e `EMULATE_PREPARES=false`
- [x] Manter o `SET time_zone` na conexão
- [x] `app/Core/Model.php`: `PDOStatement`, `rowCount()` e `lastInsertId()`
- [x] `app/Models/Ingresso.php`: `beginTransaction`, `rollBack` e `PDOException`
- [x] `IngressoController`: duplicidade por `$e->errorInfo[1] === 1062`
- [x] Remover todo uso de `mysqli`
- Pronto quando: CRUDs e venda funcionam, e assento duplicado mostra a mensagem amigável.

### Passo 3 - `.env`
- [x] Criar `.env.example` e adicionar `.env` ao `.gitignore`
- [x] Carregar o phpdotenv em `public/index.php`
- [x] `config/database.php` e `config/app.php` lendo `$_ENV`, incluindo `APP_DEBUG`
- Pronto quando: sem `.env`, a aplicação mostra um erro claro; com ele, funciona normalmente.

### Passo 4 - Rotas com URL transparente
- [x] `app/Core/Router.php` sobre o `fast-route` e `config/rotas.php` com a tabela acima
- [x] Despachar pelo caminho da URL, mantendo `Auth::sincronizar()` e o tratamento 404/500
- [x] Reescrever `url()` em `app/Core/helpers.php`, com suporte a subpasta para o XAMPP
- [x] Criar `asset()` e usá-lo em `header.php` e `footer.php`
- [x] Ajustar o menu ativo em `app/Views/layout/nav.php`
- [x] Ajustar os links de `vender` e `comprar`
- [x] Criar `public/.htaccess`
- Pronto quando: a navegação usa URLs limpas, CSS e JS carregam em rotas profundas e não sobra
  `index.php?page=`.

### Passo 5 - Revisão de MVC e OO
- [x] Tirar das views a lógica de regra, como o cálculo de sessão encerrada em `sessoes/lista.php`
- [x] Remover código sem uso, como `Sessao::STATUS`, se continuar sem uso

### Passo 6 - Documentação
- [ ] `README.md`: requisitos, `composer install`, `.env`, criação do banco, `php -S`,
      Apache/XAMPP com `mod_rewrite`, packages, nova estrutura e rotas
- [ ] Cofre: Roadmap, Rotas, Arquitetura, Core, Roteiro de Testes e Defesa

### Passo 7 - Teste final e entrega
- [ ] Executar todo o roteiro no navegador, com admin, funcionário e cliente
- [ ] Repetir os cenários do commit `ace2bea`
- [ ] Testar instalação limpa seguindo apenas o README
- [ ] Testar `.htaccess` no Apache, se o `mod_rewrite` estiver ativo
- [ ] Abrir PR para `main`, com aval do Lucas
- [ ] Enviar no Moodle

---

## Extras opcionais

Só depois do Passo 7, se houver tempo e aval, nesta ordem:

- [ ] CSRF: token nos 16 formulários POST, com verificação central no despacho das rotas
- [ ] Entidades tipadas, a começar por `Filme` (maior risco: alcança views e formulários)
- [ ] API de terceiro: pôster dos filmes ou e-mail do ingresso (exige chave de API)

## Verificação

- A cada passo: `php -l` em todos os arquivos e teste das telas alteradas.
- Testes de escrita usam transação com rollback ou dados de teste removidos depois.
- O banco nunca é resetado sem pedido do Lucas.
