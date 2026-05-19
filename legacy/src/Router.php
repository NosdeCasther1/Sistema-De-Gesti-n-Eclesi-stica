<?php

declare(strict_types=1);

namespace Nosde\ProyectoIglesia;

use Exception;
use ReflectionMethod;

/**
 * Clase Router básica para manejar el enrutamiento del proyecto.
 * Soporta métodos GET y POST y despacho a controladores.
 */
class Router
{
    private array $routes = [];
    private string $basePath;

    public function __construct(string $basePath = '/ProyectoIglesia')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Registra una ruta GET.
     */
    public function get(string $path, string|callable $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * Registra una ruta POST.
     */
    public function post(string $path, string|callable $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * Registra una ruta genérica especificando el método.
     */
    public function add(string $method, string $path, string|callable $callback): void
    {
        $this->addRoute(strtoupper($method), $path, $callback);
    }

    /**
     * Agrega una ruta al listado interno.
     */
    private function addRoute(string $method, string $path, string|callable $callback): void
    {
        $path = '/' . ltrim($path, '/');
        $this->routes[$method][$path] = $callback;
    }

    /**
     * Despacha la petición actual a la ruta correspondiente.
     *
     * @throws Exception Si la ruta no existe o el controlador es inválido.
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Limpiar el basePath de la URI para obtener la ruta relativa
        if (str_starts_with($uri, $this->basePath)) {
            $uri = substr($uri, strlen($this->basePath));
        }

        $uri = '/' . ltrim($uri, '/');
        $uri = ($uri === '') ? '/' : $uri;

        $callback = $this->routes[$method][$uri] ?? null;

        if ($callback === null) {
            header("HTTP/1.0 404 Not Found");
            throw new Exception("Ruta no encontrada: [{$method}] {$uri}", 404);
        }

        if (is_callable($callback)) {
            $callback();
            return;
        }

        if (is_string($callback) && str_contains($callback, '@')) {
            $this->callController($callback);
            return;
        }

        throw new Exception("Callback de ruta inválido para {$uri}");
    }

    /**
     * Instancia el controlador y llama al método especificado.
     * Ejemplo: "FamiliaController@index"
     */
    private function callController(string $handler): void
    {
        [$controllerName, $method] = explode('@', $handler);
        
        // Asumiendo que los controladores están en el namespace Nosde\ProyectoIglesia\Controladores
        $fullControllerName = "Nosde\\ProyectoIglesia\\Controladores\\{$controllerName}";

        if (!class_exists($fullControllerName)) {
            throw new Exception("El controlador {$fullControllerName} no existe.");
        }

        $controller = new $fullControllerName();

        if (!method_exists($controller, $method)) {
            throw new Exception("El método {$method} no existe en el controlador {$controllerName}.");
        }

        // Ejecutar el método
        $controller->$method();
    }
}
