<?php
header('Content-Type: application/json');

$collegeCourses = [
    'CAS' => [
        'Bachelor of Science in English Language',
        'Bachelor in Human Services',
        'Bachelor of Science in Biology',
        'Bachelor of Science in Community Development',
        'Bachelor of Science in Mathematics'
    ],
    'CCI' => [
        'Bachelor of Science in Computer Science',
        'Bachelor of Science in Information Systems',
        'Bachelor of Science in Information Technology'
    ],
    'COE' => [
        'Bachelor of Elementary Education',
        'Bachelor of Secondary Education',
        'Bachelor of Technical Vocational Teacher Education',
        'Bachelor of Technology and Livelihood Education'
    ],
    'CEA' => [
        'Bachelor of Science in Architecture',
        'Bachelor of Science in Civil Engineering',
        'Bachelor of Science in Electrical Engineering',
        'Bachelor of Science in Mechanical Engineering',
        'Bachelor of Science in Electronics Engineering'
    ],
    'CIT' => [
        'Bachelor of Industrial Technology',
        'Bachelor in Fashion Design and Merchandising',
        'Bachelor of Science in Entrepreneurship',
        'Bachelor of Science in Hospitality Management',
        'Bachelor of Science in Tourism Management',
        'Entrepreneurial Training Courses',
        'Evening Vocational Course'
    ]
];

echo json_encode($collegeCourses);
?>