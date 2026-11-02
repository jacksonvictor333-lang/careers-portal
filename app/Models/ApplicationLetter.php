<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApplicationLetter extends Model
{
    protected $guarded = ['id'];

    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    /** Aya za barua (zimetenganishwa kwa mstari mtupu) */
    public function paragraphs(): array
    {
        return array_values(array_filter(
            preg_split('/\R{2,}/', trim((string) $this->body)),
            fn ($p) => trim($p) !== ''
        ));
    }

    /** Aina ya kazi kulingana na kichwa cha post (kutafuta maneno muhimu) */
    private const TAILORED = [
        'teaching'   => ['lecturer', 'tutor', 'tutorial', 'teacher', 'instructor', 'academic'],
        'health'     => ['nurse', 'clinical', 'medical', 'pharmac', 'laborator', 'health', 'doctor'],
        'ict'        => ['ict', 'computer', 'software', 'system', 'network', 'database', 'information technology', 'it officer', 'programmer'],
        'finance'    => ['account', 'finance', 'audit', 'bursar', 'economist', 'budget', 'revenue', 'tax'],
        'technical'  => ['engineer', 'artisan', 'technician', 'electrical', 'mechanical', 'civil', 'plumb', 'mason', 'mechanic', 'surveyor'],
        'procure'    => ['procurement', 'supplies', 'stores', 'logistic'],
        'records'    => ['record', 'secretary', 'registry', 'clerk', 'office management', 'administrative', 'personal assistant'],
    ];

    /** Aya ya kati ya barua, kulingana na aina ya kazi na taasisi */
    private static function middleParagraph(string $title, string $institution): string
    {
        $t = mb_strtolower($title);
        $kind = 'general';

        foreach (self::TAILORED as $name => $keywords) {
            foreach ($keywords as $k) {
                if (str_contains($t, $k)) {
                    $kind = $name;
                    break 2;
                }
            }
        }

        return match ($kind) {
            'ict' => 'I hold qualifications in information and communication technology and have practical experience in supporting computer systems, networks and end users. I am confident that I can contribute to the ICT services of ' . $institution . '.',
            'finance' => 'I have training and experience in accounting and financial management, including keeping accurate records, preparing reports and observing financial regulations. I would apply this knowledge to support the financial work of ' . $institution . '.',
            'technical' => 'I have technical training and hands-on experience in my trade, and I work carefully and follow safe working practices. I am ready to carry out the duties of this post to the standard required by ' . $institution . '.',
            'teaching' => 'I have the academic background and a strong interest in teaching, preparing learning materials and supporting students to succeed. I would be proud to contribute to the academic mission of ' . $institution . '.',
            'health' => 'I am trained and registered to work in the health sector and I am committed to professional ethics, patient safety and quality care. I look forward to serving the people who depend on ' . $institution . '.',
            'procure' => 'I understand public procurement procedures and have experience in keeping supplies and stores records accurately. I would support ' . $institution . ' to obtain value for money and comply with the procurement rules.',
            'records' => 'I am organised, keep records carefully and treat official information with confidentiality. I would apply these skills to support the administrative work of ' . $institution . '.',
            default => 'I am a hardworking and disciplined person, willing to learn and to work as part of a team. I would do my best to contribute to the objectives of ' . $institution . '.',
        };
    }

    /** Kichwa na maandishi ya barua kulingana na post na taasisi (haihitaji database) */
    public static function compose(string $title, string $institution, ?string $appliedOn = null): array
    {
        $date = $appliedOn ?: 'the closing date';

        return [
            'subject' => 'Application for the post of ' . $title,
            'body'    =>
                'I write to apply for the post of ' . $title . ' at ' . $institution
                . ', which I submitted through the online recruitment portal on ' . $date . '. '
                . "My academic and professional qualifications, work experience, training and referees are recorded in my online profile and are available together with this application.\n\n"
                . self::middleParagraph($title, $institution) . "\n\n"
                . 'I confirm that the information I have provided is true and complete, and I would be grateful to be considered for this position.',
        ];
    }

    /** Barua ya mfano kwa ombi husika, iliyoandikwa kulingana na post na taasisi */
    public static function createDefaultFor(JobApplication $app): self
    {
        $letter = self::compose(
            $app->vacancy_title,
            $app->institution,
            $app->applied_at ? $app->applied_at->format('d F Y') : null
        );

        return static::create(['job_application_id' => $app->id] + $letter);
    }
}
