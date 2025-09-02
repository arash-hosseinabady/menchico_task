<?php
declare(strict_types=1);

namespace App\Error;

use Cake\Controller\Controller;
use Cake\Error\Renderer\WebExceptionRenderer;
use Cake\View\JsonView;

class ApiExceptionRenderer extends WebExceptionRenderer
{
    /**
     * @return \Cake\Controller\Controller
     */
    protected function _getController(): Controller
    {
        $controller = parent::_getController();

        $req = $this->request;
        $wantsJson = $req && (
                $req->is('json')
                || strtolower((string)$req->getParam('prefix')) === 'api'
                || str_contains($req->getHeaderLine('Accept'), 'application/json')
            );

        if ($wantsJson) {
            $controller->addViewClasses([JsonView::class]);
            $controller->viewBuilder()->setClassName(JsonView::class);
            $controller->viewBuilder()->setOption('serialize', true);
        }

        return $controller;
    }
}
