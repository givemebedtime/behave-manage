<?php
session_start();  // เริ่มต้น session

require 'connection.php'; // ดึงการเชื่อมต่อฐานข้อมูล

if (isset($_GET['hash'])) {
    $hash = mysqli_real_escape_string($connect, $_GET['hash']);

    // ค้นหาบล็อกในฐานข้อมูล
    $sql = "SELECT * FROM block WHERE CurrentHash = '$hash'";
    $result = mysqli_query($connect, $sql);

    //echo "<div style='max-width:600px; margin: 20px auto;'>";
    if (mysqli_num_rows($result) > 0) {
        $block = mysqli_fetch_assoc($result);

        // เก็บข้อมูลใน session
        $_SESSION['block'] = $block;

        echo  "<!DOCTYPE html>
            <html lang='en'>
            <head>
                 <meta charset='utf-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
                <meta name='description' content=''>
                <meta name='author' content=''>
                <link href='https://fonts.googleapis.com/css?family=Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900,900i&display=swap' rel='stylesheet'>
                <title>Cake IT</title>
                <!-- Bootstrap core CSS -->
                <link href='vendor/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
                <!-- Additional CSS Files -->
                <link rel='stylesheet' href='assets/css/fontawesome.css'>
                <link rel='stylesheet' href='assets/css/style.css'>
                <link rel='stylesheet' href='assets/css/owl.css'>
        
             </head>
            <body>
                 <!-- ***** Preloader Start ***** -->
                 <div id='preloader'>
                     <div class='jumper'>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                 </div>  
            <!-- ***** Preloader End ***** -->

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

        <!-- Page Content -->
        <!-- Banner Starts Here -->
        <div class='heading-page header-text'>
            <section class='page-heading'>
                <div class='container'>
                    <div class='row'>
                        <div class='col-lg-12'>
                            <div class='text-content'>
                                <h4>Block Found</h4>
                                <h4>Here the detail of Student</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        
        <!-- Banner Ends Here -->

        <section class='blog-posts grid-system'>
            <div class='container'>
                <br> Student Block ID : " . htmlspecialchars($block['Chain_ID']) . "<br>
                <br> Score : " . htmlspecialchars($block['DATA']) . "<br>
                <br> PreviousHash : " . htmlspecialchars($block['PreviousHash']) . "<br>
                <br> CurrentHash : " . htmlspecialchars($block['CurrentHash']) . "<br>
                <br> Timestamp : " . htmlspecialchars($block['Timestamp']) . "<br>
            </div>
        </section>
        
        <footer>
            <div class='container'>
                <div class='row'>
                    <div class='col-lg-12'>
                        <ul class='social-icons'>
                            
                        </ul>
                    </div>
                    <div class='col-lg-12'>
                        
                    </div>
                </div>
            </div>
        </footer>

        <!-- Bootstrap core JavaScript -->
        <script src='vendor/jquery/jquery.min.js'></script>
        <script src='vendor/bootstrap/js/bootstrap.bundle.min.js'></script>

        <!-- Additional Scripts -->
        <script src='assets/js/custom.js'></script>
        <script src='assets/js/owl.js'></script>
        <script src='assets/js/slick.js'></script>
        <script src='assets/js/isotope.js'></script>
        <script src='assets/js/accordions.js'></script>

        <script language = 'text/Javascript'> 
            cleared[0] = cleared[1] = cleared[2] = 0; //set a cleared flag for each field
            function clearField(t){                   //declaring the array outside of the
                if(! cleared[t.id]){                      // function makes it static and global
                    cleared[t.id] = 1;  // you could use true and false, but that's more typing
                    t.value='';         // with more chance of typos
                    t.style.color='#fff';
                }
            }
        </script>
    </body>
    </html>
            
    ";
    } else {
        echo "<script>alert('Error, Student Block Not Found'); window.location.href = 'search_block_web.php';</script>";
    }
    echo "</div>";
} else {
    echo "<p style='color:red;'>No hash provided!</p>";
}

mysqli_close($connect);
?>
