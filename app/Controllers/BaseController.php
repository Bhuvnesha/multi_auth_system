<?php namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Config\Services;

/**
 * Base Controller
 *
 * All controllers extend this class for common functionality
 */
class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var \CodeIgniter\HTTP\IncomingRequest
     */
    protected $request;

    /**
     * Instance of the main Response object.
     *
     * @var \CodeIgniter\HTTP\ResponseInterface
     */
    protected $response;

    /**
     * Instance of the main CI\Services object.
     *
     * @var \Config\Services
     */
    protected $services;

    /**
     * An array of helpers that will be loaded.
     *
     * @var array
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you
     * want to make available.
     *
     * Example:  protected $session;
     *
     * @var array
     */
    protected $session;

    /**
     * Constructor.
     *
     * @param array ...$args Arguments from the route (if any).
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Load session
        $this->session = Services::session();

        // Load common helpers
        helper(['url', 'form', 'text', 'cookie']);
    }

    /**
     * Render a view with common data
     *
     * @param string $view View name
     * @param array $data Data to pass to view
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    protected function render(string $view, array $data = [])
    {
        // Add common data to all views
        $commonData = [
            'currentUser' => auth()->getCurrentUser(),
            'isLoggedIn' => auth()->isLoggedIn(),
            'session' => session(),
        ];

        $data = array_merge($commonData, $data);

        return view($view, $data);
    }

    /**
     * Get route arguments
     *
     * @return array
     */
    protected function getRouteArgs(): array
    {
        return $this->request->getArgs() ?? [];
    }

    /**
     * JSON response helper
     *
     * @param mixed $data
     * @param integer $statusCode
     * @return \CodeIgniter\HTTP\ResponseInterface
     */
    protected function jsonResponse($data, int $statusCode = 200)
    {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($data);
    }

    /**
     * Redirect back with input and errors
     *
     * @param array $errors Validation errors
     * @param array $old Old input data (defaults to $_POST)
     * @return \CodeIgniter\HTTP\RedirectResponse
     */
    protected function redirectBackWithErrors(array $errors, array $old = null)
    {
        return redirect()->back()
            ->withInput($old)
            ->with('errors', $errors);
    }

    /**
     * Check if request is AJAX
     *
     * @return boolean
     */
    protected function isAjax(): bool
    {
        return $this->request->isAJAX();
    }
}
