<?php

namespace App\Support;

use Carbon\CarbonInterface;

/** Minimal RFC 5545 calendar invite builder. */
class Ics
{
    /**
     * @param  array{uid: string, start: CarbonInterface, end: CarbonInterface, summary: string, description?: string,
     *               location?: ?string, url?: ?string, organizer_email: string, organizer_name?: string,
     *               attendee_email: string, attendee_name?: string, method?: string, status?: string, sequence?: int}  $event
     */
    public static function event(array $event): string
    {
        $utc = fn (CarbonInterface $date) => $date->copy()->setTimezone('UTC')->format('Ymd\THis\Z');
        $method = $event['method'] ?? 'REQUEST';

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//MD Tanvir Hossain//Booking//EN',
            'CALSCALE:GREGORIAN',
            "METHOD:{$method}",
            'BEGIN:VEVENT',
            'UID:'.self::escape($event['uid']),
            'DTSTAMP:'.$utc(now()),
            'DTSTART:'.$utc($event['start']),
            'DTEND:'.$utc($event['end']),
            'SUMMARY:'.self::escape($event['summary']),
        ];

        if (! empty($event['description'])) {
            $lines[] = 'DESCRIPTION:'.self::escape($event['description']);
        }
        if (! empty($event['location'])) {
            $lines[] = 'LOCATION:'.self::escape($event['location']);
        }
        if (! empty($event['url'])) {
            $lines[] = 'URL:'.$event['url'];
        }

        $lines[] = 'ORGANIZER;CN='.self::escapeParam($event['organizer_name'] ?? $event['organizer_email']).':mailto:'.$event['organizer_email'];
        $lines[] = 'ATTENDEE;CN='.self::escapeParam($event['attendee_name'] ?? $event['attendee_email']).';ROLE=REQ-PARTICIPANT;PARTSTAT=ACCEPTED:mailto:'.$event['attendee_email'];
        $lines[] = 'STATUS:'.($event['status'] ?? 'CONFIRMED');
        $lines[] = 'SEQUENCE:'.($event['sequence'] ?? 0);
        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", array_map([self::class, 'fold'], $lines))."\r\n";
    }

    /** Escape TEXT values: backslash, semicolon, comma and newlines. */
    public static function escape(string $value): string
    {
        return str_replace(["\\", ';', ',', "\r\n", "\n", "\r"], ['\\\\', '\\;', '\\,', '\\n', '\\n', '\\n'], $value);
    }

    /** Parameter values (like CN) are quoted and may not contain double quotes. */
    private static function escapeParam(string $value): string
    {
        return '"'.str_replace('"', "'", $value).'"';
    }

    /** Fold lines longer than 75 octets, without splitting multi-byte characters. */
    public static function fold(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }

        $parts = [];
        $current = '';
        $limit = 75;

        foreach (mb_str_split($line) as $char) {
            if (strlen($current) + strlen($char) > $limit) {
                $parts[] = $current;
                $current = '';
                $limit = 74; // continuation lines start with a space
            }
            $current .= $char;
        }
        $parts[] = $current;

        return implode("\r\n ", $parts);
    }
}
