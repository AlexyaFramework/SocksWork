<?php
namespace Alexya\SocksWork\Encoders;

use \Alexya\SocksWork\Encoder;

/**
 * JSON encoder.
 * =============
 *
 * Encodes the data from and to json.
 *
 * When using this encoder you must be sure to send a second parameter to the `read*` and `write*` functions
 * indicating the name of the parameter they're going to work.
 *
 * Example:
 *
 *     $packet = new PacketBuilder(new JSON());
 *     $packet->writeShort(1, "id");
 *     $packet->writeInt(123456, "user_id");
 *     $packet->writeString("test", "user_name");
 *
 *     echo $packet->getOutputBufferAsString();
 *     // {"id":1,"user_id":123456,"user_name":"test"}
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class JSON extends Encoder
{
    /**
     * @inheritdoc
     */
    public function getOutputBufferAsString() : string
    {
        return json_encode($this->_outputBuffer);
    }

    /**
     * @inheritdoc
     */
    public function read() : void
    {
        $this->_inputBuffer = json_decode($this->_rawInput);
    }

    /**
     * @inheritdoc
     */
    public function write() : void
    {
    }

    /**
     * @inheritdoc
     */
    public function writeString(string $str, string $name = "") : void
    {
        $this->_outputBuffer[$name] = $str;
    }


    /**
     * @inheritdoc
     */
    public function writeShort(int $s, string $name = "") : void
    {
        $this->_outputBuffer[$name] = $s;
    }

    /**
     * @inheritdoc
     */
    public function writeInteger(int $i, string $name = "") : void
    {
        $this->_outputBuffer[$name] = $i;
    }

    /**
     * @inheritdoc
     */
    public function writeBoolean(bool $b, string $name = "") : void
    {
        $this->_outputBuffer[$name] = $b;
    }

    /**
     * @inheritdoc
     */
    public function writeByte(int $byte, string $name = "") : void
    {
        $this->_outputBuffer[$name] = $byte;
    }

    /**
     * @inheritdoc
     */
    public function writeByteArray(array $bytes, string $name = "") : void
    {
        $this->_outputBuffer = array_merge($this->_outputBuffer, $bytes);
    }

    /**
     * Adds an array to the output buffer
     *
     * @param array  $array Array to add
     * @param string $name  Array name
     */
    public function writeArray(array $array, string $name = "") : void
    {
        $this->_outputBuffer[$name] = $array;
    }

    /**
     * @inheritdoc
     */
    public function readString(string $name = "") : string
    {
        return $this->_inputBuffer[$name];
    }

    /**
     * @inheritdoc
     */
    public function readShort(string $name = "") : int
    {
        return $this->_inputBuffer[$name];
    }

    /**
     * @inheritdoc
     */
    public function readInteger(string $name = "") : int
    {
        return $this->_inputBuffer[$name];
    }

    /**
     * @inheritdoc
     */
    public function readBoolean(string $name = "") : bool
    {
        return $this->_inputBuffer[$name];
    }

    /**
     * @inheritdoc
     */
    public function readByte(string $name = "") : int
    {
        return $this->_inputBuffer[$name];
    }

    /**
     * @inheritdoc
     */
    public function readByteArray(int $length, string $name = "") : array
    {
        return $this->_inputBuffer[$name];
    }

    /**
     * Returns an array from input buffer
     *
     * @param string $name Array name
     *
     * @return array $name in input buffer
     */
    public function readArray(string $name = "") : array
    {
        return $this->_inputBuffer[$name];
    }
}
