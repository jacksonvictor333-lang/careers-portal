<?php

namespace App\Http\Controllers\Applicant;

use App\Models\ComputerSkill;
use Illuminate\Validation\Rule;

class ComputerSkillController extends SectionController
{
    /** Programu zinazoruhusiwa: kila moja inaongezwa mara moja tu */
    public const SKILLS = ['Microsoft Word', 'Microsoft Excel', 'Microsoft PowerPoint'];

    protected function config(): array
    {
        $levels  = ['Basic', 'Intermediate', 'Advanced'];
        $editId  = request()->route('id');

        $used = ComputerSkill::query()
            ->where('applicant_id', $this->applicant()->id)
            ->when($editId, fn ($q) => $q->where('id', '!=', $editId))
            ->pluck('skill')
            ->all();

        // Zilizokwisha kuongezwa hazitokei tena kwenye dropdown
        $available = array_values(array_diff(self::SKILLS, $used));

        // Rekodi ya zamani yenye jina lingine (iliyoandikwa kwa mkono) bado iharirike
        if ($editId) {
            $current = ComputerSkill::query()
                ->where('applicant_id', $this->applicant()->id)
                ->whereKey($editId)
                ->value('skill');
            if ($current && !in_array($current, $available, true)) {
                $available[] = $current;
            }
        }

        return [
            'slug'     => 'computer-literacy',
            'route'    => 'applicant.computer-literacy',
            'view'     => 'applicant.computer-literacy',
            'model'    => ComputerSkill::class,
            'title'    => 'Computer Literacy',
            'singular' => 'Computer skill',
            'subtitle' => 'Choose Word, Excel and PowerPoint and set your level for each.',
            'icon'     => 'bi-pc-display',
            'order'    => ['id', 'asc'],

            // Zote tatu zikishaongezwa, kitufe cha Add kinafichwa
            'can_add'      => count(array_diff(self::SKILLS, $used)) > 0,
            'full_message' => 'You have added Word, Excel and PowerPoint. Edit a row to change its level.',

            'fields' => [
                'skill' => [
                    'label'   => 'Software',
                    'type'    => 'select',
                    'options' => $available,
                    'rules'   => [
                        'required',
                        Rule::in(array_unique(array_merge(self::SKILLS, $available))),
                        Rule::unique('computer_skills', 'skill')
                            ->where('applicant_id', $this->applicant()->id)
                            ->ignore($editId),
                    ],
                ],
                'level' => [
                    'label'   => 'Level',
                    'type'    => 'select',
                    'options' => $levels,
                    'rules'   => ['required', Rule::in($levels)],
                ],
            ],

            'columns' => [
                'skill' => 'Software',
                'level' => 'Level',
            ],
        ];
    }
}
