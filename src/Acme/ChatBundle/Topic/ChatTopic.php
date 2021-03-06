<?php

namespace Acme\ChatBundle\Topic;

use Acme\ChatBundle\Client\ClientStorage;
use Ratchet\ConnectionInterface as Conn;
use JDare\ClankBundle\Server\App\Handler\TopicHandlerInterface;

class ChatTopic implements TopicHandlerInterface
{

    /**
     * @var ClientStorage $clientStorage
     */
    private $clientStorage;

    function __construct(ClientStorage $clientStorage)
    {
        $this->clientStorage = $clientStorage;
    }

    /**
     * Announce to this topic that someone else has joined the chat room
     *
     * Also, set their nickname to Guest if it doesnt exist.
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     */
    public function onSubscribe(Conn $conn, $topic)
    {
        $client = $this->clientStorage->getClient($conn);

        $msg =  $client->getName() . " joined the chat room.";

        $topic->broadcast(array("msg" => $msg, "from" => "System", "system" => true));
    }

    /**
     * Announce person left chat room
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     */
    public function onUnSubscribe(Conn $conn, $topic)
    {
        $client = $this->clientStorage->getClient($conn);

        $msg =  $client->getName() . " left the chat room.";

        $topic->broadcast(array("msg" => $msg, "from" => "System", "system" => true));
    }

    /**
     * Do some nice things like strip html/xss etc. then pass along the message
     *
     * @param \Ratchet\ConnectionInterface $conn
     * @param $topic
     * @param $event
     * @param array $exclude
     * @param array $eligible
     */
    public function onPublish(Conn $conn, $topic, $event, array $exclude, array $eligible)
    {
        $event = htmlentities($event); // removing html/js
        $client = $this->clientStorage->getClient($conn);

        $topic->broadcast(array("msg" => $event, "from" => $client->getName()));
    }
}
