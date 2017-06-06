<?php
namespace Alexya\SocksWork\Encoders;

use \Alexya\SocksWork\Encoder;

/**
 * Big endian encoder.
 * ===================
 *
 * This encoder builds a byte array and packs all values with big endian.
 * Example:
 *
 *     $packet = new PacketBuilder(new BigEndian());
 *     $packet->writeShort(1);       // 00 01
 *     $packet->writeInt(123456);    // 00 01 226 64
 *     $packet->writeString("test"); // 00 04 (string length)  116 101 115 116
 *
 *     echo bin2hex($packet->getOutputBufferAsString());
 *     // 00 01 00 01 226 64 00 04 116 101 115 116
 *
 * @author Manulaiko <manulaiko@gmail.com>
 */
class BigEndian extends Encoder
{
    /**
     * Pointer for looping through the buffers
     *
     * @var int
     */
    protected $_i = 0;

    /**
     * @inheritdoc
     */
    public function getOutputBufferAsString() : string
    {
        $binary = "";

        foreach($this->_outputBuffer as $byte) {
            $binary .= pack("c", $byte);
        }

        return $binary;
    }

    /**
     * @inheritdoc
     */
    public function read() : void
    {
        $this->_i = 0;
    }

    /**
     * @inheritdoc
     */
    public function write() : void
    {
        $this->_outputBuffer = []; //assure it's empty
    }

    /**
     * @inheritdoc
     */
    public function writeString(string $str, string $name = "") : void
    {
        $this->writeShort(strlen($str));

        foreach(str_split($str) as $s) {
            $this->writeByte(ord($s));
        }
    }

    /**
     * @inheritdoc
     */
    public function writeShort(int $s, string $name = "") : void
    {
        $this->writeByte(($s >> 8) & 0xFF);
        $this->writeByte(($s >> 0) & 0xFF);
    }

    /**
     * @inheritdoc
     */
    public function writeInteger(int $i, string $name = "") : void
    {
        $this->writeByte(($i >> 24) & 0xFF);
        $this->writeByte(($i >> 16) & 0xFF);
        $this->writeByte(($i >> 8) & 0xFF);
        $this->writeByte(($i >> 0) & 0xFF);
    }

    /**
     * @inheritdoc
     */
    public function writeBoolean(bool $b, string $name = "") : void
    {
        if($b) {
            $this->writeByte(1);
        } else {
            $this->writeByte(0);
        }
    }

    /**
     * @inheritdoc
     */
    public function writeByte(int $byte, string $name = "") : void
    {
        $this->_outputBuffer[] = $byte;
    }

    /**
     * @inheritdoc
     */
    public function writeByteArray(array $bytes, string $name = "") : void
    {
        $this->_outputBuffer = array_merge($this->_outputBuffer, $bytes);
    }

    /**
     * @inheritdoc
     */
    public function readString(string $name = "") : string
    {
        $length = $this->readShort();
        $str    = "";

        for($i = 0; $i < $length; $i++) {
            $str .= chr($this->readByte());
        }

        return $str;
    }

    /**
     * @inheritdoc
     */
    public function readShort(string $name = "") : int
    {
        $b1 = $this->readByte() << 8;
        $b2 = $this->readByte() << 0;

        return $b1 + $b2;
    }

    /**
     * @inheritdoc
     */
    public function readInteger(string $name = "") : int
    {
        $b1 = $this->readByte() << 24;
        $b2 = $this->readByte() << 16;
        $b3 = $this->readByte() << 8;
        $b4 = $this->readByte() << 0;

        return $b1 + $b2 + $b3 + $b4;
    }

    /**
     * @inheritdoc
     */
    public function readBoolean(string $name = "") : bool
    {
        return ($this->readByte() == 1) ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function readByte(string $name = "") : int
    {
        return $this->_inputBuffer[$this->_i++];
    }

    /**
     * @inheritdoc
     */
    public function readByteArray(int $length, string $name = "") : array
    {
        $bytes = [];

        for($i = 0; $i < $length; $i++) {
            $bytes[] = $this->readByte();
        }

        return $bytes;
    }
}
