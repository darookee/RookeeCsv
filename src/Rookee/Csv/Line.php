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
     * @var Rookee\Csv\Header
     */
    protected $_header = NULL;

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function __construct(Array $fields = array(), Header $header) {
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
            $this->_fields[$k] = $v;
        }
    }

    /**
     * getter for $_fields
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getFields() {
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

    /** Iterator **/
    /**
     * returns current index
     *
     * @return void
     * @author Nils Uliczka
     */
    public function current() {
        return ($this->offsetExists($this->_curIndex)?$this->_fields[$this->_curIndex]:null);
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
        $r = $this->offsetExists($offset)?$this->_fields[$offset]:null;
        if(!$r) {
            $hOffset = $this->_header[$offset];
            $r = $this->offsetExists($hOffset)?$this->_fields[$hOffset]:null;
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
    }
} // END class Header
