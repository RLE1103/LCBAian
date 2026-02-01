<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\JobPost;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UpdateAlumniAndJobsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing alumni with proper skills and profile data
        $alumniUsers = User::where('role', 'alumni')->get();
        
        // Define skill sets for different alumni profiles
        $alumniProfiles = [
            [
                'skills' => ['Vue.js', 'Laravel', 'MySQL', 'JavaScript', 'PHP'],
                'industry' => 'Technology',
                'current_job_title' => 'Full Stack Developer',
                'experience_level' => 'mid',
                'employment_status' => 'employed_full_time',
                'years_of_experience' => '3-5',
                'city' => 'Manila',
                'country' => 'Philippines',
                'salary_range' => '60000-79999'
            ],
            [
                'skills' => ['React', 'Node.js', 'MongoDB', 'Express.js', 'TypeScript'],
                'industry' => 'Technology',
                'current_job_title' => 'Frontend Developer',
                'experience_level' => 'mid',
                'employment_status' => 'employed_full_time',
                'years_of_experience' => '3-5',
                'city' => 'Makati',
                'country' => 'Philippines',
                'salary_range' => '60000-79999'
            ],
            [
                'skills' => ['Java', 'Spring Boot', 'PostgreSQL', 'Docker', 'Kubernetes'],
                'industry' => 'Technology',
                'current_job_title' => 'Backend Developer',
                'experience_level' => 'mid',
                'employment_status' => 'employed_full_time',
                'years_of_experience' => '3-5',
                'city' => 'BGC',
                'country' => 'Philippines',
                'salary_range' => '80000-99999'
            ],
            [
                'skills' => ['Python', 'Django', 'MySQL', 'RESTful API', 'Git'],
                'industry' => 'Technology',
                'current_job_title' => 'Python Developer',
                'experience_level' => 'entry',
                'employment_status' => 'employed_full_time',
                'years_of_experience' => '0-2',
                'city' => 'Quezon City',
                'country' => 'Philippines',
                'salary_range' => '40000-59999'
            ],
            [
                'skills' => ['Vue.js', 'Laravel', 'MySQL', 'JavaScript', 'HTML', 'CSS'],
                'industry' => 'Technology',
                'current_job_title' => 'Junior Developer',
                'experience_level' => 'entry',
                'employment_status' => 'employed_full_time',
                'years_of_experience' => '0-2',
                'city' => 'Manila',
                'country' => 'Philippines',
                'salary_range' => '40000-59999'
            ],
            [
                'skills' => ['React', 'Node.js', 'MongoDB', 'AWS', 'Docker'],
                'industry' => 'Technology',
                'current_job_title' => 'Senior Developer',
                'experience_level' => 'senior',
                'employment_status' => 'employed_full_time',
                'years_of_experience' => '6-10',
                'city' => 'Makati',
                'country' => 'Philippines',
                'salary_range' => '100000+'
            ],
            [
                'skills' => ['Java', 'Spring Boot', 'PostgreSQL', 'Microservices', 'CI/CD'],
                'industry' => 'Technology',
                'current_job_title' => 'Software Engineer',
                'experience_level' => 'mid',
                'employment_status' => 'employed_full_time',
                'years_of_experience' => '3-5',
                'city' => 'BGC',
                'country' => 'Philippines',
                'salary_range' => '80000-99999'
            ],
            [
                'skills' => ['Python', 'Django', 'MySQL', 'Data Analysis', 'Machine Learning'],
                'industry' => 'Data Science',
                'current_job_title' => 'Data Analyst',
                'experience_level' => 'mid',
                'employment_status' => 'employed_full_time',
                'years_of_experience' => '3-5',
                'city' => 'Manila',
                'country' => 'Philippines',
                'salary_range' => '60000-79999'
            ],
        ];

        // Update alumni with profile data
        foreach ($alumniUsers as $index => $alumni) {
            $profileIndex = $index % count($alumniProfiles);
            $profile = $alumniProfiles[$profileIndex];
            
            $alumni->update([
                'skills' => $profile['skills'],
                'industry' => $profile['industry'],
                'current_job_title' => $profile['current_job_title'],
                'experience_level' => $profile['experience_level'],
                'employment_status' => $profile['employment_status'],
                'years_of_experience' => $profile['years_of_experience'],
                'city' => $profile['city'],
                'country' => $profile['country'],
                'salary_range' => $profile['salary_range'],
                'headline' => $profile['current_job_title'] . ' at ' . ($profile['industry'] ?? 'Technology') . ' Company',
                'program' => $alumni->program ?: 'BS Computer Science',
                'batch' => $alumni->batch ?: '2020',
            ]);
        }

        // Create 4 new job posts that will match with alumni
        $newJobs = [
            [
                'title' => 'Full Stack Developer - Vue.js & Laravel',
                'description' => 'We are looking for an experienced Full Stack Developer to join our team. You will work on building modern web applications using Vue.js and Laravel.',
                'company_name' => 'Tech Innovations Inc.',
                'location' => 'Manila, Philippines',
                'work_type' => 'full-time',
                'required_skills' => ['Vue.js', 'Laravel', 'MySQL', 'JavaScript', 'PHP'],
                'preferred_skills' => ['Docker', 'Git', 'RESTful API'],
                'industry' => 'Technology',
                'experience_level' => 'mid',
                'salary_range' => '60000-79999',
                'posted_by_admin' => true,
                'user_id' => 1,
            ],
            [
                'title' => 'Frontend Developer - React & TypeScript',
                'description' => 'Join our frontend team to build beautiful and responsive user interfaces. We use React, TypeScript, and modern CSS frameworks.',
                'company_name' => 'Digital Solutions Co.',
                'location' => 'Makati, Philippines',
                'work_type' => 'full-time',
                'required_skills' => ['React', 'TypeScript', 'JavaScript', 'HTML', 'CSS'],
                'preferred_skills' => ['Node.js', 'MongoDB', 'Tailwind CSS'],
                'industry' => 'Technology',
                'experience_level' => 'mid',
                'salary_range' => '60000-79999',
                'posted_by_admin' => true,
                'user_id' => 1,
            ],
            [
                'title' => 'Backend Developer - Java & Spring Boot',
                'description' => 'We need a skilled Backend Developer to design and implement scalable microservices using Java and Spring Boot.',
                'company_name' => 'Enterprise Systems Ltd.',
                'location' => 'BGC, Philippines',
                'work_type' => 'full-time',
                'required_skills' => ['Java', 'Spring Boot', 'PostgreSQL', 'Docker'],
                'preferred_skills' => ['Kubernetes', 'Microservices', 'AWS'],
                'industry' => 'Technology',
                'experience_level' => 'mid',
                'salary_range' => '80000-99999',
                'posted_by_admin' => true,
                'user_id' => 1,
            ],
            [
                'title' => 'Python Developer - Django',
                'description' => 'Looking for a Python Developer to build robust web applications using Django framework. Entry level position with growth opportunities.',
                'company_name' => 'Startup Ventures',
                'location' => 'Quezon City, Philippines',
                'work_type' => 'full-time',
                'required_skills' => ['Python', 'Django', 'MySQL', 'RESTful API'],
                'preferred_skills' => ['Git', 'Docker', 'Data Analysis'],
                'industry' => 'Technology',
                'experience_level' => 'entry',
                'salary_range' => '40000-59999',
                'posted_by_admin' => true,
                'user_id' => 1,
            ],
        ];

        foreach ($newJobs as $jobData) {
            JobPost::create($jobData);
        }

        $this->command->info('Updated ' . $alumniUsers->count() . ' alumni profiles and created 4 new job posts!');
    }
}
