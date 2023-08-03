<?php
namespace Core;

use App\Auth;
use App\Flash;
/**
 * [Class base Controller]
 */
abstract class BaseController {
    protected $routeParams = [];

    public function __construct(array $routeParams) {
        $this->routeParams = $routeParams;
    }

    /**
     * @param string $name
     * @param array $args
     * 
     * @return void
     */
    public function __call(string $name, array $args) :void {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            //echo "Method $method not found in controller" . get_class($this);
            throw new \Exception("Method $method not found in controller" . get_class($this));
        }
    }

    /**
     * call before action method
     * 
     * @return void
     */
    protected function before() {}

    /**
     * call after action method
     * 
     * @return void
     */
    protected function after() {}

    /**
     * @param string $url
     * 
     * @return void
     */
    public function redirect(string $url) :void {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }

    /**
     * @return void
     */
    public function requiredLogin() {
        if (! Auth::getUser()) {
            Flash::addMessage('Please login to access this page', Flash::INFO);
            Auth::rememberRequestedPage();
            $this->redirect('/login');
        }
    }

}