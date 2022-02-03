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

        $data['command'] = 'notification';
        $data['message'] = "Conection ID: {$conn->resourceId}";
        $data = json_encode($data);
        $this->onMessage($conn, $data);
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

            case "notification":
                echo "Send notification to {$from->resourceId}\n";
                $this->users[$from->resourceId]->send($data->message);
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
                            $this->users[$id]->send($data->message);
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
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}
