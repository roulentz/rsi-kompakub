<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'UI/UX Design',
            'Graphic Design',
            'Web Development',
            'Mobile Development',
            'Frontend Development',
            'Backend Development',
            'Full Stack Development',
            'Data Analysis',
            'Data Science',
            'Machine Learning',
            'Artificial Intelligence',
            'Cyber Security',
            'Cloud Computing',
            'Database Management',
            'Project Management',
            'Business Analysis',
            'Digital Marketing',
            'Content Writing',
            'Copywriting',
            'Video Editing',
            'Photography',
            'Public Speaking',
            'Research',
            'Communication',
            'Leadership',
            'Teamwork',
            'Problem Solving',
        ];

        foreach ($skills as $skill) {
            DB::table('skills')->updateOrInsert(
                ['nama_skill' => $skill],
                ['nama_skill' => $skill]
            );
        }
    }
}
