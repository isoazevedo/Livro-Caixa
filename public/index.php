<?php

declare(strict_types=1);

// I) Handler global de exceções não capturadas
set_exception_handler(function (Throwable $e) {
    http_response_code(500);
    $mensagem = $e->getMessage() . "\n" . $e->getTraceAsString();
    require __DIR__ . '/../app/Views/errors/500.php';
    exit;
});

// Bootstrap
require_once __DIR__ . '/../config/config.php';

// Inicia sessão
session_name(SESSION_NAME);
session_start();

// Autoload dos arquivos da aplicação
$appFiles = [
    __DIR__ . '/../app/Models/Database.php',
    __DIR__ . '/../app/Models/UserModel.php',
    __DIR__ . '/../app/Models/CategoriaModel.php',
    __DIR__ . '/../app/Models/MovimentoModel.php',
    __DIR__ . '/../app/Models/AuditModel.php',
    __DIR__ . '/../app/Core/View.php',
    __DIR__ . '/../app/Core/Controller.php',
    __DIR__ . '/../app/Core/Router.php',
];

foreach ($appFiles as $file) {
    require_once $file;
}

// Roteador
$router = new Router();

// Rotas de autenticação
$router->get('/login',   'AuthController', 'loginForm');
$router->post('/login',  'AuthController', 'login');
$router->get('/logout',  'AuthController', 'logout');

// Rotas de movimentos
$router->get('/',                               'MovimentoController', 'index');
$router->post('/movimentos/adicionar',          'MovimentoController', 'adicionar');
$router->post('/movimentos/editar',             'MovimentoController', 'editar');
$router->get('/movimentos/apagar/:id',          'MovimentoController', 'apagar');
$router->get('/movimentos/exportar',            'MovimentoController', 'exportarCsv');
$router->get('/movimentos/importar',            'MovimentoController', 'importarForm');
$router->post('/movimentos/importar',           'MovimentoController', 'importar');
$router->post('/movimentos/importar/confirmar', 'MovimentoController', 'confirmarImportar');

// Rotas de categorias
$router->post('/categorias/adicionar',   'CategoriaController', 'adicionar');
$router->post('/categorias/editar',      'CategoriaController', 'editar');
$router->get('/categorias/apagar/:id',   'CategoriaController', 'apagar');

// Rotas de relatórios
$router->get('/relatorios', 'RelatorioController', 'index');

// Rotas de perfil
$router->get('/perfil',         'UserController', 'perfil');
$router->post('/perfil',        'UserController', 'atualizarPerfil');
$router->post('/perfil/senha',  'UserController', 'alterarSenha');

// Rotas de admin
$router->get('/admin',                      'AdminController', 'index');
$router->post('/admin/usuarios/criar',      'AdminController', 'criarUsuario');
$router->post('/admin/usuarios/toggle',     'AdminController', 'toggleAtivo');
$router->get('/admin/audit',               'AdminController', 'auditLog');

// Determina URI relativa ao diretório público
$uri = $_SERVER['REQUEST_URI'] ?? '/';

// Remove o prefixo /Livro-Caixa/public se existir (para dev local via subpasta)
$basePath = parse_url(BASE_URL, PHP_URL_PATH);
if ($basePath && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}
if (empty($uri)) $uri = '/';

$router->dispatch($_SERVER['REQUEST_METHOD'], $uri);
