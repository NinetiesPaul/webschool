<?php

namespace App\DB\Storage;

use App\DB\DB;
use PDO;
use Pecee\SimpleRouter\SimpleRouter;

class LogStorage
{
    protected $db;

    public function __construct()
    {
        $this->db = new DB();
        $this->logRequest();
    }

    public function logRequest()
    {
        session_start();
        $user = $_SESSION['user'];

        if (SimpleRouter::request()->getMethod() !== 'get') {

            $request = [
                'origin' => SimpleRouter::request()->getIp(),
                'method' => SimpleRouter::request()->getMethod(),
                'uri' => SimpleRouter::request()->getUrl(),
                'body' => $_POST
            ];

            $log = $this->db->prepare("INSERT INTO logrequest (user, request, request_data)
                VALUES (:log, :request, NOW())");

            $log->execute([
                'log' => json_encode($user),
                'request' => json_encode($request),
            ]);
        }
    }

}
