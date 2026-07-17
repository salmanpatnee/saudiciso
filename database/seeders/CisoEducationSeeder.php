<?php

namespace Database\Seeders;

use App\Models\CisoEducation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CisoEducationSeeder extends Seeder
{
    /**
     * @var array<int, array{title: string, slug: string, image: string}>
     */
    private array $entries = [
        [
            'title' => 'Applying CISSP Knowledge in Practice',
            'slug' => 'applying-cissp-knowledge-in-practice',
            'image' => 'CISSPLogo.png',
        ],
        [
            'title' => 'Applying CISM Knowledge in Practice',
            'slug' => 'applying-cism-knowledge-in-practice',
            'image' => 'CISMLogo.png',
        ],
        [
            'title' => 'Applying CGEIT Knowledge in Practice',
            'slug' => 'applying-cgeit-knowledge-in-practice',
            'image' => 'CGEITLogo.png',
        ],
        [
            'title' => 'Applying PMP Knowledge in Practice',
            'slug' => 'applying-pmp-knowledge-in-practice',
            'image' => 'PMPLogo.png',
        ],
        [
            'title' => 'Applying Agile Approach to Your Department',
            'slug' => 'applying-agile-approach',
            'image' => 'AgileLogo.png',
        ],
    ];

    public function run(): void
    {
        foreach ($this->entries as $entry) {
            $featuredImagePath = $this->storeImage($entry['image']);
            $body = file_get_contents(database_path("seeders/ciso-education-content/{$entry['slug']}.html"));

            CisoEducation::updateOrCreate(
                ['slug' => $entry['slug']],
                [
                    'title' => $entry['title'],
                    'body' => $body,
                    'featured_image_path' => $featuredImagePath,
                ]
            );
        }
    }

    private function storeImage(string $filename): ?string
    {
        $sourcePath = public_path("Images/{$filename}");

        if (! file_exists($sourcePath)) {
            return null;
        }

        $destinationPath = "ciso-education/images/{$filename}";
        Storage::disk('public')->put($destinationPath, file_get_contents($sourcePath));

        return $destinationPath;
    }
}
