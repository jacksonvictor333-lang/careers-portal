<?php

namespace App\Http\Controllers\Applicant;

use App\Models\Attachment;

class AttachmentController extends SectionController
{
    protected function config(): array
    {
        return [
            'slug'     => 'other-attachments',
            'route'    => 'applicant.other-attachments',
            'view'     => 'applicant.other-attachments',
            'model'    => Attachment::class,
            'title'    => 'Other Attachments',
            'singular' => 'Attachment',
            'subtitle' => 'Upload supporting documents: CV, ID, birth certificate, transcripts and others.',
            'icon'     => 'bi-paperclip',
            'order'    => ['id', 'desc'],

            'fields' => [
                'type' => [
                    'label'   => 'Document type',
                    'type'    => 'select',
                    'options' => [
                        'Curriculum Vitae (CV)',
                        'National ID (NIDA)',
                        'Birth Certificate',
                        'Passport Photo',
                        'Academic Transcript',
                        'Recommendation Letter',
                        'Other',
                    ],
                    'rules'   => ['required', 'string', 'max:100'],
                ],
                'title' => [
                    'label' => 'Title / Description',
                    'rules' => ['required', 'string', 'max:255'],
                ],
                'attachment_path' => [
                    'label' => 'File',
                    'type'  => 'file',
                    'rules' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                    'span'  => 2,
                ],
            ],

            'columns' => [
                'type'            => 'Type',
                'title'           => 'Title',
                'attachment_path' => 'File',
            ],
        ];
    }
}
