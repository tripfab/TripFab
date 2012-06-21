<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LandingController
 *
 * @author magentodeveloper
 */
class LandingController extends Zend_Controller_Action {
    
    public function init()
    {
        
    }
    
    public function indexAction()
    {
        
    }
    
    public function allinclusiveAction()
    {
        
    }
    
    public function tripAction()
    {
        $id = $this->_getParam('idf');
        $version = $this->_getParam('version');
        $info = array(
            'papagayo' => array(
                'rating'   => 4,
                'nights'   => 3,
                'location' => 'Nicoya, Guanacaste, Costa Rica',
                'price'    => 999,
                'price_before' => 1499,
                'save'     => 500,
                'title'    => 'Allegro Papagayo Resort',
                'description' => 'The All-Inclusive Allegro Papagayo Resort, 
                    is located on the beach at the beautiful Gulf of Papagayo 
                    in Costa Rica. 14 three story buildings descending to the 
                    sightseeing tower and, large freeform pool and restaurants. 
                    A complimentary shuttle service is available frequently to 
                    quickly get from your room to all of the amazing amenities.',
                'sale_starts' => 'April 23th',
                'sale_ends'   => 'July 20th',
                'overview'    => array(
                    array(
                        'title' => 'General',
                        'description' => "The all-inclusive Allegro Papagayo's 
                            Spanish-style architecture situates each of 14 
                            three-story buildingsinto the hillside descending to
                            the sightseeing tower, the large, freeform pool and 
                            restaurants. A frequent, complimentary shuttle 
                            conveniently delivers guests from their room to the 
                            other buildings.",
                        'points1' => array(
                            'Unlimited meals & snacks',
                            'Transportation from and to the Airport',
                            'Full open bar',
                            'restaurants',
                            'snack bar & 4 bars'
                        ),
                        'points2' => array(
                            "1 large swimming pool with children's basin",
                            "Private beach club",
                            "Kids Club with supervised activities (ages 4-12)"
                        )
                    ),
                    array(
                        'title' => 'General',
                        'points1' => array(
                            'Unlimited meals & snacks',
                            'Transportation from and to the Airport',
                            'Full open bar',
                            'restaurants',
                            'snack bar & 4 bars'
                        ),
                        'points2' => array(
                            "1 large swimming pool with children's basin",
                            "Private beach club",
                            "Kids Club with supervised activities (ages 4-12)"
                        )
                    ),
                    array(
                        'title' => 'General',
                        'points1' => array(
                            'Unlimited meals & snacks',
                            'Transportation from and to the Airport',
                            'Full open bar',
                            'restaurants',
                            'snack bar & 4 bars'
                        ),
                    ),
                    array(
                        'title' => 'General',
                        'description' => "The all-inclusive Allegro Papagayo's 
                            Spanish-style architecture situates each of 14 
                            three-story buildingsinto the hillside descending to
                            the sightseeing tower, the large, freeform pool and 
                            restaurants. A frequent, complimentary shuttle 
                            conveniently delivers guests from their room to the 
                            other buildings.",
                    ),
                ),
                'addons' => array(
                    array(
                        'title' => 'Bungee Jumping',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur 
                            adipiscing elit. Suspendisse non leo sem, quis tempor 
                            tortor. Cras eget adipiscing purus. Cum sociis 
                            natoque penatibus et magnis dis parturient montes, 
                            nascetur ridiculus',
                        'tags' => array(
                            'Price' => 'Free',
                            'Duration' => 'Undefined',
                            'Location' => '2 miles from Hotel',
                        ),
                    ),
                    array(
                        'title' => 'Bungee Jumping',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur 
                            adipiscing elit. Suspendisse non leo sem, quis tempor 
                            tortor. Cras eget adipiscing purus. Cum sociis 
                            natoque penatibus et magnis dis parturient montes, 
                            nascetur ridiculus',
                        'tags' => array(
                            'Price' => 'Free',
                            'Duration' => 'Undefined',
                            'Location' => '2 miles from Hotel',
                        ),
                    ),
                    array(
                        'title' => 'Bungee Jumping',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur 
                            adipiscing elit. Suspendisse non leo sem, quis tempor 
                            tortor. Cras eget adipiscing purus. Cum sociis 
                            natoque penatibus et magnis dis parturient montes, 
                            nascetur ridiculus',
                        'tags' => array(
                            'Price' => 'Free',
                            'Duration' => 'Undefined',
                            'Location' => '2 miles from Hotel',
                        ),
                    ),
                    array(
                        'title' => 'Bungee Jumping',
                        'description' => 'Lorem ipsum dolor sit amet, consectetur 
                            adipiscing elit. Suspendisse non leo sem, quis tempor 
                            tortor. Cras eget adipiscing purus. Cum sociis 
                            natoque penatibus et magnis dis parturient montes, 
                            nascetur ridiculus',
                        'tags' => array(
                            'Price' => 'Free',
                            'Duration' => 'Undefined',
                            'Location' => '2 miles from Hotel',
                        ),
                    ),
                ),
                'reviews' => array(
                    array(
                        'user' => 'Susan Boyd',
                        'location' => 'Virginia Beach, Virginia',
                        'title' => 'All Pura Vida Hotel',
                        'rating' => 2,
                        'date' => 'June 17, 2012',
                        'text' => "The all-inclusive Allegro Papagayo's 
                            Spanish-style architecture situates each of 14 
                            three-story buildingsinto the hillside descending to 
                            the sightseeing tower, the large, freeform pool and 
                            restaurants. A frequent, complimentary shuttle 
                            conveniently delivers guests from their room to the 
                            other buildings.",
                    ),
                    array(
                        'user' => 'Susan Boyd',
                        'location' => 'Virginia Beach, Virginia',
                        'title' => 'All Pura Vida Hotel',
                        'rating' => 1,
                        'date' => 'June 17, 2012',
                        'text' => "The all-inclusive Allegro Papagayo's 
                            Spanish-style architecture situates each of 14 
                            three-story buildingsinto the hillside descending to 
                            the sightseeing tower, the large, freeform pool and 
                            restaurants. A frequent, complimentary shuttle 
                            conveniently delivers guests from their room to the 
                            other buildings.",
                    ),
                    array(
                        'user' => 'Susan Boyd',
                        'location' => 'Virginia Beach, Virginia',
                        'title' => 'All Pura Vida Hotel',
                        'rating' => 3,
                        'date' => 'June 17, 2012',
                        'text' => "The all-inclusive Allegro Papagayo's 
                            Spanish-style architecture situates each of 14 
                            three-story buildingsinto the hillside descending to 
                            the sightseeing tower, the large, freeform pool and 
                            restaurants. A frequent, complimentary shuttle 
                            conveniently delivers guests from their room to the 
                            other buildings.",
                    ),
                    array(
                        'user' => 'Susan Boyd',
                        'location' => 'Virginia Beach, Virginia',
                        'title' => 'All Pura Vida Hotel',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "The all-inclusive Allegro Papagayo's 
                            Spanish-style architecture situates each of 14 
                            three-story buildingsinto the hillside descending to 
                            the sightseeing tower, the large, freeform pool and 
                            restaurants. A frequent, complimentary shuttle 
                            conveniently delivers guests from their room to the 
                            other buildings.",
                    ),
                    array(
                        'user' => 'Susan Boyd',
                        'location' => 'Virginia Beach, Virginia',
                        'title' => 'All Pura Vida Hotel',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "The all-inclusive Allegro Papagayo's 
                            Spanish-style architecture situates each of 14 
                            three-story buildingsinto the hillside descending to 
                            the sightseeing tower, the large, freeform pool and 
                            restaurants. A frequent, complimentary shuttle 
                            conveniently delivers guests from their room to the 
                            other buildings.",
                    ),
                    array(
                        'user' => 'Susan Boyd',
                        'location' => 'Virginia Beach, Virginia',
                        'title' => 'All Pura Vida Hotel',
                        'rating' => 3,
                        'date' => 'June 17, 2012',
                        'text' => "The all-inclusive Allegro Papagayo's 
                            Spanish-style architecture situates each of 14 
                            three-story buildingsinto the hillside descending to 
                            the sightseeing tower, the large, freeform pool and 
                            restaurants. A frequent, complimentary shuttle 
                            conveniently delivers guests from their room to the 
                            other buildings.",
                    ),
                    array(
                        'user' => 'Susan Boyd',
                        'location' => 'Virginia Beach, Virginia',
                        'title' => 'All Pura Vida Hotel',
                        'rating' => 2,
                        'date' => 'June 17, 2012',
                        'text' => "The all-inclusive Allegro Papagayo's 
                            Spanish-style architecture situates each of 14 
                            three-story buildingsinto the hillside descending to 
                            the sightseeing tower, the large, freeform pool and 
                            restaurants. A frequent, complimentary shuttle 
                            conveniently delivers guests from their room to the 
                            other buildings.",
                    ),
                    array(
                        'user' => 'Susan Boyd',
                        'location' => 'Virginia Beach, Virginia',
                        'title' => 'All Pura Vida Hotel',
                        'rating' => 1,
                        'date' => 'June 17, 2012',
                        'text' => "The all-inclusive Allegro Papagayo's 
                            Spanish-style architecture situates each of 14 
                            three-story buildingsinto the hillside descending to 
                            the sightseeing tower, the large, freeform pool and 
                            restaurants. A frequent, complimentary shuttle 
                            conveniently delivers guests from their room to the 
                            other buildings.",
                    ),
                ),
                'features' => array(
                    'Transportation from and to the Airport',
                    'Full open bar',
                    '3 restaurants',
                    '1 snack bar & 4 bars'
                ),
            ),
        );
        
        $this->view->info = $info[$id];
        
        $this->render('trip'.$version);
    }
}
