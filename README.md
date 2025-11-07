# 📋 Sistema de Gerenciamento de Formulários

Sistema web para criação e gerenciamento de formulários personalizados com suporte a múltiplos tipos de campos, validações e coleta de respostas.

## 📑 Índice

- [🚀 Sobre o Projeto](#-sobre-o-projeto)
- [✨ Principais Funcionalidades](#-principais-funcionalidades)
- [🛠️ Tecnologias Utilizadas](#️-tecnologias-utilizadas)
- [📋 Pré-requisitos](#-pré-requisitos)
- [🔧 Instalação e Configuração](#-instalação-e-configuração)
  - [Passo a Passo Detalhado](#passo-1️⃣-clone-o-repositório)
  - [Instalação Rápida (Resumo)](#-instalação-rápida-resumo)
- [🎯 Como Usar](#-como-usar)
- [👥 Sistema de Permissões](#-sistema-de-permissões)
- [📦 Estrutura do Banco de Dados](#-estrutura-do-banco-de-dados)
- [🧪 Testes](#-testes)
- [📝 Comandos Úteis](#-comandos-úteis)
- [🐛 Troubleshooting](#-troubleshooting)
- [❓ FAQ](#-faq-perguntas-frequentes)
- [🚧 Funcionalidades em Desenvolvimento](#-funcionalidades-em-desenvolvimento)
- [🤝 Contribuindo](#-contribuindo)

---

## 🚀 Sobre o Projeto

Este sistema foi desenvolvido para facilitar a criação de formulários de briefing com empresas e vistoriadores. Permite que usuários criem formulários totalmente personalizáveis com diversos tipos de campos (texto, CNPJ, CPF, upload de arquivos, listas de opções, etc.) e compartilhem via link para coleta de respostas.

### ✨ Principais Funcionalidades

- 📝 **Criação de Formulários Personalizados**: Interface visual simplificada para criar formulários
- 🎨 **Templates de Campos Prontos**: Campos pré-configurados (CNPJ, CPF, Telefone, E-mail, etc.)
- 📂 **Organização por Seções**: Agrupe campos em seções lógicas
- 🔄 **Opções Inline**: Adicione opções para campos select/radio/checkbox diretamente no formulário
- 👥 **Sistema de Permissões**: Roles (Owner, Employee, Viewer) com diferentes níveis de acesso
- 📊 **Coleta de Respostas**: Formulários podem ser respondidos via link público (em desenvolvimento)
- 📤 **Export de Dados**: Exporte respostas em CSV/Excel (planejado)

## 🛠️ Tecnologias Utilizadas

### Backend
- **Laravel 11.46** - Framework PHP
- **PHP 8.2+** - Linguagem de programação
- **PostgreSQL 18** - Banco de dados relacional
- **Redis** - Cache e filas
- **Spatie Laravel Permission 6.23** - Sistema de roles e permissões

### Frontend/Admin
- **Filament 4.1** - Admin panel moderno
- **Livewire 3.6** - Componentes reativos
- **Tailwind CSS 3.4** - Framework CSS
- **Alpine.js** - Framework JavaScript leve

### DevOps
- **Docker / Laravel Sail** - Ambiente de desenvolvimento
- **Vite 6.0** - Build tool para assets

## 📋 Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- **Docker** e **Docker Compose** (recomendado)
- **PHP 8.2+** (se rodar sem Docker)
- **Composer 2.8+**
- **Node.js 18+** e **NPM**
- **PostgreSQL 13+** (se rodar sem Docker)
- **Redis** (se rodar sem Docker)

## 🔧 Instalação e Configuração

> **💡 Dica**: Este guia assume que você está usando Docker com Laravel Sail. Se preferir rodar sem Docker, ajuste os comandos removendo `./vendor/bin/sail` ou o alias `sail`.

### Passo 1️⃣: Clone o Repositório

```bash
git clone https://github.com/joaoLucasPaiva/formManager.git formManager
cd formManager
```

### Passo 2️⃣: Instale as Dependências

```bash
composer install
npm install
```

### Passo 3️⃣: Configure o Ambiente

```bash
# Copie o arquivo de configuração
cp .env.example .env

# Gere a chave de segurança da aplicação
php artisan key:generate
```

**⚠️ Importante**: Abra o arquivo `.env` e certifique-se de que o nome do banco está configurado:

```env
DB_DATABASE=formularios
```

### Passo 4️⃣: Suba o Ambiente Docker

```bash
./vendor/bin/sail up -d
```

> **💡 Dica**: Crie um alias para facilitar os próximos comandos:
> ```bash
> alias sail='./vendor/bin/sail'
> ```
> Assim você pode usar apenas `sail` em vez de `./vendor/bin/sail`

### Passo 5️⃣: Configure o Banco de Dados

Execute este comando para criar as tabelas e popular com dados iniciais:

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

Isso vai:
- ✅ Criar todas as tabelas no banco
- ✅ Criar as roles (Owner, Employee, Viewer)
- ✅ Criar as permissões do sistema

### Passo 6️⃣: Crie o Usuário Administrador

```bash
./vendor/bin/sail artisan db:seed --class=AdminUserSeeder
```

Este comando cria automaticamente:
- **Email**: admin@admin.com
- **Senha**: password
- **Role**: Owner (acesso completo)

### Passo 7️⃣: Compile os Assets do Frontend

Em um **novo terminal**, execute:

```bash
npm run dev
```

> **💡 Dica**: Deixe este terminal rodando para hot reload automático durante o desenvolvimento.

### Passo 8️⃣: Acesse o Sistema

Abra seu navegador e acesse:

- **🌐 Admin Panel**: http://localhost/admin
- **📧 Login**: admin@admin.com
- **🔑 Senha**: password

**🎉 Pronto! O sistema está funcionando!**

### ✅ Checklist Pós-Instalação

Depois de acessar o sistema, verifique se está tudo OK:

- [ ] Consegue fazer login com admin@admin.com
- [ ] Menu lateral aparece com "Formulários"
- [ ] Consegue criar um novo formulário
- [ ] Consegue adicionar campos ao formulário
- [ ] Assets (CSS/JS) estão carregando corretamente

**Se algo não funcionar, consulte a seção [🐛 Troubleshooting](#-troubleshooting) abaixo!**

---

## 🚀 Instalação Rápida (Resumo)

Para quem já conhece o Laravel, aqui vai o resumo completo:

```bash
# 1. Clone e entre no diretório
git clone https://github.com/joaoLucasPaiva/formManager.git formManager && cd formManager

# 2. Instale dependências
composer install && npm install

# 3. Configure ambiente
cp .env.example .env && php artisan key:generate

# 4. Suba Docker e configure banco
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate:fresh --seed
./vendor/bin/sail artisan db:seed --class=AdminUserSeeder

# 5. Compile assets (em outro terminal)
npm run dev

# Acesse: http://localhost/admin
# Login: admin@admin.com | Senha: password
```

## 🎯 Como Usar

### Criar um Formulário

1. Acesse **Formulários** no menu lateral
2. Clique em **"Criar"**
3. Preencha:
   - **Título**: Nome do formulário
   - **Slug**: URL amigável (gerado automaticamente)
   - **Descrição**: Descrição do formulário
   - **Audience**: Escolha entre "Empresa" ou "Vistoriador"
4. Clique em **"Salvar"**

### Adicionar Campos ao Formulário

1. Após salvar o formulário, clique na aba **"Campos do Formulário"**
2. Clique em **"+ Criar"**
3. Em **"Tipo de Campo"**, escolha um template pronto:

#### 📋 Templates Disponíveis

**Campos Comuns** (pré-configurados):
- **CNPJ** - Máscara e validação automática
- **CPF** - Máscara e validação automática
- **Telefone** - Máscara `(99) 99999-9999`
- **CEP** - Máscara `99999-999`
- **E-mail** - Validação de e-mail
- **Valor em Reais** - Formatação monetária
- **Data** - Seletor de data
- **Sim/Não** - Radio buttons pré-configurados
- **Upload de PDF** - Aceita apenas PDF (máx 5MB)
- **Upload de Excel** - Aceita xlsx/xls/csv (máx 10MB)
- **Upload de Imagem** - Aceita jpg/png/webp (máx 5MB)

**Campos Personalizados**:
- **Texto Curto** - Campo de texto simples
- **Texto Longo** - Textarea
- **Número** - Campo numérico
- **Lista de Opções** - Select/Radio/Checkbox

4. Personalize o rótulo se necessário
5. Escolha ou crie uma **Seção** (opcional) para organizar os campos
6. Para campos tipo "Lista de Opções", adicione as opções inline:
   - Clique em **"Adicionar Opção"**
   - Digite o rótulo (ex: "Ltda")
   - O valor será gerado automaticamente (ex: "ltda")
   - Repita para todas as opções
7. Clique em **"Salvar"**

### Criar Seções (Opcional)

Seções podem ser criadas diretamente ao adicionar campos:

1. No campo **"Seção"**, clique no ícone **+**
2. Digite o título da seção
3. Adicione uma descrição (opcional)
4. A seção será criada e associada ao campo

### Publicar Formulário

1. Na lista de **Formulários**, localize o formulário desejado
2. Clique no botão **"Publicar"** (ícone de foguete 🚀)
3. Confirme a publicação
4. ⚠️ **Atenção**: Após publicar, o formulário fica **bloqueado** e não pode mais ser editado

## 👥 Sistema de Permissões

O sistema possui 3 níveis de acesso:

### Owner (Dona)
- ✅ Criar, editar e excluir formulários
- ✅ Publicar formulários
- ✅ Ver TODOS os formulários e respostas
- ✅ Gerenciar usuários
- ✅ Exportar dados
- ✅ Configurações do sistema

### Employee (Funcionária)
- ✅ Criar e editar formulários (rascunho)
- ✅ Ver apenas seus próprios formulários
- ✅ Ver apenas respostas dos seus formulários
- ✅ Exportar suas próprias respostas
- ❌ Publicar formulários
- ❌ Gerenciar usuários

### Viewer (Visualizador)
- ✅ Ver formulários publicados
- ✅ Ver respostas (readonly)
- ✅ Exportar dados
- ❌ Criar ou editar qualquer coisa

### Gerenciar Usuários

#### Criar Usuário Administrador

```bash
./vendor/bin/sail artisan db:seed --class=AdminUserSeeder
```

Cria automaticamente o usuário **admin@admin.com** com role **owner**.

#### Criar Outros Usuários (Employee ou Viewer)

Você pode usar o tinker para criar usuários adicionais:

```bash
./vendor/bin/sail artisan tinker
```

```php
// Criar funcionária
$user = App\Models\User::create([
    'name' => 'Maria Silva',
    'email' => 'maria@empresa.com',
    'password' => bcrypt('senha123')
]);
$user->assignRole('employee');

// Criar visualizador
$user = App\Models\User::create([
    'name' => 'João Santos',
    'email' => 'joao@empresa.com',
    'password' => bcrypt('senha123')
]);
$user->assignRole('viewer');

exit
```

## 📦 Estrutura do Banco de Dados

### Principais Tabelas

- **forms** - Armazena os formulários
- **form_sections** - Seções para organizar campos
- **form_fields** - Campos dos formulários
- **form_field_options** - Opções para campos select/radio/checkbox
- **form_submissions** - Respostas enviadas (planejado)
- **form_answers** - Respostas individuais por campo (planejado)
- **users** - Usuários do sistema
- **roles** - Roles do sistema (Spatie Permission)
- **permissions** - Permissões do sistema

## 🧪 Testes

```bash
# Execute os testes
sail artisan test

# Ou com coverage
sail artisan test --coverage
```

## 📝 Comandos Úteis

### Gerenciamento do Docker

```bash
# Subir os containers
./vendor/bin/sail up -d

# Parar os containers
./vendor/bin/sail down

# Reiniciar os containers
./vendor/bin/sail restart

# Ver logs dos containers
./vendor/bin/sail logs
```

### Banco de Dados

```bash
# Recriar banco completamente (⚠️ apaga todos os dados!)
./vendor/bin/sail artisan migrate:fresh --seed

# Criar apenas o usuário admin
./vendor/bin/sail artisan db:seed --class=AdminUserSeeder

# Criar roles e permissões
./vendor/bin/sail artisan db:seed --class=RoleSeeder

# Acessar o PostgreSQL via CLI
./vendor/bin/sail psql
```

### Cache e Otimização

```bash
# Limpar todo o cache
./vendor/bin/sail artisan optimize:clear

# Limpar cache de configuração
./vendor/bin/sail artisan config:clear

# Limpar cache de rotas
./vendor/bin/sail artisan route:clear

# Limpar cache de views
./vendor/bin/sail artisan view:clear
```

### Desenvolvimento

```bash
# Ver logs em tempo real
./vendor/bin/sail artisan pail

# Acessar o container bash
./vendor/bin/sail shell

# Executar comandos Composer
./vendor/bin/sail composer install
./vendor/bin/sail composer dump-autoload

# Executar testes
./vendor/bin/sail artisan test
```

## 🐛 Troubleshooting

### ❌ Erro: "SQLSTATE[08006] could not translate host name"

**Causa**: Os containers Docker não estão rodando.

**Solução**:
```bash
./vendor/bin/sail up -d
```

---

### ❌ Erro: "Class not found" ou autoload issues

**Causa**: Autoload do Composer desatualizado.

**Solução**:
```bash
./vendor/bin/sail composer dump-autoload
```

---

### ❌ Erro: "Access denied for user"

**Causa**: Credenciais do banco incorretas no `.env`.

**Solução**: Verifique se seu `.env` está assim:
```env
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=formularios
DB_USERNAME=sail
DB_PASSWORD=password
```

Depois recrie os containers:
```bash
./vendor/bin/sail down
./vendor/bin/sail up -d
```

---

### ❌ Erro de permissão em arquivos (storage/logs)

**Causa**: Permissões incorretas na pasta storage.

**Solução**:
```bash
sudo chown -R $USER:$USER .
./vendor/bin/sail artisan storage:link
chmod -R 775 storage bootstrap/cache
```

---

### ❌ Assets não carregam (CSS/JS quebrado)

**Causa**: Assets não foram compilados.

**Solução**:
```bash
# Para desenvolvimento
npm run dev

# Para produção
npm run build
```

---

### ❌ Porta já está em uso (port 80 já ocupada)

**Causa**: Outro serviço está usando a porta 80.

**Solução 1** - Parar o serviço que está usando:
```bash
# No Linux/Mac
sudo lsof -i :80
sudo kill -9 <PID>
```

**Solução 2** - Mudar a porta da aplicação:

Edite o `.env` e adicione:
```env
APP_PORT=8080
```

Reinicie os containers:
```bash
./vendor/bin/sail down
./vendor/bin/sail up -d
```

Acesse em: http://localhost:8080/admin

---

### ❌ "npm run dev" não funciona ou fica travado

**Causa**: Node_modules desatualizado ou processo travado.

**Solução**:
```bash
# Limpar cache do npm
rm -rf node_modules package-lock.json
npm install

# Executar novamente
npm run dev
```

---

### ❌ Erro: "The stream or file could not be opened"

**Causa**: Laravel não consegue escrever nos logs.

**Solução**:
```bash
chmod -R 775 storage
./vendor/bin/sail artisan cache:clear
```

## 🚧 Funcionalidades em Desenvolvimento

- [ ] Interface pública para responder formulários
- [ ] Sistema de envio de links por e-mail
- [ ] Visualização de respostas no admin
- [ ] Export de respostas (CSV/Excel)
- [ ] Dashboard com estatísticas
- [ ] Validação avançada de CNPJ/CPF
- [ ] Notificações por e-mail
- [ ] API REST para integração

---

## ❓ FAQ (Perguntas Frequentes)

### 1. Posso mudar a senha do admin depois?

Sim! Faça login e vá em Settings > Profile para alterar.

### 2. Como adiciono mais usuários?

Use o tinker conforme mostrado na seção [Gerenciar Usuários](#gerenciar-usuários), ou crie um Resource no Filament para gerenciar usuários via interface (planejado para versões futuras).

### 3. Posso usar outro banco de dados além do PostgreSQL?

Sim, o Laravel suporta MySQL, SQLite e SQL Server. Basta ajustar o `DB_CONNECTION` no `.env`. Porém, o projeto foi desenvolvido e testado com PostgreSQL.

### 4. Como faço deploy em produção?

Recomendações:
1. Use um servidor com Docker (DigitalOcean, AWS, etc)
2. Configure um domínio real
3. Use HTTPS (Let's Encrypt)
4. Mude `APP_ENV=production` e `APP_DEBUG=false`
5. Configure email real (não Mailpit)
6. Configure backups automáticos do banco

### 5. Posso personalizar o visual do admin?

Sim! O Filament permite customização via temas. Consulte a [documentação oficial do Filament](https://filamentphp.com/docs/4.x/panels/themes).

### 6. Como faço backup do banco de dados?

```bash
# Exportar
./vendor/bin/sail exec pgsql pg_dump -U sail formularios > backup.sql

# Importar
./vendor/bin/sail exec -T pgsql psql -U sail formularios < backup.sql
```

### 7. O sistema funciona offline?

Não. Ele precisa estar rodando em um servidor (local ou remoto) para funcionar.

### 8. Posso usar sem Docker?

Sim, mas você precisará instalar:
- PHP 8.2+
- PostgreSQL 13+
- Redis
- Composer
- Node.js

E ajustar os comandos removendo `./vendor/bin/sail`.

---

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo [LICENSE](LICENSE) para mais detalhes.

---

## 👨‍💻 Desenvolvedor

Desenvolvido por **João Lucas Gonçalves** e **Rodrigo Araujo**.

## 🤝 Contribuindo

Contribuições são bem-vindas! Para contribuir:

1. Faça um Fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/MinhaFeature`)
3. Commit suas mudanças (`git commit -m 'Adiciona MinhaFeature'`)
4. Push para a branch (`git push origin feature/MinhaFeature`)
5. Abra um Pull Request

---

## 📧 Suporte

Para suporte ou dúvidas:
- Abra uma [Issue no GitHub](https://github.com/seu-usuario/formularios/issues)
- Entre em contato via email: suporte@example.com

---

**Desenvolvido com ❤️ usando Laravel + Filament**

