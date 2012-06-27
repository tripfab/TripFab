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
        $version = $this->_getParam('version');
        
        $trips = array(
            array(
                'id'    => 'papagayo',
                'image' => 'https://static.tripfab.com/trips/papagayo/main_thumb.jpg',
                'title' => 'Allegro Papagayo Resort',
                'rating' => 4,
                'reviews' => 98,
                'location' => 'Guanacaste, Costa Rica',
                'travelers' => '2 - 100',
                'price_before' => 110,
                'price' => 75,
                'save'  => 35,
                'description' => 'Eat, drink and be merry – without having to 
                    worry about the bill. Enjoy all the freshly prepared meals, 
                    alcoholic'
            ),
            array(
                'id'    => 'riu',
                'image' => 'https://static.tripfab.com/trips/riu/main_thumb.jpg',
                'title' => 'RIU Guanacaste',
                'rating' => 4,
                'reviews' => 98,
                'location' => 'Guanacaste, Costa Rica',
                'travelers' => '2 - 100',
                'price_before' => 110,
                'price' => 75,
                'save'  => 35,
                'description' => 'You can never keep a good thing under wraps 
                    for long and though Costa Rica is no longer a well-kept 
                    secret hidden'
            ),
            array(
                'id'    => 'doubletree',
                'image' => 'https://static.tripfab.com/trips/doubletree/main_thumb.jpg',
                'title' => 'Double Tree Resort',
                'rating' => 4,
                'reviews' => 98,
                'location' => 'Puntarenas, Costa Rica',
                'travelers' => '2 - 100',
                'price_before' => 110,
                'price' => 75,
                'save'  => 35,
                'description' => 'You can never keep a good thing under wraps 
                    for long and though Costa Rica is no longer a well-kept 
                    secret hidden'
            ),
            array(
                'id'    => 'langosta',
                'image' => 'https://static.tripfab.com/trips/langosta/main_thumb.jpg',
                'title' => 'Barcelo Langosta',
                'rating' => 4,
                'reviews' => 98,
                'location' => 'Guanacaste, Costa Rica',
                'travelers' => '2 - 100',
                'price_before' => 110,
                'price' => 75,
                'save'  => 35,
                'description' => 'You can never keep a good thing under wraps 
                    for long and though Costa Rica is no longer a well-kept 
                    secret hidden'
            ),
        );
        
        $this->view->trips = $trips;
        
        $this->render('allinclusive'.$version);
    }
    
    public function tripAction()
    {
        $id = $this->_getParam('idf');
        $version = $this->_getParam('version');
        $info = array(
            'papagayo' => array(
                'rating'   => 4,
                'nights'   => 3,
                'location' => 'Guanacaste, Costa Rica',
                'price'    => 75,
                'price_entero' => 75,
                'price_decimal'=> 00,
                'price_before' => 110,
                'price_before_entero'=>110,
                'price_before_decimal'=>00,
                'save'     => 35,
                'title'    => 'Allegro Papagayo Resort',
                'description' => 'Eat, drink and be merry – without having to worry 
                    about the bill. Enjoy all the freshly prepared meals, alcoholic 
                    and nonalcoholic beverages you can handle, in addition to 
                    daily classes and activities offered by this all-inclusive 
                    beachside resort. Set amid some of Costa Rica’s most lush 
                    rainforests and unspoiled beaches, this all-inclusive won’t 
                    disappoint.',
                'sale_starts' => 'April 23th',
                'sale_ends'   => 'July 20th',
                'overview'    => array(
                    array(
                        'title' => 'General',
                        'description' => "Occidental Allegro Papagayo is a 
                            luxurious all-inclusive resort located along the 
                            dark-sand beaches of Guanacaste. The 14 buildings are
                            arranged in a chain-like fashion all the way down 
                            to the beach. ",
                        'points1' => array(
                            'Unlimited meals and snack',
                            'Unlimited juice and soft drinks',
                            'Unlimited alcoholic beverages',
                            'Tranfers to and from the Liberia airport',
                            '4 full open bars, plus a snack bar',
                            '3 restaurants',
                            'Immense swimming pool with kiddie section'
                        ),
                        'points2' => array(
                            "Kids club with supervised activities",
                            "Free babysitting",
                            "Private beach club",
                            'Shopping & mini-market',
                            '24-hour medical services',
                            'Car rental & taxi service'
                        )
                    ),
                    array(
                        'title' => 'Dining',
                        'description' => 'Occidental Allegro Papagayo is well
                            known for its two specialty a la carte restaurants 
                            and one buffet-style restaurant. Themes vary nightly.',                        
                        'points1' => array(
                            'Los Corales is an international, open-air buffet',
                            'La Trattoria is an Italian a la carte restauran',
                        ),
                        'points2' => array(
                            "La Cantina is a casual, laid-back restaurant",
                            "Snack Bar and Beach Club offer snacks and lunch"
                        )
                    ),
                    array(
                        'title' => 'Activities',
                        'points1' => array(
                            'Ping Pong',
                            'Darts',
                            'Beach Soccer',
                            'Volleyball Court',
                            'Billiards table',
                            'Water Polo',
                            'Snorkeling and kayaking equipment',
                            'Sports bar for watching the big game',
                        ),
                        'points2' => array(
                            'Sailing',
                            'Daily activities by the pool and beach',
                            'Daily entertainment for children 4-12 years old',
                            'Daily entertainment for adults',
                            'Nighttime entertainment, live music and dance ensembles, comedy routine',
                            'Aquatic sports',
                            'Disco for dancing after 10 p.m.'
                        )
                    ),
                    array(
                        'title' => 'Amenities',
                        'points1' => array(
                            'Choice of a king-size bed or two full-size beds',
                            'Balcony or terrace',
                            'Private full bath',
                            'Hair dryer',
                            'Air conditioning',
                            'Satellite TV'
                        ),
                        'points2' => array(
                            'Telephone',
                            'Coffee maker',
                            'In-room safe',
                            'Mini-fridge',
                            'Cribs and rollaway beds are available upon request'
                        )
                    ),
                ),
                'addons' => array(
                    array(
                        'title' => 'Off-Site Golf course',
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
                        'title' => 'Scuba diving',
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
                        'title' => 'Excursions to Palo Verde, Arenal Volcano, rainforest, national parks, Liberia, deep-sea fishing & other tours',
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
                        'title' => 'Motorized water sports',
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
                'expires'=>'2012-06-29 23:59:59'
            ),
        );
        
        $rooms = array(
            'papagayo' => array(
                array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => '- $20',
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
                    'price_desc' => '+ $50',
                    'price' => 50
                ),
            )
        );
        
        $activities = array(
            'papagayo' => array(
                array(
                    'id' => 'atv',
                    'name' => 'ATV Tour',
                    'price_desc' => '+$25/person',
                    'price' => 25
                ),
                array(
                    'id' => 'atv1',
                    'name' => 'ATV Tour',
                    'price_desc' => '+$20/person',
                    'price' => 20
                ),
                array(
                    'id' => 'atv2',
                    'name' => 'ATV Tour',
                    'price_desc' => '+$32/person',
                    'price' => 32
                ),
            )
        );
        
        $this->view->id = $id;
        
        $this->view->info = $info[$id];
        $this->view->rooms = $rooms[$id];        
        $this->view->activities = $activities[$id];
        
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
                '_price'    => 60,
                '_price_before' => 70,
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
                    'id' => 'atv1',
                    'name' => 'ATV Tour',
                    'price_desc' => 'Add $20 per person',
                    'price' => 20
                ),
                array(
                    'id' => 'atv2',
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
        $this->view->id = $id;
        
    }
    
    public function checkoutAction()
    {
         if(!$this->getRequest()->isPost())
            throw new Exception('Page not found');
        
        $version = $this->_getParam('version');
        
        $data = $_POST;
        $id = $data['package'];
        
        //echo '<pre>'; print_r($data); echo '</pre>'; die;
        
        $info = array(
            md5('papagayo') => array(
                'title' => 'Alegro Papagayo (All Inclusive)',
                '_price'    => 60,
                '_price_before' => 70,
                'room' => $data['room'],
            )
        );
        
        $rooms = array(
            md5('papagayo') => array(
                'standard' => array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Substract $20 per person per night',
                    'price' => -20,
                    'selected' => ($data['room'] == 'standard')
                ),
                'deluxe' => array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => ($data['room'] == 'deluxe')
                ),
                'suite' => array(
                    'id' => 'suite',
                    'name' => 'Master Suite',
                    'price_desc' => 'Add $50 per persona per night',
                    'price' => 50,
                    'selected' => ($data['room'] == 'suite')
                ),
            )
        );
        
        $activities = array(
            md5('papagayo') => array(
                'atv' => array(
                    'id' => 'atv',
                    'name' => 'ATV Tour',
                    'price_desc' => ' $25 per person',
                    'price' => 25
                ),
                'atv1' => array(
                    'id' => 'atv1',
                    'name' => 'ATV Tour',
                    'price_desc' => 'Add $20 per person',
                    'price' => 20
                ),
                'atv2' => array(
                    'id' => 'atv2',
                    'name' => 'ATV Tour',
                    'price_desc' => 'Add $32 per person',
                    'price' => 32
                ),
            )
        );
        
        $arrival = strtotime($data['date']);
        $nights  = $data['nights'];
        $travelers = (isset($data['travelers'])) ? $data['travelers'] : $data['adults'] + $data['kids'];
        
        $departure = $arrival + (86400 * $nights);
        
        $info[$id]['arrival'] = date('F jS, Y', $arrival);
        $info[$id]['departure'] = date('F jS, Y', $departure);
        $info[$id]['travelers'] = $travelers;
        $info[$id]['nights'] = $nights;
        
        $ppn = $info[$id]['_price'];
        $ppn_before = $info[$id]['_price_before'];
        $price = ($ppn + $rooms[$id][$data['room']]['price']) * $travelers * $nights;
        $price_before = ($ppn_before + $rooms[$id][$data['room']]['price']) * $travelers * $nights;
        
        $addons = 0;
        foreach($data['activities'] as $a) {
            $addons += $activities[$id][$a]['price'] * $travelers;
        }
        
        $price += $addons;
        $price_before += $addons;
        
        $save = $price_before - $price;
        
        $info[$id]['price'] = $price;
        $info[$id]['price_before'] = $price_before;
        $info[$id]['save'] = $save;
        
        $info[$id]['adults'] = (isset($data['adults'])) ? $data['adults'] : $travelers;
        $info[$id]['kids']   = (isset($data['kids'])) ? $data['kids'] : 0;
        
        $this->view->info = $info[$id];        
        $this->view->rooms = $rooms[$id];        
        $this->view->activities = $activities[$id];
        $this->view->id = $id;
        $this->view->addons = $data['activities'];
        
        $places = new WS_PlacesService();
        $this->view->countries = $places->getPlaces(2);
        
        $this->render('checkout'.$version);
    }
    
    public function chargeAction()
    {
        $this->_redirect('/en-US/landing/1/thanks');
    }
    
    public function thanksAction()
    {
        
    }
}
