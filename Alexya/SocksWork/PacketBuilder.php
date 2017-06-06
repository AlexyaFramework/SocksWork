<?php
namespace Alexya\SocksWork;
use Alexya\SocksWork\Encoders\StringEncoder;

/**
 * Packet builder class.
 * =====================
 *
 * This class provides an interface to build packets that will be sent
 * with `\Alexya\SocksWork\SocksWork`, it also provides methods for reading the response.
 *
 * The constructor accepts as parameter an object of type `\Alexya\SocksWork\IEncoder` that
 * will be the encoder to use to write and read the packet (By default it's an instance of
 * `\Alexya\SocksWork\Encoders\BigEndian`).
 *
 * Once the constructor has been called you need to can start adding parameters to the packet by calling
 * the methods of the encoder throw the instanced object.
 *
 * Example:
 *
 * ```php
 * $packet = new PacketBuilder();
 *
 * $packet->writeShort("id", 1);
 * $packet->writeString("name", "test");
 *
 * $SocksWork->send($packet);
 *
 * $id   = $packet->readShort("id");
 * $name = $packet->readString("name");
 * ```
 *
 * You can also extend this class for simplifying the instantiation of the object:
 *
 * ```
 * class SetUserName extends PacketBuilder
 * {
 *     private $_id = 1;
 *
 *     public $id   = -1;
 *     public $name = "";
 *
 *     public function onInstance(string $name) : void
 *     {
 *         $this->_encoder->writeShort("id", $this->_id);
 *         $this->_encoder->writeString("name", $name);
 *     }
 *
 *     public function onResponse() : void
 *     {
 *         $this->id   = $this->_encoder->readShort("id");
 *         $this->name = $this->_encoder->readString("name");
 *     }
 * }
 *
 * $packet = new SetUserName("test");
 *
 * $SocksWork->send($packet);
 *
 * $id   = $packet->id;
 * $name = $packet->name;
 * ```
 *
 * The method `\Alexya\SocksWork\PacketBuilder::onInstance` is executed right after the constructor
 * and receives as parameters the arguments sent to the constructor (without the instance of the encoder).
 * For example:
 *
 * ```php
 * class Packet extends PacketBuilder
 * {
 *     public function onInstance()
 *     {
 *         foreach(func_get_args() as $key => $val) {
 *             echo "{$key} => {$value}\n";
 *         }
 *     }
 * }
 *
 * $packet = new Packet(1, "test", "foo");
 * // Output:
 * // 0 => 1
 * // 1 => test
 * // 2 => foo
 *
 * $packet = new Packet(new \Alexya\SocksWork\Encoders\String(), 1, "test");
 * // Output:
 * // 0 => 1
 * // 1 => test
 * ```
 *
 * The method `\Alexya\SocksWork\PacketBuilder::onResponse` is executed right after the response has
 * been received.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class PacketBuilder
{
    /**
     * Encoder instance.
     *
     * @var Encoder
     */
    public $encoder = null;

    /**
     * Whether to read or not the response.
     *
     * @var bool
     */
    protected $_readResponse = true;

    /**
     * Constructor.
     */
    public function __construct()
    {
        foreach(func_get_args() as $arg) {
            if($arg instanceof Encoder) {
                $this->_encoder = $arg;
            }
        }

        if($this->_encoder == null) {
            $this->_encoder = new StringEncoder();
        }

        $this->onInstance();
    }

    /**
     * OnInstance method.
     *
     * Is executed right after the constructor.
     */
    public function onInstance() : void
    {

    }

    /**
     * OnResponse method.
     *
     * Is executed when SocksWork receives a response for this packet.
     */
    public function onResponse() : void
    {

    }

    /**
     * Magic method `__call`.
     *
     * Delivers all method calls to the encoder.
     *
     * @param string   $name Method name.
     * @param iterable $args Arguments.
     *
     * @return mixed The result of `$name` in `$this->_encoder`.
     */
    public function __call(string $name, iterable $args)
    {
        if(is_callable([$this->encoder, $name])) {
            $var = $this->encoder->{$name}(... array_values($args));

            return $var;
        } else if(is_callable([$this, $name])) {
            return $this->{$name}(... array_values($args));
        }

        return null;
    }
}
