<?php

namespace Invoke\Documentation\Documents;

use Ds\Set;
use Invoke\Container;
use Invoke\Invoke;
use Invoke\Meta\Parameter;
use Invoke\Toolkit\Validators\ArrayOf;
use Invoke\Utils\Utils;
use function Invoke\Utils\array_unique_by_key;

/**
 * Main API document.
 */
class ApiDocument extends Document
{
    /**
     * Version of Invoke.
     *
     * @var string $invokeVersion
     */
    #[Parameter]
    public string $invokeVersion;

    /**
     * Name of the API.
     *
     * @var string $name
     */
    #[Parameter]
    public string $name;

    /**
     * Short description of the API.
     *
     * @var string|null $summary
     */
    #[Parameter]
    public ?string $summary;

    /**
     * Icon url of the API.
     *
     * @var string|null $iconUrl
     */
    #[Parameter]
    public ?string $iconUrl;

    /**
     * Document sections.
     *
     * @var array $sections
     */
    #[ArrayOf(SectionDocument::class)]
    public array $sections;

    /**
     * Invoke instruction document.
     *
     * @var InvokeInstructionDocument $invokeInstruction
     */
    public InvokeInstructionDocument $invokeInstruction;

    /**
     * List of available types.
     *
     * @var array $availableTypes
     */
    #[ArrayOf(TypeDocument::class)]
    public array $availableTypes;

    /**
     * Create API document from invoke instance.
     *
     * @param Invoke $invoke
     * @return static
     */
    public static function fromInvoke(Invoke $invoke): static
    {
        $methods = [];
        $types = new Set();

        foreach ($invoke->getMethods() as $name => $method) {
            if (is_string($method) && class_exists($method)) {
                $methods[] = [
                    "name" => $name,
                    "class" => $method,
                ];

                $types->add(...Utils::extractUsedTypes($method, $types));
            }
        }

        $sections = $invoke->getConfig("apiDocument.sections");
        $sections = array_map(fn($section) => Container::make($section)->pass([
            "types" => $types->toArray(),
            "methods" => $methods,
        ]), $sections);

        $typesDocuments = TypeDocument::many($types, "fromType");
        $typesDocuments = array_unique_by_key($typesDocuments, "uniqueTypeName");

        return static::from([
            "name" => "Storinka API",

            "sections" => $sections,

            "availableTypes" => $typesDocuments,

            "invokeVersion" => Invoke::$version,

            "invokeInstruction" => InvokeInstructionDocument::from([
                "name" => "fetch",
                "protocol" => "http",
                "host" => "localhost",
                "port" => 8081,
                "path" => "",
                "type" => "json"
            ]),

            "iconUrl" => "https://business.storinka.menu/_nuxt/img/logo.b45e76c.svg",
        ]);
    }
}
