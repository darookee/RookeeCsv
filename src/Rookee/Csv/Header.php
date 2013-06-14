<?php

/**
 * undocumented class
 *
 * @package default
 * @subpackage default
 * @author Nils Uliczka
 */
namespace Rookee\Csv;

class Header implements \ArrayAccess, \Iterator {

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_curIndex = 0;

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_fields = array();

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_reverseFields = array();

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function __construct(Array $fields = array()) {
        $this->setFields($fields);
    }

    /**
     * sets fields
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setFields(Array $fields) {
        $this->_fields = $fields;
        $this->_reverseFields = array_flip($this->_fields);
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
     * converts header keys
     *
     * @return void
     * @author Nils Uliczka
     */
    public function convertHeader(Array $newHeader) {
        foreach($this->_fields as $k => $v) {
            if(isset($newHeader[$v])) {
                $val = $newHeader[$v];
            } else {
                $val = $v;
            }
            $newFields[$k] = $val;
        }
        $this->_oldFields = $this->_reverseFields;
        $this->setFields($newFields);
    }


    /** Iterator **/
    /**
     * returns current index
     *
     * @return void
     * @author Nils Uliczka
     */
    public function current() {
        return (isset($this->_fields[$this->_curIndex])?$this->_fields[$this->_curIndex]:false);
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
        return $this->_curIndex;
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
        return
            isset($this->_fields[$offset]) ||
            isset($this->_reverseFields[$offset]) ||
            isset($this->_oldFields[$offset]);
    }

    /**
     * arrayaccess offsetget
     *
     * @return void
     * @author Nils Uliczka
     */
    public function offsetGet($offset) {
        $r = isset($this->_fields[$offset])?$this->_fields[$offset]:null;
        if(!$r) {
            $r = isset($this->_reverseFields[$offset])?$this->_reverseFields[$offset]:null;
        }
        if(!$r) {
            $r = isset($this->_oldFields[$offset])?$this->_oldFields[$offset]:null;
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
