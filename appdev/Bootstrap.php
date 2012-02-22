<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Remove default routes in order to improve security
     * Set just the routes that we are going to need
     */
    public function _initRoutes()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $router = $frontController->getRouter();
        $router->removeDefaultRoutes();
        
        $languages = array('en'=>'en-US','es'=>'es-ES');
        $lang = $this->getDefaultLanguage();
        $lang = strtolower($lang[0].$lang[1]);
        $lang = (isset($languages[$lang])) ? $languages[$lang] : 'es-US';
        
        $router->addRoute(
            'index',
            new Zend_Controller_Router_Route(
                '/',
                array(
                    'controller' => 'index',
                    'action'     => 'index',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'indexLang',
            new Zend_Controller_Router_Route(
                '/:lang',
                array(
                    'controller' => 'index',
                    'action'     => 'index',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'login',
            new Zend_Controller_Router_Route(
                '/:lang/login',
                array(
                    'controller' => 'session',
                    'action'     => 'login',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'logout',
            new Zend_Controller_Router_Route(
                '/:lang/logout',
                array(
                    'controller' => 'session',
                    'action'     => 'logout',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'signup',
            new Zend_Controller_Router_Route(
                '/:lang/signup',
                array(
                    'controller' => 'session',
                    'action'     => 'signup',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'about',
            new Zend_Controller_Router_Route(
                '/:lang/about',
                array(
                    'controller' => 'index',
                    'action'     => 'about',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'faqs',
            new Zend_Controller_Router_Route(
                '/:lang/faqs',
                array(
                    'controller' => 'index',
                    'action'     => 'faqs',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'terms',
            new Zend_Controller_Router_Route(
                '/:lang/terms',
                array(
                    'controller' => 'index',
                    'action'     => 'terms',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'city',
            new Zend_Controller_Router_Route(
                '/:lang/:country/:city',
                array(
                    'controller' => 'index',
                    'action'     => 'city',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'signup_facebook',
            new Zend_Controller_Router_Route(
                '/:lang/signup/facebook',
                array(
                    'controller' => 'session',
                    'action'     => 'signupfacebook',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'signup_friends',
            new Zend_Controller_Router_Route(
                '/:lang/signup/friends',
                array(
                    'controller' => 'session',
                    'action'     => 'signupfriends',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'signup_places',
            new Zend_Controller_Router_Route(
                '/:lang/signup/places',
                array(
                    'controller' => 'session',
                    'action'     => 'signupplaces',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'signup-vendor',
            new Zend_Controller_Router_Route(
                '/:lang/signup/vendor',
                array(
                    'controller' => 'session',
                    'action'     => 'vendorsignup',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'crontasks',
            new Zend_Controller_Router_Route(
                '/:lang/crontasks/:action',
                array(
                    'controller' => 'crontasks',
                    'action'     => ':action',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'listing',
            new Zend_Controller_Router_Route(
                '/:lang/:country/:city/:listing',
                array(
                    'controller' => 'index',
                    'action'     => 'listing',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'reset',
            new Zend_Controller_Router_Route(
                '/:lang/reset/:email/:token/',
                array(
                    'controller' => 'index',
                    'action'     => 'reset',
                    'module'     => 'default',
                    'email'      => 'default',
                    'token'      => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'provider',
            new Zend_Controller_Router_Route(
                '/:lang/provider/:action/:task/:id/:tab',
                array(
                    'controller' => 'provider',
                    'action'     => 'listings',
                    'module'     => 'default',
                    'task'       => 'default',
                    'id'         => 'default',
                    'tab'        => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'ajax',
            new Zend_Controller_Router_Route(
                '/ajax/:action/:id',
                array(
                    'controller' => 'ajax',
                    'action'     => ':action',
                    'module'     => 'default',
                    'id'         => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'user',
            new Zend_Controller_Router_Route(
                '/:lang/user/:action/:task/:id',
                array(
                    'controller' => 'user',
                    'action'     => 'account',
                    'module'     => 'default',
                    'task'       => 'default',
                    'id'         => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'cart',
            new Zend_Controller_Router_Route(
                '/:lang/cart/:action/:task/:id',
                array(
                    'controller' => 'cart',
                    'action'     => 'index',
                    'module'     => 'default',
                    'task'       => 'default',
                    'id'         => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'passports',
            new Zend_Controller_Router_Route(
                '/:lang/users/:username/:action/:task/:id',
                array(
                    'controller' => 'passport',
                    'action'     => 'index',
                    'module'     => 'default',
                    'username'   => '_default',
                    'task'       => 'default',
                    'id'         => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'phone',
            new Zend_Controller_Router_Route(
                '/phone/:action',
                array(
                    'controller' => 'calls',
                    'action'     => 'index',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'trips_country',
            new Zend_Controller_Router_Route(
                '/:lang/trips/:country',
                array(
                    'controller' => 'trips',
                    'action'     => 'country',
                    'module'     => 'default',
                    'country'    => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'trips_index',
            new Zend_Controller_Router_Route(
                '/:lang/trips',
                array(
                    'controller' => 'trips',
                    'action'     => 'index',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'trips_view',
            new Zend_Controller_Router_Route(
                '/:lang/trips/view/:task',
                array(
                    'controller' => 'trips',
                    'action'     => 'view',
                    'module'     => 'default',
                    'task'       => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'trips_itinerary',
            new Zend_Controller_Router_Route(
                '/:lang/trips/itinerary/:task',
                array(
                    'controller' => 'trips',
                    'action'     => 'itinerary',
                    'module'     => 'default',
                    'task'       => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'trips_search',
            new Zend_Controller_Router_Route(
                '/:lang/trips/search',
                array(
                    'controller' => 'trips',
                    'action'     => 'search',
                    'module'     => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'trips_check',
            new Zend_Controller_Router_Route(
                '/:lang/trips/checkprice/:id',
                array(
                    'controller' => 'trips',
                    'action'     => 'checkprice',
                    'module'     => 'default',
                    'id'         => 'default',
                    'lang'       => $lang
                )
            )
        );
        $router->addRoute(
            'admin',
            new Zend_Controller_Router_Route(
                '/admin/:action/:task/:id',
                array(
                    'controller' => 'admin',
                    'action'     => 'listings',
                    'module'     => 'default',
                    'task'       => 'default',
                    'id'         => 'default',
                )
            )
        );
    }
    
    
    
    private function getDefaultLanguage() {
       if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
          return $this->parseDefaultLanguage($_SERVER["HTTP_ACCEPT_LANGUAGE"]);
       else
          return $this->parseDefaultLanguage(NULL);
    }

    private function parseDefaultLanguage($http_accept, $deflang = "en") {
        if(isset($http_accept) && strlen($http_accept) > 1)  
        {
            $x = explode(",",$http_accept);
            foreach ($x as $val) {
                if(preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i",$val,$matches))
                    $lang[$matches[1]] = (float)$matches[2];
                else
                    $lang[$val] = 1.0;
            }

        
            $qval = 0.0;
            foreach ($lang as $key => $value) {
                if ($value > $qval) {
                    $qval = (float)$value;
                    $deflang = $key;
                }
            }
        }
        return strtolower($deflang);
    }
    
    protected function _initLogger()
    {
        $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH.'/logs/activity.log');
        $logger = new Zend_Log($writer);
        Zend_Registry::set('activity_logger', $logger);
    }
    
    protected function _initStripeConfig()
    {
        $config = $this->getOption('stripe');
        Zend_Registry::set('stripe', $config);
    }
    
    protected function _initCache()
    {
        $config = $this->getOption('memcached');
        $back = new Zend_Cache_Backend_Memcached(array(
            'servers'  => array(array(
                'host' => $config['host'],
                'port' => $config['port'],
            )),
            'compression' => $config['compression'],
        ));
        
        $front = new Zend_Cache_Core(array(
            'caching'                 => true,
            'cache_id_prefix'         => $config['prefix'],
            'write_control'           => false,
            'automatic_serialization' => true,
            'ignore_user_abort'       => true
        ));
        
        $Cache = Zend_Cache::factory($front, $back);
        Zend_Registry::set('cache', $Cache);
    }
    
    protected function _initTwilioConfig()
    {
        $config = $this->getOption('twilio');
        Zend_Registry::set('twilio', $config);
    }
    
    protected function _initSSL()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()){
            if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'on') {
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"]);
            }
        }
    }
    
    protected function _initThumbnailSettings()
    {
        $convert = $this->getOption('convert');
        Zend_Registry::set('convert',$convert['path']);
    }
}

