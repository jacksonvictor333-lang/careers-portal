<?php

/*
|--------------------------------------------------------------------------
| Orodha ya programu / kozi kwa kila kiwango cha elimu
|--------------------------------------------------------------------------
| Funguo ni majina ya viwango kama yalivyo kwenye fomu (Level of education).
| O-Level na A-Level hazimo kwa sababu hazina programme.
| Ongeza au futa majina hapa; kila orodha inapangwa kwa alfabeti kiotomatiki.
| Programu isiyokuwepo: applicant anachagua "Other (not in the list)" na kuandika.
*/

$certificate = [
    'Accountancy', 'Banking and Finance', 'Business Administration', 'Community Development',
    'Computer Science', 'Cooperative Management', 'Early Childhood Education', 'Environmental Health Sciences',
    'Civil Engineering', 'Electrical Engineering', 'Mechanical Engineering', 'Automotive Engineering',
    'Fisheries', 'Forestry', 'General Agriculture', 'Animal Health and Production',
    'Human Resource Management', 'Information and Communication Technology', 'Journalism and Mass Communication',
    'Land Management and Valuation', 'Law', 'Library and Information Studies', 'Marketing',
    'Medical Laboratory Sciences', 'Clinical Medicine', 'Nursing and Midwifery', 'Pharmaceutical Sciences',
    'Primary Education (Teaching)', 'Procurement and Supplies Management',
    'Records, Archives and Information Management', 'Social Work', 'Statistics', 'Surveying (Geomatics)',
    'Tourism and Hospitality Management', 'Water Resources Engineering', 'Wildlife Management',
];

$diplomaExtra = [
    'Economics', 'Public Administration', 'Project Management', 'Education (Secondary Teaching)',
    'Environmental Science', 'Food Science and Technology', 'Architecture', 'Quantity Surveying',
    'Telecommunications Engineering', 'Computer Engineering', 'Information Technology', 'Software Development',
    'Mining Engineering', 'Irrigation Engineering', 'Dental Therapy', 'Optometry', 'Radiography',
    'Physiotherapy', 'Public Health', 'Theology',
];

$prefix = function (string $label, array $items): array {
    $out = array_map(fn ($i) => $label . ' in ' . $i, array_values(array_unique($items)));
    sort($out, SORT_NATURAL | SORT_FLAG_CASE);

    return $out;
};

$advanced = [
    'Accountancy', 'Banking and Finance', 'Business Administration', 'Community Development', 'Computer Science',
    'Civil Engineering', 'Electrical Engineering', 'Mechanical Engineering', 'Economics',
    'Education (Secondary Teaching)', 'Environmental Health Sciences', 'Forestry', 'General Agriculture',
    'Human Resource Management', 'Information Technology', 'Journalism and Mass Communication',
    'Land Management and Valuation', 'Law', 'Library and Information Studies', 'Marketing',
    'Medical Laboratory Sciences', 'Clinical Medicine', 'Nursing and Midwifery', 'Pharmaceutical Sciences',
    'Procurement and Supplies Management', 'Public Administration', 'Records and Archives Management',
    'Social Work', 'Statistics', 'Tourism and Hospitality Management', 'Wildlife Management',
];

$bachelor = [
    'Accounting', 'Accounting and Finance', 'Agricultural Economics and Agribusiness', 'Agricultural Engineering',
    'Agriculture (General)', 'Animal Science', 'Architecture', 'Banking and Finance', 'Biochemistry', 'Biology',
    'Biotechnology', 'Botany', 'Business Administration', 'Chemical and Process Engineering', 'Chemistry',
    'Civil Engineering', 'Community Development', 'Computer Engineering', 'Computer Science',
    'Cyber Security', 'Dental Surgery', 'Development Studies', 'Economics', 'Education in Arts',
    'Education in Science', 'Education in Mathematics', 'Education in Commerce', 'Education in Information Technology',
    'Electrical Engineering', 'Electronics and Telecommunications Engineering', 'Environmental Engineering',
    'Environmental Health Sciences', 'Environmental Science and Management', 'Finance', 'Fisheries and Aquaculture',
    'Food Science and Technology', 'Forestry', 'Geology', 'Geomatics', 'Health Systems Management',
    'Human Resource Management', 'Information Systems', 'Information Technology', 'International Relations',
    'Journalism and Mass Communication', 'Kiswahili', 'Land Management and Valuation', 'Law (LL.B)',
    'Library and Information Science', 'Linguistics', 'Logistics and Transport Management', 'Marketing',
    'Mathematics', 'Mechanical Engineering', 'Medical Laboratory Sciences', 'Medicine (MD)', 'Mining Engineering',
    'Nursing', 'Pharmacy', 'Physics', 'Political Science and Public Administration', 'Procurement and Supply Chain Management',
    'Project Planning and Management', 'Public Administration', 'Public Health', 'Quantity Surveying',
    'Real Estate', 'Records and Archives Management', 'Social Work', 'Sociology', 'Software Engineering',
    'Statistics', 'Theology', 'Tourism and Hospitality Management', 'Urban and Regional Planning',
    'Veterinary Medicine', 'Wildlife Management', 'Zoology',
];

$pgd = [
    'Education', 'Public Administration', 'Project Management', 'Information Technology', 'Procurement and Supply Chain Management',
    'Finance', 'Accounting', 'Human Resource Management', 'Public Health', 'Legal Practice', 'Community Economic Development',
    'Monitoring and Evaluation', 'Business Administration', 'Environmental Management', 'Agricultural Extension',
    'Computer Science', 'Statistics', 'Social Work', 'Marketing', 'Records and Archives Management',
];

$masters = [
    'Business Administration (MBA)', 'Public Administration (MPA)', 'Public Health (MPH)', 'Laws (LL.M)', 'Education',
    'Computer Science', 'Information Technology', 'Information Systems', 'Cyber Security', 'Data Science', 'Economics',
    'Accounting and Finance', 'Finance', 'Project Management', 'Procurement and Supply Chain Management',
    'Human Resource Management', 'Development Studies', 'Monitoring and Evaluation', 'Statistics', 'Mathematics',
    'Civil Engineering', 'Electrical Engineering', 'Mechanical Engineering', 'Environmental Science and Management',
    'Agricultural Economics', 'Agronomy', 'Animal Science', 'Food Science and Technology', 'Forestry',
    'Wildlife Management', 'Nursing', 'Medicine (MMed)', 'Sociology', 'Social Work', 'Linguistics', 'Kiswahili',
    'Library and Information Science', 'International Relations', 'Urban and Regional Planning', 'Real Estate',
];

$phd = [
    'Computer Science', 'Information Technology', 'Economics', 'Education', 'Business Administration',
    'Public Administration', 'Law', 'Agricultural Sciences', 'Engineering', 'Public Health', 'Medicine',
    'Natural Sciences', 'Social Sciences', 'Development Studies', 'Management', 'Environmental Science',
    'Linguistics', 'Mathematics', 'Statistics', 'Sociology', 'Physics', 'Chemistry', 'Biology',
];

return [
    'Certificate'          => $prefix('Certificate', $certificate),
    'Ordinary Diploma'     => $prefix('Diploma', array_merge($certificate, $diplomaExtra)),
    'Advanced Diploma'     => $prefix('Advanced Diploma', $advanced),
    'Bachelor Degree'      => $prefix('Bachelor Degree', $bachelor),
    'Postgraduate Diploma' => $prefix('Postgraduate Diploma', $pgd),
    "Master's Degree"      => $prefix("Master's Degree", $masters),
    'PhD'                  => $prefix('PhD', $phd),
];
