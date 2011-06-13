<?php
	/**
	 * 
	 * Represents a table in the database.
	 * 
	 * @author Jorgen Evens
	 *
	 */
   class WebLab_Data_Table
    {
        protected $_name;
        
        protected $_alias;
        
        protected $_fields = array();

        public function __construct( $name, $fields=null )
        {
            $this->_name = $name;
            
            if( is_array( $fields ) )
                call_user_func_array( array( $this, 'addFields'), $fields );
            
        }

        public function __get( $name )
        {
            return $this->getField( $name );
        }

        /**
         * 
         * Add a series of fields to this table.
         */
        public function addFields()
        {
            $fields = func_get_args();
            
            foreach( $fields as &$field )
                $this->addField( $field );

            unset( $field );
            unset( $fields );

            return $this;
        }

        /**
         * 
         * Add a column to this table by it's string name.
         * 
         * @param string $field
         */
        public function addField( $field=null )
        {
            if( is_string( $field ) )
                $field = new WebLab_Data_Field( $field );

            $field->setTable( $this );
            $this->_fields[ $field->getName() ] = $field;

            return $field;
        }

        /**
         * 
         * Remove a column from this table.
         * @param string $name
         */
        public function removeField( $name )
        {
            unset( $this->_fields[ $name ] );

            return $this;
        }

        public function getField( $name )
        {
            if( !isset( $this->_fields[ $name ] ) )
                throw new Exception( 'Field "' . $name . '" not in table.' );

            return $this->_fields[ $name ];
        }

        public function getFields()
        {
            return $this->_fields;
        }

        public function getName()
        {
            return $this->_name;
        }

        public function setName( $name )
        {
            $this->_name = $name;
        }

        public function getAlias()
        {
            return $this->_alias;
        }

        public function setAlias( $alias )
        {
            if( !is_string( $alias ) )
                throw new WebLab_Exception_Data( 'Alias should be of type string.' );

            $this->_alias = $alias;

            return $this;
        }

        public function setValues( $values ){
        	if( !is_array( $values ) )
        		throw new WebLab_Exception_Data( 'Expecting an array of key value pairs.' );
        		
        	foreach( $values as $field => &$value )
        		if( isset( $this->_fields[$field] ) )
        			$this->_fields[$field]->setValue( $value );
        		else
        			throw new WebLab_Exception_Data( 'Field ' . $field . ' not in this table.' );
        			
        	return $this;
        }
        
        public function __toString()
        {
            return $this->getName();
        }

    }