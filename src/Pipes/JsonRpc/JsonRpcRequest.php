<?php

namespace Invoke\Pipes\JsonRpc;

use Invoke\Data;
use Invoke\Support\ReadonlyParams;

class JsonRpcRequest extends Data
{
    use ReadonlyParams;

    /**
     * A String specifying the version of the JSON-RPC protocol. MUST be exactly "2.0".
     *
     * @var string $jsonrpc
     */
    public string $jsonrpc = "2.0";

    /**
     * A String containing the name of the method to be invoked. Method names that begin with the word rpc followed by a period character (U+002E or ASCII 46) are reserved for rpc-internal methods and extensions and MUST NOT be used for anything else.
     *
     * @var string $method
     */
    public string $method;

    /**
     * A Structured value that holds the parameter values to be used during the invocation of the method. This member MAY be omitted.
     *
     * @var array|null $params
     */
    public ?array $params;

    /**
     * An identifier established by the Client that MUST contain a String, Number, or NULL value if included. If it is not included it is assumed to be a notification. The value SHOULD normally not be Null [1] and Numbers SHOULD NOT contain fractional parts [2]
     *
     * @var string|int|null $id
     */
    public string|int|null $id;
}
