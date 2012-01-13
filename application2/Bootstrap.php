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
        
        $router->addRoute(
            'index',
            new Zend_Controller_Router_Route(
                '/',
                array(
                    'controller' => 'index',
                    'action'     => 'index',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'login',
            new Zend_Controller_Router_Route(
                '/login',
                array(
                    'controller' => 'session',
                    'action'     => 'login',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'logout',
            new Zend_Controller_Router_Route(
                '/logout',
                array(
                    'controller' => 'session',
                    'action'     => 'logout',
                    'module'     => 'default',
                )
            )
        );
        /*$router->addRoute(
            'signup',
            new Zend_Controller_Router_Route(
                '/signup',
                array(
                    'controller' => 'session',
                    'action'     => 'signup',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'vendor-signup',
            new Zend_Controller_Router_Route(
                '/vendor-signup',
                array(
                    'controller' => 'session',
                    'action'     => 'vendor',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'terms',
            new Zend_Controller_Router_Route(
                '/terms',
                array(
                    'controller' => 'index',
                    'action'     => 'terms',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'policy',
            new Zend_Controller_Router_Route(
                '/policy',
                array(
                    'controller' => 'index',
                    'action'     => 'policy',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'reward-store',
            new Zend_Controller_Router_Route(
                '/store',
                array(
                    'controller' => 'store',
                    'action'     => 'index',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'city',
            new Zend_Controller_Router_Route(
                '/:country/:city',
                array(
                    'controller' => 'index',
                    'action'     => 'city',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'signup_facebook',
            new Zend_Controller_Router_Route(
                '/signup/facebook',
                array(
                    'controller' => 'session',
                    'action'     => 'signupfacebook',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'signup_friends',
            new Zend_Controller_Router_Route(
                '/signup/friends',
                array(
                    'controller' => 'session',
                    'action'     => 'signupfriends',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'signup_places',
            new Zend_Controller_Router_Route(
                '/signup/places',
                array(
                    'controller' => 'session',
                    'action'     => 'signupplaces',
                    'module'     => 'default',
                )
            )
        );*/
        $router->addRoute(
            'signup-vendor',
            new Zend_Controller_Router_Route(
                '/signup/vendor',
                array(
                    'controller' => 'session',
                    'action'     => 'vendorsignup',
                    'module'     => 'default',
                )
            )
        );/*
        $router->addRoute(
            'crontasks',
            new Zend_Controller_Router_Route(
                '/crontasks/:action',
                array(
                    'controller' => 'crontasks',
                    'action'     => ':action',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'listing',
            new Zend_Controller_Router_Route(
                '/:country/:city/:listing',
                array(
                    'controller' => 'index',
                    'action'     => 'listing',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'city_filter',
            new Zend_Controller_Router_Route(
                '/:country/:city/filter/:cat/:subcat/:sort/:stars',
                array(
                    'controller' => 'index',
                    'action'     => 'city',
                    'module'     => 'default',
                    'cat'        => 'all',
                    'subcat'     => 'all',
                    'sort'       => 'newest',
                    'stars'      => 'all',
                )
            )
        );
        $router->addRoute(
            'listing_search',
            new Zend_Controller_Router_Route(
                '/search/',
                array(
                    'controller' => 'index',
                    'action'     => 'search',
                    'module'     => 'default',
                    'cat'        => 'all',
                    'subcat'     => 'all',
                    'sort'       => 'newest',
                    'stars'      => 'all',
                )
            )
        );*/
        $router->addRoute(
            'provider',
            new Zend_Controller_Router_Route(
                '/provider/:action/:task/:id/:tab',
                array(
                    'controller' => 'provider',
                    'action'     => 'listings',
                    'module'     => 'default',
                    'task'       => 'default',
                    'id'         => 'default',
                    'tab'        => 'default',
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
                )
            )
        );/*
        $router->addRoute(
            'user',
            new Zend_Controller_Router_Route(
                '/user/:action/:task/:id',
                array(
                    'controller' => 'user',
                    'action'     => 'account',
                    'module'     => 'default',
                    'task'       => 'default',
                    'id'         => 'default',
                )
            )
        );*/
        $router->addRoute(
            'cart',
            new Zend_Controller_Router_Route(
                '/cart/:action/:task/:id',
                array(
                    'controller' => 'cart',
                    'action'     => 'index',
                    'module'     => 'default',
                    'task'       => 'default',
                    'id'         => 'default'
                )
            )
        );/*
        $router->addRoute(
            'passports',
            new Zend_Controller_Router_Route(
                '/users/:username/:action/:task/:id',
                array(
                    'controller' => 'passport',
                    'action'     => 'index',
                    'module'     => 'default',
                    'username'   => '_default',
                    'task'       => 'default',
                    'id'         => 'default'
                )
            )
        );
        $router->addRoute(
            'trips_country',
            new Zend_Controller_Router_Route(
                '/trips/:country',
                array(
                    'controller' => 'trips',
                    'action'     => 'country',
                    'module'     => 'default',
                    'country'    => 'default',
                )
            )
        );
        $router->addRoute(
            'trips_index',
            new Zend_Controller_Router_Route(
                '/trips',
                array(
                    'controller' => 'trips',
                    'action'     => 'index',
                    'module'     => 'default'
                )
            )
        );
        $router->addRoute(
            'trips_view',
            new Zend_Controller_Router_Route(
                '/trips/view/:task',
                array(
                    'controller' => 'trips',
                    'action'     => 'view',
                    'module'     => 'default',
                    'task'    => 'default',
                )
            )
        );
        $router->addRoute(
            'trips_itinerary',
            new Zend_Controller_Router_Route(
                '/trips/itinerary/:task',
                array(
                    'controller' => 'trips',
                    'action'     => 'itinerary',
                    'module'     => 'default',
                    'task'    => 'default',
                )
            )
        );
        $router->addRoute(
            'trips_search',
            new Zend_Controller_Router_Route(
                '/trips/search',
                array(
                    'controller' => 'trips',
                    'action'     => 'search',
                    'module'     => 'default',
                )
            )
        );
        $router->addRoute(
            'trips_check',
            new Zend_Controller_Router_Route(
                '/trips/checkprice/:id',
                array(
                    'controller' => 'trips',
                    'action'     => 'checkprice',
                    'module'     => 'default',
                    'id'         => 'default'
                )
            )
        );*/
    }
}

