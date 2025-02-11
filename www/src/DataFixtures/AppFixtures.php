<?php

namespace App\DataFixtures;

use App\Entity\Equipment;
use App\Entity\Price;
use App\Entity\Rental;
use App\Entity\Reservation;
use App\Entity\Season;
use App\Entity\TypeRental;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadEquipments($manager);
        $this->loadTypesRentals($manager);
        $this->loadSeasons($manager);
        $this->loadRentals($manager);
        $this->loadRentalsEquipments($manager);
        $this->loadPrices($manager);
        $this->loadReservations($manager);
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager): void
    {
        $array_users = [
            [
                'firstname' => 'Tom',
                'lastname' => 'Ford',
                'email' => 'tom.ford@tom.com',
                'password' => 'tom123',
                'date_of_birth' => '2006-02-10',
                'roles' => ['ROLE_ADMIN'],
                'username' => 'tomford',
                'phone' => '0606060606',
                'address' => '5 rue de la paix',
            ],
            [
                'firstname' => 'Corentin',
                'lastname' => 'Desssapt',
                'email' => 'corentin.dessapt@corentin.com',
                'password' => 'corentin123',
                'date_of_birth' => '2003-01-01',
                'roles' => ['ROLE_USER'],
                'username' => 'corentindessapt',
                'phone' => '0707070707',
                'address' => '9 rue de la Hess',
            ],
        ];

        foreach ($array_users as $key => $value) {
            $user = new User();
            $user->setEmail($value['email']);
            $user->setPassword($this->encoder->hashPassword($user, $value['password']));
            $user->setFirstname($value['firstname']);
            $user->setLastname($value['lastname']);
            $user->setDateOfBirth(new \DateTime($value['date_of_birth']));
            $user->setRoles($value['roles']);
            $user->setUsername($value['username']);
            $user->setPhone($value['phone']);
            $user->setAddress($value['address']);
            $manager->persist($user);

            // Définir une référence pour chaque utilisateur
            $this->addReference('user_' . ($key + 1), $user);
        }
    }


    public function loadEquipments(ObjectManager $manager): void
    {
        $array_equipments = [
            [
                'label' => 'Spa'
            ],
            [
                'label' => 'tente 45 porte'
            ],
            [
                'label' => 'station de charge pour voiture électrique'
            ]
        ];

        foreach ($array_equipments as $key => $value) {
            $equipment = new Equipment();
            $equipment->setLabel($value['label']);
            $manager->persist($equipment);

            // Définir une référence pour chaque équipement
            $this->addReference('equipment_' . ($key + 1), $equipment);
        }
    }


    public function loadRentalsEquipments(ObjectManager $manager): void
    {
        $array_rental_equipment = [
            [
                'rental_id' => 1,
                'equipment_id' => 1,
            ],
            [
                'rental_id' => 1,
                'equipment_id' => 3,
            ],
        ];

        foreach ($array_rental_equipment as $key => $value) {
            $rental = $this->getReference('rental_' . $value['rental_id']);
            $equipment = $this->getReference('equipment_' . $value['equipment_id']);
            $rental->addEquipment($equipment);
            $manager->persist($rental);
        }
    }


    public function loadTypesRentals(ObjectManager $manager): void
    {
        $array_types_rental = [
            [
                'label' => 'tente 1 porte'
            ],
            [
                'label' => 'tente 45 porte'
            ],
            [
                'label' => 'mobile home'
            ]
        ];

        foreach ($array_types_rental as $key => $value) {
            $type_rental = new TypeRental();
            $type_rental->setLabel($value['label']);
            $manager->persist($type_rental);

            // Définir une référence pour chaque type de location
            $this->addReference('type_rental_' . ($key + 1), $type_rental);
        }
    }

    public function loadRentals(ObjectManager $manager): void
    {
        $array_rental = [
            [
                'title' => 'Magnifique mobil home, vue sur la mer',
                'description' => 'Mobil home 4 places avec vue sur la mer, parfait pour des vacances en famille',
                'capacity' => 5,
                'nbr_localization' => 45,
                'type_rental_id' => 3,
                'isActive' => 1,
                'image' => 'mobil_home1.jpg',
            ],
            [
                'title' => 'Tente 1 porte avec hamac',
                'description' => 'Tente 1 porte avec hamac, idéal pour un week-end en amoureux',
                'capacity' => 2,
                'nbr_localization' => 15,
                'type_rental_id' => 1,
                'isActive' => 0,
                'image' => 'tente1.jpg',
            ],
        ];

        foreach ($array_rental as $key => $value) {
            $rental = new Rental();
            $rental->setTitle($value['title']);
            $rental->setDescription($value['description']);
            $rental->setCapacity($value['capacity']);
            $rental->setNbrLocalization($value['nbr_localization']);
            $rental->setTypeRental($this->getReference('type_rental_' . $value['type_rental_id']));
            $rental->setActive($value['isActive']);
            $rental->setImage($value['image']);
            $manager->persist($rental);

            // Définir une référence pour chaque location
            $this->addReference('rental_' . ($key + 1), $rental);
        }
    }

    public function loadSeasons(ObjectManager $manager): void
    {
        $array_season = [
            [
                'label' => 'Haute saison',
                'season_start' => '2021-07-01',
                'season_end' => '2021-08-31',
            ],
            [
                'label' => 'Basse saison',
                'season_start' => '2024-09-01',
                'season_end' => '2024-06-30',
            ],
        ];

        foreach ($array_season as $key => $value) {
            $season = new Season();
            $season->setLabel($value['label']);
            $season->setSeasonStart(new \DateTime($value['season_start']));
            $season->setSeasonEnd(new \DateTime($value['season_end']));
            $manager->persist($season);

            // Définir une référence pour chaque saison
            $this->addReference('season_' . ($key + 1), $season);
        }
    }

    public function loadPrices(ObjectManager $manager): void
    {
        $array_prices = [
            [
                'rental_id' => 1,
                'season_id' => 1,
                'price_per_night' => 50,
            ],
            [
                'rental_id' => 1,
                'season_id' => 2,
                'price_per_night' => 15,
            ],
        ];

        foreach ($array_prices as $key => $value) {
            $price = new Price();
            $price->setRental($this->getReference('rental_' . $value['rental_id']));
            $price->setSeason($this->getReference('season_' . $value['season_id']));
            $price->setPricePerNight($value['price_per_night']);
            $manager->persist($price);

            // Définir une référence pour chaque prix
            $this->addReference('price_' . ($key + 1), $price);
        }
    }

    public function loadReservations(ObjectManager $manager): void
    {
        $array_reservations = [
            [
                'rental_id' => 1,
                'user_id' => 2,
                'date_start' => '2021-07-15',
                'date_end' => '2021-07-20',
                'nbr_adult' => 2,
                'nbr_minor' => 0,
                'status' => 'disponible',
                'checked' => 1,
                'applied_price_id' => 1,
            ],
            [
                'rental_id' => 2,
                'user_id' => 2,
                'date_start' => '2024-07-15',
                'date_end' => '2024-07-20',
                'nbr_adult' => 2,
                'nbr_minor' => 2,
                'status' => 'disponible',
                'checked' => 0,
                'applied_price_id' => 2,
            ],
        ];

        foreach ($array_reservations as $key => $value) {
            $reservation = new Reservation();
            $reservation->setRental($this->getReference('rental_' . $value['rental_id']));
            $reservation->setRenter($this->getReference('user_' . $value['user_id']));
            $reservation->setDateStart(new \DateTime($value['date_start']));
            $reservation->setDateEnd(new \DateTime($value['date_end']));
            $reservation->setNbrAdult($value['nbr_adult']);
            $reservation->setNbrMinor($value['nbr_minor']);
            $reservation->setStatus($value['status']);
            $reservation->setChecked($value['checked']);
            $reservation->setAppliedPrice($this->getReference('price_' . $value['applied_price_id']));
            $manager->persist($reservation);
        }
    }
    
}
