<?php
    class WebLab_Dispatcher_Visit
    {

    	protected $_default;
    	protected $_param;
    	
        public function __construct( $default, $pattern, $param=null )
        {
            $this->setPattern( $pattern )
                ->setDefault( $default );
            
			if( !is_array( $param ) ) {
				$url = WebLab_Config::getInstance()->get( 'Application.Runtime.URL' )->toArray();
				$this->_param = $url['parameters'];
			} else
				$this->_param = $param;
        }
        
        public function execute()
        {
            $moduleAliasses = WebLab_Config::getInstance()->get( 'Application.Modules.Aliasses' )->toArray();

            $module = isset( $this->_param[0] ) ? $this->_param[0] : '';

            if( isset( $moduleAliasses[ $module ] ) )
            {
                $module = $moduleAliasses[ $module ];
            }

            if( $module )
            {
                $module = $this->classFromPattern( $module );
                if( class_exists( $module ) )
                {
                    return new $module( $this->_param );
                }else
                {
                    $module = $this->classFromPattern( $this->_default );
                    return new $module( $this->_param );
                }
            }else
            {
                $module = $this->classFromPattern( $this->_default );
                return new $module( $this->_param );
            }
        }

        public function classFromPattern( $variable )
        {
            return str_replace( '{*}', ucfirst( $variable ), $this->_pattern );
        }

        final public function setPattern( $pattern )
        {
            $this->_pattern = $pattern;
            return $this;
        }

        final protected function setDefault( $class )
        {
            $this->_default = $class;
            return $this;
        }

    }