<?php
/**
 * Created by PhpStorm.
 * User: Giansalex
 * Date: 28/08/2017
 * Time: 19:50
 */

namespace Sufel\App\Controllers;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Sufel\App\Repository\DocumentRepository;

/**
 * Class ClientController
 * @package Sufel\App\Controllers
 */
class ClientController
{
    /**
     * @var DocumentRepository
     */
    private $repository;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * CompanyController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->repository = $container->get(DocumentRepository::class);
        $this->rootDir = $container->get('settings')['upload_dir'];
    }

    /**
     * @param ServerRequestInterface    $request
     * @param Response                  $response
     * @param array $args
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getDocument($request, $response, $args)
    {
        $tipo = $args['tipo'];
        $jwt = $request->getAttribute('jwt');
        $id = $jwt->doc;

        $doc = $this->repository->get($id);
        if ($doc === null) {
            return $response->withStatus(404);
        }

        $name = $doc['name'];
        $path = $this->rootDir . DIRECTORY_SEPARATOR . $doc['emisor'] . DIRECTORY_SEPARATOR . $name;

        if ($tipo == 'pdf' || $tipo  == 'xml') {
            $doc = [];
        }

        if ($tipo == 'xml' || $tipo == 'all') {
            $doc['xml'] = file_get_contents($path . '.xml');
        }

        if ($tipo == 'pdf' || $tipo == 'all') {
            $doc['pdf'] = file_get_contents($path . '.pdf');
        }

        return $response->withJson($doc);
    }
}