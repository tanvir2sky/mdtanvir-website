{{-- UI strings for the browser scripts, in the current locale. --}}
@php
  $jsStrings = [
    'locale' => app()->getLocale(),
    'common' => [
      'sending' => __('Sending…'),
      'copied' => __('Copied!'),
      'copyLink' => __('Copy link'),
      'pressCtrlC' => __('Press Ctrl+C'),
      'copyThisLink' => __('Copy this link:'),
    ],
    'terminal' => [
      'available' => __('Available commands:'),
      'commands' => [
        'about' => __('Who I am'),
        'skills' => __('Tech stack by area'),
        'experience' => __('Career timeline'),
        'projects' => __('Selected work'),
        'contact' => __('How to reach me'),
        'cv' => __('Download my CV'),
        'clear' => __('Clear the screen'),
      ],
      'role' => __('Role'),
      'email' => __('Email'),
      'scrolling' => __('Scrolling to the contact form…'),
      'noCv' => __('CV is not available yet.'),
      'openingCv' => __('Opening CV…'),
      'notDefined' => __('Command ":command" is not defined.'),
      'try' => __('Try'),
      'hint' => __('Type :help to see what else you can run.'),
    ],
    'brief' => [
      'features' => [
        'laravel' => [__('Admin dashboard'), __('REST API'), __('Authentication & roles'), __('Payments'), __('Third-party integrations'), __('Performance optimisation')],
        'shopify' => [__('Custom theme'), __('Custom Shopify app'), __('Store migration'), __('Checkout & payments'), __('ERP / CRM integration'), __('Speed optimisation')],
        'ai' => [__('Chat assistant'), __('Content generation'), __('Document summarisation'), __('Smart search'), __('Workflow automation'), __('Add AI to an existing app')],
        'other' => [__('Code review / audit'), __('Bug fixing'), __('Ongoing maintenance'), __('Technical consulting')],
      ],
      'types' => [
        'laravel' => __('Laravel web application'),
        'shopify' => __('Shopify store / app'),
        'ai' => __('AI-powered feature'),
        'other' => __('Engineering support'),
      ],
      'greeting' => __('Hi Tanvir,'),
      'intro' => __('I would like to discuss a project: :type.'),
      'idea' => __('The idea:'),
      'include' => __('What it should include:'),
      'budget' => __('Budget'),
      'timeline' => __('Timeline'),
      'closing' => __('Looking forward to hearing from you!'),
      'subject' => __('Project enquiry: :type'),
    ],
    'palette' => [
      'noResults' => __('No results for “:query”'),
      'loading' => __('Loading…'),
      'groups' => [
        'recent' => __('Recent'),
        'navigation' => __('Go to'),
        'action' => __('Actions'),
        'project' => __('Case studies'),
        'post' => __('Articles'),
      ],
      'emailCopied' => __('Email address copied'),
    ],
  ];
@endphp
<script>window.__i18n = @json($jsStrings);</script>
