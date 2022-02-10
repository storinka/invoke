<?php

namespace Invoke\Pipes\JsonRpc;

use Invoke\Data;
use Invoke\Support\ReadonlyParams;

class JsonRpcResponse extends Data
{
    use ReadonlyParams;

    /**
     * A String specifying the version of the JSON-RPC protocol. MUST be exactly "2.0".
     *
     * @var string $jsonrpc
     */
    public string $jsonrpc = "2.0";

    /**
     * This member is REQUIRED on success.
     * This member MUST NOT exist if there was an error invoking the method.
     * The value of this member is determined by the method invoked on the Server.
     *
     * @var mixed $result
     */
    public mixed $result = null;

    /**
     * A Structured value that holds the parameter values to be used during the invocation of the method. This member MAY be omitted.
     *
     * @var mixed $params
     */
    public mixed $error = null;

    /**
     * This member is REQUIRED.
     * It MUST be the same as the value of the id member in the Request Object.
     * If there was an error in detecting the id in the Request object (e.g. Parse error/Invalid Request), it MUST be Null.
     *
     * @var string|int|null $id
     */
    public string|int|null $id = null;
}