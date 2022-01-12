<?php

namespace App\DataFixtures;

use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class LivreFixtures extends Fixture
{

    protected Generator $faker;

    public function load(ObjectManager $manager): void
    {
        $this->faker = Factory::create('fr_FR');

        for ($i = 0; $i < 50; $i++) {
            $livre = new Livre();
            $livre->setAuteur($this->faker->firstName.' '.$this->faker->lastName);
            $livre->setTitre($this->faker->realText($this->faker->numberBetween(10,100)));
            $livre->setNbPage(rand(50,999));
            $manager->persist($livre);
        }

        $manager->flush();
    }
}
