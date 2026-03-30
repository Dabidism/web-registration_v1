<?php
require_once 'dbConnection.php';

$db = new Database();
$conn = $db->getConnection();

$sql1 = "CREATE TABLE IF NOT EXISTS colleges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL
)";
$conn->query($sql1);

$sql2 = "CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY(college_id) REFERENCES colleges(id) ON DELETE CASCADE
)";
$conn->query($sql2);

// Seed if empty
$res = $conn->query("SELECT COUNT(*) as c FROM colleges");
$row = $res->fetch_assoc();
if ($row['c'] == 0) {
    $colleges = [
        ['CAS', 'College of Arts and Sciences'],
        ['CCI', 'College of Computing in Informatics'],
        ['COE', 'College of Education'],
        ['CEA', 'College of Engineering and Architecture'],
        ['CIT', 'College of Industrial Technology']
    ];
    $stmt = $conn->prepare("INSERT INTO colleges (code, name) VALUES (?, ?)");
    foreach ($colleges as $col) {
        $stmt->bind_param("ss", $col[0], $col[1]);
        $stmt->execute();
    }

    $c_map = [
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
    $stmt = $conn->prepare("SELECT id, code FROM colleges");
    $stmt->execute();
    $res = $stmt->get_result();
    $id_map = [];
    while($row = $res->fetch_assoc()) {
        $id_map[$row['code']] = $row['id'];
    }

    $cstmt = $conn->prepare("INSERT INTO courses (college_id, name) VALUES (?, ?)");
    foreach ($c_map as $code => $courses) {
        if(isset($id_map[$code])) {
            foreach ($courses as $cname) {
                $cstmt->bind_param("is", $id_map[$code], $cname);
                $cstmt->execute();
            }
        }
    }
}
echo "Migration successful\n";
?>
