<?php
/**
 * Created by PhpStorm.
 * User: Bender
 * Date: 3/14/2019
 * Time: 4:24 PM
 */

namespace SegHis\modules\eclaims\services\cf4;

use DOMElement;

class XmlWriter
{

    /**
     * Creates a new XML node with given name and attributes
     *
     * @param DomDocument the original XML document
     * @param string $name tag name for the new node
     * @param array $attrs array holding the attributes of the child node
     * @return void
     */
    public function _createNode($document, $name, $attrs = array())
    {
        $node = $document->createElement($name);
        foreach ($attrs as $akey => $value) {
            $node->setAttribute($akey, $value);
        }
        return $node;
    }


    /**
     * [appendNode description]
     * @param  [type] $parent [description]
     * @param  [type] $child  [description]
     * @param  [type] $name   [description]
     * @param  [type] $attrs  [description]
     * @return [type]         [description]
     */
    public function appendNode(&$parent, &$child, $name, $attrs = array())
    {
        $child = $parent->appendChild(new DOMElement($name));
        foreach ($attrs as $akey => $attr) {
            if (mb_detect_encoding($attr, 'UTF-8', true) === false) {
                $attr = utf8_encode($attr);
            }
            $child->setAttribute($akey, $attr);
        }
    }
}