<?php

namespace App\DataFixtures;

use App\Entity\Amenity;
use App\Entity\Badge;
use App\Entity\Booking;
use App\Entity\Challenge;
use App\Entity\Message;
use App\Entity\Property;
use App\Entity\PropertyAmenity;
use App\Entity\PropertyImage;
use App\Entity\PropertyView;
use App\Entity\Review;
use App\Entity\User;
use App\Entity\UserBadge;
use App\Entity\UserChallenge;
use App\Entity\UserGamification;
use App\Entity\UserSession;
use App\Entity\Wishlist;
use App\Entity\WishlistItem;
use App\Entity\XpTransaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        // ========== USER (table user) ==========
        $users = [];
        $userData = [
            ['Marie', 'Dupont', 'marie.dupont@example.com', true],
            ['Jean', 'Martin', 'jean.martin@example.com', true],
            ['Sophie', 'Bernard', 'sophie.bernard@example.com', false],
            ['Pierre', 'Petit', 'pierre.petit@example.com', false],
            ['Julie', 'Robert', 'julie.robert@example.com', true],
            ['Thomas', 'Richard', 'thomas.richard@example.com', true],
            ['Camille', 'Durand', 'camille.durand@example.com', false],
            ['Lucas', 'Leroy', 'lucas.leroy@example.com', true],
            ['Léa', 'Moreau', 'lea.moreau@example.com', false],
            ['Hugo', 'Simon', 'hugo.simon@example.com', false],
            ['Chloé', 'Laurent', 'chloe.laurent@example.com', true],
            ['Nathan', 'Lefebvre', 'nathan.lefebvre@example.com', false],
            ['Emma', 'Michel', 'emma.michel@example.com', true],
            ['Louis', 'Garcia', 'louis.garcia@example.com', false],
            ['Manon', 'David', 'manon.david@example.com', false],
        ];
        foreach ($userData as [$firstName, $lastName, $email, $isHost]) {
            $user = (new User())
                ->setEmail($email)
                ->setPasswordHash(password_hash('motdepasse', \PASSWORD_DEFAULT))
                ->setFirstName($firstName)
                ->setLastName($lastName)
                ->setPhone('+336'.random_int(10000000, 99999999))
                ->setAvatarUrl('https://api.dicebear.com/7.x/avataaars/svg?seed='.$firstName)
                ->setBio('Bio de '.$firstName.' '.$lastName.'. Passionné(e) de voyage.')
                ->setIsHost($isHost)
                ->setIsVerified((bool) random_int(0, 1));
            $user->setCreatedAt($now);
            $user->setUpdatedAt($now);
            $manager->persist($user);
            $users[] = $user;
        }

        // ========== USER_GAMIFICATION ==========
        $levels = ['Débutant', 'Explorateur', 'Voyageur', 'Expert', 'Légende'];
        foreach ($users as $user) {
            $gamification = (new UserGamification())
                ->setUser($user)
                ->setLevel($levels[array_rand($levels)])
                ->setTotalXp(random_int(100, 8000))
                ->setCurrentStreakDays(random_int(0, 21))
                ->setLongestStreakDays(random_int(5, 60))
                ->setCitiesExploredCount(random_int(0, 25));
            $gamification->setCreatedAt($now);
            $gamification->setUpdatedAt($now);
            $user->setUserGamification($gamification);
            $manager->persist($gamification);
        }

        // ========== BADGE ==========
        $badges = [];
        $badgeData = [
            ['first_booking', 'Premier voyage', 'Réalisé votre première réservation', 'badge-first.png', 'voyage'],
            ['explorer', 'Explorateur', 'Visité 5 villes différentes', 'badge-explorer.png', 'voyage'],
            ['super_host', 'Super hôte', 'Reçu 10 avis 5 étoiles', 'badge-host.png', 'hôte'],
            ['early_bird', 'Lève-tôt', 'Réservation effectuée avant 8h', 'badge-early.png', 'divertissement'],
            ['world_traveler', 'Globe-trotter', 'Visité 10 pays', 'badge-world.png', 'voyage'],
            ['reviewer', 'Critique', 'Laissé 5 avis détaillés', 'badge-review.png', 'communauté'],
            ['loyal', 'Fidèle', '10 réservations sur la plateforme', 'badge-loyal.png', 'voyage'],
            ['eco_friendly', 'Éco-responsable', 'Séjour éco-labellisé', 'badge-eco.png', 'engagement'],
        ];
        foreach ($badgeData as [$code, $name, $description, $icon, $category]) {
            $badge = (new Badge())
                ->setCode($code)
                ->setName($name)
                ->setDescription($description)
                ->setIconUrl('https://example.com/icons/'.$icon)
                ->setCategory($category)
                ->setCreatedAt($now);
            $manager->persist($badge);
            $badges[] = $badge;
        }

        // ========== USER_BADGE ==========
        foreach ($users as $user) {
            $numBadges = random_int(1, 4);
            $assigned = [];
            for ($i = 0; $i < $numBadges; ++$i) {
                $badge = $badges[array_rand($badges)];
                if (!in_array($badge, $assigned, true)) {
                    $assigned[] = $badge;
                    $userBadge = (new UserBadge())
                        ->setUser($user)
                        ->setBadge($badge)
                        ->setEarnedAt($now->modify('-'.random_int(1, 90).' days'));
                    $manager->persist($userBadge);
                }
            }
        }

        // ========== CHALLENGE ==========
        $challenges = [];
        $challengeData = [
            ['book_3', 'Triple réservation', 'Effectuez 3 réservations', 'reservation', 3, 150, '-14 days', '+45 days'],
            ['review_1', 'Premier avis', 'Laissez un avis après un séjour', 'avis', 1, 50, '-7 days', '+30 days'],
            ['cities_5', '5 villes', 'Visitez 5 villes différentes', 'exploration', 5, 200, '-14 days', '+60 days'],
            ['weekend_escape', 'Week-end évasion', 'Réservez un week-end', 'reservation', 1, 75, '-3 days', '+21 days'],
            ['early_review', 'Avis rapide', 'Laissez un avis sous 48h', 'avis', 1, 30, '-5 days', '+14 days'],
            ['explorer_10', '10 explorations', 'Consultez 10 annonces', 'exploration', 10, 100, '-7 days', '+30 days'],
        ];
        foreach ($challengeData as [$code, $name, $desc, $type, $target, $xp, $start, $end]) {
            $challenge = (new Challenge())
                ->setCode($code)
                ->setName($name)
                ->setDescription($desc)
                ->setChallengeType($type)
                ->setTargetValue($target)
                ->setXpReward($xp)
                ->setStartDate($now->modify($start))
                ->setEndDate($now->modify($end))
                ->setIsActive(true)
                ->setCreatedAt($now)
                ->setUpdatedAt($now);
            $manager->persist($challenge);
            $challenges[] = $challenge;
        }

        // ========== USER_CHALLENGE ==========
        foreach ($users as $user) {
            $numChallenges = random_int(1, 4);
            $used = [];
            for ($i = 0; $i < $numChallenges; ++$i) {
                $idx = array_rand($challenges);
                if (!isset($used[$idx])) {
                    $used[$idx] = true;
                    $challenge = $challenges[$idx];
                    $completed = random_int(0, 1);
                    $uc = (new UserChallenge())
                        ->setUser($user)
                        ->setChallenge($challenge)
                        ->setCurrentProgress($completed ? $challenge->getTargetValue() : random_int(0, $challenge->getTargetValue()))
                        ->setIsCompleted((bool) $completed);
                    if ($completed) {
                        $uc->setCompletedAt($now->modify('-'.random_int(1, 20).' days'));
                    }
                    $uc->setCreatedAt($now);
                    $uc->setUpdatedAt($now);
                    $manager->persist($uc);
                }
            }
        }

        // ========== XP_TRANSACTION ==========
        $sources = ['reservation', 'defi', 'avis', 'bonus'];
        $sourcesLibelles = ['réservation', 'défi', 'avis', 'bonus'];
        foreach ($users as $user) {
            for ($i = 0; $i < random_int(3, 12); ++$i) {
                $sourceIdx = array_rand($sources);
                $xp = (new XpTransaction())
                    ->setUser($user)
                    ->setAmount(random_int(10, 200))
                    ->setSourceType($sources[$sourceIdx])
                    ->setSourceId(random_int(1, 50))
                    ->setDescription('XP gagné - '.$sourcesLibelles[$sourceIdx])
                    ->setCreatedAt($now->modify('-'.random_int(1, 60).' days'));
                $manager->persist($xp);
            }
        }

        // ========== AMENITY ==========
        $amenities = [];
        $amenityData = [
            ['wifi', 'Wi-Fi', 'wifi', 'confort'],
            ['parking', 'Parking', 'car', 'extérieur'],
            ['piscine', 'Piscine', 'pool', 'loisirs'],
            ['clim', 'Climatisation', 'snowflake', 'confort'],
            ['cuisine', 'Cuisine équipée', 'utensils', 'confort'],
            ['lave_linge', 'Lave-linge', 'tshirt', 'confort'],
            ['tv', 'Télévision', 'tv', 'confort'],
            ['jardin', 'Jardin', 'tree', 'extérieur'],
            ['balcon', 'Balcon', 'sun', 'extérieur'],
            ['seche_linge', 'Sèche-linge', 'wind', 'confort'],
            ['petit_dejeuner', 'Petit-déjeuner inclus', 'coffee', 'services'],
            ['animaux', 'Animaux acceptés', 'paw', 'regles'],
        ];
        foreach ($amenityData as [$code, $name, $icon, $category]) {
            $amenity = (new Amenity())
                ->setCode($code)
                ->setName($name)
                ->setIcon($icon)
                ->setCategory($category)
                ->setCreatedAt($now);
            $manager->persist($amenity);
            $amenities[] = $amenity;
        }

        // ========== PROPERTY ==========
        $properties = [];
        $hosts = array_values(array_filter($users, fn (User $u) => $u->isHost()));
        $cities = [
            ['Paris', '75001', '48.8566', '2.3522'],
            ['Lyon', '69001', '45.7640', '4.8357'],
            ['Bordeaux', '33000', '44.8378', '-0.5792'],
            ['Nice', '06000', '43.7102', '7.2620'],
            ['Marseille', '13001', '43.2965', '5.3698'],
            ['Toulouse', '31000', '43.6047', '1.4442'],
            ['Nantes', '44000', '47.2184', '-1.5536'],
            ['Strasbourg', '67000', '48.5734', '7.7521'],
        ];
        $types = ['appartement', 'maison', 'studio', 'loft'];
        $idxCity = 0;
        foreach ($hosts as $host) {
            $numProperties = random_int(2, 4);
            for ($p = 0; $p < $numProperties; ++$p) {
                $city = $cities[$idxCity % count($cities)];
                ++$idxCity;
                $property = (new Property())
                    ->setHost($host)
                    ->setTitle(ucfirst($types[array_rand($types)]).' confortable '.$city[0].' - Centre')
                    ->setDescription('Bel logement bien situé, proche des transports et commerces. Idéal pour un séjour en ville.')
                    ->setPropertyType($types[array_rand($types)])
                    ->setAddressLine1(random_int(1, 150).' rue de la République')
                    ->setAddressLine2(random_int(0, 1) ? 'Bât. '.random_int(1, 5) : null)
                    ->setCity($city[0])
                    ->setState('')
                    ->setCountry('France')
                    ->setPostalCode($city[1])
                    ->setLatitude($city[2])
                    ->setLongitude($city[3])
                    ->setMaxGuests(random_int(2, 8))
                    ->setBedrooms(random_int(1, 4))
                    ->setBeds(random_int(1, 6))
                    ->setBathrooms((string) (random_int(1, 3) + .5))
                    ->setPricePerNight((string) random_int(50, 250))
                    ->setCleaningFee((string) random_int(25, 100))
                    ->setIsActive(true);
                $property->setCreatedAt($now);
                $property->setUpdatedAt($now);
                $manager->persist($property);
                $properties[] = $property;
            }
        }

        // ========== PROPERTY_IMAGE ==========
        foreach ($properties as $idx => $property) {
            $numImages = random_int(2, 5);
            for ($imgOrder = 0; $imgOrder < $numImages; ++$imgOrder) {
                $img = (new PropertyImage())
                    ->setProperty($property)
                    ->setImageUrl('https://picsum.photos/800/600?random='.($idx * 10 + $imgOrder))
                    ->setIsPrimary(0 === $imgOrder)
                    ->setDisplayOrder($imgOrder)
                    ->setCreatedAt($now);
                $manager->persist($img);
            }
        }

        // ========== PROPERTY_AMENITY ==========
        foreach ($properties as $property) {
            $numAmenities = random_int(4, min(8, count($amenities)));
            $keys = array_values((array) array_rand($amenities, $numAmenities));
            foreach ($keys as $key) {
                $pa = (new PropertyAmenity())->setProperty($property)->setAmenity($amenities[$key]);
                $manager->persist($pa);
            }
        }

        // ========== BOOKING ==========
        $bookings = [];
        $guests = array_values(array_filter($users, fn (User $u) => !$u->isHost()));
        $statuses = ['confirme', 'confirme', 'confirme', 'en_attente', 'annule', 'termine'];
        for ($b = 0; $b < min(25, count($properties) * 2); ++$b) {
            $property = $properties[array_rand($properties)];
            $guest = $guests[array_rand($guests)];
            $daysOffset = random_int(-30, 60);
            $nights = random_int(2, 7);
            $checkIn = $now->modify('+'.$daysOffset.' days');
            $checkOut = $checkIn->modify('+'.$nights.' days');
            $pricePerNight = (float) $property->getPricePerNight();
            $cleaningFee = (float) $property->getCleaningFee();
            $total = $nights * $pricePerNight + $cleaningFee;
            $status = $statuses[array_rand($statuses)];
            $booking = (new Booking())
                ->setProperty($property)
                ->setGuest($guest)
                ->setCheckInDate(\DateTime::createFromImmutable($checkIn))
                ->setCheckOutDate(\DateTime::createFromImmutable($checkOut))
                ->setNumberOfGuests(random_int(1, min(4, (int) $property->getMaxGuests())))
                ->setNumberOfNights($nights)
                ->setPricePerNight((string) $pricePerNight)
                ->setCleaningFee((string) $cleaningFee)
                ->setTotalAmount((string) round($total, 2))
                ->setBookingStatus($status)
                ->setPaymentStatus('annule' === $status ? 'rembourse' : ('confirme' === $status || 'termine' === $status ? 'paye' : 'en_attente'))
                ->setPaymentMethod(['carte', 'paypal', 'virement'][array_rand(['carte', 'paypal', 'virement'])])
                ->setSpecialRequests(random_int(0, 1) ? 'Arrivée prévue en fin d\'après-midi.' : null);
            if ('annule' === $status) {
                $booking->setCancelledAt($now->modify('-'.random_int(1, 10).' days'));
            }
            $booking->setCreatedAt($now);
            $booking->setUpdatedAt($now);
            $manager->persist($booking);
            $bookings[] = $booking;
        }

        // ========== REVIEW ==========
        foreach ($bookings as $booking) {
            if (!in_array($booking->getBookingStatus(), ['confirme', 'termine'], true)) {
                continue;
            }
            if (0 === random_int(0, 1)) {
                continue;
            }
            $rating = (string) (random_int(35, 50) / 10);
            $review = (new Review())
                ->setBooking($booking)
                ->setReviewer($booking->getGuest())
                ->setReviewee($booking->getProperty()->getHost())
                ->setReviewType('bien')
                ->setRating($rating)
                ->setCleanlinessRating($rating)
                ->setAccuracyRating($rating)
                ->setCommunicationRating($rating)
                ->setLocationRating($rating)
                ->setValueRating($rating)
                ->setComment('Très bon séjour, tout était parfait. Je recommande !')
                ->setIsVisible(true);
            $review->setCreatedAt($now);
            $review->setUpdatedAt($now);
            $manager->persist($review);
        }

        // ========== WISHLIST ==========
        $wishlistNames = ['Mes favoris', 'Week-ends', 'Été 2025', 'Voyage en couple', 'Famille'];
        foreach ($users as $user) {
            $numWishlists = random_int(1, 3);
            for ($w = 0; $w < $numWishlists; ++$w) {
                $wishlist = (new Wishlist())
                    ->setUser($user)
                    ->setName($wishlistNames[array_rand($wishlistNames)])
                    ->setIsPrivate((bool) random_int(0, 1));
                $wishlist->setCreatedAt($now);
                $wishlist->setUpdatedAt($now);
                $manager->persist($wishlist);
                // ========== WISHLIST_ITEM ==========
                $numItems = random_int(1, min(6, count($properties)));
                $usedPropKeys = [];
                for ($wi = 0; $wi < $numItems; ++$wi) {
                    $pk = array_rand($properties);
                    if (!isset($usedPropKeys[$pk])) {
                        $usedPropKeys[$pk] = true;
                        $wishlistItem = (new WishlistItem())
                            ->setWishlist($wishlist)
                            ->setProperty($properties[$pk])
                            ->setAddedAt($now->modify('-'.random_int(1, 30).' days'));
                        $manager->persist($wishlistItem);
                    }
                }
            }
        }

        // ========== MESSAGE ==========
        foreach ($bookings as $booking) {
            if ('annule' === $booking->getBookingStatus()) {
                continue;
            }
            $numMessages = random_int(1, 4);
            $senderIsGuest = true;
            for ($m = 0; $m < $numMessages; ++$m) {
                $sender = $senderIsGuest ? $booking->getGuest() : $booking->getProperty()->getHost();
                $receiver = $senderIsGuest ? $booking->getProperty()->getHost() : $booking->getGuest();
                $msg = (new Message())
                    ->setBooking($booking)
                    ->setSender($sender)
                    ->setReceiver($receiver)
                    ->setMessageText(0 === $m ? 'Bonjour, à quelle heure puis-je récupérer les clés ?' : ('Message '.($m + 1).' de la conversation.'))
                    ->setIsRead($m < $numMessages - 1)
                    ->setReadAt($m < $numMessages - 1 ? $now : null)
                    ->setCreatedAt($now->modify('-'.($numMessages - $m).' days'));
                $manager->persist($msg);
                $senderIsGuest = !$senderIsGuest;
            }
        }

        // ========== USER_SESSION ==========
        foreach ($users as $user) {
            $numSessions = random_int(2, 8);
            for ($s = 0; $s < $numSessions; ++$s) {
                $start = $now->modify('-'.random_int(1, 30).' days')->modify('-'.random_int(0, 23).' hours');
                $end = $start->modify('+'.random_int(15, 180).' minutes');
                $session = (new UserSession())
                    ->setUser($user)
                    ->setSessionStart($start)
                    ->setSessionEnd($end)
                    ->setDurationSeconds(random_int(900, 14400))
                    ->setCreatedAt($now);
                $manager->persist($session);
            }
        }

        // ========== PROPERTY_VIEW ==========
        foreach ($users as $user) {
            $numViews = random_int(3, min(15, count($properties)));
            $viewedKeys = [];
            for ($v = 0; $v < $numViews; ++$v) {
                $pk = array_rand($properties);
                if (!isset($viewedKeys[$pk])) {
                    $viewedKeys[$pk] = true;
                    $pv = (new PropertyView())
                        ->setUser($user)
                        ->setProperty($properties[$pk])
                        ->setViewedAt($now->modify('-'.random_int(1, 45).' days'));
                    $manager->persist($pv);
                }
            }
        }

        $manager->flush();
    }
}
