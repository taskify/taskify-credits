<?php

abstract class ConnectionSettings {

    private $host;
    private $user;
    private $password;
    private $database;

    protected function setHost( $host ) { $this->host = $host; }
    public function getHost() { return $this->host; }
    protected function setUser( $user ) { $this->user = $user; }
    public function getUser() { return $this->user; }
    protected function setPassword( $password ) { $this->password = $password; }
    public function getPassword() { return $this->password; }
    public function setDatabase( $database ) { $this->database = $database; }
    public function getDatabase() { return $this->database; }

}

class Database {
    
    private static $__instance;
    private $__handle;
    private $__connectionSettings;
    public $debug = false;
    
    private function __construct( ConnectionSettings $connectionSettings ) {
        $this->open( $connectionSettings );
    }

    public function selectRowZeroColumn( $query , $strip=FALSE , $columnNumber=0 )  {
        $returnValue = '';  
        $result = $this->query( $query );
        if( mysql_num_rows($result) > 0 ){
            $returnValue = mysql_result( $result , $columnNumber ); 
            if($strip) {
                $returnValue = stripslashes($returnValue);
            }
    
        }
        mysql_free_result($result);
        return $returnValue;
    }

    public function select( $query ) {
        $result = $this->query( $query );
        $output = array();
        while( $data = mysql_fetch_assoc($result) ) {
            $output[] = $data;
        }
        mysql_free_result($result);
        return ( count($output) > 0 ) ? $output : FALSE ;
    }
    
    public function selectColumnZero( $query ) {
        $result = $this->query( $query );
        $output = array();
        while( $data = mysql_fetch_assoc($result) ) {
            $output[] = array_shift($data);
        }
        mysql_free_result($result);
        return ( count($output) > 0 ) ? $output : FALSE ;
    }

    public function insert( $query ) {
        $result = $this->query( $query );
        $last = mysql_insert_id( $this->__handle );
        if ($last !== 0) {
            $output = $last;
        } else {
            $output = false;
        }
        return $output;
    }

	public function insertMultiple($sql, $rows, $chunks=50) {
		$parts = array_chunk($rows, $chunks);
		foreach($parts as $part) {
			$out = array();
			foreach($part as $row) {
				$temp = array();
				foreach($row as $v) $temp[] = "'" . $this->clean($v) . "'";
				$out[] = implode(',',$temp);
			}
			$this->insert( sprintf( $sql . ' VALUES (%s)', implode('),(',$out) ) );
		}
	}
	
    public function update( $query ) {
        return $this->query( $query );
    }

    public function delete( $query ) {
        return $this->query( $query );
    }

    public function query( $query ) {
        $this->keepAlive();
        if($this->debug) echo $query . PHP_EOL;
        $result = mysql_query( $query , $this->__handle );
        // TODO implement timer for debug mode
        if( $result === FALSE ) {
            //TODO error reporting
            throw new Exception( 'sql failed: ' . $query );
        }
        return $result;
    }

    public function open( ConnectionSettings $connectionSettings ) {
        $this->__connectionSettings = $connectionSettings;
        $this->__handle = mysql_connect(
            $this->__connectionSettings->getHost(),
            $this->__connectionSettings->getUser(),
            $this->__connectionSettings->getPassword(),
            true
        );
        if( !is_resource($this->__handle) ) {
            throw new Exception( 'cannot connect to mysql server' );
        }
        $this->selectDatabase( $this->__connectionSettings->getDatabase() );
    }

    public function isConnected() {
        if( !is_resource($this->__handle) ) {
            return FALSE;
        }
        return mysql_ping($this->__handle);
    }
    
    public function close() {
        if( is_resource($this->__handle) ) {
            mysql_close( $this->__handle );
        }
    }

    public function keepAlive() {
        if( !$this->isConnected() ) {
            $this->open( $this->__connectionSettings );
        }
    }

    public function selectDatabase( $database ) {
        if( !mysql_select_db( $database , $this->__handle ) ) {
            // TODO error reporting
            throw new Exception( 'cannot select specified database: ' . $database );
        }
        $this->__connectionSettings->setDatabase( $database );
    }

    public function currentDatabase() {
        return $this->__connectionSettings->getDatabase();
    }

    public function clean( $parameter ) {
        if( function_exists('mysql_real_escape_string') ) {
            return mysql_real_escape_string( $this->trimMagicQuotes($parameter) );
        } else {
            return $parameter;
        }
    }
    
    public function quote( $parameter ) {
      return "'" . $this->clean($parameter) . "'";
    }

    private function trimMagicQuotes( $parameter ) {
        if( function_exists('get_magic_quotes_gpc') ) {
            if( get_magic_quotes_gpc() ) {
                return stripslashes( $parameter );
            }
        }
        return $parameter;
    }

    public static function getInstance( ConnectionSettings $connectionSettings = null ) {
        if( self::$__instance == NULL ) {
            if( $connectionSettings == NULL ) {
                throw new Exception( 'Database has not been instantiated yet' );
            }
            self::$__instance = new self( $connectionSettings );
        }
        return self::$__instance;
    }
}
