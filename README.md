# Projeto Importador CSV

## Resumo Simples
- **Service**: Lógica de importação e filas (CsvImportService, ProcessCsvImport).
- **Repositories**: Acesso a dados (ImportRepository, UserRepository).
- **Testes**: Validação de serviços e controladores (CsvImportServiceTest, ImportTest, UserTest).
- **Interfaces**: Contratos para repositórios (UserRepositoryInterface).
- **Model**: Tabela para armazenamento do andamento da importação (Imports).
- **Enums**: Padronização das mensagens internas e externas (ImportResponses, ImportStatusResponses, JwtResponses).
- **Requests**: Validador de requisições para o upload do csv (UploadCsvRequest).
- **Middleware**: Validadores de tokens JWT para as rotas com o middleware adicionado (JwtAuthMiddleware).
- **Controllers**: Lista usuários com paginação (UserController) e lida com upload do arquivo csv e consulta status (ImportController).

Desenvolvi uma API no Laravel 12 para importar CSVs, com autenticação JWT via token fixo gerado por seeder, rotas protegidas e uma pública, usando filas para processamento assíncrono. Implementei camadas de services, repository e testes unitários/integração. Resolvi erros como RouteNotFoundException criando um middleware personalizado e adaptei o `bootstrap/app.php` para o Laravel 12 e Docker. Tive dificuldades com a configuração inicial do JWT (ex.: getAuthIdentifierName) e ajustes no Docker. Pensei em simplificar a autenticação com token fixo para facilitar uso inicial e optei por enums para padronizar mensagens, melhorando manutenção.

## Instruções para uso

### Docker build
- `docker compose up -d --build`
- `docker-compose exec app php artisan key:generate`
- Configurar `.env` para os dados do banco:
    - `DB_CONNECTION=mysql`
    - `DB_HOST=db`
    - `DB_PORT=3306`
    - `DB_DATABASE=laravel`
    - `DB_USERNAME=laravel`
    - `DB_PASSWORD=your_secure_password`
- `docker-compose exec app php artisan migrate`
- `docker-compose exec app php artisan jwt:secret`
- `docker-compose exec app php artisan db:seed --class=GenerateApiTokenSeeder`

### Testes
- Configurar arquivo `phpunit.xml`:
    - `DB_CONNECTION` set value=mysql
    - `DB_DATABASE` set value=laravel
- `docker-compose exec app php artisan test`

### .env
- `APP_URL=http://localhost:8000`
- Se atente a deixar a conexão do banco com os valores certos igual nas instruções para o build do docker.
- Após rodar os comandos pelas instruções do docker, verifique se foram criadas duas novas keys no `.env`:
    - `JWT_SECRET` e `API_TOKEN`

## Rotas
- `GET -> api`
- `GET -> api/import-status/{id}`
- `POST -> api/upload`
- `GET -> api/users`

## Como usar as rotas

### POST -> api/upload
- Bearer Token = API_TOKEN (key que está dentro do arquivo `.env`, pegar valor e adicionar)
- Headers:
    - `Accept: application/json`
- Body:
    - form-data
- Fila:
    - Exemplo uso em Postman:
        - Adicionar Rota
        - Adicionar Bearer Token
        - Em Body, alterar para form-data, preencher a key para ‘file’, colocar tipo para ‘File’
        - Em Value adicionar arquivo com extensão csv
    - Após o uso da rota deve retornar um json parecido com:

### GET -> api/import-status/{id}
- Para a variável `$id`, adicione o id de algum processo que já tenha executado
- Ex URL:
    - `http://localhost:8000/api/import-status/1`
    - Deve retornar algo parecido com:
        - Deve retornar status como: completed, pending, failed, processing

### GET -> api/users
- Pode usar até dois parâmetros:
    - `per_page` -> para quantidade de registro por página.
    - `page` -> progredir para a página.
- Ex URL:
    - `http://localhost:8000/api/users?page=2&per_page=2`
    - Deve retornar uma listagem de usuário em formato de paginação.

### GET -> api
- Rota padrão para verificar se o ambiente está funcionando
- Ex URL:
    - `http://localhost:8000/api`
    - Deve retornar json com a mensagem: `{"message": "Welcome to Laravel API CSV Importer"}`
