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
    'required' => 'Das Feld :attribute ist erforderlich.',
    'string' => ':attribute muss ein Text sein.',

    'attributes' => [
        'name' => 'Name',
        'email' => 'E-Mail',
        'subject' => 'Betreff',
        'message' => 'Nachricht',
        'cf-turnstile-response' => 'Captcha',
    ],
];
