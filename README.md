# BARBERS CRODA - Sistema de Gestão

Sistema completo de gestão para barbearia desenvolvido com Laravel e Filament.

## Funcionalidades

- 📅 Agendamento de clientes
- 👥 Gestão de clientes
- 💼 Controle de planos e assinaturas
- 📊 Relatórios e estatísticas
- 🔔 Notificações automáticas

## Requisitos

- PHP 8.2 ou superior
- Composer
- SQLite (ou MySQL/PostgreSQL)

## Instalação

1. Clone o repositório
2. Copie o arquivo `.env.example` para `.env`:
```bash
copy .env.example .env
```

3. Instale as dependências:
```bash
composer install
```

4. Gere a chave da aplicação:
```bash
php artisan key:generate
```

5. Crie o banco de dados SQLite:
```bash
type nul > database/database.sqlite
```

6. Execute as migrações:
```bash
php artisan migrate
```

7. Crie um usuário administrador:
```bash
php artisan make:filament-user
```

8. Inicie o servidor:
```bash
php artisan serve
```

9. Acesse o painel administrativo em: `http://localhost:8000/admin`

## Planos

### Plano Clássico
- Valor: R$ 74,90
- Limite: 4 cortes por mês
- Desconto aplicado apenas quando o atendimento é iniciado

## Status de Agendamento

- **0**: Agendado (padrão)
- **1**: Atendimento iniciado
- **2**: Atendimento finalizado
