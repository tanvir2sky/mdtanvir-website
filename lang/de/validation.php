<?php

// German validation messages for the rules used on the public forms.

return [
    'boolean' => ':attribute muss wahr oder falsch sein.',
    'date' => ':attribute muss ein gültiges Datum sein.',
    'email' => ':attribute muss eine gültige E-Mail-Adresse sein.',
    'exists' => 'Der gewählte Wert für :attribute ist ungültig.',
    'image' => ':attribute muss ein Bild sein.',
    'max' => [
        'array' => ':attribute darf nicht mehr als :max Elemente haben.',
        'file' => ':attribute darf nicht größer als :max Kilobyte sein.',
        'numeric' => ':attribute darf nicht größer als :max sein.',
        'string' => ':attribute darf nicht länger als :max Zeichen sein.',
    ],
    'in' => 'Der gewählte Wert für :attribute ist ungültig.',
    'min' => [
        'array' => ':attribute muss mindestens :min Elemente haben.',
        'file' => ':attribute muss mindestens :min Kilobyte groß sein.',
        'numeric' => ':attribute muss mindestens :min sein.',
        'string' => ':attribute muss mindestens :min Zeichen lang sein.',
    ],
    'required' => 'Das Feld :attribute ist erforderlich.',
    'string' => ':attribute muss ein Text sein.',
    'timezone' => ':attribute muss eine gültige Zeitzone sein.',
    'url' => ':attribute muss eine gültige URL sein.',

    'attributes' => [
        'name' => 'Name',
        'email' => 'E-Mail',
        'subject' => 'Betreff',
        'message' => 'Nachricht',
        'cf-turnstile-response' => 'Captcha',
        'website' => 'Website',
        'topic' => 'Thema',
        'start' => 'Termin',
        'timezone' => 'Zeitzone',
    ],
];
