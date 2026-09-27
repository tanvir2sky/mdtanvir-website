<?php

namespace Tests\Unit;

use App\Support\Ics;
use Carbon\Carbon;
use Tests\TestCase;

class IcsTest extends TestCase
{
    public function test_text_values_are_escaped(): void
    {
        $this->assertSame('a\\, b\\; c\\\\ d\\ne', Ics::escape("a, b; c\\ d\ne"));
    }

    public function test_long_lines_are_folded_without_breaking_characters(): void
    {
        $line = 'DESCRIPTION:'.str_repeat('Größe äöü ', 30);
        $folded = Ics::fold($line);

        foreach (explode("\r\n", $folded) as $part) {
            $this->assertLessThanOrEqual(75, strlen($part));
            $this->assertTrue(mb_check_encoding($part, 'UTF-8'));
        }

        // Unfolding (removing CRLF + space) restores the original line.
        $this->assertSame($line, str_replace("\r\n ", '', $folded));
    }

    public function test_event_structure(): void
    {
        $ics = Ics::event([
            'uid' => 'abc@example.com',
            'start' => Carbon::parse('2026-10-06 09:00', 'Europe/Berlin'),
            'end' => Carbon::parse('2026-10-06 09:30', 'Europe/Berlin'),
            'summary' => 'Call, with; notes',
            'organizer_email' => 'me@example.com',
            'attendee_email' => 'you@example.com',
            'attendee_name' => 'You "Quoted"',
        ]);

        $this->assertStringStartsWith("BEGIN:VCALENDAR\r\n", $ics);
        $this->assertStringEndsWith("END:VCALENDAR\r\n", $ics);
        $this->assertStringContainsString("DTSTART:20261006T070000Z\r\n", $ics);
        $this->assertStringContainsString('SUMMARY:Call\\, with\\; notes', $ics);
        $this->assertStringContainsString('ATTENDEE;CN="You \'Quoted\'"', $ics);
        $this->assertStringContainsString('METHOD:REQUEST', $ics);
    }
}
