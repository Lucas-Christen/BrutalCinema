# BrutalCinema

Sistema web de gestão de cinema: cadastro de filmes, salas e sessões, venda de ingressos com mapa de assentos e área do cliente. Desenvolvido em PHP 8 puro (sem framework) com MariaDB, para a disciplina de Programação Web da UTFPR.

## Integrante

| Nome | RA |
|------|----|
| Lucas Fernandes Christen | 2343045 |

Trabalho desenvolvido individualmente. Todas as atividades (modelagem do banco, arquitetura MVC, controllers, models, views, validações, autenticação, interface e documentação) foram realizadas pelo integrante acima.

## Sumário

1. [Funcionalidades](#funcionalidades)
2. [Requisitos](#requisitos)
3. [Instalação](#instalação)
4. [Configuração](#configuração)
5. [Usuários de teste](#usuários-de-teste)
6. [Arquitetura](#arquitetura)
7. [Regras de negócio e validações](#regras-de-negócio-e-validações)
8. [Particularidades e limitações](#particularidades-e-limitações)
9. [Próximos trabalhos](#próximos-trabalhos)

## Funcionalidades

Três perfis de usuário, com permissões distintas:

| Funcionalidade | admin | funcionario | cliente |
|---|:-:|:-:|:-:|
| Login / logout | x | x | x |
| Cadastro público (cria conta de cliente) | | | x |
| Painel inicial com indicadores e próximas sessões | x | x | |
| Página inicial com sessões em cartaz | | | x |
| Listar filmes, salas e sessões | x | x | x |
| Cadastrar / editar / desativar filmes | x | | |
| Cadastrar / editar / desativar salas | x | | |
| Cadastrar / editar / cancelar sessões | x | | |
| Vender ingressos no balcão (mapa de assentos) | x | x | |
| Comprar ingressos para si (mapa de assentos) | | | x |
| Listar e cancelar ingressos vendidos | x | x | |
| Ver "meus ingressos" | | | x |
| Gerenciar usuários (perfil, ativar/desativar) | x | | |

Formulários de cadastro/edição: **Filmes**, **Salas**, **Sessões**, **Usuários**, **Cadastro de cliente** e **Venda/compra de ingresso**. Todos com validação e tratamento de erros no servidor (PHP).

## Requisitos

- PHP 8.1 ou superior, com as extensões `mysqli` e `mbstring`
- MariaDB 10.4+ ou MySQL 8+
- Navegador moderno (Bootstrap 5 e Bootstrap Icons são carregados via CDN, então é necessário acesso à internet para a interface carregar os estilos)

Não há dependências via Composer nem etapa de build.

## Instalação

### Opção A: Linux (PHP nativo)

1. Instale PHP e MariaDB:

   ```bash
   sudo apt update && sudo apt install -y php php-cli php-mysql php-mbstring mariadb-server
   ```

2. Clone o repositório:

   ```bash
   git clone https://github.com/Lucas-Christen/BrutalCinema.git
   cd BrutalCinema
   ```

3. Crie o banco de dados e o usuário:

   ```bash
   sudo mariadb -e "CREATE DATABASE brutalcinema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                    CREATE USER 'brutal'@'localhost' IDENTIFIED BY 'brutal123';
                    GRANT ALL PRIVILEGES ON brutalcinema.* TO 'brutal'@'localhost';
                    FLUSH PRIVILEGES;"
   ```

4. Crie as tabelas e carregue os dados de exemplo:

   ```bash
   mariadb -u brutal -pbrutal123 brutalcinema < database/schema.sql
   mariadb -u brutal -pbrutal123 brutalcinema < database/seed.sql
   ```

5. Inicie o servidor embutido do PHP apontando para a pasta `public`:

   ```bash
   php -S localhost:8000 -t public
   ```

6. Acesse `http://localhost:8000` e entre com um dos [usuários de teste](#usuários-de-teste).

### Opção B: Windows com XAMPP

1. Instale o [XAMPP](https://www.apachefriends.org/) com PHP 8 e inicie **Apache** e **MySQL** no painel de controle.
2. Clone ou extraia o projeto em `C:\xampp\htdocs\BrutalCinema`.
3. Abra `http://localhost/phpmyadmin` e:
   - crie o banco `brutalcinema` com collation `utf8mb4_unicode_ci`;
   - na aba **Contas de usuário**, crie o usuário `brutal` com senha `brutal123` e conceda todos os privilégios no banco `brutalcinema`;
   - selecione o banco `brutalcinema`, aba **Importar**, e importe `database/schema.sql`; depois importe `database/seed.sql`.
4. Acesse `http://localhost/BrutalCinema/public/`.

Alternativa ao passo 3 pelo terminal do XAMPP (`C:\xampp\mysql\bin`):

```bat
mysql -u root -e "CREATE DATABASE brutalcinema CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER 'brutal'@'localhost' IDENTIFIED BY 'brutal123'; GRANT ALL PRIVILEGES ON brutalcinema.* TO 'brutal'@'localhost'; FLUSH PRIVILEGES;"
mysql -u brutal -pbrutal123 brutalcinema < C:\xampp\htdocs\BrutalCinema\database\schema.sql
mysql -u brutal -pbrutal123 brutalcinema < C:\xampp\htdocs\BrutalCinema\database\seed.sql
```

> Se preferir usar o usuário `root` do XAMPP (sem senha), ajuste `config/database.php` conforme a seção seguinte.

## Configuração

Existem apenas dois arquivos de configuração, ambos na pasta `config/`.

### `config/database.php`

Dados de conexão com o banco. Se você seguiu a instalação acima, **não precisa alterar nada**. Caso use outro host, usuário, senha ou nome de banco, edite os valores:

```php
return [
    'host'    => 'localhost',
    'port'    => 3306,
    'dbname'  => 'brutalcinema',
    'user'    => 'brutal',
    'pass'    => 'brutal123',
    'charset' => 'utf8mb4',
];
```

### `config/app.php`

Configurações gerais da aplicação:

| Chave | Padrão | Descrição |
|---|---|---|
| `nome` | `BrutalCinema` | Nome exibido no título e no menu |
| `timezone` | `America/Sao_Paulo` | Fuso horário usado nas datas |
| `debug` | `true` | Exibe a mensagem do erro na página 500. **Coloque `false` em produção.** |
| `intervalo_limpeza_min` | `15` | Minutos reservados entre o fim de um filme e a próxima sessão na mesma sala |

## Usuários de teste

Criados pelo `seed.sql`. Senha de todos: **`123456`**.

| Perfil | E-mail |
|---|---|
| admin | `admin@brutalcinema.com` |
| funcionario | `func@brutalcinema.com` |
| cliente | `diego@brutalcinema.com` |

O seed também cadastra 3 filmes e 3 salas. Sessões e ingressos não são criados: cadastre uma sessão como admin para testar a venda.

Novos clientes podem se cadastrar pelo link "Criar conta" na tela de login. Funcionários e outros admins são criados pelo admin na tela **Usuários**.

## Arquitetura

O projeto segue o padrão **MVC** implementado manualmente, sem framework.

```
BrutalCinema/
├── public/                 raiz web (única pasta exposta pelo servidor)
│   ├── index.php           front controller: recebe todas as requisições
│   ├── css/estilo.css      ajustes visuais sobre o Bootstrap
│   └── js/app.js           máscara de CPF e resumo do mapa de assentos
├── app/
│   ├── Core/               infraestrutura compartilhada
│   │   ├── Database.php    conexão mysqli única (singleton)
│   │   ├── Model.php       classe base dos models: prepared statements, CRUD genérico
│   │   ├── Controller.php  classe base dos controllers: renderizar(), redirecionar()
│   │   ├── Validator.php   regras de validação encadeáveis, erros por campo
│   │   ├── Session.php     sessão e mensagens flash
│   │   ├── Auth.php        login, logout, exigirLogin(), exigirPerfil()
│   │   └── helpers.php     e(), url(), config(), moeda(), dataHoraBr()...
│   ├── Controllers/        um por recurso: Filme, Sala, Sessao, Ingresso, Usuario, Auth, Inicio, Erro
│   ├── Models/             um por tabela: Filme, Sala, Sessao, Ingresso, Usuario
│   └── Views/              apenas HTML + PHP de apresentação
│       ├── layout/         header, nav, flash, footer
│       └── <recurso>/      lista.php, form.php, etc.
├── config/                 database.php e app.php
├── database/               schema.sql (tabelas) e seed.sql (dados de exemplo)
└── docs/PLANO.md           plano de desenvolvimento seguido no projeto
```

### Fluxo de uma requisição

1. Toda URL tem a forma `index.php?page=filmes&acao=editar&id=3`.
2. `public/index.php` carrega o núcleo, inicia a sessão e consulta o mapa `$rotas`, que associa cada `page` a um Controller.
3. O valor de `acao` (padrão `index`) é o método chamado no Controller. Página ou método inexistente resulta em 404.
4. O Controller verifica permissão (`Auth::exigirLogin()` ou `Auth::exigirPerfil()`), usa o Model para ler/gravar e chama `renderizar()`, que inclui header, view e footer.
5. Após um POST bem-sucedido, o Controller grava uma mensagem flash na sessão e redireciona (padrão POST-redirect-GET). Se houver erro de validação, a view é reexibida com os valores digitados e a mensagem abaixo de cada campo.
6. Qualquer exceção não tratada é capturada em `index.php` e exibida na página 500.

### Segurança

- Senhas com `password_hash()` / `password_verify()`.
- `session_regenerate_id()` no login.
- Todas as consultas com **prepared statements** (`mysqli`), nunca concatenando valores do usuário.
- Toda saída nas views passa por `e()` (`htmlspecialchars`).
- Ações que alteram dados (desativar, cancelar, sair) só aceitam **POST**.
- Autorização por perfil no início de cada ação protegida, no servidor.

## Regras de negócio e validações

Todas verificadas em PHP, no servidor. A validação HTML5 está desativada nos formulários (`novalidate`) justamente para que a do servidor seja exercitada.

**Filmes**: título 2–150 caracteres; duração 1–600 min; classificação entre L/10/12/14/16/18; gênero obrigatório; ano entre 1888 e o ano atual + 2. Não pode ser desativado se tiver sessão futura agendada.

**Salas**: nome único; 1–26 fileiras (letras A–Z); 1–50 assentos por fileira; tipo 2D/3D/IMAX. Capacidade é calculada, não armazenada. Não pode ser desativada com sessão futura, nem reduzida abaixo de um assento já vendido em sessão futura.

**Sessões**: filme e sala devem existir e estar ativos; início no futuro; preço maior que zero; idioma dublado/legendado. O término é calculado no servidor: início + duração do filme + intervalo de limpeza. **Não pode haver sobreposição de horário na mesma sala** (a mensagem informa qual sessão conflita). Sessão com ingressos vendidos não pode trocar de sala nem ser cancelada.

**Ingressos**: sessão deve estar agendada e no futuro; de 1 a 10 assentos por operação, cada um no formato `A-5`, dentro dos limites da sala e livre; CPF com 11 dígitos; tipo inteira/meia. O valor pago é calculado no servidor (meia = 50%). Os ingressos de uma operação são gravados em **transação**: se um falhar, nenhum é salvo. A tabela tem `UNIQUE (sessao_id, fileira, numero)`, então o banco impede venda dupla mesmo em acesso simultâneo; nesse caso o usuário recebe uma mensagem amigável.

**Usuários**: e-mail válido e único; senha 6–72 caracteres (obrigatória só no cadastro; em branco na edição mantém a atual); confirmação igual à senha. Admin não pode desativar a própria conta nem rebaixar o próprio perfil. Usuário inativo não consegue fazer login.

## Particularidades e limitações

- **Cancelar ingresso exclui o registro** (libera o assento). Não há histórico de ingressos cancelados. Decisão tomada para manter a restrição `UNIQUE` do banco simples.
- **Assentos não têm tabela própria**: são derivados das dimensões da sala e marcados como ocupados pelos ingressos existentes.
- O status "encerrada" de uma sessão é apenas visual (calculado pela data), não é gravado no banco.
- A máscara de CPF (`js/app.js`) é conveniência de interface; a verificação real (11 dígitos) é feita em PHP. Não há validação de dígito verificador do CPF.
- Não há CSRF token nos formulários (previsto como melhoria para o Trabalho 2).
- Não há paginação nas listagens.
- Não há recuperação de senha.
- Bootstrap e ícones vêm de CDN; sem internet a aplicação funciona, mas sem estilos.
- Rotas no formato `index.php?page=...` (URLs transparentes ficam para o Trabalho 2).

Nenhum bug conhecido no momento da entrega.

## Próximos trabalhos

- **Trabalho 2**: Composer com autoload PSR-4 e namespaces, sistema de rotas com URLs transparentes, migração de `mysqli` para PDO, entidades tipadas, packages Composer e CSRF.
- **Trabalho 3**: API REST em Laravel no mesmo domínio, com Sanctum, em repositório separado.

## Licença

MIT. Veja [LICENSE](LICENSE).
