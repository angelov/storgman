<?php

use Angelov\Eestec\Platform\LocalCommittees\Cities\Commands\StoreCityCommand;
use Angelov\Eestec\Platform\LocalCommittees\Cities\Location;
use Illuminate\Database\Seeder;
use Symfony\Component\HttpFoundation\File\File;

class FakeCitiesSeeder extends Seeder
{
    protected $faker;

    public function __construct(\Faker\Factory $fakerFactory)
    {
        $this->faker = $fakerFactory->create();
    }

    public function run()
    {
        for ($i=0; $i<10; $i++) {

            $name = $this->faker->city;
            $country = $this->faker->country;
            $location = new Location($this->faker->latitude, $this->faker->longitude); // this may not be in europe
            $details = $this->faker->realText();

            $image = $this->faker->image('/tmp', 640, 480, 'city');
            $image = new File($image);

            dispatch(new StoreCityCommand($name, $country, $location, $image, $details));

        }
    }
}
