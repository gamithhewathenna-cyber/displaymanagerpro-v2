<?php
/**
 * Router – simple pattern-based URL router
 */
class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $pattern, string $controller, string $method, array $middleware = []): void
    {
        $this->add('GET', $pattern, $controller, $method, $middleware);
    }

    public function post(string $pattern, string $controller, string $method, array $middleware = []): void
    {
        $this->add('POST', $pattern, $controller, $method, $middleware);
    }

    private function add(string $httpMethod, string $pattern, string $controller, string $action, array $middleware): void
    {
        $this->routes[] = compact('httpMethod', 'pattern', 'controller', 'action', 'middleware');
    }

    public function dispatch(string $uri, string $httpMethod): void
    {
        // Strip query string
        $uri = strtok($uri, '?');
        $uri = '/' . trim($uri, '/');

        // HEAD requests are answered like GET, but with the body discarded
        $isHead      = strtoupper($httpMethod) === 'HEAD';
        $matchMethod = $isHead ? 'GET' : strtoupper($httpMethod);

        foreach ($this->routes as $route) {
            if ($route['httpMethod'] !== $matchMethod) continue;

            $pattern = $this->patternToRegex($route['pattern']);
            if (preg_match($pattern, $uri, $matches)) {
                // Run middleware
                foreach ($route['middleware'] as $mw) {
                    (new $mw())->handle();
                }
                array_shift($matches);
                $ctrl = new $route['controller']();
                if ($isHead) ob_start();
                call_user_func_array([$ctrl, $route['action']], $matches);
                if ($isHead) ob_end_clean();
                return;
            }
        }

        // 404
        http_response_code(404);
        $ctrl = new ErrorController();
        $ctrl->notFound();
    }

    private function patternToRegex(string $pattern): string
    {
        $pattern = preg_replace('/\{([a-z_]+)\}/', '([^/]+)', $pattern);
        return '#^' . $pattern . '$#i';
    }
}

/**
 * Base Controller
 */
abstract class BaseController
{
    protected function view(string $template, array $data = [], ?string $layout = 'default'): void
    {
        extract($data);
        if ($layout) {
            $content_view = $template;
            require VIEWS_PATH . '/layouts/' . $layout . '.php';
        } else {
            require VIEWS_PATH . '/' . $template . '.php';
        }
    }

    protected function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header('Location: ' . $url);
        exit;
    }

    protected function abort(int $code = 403, string $message = 'Forbidden'): void
    {
        http_response_code($code);
        die($message);
    }

    protected function currentUser(): ?array
    {
        return Session::get('user') ?? null;
    }

    protected function requireAuth(): void
    {
        if (!Session::get('user')) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $user = $this->currentUser();
        if (!$user || $user['role'] !== 'admin') {
            $this->abort(403);
        }
    }

    protected function csrfField(): string
    {
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . Csrf::token() . '">';
    }

    protected function validateCsrf(): void
    {
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!Csrf::verify($token)) {
            $this->abort(419, 'CSRF token mismatch.');
        }
    }
}

/**
 * Base Model
 */
abstract class BaseModel
{
    protected static string $table = '';

    public static function find(int $id): ?array
    {
        return Database::fetchOne('SELECT * FROM `' . static::$table . '` WHERE id = ?', [$id]);
    }

    public static function findBy(string $column, mixed $value): ?array
    {
        return Database::fetchOne(
            'SELECT * FROM `' . static::$table . '` WHERE `' . $column . '` = ? LIMIT 1',
            [$value]
        );
    }

    public static function all(string $orderBy = 'id DESC'): array
    {
        return Database::fetchAll('SELECT * FROM `' . static::$table . '` ORDER BY ' . $orderBy);
    }

    public static function where(array $conditions, string $orderBy = 'id DESC', ?int $limit = null): array
    {
        $clauses = [];
        $params  = [];
        foreach ($conditions as $col => $val) {
            $clauses[] = '`' . $col . '` = ?';
            $params[]  = $val;
        }
        $sql = 'SELECT * FROM `' . static::$table . '` WHERE ' . implode(' AND ', $clauses) . ' ORDER BY ' . $orderBy;
        if ($limit) $sql .= ' LIMIT ' . (int) $limit;
        return Database::fetchAll($sql, $params);
    }

    public static function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            return (int) Database::fetchOne('SELECT COUNT(*) as c FROM `' . static::$table . '`')['c'];
        }
        $clauses = [];
        $params  = [];
        foreach ($conditions as $col => $val) {
            $clauses[] = '`' . $col . '` = ?';
            $params[]  = $val;
        }
        $sql = 'SELECT COUNT(*) as c FROM `' . static::$table . '` WHERE ' . implode(' AND ', $clauses);
        return (int) Database::fetchOne($sql, $params)['c'];
    }

    public static function delete(int $id): bool
    {
        return Database::execute('DELETE FROM `' . static::$table . '` WHERE id = ?', [$id]) > 0;
    }
}
