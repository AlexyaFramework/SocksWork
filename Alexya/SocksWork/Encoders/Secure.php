<?php
namespace Alexya\SocksWork\Encoders;

use \Alexya\SocksWork\Encoder;

/**
 * Secure encoder.
 * ===============
 *
 * This encoder works as a layer for encrypting the data to send.
 *
 * The constructor accepts as parameter another encoder (this will be the actual encoder)
 * and a string being the algorithm where to encrypt the result.
 *
 * If the second parameter is a callback, it must accept a string as parameter that is the packet to encrypt
 * and a boolean indicating whether if the parameter needs to be encrypted (true) or decrypted (false).
 *
 * Example:
 *
 *     $packet = new PacketBuilder(new Secure(new BigEndian(), "base64"));
 *     $packet->writeShort(1);       // 00 01
 *     $packet->writeInt(123456);    // 00 01 226 64
 *     $packet->writeString("test"); // 00 04 (string length)  116 101 115 116
 *
 *     echo $packet->getOutputBufferAsString();
 *     // 01 00 64 226 01 00 04 00 116 101 115 116 encoded in base64
 *
 *     $packet = new PacketBuilder(new Secure(new BigEndian(), function encrypt(string $str, boolean $encrypt) : string {
 *         if($encrypt) {
 *             return base64_encode($str);
 *         }
 *
 *         return base64_decode($str);
 *     }));
 *     $packet->writeShort(1);       // 00 01
 *     $packet->writeInt(123456);    // 00 01 226 64
 *     $packet->writeString("test"); // 00 04 (string length)  116 101 115 116
 *
 *     echo $packet->getOutputBufferAsString();
 *     // 01 00 64 226 01 00 04 00 116 101 115 116 encoded in base64
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class Secure extends Encoder
{
    /**
     * Encoder.
     *
     * @var Encoder
     */
    private $_encoder = null;

    /**
     * Encrypt algorithm.
     *
     * @var string
     */
    private $_algorithm = "base64";

    /**
     * Constructor.
     *
     * @param Encoder         $encoder   Encoder on which the packet will be encoded.
     * @param string|callable $algorithm Encryption algorithm (by default Base64).
     */
    public function __construct(Encoder $encoder, $algorithm = "base64")
    {
        $this->_encoder   = $encoder;
        $this->_algorithm = $algorithm;
    }

    /**
     * @inheritDoc
     */
    public function getOutputBufferAsString() : string
    {
        if(is_callable($this->_algorithm)) {
            return ($this->_algorithm)($this->_encoder->getOutputBufferAsString(), true);
        }

        switch(strtolower($this->_algorithm))
        {
            case "base64":
            default:
                return base64_encode($this->_encoder->getOutputBufferAsString());
                break;
        }
    }

    /**
     * @inheritDoc
     */
    public function read() : void
    {
        $inputBuffer = $this->_inputBuffer;

        if(is_callable($this->_algorithm)) {
            $inputBuffer = ($this->_algorithm)($inputBuffer, false);
        } else {
            switch(strtolower($this->_algorithm))
            {
                case "base64":
                default:
                    $inputBuffer = base64_decode($inputBuffer);
                    break;
            }
        }

        $this->_encoder->setInputBuffer($inputBuffer);
        $this->_encoder->read();
    }

    /**
     * @inheritDoc
     */
    public function write() : void
    {
        $this->_encoder->write();
    }

    /**
     * @inheritDoc
     */
    public function writeString(string $str, string $name = "") : void
    {
        $this->_encoder->writeString($str, $name);
    }


    /**
     * @inheritDoc
     */
    public function writeShort(int $s, string $name = "") : void
    {
        $this->_encoder->writeShort($s, $name);
    }

    /**
     * @inheritDoc
     */
    public function writeInteger(int $i, string $name = "") : void
    {
        $this->_encoder->writeInteger($i, $name);
    }

    /**
     * @inheritDoc
     */
    public function writeBoolean(bool $b, string $name = "") : void
    {
        $this->_encoder->writeBoolean($b, $name);
    }

    /**
     * @inheritDoc
     */
    public function writeByte(int $byte, string $name = "") : void
    {
        $this->_encoder->writeByte($byte, $name);
    }

    /**
     * @inheritDoc
     */
    public function writeByteArray(array $bytes, string $name = "") : void
    {
        $this->_encoder->writeByteArray($bytes, $name);
    }

    /**
     * @inheritDoc
     */
    public function readString(string $name = "") : string
    {
        return $this->_encoder->readString($name);
    }

    /**
     * @inheritDoc
     */
    public function readShort(string $name = "") : int
    {
        return $this->_encoder->readShort($name);
    }

    /**
     * @inheritDoc
     */
    public function readInteger(string $name = "") : int
    {
        return $this->_encoder->readInteger($name);
    }

    /**
     * @inheritDoc
     */
    public function readBoolean(string $name = "") : bool
    {
        return $this->_encoder->readBoolean($name);
    }

    /**
     * @inheritDoc
     */
    public function readByte(string $name = "") : int
    {
        return $this->_encoder->readByte($name);
    }

    /**
     * @inheritDoc
     */
    public function readByteArray(int $length, string $name = "") : array
    {
        return $this->_encoder->readByteArray($length, $name);
    }
}
