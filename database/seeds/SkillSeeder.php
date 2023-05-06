<?php

use App\Skill;
use App\Category;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skills = [
            [
                'name' => 'Construction and Building Trades',
                'children' => [
                    'Building codes knowledge',
                    'Tool familiarity',
                    'Attention to detail',
                    'Spatial awareness',
                    'Physical stamina',
                    'Communication',
                    'Teamwork'
                ],
            ],
            [
                'name' => 'Manufacturing and Production',
                'children' => [
                    'Understanding of processes',
                    'Attention to detail',
                    'Quality control',
                    'Technical reading',
                    'Hand-eye coordination',
                    'Physical stamina',
                    'Problem-solving',
                    'Troubleshooting'
                ],
            ],
            [
                'name' => 'Transportation and Warehousing',
                'children' => [
                    'Driving proficiency',
                    'Vehicle operation',
                    'GPS navigation',
                    'Math skills',
                    'Inventory management',
                ],
            ],
            [
                'name' => 'Mechanics and Repair Technicians',
                'children' => [
                    'Mechanical systems knowledge',
                    'Tool proficiency',
                    'Attention to detail',
                    'Quality control',
                    'Diagnostic software proficiency',
                    'Communication',
                    'Customer service'
                ],
            ],
            [
                'name' => 'Agriculture, Forestry, and Fishing',
                'children' => [
                    'Techniques & equipment knowledge',
                    'Machinery operation',
                    'Physical stamina',
                    'Attention to detail',
                    'Quality control',
                    'Math/budgeting skills',
                ],
            ],
        ];


        foreach ($skills as $skill) {
            $parentSkill = Skill::updateOrCreate([
                'name' => $skill['name'],
            ]);

            foreach ($skill['children'] as $childSkill) {
                Skill::updateOrCreate([
                    'name' => $childSkill,
                    'parent_id' => $parentSkill->id,
                ]);
            }
        }
    }
}
