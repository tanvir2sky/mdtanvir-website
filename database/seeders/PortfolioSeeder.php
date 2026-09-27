<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\Project;
use App\Models\SkillGroup;
use Illuminate\Database\Seeder;

/**
 * Home page content (experience, skills, projects) in English and German.
 * Records are only created when missing, so edits made in the admin are kept.
 */
class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->experiences() as $order => $experience) {
            Experience::query()->firstOrCreate(
                ['company' => $experience['company']],
                $experience + ['sort_order' => $order + 1]
            );
        }

        foreach ($this->skillGroups() as $order => $group) {
            if (! SkillGroup::query()->where('name->en', $group['name']['en'])->exists()) {
                SkillGroup::query()->create($group + ['sort_order' => $order + 1]);
            }
        }

        foreach ($this->projects() as $order => $project) {
            Project::query()->firstOrCreate(
                ['slug' => $project['slug']],
                $project + ['sort_order' => $order + 1]
            );
        }
    }

    private function experiences(): array
    {
        return [
            [
                'company' => 'Altruan GmbH',
                'url' => 'https://altruan.de',
                'period' => 'Present',
                'is_current' => true,
                'role' => ['en' => 'Software Engineer', 'de' => 'Softwareentwickler'],
                'focus' => [
                    'en' => [
                        ['icon' => 'fas fa-brain', 'title' => 'AI features', 'text' => 'LLM-powered features shipped in production Laravel apps.'],
                        ['icon' => 'fab fa-laravel', 'title' => 'Laravel platforms', 'text' => 'Scalable web applications and RESTful APIs.'],
                        ['icon' => 'fab fa-shopify', 'title' => 'Shopify solutions', 'text' => 'Custom Shopify builds and integrations.'],
                    ],
                    'de' => [
                        ['icon' => 'fas fa-brain', 'title' => 'KI-Features', 'text' => 'LLM-gestützte Features in produktiven Laravel-Anwendungen.'],
                        ['icon' => 'fab fa-laravel', 'title' => 'Laravel-Plattformen', 'text' => 'Skalierbare Webanwendungen und RESTful APIs.'],
                        ['icon' => 'fab fa-shopify', 'title' => 'Shopify-Lösungen', 'text' => 'Individuelle Shopify-Lösungen und Integrationen.'],
                    ],
                ],
                'highlights' => [
                    'en' => [
                        'Developing and maintaining scalable web applications using Laravel and PHP.',
                        'Building LLM-powered product features: API integration, prompt design, queued background processing, and keeping cost and latency under control.',
                        'Building custom Shopify solutions and RESTful APIs.',
                        'Speeding up delivery with AI coding assistants (Claude Code, Copilot) and agentic development workflows.',
                        'Collaborating with cross-functional teams to deliver high-quality software.',
                    ],
                    'de' => [
                        'Entwicklung und Wartung skalierbarer Webanwendungen mit Laravel und PHP.',
                        'Entwicklung LLM-gestützter Produktfeatures: API-Integration, Prompt-Design, Hintergrundverarbeitung über Queues sowie Kontrolle von Kosten und Latenz.',
                        'Entwicklung individueller Shopify-Lösungen und RESTful APIs.',
                        'Schnellere Umsetzung mit KI-Coding-Assistenten (Claude Code, Copilot) und agentischen Entwicklungs-Workflows.',
                        'Zusammenarbeit mit funktionsübergreifenden Teams für hochwertige Software.',
                    ],
                ],
                'tags' => ['Laravel', 'PHP', 'LLM APIs', 'Shopify', 'REST APIs', 'Claude Code'],
            ],
            [
                'company' => 'technoPLUS IT',
                'url' => 'https://technoplusit.com.au',
                'period' => '2020 - 2025',
                'is_current' => false,
                'role' => ['en' => 'Full Stack Developer', 'de' => 'Full-Stack-Entwickler'],
                'highlights' => [
                    'en' => [
                        'Built responsive web applications from concept to deployment.',
                        'Implemented frontend interfaces with modern CSS and JavaScript.',
                        'Developed robust backend systems with the Laravel framework.',
                    ],
                    'de' => [
                        'Entwicklung responsiver Webanwendungen vom Konzept bis zum Deployment.',
                        'Umsetzung von Frontend-Oberflächen mit modernem CSS und JavaScript.',
                        'Entwicklung robuster Backend-Systeme mit dem Laravel-Framework.',
                    ],
                ],
                'tags' => ['Laravel', 'PHP', 'JavaScript', 'CSS', 'Responsive Design'],
            ],
            [
                'company' => 'Creativeitem',
                'url' => 'https://www.creativeitem.com',
                'period' => '2018 - 2020',
                'is_current' => false,
                'role' => ['en' => 'Junior Developer', 'de' => 'Junior-Entwickler'],
                'highlights' => [
                    'en' => [
                        'Started my professional journey working on PHP-based projects.',
                        'Gained expertise in database design, API development, and modern web development practices.',
                        'Contributed to building high-quality web applications and digital products.',
                    ],
                    'de' => [
                        'Beginn meiner beruflichen Laufbahn mit PHP-basierten Projekten.',
                        'Erfahrung in Datenbankdesign, API-Entwicklung und modernen Webentwicklungspraktiken gesammelt.',
                        'Mitarbeit an hochwertigen Webanwendungen und digitalen Produkten.',
                    ],
                ],
                'tags' => ['PHP', 'MySQL', 'API Development'],
            ],
        ];
    }

    private function skillGroups(): array
    {
        return [
            [
                'icon' => 'fas fa-server',
                'name' => ['en' => 'Backend', 'de' => 'Backend'],
                'items' => [
                    'en' => ['Laravel', 'PHP', 'RESTful APIs', 'MySQL', 'PostgreSQL'],
                    'de' => ['Laravel', 'PHP', 'RESTful APIs', 'MySQL', 'PostgreSQL'],
                ],
            ],
            [
                'icon' => 'fas fa-code',
                'name' => ['en' => 'Frontend', 'de' => 'Frontend'],
                'items' => [
                    'en' => ['HTML5', 'CSS3', 'JavaScript', 'TailwindCSS', 'Responsive Design'],
                    'de' => ['HTML5', 'CSS3', 'JavaScript', 'TailwindCSS', 'Responsive Design'],
                ],
            ],
            [
                'icon' => 'fas fa-shopping-cart',
                'name' => ['en' => 'E-commerce', 'de' => 'E-Commerce'],
                'items' => [
                    'en' => ['Shopify', 'Shopify Apps', 'Liquid Templates', 'Store Customization'],
                    'de' => ['Shopify', 'Shopify-Apps', 'Liquid-Templates', 'Shop-Anpassungen'],
                ],
            ],
            [
                'icon' => 'fas fa-robot',
                'name' => ['en' => 'AI & LLM', 'de' => 'KI & LLM'],
                'items' => [
                    'en' => ['LLM API Integration', 'Prompt Engineering', 'AI Product Features', 'Claude Code & Copilot', 'Agentic Workflows'],
                    'de' => ['LLM-API-Integration', 'Prompt Engineering', 'KI-Produktfeatures', 'Claude Code & Copilot', 'Agentische Workflows'],
                ],
            ],
            [
                'icon' => 'fas fa-tools',
                'name' => ['en' => 'Tools & DevOps', 'de' => 'Tools & DevOps'],
                'items' => [
                    'en' => ['Git & GitHub', 'Docker', 'CI/CD', 'Linux', 'AWS'],
                    'de' => ['Git & GitHub', 'Docker', 'CI/CD', 'Linux', 'AWS'],
                ],
            ],
        ];
    }

    /**
     * Case studies start as unpublished drafts built only from the card descriptions.
     * Add real details, screenshots and outcomes in the admin before publishing.
     */
    private function projects(): array
    {
        return [
            [
                'slug' => 'ai-powered-features',
                'is_featured' => true,
                'icon' => 'fas fa-brain',
                'accent' => 'cyan',
                'tags' => ['Laravel', 'LLM APIs', 'Prompt Engineering', 'Queues'],
                'title' => ['en' => 'AI-Powered Features', 'de' => 'KI-gestützte Features'],
                'category' => ['en' => 'AI Engineering', 'de' => 'KI-Engineering'],
                'summary' => [
                    'en' => 'LLM-driven features built into production Laravel applications, with prompt design, queued processing for long requests, and guardrails for reliability and cost.',
                    'de' => 'LLM-gestützte Features in produktiven Laravel-Anwendungen, mit Prompt-Design, Queue-Verarbeitung für lange Anfragen und Leitplanken für Zuverlässigkeit und Kosten.',
                ],
                'challenge' => [
                    'en' => 'Bring large language models into production Laravel applications while keeping them fast, reliable and affordable.',
                    'de' => 'Große Sprachmodelle in produktive Laravel-Anwendungen bringen und dabei schnell, zuverlässig und bezahlbar halten.',
                ],
                'approach' => [
                    'en' => 'Model calls run in queued jobs with explicit timeouts and retries, prompts are designed and versioned like code, and outputs are validated before they are used.',
                    'de' => 'Modellaufrufe laufen in Queue-Jobs mit festen Timeouts und Retries, Prompts werden wie Code entworfen und versioniert, und Ausgaben werden vor der Verwendung validiert.',
                ],
                'body' => ['en' => '<h2>Overview</h2><p>Add the full story here: the product context, the features you built, screenshots, and what changed for users.</p>'],
            ],
            [
                'slug' => 'e-commerce-platform',
                'icon' => 'fas fa-bag-shopping',
                'accent' => 'sky',
                'tags' => ['Laravel', 'Shopify', 'PHP'],
                'title' => ['en' => 'E-commerce Platform', 'de' => 'E-Commerce-Plattform'],
                'category' => ['en' => 'E-commerce', 'de' => 'E-Commerce'],
                'summary' => [
                    'en' => 'A full-featured e-commerce solution built with Laravel and Shopify integration. Includes custom storefront, payment processing, and inventory management.',
                    'de' => 'Eine umfassende E-Commerce-Lösung mit Laravel und Shopify-Integration, inklusive individueller Storefront, Zahlungsabwicklung und Lagerverwaltung.',
                ],
                'body' => ['en' => '<h2>Overview</h2><p>Add the full story here: the client, the problem, your role, screenshots, and the results.</p>'],
            ],
            [
                'slug' => 'shopify-app',
                'icon' => 'fab fa-shopify',
                'accent' => 'emerald',
                'tags' => ['Laravel', 'Shopify API', 'PHP', 'REST API'],
                'title' => ['en' => 'Shopify App', 'de' => 'Shopify-App'],
                'category' => ['en' => 'Shopify', 'de' => 'Shopify'],
                'summary' => [
                    'en' => 'Custom Shopify application built with Laravel and Shopify API integration. Features include product management, order processing, and automated workflows for e-commerce stores.',
                    'de' => 'Individuelle Shopify-App mit Laravel und Shopify-API-Integration, mit Produktverwaltung, Bestellabwicklung und automatisierten Workflows für Onlineshops.',
                ],
                'body' => ['en' => '<h2>Overview</h2><p>Add the full story here: what the app does, who uses it, how it integrates with Shopify, and screenshots.</p>'],
            ],
            [
                'slug' => 'financial-app',
                'icon' => 'fas fa-chart-pie',
                'accent' => 'violet',
                'tags' => ['Laravel', 'PHP', 'MySQL', 'Payment Gateway'],
                'title' => ['en' => 'Financial App', 'de' => 'Finanz-App'],
                'category' => ['en' => 'FinTech', 'de' => 'FinTech'],
                'summary' => [
                    'en' => 'Comprehensive financial management application built with Laravel. Features include transaction tracking, budget management, financial reporting, and secure payment processing.',
                    'de' => 'Umfassende Finanzverwaltungs-App mit Laravel, mit Transaktionsverfolgung, Budgetverwaltung, Finanzberichten und sicherer Zahlungsabwicklung.',
                ],
                'body' => ['en' => '<h2>Overview</h2><p>Add the full story here: the users, the core workflows, security considerations, screenshots, and outcomes.</p>'],
            ],
        ];
    }
}
