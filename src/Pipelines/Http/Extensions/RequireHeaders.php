<?php

namespace Invoke\Pipelines\Http\Extensions;

use Attribute;
use Invoke\Container;
use Invoke\Meta\AbstractMethodExtension;
use Invoke\Method;
use Invoke\Pipelines\Http\Exceptions\RequiredHeaderNotProvidedException;
use Psr\Http\Message\ServerRequestInterface;

#[Attribute(Attribute::TARGET_CLASS)]
class RequireHeaders extends AbstractMethodExtension
{
    public array $headers;

    public function __construct(array|string $headers)
    {
        if (is_string($headers)) {
            $headers = [$headers];
        }

        $this->headers = $headers;
    }

    public function beforeValidateParams(Method $method): void
    {
        $request = Container::get(ServerRequestInterface::class);

        foreach ($this->headers as $header) {
            if (!$request->hasHeader($header)) {
                throw new RequiredHeaderNotProvidedException($header);
            }
        }
    }
}