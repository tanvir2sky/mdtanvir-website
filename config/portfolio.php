<?php

/*
|--------------------------------------------------------------------------
| Portfolio content
|--------------------------------------------------------------------------
|
| Profile facts used by the interactive hero terminal. Keep this in sync
| with the sections in resources/views/home.blade.php.
|
*/

return [

    'name' => 'MD Tanvir Hossain',
    'role' => 'Software Engineer',
    'location' => 'Germany',
    'email' => 'contact@mdtanvir.com',
    'linkedin' => 'https://www.linkedin.com/in/tanvir-cs',
    'github' => 'https://github.com/tanvir-cs',
    'summary' => 'Software Engineer with 8+ years building scalable web apps with Laravel, PHP and Shopify, now shipping LLM-powered features in production.',

    'skills' => [
        'Backend' => ['Laravel', 'PHP', 'RESTful APIs', 'MySQL', 'PostgreSQL'],
        'Frontend' => ['HTML5', 'CSS3', 'JavaScript', 'TailwindCSS', 'Responsive Design'],
        'E-commerce' => ['Shopify', 'Shopify Apps', 'Liquid', 'Store Customization'],
        'AI & LLM' => ['LLM API integration', 'Prompt engineering', 'AI coding tools', 'Agentic dev workflows'],
        'Tools & DevOps' => ['Git & GitHub', 'Docker', 'CI/CD', 'Linux', 'AWS'],
    ],

    'experience' => [
        [
            'role' => 'Software Engineer',
            'company' => 'Altruan GmbH',
            'url' => 'https://altruan.de',
            'period' => 'Present',
            'current' => true,
            'focus' => [
                ['icon' => 'fas fa-brain', 'title' => 'AI features', 'text' => 'LLM-powered features shipped in production Laravel apps.'],
                ['icon' => 'fab fa-laravel', 'title' => 'Laravel platforms', 'text' => 'Scalable web applications and RESTful APIs.'],
                ['icon' => 'fab fa-shopify', 'title' => 'Shopify solutions', 'text' => 'Custom Shopify builds and integrations.'],
            ],
            'highlights' => [
                'Developing and maintaining scalable web applications using Laravel and PHP.',
                'Building LLM-powered product features: API integration, prompt design, queued background processing, and keeping cost and latency under control.',
                'Building custom Shopify solutions and RESTful APIs.',
                'Speeding up delivery with AI coding assistants (Claude Code, Copilot) and agentic development workflows.',
                'Collaborating with cross-functional teams to deliver high-quality software.',
            ],
            'tags' => ['Laravel', 'PHP', 'LLM APIs', 'Shopify', 'REST APIs', 'Claude Code'],
        ],
        [
            'role' => 'Full Stack Developer',
            'company' => 'technoPLUS IT',
            'url' => 'https://technoplusit.com.au',
            'period' => '2020 - 2025',
            'highlights' => [
                'Built responsive web applications from concept to deployment.',
                'Implemented frontend interfaces with modern CSS and JavaScript.',
                'Developed robust backend systems with the Laravel framework.',
            ],
            'tags' => ['Laravel', 'PHP', 'JavaScript', 'CSS', 'Responsive Design'],
        ],
        [
            'role' => 'Junior Developer',
            'company' => 'Creativeitem',
            'url' => 'https://www.creativeitem.com',
            'period' => '2018 - 2020',
            'highlights' => [
                'Started my professional journey working on PHP-based projects.',
                'Gained expertise in database design, API development, and modern web development practices.',
                'Contributed to building high-quality web applications and digital products.',
            ],
            'tags' => ['PHP', 'MySQL', 'API Development'],
        ],
    ],

    'projects' => [
        ['name' => 'AI-Powered Features', 'stack' => 'Laravel, LLM APIs, Queues'],
        ['name' => 'E-commerce Platform', 'stack' => 'Laravel, Shopify, PHP'],
        ['name' => 'Shopify App', 'stack' => 'Laravel, Shopify API, REST'],
        ['name' => 'Financial App', 'stack' => 'Laravel, MySQL, Payment Gateway'],
    ],

    // Place your CV at public/cv/MD-Tanvir-Hossain-CV.pdf to enable the download button.
    'cv_path' => 'cv/MD-Tanvir-Hossain-CV.pdf',

];
