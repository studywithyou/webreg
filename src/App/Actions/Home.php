<?php
namespace App\Actions;

use Aura\View\ViewFactory;
use Aura\Web\Request;
use Aura\Web\Response;

class Home
{
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function __invoke()
    {
        $view_factory = new ViewFactory();
        $view = $view_factory->newInstance();
        $view_registry = $view->getViewRegistry();
        $view_registry->set('home', 'templates/home.php');
        $this->response->content->set($view());
    }
}
