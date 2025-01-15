<?php

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
$app->post('/usuarios', [$authController, 'verificarJWT'], [$usuarioController, 'create']);
$app->get('/usuarios', [$authController, 'verificarJWT'], [$usuarioController, 'getAll']);
$app->get('/usuarios/{id}', [$authController, 'verificarJWT'], [$usuarioController, 'getById']);
$app->put('/usuarios/{id}', [$authController, 'verificarJWT'], [$usuarioController, 'update']);
$app->delete('/usuarios/{id}', [$authController, 'verificarJWT'], [$usuarioController, 'delete']);

// Rutas para la API de productos (requieren autenticación JWT)
$app->post('/productos', [$authController, 'verificarJWT'], [$productoController, 'create']);
$app->get('/productos', [$authController, 'verificarJWT'], [$productoController, 'getAll']);
$app->get('/productos/{id}', [$authController, 'verificarJWT'], [$productoController, 'getById']);
$app->put('/productos/{id}', [$authController, 'verificarJWT'], [$productoController, 'update']);
$app->delete('/productos/{id}', [$authController, 'verificarJWT'], [$productoController, 'delete']);

// Rutas para la API de pedidos (requieren autenticación JWT)
$app->post('/pedidos', [$authController, 'verificarJWT'], [$pedidoController, 'create']);
$app->get('/pedidos', [$authController, 'verificarJWT'], [$pedidoController, 'getAll']);
$app->get('/pedidos/{id}', [$authController, 'verificarJWT'], [$pedidoController, 'getById']);
$app->put('/pedidos/{id}', [$authController, 'verificarJWT'], [$pedidoController, 'updateStatus']);
$app->delete('/pedidos/{id}', [$authController, 'verificarJWT'], [$pedidoController, 'delete']);

// Ejecutar la aplicación
$app->run();
