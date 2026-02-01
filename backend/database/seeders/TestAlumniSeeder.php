<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestAlumniSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            'BS Computer Science',
            'BS Computer Engineering',
            'AB Political Science',
            'Bachelor of Elementary Education',
            'BS Psychology',
            'BS Accountancy',
            'BS Business Administration (Marketing Management)',
            'BS Business Administration (Human Resources Management)',
        ];

        $suffixes = ['', 'Jr.', 'Sr.', 'II', 'III'];
        
        $techSkills = [
            'React', 'Vue.js', 'Angular', 'JavaScript', 'TypeScript', 'HTML/CSS',
            'Node.js', 'PHP', 'Laravel', 'Python', 'Java', 'C#', 'C++',
            'MySQL', 'PostgreSQL', 'MongoDB', 'Redis',
            'Docker', 'AWS', 'Git', 'CI/CD', 'Kubernetes',
            'Problem Solving', 'Team Collaboration', 'Agile', 'Scrum'
        ];
        
        $businessSkills = ['Marketing', 'Sales', 'Customer Service', 'Project Management', 'Communication', 'Leadership'];
        $educationSkills = ['Curriculum Development', 'Classroom Management', 'Student Assessment', 'Teaching'];
        $accountingSkills = ['Financial Analysis', 'Auditing', 'Tax Preparation', 'Bookkeeping', 'QuickBooks'];
        $psySkills = ['Counseling', 'Research', 'Data Analysis', 'Communication', 'Empathy'];

        $industries = ['Technology', 'Education', 'Finance', 'Healthcare', 'Business Services', 'Government'];
        $employmentStatuses = ['employed_full_time', 'employed_part_time', 'self_employed', 'unemployed_looking'];
        $yearsExp = ['0-2', '3-5', '6-10'];
        $salaryRanges = ['20000-39999', '40000-59999', '60000-79999', '80000-99999'];
        $cities = ['Manila', 'Quezon City', 'Makati', 'Taguig', 'Pasig', 'Cebu City', 'Davao City'];

        $alumni = [
            // 10 Computer Science alumni
            [
                'first_name' => 'Juan', 'middle_name' => 'Cruz', 'last_name' => 'Dela Cruz', 'suffix' => '',
                'email' => 'juan.delacruz@test.com', 'program' => 'BS Computer Science', 'batch' => '2020',
                'skills' => ['React', 'Node.js', 'JavaScript', 'MySQL', 'Git', 'Problem Solving'],
                'current_job_title' => 'Frontend Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Maria', 'middle_name' => 'Santos', 'last_name' => 'Garcia', 'suffix' => '',
                'email' => 'maria.garcia@test.com', 'program' => 'BS Computer Science', 'batch' => '2019',
                'skills' => ['Vue.js', 'PHP', 'Laravel', 'PostgreSQL', 'Docker', 'Team Collaboration'],
                'current_job_title' => 'Full Stack Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Pedro', 'middle_name' => 'Ramos', 'last_name' => 'Reyes', 'suffix' => 'Jr.',
                'email' => 'pedro.reyes@test.com', 'program' => 'BS Computer Science', 'batch' => '2021',
                'skills' => ['Python', 'Django', 'MongoDB', 'AWS', 'CI/CD', 'Agile'],
                'current_job_title' => 'Backend Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Ana', 'middle_name' => 'Lopez', 'last_name' => 'Bautista', 'suffix' => '',
                'email' => 'ana.bautista@test.com', 'program' => 'BS Computer Science', 'batch' => '2018',
                'skills' => ['React', 'TypeScript', 'Node.js', 'PostgreSQL', 'Kubernetes', 'Scrum'],
                'current_job_title' => 'Senior Frontend Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Carlos', 'middle_name' => 'Miguel', 'last_name' => 'Fernandez', 'suffix' => '',
                'email' => 'carlos.fernandez@test.com', 'program' => 'BS Computer Science', 'batch' => '2020',
                'skills' => ['JavaScript', 'Vue.js', 'PHP', 'MySQL', 'Git', 'Problem Solving'],
                'current_job_title' => 'Web Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Sofia', 'middle_name' => 'Rose', 'last_name' => 'Villanueva', 'suffix' => '',
                'email' => 'sofia.villanueva@test.com', 'program' => 'BS Computer Science', 'batch' => '2019',
                'skills' => ['Angular', 'TypeScript', 'Java', 'MongoDB', 'Docker', 'Team Collaboration'],
                'current_job_title' => 'Frontend Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Miguel', 'middle_name' => 'Angelo', 'last_name' => 'Torres', 'suffix' => 'III',
                'email' => 'miguel.torres@test.com', 'program' => 'BS Computer Science', 'batch' => '2021',
                'skills' => ['C#', '.NET', 'SQL Server', 'Azure', 'Git', 'Agile'],
                'current_job_title' => '.NET Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Isabella', 'middle_name' => 'Mae', 'last_name' => 'Santos', 'suffix' => '',
                'email' => 'isabella.santos@test.com', 'program' => 'BS Computer Science', 'batch' => '2022',
                'skills' => ['React', 'JavaScript', 'HTML/CSS', 'Git', 'Problem Solving', 'Communication'],
                'current_job_title' => 'Junior Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Diego', 'middle_name' => 'Jose', 'last_name' => 'Mendoza', 'suffix' => '',
                'email' => 'diego.mendoza@test.com', 'program' => 'BS Computer Science', 'batch' => '2018',
                'skills' => ['Java', 'Spring Boot', 'MySQL', 'Redis', 'Kubernetes', 'Leadership'],
                'current_job_title' => 'Senior Backend Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Lucia', 'middle_name' => 'Grace', 'last_name' => 'Ramirez', 'suffix' => '',
                'email' => 'lucia.ramirez@test.com', 'program' => 'BS Computer Science', 'batch' => '2020',
                'skills' => ['Python', 'Flask', 'PostgreSQL', 'Docker', 'CI/CD', 'Team Collaboration'],
                'current_job_title' => 'Software Engineer', 'industry' => 'Technology',
            ],
            
            // 5 Computer Engineering alumni
            [
                'first_name' => 'Rafael', 'middle_name' => 'Luis', 'last_name' => 'Cruz', 'suffix' => '',
                'email' => 'rafael.cruz@test.com', 'program' => 'BS Computer Engineering', 'batch' => '2019',
                'skills' => ['C++', 'Python', 'Embedded Systems', 'IoT', 'Linux', 'Problem Solving'],
                'current_job_title' => 'Embedded Systems Engineer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Gabriela', 'middle_name' => 'Sofia', 'last_name' => 'Morales', 'suffix' => '',
                'email' => 'gabriela.morales@test.com', 'program' => 'BS Computer Engineering', 'batch' => '2020',
                'skills' => ['Java', 'Python', 'MySQL', 'AWS', 'Git', 'Agile'],
                'current_job_title' => 'Software Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Antonio', 'middle_name' => 'Carlos', 'last_name' => 'Navarro', 'suffix' => 'Sr.',
                'email' => 'antonio.navarro@test.com', 'program' => 'BS Computer Engineering', 'batch' => '2018',
                'skills' => ['C++', 'Java', 'FPGA', 'Hardware Design', 'Debugging', 'Team Collaboration'],
                'current_job_title' => 'Hardware Engineer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Valentina', 'middle_name' => 'Marie', 'last_name' => 'Lopez', 'suffix' => '',
                'email' => 'valentina.lopez@test.com', 'program' => 'BS Computer Engineering', 'batch' => '2021',
                'skills' => ['Python', 'JavaScript', 'Node.js', 'React', 'Git', 'Problem Solving'],
                'current_job_title' => 'Full Stack Developer', 'industry' => 'Technology',
            ],
            [
                'first_name' => 'Ricardo', 'middle_name' => 'Manuel', 'last_name' => 'Hernandez', 'suffix' => '',
                'email' => 'ricardo.hernandez@test.com', 'program' => 'BS Computer Engineering', 'batch' => '2019',
                'skills' => ['C', 'C++', 'Assembly', 'RTOS', 'Embedded Systems', 'Debugging'],
                'current_job_title' => 'Firmware Engineer', 'industry' => 'Technology',
            ],
            
            // 10 other programs
            [
                'first_name' => 'Elena', 'middle_name' => 'Joy', 'last_name' => 'Santiago', 'suffix' => '',
                'email' => 'elena.santiago@test.com', 'program' => 'BS Accountancy', 'batch' => '2019',
                'skills' => ['Financial Analysis', 'Auditing', 'Tax Preparation', 'QuickBooks', 'Excel'],
                'current_job_title' => 'Staff Accountant', 'industry' => 'Finance',
            ],
            [
                'first_name' => 'Fernando', 'middle_name' => 'Gabriel', 'last_name' => 'Ramos', 'suffix' => '',
                'email' => 'fernando.ramos@test.com', 'program' => 'BS Business Administration (Marketing Management)', 'batch' => '2020',
                'skills' => ['Marketing', 'Social Media', 'Content Creation', 'SEO', 'Communication'],
                'current_job_title' => 'Marketing Specialist', 'industry' => 'Business Services',
            ],
            [
                'first_name' => 'Carmen', 'middle_name' => 'Lisa', 'last_name' => 'Flores', 'suffix' => '',
                'email' => 'carmen.flores@test.com', 'program' => 'Bachelor of Elementary Education', 'batch' => '2018',
                'skills' => ['Teaching', 'Curriculum Development', 'Classroom Management', 'Communication'],
                'current_job_title' => 'Elementary Teacher', 'industry' => 'Education',
            ],
            [
                'first_name' => 'Roberto', 'middle_name' => 'Luis', 'last_name' => 'Gutierrez', 'suffix' => 'Jr.',
                'email' => 'roberto.gutierrez@test.com', 'program' => 'AB Political Science', 'batch' => '2019',
                'skills' => ['Research', 'Policy Analysis', 'Communication', 'Public Speaking', 'Writing'],
                'current_job_title' => 'Policy Analyst', 'industry' => 'Government',
            ],
            [
                'first_name' => 'Patricia', 'middle_name' => 'Anne', 'last_name' => 'Rivera', 'suffix' => '',
                'email' => 'patricia.rivera@test.com', 'program' => 'BS Psychology', 'batch' => '2020',
                'skills' => ['Counseling', 'Research', 'Data Analysis', 'Empathy', 'Communication'],
                'current_job_title' => 'HR Specialist', 'industry' => 'Business Services',
            ],
            [
                'first_name' => 'Andres', 'middle_name' => 'Miguel', 'last_name' => 'Castro', 'suffix' => '',
                'email' => 'andres.castro@test.com', 'program' => 'BS Business Administration (Human Resources Management)', 'batch' => '2019',
                'skills' => ['Recruitment', 'Employee Relations', 'Training', 'Leadership', 'Communication'],
                'current_job_title' => 'HR Manager', 'industry' => 'Business Services',
            ],
            [
                'first_name' => 'Monica', 'middle_name' => 'Rose', 'last_name' => 'Ortega', 'suffix' => '',
                'email' => 'monica.ortega@test.com', 'program' => 'BS Accountancy', 'batch' => '2021',
                'skills' => ['Bookkeeping', 'Tax Preparation', 'Financial Reporting', 'Excel', 'Attention to Detail'],
                'current_job_title' => 'Junior Accountant', 'industry' => 'Finance',
            ],
            [
                'first_name' => 'Eduardo', 'middle_name' => 'Jose', 'last_name' => 'Vargas', 'suffix' => '',
                'email' => 'eduardo.vargas@test.com', 'program' => 'Bachelor of Elementary Education', 'batch' => '2020',
                'skills' => ['Teaching', 'Student Assessment', 'Lesson Planning', 'Patience', 'Communication'],
                'current_job_title' => 'Elementary Teacher', 'industry' => 'Education',
            ],
            [
                'first_name' => 'Natalia', 'middle_name' => 'Grace', 'last_name' => 'Medina', 'suffix' => '',
                'email' => 'natalia.medina@test.com', 'program' => 'BS Psychology', 'batch' => '2018',
                'skills' => ['Counseling', 'Assessment', 'Intervention', 'Communication', 'Empathy'],
                'current_job_title' => 'Clinical Psychologist', 'industry' => 'Healthcare',
            ],
            [
                'first_name' => 'Javier', 'middle_name' => 'Antonio', 'last_name' => 'Jimenez', 'suffix' => '',
                'email' => 'javier.jimenez@test.com', 'program' => 'BS Business Administration (Marketing Management)', 'batch' => '2019',
                'skills' => ['Sales', 'Customer Service', 'Negotiation', 'Communication', 'Project Management'],
                'current_job_title' => 'Sales Manager', 'industry' => 'Business Services',
            ],
        ];

        foreach ($alumni as $data) {
            $yearsExpIndex = array_rand($yearsExp);
            $statusIndex = array_rand($employmentStatuses);
            $cityIndex = array_rand($cities);
            $salaryIndex = array_rand($salaryRanges);

            User::create([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? '',
                'last_name' => $data['last_name'],
                'suffix' => $data['suffix'] ?? '',
                'email' => $data['email'],
                'password' => Hash::make('password123'),
                'role' => 'alumni',
                'program' => $data['program'],
                'batch' => $data['batch'],
                'skills' => $data['skills'],
                'current_job_title' => $data['current_job_title'],
                'industry' => $data['industry'],
                'employment_status' => $employmentStatuses[$statusIndex],
                'years_of_experience' => $yearsExp[$yearsExpIndex],
                'salary_range' => $salaryRanges[$salaryIndex],
                'city' => $cities[$cityIndex],
                'country' => 'Philippines',
                'headline' => $data['current_job_title'] . ' | ' . $data['program'],
            ]);
        }

        $this->command->info('Created 25 alumni users successfully!');
    }
}
