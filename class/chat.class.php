<?php

namespace MyChat;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface
{
    protected $clients;
    private $subscriptions;
    private $users;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
        $this->users = [];
        $this->subscriptions = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->clients->attach($conn);
        $this->users[$conn->resourceId] = $conn;
        echo "New connection! ({$conn->resourceId})\n";

        

        $data['command'] = 'open';
        $data['message'] = $conn->resourceId;
        $data = json_encode($data);
        $this->onMessage($conn, $data);

        $this->notification($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {

        /*
        $numRecv = count($this->clients) - 1;
        echo sprintf(
            'Connection %d sending message "%s" to %d other connection%s' . "\n",
            $from->resourceId,
            $msg,
            $numRecv,
            $numRecv == 1 ? '' : 's'
        );


        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // O remetente não é o destinatário, envie para cada cliente conectado
                $client->send($msg);
            }
        }
        */


        $data = json_decode($msg);

        switch ($data->command) {


            case "open":
                $message['id'] = $data->message;
                $message = json_encode($message);
                $this->users[$from->resourceId]->send($message);

                break;
            case "notification":

                $message = json_encode($data->message);

                foreach ($this->users as $key => $users) {
                    $this->users[$key]->send($message);
                }

                break;
            case "subscribe":
                $this->subscriptions[$from->resourceId] = $data->channel;
                echo "{$from->resourceId} Subscribe in new channel ({$data->channel})\n";
                break;
            case "message":
                if (isset($this->subscriptions[$from->resourceId])) {
                    $target = $this->subscriptions[$from->resourceId];
                    foreach ($this->subscriptions as $id => $channel) {
                        if ($channel == $target && $id != $from->resourceId) {

                            $response['message'] = $data->message;
                            $response = json_encode($response);

                            $this->users[$id]->send($response);
                        }
                    }
                }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // A conexão está fechada, remova-a, pois não podemos mais enviar mensagens
        $this->clients->detach($conn);
        unset($this->users[$conn->resourceId]);
        unset($this->subscriptions[$conn->resourceId]);

        echo "Connection {$conn->resourceId} has disconnected\n";
        $this->notification($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

    public function notification($conn)
    {
        $message['settings']['total_users'] = count($this->users);
        $data['command'] = 'notification';
        $data['message'] = $message;
        $data = json_encode($data);
        $this->onMessage($conn, $data);
    }
}
