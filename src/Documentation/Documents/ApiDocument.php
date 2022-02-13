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
     * Show only icon in header.
     *
     * @var bool $iconOnly
     */
    #[Parameter]
    public bool $iconOnly;

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
     * List of available methods.
     *
     * @var array $availableMethods
     */
    #[ArrayOf(MethodDocument::class)]
    public array $availableMethods;

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

        $methodsDocuments = array_map(
            fn($method) => MethodDocument::fromNameAndClass($method["name"], $method["class"]),
            $methods
        );

        $name = $invoke->getConfig("apiDocument.name", "Invoke API Document");
        $summary = $invoke->getConfig("apiDocument.summary", null);
        $iconUrl = $invoke->getConfig("apiDocument.iconUrl", "https://user-images.githubusercontent.com/21020331/145628046-ca19dbdf-2935-49fe-934c-a171219566cc.png");
        $iconOnly = $invoke->getConfig("apiDocument.iconOnly", true);

        $invokeInstruction = [
            "name" => $invoke->getConfig("apiDocument.invokeInstruction.name", "fetch"),
            "protocol" => $invoke->getConfig("apiDocument.invokeInstruction.protocol", $invoke->getConfig("serve.protocol", "http")),
            "host" => $invoke->getConfig("apiDocument.invokeInstruction.host", $invoke->getConfig("serve.host", "localhost")),
            "port" => $invoke->getConfig("apiDocument.invokeInstruction.port", $invoke->getConfig("serve.port", 8081)),
            "path" => $invoke->getConfig("apiDocument.invokeInstruction.path", $invoke->getConfig("serve.pathPrefix", "")),
            "type" => $invoke->getConfig("apiDocument.invokeInstruction.type", "json")
        ];

        return static::from([
            "name" => $name,
            "summary" => $summary,
            "iconUrl" => $iconUrl,
            "iconOnly" => $iconOnly,

            "sections" => $sections,

            "availableTypes" => $typesDocuments,
            "availableMethods" => $methodsDocuments,

            "invokeVersion" => Invoke::$version,

            "invokeInstruction" => InvokeInstructionDocument::from($invokeInstruction),
        ]);
    }
}
