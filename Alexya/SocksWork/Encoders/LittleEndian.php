<?php
namespace Alexya\SocksWork\Encoders;

use \Alexya\SocksWork\Encoder;

/**
 * Little endian encoder.
 *
 * This encoder builds a byte array and packs all values with little endian.
 *
 * Example:
 *
 *     $packet = new PacketBuilder(new LittleEndian());
 *     $packet->writeShort(1);       // 00 01
 *     $packet->writeInt(123456);    // 00 01 226 64
 *     $packet->writeString("test"); // 00 04 (string length)  116 101 115 116
 *
 *     echo bin2hex($packet->getOutputBufferAsString());
 *     // 01 00 64 226 01 00 04 00 116 101 115 116
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class LittleEndian extends Encoder
{
    /**
     * Pointer for looping through the buffers.
     *
     * @var int
     */
    protected $_i = 0;

    /**
     * @inheritDoc
     */
    public function getOutputBufferAsString() : string
    {
        $binary = "";

        foreach($this->byteArray as $byte) {
            $binary .= pack("c", $byte);
        }

        return $binary;
    }

    /**
     * @inheritDoc
     */
    public function read()
    {
        $this->_i = 0;
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
        $this->writeShort(strlen($str));

        foreach(str_split($str) as $s) {
            $this->writeByte(ord($s));
        }
    }


    /**
     * @inheritDoc
     */
    public function writeShort(int $s)
    {
        $this->writeByte(($s >> 0) & 0xFF);
        $this->writeByte(($s >> 8) & 0xFF);
    }

    /**
     * @inheritDoc
     */
    public function writeInteger(int $i)
    {
        $this->writeByte(($n >> 0) & 0xFF);
        $this->writeByte(($n >> 8) & 0xFF);
        $this->writeByte(($n >> 16) & 0xFF);
        $this->writeByte(($n >> 24) & 0xFF);
    }

    /**
     * @inheritDoc
     */
    public function writeBoolean(bool $b)
    {
        if($b) {
            $this->writeByte(1);
        } else {
            $this->writeByte(0);
        }
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
        $this->_outputBuffer = array_merge($this->_outputBuffer, $bytes);
    }

    /**
     * @inheritDoc
     */
    public function readString() : string
    {
        $length = $this->readShort();
        $str    = "";

        for($i = 0; $i < $length; $i++) {
            $str .= chr($this->readByte());
        }

        return $str;
    }

    /**
     * @inheritDoc
     */
    public function readShort() : int
    {
        $b2 = $this->readByte() << 8;
        $b1 = $this->readByte() << 0;

        return $b1 + $b2;
    }

    /**
     * @inheritDoc
     */
    public function readInteger() : int
    {
        $b4 = $this->readByte() << 24;
        $b3 = $this->readByte() << 16;
        $b2 = $this->readByte() << 8;
        $b1 = $this->readByte() << 0;

        return $b1 + $b2 + $b3 + $b4;
    }

    /**
     * @inheritDoc
     */
    public function readBoolean() : bool
    {
        return ($this->readByte() == 1) ? true : false;
    }

    /**
     * @inheritDoc
     */
    public function readByte() : int
    {
        return $this->_inputBuffer[$this->_i++];
    }

    /**
     * @inheritDoc
     */
    public function readByteArray(int $legnth) : array
    {
        $bytes = [];

        for($i = 0; $i < $legnth; $i++) {
            $bytes[] = $this->readByte();
        }

        return $bytes;
    }
}
