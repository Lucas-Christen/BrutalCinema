# BrutalCinema - Plano de Desenvolvimento

Sistema de gestão de cinema: filmes, salas, sessões e venda de ingressos.
Disciplina de Programação Web (UTFPR). Projeto dividido em três trabalhos.

## Regras gerais

- Um passo por vez. Cada passo vira um ou mais commits pequenos.
- Nada é adicionado sem estar previsto aqui. Se o plano mudar, atualizar este arquivo primeiro.
- Código simples e funcional. Sem antecipar conteúdo dos trabalhos seguintes.
- Branch `main` sempre funcional. Desenvolvimento em branches `feat/...`, merge via Pull Request.

## Escopo por trabalho

| Item                    | Trabalho 1 (24/09)             | Trabalho 2 (03/11)                   | Trabalho 3 (03/12)          |
|-------------------------|--------------------------------|--------------------------------------|-----------------------------|
| Arquitetura             | MVC simples com classes        | MVC refinado com OO                  | Laravel (MVC do framework)  |
| Banco                   | MariaDB via `mysqli`           | Migrar para PDO                      | Eloquent + migrations       |
| Rotas                   | `index.php?page=X&acao=Y`      | Router com URL transparente          | `routes/api.php`            |
| Autoload                | `require` manual               | Composer PSR-4 + packages            | Composer (Laravel)          |
| Autenticação            | Sessão PHP                     | Sessão (melhorada)                   | Sanctum (token)             |
| Interface               | Bootstrap 5 via CDN            | Bootstrap 5                          | Sem views, só JSON          |
| Repositório             | `BrutalCinema`                 | `BrutalCinema` (continuação)         | Repositório separado (API)  |

## Modelo de dados

Definido em `database/schema.sql`. Cinco tabelas:

- `usuarios` - perfis `admin`, `funcionario`, `cliente`
- `filmes` - catálogo, soft delete via `ativo`
- `salas` - `fileiras` x `assentos_por_fileira` define a capacidade
- `sessoes` - filme + sala + horário; `fim` calculado no PHP (duração + limpeza)
- `ingressos` - assento = (`fileira`, `numero`); UNIQUE por sessão

Assentos não têm tabela própria: são derivados da sala e marcados como ocupados pelos ingressos vendidos.

## Interface (front-end)

### Identidade visual
- Nome: **BrutalCinema**. Tema escuro, inspirado em sala de cinema.
- Bootstrap 5 via CDN, com um único arquivo `public/css/estilo.css` para ajustes próprios.
- Paleta: fundo `#121212`, superfícies `#1e1e1e`, destaque vermelho `#e50914`, texto `#f5f5f5`, texto secundário `#9e9e9e`.
- Tipografia padrão do Bootstrap (system font). Sem fontes externas.
- Ícones: Bootstrap Icons via CDN.

### Estrutura de layout
- `layout/header.php`: `<head>`, CDN do Bootstrap, navbar fixa no topo.
- `layout/nav.php`: menu com itens conforme perfil logado; nome do usuário e botão "Sair" à direita.
- `layout/flash.php`: alertas Bootstrap (`alert-success`, `alert-danger`) lidos da sessão.
- `layout/footer.php`: rodapé simples, fecha o HTML, script do Bootstrap.
- Conteúdo dentro de `<main class="container py-4">`.

### Padrão das telas
- **Listagem**: título da página + botão "Novo" à direita; tabela Bootstrap (`table-dark table-hover`)
  com colunas principais e coluna "Ações" (Editar, Desativar). Registros inativos com badge cinza.
- **Formulário**: card centralizado (`col-lg-8`); um campo por linha; erro do servidor exibido abaixo
  do campo com `is-invalid` e `invalid-feedback`; valores digitados preservados após erro;
  botões "Salvar" (vermelho) e "Cancelar" (link para a lista).
- **Login / Cadastro**: card pequeno centralizado na tela, sem navbar completa, logo no topo.
- **Mapa de assentos**: tela de venda mostra grade fileira x número. Cada assento é um botão
  quadrado: livre (cinza escuro), ocupado (vermelho, desabilitado), selecionado (branco).
  Letra da fileira à esquerda, "TELA" indicado no topo. Ao lado, resumo: filme, sala, horário,
  preço, tipo (inteira/meia) e dados do cliente.
- **Meus ingressos**: cards com filme, sala, data/hora, assento e valor; ingressos passados esmaecidos.
- **Página inicial (após login)**: cards com contadores (filmes ativos, sessões de hoje,
  ingressos vendidos hoje) e lista das próximas sessões.

### Telas do Trabalho 1
| Tela                  | Rota (`?page=`)          | Acesso                    |
|-----------------------|--------------------------|---------------------------|
| Login                 | `login`                  | público                   |
| Cadastro de cliente   | `registro`               | público                   |
| Início (dashboard)    | `inicio`                 | logado                    |
| Filmes: lista/form    | `filmes`                 | lista: todos; form: admin |
| Salas: lista/form     | `salas`                  | lista: todos; form: admin |
| Sessões: lista/form   | `sessoes`                | lista: todos; form: admin |
| Venda / mapa          | `ingressos&acao=vender`  | admin, funcionario        |
| Compra / mapa         | `ingressos&acao=comprar` | cliente                   |
| Meus ingressos        | `ingressos&acao=meus`    | cliente                   |
| Ingressos vendidos    | `ingressos`              | admin, funcionario        |
| Usuários              | `usuarios`               | admin                     |
| 404 / erro            | -                        | público                   |

## Perfis e permissões

| Ação                              | admin | funcionario | cliente |
|-----------------------------------|:-----:|:-----------:|:-------:|
| Cadastrar/editar filmes e salas   |   x   |             |         |
| Cadastrar/editar sessões          |   x   |             |         |
| Gerenciar usuários                |   x   |             |         |
| Vender ingresso (balcão)          |   x   |      x      |         |
| Listar filmes, salas e sessões    |   x   |      x      |    x    |
| Comprar ingresso para si          |       |             |    x    |
| Ver "meus ingressos"              |       |             |    x    |
| Cadastro público (registro)       | -     | -           |    x    |

---

## Trabalho 1 - Passos

### Passo 1 - Estrutura do banco (concluído)
- [x] `.gitignore`
- [x] `database/schema.sql`
- [x] `database/seed.sql` (usuários, filmes e salas de exemplo)

### Passo 2 - Configuração e conexão
- [x] `config/database.php` - host, porta, banco, usuário e senha (único arquivo a editar na instalação)
- [x] `config/app.php` - nome, timezone, intervalo de limpeza
- [x] `app/Core/Database.php` - conexão `mysqli` única (singleton)
- [x] `public/index.php` - front controller mínimo, testa conexão

### Passo 3 - Núcleo da aplicação (Core)
- [x] `app/Core/Session.php` - iniciar sessão, get/set, mensagens flash
- [x] `app/Core/Controller.php` - classe base: `renderizar()`, `redirecionar()`, `dadosPost()`
- [x] `app/Core/Model.php` - classe base: prepared statements `mysqli`, `todos()`, `buscar()`, `inserir()`, `atualizar()`
- [x] `app/Core/Validator.php` - regras: obrigatório, tamanho, e-mail, número, data, enum, CPF
- [x] Roteamento simples no `index.php` via `?page=` e `?acao=`
- [x] Página 404 e página de erro genérica

### Passo 4 - Layout e autenticação
- [x] `app/Views/layout/` - cabeçalho, rodapé, menu, exibição de flash (Bootstrap 5)
- [x] `app/Core/Auth.php` - login, logout, usuário logado, `exigirLogin()`, `exigirPerfil()`
- [x] `app/Models/Usuario.php`
- [x] `app/Controllers/AuthController.php` - login e logout
- [x] Tela de login com validação e mensagens de erro
- [x] Menu muda conforme perfil logado

### Passo 5 - Filmes (formulário 1)
- [x] `app/Models/Filme.php`
- [x] `app/Controllers/FilmeController.php` - listar, criar, editar, desativar
- [x] Views: lista (tabela) e formulário (criar/editar reaproveitam a mesma view)
- [x] Validações: título obrigatório, duração entre 1 e 600, classificação válida, ano válido
- [x] Regra: não desativar filme com sessão futura agendada

### Passo 6 - Salas (formulário 2)
- [ ] `app/Models/Sala.php`
- [ ] `app/Controllers/SalaController.php`
- [ ] Views: lista e formulário
- [ ] Validações: nome único, fileiras 1..26, assentos por fileira 1..50, tipo válido
- [ ] Regra: não reduzir tamanho se houver ingresso vendido fora do novo limite

### Passo 7 - Sessões (formulário 3)
- [ ] `app/Models/Sessao.php`
- [ ] `app/Controllers/SessaoController.php`
- [ ] Views: lista com JOIN (título do filme, nome da sala) e formulário
- [ ] Cálculo de `fim` = início + duração do filme + intervalo de limpeza
- [ ] Validações: filme e sala existem e estão ativos, início no futuro, preço > 0, idioma válido
- [ ] Regra: não permitir sobreposição de horário na mesma sala

### Passo 8 - Cadastro de cliente (formulário 4)
- [ ] `AuthController` - registro público
- [ ] Tela de cadastro
- [ ] Validações: nome, e-mail único, senha mínima, confirmação de senha
- [ ] Novo usuário recebe perfil `cliente`

### Passo 9 - Ingressos (formulário 5)
- [ ] `app/Models/Ingresso.php`
- [ ] `app/Controllers/IngressoController.php`
- [ ] Mapa de assentos da sessão (grade fileira x número, ocupados destacados)
- [ ] Venda pelo funcionário: informa nome e CPF do cliente
- [ ] Compra pelo cliente: usa dados do próprio usuário
- [ ] Validações: assento dentro dos limites, assento livre, CPF válido, sessão agendada e futura
- [ ] `valor_pago` calculado no servidor (meia = 50%)
- [ ] Tela "meus ingressos" para o cliente

### Passo 10 - Usuários (admin)
- [ ] `app/Controllers/UsuarioController.php`
- [ ] Lista de usuários, alterar perfil, ativar/desativar

### Passo 11 - Documentação e entrega
- [ ] `README.md`: integrantes e atividades de cada um, requisitos, instalação passo a passo
      (PHP nativo com `php -S`, e alternativa com XAMPP), configuração do banco, usuários de teste,
      bugs conhecidos e funcionalidades faltantes
- [ ] Testar instalação do zero em outra máquina seguindo apenas o README
- [ ] Revisão final: comentários, padronização, mensagens de erro
- [ ] Merge em `main` e envio do link no Moodle

---

## Trabalho 2 - Direção (detalhar após entrega do T1)

- Composer com autoload PSR-4 e namespaces (`App\Core`, `App\Models`, ...)
- Router com URL transparente (`/filmes/3/editar`) e `.htaccess` ou script de rotas do `php -S`
- Migrar `mysqli` para PDO
- Entidades tipadas e repositórios no lugar de arrays associativos
- Packages Composer: por exemplo `vlucas/phpdotenv` para configuração, biblioteca de e-mail
- Opcional: API de terceiro (envio de e-mail com o ingresso, pôster do filme via API externa)
- Corrigir pontos levantados pelo professor na apresentação do T1

## Trabalho 3 - Direção (detalhar após entrega do T2)

- Novo repositório com Laravel
- Migrations reproduzindo o modelo de dados
- Rotas em `routes/api.php`: filmes, salas, sessões, assentos disponíveis, ingressos (GET, POST, PUT, DELETE)
- Validação via Form Requests
- Autenticação com Laravel Sanctum
- Padrão de resposta JSON (`status`, `data`, `errors`)
- Coleção de testes exportada (Insomnia ou Postman) versionada no repositório

---