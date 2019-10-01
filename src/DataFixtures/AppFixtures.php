<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Command;
use App\Entity\CommandProduct;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;
use Metrakit\EddyMalou\EddyMalouProvider;
use Metrakit\EddyMalou\TextProvider;
use Faker\Factory;
use DavidBadura\FakerMarkdownGenerator\FakerProvider;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new EddyMalouProvider($faker));
        $faker->addProvider(new TextProvider($faker));
        $faker->addProvider(new FakerProvider($faker));

        $products = [];
        $categoryTitles = [
            "High-tech" => ["Ordinateurs", "Matériel", "Audio"],
            "Geekeries" => ["habits", "Goodies"]
        ];
        foreach ($categoryTitles as $title => $subCategories) {
            $category = new Category();
            $category->setName($title);
            foreach ($subCategories as $title => $subCategoryName) {
                $subCategory = new Category();
                $subCategory->setName($subCategoryName);
                $subCategory->setParent($category);
                for ($p = 0; $p < 20; $p++) {
                    $product = new Product();
                    $product
                        ->setTitle($faker->catchPhrase())
                        ->setIntroduction($faker->markdownP())
                        ->setDescription(
                            $faker->markdownP(250) . "\n\n" . $faker->markdownH3() . "\n\n" . $faker->markdownP(800)
                        )
                        ->setPrice($faker->randomFloat(2, 50, 5000))
                        ->setPicture($faker->imageUrl(400, 400))
                        ->setFeatured($faker->boolean(20))
                        ->setCategory($subCategory);
                    $manager->persist($product);
                    $products[] = $product;
                }
                $manager->persist($subCategory);
            }
            $manager->persist($category);
        }

        for ($c = 0; $c < 10; $c++) {
            $command = new Command();
            $command
                ->setAddress($faker->address())
                ->setCreatedAt($faker->dateTimeBetween("-6 months"));
            $manager->persist($command);
            $randomProducts = $faker->randomElements($products, mt_rand(1, 4));
            foreach ($randomProducts as $product) {
                $commandProduct = new CommandProduct();
                $commandProduct
                    ->setProduct($product)
                    ->setCommand($command)
                    ->setQuantity(mt_rand(1, 5));
                $manager->persist($commandProduct);
            }
        }

        $manager->flush();
    }
}
