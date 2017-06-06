<?php
namespace Alexya\SocksWork\Encoders;

use \Alexya\SocksWork\Encoder;

/**
 * String encoder.
 *
 * This encoder simply joins all parameters in a string separated by the delimiter sent
 * to the constructor, if no delimiter has been specified it will be "|".
 *
 * Example:
 *
 *     $packet = new PacketBuilder(new String());
 *     $packet->writeShort(1);
 *     $packet->writeInt(123456);
 *     $packet->writeString("test");
 *
 *     echo $packet->getOutputBufferAsString();
 *     // 1|123456|test
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class StringEncoder extends Encoder
{
    /**
     * Pointer for looping through the buffers.
     *
     * @var int
     */
    protected $_i = 0;

    /**
     * Delimiter.
     *
     * @var string
     */
    protected $_delimiter = "|";

    /**
     * Constructor.
     *
     * @param string $delimiter Delimiter, empty = "|".
     */
    public function __construct(string $delimiter = "|")
    {
        if(!empty($delimiter)) {
            $this->_delimiter = $delimiter;
        }
    }

    /**
     * @inheritDoc
     */
    public function getOutputBufferAsString() : string
    {
        return implode($this->_delimiter, $this->_outputBuffer);
    }

    /**
     * @inheritDoc
     */
    public function read() : void
    {
        $this->_inputBuffer = explode($this->_delimiter, $this->_rawInput);
    }

    /**
     * @inheritDoc
     */
    public function write() : void
    {
        $this->_outputBuffer = []; //assure it's empty
    }

    /**
     * @inheritDoc
     */
    public function writeString(string $str, string $name = "") : void
    {
        $this->_outputBuffer[] = $str;
    }


    /**
     * @inheritDoc
     */
    public function writeShort(int $s, string $name = "") : void
    {
        $this->_outputBuffer[] = $s;
    }

    /**
     * @inheritDoc
     */
    public function writeInteger(int $i, string $name = "") : void
    {
        $this->_outputBuffer[] = $i;
    }

    /**
     * @inheritDoc
     */
    public function writeBoolean(bool $b, string $name = "") : void
    {
        $this->_outputBuffer[] = $b;
    }

    /**
     * @inheritDoc
     */
    public function writeByte(int $byte, string $name = "") : void
    {
        $this->_outputBuffer[] = $byte;
    }

    /**
     * @inheritDoc
     */
    public function writeByteArray(array $bytes, string $name = "") : void
    {
        $this->_outputBuffer[] = array_merge($this->_outputBuffer, $bytes);
    }

    /**
     * @inheritDoc
     */
    public function readString(string $name = "") : string
    {
        return $this->_inputBuffer[$this->_i++];
    }

    /**
     * @inheritDoc
     */
    public function readShort(string $name = "") : int
    {
        return (int)$this->_inputBuffer[$this->_i++];
    }

    /**
     * @inheritDoc
     */
    public function readInteger(string $name = "") : int
    {
        return (int)$this->_inputBuffer[$this->_i++];
    }

    /**
     * @inheritDoc
     */
    public function readBoolean(string $name = "") : bool
    {
        $b = $this->_inputBuffer[$this->_i++];

        if(
            $b == 0 ||
            $b == "false"
        ) {
            return false;
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function readByte(string $name = "") : int
    {
        return (int)$this->_inputBuffer[$this->_i++];
    }

    /**
     * @inheritDoc
     */
    public function readByteArray(int $length, string $name = "") : array
    {
        $arr = [];

        for($i = 0; $i < $length; $i++) {
            $arr[] = $this->_inputBuffer[$this->_i++];
        }

        return $arr;
    }
}
