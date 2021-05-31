<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    
    protected $connections;
    protected $clients;
    protected $MESSAGE_TYPE = ["chat", "ping"];
    // $numConnections = count($this->connections);

    public function __construct()
    {
        $this->connections = new \SplObjectStorage;
        $this->clients = [];
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // ping connection for session data
        $this->pingClient($conn);

        $this->connections->attach($conn);

        echo "new connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $data)
    {
        list ($messageType, $dataObj) = $this->parseData($data);

        switch($messageType)
        {
            case $this->MESSAGE_TYPE[1]:
                // append connection to client, then if connection is a new user, notify all clients
                $this->handlePingResponse($dataObj);
                break;
            case $this->MESSAGE_TYPE[0]:
                $this->handleChatFromClient($from, $data);
                break;
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->connections->detach($conn);

        $this->handleDisconnection($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occured: {$e->getMessage()}\n";
        $conn->close();
    }

    // HELPER FUNCTIONS:
    private function parseData($stringifiedData)
    {
        $dataObj = json_decode($stringifiedData);
        $messageType = $dataObj->type;
        return array ($messageType, $dataObj);
    }

    private function pingClient(ConnectionInterface $conn)
    {
        $pingData["type"] = "ping";
        $pingData["resourceId"] = $conn->resourceId;
        $conn->send(json_encode($pingData));
    }

    private function handlePingResponse($dataObj)
    {
        $username = $dataObj->username;
        $resourceId = $dataObj->resourceId;
        
        // Step 1) check if username already exists in clients 
        $connectionIsNewUser = !array_key_exists($username, $this->clients);
        
        // Step 2) add the username-resourceID pair to clients
        $this->clients[$username][] = $resourceId;
        
        // if ($connectionIsNewUser)
        // {
            // notify clients of modified clients key-list
            $connectData["type"] = "connect";
            $connectData["activeUsers"] = array_keys($this->clients);
            
            foreach($this->connections as $connection)
            {
                $connection->send(json_encode($connectData));
            }
        // }
        
        echo "appended connection to client\n";
        var_dump($this->clients);
        echo "\n";
    }

    private function handleDisconnection(ConnectionInterface $conn)
    {
        $connectionID = $conn->resourceId;

        // search for whose client the connection belongs to
        foreach ($this->clients as $client => $connectionIDs)
        {
            if (in_array ($connectionID, $connectionIDs))
            {
                // remove $connectionID from $connectionIDs subarray
                $k = array_search($connectionID, $connectionIDs);
                unset($this->clients[$client][$k]);
                $this->clients[$client] = array_values($this->clients[$client]); // re-index subarray
                
                if (count($this->clients[$client]) == 0)
                {
                    // remove the client from clients attribute
                    unset($this->clients[$client]);

                    // notify clients of modified clients key-list
                    $disconnectData["type"] = "disconnect";
                    $disconnectData["activeUsers"] = array_keys($this->clients);
                    
                    foreach ($this->connections as $connection)
                    {
                        $connection->send(json_encode($disconnectData));
                    }
                }
                break;
            }
        }
    }

    private function handleChatFromClient(ConnectionInterface $from, $data)
    {
        foreach ($this->connections as $connection)
        {
            if ($from !== $connection)
            {
                $connection->send($data);
            }
            else if ($from == $connection)
            {
                $connection->send($data);
            }
        }
        echo "sent message\n";
    }
}
?>