<?php
	/**
	 * URL Parser class
	 * 
	 * Parses the url and makes it accessible through an instance of this object.
	 * 
     * @author  Jorgen Evens <jorgen@wlab.be>
     * @version 0.1
     * @package WebLab
	 *
	 */
    class WebLab_Parser_URL
    {
        /**
         * Holds the parsed url
         * @var Array Holds the parts of the url.
         */
        protected $_url;

        /**
         * Constructs a new URL Parser.
         */
        public function __construct()
        {
            $this->_url = parse_url( $_SERVER['REQUEST_URI'] );
            DEFINE( 'BASE', $this->getBasePath() );
        }

        /**
         * Get a collection of URL values
         * @param Array $values Names of the values you want to retrieve.
         * @return Array Returns the computed values for the requested values.
         */
        public function get( $values )
        {
            if( !is_array( $values ) )
                throw new WebLab_Exception_Parser( 'WebLab_Parser_URL::get() only accepts arrays' );

            $array = array();
            foreach( $values as $value )
                $array[ $value ] = $this->_get( $value );

            return $array;
        }

        /**
         * Retrieves property by name
         * @param String $method The name of the property to get.
         * @return Object|bool If the requested property exists its value is returned. Otherwise false is returned.
         */
        protected function _get( $method )
        {
            $method = 'get' . ucfirst( $method );
            if( method_exists( $this, $method ) )
            {
                return $this->$method();
            }

            return false;
        }

        /**
         * The full URL of the current page.
         * @return String Returns the full URL.
         */
        public function getFullURL()
        {
            $fullUrl = $this->getProtocol() . '://' .
                $this->getHostname() . ':' . $this->getPort() .
                $this->getPath();

            return $fullUrl;
        }

        /**
         * The name of the current script running.
         * This will probably be your entry page, mostly index.php
         * @return String The name of the current script running. This will probably be your entry page, mostly index.php
         */
        public function getScriptname()
        {
            return array_pop( explode( '/', $_SERVER[ 'SCRIPT_FILENAME' ] ) );
        }

        /**
         * The basepath for urls.
         * This path exists out of
         * document_root/folder/to/application/
         * @return String Path to the current application root.
         */
        public function getBasePath()
        {
            $urlParts = explode( '/', $_SERVER[ 'SCRIPT_NAME'] );
            unset( $urlParts[ count($urlParts)-1 ] );
            $httpPath = implode( '/', $urlParts ) . '/';

            return $httpPath;
        }

        /**
         * Get the parameters supplied through URL
         * This is everything behind the basepath and the $_GET parameter
         * @return Array Everything behind the basepath and the $_GET parameter as an array.
         */
        public function getParameters()
        {
            $base = $this->getBasePath();
            $path = $this->_url['path'];

            if( $base != '/' )
                $path = str_replace( $base, '', $path );

            $params = explode( '/', $path );
            $params = array_values( array_filter( $params ) );
            
            $tmp = array();

            for( $i=0; $i<count($params);$i++ )
            {
                $param = $params[ $i ];
                if( !empty( $param ) && !is_numeric( $param ) )
                {
                    $tmp[ $param ] = $params[ $i+1 ];
                }
            }

            $tmp = array_merge( $tmp, $params );
            return array_merge( $tmp, $_GET );
        }
		
        /**
         * Get the protocol used to load this page.
         * @return String Locked at http for now.
         */
        public function getProtocol()
        {
        	// Temporary fix
        	return 'http';
        	
            return $this->_url['scheme'];
        }

        /**
         * Get the port on which the server is running.
         * @return Integer Locked at 80 for now.
         */
        public function getPort()
        {
        	// Temporary fix
        	return '80';
        	
            return $this->_url['port'];
        }

        /**
         * Get the hostname.
         * @return String Shorthand for $_SERVER['HTTP_HOST']
         */
        public function getHostname()
        {
            return $_SERVER['HTTP_HOST'];
        }

        /**
         * Get the path used to access this page.
         * @return String The path of the URL that is currently being viewed.
         */
        public function getPath()
        {
            return $this->_url['path'];
        }
        
        /**
         * Get the relative path to the current install
         * @return String The absolute path to this application.
         */
        public function getDirectory()
        {
        	$start = strpos( $this->getBasePath(), $this->getPath() ) + strlen( $this->getBasePath() );
        	$dir = substr( $this->getPath(), $start );
        	if( empty( $dir ) )
        		$dir = '/';
        	
        	if( substr( $dir, 0, 1 ) != '/' )
        		$dir = '/' . $dir;
        		
        	return $dir;
        }
    }