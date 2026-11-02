# Applicant Portal: sehemu za Profile (Personal Details hadi CV Preview)

Hii inaongeza kurasa 11 za sidebar. Faili hizi **zinabadilisha** 3 ulizonazo
(`app/Models/Applicant.php`, `routes/applicant.php`, `resources/views/applicant/layout.blade.php`)
na kuongeza mpya. `dashboard.blade.php` yako haijaguswa.

## 1. Nakili (extract) kwenye mradi
Toa zip kwenye `C:\xampp\htdocs\careers-portal` na ukubali **Replace**.

Faili mpya:
- `app/Models/`: AcademicQualification, ProfessionalQualification, WorkExperience, LanguageProficiency, Training, ComputerSkill, Referee, Attachment
- `app/Http/Controllers/Applicant/`: SectionController (msingi), PersonalDetails, AcademicQualification, ProfessionalQualification, LanguageProficiency, WorkExperience, Training, ComputerSkill, Referee, Attachment, Declaration, CvPreview
- `database/migrations/`: faili 9 zilizoanza `2026_10_31_14xxxx`
- `resources/views/applicant/`: personal-details, academic-qualifications, professional-qualifications, language-proficiency, working-experience, training-and-workshop, computer-literacy, referees, other-attachments, declarations, cv-preview
- `resources/views/applicant/partials/`: header, fields, section (zinatumika na kurasa zote)

Faili zinazobadilishwa: `Applicant.php`, `routes/applicant.php`, `layout.blade.php`.

## 2. Endesha
```powershell
composer dump-autoload
php artisan migrate
php artisan optimize:clear
php artisan storage:link
php artisan serve
```
`migrate` itaongeza safu kwenye `applicants` na kutengeneza majedwali 8 mapya.
Kama `storage:link` inasema kiungo kipo tayari, hiyo ni sawa.

## 3. Jaribu
Ingia kwenye `/applicant/login`, kisha bonyeza kila kipengele cha sidebar:
Personal Details, Academic, Professional, Language, Working Experience, Training,
Computer, Referees, Other Attachments, Declarations, CV Preview.

## Jinsi inavyofanya kazi (kwa maendeleo ya baadaye)
- Sehemu 8 za orodha zinatumia `SectionController`. Kila moja ina `config()` yenye
  `fields` (fomu) na `columns` (jedwali). Kuongeza sehemu mpya = controller moja ndogo,
  model, migration, view ya mistari 5, na jina moja kwenye `$sections` ndani ya `routes/applicant.php`.
- Kila applicant anaona na kuhariri rekodi zake tu (`applicant_id`).
- Faili zinahifadhiwa kwenye `storage/app/public/applicants/{id}/{sehemu}` (PDF/JPG/PNG, 2MB).
- Namba ya NIDA inahifadhiwa ikiwa imesimbwa (encrypted); usibadilishe `APP_KEY` baada ya kuhifadhi data.
