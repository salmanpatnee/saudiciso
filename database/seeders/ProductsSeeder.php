<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductsSeeder extends Seeder
{
    /**
     * @var array<int, array{title: string, slug: string, image: string}>
     */
    private array $entries = [
        ['title' => 'Anti Phishing Software', 'slug' => 'anti-phishing-software', 'image' => 'Slide1.JPG'],
        ['title' => 'Anti Ransomware Software', 'slug' => 'anti-ransomware-software', 'image' => 'Slide2.jpg'],
        ['title' => 'Application Whitelisting', 'slug' => 'application-whitelisting', 'image' => 'Slide3.JPG'],
        ['title' => 'Backup Recovery', 'slug' => 'backup-recovery', 'image' => 'Slide4.JPG'],
        ['title' => 'Brand Protection', 'slug' => 'brand-protection', 'image' => 'Slide5.JPG'],
        ['title' => 'CASB', 'slug' => 'casb', 'image' => 'Slide6.JPG'],
        ['title' => 'Container and Kubernetes Security', 'slug' => 'container-kubernetes-security', 'image' => 'Slide7.JPG'],
        ['title' => 'Data Classification', 'slug' => 'data-classification', 'image' => 'Slide8.JPG'],
        ['title' => 'Data Loss Prevention', 'slug' => 'data-loss-prevention', 'image' => 'Slide9.JPG'],
        ['title' => 'Database Activity Monitoring', 'slug' => 'database-activity-monitoring', 'image' => 'Slide10.JPG'],
        ['title' => 'Distributed Denial-of-Service (DDoS) Attack', 'slug' => 'distributed-denial-of-service-of-attack', 'image' => 'Slide11.JPG'],
        ['title' => 'Email Security', 'slug' => 'email-security', 'image' => 'Slide12.JPG'],
        ['title' => 'Encryption', 'slug' => 'encryption', 'image' => 'Slide13.JPG'],
        ['title' => 'End-Point Detection and Response', 'slug' => 'end-point-detection-response', 'image' => 'Slide14.JPG'],
        ['title' => 'Extended Detection and Response', 'slug' => 'extended-detection-and-response', 'image' => 'Slide15.JPG'],
        ['title' => 'Identity and Access Management', 'slug' => 'identity-access-management', 'image' => 'Slide16.JPG'],
        ['title' => 'IoT Security', 'slug' => 'iot-security', 'image' => 'Slide17.JPG'],
        ['title' => 'Multi Factor Authentication', 'slug' => 'multi-factor-authentication', 'image' => 'Slide18.JPG'],
        ['title' => 'Network Access Control', 'slug' => 'network-access-control', 'image' => 'Slide19.JPG'],
        ['title' => 'Next Generation Firewall', 'slug' => 'next-generation-firewall', 'image' => 'Slide20.JPG'],
        ['title' => 'Penetration Testing', 'slug' => 'penetration-testing', 'image' => 'Slide21.JPG'],
        ['title' => 'Privileged Access Management', 'slug' => 'privilege-access-management', 'image' => 'Slide22.JPG'],
        ['title' => 'SIEM Solution', 'slug' => 'siem-solution', 'image' => 'Slide23.JPG'],
        ['title' => 'Threat Intelligence', 'slug' => 'threat-intelligence', 'image' => 'Slide24.JPG'],
        ['title' => 'Unified Threat Management', 'slug' => 'unified-threat-management', 'image' => 'Slide25.JPG'],
        ['title' => 'User and Entity Behavior Analytics', 'slug' => 'user-entity-behavior-analytics', 'image' => 'Slide26.JPG'],
        ['title' => 'Web Application Firewall', 'slug' => 'web-application-firewall', 'image' => 'Slide27.JPG'],
        ['title' => 'WiFi Security', 'slug' => 'wifi-security', 'image' => 'Slide28.JPG'],
        ['title' => 'Zero Day Attack Protection', 'slug' => 'zero-day-attack', 'image' => 'Slide29.JPG'],
        ['title' => 'Zero Trust', 'slug' => 'zero-trust', 'image' => 'Slide30.JPG'],
    ];

    public function run(): void
    {
        foreach ($this->entries as $entry) {
            $featuredImagePath = $this->storeImage($entry['image'], $entry['slug']);
            $body = file_get_contents(database_path("seeders/products-content/{$entry['slug']}.html"));

            Product::updateOrCreate(
                ['slug' => $entry['slug']],
                [
                    'title' => $entry['title'],
                    'body' => $body,
                    'featured_image_path' => $featuredImagePath,
                ]
            );
        }
    }

    private function storeImage(string $filename, string $slug): ?string
    {
        $sourcePath = public_path("Images/products/{$filename}");

        if (! file_exists($sourcePath)) {
            return null;
        }

        $destinationPath = "products/images/{$slug}.jpg";
        Storage::disk('public')->put($destinationPath, file_get_contents($sourcePath));

        return $destinationPath;
    }
}
