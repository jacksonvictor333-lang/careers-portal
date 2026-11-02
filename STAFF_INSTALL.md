# Careers Portal - Mfumo kamili wa STAFF

Applicant portal haijaguswa isipokuwa mabadiliko madogo yanayohitajika na staff
(Applicant.php, JobApplication.php, Vacancy.php, routes/applicant.php, routes/web.php,
ukurasa wa reset password). Hifadhi nakala ya hizi na config/auth.php, bootstrap/app.php kabla ya Replace.

```powershell
cd C:\xampp\htdocs\careers-portal
Expand-Archive -Path "$env:USERPROFILE\Downloads\careers-portal-STAFF-full.zip" -DestinationPath . -Force
php artisan migrate
php artisan optimize:clear
```
Staff: /staff/login   admin@careers.test / Admin@12345  (badilisha password mara moja)

Yaliyomo: Dashboard, Users (viewer/officer/admin), Employer, Applicant (tabs + Action menu),
Salary Scales, Scheme Of Service, Job Permit*, Adverts, Archive, Attachments,
Settings (Programme categories, My account), Reports (CSV), Tools*   (*placeholder)
Hali za maombi: single + bulk, historia ya mabadiliko, namba ya usaili otomatiki.
