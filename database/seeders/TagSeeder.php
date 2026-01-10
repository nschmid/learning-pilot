<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Programming languages
            'PHP',
            'JavaScript',
            'Python',
            'TypeScript',
            'Java',
            'Go',
            'Rust',
            'SQL',

            // Frameworks
            'Laravel',
            'Vue.js',
            'React',
            'Angular',
            'Livewire',
            'Alpine.js',
            'Tailwind CSS',
            'Bootstrap',

            // Levels
            'Anfänger',
            'Fortgeschritten',
            'Experte',

            // Topics
            'Webentwicklung',
            'Backend',
            'Frontend',
            'Datenbanken',
            'API',
            'REST',
            'GraphQL',
            'Testing',
            'Security',
            'DevOps',
            'Cloud',
            'Architektur',
            'Clean Code',
            'Design Patterns',
            'Agile',
            'Projektmanagement',

            // Tools
            'Git',
            'Docker',
            'Kubernetes',
            'CI/CD',
            'VS Code',
            'PHPStorm',

            // Industries
            'E-Commerce',
            'FinTech',
            'HealthTech',
            'EdTech',
        ];

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
        }

        $this->command->info('Tags seeded successfully! Created ' . count($tags) . ' tags.');
    }
}
