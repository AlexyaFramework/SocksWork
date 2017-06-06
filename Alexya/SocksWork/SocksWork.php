<?php
namespace Alexya\SocksWork;

use \Exception;

/**
 * SocksWork class.
 * ================
 *
 * This class provides an easy way to connect to a server and send
 * packets.
 *
 * The constructor accepts as parameter the host of the server, the port and
 * the timeout of the connection.
 * It also accepts as 4th parameter a boolean that indicates if SocksWork should connect to the
 * server once the constructor has finished or not, by default it's set to true, however if
 * you set it to false you'll need to call the `\Alexya\SocksWork\SocksWork::connect` method.
 *
 * Example:
 *
 * ```php
 * $SocksWork = new SocksWork("localhost", 8080, 100); // Connects to localhost:8080 and sets a timeout of 100ms
 * ```
 *
 * Once the connection has been established you can send anything with the `\Alexya\SocksWork\SocksWork::send` command
 * that accepts as parameter the binary data to send or an instance of `\Alexya\SocksWork\PacketBuilder`.
 * If the parameter is binary data the response will be set to the `\Alexya\SocksWork\SocksWork::$response` property,
 * if it's a `\Alexya\SocksWork\PacketBuilder` instance the response will be sent directly to it's property.
 * The method also accepts a 2nd parameter that is a boolean that indicates whether if SocksWork should wait
 * for the response or not.
 *
 * Example:
 *
 * ```php
 * $SocksWork->send((binary) "Hello world!");
 * echo $SocksWork->response; // Response to the packet
 *
 * $packet = new PacketBuilder();
 * $packet->writeString("message", "Hello World!");
 *
 * $SocksWork->send($packet);
 *
 * echo $packet->readString("response"); // Response to the packet
 * ```
 *
 * To close the connection simply call the method `\Alexya\SocksWork\SocksWork::close` and when you
 * want to reconnect call the method `\Alexya\SocksWork\SocksWork::connect`, to see if SocksWork is
 * already connected use the method `\Alexya\SocksWork\SocksWork::isConnected`.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class SocksWork
{
    /**
     * Response data.
     *
     * If the packet has been sent with a PacketBuilder this
     * property will be null.
     *
     * @var string
     */
    public $response = null;

    /**
     * Hostname or ip.
     *
     * @var string
     */
    private $_host = "localhost";

    /**
     * Host port.
     *
     * @var int
     */
    private $_port = 8080;

    /**
     * Connection timeout.
     *
     * @var int
     */
    private $_timeout = 100;

    /**
     * Socket.
     *
     * @var resource
     */
    private $_connection = null;

    /**
     * Max buffer for server response.
     *
     * @var int
     */
    private $_maxInputBuffer = 2048;

    /**
     * Constructor.
     *
     * @param string  $host    Host name.
     * @param int     $port    Server port.
     * @param int     $timeout Timeout in ms to wait for connection to establish.
     * @param bool    $connect Whether to connect to the server directly or not.
     */
    public function __construct(string $host, int $port, int $timeout, bool $connect = true)
    {
        $this->_host    = $host;
        $this->_port    = $port;
        $this->_timeout = $timeout;

        if($connect) {
            $this->connect();
        }
    }

    /**
     * Builds and returns a socket with a give $timeout connection.
     *
     * @see https://gist.github.com/brianlmoon/442310033bf44565bddd for more info.
     *
     * @throws Exception If connection timed out or connection failed.
     */
    public function connect() : void
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        //socket_set_block($socket);

        /**
         * Set the send and receive timeouts super low so that socket_connect
         * will return to us quickly. We then loop and check the real timeout
         * and check the socket error to decide if its connected yet or not.

        $connect_timeval = [
            "sec"  => 0,
            "usec" => 100
        ];*/

        //socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, $connect_timeval);
        //socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $connect_timeval);
        $now = microtime(true);

        /**
         * Loop calling socket_connect. As long as the error is 115 (in progress)
         * or 114 (already called) and our timeout has not been reached, keep
         * trying.
         */
        $err              = 115;
        $socket_connected = false;
        $elapsed          = 0;
        while(($err === 115 || $err === 114) && $elapsed < $this->_timeout) {
            socket_clear_error($socket);
            $socket_connected = socket_connect($socket, $this->_host, $this->_port);

            $elapsed = (microtime(true) - $now) * 1000;
            $err     = socket_last_error($socket);
        }

        //socket_set_block($socket);
        /**
         * For some reason, socket_connect can return true even when it is
         * not connected. Make sure it returned true the last error is zero
         */
        $socket_connected = $socket_connected && $err === 0;

        if(!$socket_connected) {
            $elapsed = round($elapsed, 4);
            if(
                !is_null($err) &&
                $err !== 0     &&
                $err !== 114   &&
                $err !== 115
            ) {
                throw new Exception("Connection to ". $this->_host.":".$this->_port ." failed: ". $err);
            }

            throw new Exception("Connection to ". $this->_host.":". $this->_port ." timed out (". $elapsed .").");
        }

        /**
         * Set keep alive on so the other side does not drop us
         */
        //socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE, 1);

        /**
         * set the real send/receive timeouts here now that we are connected
         */
        $timeval = [
            "sec"  => 0,
            "usec" => 0
        ];

        if($this->_timeout >= 1000) {
            $ts_seconds = $this->_timeout / 1000;

            $timeval["sec"]  = floor($ts_seconds);
            $timeval["usec"] = ($ts_seconds - $timeval["sec"]) * 1000000;
        } else {
            $timeval["usec"] = $this->_timeout * 1000;
        }

        //socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, $timeval);
        //socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $timeval);

        //socket_set_block($socket);

        $this->_connection = $socket;
    }

    /**
     * Checks whether if SocksWork is connected to the server or not.
     *
     * @return bool Whether SocksWork is connected or not.
     */
    public function isConnected() : bool
    {
        return !empty($this->_connection);
    }

    /**
     * Closes the current connection.
     */
    public function close() : void
    {
        $this->_connection = null;
    }

    /**
     * Sends a packet (or binary data) to the server.
     *
     * @param PacketBuilder $packet       Packet to send.
     * @param bool          $readResponse Whether to read the response or not.
     *
     * @throws Exception If couldn't read response.
     */
    public function send(PacketBuilder $packet, bool $readResponse = true) : void
    {
        // Assure socket is connected
        if(!$this->isConnected()) {
            return;
        }

        $write = $packet->encoder->getOutputBufferAsString();

        // Check packet isn't empty
        if(
            empty($write) &&
            strlen($write) <= 0
        ) {
            return;
        }

        // write length and bytes
        socket_send($this->_connection, $write, strlen($write), MSG_EOR);
        //socket_write($this->_connection, $write, strlen($write));

        if(
            !($packet instanceof PacketBuilder) ||
            !$readResponse
        ) {
            return;
        }

        $inputBuffer = "";
        if(0 === ($bytes = socket_recv($this->_connection, $inputBuffer, $this->_maxInputBuffer, MSG_WAITALL))) {
            throw new Exception("Couldnt' read response: ". socket_last_error($this->_connection));
        }

        if($inputBuffer == null) {
            throw new Exception("Couldn't read response: ". socket_last_error($this->_connection));
        }

        $packet->encoder->setInputBuffer($inputBuffer);
        $packet->encoder->read();
        $packet->onResponse();

        if(!$this->isConnected()) {
            $this->connect();
        }
    }
}
