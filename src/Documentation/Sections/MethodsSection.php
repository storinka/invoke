<?php

namespace Invoke\Documentation\Sections;

use Invoke\Documentation\Documents\MethodReferenceDocument;
use Invoke\Documentation\Documents\SectionDocument;
use Invoke\Documentation\SectionBuilder;

/**
 * Methods section builder.
 */
class MethodsSection extends SectionBuilder
{
    public function build(array $types, array $methods): SectionDocument
    {
        $methodsDocuments = array_map(
            fn(array $method) => MethodReferenceDocument::from([
                "methodName" => $method["name"],
            ]),
            $methods
        );

        return SectionDocument::from([
            "name" => "Methods",
            "items" => $methodsDocuments,
        ]);
    }
}