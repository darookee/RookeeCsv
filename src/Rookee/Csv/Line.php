<?php

/**
 * undocumented class
 *
 * @package default
 * @subpackage default
 * @author Nils Uliczka
 */
namespace Rookee\Csv;

class Line implements \ArrayAccess, \Iterator {

    /**
     * undocumented class variable
     *
     * @var int
     */
    protected $_curIndex = 0;

    /**
     * undocumented class variable
     *
     * @var array
     */
    protected $_fields = array();

    /**
     * undocumented class variable
     *
     * @var array
     */
    protected $_convertedFields = array();

    /**
     * undocumented class variable
     *
     * @var Rookee\Csv\Header
     */
    protected $_header = NULL;

    /**
     * undocumented class variable
     *
     * @var array
     */
    protected $_converter = array();

    /**
     * undocumented class variable
     *
     * @var bool
     */
    protected $_useConverted = true;

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function __construct(Array $fields = array(), Header $header, Array $converter = array()) {
        $this->setConverter($converter);
        $this->setHeader($header);
        $this->setFields($fields);
    }

    /**
     * sets fields
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setFields(Array $fields) {
        foreach($fields as $k => $v) {
            if(!is_numeric($k))
                $k = $this->getHeader()->offsetGet($k);

            if(is_callable($conv = $this->getConverter($this->getHeader()->offsetGet($k))))
                $cV = call_user_func_array($conv, array($v));
            else
                $cV = $v;

            $this->_fields[$k] = $v;
            $this->_convertedFields[$k] = $cV;
        }
    }

    /**
     * getter for $_fields
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getFields() {
        if($this->getUseConverted() && !empty($this->_convertedFields))
            return $this->_convertedFields;
        return $this->_fields;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setHeader(Header $header) {
        $this->_header = $header;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getHeader() {
        return $this->_header;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setUseConverted($use = true) {
        $this->_useConverted = $use;
    }

    /**
     * undocumented function
     *
     * @return bool
     * @author Nils Uliczka
     */
    public function getUseConverted() {
        return $this->_useConverted;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setConverter(Array $converter = array()) {
        $this->_converter = $converter;
        $this->setFields($this->getFields());
    }

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getConverter($offset = NULL) {
        if(empty($offset))
            return $this->_converter;
        if(is_numeric($offset))
            $offset = $this->getHeader()->offsetGet($offset);
        if(isset($this->_converter[$offset]))
            return $this->_converter[$offset];

        return false;
    }

    /** Iterator **/
    /**
     * returns current index
     *
     * @return void
     * @author Nils Uliczka
     */
    public function current() {
        return ($this->offsetExists($this->_curIndex)?($this->getUseConverted()?$this->_convertedFields[$this->_curIndex]:$this->_fields[$this->_curIndex]):null);
    }

    /**
     * iterator next
     *
     * @return void
     * @author Nils Uliczka
     */
    public function next() {
        $this->_curIndex++;
        return $this->current();
    }

    /**
     * iterator key
     *
     * @return void
     * @author Nils Uliczka
     */
    public function key() {
        return $this->_header[$this->_curIndex];
    }

    /**
     * iterator rewind
     *
     * @return void
     * @author Nils Uliczka
     */
    public function rewind() {
        $this->_curIndex = 0;
    }

    /**
     * iteartor valid
     *
     * @return void
     * @author Nils Uliczka
     */
    public function valid() {
        return isset($this->_fields[$this->_curIndex]);
    }

    /** ArrayAccess **/
    /**
     * arrayaccess offsetexists
     *
     * @return void
     * @author Nils Uliczka
     */
    public function offsetExists($offset) {
        return isset($this->_fields[$offset]);
    }

    /**
     * arrayaccess offsetget
     *
     * @return void
     * @author Nils Uliczka
     */
    public function offsetGet($offset) {
        $r = $this->offsetExists($offset)?($this->getUseConverted()?$this->_convertedFields[$offset]:$this->_fields[$offset]):null;
        if(!$r) {
            $hOffset = $this->_header[$offset];
            $r = $this->offsetExists($hOffset)?($this->getUseConverted()?$this->_convertedFields[$hOffset]:$this->_fields[$hOffset]):null;
        }
        return $r;
    }

    /**
     * arrayaccess offsetset
     *
     * @return void
     * @author Nils Uliczka
     */
    public function offsetSet($offset, $value) {
        if(is_null($offset)) {
            throw new \Excepton('Cannot set without key');
        } else {
            $this->_fields[$offset] = $value;
            $this->_convertedFields[$offset] = $value;
        }
    }

    /**
     * arrayaccess unset
     *
     * @return void
     * @author Nils Uliczka
     */
    public function offsetUnset($offset) {
        unset($this->_fields[$offset]);
        unset($this->_convertedFields[$offset]);
    }
} // END class Header
