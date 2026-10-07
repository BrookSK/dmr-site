<?php

declare(strict_types=1);

/**
 * Definição das rotas da aplicação.
 *
 * @var \App\Core\Router $router
 */

use App\Controllers\HomeController;
use App\Controllers\ContactController;
use App\Controllers\LegalController;
use App\Controllers\SeoController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;
use App\Controllers\SettingController;

// --- Site público ----------------------------------------------------------
$router->get('/', [HomeController::class, 'index']);
$router->post('/contato', [ContactController::class, 'submit']);

$router->get('/politica-de-privacidade', [LegalController::class, 'privacy']);
$router->get('/termos-de-uso', [LegalController::class, 'terms']);

// SEO
$router->get('/robots.txt', [SeoController::class, 'robots']);
$router->get('/sitemap.xml', [SeoController::class, 'sitemap']);

// --- Autenticação do painel -------------------------------------------------
$router->get('/admin/login', [AuthController::class, 'showLogin']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->post('/admin/logout', [AuthController::class, 'logout'], ['Auth']);

// --- Área administrativa (protegida) ---------------------------------------
$router->get('/admin', [DashboardController::class, 'index'], ['Auth']);

// Perfil / troca de senha
$router->get('/admin/perfil', [AuthController::class, 'showProfile'], ['Auth']);
$router->post('/admin/perfil/senha', [AuthController::class, 'updatePassword'], ['Auth']);

// Usuários (exige permissão users.manage)
$router->get('/admin/usuarios', [UserController::class, 'index'], ['Auth', 'CanManageUsers']);
$router->get('/admin/usuarios/novo', [UserController::class, 'create'], ['Auth', 'CanManageUsers']);
$router->post('/admin/usuarios', [UserController::class, 'store'], ['Auth', 'CanManageUsers']);
$router->get('/admin/usuarios/{id}/editar', [UserController::class, 'edit'], ['Auth', 'CanManageUsers']);
$router->post('/admin/usuarios/{id}', [UserController::class, 'update'], ['Auth', 'CanManageUsers']);
$router->post('/admin/usuarios/{id}/status', [UserController::class, 'toggleStatus'], ['Auth', 'CanManageUsers']);
$router->post('/admin/usuarios/{id}/senha', [UserController::class, 'resetPassword'], ['Auth', 'CanManageUsers']);
$router->post('/admin/usuarios/{id}/excluir', [UserController::class, 'destroy'], ['Auth', 'CanManageUsers']);

// Configurações (exige permissão settings.manage)
$router->get('/admin/configuracoes', [SettingController::class, 'index'], ['Auth', 'CanManageSettings']);
$router->post('/admin/configuracoes/geral', [SettingController::class, 'saveGeneral'], ['Auth', 'CanManageSettings']);
$router->post('/admin/configuracoes/smtp', [SettingController::class, 'saveSmtp'], ['Auth', 'CanManageSettings']);
$router->post('/admin/configuracoes/smtp/teste', [SettingController::class, 'testSmtp'], ['Auth', 'CanManageSettings']);
