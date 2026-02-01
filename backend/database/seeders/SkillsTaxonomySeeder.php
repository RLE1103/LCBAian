<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SkillsTaxonomy;

class SkillsTaxonomySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Programming Languages
            ['name' => 'Java', 'category' => 'Programming'],
            ['name' => 'Python', 'category' => 'Programming'],
            ['name' => 'JavaScript', 'category' => 'Programming'],
            ['name' => 'TypeScript', 'category' => 'Programming'],
            ['name' => 'C++', 'category' => 'Programming'],
            ['name' => 'C#', 'category' => 'Programming'],
            ['name' => 'PHP', 'category' => 'Programming'],
            ['name' => 'Ruby', 'category' => 'Programming'],
            ['name' => 'Go', 'category' => 'Programming'],
            ['name' => 'Swift', 'category' => 'Programming'],
            ['name' => 'Kotlin', 'category' => 'Programming'],
            ['name' => 'Rust', 'category' => 'Programming'],
            
            // Web Frameworks
            ['name' => 'React', 'category' => 'Frontend'],
            ['name' => 'Vue.js', 'category' => 'Frontend'],
            ['name' => 'Angular', 'category' => 'Frontend'],
            ['name' => 'Laravel', 'category' => 'Backend'],
            ['name' => 'Django', 'category' => 'Backend'],
            ['name' => 'Spring Boot', 'category' => 'Backend'],
            ['name' => 'Express.js', 'category' => 'Backend'],
            ['name' => 'Flask', 'category' => 'Backend'],
            ['name' => 'Ruby on Rails', 'category' => 'Backend'],
            ['name' => 'ASP.NET', 'category' => 'Backend'],
            ['name' => 'Next.js', 'category' => 'Frontend'],
            ['name' => 'Nuxt.js', 'category' => 'Frontend'],
            
            // Databases
            ['name' => 'MySQL', 'category' => 'Database'],
            ['name' => 'PostgreSQL', 'category' => 'Database'],
            ['name' => 'MongoDB', 'category' => 'Database'],
            ['name' => 'Redis', 'category' => 'Database'],
            ['name' => 'SQLite', 'category' => 'Database'],
            ['name' => 'Oracle', 'category' => 'Database'],
            ['name' => 'SQL Server', 'category' => 'Database'],
            
            // Tools & Technologies
            ['name' => 'Git', 'category' => 'Tools'],
            ['name' => 'Docker', 'category' => 'Tools'],
            ['name' => 'Kubernetes', 'category' => 'Tools'],
            ['name' => 'AWS', 'category' => 'Cloud'],
            ['name' => 'Azure', 'category' => 'Cloud'],
            ['name' => 'GCP', 'category' => 'Cloud'],
            ['name' => 'Linux', 'category' => 'Operating Systems'],
            ['name' => 'Windows', 'category' => 'Operating Systems'],
            ['name' => 'macOS', 'category' => 'Operating Systems'],
            
            // Mobile Development
            ['name' => 'React Native', 'category' => 'Mobile'],
            ['name' => 'Flutter', 'category' => 'Mobile'],
            ['name' => 'Ionic', 'category' => 'Mobile'],
            ['name' => 'Xamarin', 'category' => 'Mobile'],
            
            // Design & UI/UX
            ['name' => 'Figma', 'category' => 'Design'],
            ['name' => 'Adobe XD', 'category' => 'Design'],
            ['name' => 'Photoshop', 'category' => 'Design'],
            ['name' => 'Illustrator', 'category' => 'Design'],
            ['name' => 'UI/UX Design', 'category' => 'Design'],
            
            // Data Science & Analytics
            ['name' => 'Data Analysis', 'category' => 'Data Science'],
            ['name' => 'Machine Learning', 'category' => 'Data Science'],
            ['name' => 'R', 'category' => 'Data Science'],
            ['name' => 'TensorFlow', 'category' => 'Data Science'],
            ['name' => 'Pandas', 'category' => 'Data Science'],
            ['name' => 'NumPy', 'category' => 'Data Science'],
            
            // Other Common Skills
            ['name' => 'Project Management', 'category' => 'Management'],
            ['name' => 'Agile', 'category' => 'Methodology'],
            ['name' => 'Scrum', 'category' => 'Methodology'],
            ['name' => 'RESTful API', 'category' => 'Backend'],
            ['name' => 'GraphQL', 'category' => 'Backend'],
            ['name' => 'Microservices', 'category' => 'Architecture'],
            ['name' => 'DevOps', 'category' => 'Operations'],
            ['name' => 'CI/CD', 'category' => 'Operations'],
            ['name' => 'HTML', 'category' => 'Frontend'],
            ['name' => 'CSS', 'category' => 'Frontend'],
            ['name' => 'SASS', 'category' => 'Frontend'],
            ['name' => 'Bootstrap', 'category' => 'Frontend'],
            ['name' => 'Tailwind CSS', 'category' => 'Frontend'],
        ];

        foreach ($skills as $skill) {
            SkillsTaxonomy::create($skill);
        }
    }
}
