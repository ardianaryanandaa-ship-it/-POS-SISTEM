<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\MetaModel;
use App\Models\ContactModel; 

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 *     class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */

    protected $session;
    protected $helpers = ['url', 'form', 'session', 'auth', 'app'];
    protected $meta; 
    protected $contact;
    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Load here all helpers you want to be available in your controllers that extend BaseController.
        // Caution: Do not put the this below the parent::initController() call below.
        // $this->helpers = ['form', 'url'];

        // Caution: Do not edit this line.
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');

        // 1. Load Session Service
        $this->session = \Config\Services::session();

        // 2. PROTEKSI SESSION (Auto Redirect)
        // Ambil nama controller yang sedang diakses
        $router = service('router');
        $controllerName = $router->controllerName();

        // Jika user mengakses halaman SELAIN AuthController dan BELUM login
        if ($controllerName !== '\App\Controllers\AuthController') {
            if (!$this->session->get('logged_in')) {
                // Gunakan header redirect agar eksekusi berhenti total
                header('Location: ' . base_url('login'));
                exit; 
            }
        }

        $metaModel = new MetaModel();
        $this->meta = $metaModel->getMetaArray();

        $contactModel = new ContactModel();
        $this->contact = $contactModel->getContactArray();

        $renderer = service('renderer');
    $renderer->setVar('meta', $this->meta);
    $renderer->setVar('contact', $this->contact);
    }
}
