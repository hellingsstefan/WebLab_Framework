<?php
    abstract class WebLab_Data_Adapter
    {
        public $error;
        protected $_resource;
        protected $_wildcard = '*';
        protected $_prefix = '';
        
        abstract protected function _query( $query );
        abstract public function isConnected();
        abstract public function insert_id();
        abstract public function escape_string( $str );
        abstract public function getAdapterSpecs();

        public final function query( $query )
        {
            $query = $this->_prefixTables( $query );

            $this->_query( $query );
        }

        protected function _prefixTables( $query )
        {
            if( empty( $this->_prefix ) )
            {
                return $query;
            }
            
            if( is_string( $query ) )
            {
                return $this->_prefixTablesFromString( $query );
            }elseif( $query instanceof WebLab_Data_Query )
            {
                return $this->_prefixTablesFromQuery( $query );
            }else
            {
                throw new Exception( 'An invalid query has been passed on.' );
            }
        }

        protected function _prefixTablesFromQuery( WebLab_Data_Query $query )
        {
            $tables = &$query->getTables();

            foreach( $tables as &$table )
            {
                $table->setName( $this->getPrefix() . $table->getName() );
            }

            return $query;
        }

        protected function _prefixTablesFromString( $query )
        {
            if( !is_string( $query ) )
            {
                throw new Exception( 'Expecting that the query supplied is a string.' );
            }

            // Take everything after the FROM keyword.
            $query = strtolower( $query );
            $start = strpos( $query, ' from ' ) + 6;

            if( $start < 6 )
            {
                throw new Exception( 'The FROM Keyword was not found, so this is not a valid query.' );
            }

            // explode it to get the different tables;
            $tables = explode( ',', substr( $query, $start ) );

            // Replace every table with it's prefixed form.
            foreach( $tables as $table )
            {
                $table = trim( $table ); // Remove redundant spaces.

                // If the tablename contains a space then this is the end of the table listing.
                $emptyChar = strpos( $table, ' ' );
                if( $emptyChar > -1 )
                {
                    $table = substr( $table, strlen( $table ) - $emptyChar );
                    $query = str_replace( $table, $this->getPrefix() . $table, $query );
                    break;
                }

                // Otherwise just replace the table with it's prefixed form and continue;
                $query = str_replace( $table, $this->getPrefix() . $table, $query );
            }

            var_dump( $query );
            return $query;
        }

        public function getPrefix()
        {
            return $this->_prefix;
        }

        public function setPrefix( $prefix )
        {
            $this->_prefix = $prefix;
            return $this;
        }

        public function getAdapterSpecification()
        {
            return $this->getAdapterSpecs();
        }
    }