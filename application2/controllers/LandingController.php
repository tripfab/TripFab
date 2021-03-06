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

    public function init() {
        
    }

    public function indexAction() {
        
    }

    public function allinclusiveAction() {
        $version = $this->_getParam('version');

        $trips = array(
            array(
                'id' => 'papagayo',
                'image' => 'https://static.tripfab.com/trips/papagayo/main_thumb.jpg',
                'title' => 'Allegro Papagayo Resort',
                'duration'=>2,
                'rating' => 4,
                'reviews' => 9,
                'location' => 'Guanacaste, Costa Rica',
                'travelers' => 'Min 2',
                'price_before' => 248,
                'price' => 109,
                'save' => 139,
                'description' => 'Eat, drink and be merry – without having to 
                    worry about the bill. Enjoy all the freshly prepared meals, 
                    alcoholic'
            ),
            array(
                'id' => 'riu',
                'image' => 'https://static.tripfab.com/trips/riu/main_thumb.jpg',
                'title' => 'RIU Guanacaste',
                'duration'=>2,
                'rating' => 4,
                'reviews' => 10,
                'location' => 'Guanacaste, Costa Rica',
                'travelers' => 'Min 2',
                'price_before' => 210,
                'price' => 166,
                'save' => 44,
                'description' => 'With delicious food, beautiful beaches, 
                    relaxing jacuzzis, a gym, sauna, spa and an amazing 
                    all-inclusive'
            ),
            array(
                'id' => 'doubletree',
                'image' => 'https://static.tripfab.com/trips/doubletree/main_thumb.jpg',
                'title' => 'DoubleTree Resort',
                'duration'=>2,
                'rating' => 4,
                'reviews' => 9,
                'location' => 'Puntarenas, Costa Rica',
                'travelers' => 'Min 2',
                'price_before' => 133,
                'price' => 90,
                'save' => 43,
                'description' => 'From the fresh-out-of-the oven “welcome cookie”
                    to the afternoons kayaking on the Pacific, this is a guaranteed 
                    trip of a'
            ),
            array(
                'id' => 'langosta',
                'image' => 'https://static.tripfab.com/trips/langosta/main_thumb.jpg',
                'title' => 'Barcelo Langosta',
                'duration'=>2,
                'rating' => 4,
                'reviews' => 11,
                'location' => 'Guanacaste, Costa Rica',
                'travelers' => 'Min 2',
                'price_before' => 180,
                'price' => 120,
                'save' => 60,
                'description' => 'Be treated like royalty for a day – or as many 
                    days as you like. Indulge in some local fare with complimentary 
                    breakfast'
            ),
        );

        $this->view->trips = $trips;

        $this->render('allinclusive' . $version);
    }

    public function tripAction() {
        $id = $this->_getParam('idf');
        $version = $this->_getParam('version');
        $info = array(
            'papagayo' => array(
                'rating' => 4,
                'nights' => 2,
                'location' => 'Guanacaste, Costa Rica',
                'price' => 109,
                'price_entero' => 109,
                'price_decimal' => 00,
                'price_before' => 248,
                'price_before_entero' => 248,
                'price_before_decimal' => 00,
                'save' => 139,
                'title' => 'Allegro Papagayo Resort',
                'description' => 'Eat, drink and be merry – without having to worry 
                    about the bill. Enjoy all the freshly prepared meals, alcoholic 
                    and nonalcoholic beverages you can handle, in addition to 
                    daily classes and activities offered by this all-inclusive 
                    beachside resort. Set amid some of Costa Rica’s most lush 
                    rainforests and unspoiled beaches, this all-inclusive won’t 
                    disappoint.',
                'sale_starts' => 'July 1st',
                'sale_ends' => 'December 31st',
                'overview' => array(
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
                            'La Trattoria is an Italian a la carte restaurant',
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
                        'user' => 'Kristen',
                        'location' => 'Bloomington, Indiana',
                        'title' => 'Costa Rica is GOREOUS!!',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "Costa Rica is GOREOUS!! I saw so many animals 
                            it wasn’t even FUNNY. There were black “Congo” monkeys 
                            almost every single morning and they got SO CLOSE we
                            took tons of pictures. There are a lot of steep hills 
                            and my parents usually took the shuttle that is always 
                            running, but I liked to walk for extra exercise. 
                            Especially after eating so much at the buffet lol.",
                    ),
                    array(
                        'user' => 'Brandon',
                        'location' => 'Tampa, Florida',
                        'title' => 'Very nice hotel',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "I was there for a week and thought it was a 
                            very nice hotel. Would visit again. Beach was nice.
                            Rainy season, so often showers in the afternoon.",
                    ),
                    array(
                        'user' => 'Allison Suri',
                        'location' => 'Kansas City, Kansas',
                        'title' => 'love the pool',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "This was my first time out of the country and 
                            i was nervous because i don’t speak any spanish, 
                            but the staff was totally friendly and almost everyone 
                            spoke perfect english. love the pool i got so sunburned 
                            the first day do NOT forget your’e sunblock!",
                    ),
                    array(
                        'user' => 'Stephanie',
                        'location' => 'New York, New York',
                        'title' => 'This is a seriously gorgeous area',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "I loved doing yoga on the beach every morning.
                            This is a seriously gorgeous area. I found that there 
                            were not a whole lot of vegetarian options for very 
                            strict vegetarians, but the staff was super friendly 
                            and always willing to make me something special.",
                    ),
                    array(
                        'user' => 'Thomas',
                        'location' => 'Washington, D.C',
                        'title' => 'I love almost every day I was there',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "I’m an intermediate scuba diver, and found 
                            the diving here phenomenal. We saw all kinds of fish,
                            sea turtles, dolphins, and even a humpback whale!! 
                            Also clown shrimp and a couple different kinds of 
                            rays. I dove almost every day I was there. My wife 
                            really liked the spa and came along on the boat to 
                            snorkel a couple of times.",
                    ),
                    array(
                        'user' => 'Signe',
                        'location' => 'Orange County, California',
                        'title' => 'WOW!!! I friggin’ love this place',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "WOW!!! I friggin’ love this place. That’s all 
                            I gotta say about that!!",
                    ),
                    array(
                        'user' => 'Sinead',
                        'location' => 'Cork, Ireland',
                        'title' => 'Sunset sailing is a MUST',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "I came for two weeks and had an amazing time. Sunset sailing is a MUST.",
                    ),
                    array(
                        'user' => 'Olger',
                        'location' => 'Los Angeles, CA',
                        'title' => 'I liked the pool and the nighttime stuff',
                        'rating' => 2,
                        'date' => 'June 17, 2012',
                        'text' => "Meh. It was OK. Food was mediocre. 
                            The beach was nice. I also liked the pool and the 
                            nighttime stuff",
                    ),
                    array(
                        'user' => 'Alisande',
                        'location' => 'Alpharetta, GA',
                        'title' => 'Great staff, they were WONDERFUL with the kids',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "Great staff, they were WONDERFUL with the 
                            kids. My husband and I are usually a bit paranoid 
                            with about who we leave them with (they are only 3 
                            and 5), but we felt really comfortable here with the 
                            daytime activities. They had so much fun. Licors they 
                            put in the drinks were kinda nasty like in most 
                            all-inclusives but other than that it was an ideal 
                            vacation. ",
                    ),
                ),
                'features' => array(
                    'Unlimited meals and snack',
                    'Unlimited juice and soft drinks',
                    'Unlimited alcoholic beverages',
                    'Tranfers to and from the Liberia airport',
                    '4 full open bars, plus a snack bar',
                    '3 restaurants',
                    'Immense swimming pool with kiddie section',
                    'Kids club with supervised activities',
                ),
                'images' => array(
                    array(
                        'url' => 'https://static.tripfab.com/trips/papagayo/main.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/papagayo/main_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/papagayo/main_big.jpg',
                        'desc' => 'The free-form pool is just one of many unique aspects of this beachside all-inclusive'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/papagayo/1.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/papagayo/1_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/papagayo/1_big.jpg',
                        'desc' => 'Sit back and relax on a lounge chair, drink in hand'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/papagayo/2.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/papagayo/2_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/papagayo/2_big.jpg',
                        'desc' => 'Views from the balcony are stunning'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/papagayo/3.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/papagayo/3_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/papagayo/3_big.jpg',
                        'desc' => 'World class scuba diving, snorkeling and sailing are just a short boat ride away'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/papagayo/4.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/papagayo/4_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/papagayo/4_big.jpg',
                        'desc' => 'Stroll down the quiet beach at sunrise or sunset for a real treat'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/papagayo/5.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/papagayo/5_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/papagayo/5_big.jpg',
                        'desc' => 'With various room types, there is something here for everyone'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/papagayo/6.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/papagayo/6_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/papagayo/6_big.jpg',
                        'desc' => "Families, couples or solo travelers alike won't be able to resist the free-form pool"
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/papagayo/7.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/papagayo/7_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/papagayo/7_big.jpg',
                        'desc' => 'The panoramic vistas of the Gulf of Papagayo are truly breathtaking'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/papagayo/8.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/papagayo/8_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/papagayo/8_big.jpg',
                        'desc' => 'At night, enjoy succulent food at one of the many restaurants – then dancing'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/papagayo/9.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/papagayo/9_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/papagayo/9_big.jpg',
                        'desc' => 'Laze about in a hammock, letting all of your cares dissolve into the cool breeze'
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
                        'title' => 'La Trattoria',
                        'description' => 'La Trattoria is an Italian a la carte 
                            restaurant where food is ordered off of a menu. Dress 
                            code is a bit more formal than at Los Corales',
                    ),
                    array(
                        'title' => 'La Cantina',
                        'description' => 'La Cantina is a casual, laid-back 
                            restaurant with made-to-order, menu food',
                    ),
                    array(
                        'title' => 'Snack Bar and Beach Club',
                        'description' => 'Snack Bar and Beach Club offer snacks and lunch',
                    ),
                ),
                'expires' => '2012-06-29 23:59:59'
            ),
            'riu' => array(
                'rating' => 4,
                'nights' => 2,
                'location' => 'Guanacaste, Costa Rica',
                'price' => 166,
                'price_entero' => 166,
                'price_decimal' => 00,
                'price_before' => 210,
                'price_before_entero' => 210,
                'price_before_decimal' => 00,
                'save' => 44,
                'title' => 'RIU Guanacaste',
                'description' => 'With delicious food, beautiful beaches, 
                    relaxing jacuzzis, a gym, sauna, spa and an amazing 
                    all-inclusive plan, Hotel Riu Guanacaste is the perfect d
                    estination for a carefree trip to Costa Rica. The expansive
                    property features 701 rooms,  3 specialty restaurants and an 
                    immense and luxurious swimming pool.',
                'sale_starts' => 'July 1st',
                'sale_ends' => 'December 31st',
                'overview' => array(
                    array(
                        'title' => 'General',
                        'description' => "The all-inclusive Riu resort is situated 
                            right on the dark sands of Playa Matapalo beach, where 
                            sea turtles come to nest at certain times of year. 
                            Only about 40 minutes from Liberia’s international 
                            airport, its convenient location and isolated sands 
                            make this a prime vacation destination.",
                        'points1' => array(
                            'Unlimited meals and snack',
                            'Unlimited alcoholic beverages',
                            'Tranfers to and from the Liberia airport',
                            '3 full open bars, plus a sports bar and a pool bar',
                            '4 restaurants and one snack bar'
                        ),
                        'points2' => array(
                            "5 conference rooms",
                            "Immense swimming pool with integrated jacuzzi",
                            "Kids pool",
                            'Kids club',
                            'Beach club',
                        )
                    ),
                    array(
                        'title' => 'Dining',
                        'description' => 'Hotel Riu offers 3 speciality restaurants 
                            and a buffet-style main restaurant. Asian, Italian 
                            and the Steak House both offer to-order and buffet 
                            options. Check out the lobby bar or the pool bar for 
                            a cocktail in the fresh air.',
                        'points1' => array(
                            'Liberia Resaurant typical and international Food',
                            'La Toscana Italian sit-down restaurant',
                        ),
                        'points2' => array(
                            "Furama Specializing in sushi",
                            "The Steak House serves up succulent cuts of meat"
                        )
                    ),
                    array(
                        'title' => 'Activities',
                        'points1' => array(
                            'Gym',
                            'Lighted Tennis Courts',
                            'Volleyball Court',
                            'Billiards table',
                            'Steam room & Jacuzzi',
                            'Snorkeling and kayaking equipment',
                            '1 Free Scuba Diving Lesson in the Pool',
                        ),
                        'points2' => array(
                            'Daily entertainment for children 4-12 years old',
                            'Daily entertainment for adults',
                            'Nighttime entertainment, live music and dance ensembles, comedy routine',
                            'Entrance to the casino and dance club (does not include money spent at casino)',
                            'Aquatic sports',
                        )
                    ),
                    array(
                        'title' => 'Amenities',
                        'points1' => array(
                            'Bathroom',
                            'Hair Dryer',
                            '110 V Outlets',
                            'Telephone',
                            'Central Air Conditioning',
                            'Ceiling Fan',
                            'Minibar'
                        ),
                        'points2' => array(
                            'Lico Dispensor',
                            'Satellite TV (flat screen)',
                            'Radio alarm clock',
                            'Iron & Ironing Board',
                            '2 Beds 125 x 200 cm or 2 bed 200 x 200 cm',
                            'Terrace or Balcony'
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
                ),
                'reviews' => array(
                    array(
                        'user' => 'Henry',
                        'location' => 'El Paso, Texas',
                        'title' => 'love RIU hotels',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => " love RIU hotels, I’ve stayed in them all 
                            over the world and RIU Costa Rico is one of my all 
                            time favorites. Great food, beautiful hotel, great 
                            weather. All around great experience. A+++",
                    ),
                    array(
                        'user' => 'Sharon',
                        'location' => 'Orlando, Florida',
                        'title' => 'They loved the kiddie activities',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "My daughter has food allergies and the chef 
                            at the Liberia restaurant specially-cooked her food 
                            each lunch and dinner. They loved the kiddie activities 
                            during the day and the kid’s shows in teh evening. 
                            Starlight was everyone’s favorite we watched him 
                            every night. AMAZING STAFF!!!",
                    ),
                    array(
                        'user' => 'CaMeRon',
                        'location' => 'Atlanta, Georgia',
                        'title' => 'Strong drinks, best spring break ever',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "Strong drinks, best spring break ever. If your 
                            a student this is a great place to come with all of 
                            your friends. I didn’t really like the food at the 
                            asian place but the rest of it was seriuosly awesome. 
                            we can’t wait to come again",
                    ),
                    array(
                        'user' => 'Rayda M',
                        'location' => 'New York, New York',
                        'title' => 'It was fun. Not the best Riu',
                        'rating' => 3,
                        'date' => 'June 17, 2012',
                        'text' => "It was fun. Not the best Riu I’ve ever been 
                            too, but I enjoyed my stay and the staff was friendly. 
                            Really loved the scuba diving and got to see a sea 
                            turtle and some dolphins. Not sure I’d go back to 
                            that Riu again, but I’m glad we went once. ",
                    ),
                    array(
                        'user' => 'Jonathon',
                        'location' => 'Tom’s River, New Jersey',
                        'title' => 'the food was amazing',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "the food was amazing and i won like $100 this 
                            one night in the casino lots of hot chicks at the 
                            bar on the weekend but kinda slow during the week. 
                            also a bit $$ to taxi into town at night, but most 
                            nights we just stayed at the resort",
                    ),
                    array(
                        'user' => 'Niall',
                        'location' => 'Rochester, New York',
                        'title' => 'Sushi restaurant is the best',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "Sushi restaurant is the best, then Italian 
                            then Liberia then the Steak House. This was our 
                            family’s first all inclusive, and it was so nice not 
                            having to worry about meals and money. Everyone could 
                            always find something to do, even my 14 year old. 
                            My wife and I felt comfortable leaving the kids to 
                            do their own thing so we could do ours. Overall, a 
                            wonderful experience.",
                    ),
                    array(
                        'user' => 'Ruthie',
                        'location' => 'Los Angeles, CA',
                        'title' => 'LOVE LOVE LOVE!',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "LOVE LOVE LOVE! Already planning a trip back 
                            next year. I think next time I would NOT go during 
                            Easter Week because it was really crowded. ",
                    ),
                    array(
                        'user' => 'Kimberly H.',
                        'location' => 'Los Angeles, CA',
                        'title' => 'It was super romantic.',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "My husband and I celebrated our honeymoon 
                            here and it was phenomenal. The staff went out of 
                            their way to make us feels special. There was 
                            champagne in the room and rosepetals on the dinner 
                            table at night. It was super romantic. ",
                    ),
                    array(
                        'user' => 'Joan',
                        'location' => 'Alpharetta, GA',
                        'title' => 'The beach was pretty nice',
                        'rating' => 2,
                        'date' => 'June 17, 2012',
                        'text' => "I did not like the food here at all. It was very greasy. 
                            There were a few things at the Asian place that were ok. 
                            The beach was pretty nice. Also, it took forever to check in. ",
                    ),
                    array(
                        'user' => 'Patricia',
                        'location' => '',
                        'title' => 'Super friendly.',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "The wait staff was so nice, and helped me 
                            practice my Spanish. Super friendly. One of the 
                            waiters told us that there were turtles nesting at 
                            this time of year and we went and sure enough! saw 
                            a momma turtle dig a big hole in the sand and lay 
                            her eggs!!!!! It was one of the most amazing things 
                            I’ve ever seen. he told us we couldn’t use flashlights 
                            or cameras with a flash because it disturbs the 
                            turtles but there was enough light from the stars 
                            and moon to see.",
                    ),
                ),
                'features' => array(
                    'Unlimited meals and snack',
                    'Unlimited alcoholic beverages',
                    'Tranfers to and from the Liberia airport',
                    '3 full open bars, plus a sports bar and a pool bar',
                    '4 restaurants and one snack bar',
                    '5 conference rooms',
                ),
                'images' => array(
                    array(
                        'url' => 'https://static.tripfab.com/trips/riu/main.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/riu/main_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/riu/main_big.jpg',
                        'desc' => 'Let your cares melt away at this opulent all-inclusive resort in Guanacaste'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/riu/1.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/riu/1_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/riu/1_big.jpg',
                        'desc' => 'The pool bar is the perfect place to relax and throw back a couple of cocktails'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/riu/2.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/riu/2_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/riu/2_big.jpg',
                        'desc' => 'Hotel Riu is one of the largest all-inclusives in the country'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/riu/3.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/riu/3_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/riu/3_big.jpg',
                        'desc' => 'The dark-sand beach attracts nesting sea turtles at various times of the year'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/riu/4.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/riu/4_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/riu/4_big.jpg',
                        'desc' => 'Buffet style and sit-down restaurants offer top notch dining'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/riu/5.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/riu/5_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/riu/5_big.jpg',
                        'desc' => 'Chefs are highly trained, and more than willing to accommodate food allergies and preferences'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/riu/6.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/riu/6_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/riu/6_big.jpg',
                        'desc' => 'Purely luxurious high thread-count sheets'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/riu/7.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/riu/7_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/riu/7_big.jpg',
                        'desc' => "It's tempting to sleep all day in the Riu's super-comfy beds"
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/riu/8.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/riu/8_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/riu/8_big.jpg',
                        'desc' => 'Have a romantic soak in the hot tub with someone special'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/riu/9.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/riu/9_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/riu/9_big.jpg',
                        'desc' => 'The enormous swimming pool just meters away from the beach is the perfect place to pass the day'
                    ),
                ),
                'dining' => 'Hotel Riu offers 3 speciality restaurants and a 
                    buffet-style main restaurant. Asian, Italian and the Steak 
                    House both offer to-order and buffet options. Check out the 
                    lobby bar or the pool bar for a cocktail in the fresh air. ',
                'restaurants' => array(
                    array(
                        'title' => 'Liberia Resaurant',
                        'description' => 'This buffet offers everything you could 
                            ever dream of – from typical Costa Rican fares to good 
                            ‘ole Texan-style cheeseburgers. There are options for 
                            vegans, vegetarians, meat and seafood lovers and, best 
                            of all, a huge row delectable desserts to top it all off. ',
                    ),
                    array(
                        'title' => 'La Toscana',
                        'description' => 'This authentic Italian sit-down 
                            restaurant requires a reservation. Dishes are made 
                            to order, but there is a buffet-style salad bar.',
                    ),
                    array(
                        'title' => 'Furama',
                        'description' => 'Specializing in sushi, meals here are 
                            mostly served buffet style; although the sushi chefs 
                            are very friendly and open to making rolls to-order 
                            if you want something special. ',
                    ),
                    array(
                        'title' => 'Grill/Steak House',
                        'description' => 'The Steak House serves up succulent 
                            cuts of meat, ordered off of a menu. By reservation only. ',
                    ),
                ),
                'expires' => '2012-06-29 23:59:59'
            ),
            'doubletree' => array(
                'rating' => 4,
                'nights' => 2,
                'location' => 'Puntarenas, Costa Rica',
                'price' => 90,
                'price_entero' => 90,
                'price_decimal' => 00,
                'price_before' => 133,
                'price_before_entero' => 133,
                'price_before_decimal' => 00,
                'save' => 43,
                'title' => 'DoubleTree Puntarenas',
                'description' => 'From the fresh-out-of-the oven “welcome cookie”
                    to the afternoons kayaking on the Pacific, this is a guaranteed 
                    trip of a lifetime. Endless buffets, fresh smoothies and 
                    cocktails, and hours lounging poolside define the lifestyle of 
                 every guest.',
                'sale_starts' => 'July 1st',
                'sale_ends' => 'December 31st',
                'overview' => array(
                    array(
                        'title' => 'General',
                        'description' => "The Doubletree experience is something 
                        for the record books – especially at the Puntarenas resort
                         in Costa Rica. What this paradise lacks in geographical 
                         size, it makes up for in beauty and warmth.",
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
                        'description' => 'The Doubletree Resort by Hilton in 
                        Puntarenas offers 13 different restaurants and bars 
                        serving up an array international cuisine.',
                        'points1' => array(
                            'El Pelicano Bar and Snack Bar quick snacks and fast food.',
                            'Calypso The main restaurant, Calypso offers local and 
                            international dishes buffet-style. Each night has a 
                            different theme.',
                            'El Tucan Fruit Bar This eatery offers 
                            an assortment of fresh fruits, smoothies and shakes.',
                        ),
                        'points2' => array(
                            "Caña Brava Specializing in seafood, this outdoor
                             restaurant requires reservations, and limitations may apply.",
                            "Macondo Macondo has perfected Latin American cuisine. 
                            The restaurant requires reservations, and limitations may apply. "
                        )
                    ),
                    array(
                        'title' => 'Activities',
                        'points1' => array(
                            'Arts and crafts',
                            'Kayaking',
                            'Boogie boards',
                            'Beach soccer',
                            'Beach volleyball',
                            'Surfing',
                            'Aquaerobics',
                            'Water polo'
                        ),
                        'points2' => array(
                            'Precor Fitness center',
                            'Tennis',
                            'Ping pong',
                        )
                    ),
                    array(
                        'title' => 'Amenities',
                        'points1' => array(
                            'Sweet Dreams Beds by DoubleTree Sleep Experience ',
                            'Either a king-sized bed or two double beds',
                            'Neutrogena bath products',
                            '37” LCD TV with cable',
                            'Coffee maker',
                            'Easy access to the pool complex and all of the hotels facilities'
                        ),
                        'points2' => array(
                            '37” LCD TV with cable',
                            'Coffee maker',
                            'Easy access to the pool complex and all of the hotels facilities'
                        )
                    ),
                ),
                'addons' => array(),
                'reviews' => array(
                    array(
                        'user' => 'Cara',
                        'location' => 'Roswell, GA ',
                        'title' => 'Great staff...',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "Great staff, they were WONDERFUL with the kids. 
                        My husband and I are usually a bit paranoid with about who 
                        we leave them with (they are only 3 and 5), but we felt 
                        really comfortable here with the daytime activities. They
                         had so much fun. Licors they put in the drinks were kinda 
                         nasty like in most all-inclusives but other than that it 
                         was an ideal vacation.",
                    ),
                    array(
                        'user' => 'Matt',
                        'location' => 'Bloomington, Indiana',
                        'title' => 'Costa Rica is GOREOUS!!',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "I saw so many animals it wasn’t even FUNNY.
                         There were black “Congo” monkeys almost every single 
                         morning and they got SO CLOSE we took tons of pictures.
                         There are a lot of steep hills and my parents usually
                         took the shuttle that is always running, but I liked
                         to walk for extra exercise. Especially after eating 
                         so much at the buffet lol.",
                    ),
                    array(
                        'user' => 'Brandon',
                        'location' => 'Tampa, Florida',
                        'title' => 'Nice',
                        'rating' => 3,
                        'date' => 'June 17, 2012',
                        'text' => "I was there for a week and thought it was a
                         very nice hotel - quite a bargain at these rates. Would 
                         visit again. Beach was nice. I went during the rainy season, 
                         so there were a lot of rain showers in the afternoons.",
                    ),
                    array(
                        'user' => 'Sinead',
                        'location' => 'Cork, Ireland',
                        'title' => 'So romantic',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "I came for two weeks and had an amazing time. 
                        Walking down the promenade at sunset is a MUST. 
                        Totally romantic",
                    ),
                    array(
                        'user' => 'Jorge',
                        'location' => 'Minnesota',
                        'title' => 'Not the best',
                        'rating' => 2,
                        'date' => 'June 17, 2012',
                        'text' => "I thought that the staff was nice, but the beach was not as nice as I’d hoped.",
                    ),
                    array(
                        'user' => 'Signe',
                        'location' => 'Orange County, California',
                        'title' => 'WOWWW',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "WOW!!! I friggin’ love this place. That’s all I gotta say about that!!",
                    ),
                    array(
                        'user' => 'Elsa',
                        'location' => '--',
                        'title' => 'All Pura Vida Hotel',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "The food was absolutely out of this world. I found that there were lots of things to do in the area. We found a really great combo tour that took us ziplining and whitewtaer rafting and we had a blast!! Highly recommend.",
                    ),
                ),
                /**
                 *  Editar
                 */
                'features' => array(
                    'Full open bar',
                    '4 restaurants',
                ),
                'images' => array(
                    array(
                        'url' => 'https://static.tripfab.com/trips/doubletree/main.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/doubletree/main_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/doubletree/main_big.jpg',
                        'desc' => 'Sparkling pools, tropical weather and an unbeatable atmosphere are par for the course'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/doubletree/1.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/doubletree/1_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/doubletree/1_big.jpg',
                        'desc' => "Eat and drink to your heart's content!"
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/doubletree/2.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/doubletree/2_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/doubletree/2_big.jpg',
                        'desc' => 'It will be a tough choice between the beach and the sparkling swimming pool'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/doubletree/3.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/doubletree/3_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/doubletree/3_big.jpg',
                        'desc' => 'Melt into the plush bed'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/doubletree/4.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/doubletree/4_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/doubletree/4_big.jpg',
                        'desc' => 'Just steps away from the beach'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/doubletree/5.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/doubletree/5_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/doubletree/5_big.jpg',
                        'desc' => 'The nightly entertainment is legendary'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/doubletree/6.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/doubletree/6_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/doubletree/6_big.jpg',
                        'desc' => "There's something for everyone at the enormous swimming pool"
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/doubletree/7.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/doubletree/7_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/doubletree/7_big.jpg',
                        'desc' => 'Dine on an array of international foods at the open-air restaurant'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/doubletree/8.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/doubletree/8_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/doubletree/8_big.jpg',
                        'desc' => "Practice your game or grab a lesson at the property's private tennis courts"
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/doubletree/9.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/doubletree/9_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/doubletree/9_big.jpg',
                        'desc' => 'Easy beach access makes strolling along the beach all but too convenient'
                    ),
                ),
                'overview2' => array(
                    array(
                        'title' => 'Activities',
                        'points1' => array(
                            'Arts and crafts',
                            'Kayaking',
                            'Boogie boards',
                            'Beach soccer',
                            'Beach volleyball',
                            'Surfing',
                        ),
                        'points2' => array(
                            'Aquaerobics',
                            'Water polo',
                            'Beach Soccer',
                            'Precor Fitness center',
                            'Tennis',
                            'Ping pong',
                        ),
                    ),
                    array(
                        'title' => 'AMENITIES',
                        'points1' => array(
                            'Sweet Dreams Beds by DoubleTree Sleep Experience ',
                            'Either a king-sized bed or two double beds',
                            'Neutrogena bath products',
                            '37” LCD TV with cable',
                        ),
                        'points2' => array(
                            'High speed internet access',
                            'Coffee maker',
                            '*Easy access to the pool complex and all of the hotels facilities',
                        ),
                    ),
                ),
                'expires' => '2012-06-29 23:59:59'
            ),
            'langosta' => array(
                'rating' => 4,
                'nights' => 2,
                'location' => 'Tamarindo, Costa Rica',
                'price' => 120,
                'price_entero' => 120,
                'price_decimal' => 00,
                'price_before' => 180,
                'price_before_entero' => 180,
                'price_before_decimal' => 00,
                'save' => 60,
                'title' => 'Barcelo Langosta',
                'description' => 'Be treated like royalty for a day – or as many days as you 
                like. Indulge in some local fare with complimentary breakfast, lunch, dinner, 
                and snack bar. When it comes time to play, there are no limits.',
                'sale_starts' => 'July 1st',
                'sale_ends' => 'December 31st',
                'overview' => array(
                    array(
                        'title' => 'General',
                        'description' => "This is the life. Some trips keep travelers rushing 
                        from one activity to another, but at Barcelo Langosta, everything is 
                        on guests’ terms. This is the life. Some trips keep travelers rushing 
                        from one activity to another, but at Barcelo Langosta, everything is 
                        on guests’ terms.",
                        'points1' => array(
                            'Breakfast, lunch and dinner in the buffet restaurant',
                            'Snack bar and fast food ',
                            'Snack bar in the pool area',
                            'Lobby bar',
                            'Fitness, aerobics, dance classes',
                            'Jacuzzi',
                            'Daily entertainment program for children and adults',
                            'Night time shows'
                        ),
                        'points2' => array(
                            "Tennis courts",
                            "Access to amphitheater",
                            "Table tennis",
                            'Concierge',
                            'Free use of sun loungers in the pool area',
                            'Pool and beach towels',
                            'Safety deposit box',
                            'Souvenir shop'
                        )
                    ),
                    array(
                        'title' => 'Dining',
                        'description' => 'With such succulent dining options 
                        set amid one of the Gold Coast’s most stunning beaches, 
                        there will be no reason you’ll want to leave the hotel 
                        to eat – or perhaps ever.',
                        'points1' => array(
                            'A la carte restaurant',
                            'Buffet restaurant',
                        ),
                        'points2' => array(
                            "Snack bar in the area near the beach"
                        )
                    ),
                    array(
                        'title' => 'Activities',
                        'points1' => array(
                            'Spa',
                            'Giant Chess Board',
                            'Fitness, aerobics, dance classes',
                            'Sand courts for volleyball and soccer',
                            'Dance floor',
                            'Tennis courts',
                            'Children’s playground',
                            'Mini-club for kids',
                        ),
                        'points2' => array(
                            'Table tennis',
                            'Amphitheater',
                            'Archery',
                            'Darts'
                        )
                    ),
                    array(
                        'title' => 'Amenities',
                        'points1' => array(
                            'Terrace or balcony',
                            'Wide bed',
                            'Ceiling fan',
                            'Iron/Ironing board',
                            'Minibar',
                            'Air conditioning',
                            'Coffee Machine',
                            'Safe',
                        ),
                        'points2' => array(
                            'TV',
                            'Telephone',
                            'Fully equipped bathroom',
                            'Magnifying mirror',
                            'Hairdryer'
                        )
                    ),
                ),
                'addons' => array(),
                'reviews' => array(
                    array(
                        'user' => 'Kari',
                        'location' => 'Orlando Florida',
                        'title' => 'OMG',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "I lOVE THIS BEACH!! I want to retire there.",
                    ),
                    array(
                        'user' => 'Adam',
                        'location' => 'Riverton, New Jersey',
                        'title' => 'Some of the best waves ever.',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "If you’re a surfer, go. If you are just learning, 
                        best to head next door to the smaller waves at Tamarindo.",
                    ),
                    array(
                        'user' => 'Ruthie',
                        'location' => '-',
                        'title' => 'LOVE LOVE LOVE!',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "Already planning a trip back next year. 
                        I think next time I would NOT go during Easter Week 
                        because it was really crowded.",
                    ),
                    array(
                        'user' => 'Sydney H.',
                        'location' => '-',
                        'title' => 'Our Honeymoon',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "My husband and I celebrated our honeymoon 
                        here and it was phenomenal. The staff went out of their
                        way to make us feels special. There was champagne 
                        in the room and rosepetals on the dinner table at 
                        night. It was super romantic.",
                    ),
                    array(
                        'user' => 'Sufri M',
                        'location' => 'Washington, DC',
                        'title' => 'It was fun',
                        'rating' => 3,
                        'date' => 'June 17, 2012',
                        'text' => "Not the best all-inclusive I’ve ever been too, 
                        but I enjoyed my stay and the staff was friendly. Really 
                        loved the scuba diving and got to see a sea turtle and some 
                        dolphins. Not sure I’d go back to that again, but I’m glad 
                        we went once.",
                    ),
                    array(
                        'user' => 'Roy',
                        'location' => 'Alberta, Canada',
                        'title' => 'All Pura Vida Hotel',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "Costa Rica is GOREOUS!! I saw so many animals it 
                        wasn’t even FUNNY. There were black “Congo” monkeys almost 
                        every single morning and they got SO CLOSE we took tons of 
                        pictures. There are a lot of steep hills and my parents usually 
                        took the shuttle that is always running, but I liked to walk
                        for extra exercise. Especially after eating so much at the buffet LOL.",
                    ),
                    array(
                        'user' => 'Stephen',
                        'location' => 'For Lauderdale, Florida',
                        'title' => 'I love it!!!',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "I love this place. Perfect get away!!",
                    ),
                    array(
                        'user' => 'Sean',
                        'location' => 'London, England',
                        'title' => 'Just amazing',
                        'rating' => 5,
                        'date' => 'June 17, 2012',
                        'text' => "I came for two weeks and had an amazing time.
                         Sunset sailing is a MUST. I caught a huge marlin sport 
                         fishing and the guys on the boat had lunch and beers 
                         and snacks for us on hand all day. ",
                    ),
                    array(
                        'user' => 'Sandy',
                        'location' => 'Alpharetta, GA',
                        'title' => 'Great Staff',
                        'rating' => 4,
                        'date' => 'June 17, 2012',
                        'text' => "Great staff, they were WONDERFUL with the kids. My husband and I are usually a bit paranoid with about who we leave them with (they are only 3 and 5), but we felt really comfortable here with the daytime activities. They had so much fun.",
                    ),
                    array(
                        'user' => 'Olger',
                        'location' => 'Los Angeles, CA',
                        'title' => 'Not what I expected...',
                        'rating' => 2,
                        'date' => 'June 17, 2012',
                        'text' => "It was OK. Food was mediocre. The beach was nice. I also liked the pool and the nighttime stuff.",
                    ),
                    array(
                        'user' => 'Casey',
                        'location' => 'Portland, Oregon',
                        'title' => 'Rainy Season',
                        'rating' => 3,
                        'date' => 'June 17, 2012',
                        'text' => "I was there for a week and thought it was a very nice hotel. Would visit again. Beach was nice. Rainy season, so often showers in the afternoon.",
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
                        'url' => 'https://static.tripfab.com/trips/langosta/main.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/langosta/main_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/langosta/main_big.jpg',
                        'desc' => 'Enjoy royal treatment at the beautiful Barcelo Langosta'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/langosta/1.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/langosta/1_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/langosta/1_big.jpg',
                        'desc' => 'Guests appreciate the meticulous landscaping while strolling the grounds'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/langosta/2.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/langosta/2_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/langosta/2_big.jpg',
                        'desc' => 'The pool is particularly beautiful in the evenings, when it is all lit up'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/langosta/3.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/langosta/3_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/langosta/3_big.jpg',
                        'desc' => 'Various national flags don the entrance to this international hotel'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/langosta/4.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/langosta/4_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/langosta/4_big.jpg',
                        'desc' => 'Poolside or beach? Life is full of tough decisions'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/langosta/5.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/langosta/5_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/langosta/5_big.jpg',
                        'desc' => "Enjoy fresh sea breezes and stunning views from your room's private balcony"
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/langosta/6.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/langosta/6_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/langosta/6_big.jpg',
                        'desc' => 'There are plenty of places to explore here, including the estuary'
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/langosta/7.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/langosta/7_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/langosta/7_big.jpg',
                        'desc' => "No matter what time of day, there's always something to see just outside your window"
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/langosta/8.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/langosta/8_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/langosta/8_big.jpg',
                        'desc' => "Howler monkeys and tropical birds are known to frequent the trees near the pool"
                    ),
                    array(
                        'url' => 'https://static.tripfab.com/trips/langosta/9.jpg',
                        'thumb' => 'https://static.tripfab.com/trips/langosta/9_thumb.jpg',
                        'big' => 'https://static.tripfab.com/trips/langosta/9_big.jpg',
                        'desc' => 'Original artwork and unique accents adorn each of the rooms'
                    ),
                ),
                'dining' => 'With such succulent dining options set amid one of the Gold Coast’s most stunning beaches, there will be no reason you’ll want to leave the hotel to eat – or perhaps ever.',
                'restaurants' => array(
                    array(
                        'title' => 'A la carte restaurant',
                        'description' => 'Food here is positively delicious, and prepared made-to-order.',
                    ),
                    array(
                        'title' => 'Buffet restaurant ',
                        'description' => 'The hotel’s main restaurant, the buffet offers the widest variety of options.',
                    ),
                    array(
                        'title' => 'Los Corales',
                        'description' => 'This snack bar offers up beach bites that you can eat on the sand and in your swimsuit.',
                    ),
                ),
                'overview2' => array(
                    array(
                        'title' => 'Activities',
                        'points1' => array(
                            'Spa',
                            'Giant Chess Board',
                            'Fitness, aerobics, dance classes',
                            'Sand courts for volleyball and soccer',
                            'Dance floor',
                            'Tennis courts'
                        ),
                        'points2' => array(
                            'Children’s playground',
                            'Mini-club for kids',
                            'Table tennis',
                            'Amphitheater',
                            'Archery',
                            'Darts',
                        ),
                    ),
                    array(
                        'title' => 'Amenities',
                        'points1' => array(
                            'Terrace or balcony',
                            'Wide bed',
                            'Ceiling fan',
                            'Iron/Ironing board',
                            'Minibar',
                            'Air conditioning',
                            'Coffee Machine'
                        ),
                        'points2' => array(
                            'Safe',
                            'TV',
                            'Telephone',
                            'Fully equipped bathroom',
                            'Magnifying mirror',
                            'Hairdryer'
                        ),
                    ),
                ),
                'expires' => '2012-06-29 23:59:59'
            ),
        );

        $rooms = array(
            'papagayo' => array(
                array(
                    'id' => 'sup',
                    'name' => 'Superior',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'description' => 'All the luxury and ammenities of a 4 star 
                        hotel. Minibar, A/c, Satelite TV.',
                    'min'         => 2,
                    'max'         => 4,
                    'selected' => 1
                ),
                array(
                    'id' => 'supov',
                    'name' => 'Superior OV',
                    'price_desc' => '+ 12',
                    'price' => 12,
                    'description' => 'Same as the Superior but also including an
                        astonishing ocean view',
                    'min'         => 2,
                    'max'         => 4,
                ),
                array(
                    'id' => 'supovprem',
                    'name' => 'Superior OV Premium',
                    'price_desc' => '+ $18',
                    'price' => 18,
                    'description' => 'Choice of a king bed and two queen beds. 
                        Valcony with spectacular view of the Papagayo Bay. 
                        Satelite TV , safe , refrigerator, A/C and coffeemaker. 
                        Located close to the pool.',
                    'min'         => 2,
                    'max'         => 4,
                ),
            ),
            'riu' => array(
                array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'description' => 'With a choice of two double beds or one 
                        king bed, these rooms can sleep up to four guests. A 
                        private bathroom, central air and mini-bar are available 
                        in all double rooms. Interconnecting rooms are available.',
                    'min'         => 2,
                    'max'         => 3,
                    'selected' => 1
                ),
                array(
                    'id' => 'ocean',
                    'name' => 'Standard ocean view',
                    'price_desc' => '+ $50',
                    'price' => 50,
                    'description' => 'Also available with two double beds or one 
                        king bed, a private bathroom, central air and mini-bar . 
                        With a breathtaking ocean view',
                    'min'         => 2,
                    'max'         => 3,
                ),
                array(
                    'id' => 'jacuzzi',
                    'name' => 'Jacuzzi Suite',
                    'price_desc' => '+ $145',
                    'price' => 145,
                    'description' => 'These suites combine all of the features 
                        of the suite above with a private terrace and a jacuzzi. 
                        Enjoy a romantic evening lounging in the hot tub with 
                        your honey as you watch the sun sink below the horizon.',
                    'min'         => 2,
                    'max'         => 4,
                ),
            ),
            'langosta' => array(
                array(
                    'id' => 'superior',
                    'name' => 'Superior',
                    'price_desc' => 'Imcluded',
                    'price' => 0,
                    'description' => 'These comfortable rooms measure 345 square 
                        feet and feature views of Las Baulas National Marine Park. 
                        Amenities include a king-sized bed, terrace or balcony,
                        full bathroom, air-conditioning, TV, phone, minibar, 
                        and in-room safe.',
                    'min'         => 2,
                    'max'         => 4,
                    'selected' => 1
                ),
                array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe',
                    'price_desc' => '+ $35',
                    'price' => 35,
                    'description' => "The suite's 710 square feet are spread over 
                        two levels and designed to accommodate families. You'll 
                        enjoy gorgeous ocean views, and also a king bed, sofa bed, 
                        balcony, living area, air-conditioning, full bathroom, 
                        jacuzzi, minibar, cable TV, phone, and in-room safe.",
                    'min'         => 2,
                    'max'         => 4,
                ),
                array(
                    'id' => 'suite',
                    'name' => 'Suite',
                    'price_desc' => '+ $42',
                    'price' => 42,
                    'description' => "Enjoy the ultimate in luxury: a 1,065-square 
                        foot master suite with two levels and breathtaking views. 
                        You'll have at your disposal a king bed, sofa bed, living 
                        area, terrace or balcony, jacuzzi, full bath, cable TV, 
                        phone, air-conditioning, minibar, and in-room safe.",
                    'min'         => 2,
                    'max'         => 4,
                ),
            ),
            'doubletree' => array(
                array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'description' => ' Located in building one near the main pool, 
                        these rooms feature a king size Sweet Dreams plush top 
                        king bed, perfect for a couple or solo traveler.',
                    'min'         => 2,
                    'max'         => 4,
                    'selected' => 1
                ),
                array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe o Junior Suite',
                    'price_desc' => '+ $19',
                    'price' => 19,
                    'description' => 'Located in building two close to the main
                        restaurant and theater, these spacious suites feature a 
                        king Sweet Dreams bed in an oversized bedroom and a 
                        large jacuzzi tub.',
                    'min'         => 2,
                    'max'         => 4,
                ),
            )
        );

        $activities = array(
            'langosta' => array(
                array(
                    'id' => 'atv',
                    'name' => 'ATV Tours',
                    'price_desc' => '+$110/person',
                    'price' => 110
                ),
                array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                array(
                    'id' => 'canopy',
                    'name' => 'Canopy tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
                ),
            ),
            'riu' => array(
                array(
                    'id' => 'atv',
                    'name' => '2 hr ATV',
                    'price_desc' => '+$70/person',
                    'price' => 70
                ),
                array(
                    'id' => 'canopy',
                    'name' => 'Canopy tour',
                    'price_desc' => '+$50/person',
                    'price' => 50
                ),
                array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                array(
                    'id' => 'massage',
                    'name' => '1hr Massage',
                    'price_desc' => '+$80/person',
                    'price' => 80
                ),
            ),
            'papagayo' => array(
                array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                array(
                    'id' => 'shopping',
                    'name' => 'Liberia Shopping Tour',
                    'price_desc' => '+$80/person',
                    'price' => 80
                ),
                array(
                    'id' => 'atv',
                    'name' => 'ATV Tours',
                    'price_desc' => '+$110/person',
                    'price' => 110
                ),
                array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
                ),
            ),
            'doubletree' => array(
                array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
               ),
                array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
               ),
                array(
                    'id' => 'cruises',
                    'name' => 'Tortuga Island Cruises Day Tour',
                    'price_desc' => '+$120/person',
                    'price' => 120
               ),
                array(
                    'id' => 'kajak',
                    'name' => 'Kajak Tour at Punta Coral',
                    'price_desc' => '+$190/person',
                    'price' => 190
               ),
            )
        );

        $this->view->id = $id;

        $this->view->info = $info[$id];
        $this->view->rooms = $rooms[$id];
        $this->view->activities = $activities[$id];

        $this->render('trip' . $version);
    }

    public function confirmAction() {
        if (!$this->getRequest()->isPost())
            throw new Exception('Page not found');

        $version = $this->_getParam('version');
        if ($version != 1)
            throw new Exception('Page not found');

        $id = $this->_getParam('idf');
        $data = $_POST;

        $info = array(
            'papagayo' => array(
                'title' => 'Alegro Papagayo (All Inclusive)',
                '_price' => 109,
                '_price_before' => 248,
                'room' => 'Superior',
            ),
            'riu' => array(
                'title' => 'RIU Guanacaste  (All Inclusive)',
                '_price' => 166,
                '_price_before' => 210,
                'room' => 'Standard',
            ),
            'langosta' => array(
                'title' => 'Barcelo Langosta (All Inclusive)',
                '_price' => 120,
                '_price_before' => 180,
                'room' => 'Superior',
            ),
            'doubletree' => array(
                'title' => 'DoubleTree Puntarenas (All Inclusive)',
                '_price' => 90,
                '_price_before' => 133,
                'room' => 'Standard',
            )
        );

        $rooms = array(
            'papagayo' => array(
                array(
                    'id' => 'sup',
                    'name' => 'Superior',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => 1
                ),
                array(
                    'id' => 'supov',
                    'name' => 'Superior OV',
                    'price_desc' => 'Add $12 per person per night',
                    'price' => 12,
                ),
                array(
                    'id' => 'supovprem',
                    'name' => 'Superior OV Premium',
                    'price_desc' => 'Add $18 per person per night',
                    'price' => 18
                ),
            ),
            'riu' => array(
                array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => 1
                ),
                array(
                    'id' => 'ocean',
                    'name' => 'Standard ocean view',
                    'price_desc' => 'Add $50 per person per night',
                    'price' => 50
                ),
                array(
                    'id' => 'jacuzzi',
                    'name' => 'Jacuzzi Suite',
                    'price_desc' => 'Add $145 per person per night',
                    'price' => 145
                ),
            ),
            'langosta' => array(
                array(
                    'id' => 'superior',
                    'name' => 'Superior',
                    'price_desc' => 'Imcluded',
                    'price' => 0,
                    'selected' => 1
                ),
                array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe',
                    'price_desc' => 'Add $35 per person per night',
                    'price' => 35
                ),
                array(
                    'id' => 'suite',
                    'name' => 'Suite',
                    'price_desc' => 'Add $42 per person per night',
                    'price' => 42
                )
            ),
            'doubletree' => array(
                array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => 1
                ),
                array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe o Junior Suite',
                    'price_desc' => 'Add $19 per person per night',
                    'price' => 19,
                )
            )
        );

        $activities = array(
            'langosta' => array(
                array(
                    'id' => 'atv',
                    'name' => 'ATV Tours',
                    'price_desc' => '+$110/person',
                    'price' => 110
                ),
                array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                array(
                    'id' => 'canopy',
                    'name' => 'Canopy tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
                )
            ),
            'riu' => array(
                array(
                    'id' => 'atv',
                    'name' => '2 hr ATV',
                    'price_desc' => ' $70 per person',
                    'price' => 70
                ),
                array(
                    'id' => 'canopy',
                    'name' => 'Canopy tour',
                    'price_desc' => ' $50 per person',
                    'price' => 50
                ),
                array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => ' $60 per person',
                    'price' => 60
                ),
                array(
                    'id' => 'massage',
                    'name' => '1hr Massage',
                    'price_desc' => ' $80 per person',
                    'price' => 80
                ),
            ),
            'papagayo' => array(
                array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                array(
                    'id' => 'shopping',
                    'name' => 'Liberia Shopping Tour',
                    'price_desc' => '+$80/person',
                    'price' => 80
                ),
                array(
                    'id' => 'atv',
                    'name' => 'ATV Tours',
                    'price_desc' => '+$110/person',
                    'price' => 110
                ),
                array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
                ),
            ),
            'doubletree' => array(
                array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
               ),
                array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
               ),
                array(
                    'id' => 'cruises',
                    'name' => 'Tortuga Island Cruises Day Tour',
                    'price_desc' => '+$120/person',
                    'price' => 120
               ),
                array(
                    'id' => 'kajak',
                    'name' => 'Kajak Tour at Punta Coral',
                    'price_desc' => '+$190/person',
                    'price' => 190
               ),
            )
        );

        $arrival = strtotime($data['date']);
        $nights = $data['nights'];
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

    public function checkoutAction() {
        if (!$this->getRequest()->isPost())
            throw new Exception('Page not found');

        $version = $this->_getParam('version');

        $data = $_POST;
        $id = $data['package'];

        //echo '<pre>'; print_r($data); echo '</pre>'; die;

        $info = array(
            md5('papagayo') => array(
                'title' => 'Alegro Papagayo (All Inclusive)',
                '_price' => 109,
                '_price_before' => 248,
                'room' => $data['room'],
                'image' => 'https://static.tripfab.com/trips/papagayo/main_thumb.jpg',
            ),
            md5('riu') => array(  
                'title' => 'RIU Guanacaste (All Inclusive)',
                '_price' => 166,
                '_price_before' => 210,
                'room' => $data['room'],
                'image' => 'https://static.tripfab.com/trips/riu/main_thumb.jpg',
            ),
            md5('langosta') => array(
                'title' => 'Barcelo Langosta (All Inclusive)',
                '_price' => 120,
                '_price_before' => 180,
                'room' => $data['room'],
                'image' => 'https://static.tripfab.com/trips/langosta/main_thumb.jpg',
            ),
            md5('doubletree') => array(
                'title' => 'DoubleTree Puntarenas (All Inclusive)',
                '_price' => 90,
                '_price_before' => 133,
                'room' => $data['room'],
                'image' => 'https://static.tripfab.com/trips/doubletree/main_thumb.jpg',
            )
        );

        $rooms = array(
            md5('papagayo') => array(
                'sup' => array(
                    'id' => 'sup',
                    'name' => 'Superior',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => ($data['room'] == 'sup')
                ),
                'supov' => array(
                    'id' => 'supov',
                    'name' => 'Superior OV',
                    'price_desc' => 'Add $12 per person per night',
                    'price' => 12,
                    'selected' => ($data['room'] == 'supov')
                ),
                'supovprem' => array(
                    'id' => 'supovprem',
                    'name' => 'Superior OV Premium',
                    'price_desc' => 'Add $18 per person per night',
                    'price' => 18,
                    'selected' => ($data['room'] == 'supovprem')
                ),
            ),
            md5('riu') => array(
                'standard' => array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => ($data['room'] == 'standard')
                ),
                'ocean' => array(
                    'id' => 'ocean',
                    'name' => 'Standard ocean view',
                    'price_desc' => 'Add $50 per person per night',
                    'price' => 50,
                    'selected' => ($data['room'] == 'ocean')
                ),
                'jacuzzi' => array(
                    'id' => 'jacuzzi',
                    'name' => 'Jacuzzi Suite',
                    'price_desc' => 'Add $145 per person per night',
                    'price' => 145,
                    'selected' => ($data['room'] == 'jacuzzi')
                ),
            ),
            md5('langosta') => array(
                'superior' => array(
                    'id' => 'superior',
                    'name' => 'Superior',
                    'price_desc' => 'Imcluded',
                    'price' => 0,
                    'selected' => ($data['room'] == 'superior')
                ),
                'deluxe' => array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe',
                    'price_desc' => 'Add $35 per person per night',
                    'price' => 35,
                    'selected' => ($data['room'] == 'deluxe')
                ),
                'suite' => array(
                    'id' => 'suite',
                    'name' => 'Suite',
                    'price_desc' => 'Add $42 per person per night',
                    'price' => 42,
                    'selected' => ($data['room'] == 'suite')
                )
            ),
            md5('doubletree') => array(
                'standard' => array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => ($data['room'] == 'standard')
                ),
                'deluxe' => array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe o Junior Suite',
                    'price_desc' => 'Add $19 per person per night',
                    'price' => 19,
                    'selected' => ($data['room'] == 'deluxe')
                )
            )
        );

        $activities = array(
            md5('langosta') => array(
                'atv' => array(
                    'id' => 'atv',
                    'name' => 'ATV Tours',
                    'price_desc' => '+$110/person',
                    'price' => 110
                ),
                'horseback' => array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                'canopy' => array(
                    'id' => 'canopy',
                    'name' => 'Canopy tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                'snorkeling' => array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
                )
            ),
            md5('papagayo') => array(
                'horseback' => array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                'shopping' => array(
                    'id' => 'shopping',
                    'name' => 'Liberia Shopping Tour',
                    'price_desc' => '+$80/person',
                    'price' => 80
                ),
                'atv' => array(
                    'id' => 'atv',
                    'name' => 'ATV Tours',
                    'price_desc' => '+$110/person',
                    'price' => 110
                ),
                'snorkeling' => array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
                ),
            ),
            md5('riu') => array(
                'atv' => array(
                    'id' => 'atv',
                    'name' => '2 hr ATV',
                    'price_desc' => ' $70 per person',
                    'price' => 70
                ),
                'canopy' => array(
                    'id' => 'canopy',
                    'name' => 'Canopy tour',
                    'price_desc' => ' $50 per person',
                    'price' => 50
                ),
                'horseback' => array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => ' $60 per person',
                    'price' => 60
                ),
                'massage' => array(
                    'id' => 'massage',
                    'name' => '1hr Massage',
                    'price_desc' => ' $80 per person',
                    'price' => 80
                ),
            ),
            md5('doubletree') => array(
                'horseback' => array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
               ),
               'snorkeling' => array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
               ),
               'cruises' => array(
                    'id' => 'cruises',
                    'name' => 'Tortuga Island Cruises Day Tour',
                    'price_desc' => '+$120/person',
                    'price' => 120
               ),
               'kajak' => array(
                    'id' => 'kajak',
                    'name' => 'Kajak Tour at Punta Coral',
                    'price_desc' => '+$190/person',
                    'price' => 190
               ),
            )
        );

        $arrival = strtotime($data['date']);
        $nights = $data['nights'];
        $travelers = (isset($data['travelers'])) ? $data['travelers'] : $data['adults'] + $data['childs'];

        $departure = $arrival + (86400 * $nights);

        $info[$id]['arrival'] = date('F jS, Y', $arrival);
        $info[$id]['departure'] = date('F jS, Y', $departure);
        $info[$id]['travelers'] = $travelers;
        $info[$id]['adults'] = (isset($data['adults'])) ? $data['adults'] : $travelers;
        $info[$id]['childs'] = (isset($data['childs'])) ? $data['childs'] : 0;
        $info[$id]['nights'] = $nights;

        $ppn = $info[$id]['_price'];
        $ppn_before = $info[$id]['_price_before'];
        $price = ($ppn + $rooms[$id][$data['room']]['price']) * $travelers * $nights;
        $price_before = ($ppn_before + $rooms[$id][$data['room']]['price']) * $travelers * $nights;

        $addons = 0;
        foreach ($data['activities'] as $a) {
            $addons += $activities[$id][$a]['price'] * $travelers;
        }

        $price += $addons;
        $price_before += $addons;

        $save = $price_before - $price;

        $info[$id]['price'] = $price;
        $info[$id]['price_before'] = $price_before;
        $info[$id]['save'] = $save;

        $this->view->info = $info[$id];
        $this->view->rooms = $rooms[$id];
        $this->view->activities = $activities[$id];
        $this->view->id = $id;
        $this->view->addons = $data['activities'];

        $places = new WS_PlacesService();
        $this->view->countries = $places->getPlaces(2);
        
        $keys = Zend_Registry::get('stripe');
        $this->view->pkey = $keys['public_key'];

        $this->render('checkout' . $version);
    }

    public function chargeAction() {
        if(!$this->getRequest()->isPost())
            throw new Exception();
        
        $version = $this->_getParam('version');
        $data = $_POST;
        $id = $data['package'];

        //echo '<pre>'; print_r($data); echo '</pre>'; die;
        
        $packages = array(
            md5('papagayo')   => 'papagayo',
            md5('riu')        => 'riu',
            md5('langosta')   => 'langosta',
            md5('doubletree') => 'doubletree',
        );
        
        $hotels = array(
            'papagayo'   => 'Alegro Papagayo (All Inclusive)',
            'riu'        => 'RIU Guanacaste (All Inclusive)',
            'langosta'   => 'Barcelo Langosta (All Inclusive)',
            'doubletree' => 'DoubleTree Puntarenas (All Inclusive)',
        );

        $info = array(
            md5('papagayo') => array(
                'title' => 'Alegro Papagayo (All Inclusive)',
                '_price' => 109,
                '_price_before' => 248,
                'room' => $data['room'],
            ),
            md5('riu') => array(
                'title' => 'RIU Guanacaste (All Inclusive)',
                '_price' => 166,
                '_price_before' => 210,
                'room' => $data['room'],
            ),
            md5('langosta') => array(
                'title' => 'Barcelo Langosta (All Inclusive)',
                '_price' => 120,
                '_price_before' => 180,
                'room' => $data['room'],
            ),
            md5('doubletree') => array(
                'title' => 'DoubleTree Puntarenas (All Inclusive)',
                '_price' => 90,
                '_price_before' => 133,
                'room' => $data['room'],
            )
        );

        $rooms = array(
            md5('papagayo') => array(
                'sup' => array(
                    'id' => 'sup',
                    'name' => 'Superior',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => ($data['room'] == 'sup')
                ),
                'supov' => array(
                    'id' => 'supov',
                    'name' => 'Superior OV',
                    'price_desc' => 'Add $12 per person per night',
                    'price' => 12,
                    'selected' => ($data['room'] == 'supov')
                ),
                'supovprem' => array(
                    'id' => 'supovprem',
                    'name' => 'Superior OV Premium',
                    'price_desc' => 'Add $18 per person per night',
                    'price' => 18,
                    'selected' => ($data['room'] == 'supovprem')
                ),
            ),
            md5('riu') => array(
                'standard' => array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => ($data['room'] == 'standard')
                ),
                'ocean' => array(
                    'id' => 'ocean',
                    'name' => 'Standard ocean view',
                    'price_desc' => 'Add $50 per person per night',
                    'price' => 50,
                    'selected' => ($data['room'] == 'ocean')
                ),
                'jacuzzi' => array(
                    'id' => 'jacuzzi',
                    'name' => 'Jacuzzi Suite',
                    'price_desc' => 'Add $145 per person per night',
                    'price' => 145,
                    'selected' => ($data['room'] == 'jacuzzi')
                ),
            ),
            md5('langosta') => array(
                'superior' => array(
                    'id' => 'superior',
                    'name' => 'Superior',
                    'price_desc' => 'Imcluded',
                    'price' => 0,
                    'selected' => ($data['room'] == 'superior')
                ),
                'deluxe' => array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe',
                    'price_desc' => 'Add $35 per person per night',
                    'price' => 35,
                    'selected' => ($data['room'] == 'deluxe')
                ),
                'suite' => array(
                    'id' => 'suite',
                    'name' => 'Suite',
                    'price_desc' => 'Add $42 per person per night',
                    'price' => 42,
                    'selected' => ($data['room'] == 'suite')
                )
            ),
            md5('doubletree') => array(
                'standard' => array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Included',
                    'price' => 0,
                    'selected' => ($data['room'] == 'standard')
                ),
                'deluxe' => array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe o Junior Suite',
                    'price_desc' => 'Add $19 per person per night',
                    'price' => 19,
                    'selected' => ($data['room'] == 'deluxe')
                )
            )
        );

        $activities = array(
            md5('langosta') => array(
                'atv' => array(
                    'id' => 'atv',
                    'name' => 'ATV Tours',
                    'price_desc' => '+$110/person',
                    'price' => 110
                ),
                'horseback' => array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                'canopy' => array(
                    'id' => 'canopy',
                    'name' => 'Canopy tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                'snorkeling' => array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
                )
            ),
            md5('papagayo') => array(
                'horseback' => array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
                ),
                'shopping' => array(
                    'id' => 'shopping',
                    'name' => 'Liberia Shopping Tour',
                    'price_desc' => '+$80/person',
                    'price' => 80
                ),
                'atv' => array(
                    'id' => 'atv',
                    'name' => 'ATV Tours',
                    'price_desc' => '+$110/person',
                    'price' => 110
                ),
                'snorkeling' => array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
                ),
            ),
            md5('riu') => array(
                'atv' => array(
                    'id' => 'atv',
                    'name' => '2 hr ATV',
                    'price_desc' => ' $70 per person',
                    'price' => 70
                ),
                'canopy' => array(
                    'id' => 'canopy',
                    'name' => 'Canopy tour',
                    'price_desc' => ' $50 per person',
                    'price' => 50
                ),
                'horseback' => array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => ' $60 per person',
                    'price' => 60
                ),
                'massage' => array(
                    'id' => 'massage',
                    'name' => '1hr Massage',
                    'price_desc' => ' $80 per person',
                    'price' => 80
                ),
            ),
            md5('doubletree') => array(
                'horseback' => array(
                    'id' => 'horseback',
                    'name' => '2hr Horseback tour',
                    'price_desc' => '+$60/person',
                    'price' => 60
               ),
               'snorkeling' => array(
                    'id' => 'snorkeling',
                    'name' => 'Snorkeling Tours',
                    'price_desc' => '+$70/person',
                    'price' => 70
               ),
               'cruises' => array(
                    'id' => 'cruises',
                    'name' => 'Tortuga Island Cruises Day Tour',
                    'price_desc' => '+$120/person',
                    'price' => 120
               ),
               'kajak' => array(
                    'id' => 'kajak',
                    'name' => 'Kajak Tour at Punta Coral',
                    'price_desc' => '+$190/person',
                    'price' => 190
               ),
            )
        );

        $arrival = strtotime($data['date']);
        $nights = $data['nights'];
        $travelers = (isset($data['travelers'])) ? $data['travelers'] : $data['adults'] + $data['childs'];

        $departure = $arrival + (86400 * $nights);
        
        $ppn = $info[$id]['_price'];
        $price = ($ppn + $rooms[$id][$data['room']]['price']) * $travelers * $nights;

        $addons = 0;
        $addons_keys = '';
        foreach ($data['activities'] as $a) {
            $addons += $activities[$id][$a]['price'] * $travelers;
            $addons_keys .= $a.', ';
        }

        $price += $addons;
        
        $purchases = new Zend_Db_Table('ai_purchases');
        $pur = $purchases->fetchNew();
        
        $pur->package     = $packages[$data['package']];
        $pur->name        = $data['name'];
        $pur->email       = $data['email'];
        $pur->phone       = $data['phone'];
        $pur->country     = $data['country'];
        $pur->arrival     = date('Y-m-d', $arrival);
        $pur->departure   = date('Y-m-d', $departure);
        $pur->adults      = $data['adults'];
        $pur->childs      = $data['childs'];
        $pur->room        = $data['room'];
        $pur->nights      = $data['nights'];
        $pur->price       = $price;
        $pur->additional  = $addons;
        $pur->addons      = $addons_keys;
        $pur->stripeToken = $data['stripeToken'];
        $pur->created     = date('Y-m-d H:i:s');
        $pur->version     = $version;
        
        $pur->save();
        
        $notifier = new WS_Notifier();
        $notifier->notifyAIpurchase($pur, $hotels, $rooms[$id]);
        
        //echo '<pre>'; print_r($pur); echo '</pre>'; die;
        
        $this->_redirect('/en-US/landing/'.$version.'/thanks/?p='.$pur->id);
    }

    public function thanksAction() {
        $purchases = new Zend_Db_Table('ai_purchases');
        $select = $purchases->select();
        $select->where('id = ?', $_GET['p']);
        $pur = $purchases->fetchRow($select);
        
        $hotels = array(
            'papagayo'   => 'Alegro Papagayo (All Inclusive)',
            'riu'        => 'RIU Guanacaste (All Inclusive)',
            'langosta'   => 'Barcelo Langosta (All Inclusive)',
            'doubletree' => 'DoubleTree Puntarenas (All Inclusive)',
        );
        
        $rooms = array(
            'papagayo' => array(
                'sup' => array(
                    'id' => 'sup',
                    'name' => 'Superior',
                    'price_desc' => 'Included',
                    'price' => 0,
                ),
                'supov' => array(
                    'id' => 'supov',
                    'name' => 'Superior OV',
                    'price_desc' => 'Add $12 per person per night',
                    'price' => 12
                ),
                'supovprem' => array(
                    'id' => 'supovprem',
                    'name' => 'Superior OV Premium',
                    'price_desc' => 'Add $18 per person per night',
                    'price' => 18,
                ),
            ),
            'riu' => array(
                'standard' => array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Included',
                    'price' => 0,
                ),
                'ocean' => array(
                    'id' => 'ocean',
                    'name' => 'Standard ocean view',
                    'price_desc' => 'Add $50 per person per night',
                    'price' => 50,
                ),
                'jacuzzi' => array(
                    'id' => 'jacuzzi',
                    'name' => 'Jacuzzi Suite',
                    'price_desc' => 'Add $145 per person per night',
                    'price' => 145,
                ),
            ),
            'langosta' => array(
                'superior' => array(
                    'id' => 'superior',
                    'name' => 'Superior',
                    'price_desc' => 'Imcluded',
                    'price' => 0
                ),
                'deluxe' => array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe',
                    'price_desc' => 'Add $35 per person per night',
                    'price' => 35
                ),
                'suite' => array(
                    'id' => 'suite',
                    'name' => 'Suite',
                    'price_desc' => 'Add $42 per person per night',
                    'price' => 42
                )
            ),
            'doubletree' => array(
                'standard' => array(
                    'id' => 'standard',
                    'name' => 'Standard',
                    'price_desc' => 'Included',
                    'price' => 0
                ),
                'deluxe' => array(
                    'id' => 'deluxe',
                    'name' => 'Deluxe o Junior Suite',
                    'price_desc' => 'Add $19 per person per night',
                    'price' => 19
                )
            )
        );
        
        require_once "Stripe/Stripe.php";
        $keys = Zend_Registry::get('stripe');
        Stripe::setApiKey($keys['secret_key']);

        // get the credit card details submitted by the form
        if(empty($pur->stripe_id)) {
            $token = $pur->stripeToken;

            // create a Customer
            $customer = Stripe_Customer::create(array(
                "card" => $token,
                "description" => $pur->email)
            );
            $pur->stripe_id = $customer->id;
            $pur->save();
        } else {
            $customer = Stripe_Customer::retrieve($pur->stripe_id);            
        }
        
        $this->view->info = $pur;
        $this->view->hotel = $hotels[$pur->package];
        $this->view->room  = $rooms[$pur->package][$pur->room]['name'];
        $this->view->card = $customer->active_card;
    }

}
