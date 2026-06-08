<?php
namespace Controllers;

use Models\UsuarioModel;
use Models\LogModel;
use Helpers\Csrf;

/**
 * Clase que gestiona la autenticacion de administradores.
 *
 * @package Controllers
 * @author Tienda MVC
 * @version 1.0.0
 */
class AuthController
{
    /**
     * Muestra el formulario de inicio de sesion.
     */
    public function showLogin(): void
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Procesa el inicio de sesion.
     *
     * Valida CSRF, verifica credenciales y registra en bitacora.
     */
    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!Csrf::validar($_POST['csrf_token'] ?? '')) {
            $_SESSION['error'] = 'Token de seguridad invalido.';
            header('Location: login');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: login');
            exit;
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->buscarPorUsername($username);

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['admin'] = [
                'id' => $usuario['id'],
                'username' => $usuario['username'],
                'nombre_completo' => $usuario['nombre_completo']
            ];

            $log = new LogModel();
            $log->registrar(
                $usuario['id'],
                $usuario['username'],
                'Inicio de sesion',
                'El administrador inició sesion en el sistema'
            );

            $_SESSION['success'] = 'Bienvenido, ' . $usuario['nombre_completo'] . '.';
            header('Location: productos');
            exit;
        }

        $_SESSION['error'] = 'Credenciales incorrectas.';
        header('Location: login');
        exit;
    }

    /**
     * Cierra la sesion del administrador.
     *
     * Registra el cierre en bitacora antes de destruir la sesion.
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['admin'])) {
            $log = new LogModel();
            $log->registrar(
                $_SESSION['admin']['id'],
                $_SESSION['admin']['username'],
                'Cierre de sesion',
                'El administrador cerró sesion en el sistema'
            );
        }

        session_destroy();
        header('Location: login');
        exit;
    }
}
