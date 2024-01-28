<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Trick;

class SnowboardFiguresFixtures extends Fixture
{
    public function load(ObjectManager $manager) : void
    {
        $tricksData = [
            ['name' => 'Method Grab', 'description' => 'Saisir le talon de la planche avec la main arrière tout en pliant les genoux et en ouvrant les épaules.'],
            ['name' => 'Frontside 360', 'description' => 'Effectuer une rotation de 360 degrés dans le sens des aiguilles d\'une montre (pour un rider régulier) en l\'air.'],
            ['name' => 'Backside Boardslide', 'description' => 'Glisser sur un obstacle avec la planche perpendiculaire à la barre, en utilisant le côté arrière de la planche.'],
            ['name' => 'Indy Grab', 'description' => 'Saisir le côté de la planche entre les fixations avec la main avant.'],
            ['name' => '720 Double Cork', 'description' => 'Effectuer une rotation de 720 degrés tout en effectuant deux flips.'],
            ['name' => 'Frontside 540 Tail Grab', 'description' => 'Effectuer une rotation de 540 degrés dans le sens des aiguilles d\'une montre tout en saisissant le tail (extrémité arrière) de la planche.'],
            ['name' => 'Nose Press', 'description' => 'Incliner la planche vers l\'avant de manière à ce que le nose (extrémité avant) touche un obstacle tout en maintenant l\'équilibre.'],
            ['name' => 'Cab 180 Nose Grab', 'description' => 'Effectuer une rotation de 180 degrés dans le sens inverse des aiguilles d\'une montre tout en saisissant le nose de la planche.'],
            ['name' => 'Backflip Tailgrab', 'description' => 'Effectuer un flip arrière tout en saisissant le tail de la planche.'],
            ['name' => 'Switch Backside 50-50', 'description' => 'Glisser sur un obstacle avec la planche perpendiculaire à la barre, en utilisant le côté arrière de la planche, en position switch.'],
        ];

        foreach ($tricksData as $trickData) {
            $trick = new Trick();
            $trick->setName($trickData['name']);
            $trick->setDescription($trickData['description']);

            $manager->persist($trick);
        }

        $manager->flush();
    }
}
