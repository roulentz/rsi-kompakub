<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Daftar keahlian untuk wizard, Edit Profil, dan Cari Tim.
 * Aman dijalankan berulang (updateOrInsert), tidak menghapus atau mengubah id skill yang sudah ada:
 *   php artisan db:seed --class=SkillSeeder
 * Aturan nama: unik, maksimal 100 karakter, dan JANGAN memakai koma (koma dipakai sebagai pemisah tag_skill).
 */
class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = array_unique(array_merge(...array_values(self::daftar())));

        foreach ($skills as $skill) {
            DB::table('skills')->updateOrInsert(
                ['nama_skill' => $skill],
                ['nama_skill' => $skill]
            );
        }
    }

    /** @return array<string, array<int, string>> */
    public static function daftar(): array
    {
        return [
            // 27 skill awal: urutan dipertahankan supaya id di database lama tidak bergeser.
            'Awal' => [
                'UI/UX Design', 'Graphic Design', 'Web Development', 'Mobile Development', 'Frontend Development',
                'Backend Development', 'Full Stack Development', 'Data Analysis', 'Data Science', 'Machine Learning',
                'Artificial Intelligence', 'Cyber Security', 'Cloud Computing', 'Database Management', 'Project Management',
                'Business Analysis', 'Digital Marketing', 'Content Writing', 'Copywriting', 'Video Editing',
                'Photography', 'Public Speaking', 'Research', 'Communication', 'Leadership', 'Teamwork', 'Problem Solving',
            ],

            'Desain dan Kreatif' => [
                'UX Research', 'Wireframing', 'Prototyping', 'Figma', 'Adobe Photoshop', 'Adobe Illustrator', 'Adobe XD',
                'Adobe After Effects', 'Adobe Premiere Pro', 'Adobe InDesign', 'Canva', 'CorelDRAW', 'Sketch', 'Blender',
                '3D Modeling', '3D Animation', '2D Animation', 'Motion Graphics', 'Illustration', 'Digital Illustration',
                'Branding', 'Brand Identity', 'Logo Design', 'Typography', 'Layout Design', 'Poster Design',
                'Infographic Design', 'Presentation Design', 'Pitch Deck Design', 'Packaging Design', 'Product Design',
                'Industrial Design', 'Interior Design', 'Architecture Design', 'Fashion Design', 'Game Design',
                'Level Design', 'Character Design', 'Storyboarding', 'Videography', 'Video Production', 'Cinematography',
                'Photo Editing', 'Product Photography', 'Event Photography', 'Color Grading', 'Podcast Production',
                'Audio Editing', 'Music Production', 'Sound Design', 'Voice Over', 'Scriptwriting', 'Creative Direction',
                'Art Direction', 'Design Thinking', 'Design System', 'Information Architecture', 'Interaction Design',
                'Visual Design', 'Accessibility Design', 'Live Streaming',
            ],

            'Pemrograman dan Pengembangan' => [
                'HTML', 'CSS', 'JavaScript', 'TypeScript', 'React', 'Vue.js', 'Angular', 'Next.js', 'Nuxt.js', 'Svelte',
                'Tailwind CSS', 'Bootstrap', 'Node.js', 'Express.js', 'Laravel', 'PHP', 'Python', 'Django', 'Flask',
                'FastAPI', 'Java', 'Spring Boot', 'Kotlin', 'Swift', 'Flutter', 'React Native', 'Dart', 'C', 'C++', 'C#',
                '.NET', 'Go', 'Rust', 'Ruby on Rails', 'Android Development', 'iOS Development', 'Progressive Web App',
                'SQL', 'MySQL', 'PostgreSQL', 'MongoDB', 'Firebase', 'Supabase', 'GraphQL', 'REST API', 'API Development',
                'Git', 'GitHub', 'Docker', 'Kubernetes', 'Linux', 'Shell Scripting', 'DevOps', 'CI/CD',
                'Software Engineering', 'Software Testing', 'Quality Assurance', 'Unit Testing', 'Automation Testing',
                'Selenium', 'Game Development', 'Unity', 'Unreal Engine', 'Godot', 'AR/VR Development',
                'Embedded Systems', 'Arduino', 'Raspberry Pi', 'IoT', 'Robotics', 'Microcontroller Programming',
                'Blockchain', 'Smart Contract', 'Web3', 'WordPress', 'Shopify', 'No-Code Development',
                'Low-Code Development', 'System Design', 'Software Architecture', 'Microservices',
                'Data Structures and Algorithms', 'Competitive Programming', 'Object-Oriented Programming',
                'Technical Documentation', 'Chatbot Development', 'Web Scraping', 'ERP Systems', 'Odoo',
            ],

            'Data dan AI' => [
                'Deep Learning', 'Natural Language Processing', 'Computer Vision', 'Generative AI', 'Prompt Engineering',
                'Large Language Models', 'Data Mining', 'Data Visualization', 'Data Engineering', 'Data Cleaning',
                'Big Data', 'Apache Spark', 'ETL', 'Data Warehousing', 'Business Intelligence', 'Tableau', 'Power BI',
                'Looker Studio', 'Microsoft Excel', 'Google Sheets', 'Excel VBA', 'R Programming', 'SPSS', 'Statistics',
                'Statistical Modeling', 'Predictive Modeling', 'Time Series Analysis', 'Recommender Systems',
                'TensorFlow', 'PyTorch', 'Scikit-learn', 'Pandas', 'NumPy', 'A/B Testing', 'Data Storytelling',
                'Database Design', 'Data Governance', 'Reinforcement Learning', 'MLOps', 'Optimization',
                'Operations Research', 'Geographic Information Systems', 'Remote Sensing',
            ],

            'Keamanan dan Infrastruktur' => [
                'Network Security', 'Ethical Hacking', 'Penetration Testing', 'Digital Forensics', 'Cryptography',
                'Information Security', 'Computer Networks', 'Network Administration', 'System Administration', 'AWS',
                'Google Cloud Platform', 'Microsoft Azure', 'Server Management', 'Virtualization', 'IT Support',
                'IT Infrastructure', 'Cisco Networking', 'Mikrotik', 'Cloud Security', 'IT Risk Management',
                'IT Governance', 'IT Audit', 'ITIL',
            ],

            'Bisnis dan Manajemen' => [
                'Product Management', 'Product Strategy', 'Product Discovery', 'Scrum', 'Agile Methodology', 'Kanban',
                'Jira', 'Trello', 'Notion', 'Business Development', 'Business Planning', 'Business Model Canvas',
                'Business Case Development', 'Business Strategy', 'Entrepreneurship', 'Startup Development',
                'Lean Startup', 'Market Research', 'Market Analysis', 'Competitive Analysis', 'Feasibility Study',
                'Financial Modeling', 'Financial Analysis', 'Financial Planning', 'Accounting', 'Bookkeeping',
                'Budgeting', 'Fundraising', 'Investment Analysis', 'Valuation', 'Pricing Strategy', 'Negotiation',
                'Sales', 'B2B Sales', 'Customer Service', 'Customer Relationship Management', 'Customer Discovery',
                'Operations Management', 'Supply Chain Management', 'Logistics', 'Process Improvement', 'Lean Six Sigma',
                'Risk Management', 'Strategic Planning', 'Consulting', 'Case Study Analysis', 'Event Management',
                'Event Planning', 'Event Organizing', 'Partnership Management', 'Stakeholder Management',
                'Human Resource Management', 'Recruitment', 'Talent Management', 'Organizational Development',
                'Social Entrepreneurship', 'Sustainability', 'Corporate Social Responsibility', 'ESG Analysis',
                'Impact Measurement', 'Grant Writing', 'Proposal Writing', 'Volunteer Management',
                'Legal Drafting', 'Legal Research', 'Contract Drafting', 'Moot Court', 'Policy Analysis', 'Public Policy',
                'Debate',
            ],

            'Marketing dan Konten' => [
                'Social Media Management', 'Social Media Marketing', 'Content Marketing', 'Content Strategy',
                'Content Creation', 'SEO', 'SEM', 'Google Ads', 'Meta Ads', 'TikTok Marketing', 'Influencer Marketing',
                'Email Marketing', 'Marketing Strategy', 'Brand Management', 'Brand Strategy', 'Public Relations',
                'Media Relations', 'Community Management', 'Growth Hacking', 'Marketing Analytics', 'Google Analytics',
                'E-commerce Management', 'Marketplace Management', 'Affiliate Marketing', 'Storytelling', 'Journalism',
                'Editing and Proofreading', 'Technical Writing', 'Creative Writing', 'Blog Writing', 'Translation',
                'Copy Editing', 'Campaign Planning', 'Video Content Creation',
            ],

            'Akademik dan Sains' => [
                'Academic Writing', 'Scientific Writing', 'Literature Review', 'Research Methodology',
                'Qualitative Research', 'Quantitative Research', 'Survey Design', 'Data Collection', 'Interviewing',
                'Laboratory Techniques', 'Mathematics', 'Mathematical Modeling', 'Biotechnology', 'Bioinformatics',
                'Environmental Science', 'Agricultural Technology', 'Food Technology', 'Public Health',
                'Health Education', 'Nutrition', 'Psychology', 'Counseling', 'Behavioral Research',
                'Instructional Design', 'Curriculum Development', 'Teaching', 'Tutoring', 'Mentoring',
                'Training and Facilitation', 'AutoCAD', 'SketchUp', 'Revit', 'SolidWorks', 'Structural Analysis',
                'Mechanical Design', '3D Printing', 'Circuit Design', 'PCB Design', 'MATLAB', 'LabVIEW',
                'PLC Programming', 'Renewable Energy',
            ],

            'Soft Skill dan Bahasa' => [
                'Critical Thinking', 'Creative Thinking', 'Analytical Thinking', 'Time Management', 'Adaptability',
                'Collaboration', 'Presentation Skills', 'Facilitation', 'Emotional Intelligence', 'Conflict Resolution',
                'Decision Making', 'Professional Networking', 'Self-Management', 'Master of Ceremony',
                'English Communication', 'Japanese Language', 'Mandarin Language', 'Korean Language', 'Arabic Language',
                'German Language', 'French Language', 'Sign Language',
            ],
        ];
    }
}
