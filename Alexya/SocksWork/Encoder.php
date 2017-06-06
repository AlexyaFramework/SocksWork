<?php
namespace Alexya\SocksWork;

/**
 * Abstract encoder class.
 * =======================
 *
 * This is the base class for all encoders.
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
abstract class Encoder
{
    /**
     * Input buffer from server.
     *
     * The response of the server as a byte array.
     *
     * @var array Byte array containing response.
     */
    protected $_inputBuffer = [];

    /**
     * Raw input as string.
     *
     * @var array Bytes received from server.
     */
    protected $_rawInput = "";

    /**
     * Output buffer.
     *
     * The packet to send as a byte array.
     *
     * @var array Byte array containing packet to send.
     */
    protected $_outputBuffer = [];

    /**
     * Sets the input buffer.
     *
     * @param string $inputBuffer Server's response.
     */
    public function setInputBuffer(string $inputBuffer) : void
    {
        $this->_rawInput    = $inputBuffer;
        $this->_inputBuffer = unpack("C*", $inputBuffer);
    }

    /**
     * Returns the input buffer.
     *
     * @return array Input buffer.
     */
    public function getInputBuffer() : array
    {
        return $this->_inputBuffer;
    }

    /**
     * Returns raw received data.
     *
     * @return string Received server's response.
     */
    public function getRawInput() : string
    {
        return $this->_rawInput;
    }

    /**
     * Sets output buffer.
     *
     * @param array $outputBuffer Output buffer.
     */
    public function setRawInput(array $outputBuffer) : void
    {
        $this->_outputBuffer = $outputBuffer;
    }

    /**
     * Returns the output buffer.
     *
     * @return array Output buffer as byte array.
     */
    public function getOutputBuffer() : array
    {
        return $this->_outputBuffer;
    }

    ////////////////////////////
    // Start abstract methods //
    ////////////////////////////

    /**
     * Returns output buffer ready to send.
     *
     * @return string Output buffer ready to send.
     */
    public abstract function getOutputBufferAsString() : string;

    /**
     * Starts reading the response.
     *
     * Sets everything needed for reading the response.
     */
    public abstract function read() : void;

    /**
     * Starts writing the packet.
     *
     * Sets everything needed for writing the packet.
     */
    public abstract function write() : void;

    /**
     * Adds a string to the output buffer.
     *
     * @param string $str  String to write.
     * @param string $name Name of the parameter.
     */
    public abstract function writeString(string $str, string $name) : void;

    /**
     * Adds a short integer to the output buffer.
     *
     * @param int    $s    Short to write.
     * @param string $name Name of the parameter.
     */
    public abstract function writeShort(int $s, string $name) : void;

    /**
     * Adds an integer to the output buffer.
     *
     * @param int    $i     Integer to write.
     * @param string $name  Name of the parameter.
     */
    public abstract function writeInteger(int $i, string $name) : void;

    /**
     * Adds a boolean to the output buffer.
     *
     * @param bool   $b    Boolean to write.
     * @param string $name Name of the parameter.
     */
    public abstract function writeBoolean(bool $b, string $name) : void;

    /**
     * Adds a byte to the output buffer.
     *
     * @param int    $byte Byte to write.
     * @param string $name Name of the parameter.
     */
    public abstract function writeByte(int $byte, string $name) : void;

    /**
     * Adds a byte array to the output buffer.
     *
     * @param array  $bytes Byte array to write.
     * @param string $name  Name of the parameter.
     */
    public abstract function writeByteArray(array $bytes, string $name) : void;

    /**
     * Retrieves a string from the input buffer.
     *
     * @param string $name Name of the parameter.
     *
     * @return string Next string.
     */
    public abstract function readString(string $name) : string;

    /**
     * Retrieves a short integer from the input buffer.
     *
     * @param string $name Name of the parameter.
     *
     * @return int Next 2 bytes integer.
     */
    public abstract function readShort(string $name) : int;

    /**
     * Retrieves an integer from the input buffer.
     *
     * @param string $name Name of the parameter.
     *
     * @return int Next 4 bytes integer.
     */
    public abstract function readInteger(string $name) : int;

    /**
     * Retrieves a boolean from the input buffer.
     *
     * @param string $name Name of the parameter.
     *
     * @return bool Next byte as a boolean.
     */
    public abstract function readBoolean(string $name) : bool;

    /**
     * Retrieves a byte from the input buffer.
     *
     * @param string $name Name of the parameter.
     *
     * @return int Next byte.
     */
    public abstract function readByte(string $name) : int;

    /**
     * Retrieves an integer from the input buffer.
     *
     * @param int    $length Length of the byte array.
     * @param string $name   Name of the parameter.
     *
     * @return array $length array with input buffer bytes.
     */
    public abstract function readByteArray(int $length, string $name) : array;
    //////////////////////////
    // End abstract methods //
    //////////////////////////
}
