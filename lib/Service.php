<?php

namespace Sabre\Xml\Atom;

/**
 * The Atom Service object gives you quick access to the writer and reader.
 *
 * See \Sabre\Xml\Service for all the utility methods.
 *
 * @copyright Copyright (C) fruux GmbH.
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/
 */
class Service extends \Sabre\Xml\Service {

    const ATOM_NS = 'http://www.w3.org/2005/Atom';

    /**
     * Constructor
     */
    function __construct() {

        $atom = '{http://www.w3.org/2005/Atom}';

        // The following elements are all simple xml elements, and we can use
        // the VO system for mapping from PHP object to XML element.
        $this->mapValueObject($atom . 'feed', Element\Feed::class);
        $this->mapValueObject($atom . 'entry', Element\Entry::class);
        $this->mapValueObject($atom . 'source', Element\Source::class);
        $this->mapValueObject($atom . 'author', Element\Person::class);
        $this->mapValueObject($atom . 'contributer', Element\Person::class);

        // The following elements need custom (de-)serializers 

        // This serializer takes an object and encodes all its properties as
        // XML attributes.
        $attributeWriter = function($writer, $object) {
            $writer->writeAttributes(
                get_object_vars($value)
            );
        };

        // This deserializer takes all attributes from an xml element and
        // turns the into properties of a class. 
        $attributeReader = function($reader, $class) {

            $attributes = $reader->parseAttributes();
            $object = new $class();
            foreach ($attributes as $key => $value) {
                if (property_exists($object, $key)) {
                    $object->{$key} = $value;
                }
            }

            // Always advance the reader
            $reader->next();

            return $object;

        };



        // atom:category
        $this->classMap[Element\Category::class] = $attributeWriter;
        $this->elementMap[$atom . 'category'] = function($reader) use ($attributeReader) {
            return $attributeReader($reader, Element\Category::class);
        };
        // atom:link
        $this->classMap[Element\Link::class] = $attributeWriter;
        $this->elementMap[$atom . 'link'] = function($reader) use ($attributeReader) {
            return $attributeReader($reader, Element\Link::class);
        };

    }

}