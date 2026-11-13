<?php
/**
 * Router sederhana berbasis regex.
 *
 * Contoh pola:
 *   /artikel                -> literal
 *   /artikel/{id:\d+}       -> parameter terbatas angka
 *   /admin/tempat/{id}/menu/{menu}/edit -> parameter bebas
 */
class Router
{
    /** @var array<string, array<int, array{0:string,1:array,2:string}>> */
    private array $routes = [];

    public function get(string $pattern, array $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    public function post(string $pattern, array $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    private function add(string $method, string $pattern, array $handler): void
    {
        $regex = preg_replace_callback(
            '#\{(\w+)(?::([^}]+))?\}#',
            static fn(array $m): string => '(?P<' . $m[1] . '>' . ($m[2] ?? '[^/]+') . ')',
            $pattern
        );
        $this->routes[$method][] = ['#^' . $regex . '$#', $handler, $pattern];
    }

    public function dispatch(string $uri, string $method): void
    {
        $path = (string) parse_url($uri, PHP_URL_PATH);

        // Pangkas base URL (mis. /healthyhabit)
        if (BASE_URL !== '' && strpos($path, BASE_URL) === 0) {
            $path = substr($path, strlen(BASE_URL));
        }
        $path = '/' . trim($path, '/');

        $method = strtoupper($method);
        $match  = $this->match($method, $path);
        if (!$match && $method === 'HEAD') {
            $match = $this->match('GET', $path);
        }

        if (!$match) {
            (new HomeController())->notFound();
            return;
        }

        // Seluruh request POST wajib lolos verifikasi CSRF
        if ($method === 'POST') {
            verify_csrf();
        }

        [$handler, $params] = $match;
        [$class, $action]   = $handler;

        if (!class_exists($class) || !method_exists($class, $action)) {
            (new HomeController())->notFound();
            return;
        }

        $controller = new $class();
        $controller->$action(...array_values($params));
    }

    private function match(string $method, string $path): ?array
    {
        foreach ($this->routes[$method] ?? [] as [$regex, $handler]) {
            if (preg_match($regex, $path, $m)) {
                // Hanya parameter bernama yang diteruskan
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                return [$handler, $params];
            }
        }
        return null;
    }
}
