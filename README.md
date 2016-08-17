SocksWork
=========
Alexya's SocksWork library

Contents
--------

 - [SocksWork](#sockswork)
 - [PacketBuilder](#packet_builder)
 - [Encoder](#encoder)

<a name="sockswork"></a>
SocksWork
---------

The class `\Alexya\SocksWork\SocksWork` provides an easy way to connect to a server and send packets.

The constructor accepts as parameter the host of the server, the port and the timeout of the connection.
It also accepts as 4th parameter a boolean that indicates if SocksWork should connect to the server once the constructor
has finnished or not, by default it's set to true, however if you set it to false you'll need to call
the `\Alexya\SocksWork\SocksWork::connect` method.

Example:

```php
<?php

$SocksWork = new SocksWork("localhost", 8080, 100); // Connects to localhost:8080 and sets a timeout of 100ms
```

Once the connection has been established you can send anything with the `\Alexya\SocksWork\SocksWork::send` command
that accepts as parameter the binary data to send or an instance of `\Alexya\SocksWork\PacketBuilder`.

If the parameter is binary data the response will be set to the `\Alexya\SocksWork\SocksWork::$response` property,
if it's a `\Alexya\SocksWork\PacketBuilder` instance the response will be sent directly to it's property.

The method also accepts a 2nd parameter that is a boolean that indicates wether if SocksWork should wait
for the response or not.

Example:

```php
<?php

$SocksWork->send((binary) "Hello world!");
echo $SocksWork->response; // Response to the packet

$packet = new PacketBuilder();
$packet->writeString("message", "Hello World!");

$SocksWork->send($packet);

echo $packet->readString("response"); // Response to the packet
```

To close the connection simply call the method `\Alexya\SocksWork\SocksWork::close` and when you want to reconnect
call the method `\Alexya\SocksWork\SocksWork::connect`, to see if SocksWork is already connected use the
method `\Alexya\SocksWork\SocksWork::isConnected`.

<a name="packet_builder"></a>
PacketBuilder
-------------

The class `\Alexya\SocksWork\PacketBuilder` provides an interface to build packets that will be sent
with `\Alexya\SocksWork\SocksWork`, it also provides methods for reading the response.

The constructor accepts as parameter an object of type `\Alexya\SocksWork\IEncoder` that will be the encoder to
use to write and read the packet (By default it's an instance of `\Alexya\SocksWork\Encoders\StringEncoder`).

Once the constructor has been called you need to can start adding parameters to the packet by calling
the methods of the encoder throw the instanced object.

Example:

```php
<?php

$packet = new PacketBuilder();

$packet->writeShort("id", 1);
$packet->writeString("name", "test");

$SocksWork->send($packet);

$id   = $packet->readShort("id");
$name = $packet->readString("name");
```

You can also extend this class for simplifying the instantation of the object:

```php
<?php

class SetUserName extends PacketBuilder
{
    private $_id = 1;

    public $id   = -1;
    public $name = "";

    public function onInstance(string $name)
    {
        $this->_encoder->writeShort("id", $this->_id);
        $this->_encoder->writeString("name", $name);
    }

    public function onResponse()
    {
        $this->id   = $this->_ecoder->readShort("id");
        $this->name = $this->_encoder->readString("name");
    }
}

$packet = new SetUserName("test");

$SocksWork->send($packet);

$id   = $packet->id;
$name = $packet->name;
```

The method `\Alexya\SocksWork\PacketBuilder::onInstance` is executed right after the constructor and receives as
parameters the arguments sent to the constructor (without the instance of the encoder).

For example:

```php
<?php

class Packet extends PacketBuilder
{
    public function onInstance()
    {
        foreach(func_get_args() as $key => $val) {
            echo "{$key} => {$value}\n";
        }
    }
}

$packet = new Packet(1, "test", "foo");
// Output:
// 0 => 1
// 1 => test
// 2 => foo

$packet = new Packet(new \Alexya\SocksWork\Encoders\String(), 1, "test");
// Output:
// 0 => 1
// 1 => test
```

The method `\Alexya\SocksWork\PacketBuilder::onResponse` is executed right after the response has
been received.

<a name="encoders"></a>
Encoders
--------

The encoders are the classes that will encode the data that `SocksWork` will send, they must extend the class
`\Alexya\SocksWork\Encoder`. You can see the available encoders in the namespace `\Alexya\SocksWork\Encoders`.
