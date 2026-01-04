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
        'Bachelor of Secondary Education major in Filipino',
        'Bachelor of Secondary Education major in Mathematics',
        'Bachelor of Secondary Education major in Science',
        'Bachelor of Technology and Livelihood Education major in Home Economics',
        'Bachelor of Technology and Livelihood Education major in Industrial Arts',
        'Bachelor of Technology and Livelihood Education major in Information and Communication Technology',
        'Bachelor of Technical-Vocational Teacher Education major in Automotive Technology',
        'Bachelor of Technical-Vocational Teacher Education major in Beauty Care and Wellness',
        'Bachelor of Technical-Vocational Teacher Education major in Computer Programming',
        'Bachelor of Technical-Vocational Teacher Education major in Drafting Technology',
        'Bachelor of Technical-Vocational Teacher Education major in Electrical Technology',
        'Bachelor of Technical-Vocational Teacher Education major in Electronics Technology',
        'Bachelor of Technical-Vocational Teacher Education major in Food Services Management'
    ],
    'CEA' => [
        'Bachelor of Science in Architecture',
        'Bachelor of Science in Civil Engineering',
        'Bachelor of Science in Electrical Engineering',
        'Bachelor of Science in Mechanical Engineering',
        'Bachelor of Science in Electronics Engineering'
    ],
    'CIT' => [
        'Bachelor of Industrial Technology major in Architectural Drafting Technology',
        'Bachelor of Industrial Technology major in Automotive Technology',
        'Bachelor of Industrial Technology major in Beauty Care and Wellness Technology',
        'Bachelor of Industrial Technology major in Construction/Civil Technology',
        'Bachelor of Industrial Technology major in Culinary Technology',
        'Bachelor of Industrial Technology major in Electrical Technology',
        'Bachelor of Industrial Technology major in Electronics Technology',
        'Bachelor of Industrial Technology major in Apparel Fashion Technology',
        'Bachelor of Industrial Technology major in Heating, Ventilating, Air-Conditioning and Refrigeration Technology',
        'Bachelor of Industrial Technology major in Mechanical Technology',
        'Bachelor of Industrial Technology major in Welding and Fabrication Technology',
        'Bachelor of Science in Entrepreneurship',
        'Bachelor of Science in Fashion Design and Merchandising',
        'Bachelor of Science in Hospitality Management',
        'Bachelor of Science in Tourism Management',
        'Entrepreneurial Training Courses',
        'Evening Vocational Course'
    ]
];

echo json_encode($collegeCourses);
?>