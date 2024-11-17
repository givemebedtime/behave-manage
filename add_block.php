<?php
require 'connection.php';

// ฟังก์ชันคำนวณ CurrentHash
function calculateHash($chainID, $data, $previousHash, $timestamp) {
    return hash('sha256', $chainID . $data . $previousHash . $timestamp);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // รับค่าจากฟอร์ม
    $chainID = mysqli_real_escape_string($connect, $_POST['chain_id']);
    $data = mysqli_real_escape_string($connect, $_POST['data']);
    $timestamp = date("Y-m-d H:i:s");

    // ดึงบล็อกล่าสุดใน Chain ที่ระบุ
    $sql = "SELECT * FROM block WHERE Chain_ID = '$chainID' ORDER BY Timestamp DESC LIMIT 1";
    $result = mysqli_query($connect, $sql);

    if (mysqli_num_rows($result) > 0) {
        $lastBlock = mysqli_fetch_assoc($result);
        $previousHash = $lastBlock['CurrentHash'];
    } else {
        $previousHash = "0"; // บล็อกแรกของ Chain นี้
    }

    // คำนวณ CurrentHash
    $currentHash = calculateHash($chainID, $data, $previousHash, $timestamp);

    // เพิ่มบล็อกใหม่ในฐานข้อมูล
    $sql = "INSERT INTO block (Chain_ID, CurrentHash, Data, PreviousHash, Timestamp) 
            VALUES ('$chainID', '$currentHash', '$data', '$previousHash', '$timestamp')";

    if (mysqli_query($connect, $sql)) {
        // ดึงข้อมูลบล็อกที่เพิ่งเพิ่ม
        $newBlockResult = mysqli_query($connect, "SELECT * FROM block WHERE Chain_ID = '$chainID' AND CurrentHash = '$currentHash' LIMIT 1");
        if (mysqli_num_rows($newBlockResult) > 0) {
            $newBlock = mysqli_fetch_assoc($newBlockResult);
            echo "
                <!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='utf-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
                    <meta name='description' content=''>
                    <meta name='author' content=''>
                    <link href='https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&display=swap' rel='stylesheet'>
                    <title>Result</title>
                    <link href='vendor/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
                    <link rel='stylesheet' href='assets/css/fontawesome.css'>
                    <link rel='stylesheet' href='assets/css/style.css'>
                    <link rel='stylesheet' href='assets/css/owl.css'>
                </head>
                <body>
                    <div id='preloader'>
                        <div class='jumper'>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div> 
                    
                    <header class=''>
                            <nav class='navbar navbar-expand-lg'>
                                <div class='container'>
                                        <a class='navbar-brand' href='index.php'><h2>Behavior Student Manage<em>.</em></h2></a>
                                        <button class='navbar-toggler' type='button' data-toggle='collapse' data-target='#navbarResponsive' aria-controls='navbarResponsive' aria-expanded='false' aria-label='Toggle navigation'>
                                            <span class='navbar-toggler-icon'></span>
                                        </button>
                                        <div class='collapse navbar-collapse' id='navbarResponsive'>
                                            <ul class='navbar-nav ml-auto'>
                                                <li class='nav-item '>
                                                    <a class='nav-link' href='index.php'>Student</a>
                                                </li>
                                                <li class='nav-item '>
                                                    <a class='nav-link' href='search_block_web.php'>Search Data</a>
                                                </li>
                                                <li class='nav-item '>
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
                                            <h4>Result</h4>
                                            <h4>Here the Details of Student Block</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    <section class='blog-posts grid-system'>
                        <div class='container'>
                            <br> Student Block ID : " . htmlspecialchars($newBlock['Chain_ID']) . "<br>
                            <br> Score : " . htmlspecialchars($newBlock['DATA']) . "<br>
                            <br> PreviousHash : " . htmlspecialchars($newBlock['PreviousHash']) . "<br>
                            <br> CurrentHash : " . htmlspecialchars($newBlock['CurrentHash']) . "<br>
                            <br> Timestamp : " . htmlspecialchars($newBlock['Timestamp']) . "<br>
                        </div>
                    </section>
                    <footer>
                        <div class='container'>
                            <div class='row'>
                                <div class='col-lg-12'>
                                    <ul class='social-icons'>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </footer>

                    <script src='vendor/jquery/jquery.min.js'></script>
                    <script src='vendor/bootstrap/js/bootstrap.bundle.min.js'></script>
                    <script src='assets/js/custom.js'></script>
                    <script src='assets/js/owl.js'></script>
                    <script src='assets/js/slick.js'></script>
                    <script src='assets/js/isotope.js'></script>
                    <script src='assets/js/accordions.js'></script>
                </body>
                </html>
            ";
        }
    } else {
        echo "<p style='color:red;'>Error: " . mysqli_error($connect) . "</p>";
    }
} else {
    echo "<p style='color:red;'>Invalid request method.</p>";
}

mysqli_close($connect);
?>
