<?php
namespace App\Onset\Controller;

use Slim\Container;
use App\Onset\Room;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Symfony\Component\Translation\Translator;
use Illuminate\Validation\{
    Validator,
    Factory
};

class RoomController extends Controller
{
    private $room;

    public function __construct(Container $c)
    {
        parent::__construct($c);
        $this->room = new Room($this->container, $this->onset);
    }

    public function create(Request $request, Response $response, $args)
    {
        $result = $this->room->create($request->getParams());
        return $response->withJson($result);
    }

    public function remove(Request $request, Response $response, $args)
    {
        $result = $this->room->remove($request->getParams());
        return $response->withJson($result);
    }

    public function enter(Request $request, Response $response, $args)
    {
        $result = $this->room->enter($request->getParams());
        return $response->withJson($result);
    }

    public function autoremove(Request $request, Response $response, $args)
    {
        $result = $this->room->autoremove($request->getParams());
        return $response->withJson($result);
    }

    public function read(Request $request, Response $response, $args)
    {
        $result = $this->room->read($request->getParams());
        return $response->withJson($result);
    }

    public function write(Request $request, Response $response, $args)
    {
        $result = $this->room->write($request->getParams());
        return $response->withJson($result);
    }

    public function logout(Request $request, Response $response, $args)
    {
        session_destroy();
        return $response->withRedirect('/');
    }

    public function logput(Request $request, Response $response, $args)
    {
        if (!isset($_SESSION['onset_roomid'])) {
            $response = $response->withRedirect('/room/logout');
        } else {
            $response = $response
                ->write($this->room->getChatLog());
        }
        return $response;
    }

    public function users(Request $request, Response $response, $args)
    {
        // $result = $this->room->users();
        return $response
            ->write($this->room->users($request->getParams()));
    }
}
