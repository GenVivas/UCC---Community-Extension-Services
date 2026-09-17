<?php

/**
 * Front Controller / Router
 * Parses the URL and dispatches to the correct controller and method.
 *
 * URL pattern: BASE_URL/{controller}/{method}/{param1}/{param2}
 * Example:     /proposals/view/5
 */
class App
{
    private string $controller = 'DashboardController';
    private string $method     = 'index';
    private array  $params     = [];

    public function __construct()
    {
        $this->parseUrl();
    }

    /**
     * Parse the URL from the query string (?url=...) set by .htaccess.
     */
    private function parseUrl(): void
    {
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $segments = $url ? explode('/', $url) : [];

        // ── Segment 0: Controller ─────────────────────────────
        if (!empty($segments[0])) {
            // Convert URL slug to PascalCase controller name
            // e.g. "community-needs" → "CommunityNeedsController"
            $controllerName = $this->toClassName($segments[0]) . 'Controller';
            $controllerFile = APP_PATH . "/controllers/{$controllerName}.php";

            if (file_exists($controllerFile)) {
                $this->controller = $controllerName;
            } else {
                $this->notFound();
                return;
            }
        }

        // Auto-load all models before instantiating controller
        foreach (glob(APP_PATH . '/models/*.php') as $modelFile) {
            require_once $modelFile;
        }

        // Load and instantiate controller
        require_once APP_PATH . "/controllers/{$this->controller}.php";
        $controllerInstance = new $this->controller();

        // ── Segment 1: Method ─────────────────────────────────
        if (!empty($segments[1])) {
            $methodName = $this->toCamelCase($segments[1]);
            if (method_exists($controllerInstance, $methodName)) {
                $this->method = $methodName;
            } else {
                $this->notFound();
                return;
            }
        }

        // ── Segment 2+: Params ────────────────────────────────
        $this->params = array_slice($segments, 2);

        // Dispatch
        call_user_func_array([$controllerInstance, $this->method], $this->params);
    }

    /**
     * Convert a URL segment to a PascalCase class name.
     * "community-needs" → "CommunityNeeds"
     * "proposals"       → "Proposals"
     */
    private function toClassName(string $segment): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $segment)));
    }

    /**
     * Convert a URL segment to camelCase method name.
     * "create-form" → "createForm"
     */
    private function toCamelCase(string $segment): string
    {
        $pascal = $this->toClassName($segment);
        return lcfirst($pascal);
    }

    /**
     * Show 404 page.
     */
    private function notFound(): void
    {
        http_response_code(404);
        $errorView = APP_PATH . '/views/errors/404.php';
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo '<h1>404 — Page Not Found</h1>';
        }
        exit;
    }
}
