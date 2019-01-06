<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run() {
        $this->call(UserTableSeeder::class);
        $this->call(AuthTypeTableSeeder::class);
        $this->call(AuthTrackingTableSeeder::class);
//        $this->call(NetworkTableSeeder::class);     // To be seeded with MySQL Command
        $this->call(StatusTableSeeder::class);
        $this->call(ContentRatingTableSeeder::class);
        $this->call(BookmarkTypeTableSeeder::class);
    }

    /*
     * Genres       INSERT INTO serie.genres (name) SELECT name FROM tvshows.genres
     * Networks     INSERT INTO serie.networks (name, type, country_code, country_name, link, banner, created_at, updated_at) SELECT name, 0, country, NULL, link, NULL, NOW(), NOW() FROM tvshows.networks
     * Updates      INSERT INTO serie.updates (type, finished_at, created_at, updated_at) SELECT type, finish, time, finish FROM tvshows.updates
     */
}

class UserTableSeeder extends Seeder {

    public function run() {
        DB::table('users')->delete();
        \App\User::create([
            'name' => 'ivan',
            'email' => 'ivan.zuanella@gmail.com',
            'email_verified_at' => NULL,
            'password' => '$2y$07$mZ3StKt6PkWcJOpER4hyHO2e789C29za3zsEff8C.V/cUvlcYx7o2',
            'firstname' => 'Ivan',
            'lastname' => 'Zuanella',
            'privileges' => 100,
            'role' => 100,
            'timezone' => 'Europe/Rome',
            'created_at' => '2018-12-29 17:32:04',
            'updated_at' => '2018-12-29 17:32:04',
        ]);
    }
}

class AuthTypeTableSeeder extends Seeder {
    public function run() {
        DB::table('auth_types')->delete();
        \App\AuthType::create([
                'name' => 'login',
                'created_at' => '2018-12-31 10:03:34',
                'updated_at' => '2018-12-30 10:03:34',
            ]
        );

        DB::table('auth_types')->insert([
                [
                    'name' => 'logout',
                    'created_at' => '2018-12-30 10:03:34',
                    'updated_at' => '2018-12-30 10:03:34',
                ],[
                    'name' => 'admin',
                    'created_at' => '2018-12-30 10:03:34',
                    'updated_at' => '2018-12-30 10:03:34',
                ]
            ]
        );
    }
}

class AuthTrackingTableSeeder extends Seeder {
    public function run() {
        DB::table('auth_trackings')->delete();
        \App\AuthTracking::create([
                'user_id' => '1',
                'type_id' => '1',
                'ip' => '::1',
                'useragent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:64.0) Gecko/20100101 Firefox/64.0',
                'created_at' => '2018-12-31 13:05:51',
                'updated_at' => '2018-12-31 13:05:51',
            ]
        );

        DB::table('auth_trackings')->insert([
                [
                    'user_id' => '1',
                    'type_id' => '1',
                    'ip' => '192.168.1.128',
                    'useragent' => 'Mozilla/5.0 (Android 9; Mobile; rv:64.0) Gecko/64.0 Firefox/64.0',
                    'created_at' => '2018-12-31 13:06:07',
                    'updated_at' => '2018-12-31 13:06:07',
                ],[
                    'user_id' => '1',
                    'type_id' => '1',
                    'ip' => '192.168.1.134',
                    'useragent' => 'Mozilla/5.0 (Android 7.0; Tablet; rv:64.0) Gecko/64.0 Firefox/64.0',
                    'created_at' => '2018-12-31 13:06:37',
                    'updated_at' => '2018-12-31 13:06:37',
                ],[
                    'user_id' => '1',
                    'type_id' => '2',
                    'ip' => '192.168.1.134',
                    'useragent' => 'Mozilla/5.0 (Android 7.0; Tablet; rv:64.0) Gecko/64.0 Firefox/64.0',
                    'created_at' => '2018-12-31 13:06:46',
                    'updated_at' => '2018-12-31 13:06:46',
                ],[
                    'user_id' => '1',
                    'type_id' => '2',
                    'ip' => '192.168.1.128',
                    'useragent' => 'Mozilla/5.0 (Android 9; Mobile; rv:64.0) Gecko/64.0 Firefox/64.0',
                    'created_at' => '2018-12-31 13:06:51',
                    'updated_at' => '2018-12-31 13:06:51',
                ]
            ]
        );
    }
}

class NetworkTableSeeder extends Seeder {

    public function run() {
        DB::table('networks')->delete();
        \App\Network::create([
            'name' => 'No Network Available',
            'type' => NULL,
            'country_code' => NULL,
            'country_name' => NULL,
            'link' => NULL,
            'banner' => NULL,
            'created_at' => '2018-12-31 13:06:46',
            'updated_at' => '2018-12-31 13:06:46',
        ]);
    }
}

class StatusTableSeeder extends Seeder {

    public function run() {
        DB::table('statuses')->delete();
        \App\Status::create([
            'name' => 'Ended',
            'created_at' => '2019-01-05 23:33:38',
            'updated_at' => '2019-01-05 23:33:38',
        ]);

        DB::table('statuses')->insert([
            [
                'name' => 'In Development',
                'created_at' => '2019-01-05 23:33:38',
                'updated_at' => '2019-01-05 23:33:38',
            ],[
                'name' => 'Running',
                'created_at' => '2019-01-05 23:33:38',
                'updated_at' => '2019-01-05 23:33:38',
            ],[
                'name' => 'To Be Determined',
                'created_at' => '2019-01-05 23:33:38',
                'updated_at' => '2019-01-05 23:33:38',
            ]
        ]);
    }
}

class ContentRatingTableSeeder extends Seeder {

    public function run() {
        DB::table('content_ratings')->delete();
        \App\ContentRating::create([
            'name' => 'No rating',
            'description' => 'There is no rating.',
            'icon' => NULL,
            'created_at' => '2018-12-31 13:06:46',
            'updated_at' => '2018-12-31 13:06:46',
        ]);

        DB::table('content_ratings')->insert([
            [
                'name' => 'TV-Y',
                'description' => 'This program is designed to be appropriate for all children.',
                'icon' => 'TV-Y_icon',
                'created_at' => '2018-12-31 13:06:46',
                'updated_at' => '2018-12-31 13:06:46',
            ],[
                'name' => 'TV-Y7',
                'description' => 'This program is designed for children age 7 and above.',
                'icon' => 'TV-Y7_icon',
                'created_at' => '2018-12-31 13:06:46',
                'updated_at' => '2018-12-31 13:06:46',
            ],[
                'name' => 'TV-G',
                'description' => 'Most parents would find this program suitable for all ages.',
                'icon' => 'TV-G_icon',
                'created_at' => '2018-12-31 13:06:46',
                'updated_at' => '2018-12-31 13:06:46',
            ],[
                'name' => 'TV-PG',
                'description' => 'This program contains material that parents may find unsuitable for younger children.',
                'icon' => 'TV-PG_icon',
                'created_at' => '2018-12-31 13:06:46',
                'updated_at' => '2018-12-31 13:06:46',
            ],[
                'name' => 'TV-14',
                'description' => 'This program contains some material that many parents would find unsuitable for children under 14 years of age.',
                'icon' => 'TV-14_icon',
                'created_at' => '2018-12-31 13:06:46',
                'updated_at' => '2018-12-31 13:06:46',
            ],[
                'name' => 'TV-14',
                'description' => 'This program contains some material that many parents would find unsuitable for children under 14 years of age.',
                'icon' => 'TV-14_icon',
                'created_at' => '2018-12-31 13:06:46',
                'updated_at' => '2018-12-31 13:06:46',
            ],[
                'name' => 'TV-MA',
                'description' => 'This program is specifically designed to be viewed by adults and therefore may be unsuitable for children under 17.',
                'icon' => 'TV-MA_icon',
                'created_at' => '2018-12-31 13:06:46',
                'updated_at' => '2018-12-31 13:06:46',
            ],[
                'name' => 'Not Rated',
                'description' => NULL,
                'icon' => NULL,
                'created_at' => '2018-12-31 13:06:46',
                'updated_at' => '2018-12-31 13:06:46',
            ],[
                'name' => 'VM14',
                'description' => NULL,
                'icon' => NULL,
                'created_at' => '2018-12-31 13:06:46',
                'updated_at' => '2018-12-31 13:06:46',
            ]
        ]);
    }
}

class BookmarkTypeTableSeeder extends Seeder {
    public function run() {
        DB::table('bookmark_types')->delete();
        \App\BookmarkType::create([
                'name' => 'manual',
                'created_at' => '2018-12-31 13:05:51',
                'updated_at' => '2018-12-31 13:05:51',
            ]
        );

        DB::table('bookmark_types')->insert([
                [
                    'name' => 'pause',
                    'created_at' => '2018-12-31 23:11:21',
                    'updated_at' => '2018-12-31 23:11:21',
                ],[
                    'name' => 'soft',
                    'created_at' => '2018-12-31 13:06:07',
                    'updated_at' => '2018-12-31 13:06:07',
                ],[
                    'name' => 'hard',
                    'created_at' => '2018-12-31 13:06:37',
                    'updated_at' => '2018-12-31 13:06:37',
                ],[
                    'name' => 'seek',
                    'created_at' => '2018-12-31 13:06:46',
                    'updated_at' => '2018-12-31 13:06:46',
                ]
            ]
        );
    }
}

class VideoQualitiesTableSeeder extends Seeder {
    public function run() {
        DB::table('video_qualities')->delete();
        \App\VideoQuality::create([
                'name' => '4k',
                'code' => '2160p',
                'priority' => '0',
            ]
        );

        DB::table('video_qualities')->insert([
                [
                    'name' => '2k',
                    'code' => '1440p',
                    'priority' => '1',
                ],[
                    'name' => 'Full HD',
                    'code' => '1080p',
                    'priority' => '2',
                ],[
                    'name' => 'HD',
                    'code' => '720p',
                    'priority' => '3',
                ],[
                    'name' => 'SD',
                    'code' => '480p',
                    'priority' => '4',
                ]
            ]
        );
    }
}
