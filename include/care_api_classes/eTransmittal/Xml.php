<?php

/**
 * @author Nick B. Alcala 5-1-2015
 * Class Xml
 * @see example.php
 * @see example-output.xml
 */
class Xml
{

    private $arrXmlData;

    /* @var $dom DOMDocument */
    private $dom;

    private $rules = array();

    private $errors = array();

    public function __construct(Array $arrXmlData, $qualifiedName, $systemId, $rules = array())
    {
        $this->arrXmlData = $arrXmlData;
        $this->rules = $rules;
        $this->initialize($qualifiedName,$systemId);
        $this->convert($this->arrXmlData,$this->dom);
    }

    public function toString()
    {
        return $this->dom->saveXML();
    }

    function getXmlBody()
    {
        return $this->dom->saveXML($this->dom->documentElement);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    private function initialize($qualifiedName, $systemId, $version = '1.0', $encoding = 'UTF-8')
    {
        $implementation = new DOMImplementation();
        $dtd = $implementation->createDocumentType($qualifiedName, '',$systemId);
        $this->dom = $implementation->createDocument('', '', $dtd);
        $this->dom->encoding = $encoding;
        $this->dom->version = $version;
    }

    private function convert(Array $array, DOMNode $parentDom)
    {
        $parent = $this->toXml($array,$parentDom);
        if($this->hasChildren($array)){
            foreach($array['children'] as $key => $value){
                $this->convert($value,$parent);
            }
        }
    }

    private function isNode($arr)
    {
        return array_key_exists('name', $arr) && array_key_exists('attributes', $arr) && array_key_exists('children', $arr);
    }

    private function hasChildren($arr)
    {
        return is_array($arr) && !empty($arr['children']);
    }

    private function toXml($data, DOMNode $parentDom)
    {
        $this->validateNode($data);
        $this->$data['name'] = $parentDom->appendChild(new DOMElement($data['name']));
        if(!empty($data['attributes'])){
            foreach($data['attributes'] as $key => $value){
                $this->validateAttribute($key,$value);
                $value = mb_detect_encoding($value) != 'UTF-8' ? utf8_decode($value) : $value;
                $this->$data['name']->setAttribute($key,utf8_decode($value));
            }
        }
        return $this->$data['name'];
    }

    private function validateNode($data)
    {
        if(!$this->isNode($data)){
            var_dump($data);
            die('Invalid array format');
        }
        if($this->hasRules($data['name'])){
            $rules = $this->rules[$data['name']];
            foreach($rules as $key => $rule){
                if(is_numeric($key))
                    $this->$rule($data,array());
                else
                    $this->$key($data,$rule);
            }
        }
    }

    private function validateAttribute($attributeKey, &$attributeValue)
    {
        //escape some commonly used special characters
        $attributeValue = str_replace('&','and',$attributeValue);
        $attributeValue = str_replace('\'','`',$attributeValue);

        if($this->hasRules($attributeKey)){
            $rules = $this->rules[$attributeKey];
            foreach ($rules as $key => $value) {
                if(is_numeric($key))
                    $this->$value($attributeKey,$attributeValue,array());
                else
                    $this->$key($attributeKey,$attributeValue,$value);
            }
        }
    }

    private function hasRules($nodeOrAttributeName)
    {
        return is_array($this->rules[$nodeOrAttributeName]) && !empty($this->rules[$nodeOrAttributeName]);
    }

    private function addError($message)
    {
        $this->errors[] = $message;
    }

    private function arrayChildSearch($parent, $childName)
    {
        $list = array();

        if(is_array($parent) && !empty($parent)){
            foreach($parent['children'] as $child){
                if($child['name'] == $childName)
                    $list[] = $child;
            }
        }

        return $list;
    }

    protected static function isEmpty($value)
    {
        return
            $value == null ||
            trim($value) == "";
    }

    /************************************************************/

    protected function defaultValue($key, &$value, $functionParameters)
    {
        if (self::isEmpty($value))
            $value = $functionParameters;
    }

    protected function in($key, $value, $functionParameters)
    {
        if(!in_array($value,$functionParameters)){
            $this->addError($key . ' has invalid value');
        }
    }

    protected function dateFormat($key, &$value, $functionParameters)
    {
        if(strtotime($value))
            $value = date($functionParameters,strtotime($value));
        else
            $value = '';
    }

    protected function upper($key, &$value, $functionParameters)
    {
        $value = strtoupper($value);
    }

    protected function enye($key, &$value, $functionParameters)
    {
        $value = str_replace(array('ñ','Ñ','?',chr(241),chr(209)),'&Ntilde',$value);
    }

    protected function currency($key, &$value, $functionParameters)
    {
        $value = number_format(doubleval($value),2);
    }

    protected function number($key, &$value, $functionParameters)
    {
        $value = number_format(doubleval($value),2,'.','');
    }

    protected function limit($key, &$value, $functionParameters)
    {
        $value = substr($value, 0, $functionParameters - 1);
    }

    protected function attributes($node, $functionParameters)
    {
        if(count($node['attributes']) == count($functionParameters)){
            foreach($functionParameters as $attribute){
                if(!array_key_exists($attribute,$node['attributes']))
                    $this->addError($node['name'].' should have '.$attribute.' attribute');
            }
        }else
            $this->addError($node['name'].' has exceeded the maximum attribute count');
    }

    protected function hasOne($node, $functionParameters)
    {
        foreach($functionParameters as $childNode){
            $count = count($this->arrayChildSearch($node,$childNode));
            if($count > 1)
                $this->addError($node['name'].' can only have one '.$childNode.' child node');
            else if($count <= 0)
                $this->addError($node['name'].' should have one '.$childNode.' child node');
        }
    }

}//end class