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
    public function read()
    {
        $this->_inputBuffer = explode($this->_delimiter, $this->_rawInput);
    }

    /**
     * @inheritDoc
     */
    public function write()
    {
        $this->_outputBuffer = []; //asuer it's empty
    }

    /**
     * @inheritDoc
     */
    public function writeString(string $str)
    {
        $this->_outputBuffer[] = $str;
    }


    /**
     * @inheritDoc
     */
    public function writeShort(int $s)
    {
        $this->_outputBuffer[] = $s;
    }

    /**
     * @inheritDoc
     */
    public function writeInteger(int $i)
    {
        $this->_outputBuffer[] = $i;
    }

    /**
     * @inheritDoc
     */
    public function writeBoolean(bool $b)
    {
        $this->_outputBuffer[] = $b;
    }

    /**
     * @inheritDoc
     */
    public function writeByte(int $byte)
    {
        $this->_outputBuffer[] = $byte;
    }

    /**
     * @inheritDoc
     */
    public function writeByteArray(array $bytes)
    {
        $this->_outputBuffer[] = array_merge($this->_outputBuffer, $bytes);
    }

    /**
     * @inheritDoc
     */
    public function readString() : string
    {
        return $this->_inputBuffer[$this->_i++];
    }

    /**
     * @inheritDoc
     */
    public function readShort() : int
    {
        return (int)$this->_inputBuffer[$this->_i++];
    }

    /**
     * @inheritDoc
     */
    public function readInteger() : int
    {
        return (int)$this->_inputBuffer[$this->_i++];
    }

    /**
     * @inheritDoc
     */
    public function readBoolean() : bool
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
    public function readByte() : int
    {
        return (int)$this->_inputBuffer[$this->_i++];
    }

    /**
     * @inheritDoc
     */
    public function readByteArray(int $legnth) : array
    {
        $arr = [];

        for($i = 0; $i < $length; $i++) {
            $arr[] = $this->_inputBuffer[$this->_i++];
        }

        return $arr;
    }
}
