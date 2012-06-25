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
                'price_entero' => 998,
                'price_decimal'=> 45,
                'price_before' => 1499,
                'price_before_entero'=>1498,
                'price_before_decimal'=>48,
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
                    '1 snack bar & 4 bars',
                    '1 snack bar & 4 bars'
                ),
                'images' => array(
                    array(
                        'url' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'thumb' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'desc' => 'You will see beautiful birds in the hiking activities'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'thumb' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'desc' => 'You will see beautiful birds in the hiking activities'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'thumb' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'desc' => 'You will see beautiful birds in the hiking activities'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'thumb' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'desc' => 'You will see beautiful birds in the hiking activities'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'thumb' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'desc' => 'You will see beautiful birds in the hiking activities'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'thumb' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'desc' => 'You will see beautiful birds in the hiking activities'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'thumb' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'desc' => 'You will see beautiful birds in the hiking activities'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'thumb' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'desc' => 'You will see beautiful birds in the hiking activities'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'thumb' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'desc' => 'You will see beautiful birds in the hiking activities'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'thumb' => 'https://static.tripfab.com/images6/tripsView4ico3.png',
                        'desc' => 'You will see beautiful birds in the hiking activities'
                    ),
                ),
                'dining' => 'Occidental Allegro Papagayo is well known for its 
                    two specialty a la carte restaurants and one buffet-style 
                    restaurant. Themes vary nightly.',
                'restaurants' => array(
                    array(
                        'title' => 'Los Corales',
                        'description' => 'Los Corales is an international, 
                            open-air buffet that is open for breakfast, lunch 
                            and dinner. It includes a seafood buffet with nightly 
                            lobster and jumbo shrimp. Dress code is casual.',
                    ),
                    array(
                        'title' => 'Los Corales',
                        'description' => 'Los Corales is an international, 
                            open-air buffet that is open for breakfast, lunch 
                            and dinner. It includes a seafood buffet with nightly 
                            lobster and jumbo shrimp. Dress code is casual.',
                    ),
                    array(
                        'title' => 'Los Corales',
                        'description' => 'Los Corales is an international, 
                            open-air buffet that is open for breakfast, lunch 
                            and dinner. It includes a seafood buffet with nightly 
                            lobster and jumbo shrimp. Dress code is casual.',
                    ),
                    array(
                        'title' => 'Los Corales',
                        'description' => 'Los Corales is an international, 
                            open-air buffet that is open for breakfast, lunch 
                            and dinner. It includes a seafood buffet with nightly 
                            lobster and jumbo shrimp. Dress code is casual.',
                    ),
                ),
                'overview2' => array(
                    array(
                        'title' => 'Activities',
                        'points1' => array(
                            'Ping Pong',
                            'Darts',
                            'Beach Soccer',
                            'Volleyball Court',
                        ),
                        'points2' => array(
                            'Ping Pong',
                            'Darts',
                            'Beach Soccer',
                            'Volleyball Court',
                            'Beach Soccer',
                            'Volleyball Court',
                        ),
                    ),
                    array(
                        'title' => 'Activities',
                        'points1' => array(
                            'Ping Pong',
                            'Darts',
                            'Beach Soccer',
                            'Volleyball Court',
                        ),
                        'points2' => array(
                            'Ping Pong',
                            'Darts',
                            'Beach Soccer',
                            'Volleyball Court',
                            'Beach Soccer',
                            'Volleyball Court',
                        ),
                    ),
                    array(
                        'title' => 'Activities',
                        'points1' => array(
                            'Ping Pong',
                            'Darts',
                            'Beach Soccer',
                            'Volleyball Court',
                        ),
                        'points2' => array(
                            'Ping Pong',
                            'Darts',
                            'Beach Soccer',
                            'Volleyball Court',
                            'Beach Soccer',
                            'Volleyball Court',
                        ),
                    ),
                    array(
                        'title' => 'Activities',
                        'points1' => array(
                            'Ping Pong',
                            'Darts',
                            'Beach Soccer',
                            'Volleyball Court',
                        ),
                        'points2' => array(
                            'Ping Pong',
                            'Darts',
                            'Beach Soccer',
                            'Volleyball Court',
                            'Beach Soccer',
                            'Volleyball Court',
                        ),
                    ),
                ),
            ),
        );
        
        $this->view->info = $info[$id];
        
        $this->render('trip'.$version);
    }
    
    public function confirmAction()
    {
        if(!$this->getRequest()->isPost())
            throw new Exception('Page not found');
        
        $version = $this->_getParam('version');
        if($version != 1) 
            throw new Exception('Page not found');
        
        $id = $this->_getParam('idf');
        $data = $_POST;
        
        $info = array(
            'papagayo' => array(
                'title' => 'Alegro Papagayo (All Inclusive)',
                '_price'    => 999,
                '_price_before' => 1499,
                'room' => 'Deluxe',
            )
        );
        
        $rooms = array(
            'papagayo' => array(
                array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Substract $20 per person per night',
                    'price' => -20
                ),
                array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => 1
                ),
                array(
                    'id' => 'suite',
                    'name' => 'Master Suite',
                    'price_desc' => 'Add $50 per persona per night',
                    'price' => 50
                ),
            )
        );
        
        $activities = array(
            'papagayo' => array(
                array(
                    'id' => 'atv',
                    'name' => 'ATV Tour',
                    'price_desc' => ' $25 per person',
                    'price' => 25
                ),
                array(
                    'id' => 'atv',
                    'name' => 'ATV Tour',
                    'price_desc' => 'Add $20 per person',
                    'price' => 20
                ),
                array(
                    'id' => 'atv',
                    'name' => 'ATV Tour',
                    'price_desc' => 'Add $32 per person',
                    'price' => 32
                ),
            )
        );
        
        
        
        
        $arrival = strtotime($data['date']);
        $nights  = $data['nights'];
        $travelers = $data['travelers'];
        
        $departure = $arrival + (86400 * $nights);
        
        $info[$id]['arrival'] = date('F jS, Y', $arrival);
        $info[$id]['departure'] = date('F jS, Y', $departure);
        $info[$id]['travelers'] = $travelers;
        $info[$id]['nights'] = $nights;
        
        $ppn = $info[$id]['_price'];
        $ppn_before = $info[$id]['_price_before'];
        $price = $ppn * $travelers * $nights;
        $price_before = $ppn_before * $travelers * $nights;
        
        $save = $price_before - $price;
        
        $info[$id]['price'] = $price;
        $info[$id]['price_before'] = $price_before;
        $info[$id]['save'] = $save;
        
        $this->view->info = $info[$id];        
        $this->view->rooms = $rooms[$id];        
        $this->view->activities = $activities[$id];
        
    }
}
