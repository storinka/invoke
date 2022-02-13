<?php

namespace Invoke\Documentation\Documents;

use Invoke\Meta\Parameter;
use Invoke\Toolkit\Validators\ArrayOf;

/**
 * Section document.
 */
class SectionDocument extends Document
{
    /**
     * Title of the section.
     *
     * @var string $name
     */
    #[Parameter]
    public string $name;

    /**
     * Section items.
     *
     * @var array $items
     */
    #[ArrayOf([
        MethodDocument::class,
        TypeDocument::class,
        MarkdownDocument::class,
        IframeDocument::class,
        SectionDocument::class,

        TypeReferenceDocument::class,
        MethodReferenceDocument::class,
    ])]
    public array $items;
}
