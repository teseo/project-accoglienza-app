<?php
/**
 * Copyright (c) 2017. This file belongs to Misericordia di "Torre del lago Puccini"
 */

namespace App\Home\Actions;

use App\Operator\Repository\OperatorRepository;
use App\Validation\Validator;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Exceptions\EqualsException;
use Respect\Validation\Validator as v;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\RouterInterface;

/**
 * Class SaveNewPasswordAction
 * @package App\Home\Actions
 * @author Javier Mellado <sol@javiermellado.com>
 */
final class SaveNewPasswordAction
{
    /**
     * @var OperatorRepository
     */
    protected $operatorRepository;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var Validator
     */
    private $validator;
    /**
     * @var Messages
     */
    private $flash;

    /**
     * OperatorController constructor.
     * @param RouterInterface $router
     * @param Validator $validator
     * @param OperatorRepository $operatorRepository
     * @param Messages $flash
     * @internal param View $view
     */
    function __construct(
        RouterInterface $router,
        Validator $validator,
        OperatorRepository $operatorRepository,
        Messages $flash
    )
    {
        $this->router = $router;
        $this->flash = $flash;
        $this->operatorRepository = $operatorRepository;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Request $request, Response $response): ResponseInterface
    {

        try {
            //get token from url
            $token = $request->getAttribute('token');
//            /** @var  Operator $originalOperator */
//            $originalOperator = $this->operatorRepository->findById($id);


            $validation = $this->validator->validate($request, [
                'password1' => v::notEmpty()->length(2, 20),
                'password2' => v::notEmpty()->length(2, 20),
                v::keyValue('password1', 'equals', 'password2'),
            ]);


            var_export($_SESSION['errors']);
            var_export($request->getParams());
            die;
            //If validation fails, return to edit form with error messages embeded in the view
            if ($validation->failed()) {
                $this->flash->addMessage('error', 'Operator data is not correct');
                return $response->withRedirect($this->router->pathFor('edit-operator', ['token' => $token]));
            }

            $originalOperator->update($request->getParams());

            $this->flash->addMessage('info', 'Operator updated correctly');

            return $response->withRedirect($this->router->pathFor('home'));
        } catch (EqualsException $e) {

            $this->flash->addMessage('error', 'Passwords don\'t match');
            return $response->withRedirect($this->router->pathFor('enter-new-password', ['token' => $token]));
        } catch (\InvalidArgumentException $e) {
            return $response->withRedirect($this->router->pathFor('home'));
        }
    }
}