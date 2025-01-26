<?php

// Permitir CORS para acceso desde otros orígenes
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar solicitudes OPTIONS (pre-flight) para CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}


use Slim\Factory\AppFactory;
use Src\Controllers\AuthController;
use Src\Controllers\UsuarioController;
use Src\Controllers\ProductoController;
use Src\Controllers\PedidoController;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

// Crear la aplicación Slim
$app = AppFactory::create();

// Middleware para analizar el cuerpo de las solicitudes
$app->addBodyParsingMiddleware();

// Middleware de enrutamiento y manejo de errores
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// Instancia de los controladores
$authController = new AuthController();
$usuarioController = new UsuarioController();
$productoController = new ProductoController();
$pedidoController = new PedidoController();

// Ruta de login (sin autenticación previa)
$app->post('/login', [$authController, 'login']);

// Rutas de usuarios (requieren autenticación JWT)
$app->post('/usuarios', [$usuarioController, 'create'])->add([$authController, 'verificarJWT']);
$app->get('/usuarios', [$usuarioController, 'getAll'])->add([$authController, 'verificarJWT']);
$app->get('/usuarios/{id}', [$usuarioController, 'getById'])->add([$authController, 'verificarJWT']);
$app->put('/usuarios/{id}', [$usuarioController, 'update'])->add([$authController, 'verificarJWT']);
$app->delete('/usuarios/{id}', [$usuarioController, 'delete'])->add([$authController, 'verificarJWT']);

// Rutas para la API de productos (requieren autenticación JWT)
$app->post('/productos', [$productoController, 'create'])->add([$authController, 'verificarJWT']);
$app->get('/productos', [$productoController, 'getAll'])->add([$authController, 'verificarJWT']);
$app->get('/productos/{id}', [$productoController, 'getById'])->add([$authController, 'verificarJWT']);
$app->put('/productos/{id}', [$productoController, 'update'])->add([$authController, 'verificarJWT']);
$app->delete('/productos/{id}', [$productoController, 'delete'])->add([$authController, 'verificarJWT']);

// Rutas para la API de pedidos (requieren autenticación JWT)
$app->post('/pedidos', [$pedidoController, 'create'])->add([$authController, 'verificarJWT']);
$app->get('/pedidos', [$pedidoController, 'getAll'])->add([$authController, 'verificarJWT']);
$app->get('/pedidos/{id}', [$pedidoController, 'getById'])->add([$authController, 'verificarJWT']);
$app->put('/pedidos/{id}', [$pedidoController, 'updateStatus'])->add([$authController, 'verificarJWT']);
$app->delete('/pedidos/{id}', [$pedidoController, 'delete'])->add([$authController, 'verificarJWT']);

// Ejecutar la aplicación
$app->run();
