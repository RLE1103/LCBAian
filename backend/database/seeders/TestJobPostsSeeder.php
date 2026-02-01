<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JobPost;
use App\Models\User;

class TestJobPostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get an admin user to post jobs
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->command->warn('No admin user found. Creating one...');
            $admin = User::create([
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@lcba.edu.ph',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]);
        }

        $jobs = [
            // 5 CS/CE Jobs
            [
                'title' => 'Frontend Developer (React)',
                'company_name' => 'TechStartup Philippines',
                'description' => 'We are looking for a talented Frontend Developer with strong React skills to join our growing team. You will work on building modern web applications using the latest technologies.',
                'required_skills' => ['React', 'JavaScript', 'HTML/CSS', 'Git'],
                'preferred_skills' => ['TypeScript', 'Redux', 'Tailwind CSS'],
                'work_type' => 'remote',
                'employment_type' => 'full_time',
                'location' => 'Makati City, Philippines',
                'salary_range' => '40000-60000',
            ],
            [
                'title' => 'Frontend Developer (Vue.js)',
                'company_name' => 'Digital Solutions Inc.',
                'description' => 'Join our team as a Vue.js Frontend Developer. Build responsive and dynamic user interfaces for enterprise applications.',
                'required_skills' => ['Vue.js', 'JavaScript', 'HTML/CSS'],
                'preferred_skills' => ['Vuex', 'Nuxt.js', 'TypeScript'],
                'work_type' => 'hybrid',
                'employment_type' => 'full_time',
                'location' => 'Taguig City, Philippines',
                'salary_range' => '45000-65000',
            ],
            [
                'title' => 'Backend Developer (Laravel/PHP)',
                'company_name' => 'WebDev Corp',
                'description' => 'We need an experienced Backend Developer proficient in Laravel and PHP. You will be responsible for developing and maintaining our RESTful APIs and database systems.',
                'required_skills' => ['PHP', 'Laravel', 'MySQL', 'Git'],
                'preferred_skills' => ['Docker', 'Redis', 'AWS'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Quezon City, Philippines',
                'salary_range' => '50000-70000',
            ],
            [
                'title' => 'Backend Developer (Node.js)',
                'company_name' => 'CloudTech Solutions',
                'description' => 'Looking for a Backend Developer with Node.js expertise. Build scalable APIs and microservices for our cloud-based platform.',
                'required_skills' => ['Node.js', 'JavaScript', 'MongoDB', 'Git'],
                'preferred_skills' => ['Express.js', 'Docker', 'AWS', 'Kubernetes'],
                'work_type' => 'remote',
                'employment_type' => 'full_time',
                'location' => 'Manila, Philippines',
                'salary_range' => '55000-75000',
            ],
            [
                'title' => 'Full Stack Developer',
                'company_name' => 'Innovation Labs PH',
                'description' => 'We are seeking a Full Stack Developer who can work on both frontend and backend. Must have experience with modern JavaScript frameworks and server-side technologies.',
                'required_skills' => ['React', 'Node.js', 'PostgreSQL', 'Git'],
                'preferred_skills' => ['TypeScript', 'Docker', 'AWS', 'CI/CD'],
                'work_type' => 'hybrid',
                'employment_type' => 'full_time',
                'location' => 'Pasig City, Philippines',
                'salary_range' => '60000-80000',
            ],
            
            // 3 Education Jobs
            [
                'title' => 'Elementary School Teacher',
                'company_name' => 'Bright Future Academy',
                'description' => 'We are hiring passionate Elementary School Teachers who love working with children. Experience in curriculum development is a plus.',
                'required_skills' => ['Teaching', 'Classroom Management', 'Lesson Planning'],
                'preferred_skills' => ['Curriculum Development', 'Student Assessment'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Quezon City, Philippines',
                'salary_range' => '25000-35000',
            ],
            [
                'title' => 'High School Teacher (English)',
                'company_name' => 'St. Marys College',
                'description' => 'Looking for a qualified High School English Teacher. Must have a passion for literature and excellent communication skills.',
                'required_skills' => ['Teaching', 'English', 'Communication'],
                'preferred_skills' => ['Literature', 'Creative Writing', 'Public Speaking'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Manila, Philippines',
                'salary_range' => '30000-40000',
            ],
            [
                'title' => 'Academic Coordinator',
                'company_name' => 'Learning Center Philippines',
                'description' => 'We need an Academic Coordinator to oversee curriculum implementation and teacher training programs.',
                'required_skills' => ['Curriculum Development', 'Training', 'Leadership'],
                'preferred_skills' => ['Project Management', 'Communication'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Makati City, Philippines',
                'salary_range' => '40000-55000',
            ],
            
            // 3 Business/Marketing Jobs
            [
                'title' => 'Marketing Manager',
                'company_name' => 'BrandMax Marketing',
                'description' => 'Lead our marketing team in developing and executing marketing strategies. Must have experience in digital marketing and brand management.',
                'required_skills' => ['Marketing', 'Social Media', 'Project Management'],
                'preferred_skills' => ['SEO', 'Content Marketing', 'Analytics'],
                'work_type' => 'hybrid',
                'employment_type' => 'full_time',
                'location' => 'Taguig City, Philippines',
                'salary_range' => '45000-60000',
            ],
            [
                'title' => 'Sales Executive',
                'company_name' => 'Elite Sales Corp',
                'description' => 'Join our sales team and help us expand our client base. Experience in B2B sales is preferred.',
                'required_skills' => ['Sales', 'Communication', 'Negotiation'],
                'preferred_skills' => ['Customer Service', 'CRM'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Makati City, Philippines',
                'salary_range' => '35000-50000',
            ],
            [
                'title' => 'Digital Marketing Specialist',
                'company_name' => 'Social Media Agency PH',
                'description' => 'We are looking for a Digital Marketing Specialist to manage social media campaigns and content creation.',
                'required_skills' => ['Social Media', 'Content Creation', 'Marketing'],
                'preferred_skills' => ['SEO', 'Analytics', 'Copywriting'],
                'work_type' => 'remote',
                'employment_type' => 'full_time',
                'location' => 'Manila, Philippines',
                'salary_range' => '30000-45000',
            ],
            
            // 2 Accountancy Jobs
            [
                'title' => 'Staff Accountant',
                'company_name' => 'Financial Services Group',
                'description' => 'We need a detail-oriented Staff Accountant to handle bookkeeping and financial reporting.',
                'required_skills' => ['Bookkeeping', 'Financial Reporting', 'Excel'],
                'preferred_skills' => ['Tax Preparation', 'QuickBooks'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Makati City, Philippines',
                'salary_range' => '30000-40000',
            ],
            [
                'title' => 'Senior Accountant',
                'company_name' => 'Audit & Tax Associates',
                'description' => 'Looking for a Senior Accountant with auditing experience. CPA license is required.',
                'required_skills' => ['Auditing', 'Financial Analysis', 'Tax Preparation'],
                'preferred_skills' => ['Financial Planning', 'Team Leadership'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Manila, Philippines',
                'salary_range' => '50000-70000',
            ],
            
            // 2 Psychology Jobs
            [
                'title' => 'HR Specialist',
                'company_name' => 'People Plus Inc.',
                'description' => 'Join our HR team as an HR Specialist. Psychology background preferred for employee relations and recruitment.',
                'required_skills' => ['Recruitment', 'Employee Relations', 'Communication'],
                'preferred_skills' => ['Counseling', 'Training', 'Data Analysis'],
                'work_type' => 'hybrid',
                'employment_type' => 'full_time',
                'location' => 'Taguig City, Philippines',
                'salary_range' => '35000-50000',
            ],
            [
                'title' => 'Clinical Psychologist',
                'company_name' => 'Mental Health Clinic PH',
                'description' => 'We are hiring a Clinical Psychologist to provide counseling and assessment services.',
                'required_skills' => ['Counseling', 'Assessment', 'Intervention'],
                'preferred_skills' => ['Research', 'Group Therapy', 'Report Writing'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Quezon City, Philippines',
                'salary_range' => '40000-60000',
            ],
            
            // 5 General/Mixed Jobs
            [
                'title' => 'Project Coordinator',
                'company_name' => 'Global Projects Inc.',
                'description' => 'We need a Project Coordinator to assist in planning and executing various projects across departments.',
                'required_skills' => ['Project Management', 'Communication', 'Organization'],
                'preferred_skills' => ['MS Office', 'Problem Solving', 'Team Collaboration'],
                'work_type' => 'hybrid',
                'employment_type' => 'full_time',
                'location' => 'Makati City, Philippines',
                'salary_range' => '35000-45000',
            ],
            [
                'title' => 'Executive Assistant',
                'company_name' => 'Corporate Services Co.',
                'description' => 'Looking for an Executive Assistant to support our senior management team.',
                'required_skills' => ['Administrative Support', 'Communication', 'MS Office'],
                'preferred_skills' => ['Scheduling', 'Report Preparation', 'Attention to Detail'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Taguig City, Philippines',
                'salary_range' => '28000-38000',
            ],
            [
                'title' => 'Research Analyst',
                'company_name' => 'Policy Think Tank PH',
                'description' => 'Join our research team as a Research Analyst. Conduct policy research and analysis.',
                'required_skills' => ['Research', 'Data Analysis', 'Writing'],
                'preferred_skills' => ['Policy Analysis', 'Statistics', 'Communication'],
                'work_type' => 'hybrid',
                'employment_type' => 'full_time',
                'location' => 'Manila, Philippines',
                'salary_range' => '35000-50000',
            ],
            [
                'title' => 'Customer Service Representative',
                'company_name' => 'BPO Solutions PH',
                'description' => 'We are hiring Customer Service Representatives for our growing BPO operations.',
                'required_skills' => ['Customer Service', 'Communication', 'Problem Solving'],
                'preferred_skills' => ['Call Handling', 'Data Entry', 'Patience'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Quezon City, Philippines',
                'salary_range' => '20000-30000',
            ],
            [
                'title' => 'Administrative Officer',
                'company_name' => 'Government Agency',
                'description' => 'Government position for Administrative Officer. Must have good organizational and communication skills.',
                'required_skills' => ['Administrative Support', 'Documentation', 'Communication'],
                'preferred_skills' => ['Government Procedures', 'MS Office', 'Filing Systems'],
                'work_type' => 'onsite',
                'employment_type' => 'full_time',
                'location' => 'Manila, Philippines',
                'salary_range' => '25000-35000',
            ],
        ];

        foreach ($jobs as $job) {
            JobPost::create([
                'title' => $job['title'],
                'company_name' => $job['company_name'],
                'description' => $job['description'],
                'required_skills' => $job['required_skills'],
                'preferred_skills' => $job['preferred_skills'],
                'work_type' => $job['work_type'],
                'location' => $job['location'],
                'salary_range' => $job['salary_range'],
                'status' => 'approved',
                'posted_by_admin' => true,
                'user_id' => $admin->id,
            ]);
        }

        $this->command->info('Created 20 job posts successfully!');
    }
}
