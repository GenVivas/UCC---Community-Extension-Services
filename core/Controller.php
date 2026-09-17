<?php

/**
 * Base Controller
 * Provides view loading, redirects, and JSON responses.
 * All controllers extend this class.
 */
class Controller
{
    /**
     * Render a view file within the main layout.
     *
     * @param string $view   Path relative to app/views/ (e.g. 'dashboard/admin')
     * @param array  $data   Variables to extract into the view
     * @param string $layout Layout file name in app/views/layouts/ (default: main)
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        // Make data variables available in the view
        extract($data, EXTR_SKIP);

        $viewFile   = APP_PATH . "/views/{$view}.php";
        $layoutFile = APP_PATH . "/views/layouts/{$layout}.php";

        if (!file_exists($viewFile)) {
            $this->abort(404, "View not found: {$view}");
        }

        // Capture view content
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render inside layout
        require $layoutFile;
    }

    /**
     * Render a view without any layout (for partials or AJAX responses).
     */
    protected function viewOnly(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $viewFile = APP_PATH . "/views/{$view}.php";

        if (!file_exists($viewFile)) {
            $this->abort(404, "View not found: {$view}");
        }

        require $viewFile;
    }

    /**
     * Redirect to a URL relative to BASE_URL.
     *
     * @param string $path e.g. 'auth/login' or 'proposals/create'
     */
    protected function redirect(string $path = ''): void
    {
        $url = rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
        header("Location: {$url}");
        exit;
    }

    /**
     * Send a JSON response (for AJAX endpoints).
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Send a success JSON response.
     */
    protected function jsonSuccess(string $message, array $data = []): void
    {
        $this->json(array_merge(['success' => true, 'message' => $message], $data));
    }

    /**
     * Send an error JSON response.
     */
    protected function jsonError(string $message, int $code = 400): void
    {
        $this->json(['success' => false, 'message' => $message], $code);
    }

    /**
     * Abort with an HTTP error page.
     */
    protected function abort(int $code = 404, string $message = 'Not Found'): void
    {
        http_response_code($code);
        $errorView = APP_PATH . "/views/errors/{$code}.php";
        if (file_exists($errorView)) {
            require $errorView;
        } else {
            echo "<h1>Error {$code}</h1><p>{$message}</p>";
        }
        exit;
    }

    /**
     * Validate CSRF token from POST data.
     * Aborts with 403 on failure.
     */
    protected function validateCsrf(): void
    {
        $token = $_POST['_csrf'] ?? '';
        if (!Session::validateCsrf($token)) {
            $this->abort(403, 'Invalid CSRF token.');
        }
    }

    /**
     * Check if the current request is a POST.
     */
    protected function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Safely get a POST value with optional default.
     */
    protected function input(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
    }

    /**
     * Safely get a GET value with optional default.
     */
    protected function query(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
    }
}
