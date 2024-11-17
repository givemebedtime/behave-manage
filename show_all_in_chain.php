<?php
require 'connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // รับค่าจากฟอร์ม
    $chainID = mysqli_real_escape_string($connect, $_POST['chain_id']);

    // ดึงข้อมูลทั้งหมดใน Chain ที่ระบุ
    $sql = "SELECT * FROM block WHERE Chain_ID = '$chainID' ORDER BY Timestamp ASC";
    $result = mysqli_query($connect, $sql);

    if (mysqli_num_rows($result) > 0) {
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
            <meta name='description' content=''>
            <meta name='author' content=''>
            <link href='https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&display=swap' rel='stylesheet'>
            <title>Student Details</title>
            <link href='vendor/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='assets/css/fontawesome.css'>
            <link rel='stylesheet' href='assets/css/style.css'>
            <link rel='stylesheet' href='assets/css/owl.css'>
        </head>
        <body>
            <!-- Header -->
                 <header class=''>
                    <nav class='navbar navbar-expand-lg'>
                        <div class='container'>
                            <a class='navbar-brand' href='index.php'><h2>Behavior Student Manage<em>.</em></h2></a>
                        <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarResponsive' aria-controls='navbarResponsive' aria-expanded='false' aria-label='Toggle navigation'>
                            <span class='navbar-toggler-icon'></span>
                        </button>
                        <div class='collapse navbar-collapse' id='navbarResponsive'>
                        <ul class='navbar-nav ml-auto'>                     
                        <li class='nav-item'>
                            <a class='nav-link' href='index.php'>Student</a>
                        </li>
                        <li class='nav-item '>
                            <a class='nav-link' href='search_block_web.php'>Search Data</a>
                        </li>
                        <li class='nav-item active'>
                            <a class='nav-link' href='search_chain.php'>Search Student</a>
                        </li>
                        </ul>
                    </div>
                </div>
                </nav>
            </header>

            <div class='heading-page header-text'>
                <section class='page-heading'>
                    <div class='container'>
                        <div class='row'>
                            <div class='col-lg-12'>
                                <div class='text-content'>
                                    <h4>Chain Details</h4>
                                    <h4>All blocks in Chain ID: " . htmlspecialchars($chainID) . "</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <section class='blog-posts grid-system'>
    <div class='container'>
        <!-- เพิ่ม div เพื่อทำให้เลื่อนตารางได้ -->
        <div style='overflow-x: auto;'>
            <table class='table table-bordered table-sm' style='min-width: 800px; font-size: 14px;'>
                <thead class='thead-dark'>
                    <tr>
                        <th>Student ID</th>
                        <th>Score</th>
                        <th>Previous Hash</th>
                        <th>Current Hash</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>";
                    // วนลูปแสดงข้อมูลบล็อกทั้งหมด
                    while ($block = mysqli_fetch_assoc($result)) {
                        echo "
                        <tr>
                            <td>" . htmlspecialchars($block['Chain_ID']) . "</td>
                            <td>" . htmlspecialchars($block['DATA']) . "</td>
                            <td>" . htmlspecialchars($block['PreviousHash']) . "</td>
                            <td>" . htmlspecialchars($block['CurrentHash']) . "</td>
                            <td>" . htmlspecialchars($block['Timestamp']) . "</td>
                        </tr>";
                    }
                    echo "
                </tbody>
            </table>
        </div>
    </div>
</section>";
    } else {
        echo "<script>alert('Error, Student Not Found'); window.location.href = 'search_chain.php';</script>";
    }
} else {
    echo "<p style='color:red;'>Invalid request method.</p>";
}

mysqli_close($connect);
?>
