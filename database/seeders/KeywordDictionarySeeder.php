<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KeywordDictionarySeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            ['keyword' => 'dogum gunu', 'event_type' => 'birthday'],
            ['keyword' => 'dogum gunun', 'event_type' => 'birthday'],
            ['keyword' => 'yas gunu', 'event_type' => 'birthday'],
            ['keyword' => 'yildonumu', 'event_type' => 'anniversary'],
            ['keyword' => 'evlilik yildonumu', 'event_type' => 'anniversary'],
            ['keyword' => 'nikah', 'event_type' => 'anniversary'],
            ['keyword' => 'sevgililer gunu', 'event_type' => 'valentines'],
            ['keyword' => 'askim', 'event_type' => 'valentines'],
            ['keyword' => 'sevgilim', 'event_type' => 'valentines'],
            ['keyword' => 'anneler gunu', 'event_type' => 'mothers_day'],
            ['keyword' => 'annecigim', 'event_type' => 'mothers_day'],
            ['keyword' => 'canim annem', 'event_type' => 'mothers_day'],
        ];

        foreach ($entries as $entry) {
            DB::table('keyword_dictionary')->updateOrInsert(
                ['keyword' => $entry['keyword'], 'event_type' => $entry['event_type']],
                ['is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
