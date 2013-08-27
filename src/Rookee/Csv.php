<?php

/**
 * undocumented class
 *
 * @package default
 * @subpackage default
 * @author Nils Uliczka
 */
namespace Rookee\Csv;

class Csv implements \Iterator {

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_filename = NULL;

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_lines = array();

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_header = NULL;

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_curLineIndex = 0;

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_delimiter = ';';

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_enclosure = '"';

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_escape = "\\";

    /**
     * undocumented class variable
     *
     * @var string
     */
    protected $_linefeed = "\n";

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function __construct($fileOrLines = NULL) {
        if(is_array($fileOrLines)) {
            $this->setLines($fileOrLines);
        } elseif(!empty($fileOrLines)) {
            $this->setFilename($fileOrLines);
        }
    }

    /**
     * setter for $_filename
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setFilename($filename) {
        if(!file_exists($filename))
            throw new \Exception('CSV File not found');

        $this->_filename = $filename;
        return $this;
    }

    /**
     * getter for $_filename
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getFilename() {
        return $this->_filename;
    }

    /**
     * setter for $_lines
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setLines(Array $lines) {
        foreach($lines as $k => $line) {
            $this->_lines[$k] = $line;
        }
    }

    /**
     * reset $_lines
     *
     * @return void
     * @author Nils Uliczka
     */
    public function resetLines() {
        $this->_lines = array();
    }

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function addLine(Array $line = array()) {
        if(empty($line))
            throw new \Exception('Line may not be empty');
        $this->_lines[] = new Line($line, $this->getHeader());
    }

    /**
     * setter for $_header
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setHeader(Header $header) {
        $this->_header = $header;
    }

    /**
     * getter for $_header
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getHeader() {
        if(empty($this->_header))
            $this->readHeader();
        return $this->_header;
    }

    /**
     * reads the header
     *
     * @return void
     * @author Nils Uliczka
     */
    public function readHeader() {
        if(!empty($this->_filename)) {
            $f = fopen($this->_filename, 'r');
            $h = fgets($f);
            fclose($f);
            $header = str_getcsv($h, $this->getDelimiter(), $this->getEnclosure(), $this->getEscape());
            $this->setHeader(new Header($header));
            $this->_lines[0] = new Line($header, $this->getHeader());
            return $this->getHeader();
        } else {
            throw new \Exception('No CSV file given.');
        }
    }

    /**
     * returns $_delimiter
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getDelimiter() {
        return $this->_delimiter;
    }

    /**
     * sets delimiter
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setDelimiter($sep) {
        if(strlen($sep)>1)
            throw new \Exception('delimiter can only be one character');
        $this->_delimiter = $sep;
    }

    /**
     * returns $_enclosure
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getEnclosure() {
        return $this->_enclosure;
    }

    /**
     * sets enclosure
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setEnclosure($en) {
        if(strlen($en)>1)
            throw new \Exception('Enclosure can only be one character');
        $this->_enclosure = $en;
    }

    /**
     * returns $_escape
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getEscape() {
        return $this->_escape;
    }

    /**
     * sets $_escape
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setEscape($es) {
        if(strlen($es)>1)
            throw new \Exception('Escape can only be one character');
        $this->_escape = $es;
    }

    /**
     * returns $_linefeed
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getLinefeed() {
        return $this->_linefeed;
    }

    /**
     * sets $_linefeed
     *
     * @return void
     * @author Nils Uliczka
     */
    public function setLinefeed($str) {
        $this->_linefeed = $str;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getLines($start = 1, $end = NULL) {
        if(is_null($end))
            $end = $this->getLastIndex();
        if(!isset($this->_lines[$start]) || !isset($this->_lines[$end]))
            throw new \Exception('Lines out of range');

        for($i=$start; $i<=$end; $i++) {
            $r[$i] = $this->_lines[$i];
        }
        if(isset($r))
            return $r;
        return false;
    }

    /**
     * reads the file
     *
     * @return void
     * @author Nils Uliczka
     */
    public function read($start = 1, $limit = NULL) {
        if(isset($this->_lines[$start])) {
            if(is_null($limit))
                $end = ($this->getLastIndex()-$start);
            else
                $end = $start + $limit;
            return $this->getLines($start, $end);
        }
        if(!empty($this->_filename)) {
            if(empty($this->_header))
                $this->readHeader();
            $f = fopen($this->_filename, 'r');
            $cl = 0;
            while(!feof($f)) {
                $line = str_getcsv(fgets($f), $this->getDelimiter(), $this->getEnclosure(), $this->getEscape());
                if($cl >= $start && !empty($line[0]))
                    $lines[$cl] = new Line($line, $this->getHeader());
                if($cl == ($start+$limit)-1 && !is_null($limit))
                    break;
                $cl++;
            }
            fclose($f);
            $this->resetLines();
            $lines[0] = $this->getHeader();
            ksort($lines);
            $this->setLines($lines);
            return $this->getLines($start, ($start+$limit)-1);
        } else {
            throw new \Exception('No CSV file given.');
        }
    }

    /**
     * returns the key of the last line
     *
     * @return void
     * @author Nils Uliczka
     */
    public function getLastIndex() {
        $keys = array_keys($this->_lines);
        return $keys[count($keys)-1];
    }
    /**
    * returns the csv or writes it to file
     *
     * @return string|int csvString or bytes written
     * @author Nils Uliczka
     */
    public function write($toFile = false, $start = 0, $limit = NULL) {
        if(empty($this->_lines))
            return false;

        if(!is_null($limit))
            $end = $start + $limit;
        else
            $end = $this->getLastIndex();

        for($i=$start; $i<=$end; $i++) {
            $cline = $this->getWritableLine($i);
            if($cline)
                $lines[$i] = $cline;
        }

        $csvString = implode($this->getLinefeed(), $lines);

        if($toFile !== false && is_string($toFile)) {
            $f = fopen($toFile, 'w+');
            if(!$f)
                throw new \Exception('File '.$toFile.' could not be opened');
            $r = fwrite($f, $csvString);
            fclose($f);
            return $r;
        } else {
            return $csvString;
        }
    }

    /**
     * undocumented function
     *
     * @return string
     * @author Nils Uliczka
     */
    public function getWritableLine($line = 0) {
        if(isset($this->_lines[$line])) {
            foreach($this->_lines[$line] as $lk => $v) {
                $ret[$lk] = $this->getEnclosure() . str_replace($this->getEnclosure(), $this->getEscape().$this->getEnclosure(), $v) . $this->getEnclosure();
            }
            return implode($this->getDelimiter(), $ret);
        }
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
        return (isset($this->_lines[$this->_curLineIndex])?$this->_lines[$this->_curLineIndex]:null);
    }

    /**
     * iterator next
     *
     * @return void
     * @author Nils Uliczka
     */
    public function next() {
        $this->_curLineIndex++;
        return $this->current();
    }

    /**
     * iterator key
     *
     * @return void
     * @author Nils Uliczka
     */
    public function key() {
        return $this->_curLineIndex;
    }

    /**
     * iterator rewind
     *
     * @return void
     * @author Nils Uliczka
     */
    public function rewind() {
        $this->_curLineIndex = 0;
    }

    /**
     * iteartor valid
     *
     * @return void
     * @author Nils Uliczka
     */
    public function valid() {
        return isset($this->_lines[$this->_curLineIndex]);
    }
} // END class Csv
