<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Programmierung',
                'slug' => 'programmierung',
                'description' => 'Lernpfade rund um Softwareentwicklung und Programmierung',
                'children' => [
                    ['name' => 'Webentwicklung', 'slug' => 'webentwicklung', 'description' => 'Frontend und Backend Webentwicklung'],
                    ['name' => 'Mobile Apps', 'slug' => 'mobile-apps', 'description' => 'iOS und Android App-Entwicklung'],
                    ['name' => 'Python', 'slug' => 'python', 'description' => 'Python Programmierung und Scripting'],
                    ['name' => 'JavaScript', 'slug' => 'javascript', 'description' => 'JavaScript und TypeScript'],
                ],
            ],
            [
                'name' => 'Datenanalyse',
                'slug' => 'datenanalyse',
                'description' => 'Datenverarbeitung, Analyse und Visualisierung',
                'children' => [
                    ['name' => 'Data Science', 'slug' => 'data-science', 'description' => 'Wissenschaftliche Datenanalyse'],
                    ['name' => 'Machine Learning', 'slug' => 'machine-learning', 'description' => 'Maschinelles Lernen und KI'],
                    ['name' => 'Business Intelligence', 'slug' => 'business-intelligence', 'description' => 'BI und Reporting'],
                ],
            ],
            [
                'name' => 'Design',
                'slug' => 'design',
                'description' => 'Grafik- und UX/UI Design',
                'children' => [
                    ['name' => 'UX Design', 'slug' => 'ux-design', 'description' => 'User Experience Design'],
                    ['name' => 'UI Design', 'slug' => 'ui-design', 'description' => 'User Interface Design'],
                    ['name' => 'Grafikdesign', 'slug' => 'grafikdesign', 'description' => 'Visuelle Gestaltung'],
                ],
            ],
            [
                'name' => 'Sprachen',
                'slug' => 'sprachen',
                'description' => 'Fremdsprachen lernen',
                'children' => [
                    ['name' => 'Englisch', 'slug' => 'englisch', 'description' => 'Business und technisches Englisch'],
                    ['name' => 'Französisch', 'slug' => 'franzoesisch', 'description' => 'Französisch für Anfänger und Fortgeschrittene'],
                    ['name' => 'Deutsch', 'slug' => 'deutsch', 'description' => 'Deutsch als Fremdsprache'],
                ],
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Betriebswirtschaft und Management',
                'children' => [
                    ['name' => 'Projektmanagement', 'slug' => 'projektmanagement', 'description' => 'Agile und klassische Projektmethoden'],
                    ['name' => 'Marketing', 'slug' => 'marketing', 'description' => 'Digital Marketing und Strategie'],
                    ['name' => 'Führung', 'slug' => 'fuehrung', 'description' => 'Leadership und Teamführung'],
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $parent = Category::create($categoryData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                Category::create($childData);
            }
        }

        $this->command->info('Categories seeded successfully!');
    }
}
