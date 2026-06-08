<?php
namespace Helpers;

/**
 * Clase que genera y valida tokens CSRF para formularios.
 *
 * @package Helpers
 * @author Tienda MVC
 * @version 1.0.0
 */
class Csrf
{
    /**
     * Genera o recupera el token CSRF almacenado en sesion.
     *
     * @return string Token hexadecimal de 64 caracteres
     */
    public static function generar(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Genera un campo HTML oculto con el token CSRF.
     *
     * @return string HTML del input hidden con el token
     */
    public static function campo(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . self::generar() . '">';
    }

    /**
     * Valida el token CSRF recibido contra el almacenado en sesion.
     *
     * @param  string|null $token Token enviado desde el formulario
     * @return bool               True si el token es valido, false en caso contrario
     */
    public static function validar(?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
