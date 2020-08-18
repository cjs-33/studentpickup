<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="SchoolPickup.net">
    <meta property="og:site_name" content="SchoolPickup.net: Safe, Socially-Distant, Secure Student Pickup Application">
    <meta property="og:title" content="SchoolPickup.net: Safe, Socially-Distant, Secure Student Pickup Application">
    <meta property="og:url" content="https://schoolpickup.net" >
    <meta property="og:type" content="website">
    <meta property="og:description" content="Easy to use, socially distant, Covid-19 safe application that makes school dismissals straightfowrward and organized. ">
    <meta itemprop="name" content="SchoolPickup.net: Safe, Socially-Distant, Secure Student Pickup Application">
    <meta itemprop="url" content="https://schoolpickup.net">
    <meta itemprop="description" content="Easy to use, socially distant, Covid-19 safe application that makes school dismissals straightfowrward and organized.">
    <meta name="description" content="Easy to use, socially distant, Covid-19 safe application that makes school dismissals straightfowrward and organized.">
    <meta name="keywords" content="school, pickup, dismissal, education, teachers, covid-19, safe dismissal, school pickup, pickup, parents, driving" >
    <meta name="theme-color" content="#007bff">
    
    <title>SchoolPickup.net | Coming Soon</title>
    <!-- app includes -->
    <?php include_once(__DIR__ . "/includes/includes.php"); ?>
</head>

<body class='text-center'>
    <div class='container-fluid' id='header'>
        <div class='row'>
            <div class='col-xs-12 col-sm-4'>
                <h1 class=''><a href='/'>SchoolPickup.net</a></h1>
            </div>
            <div class='col-xs-12 col-sm-2'>
                <!-- this area intentionally left blank -->
            </div>
            <div class='col-xs-12 col-sm-6'>
                <ul class="nav nav-pills nav-fill">

                    <?php// if (isset($_SESSION['user_id'])) {
                    ?>
                        <li class="nav-item">
                            <a class='nav-link' href='/view/ocps'>Pickup</a>
                        </li>
                        <?php
                        //if ($_SESSION['user_type'] == 'admin') {
                        ?>
                            <li class='nav-item'>
                                <a class='nav-link' href='/edit/ocps'>Add/Edit</a>
                            </li>

                        <?php
                      //  }
                        ?>
                        <!-- <li class="nav-item">
                            <a class='nav-link' href='/logout'>Log Out</a>
                        </li> -->

                    <?php
                   // } else {
                    ?>
                        <!-- <li class="nav-item">
                            <a class='nav-link' href='/login'>Log In</a>
                        </li> -->
                    <?php
                 //   }

                    ?>


                    <li class="nav-item">
                        <!-- <a class="nav-link" href="#">Login</a> -->
                        <!-- <a class="nav-link" href="#">Contact</a> -->
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class='clear'></div>

    <script type='text/javascript'>
        // var userId = localStorage.getItem('user_id');

        // $.ajax({
        //     url: "/app/api/public.php",
        //     method: "POST",
        //     data: {"fname" : "checkSession", "user_id" : userId},
        //     success: function(response) {
        //         console.log(response);

        //         response = JSON.parse(response);

        //         if (response === true) {
        //             //all ok
        //         } else {
        //             $.ajax({
        //                 url: "/app/api/public.php",
        //                 method: "POST",
        //                 data: {"fname" : "logout", "user_id" : userId},
        //                 success: function(response) {
        //                     alert("Your session has timed out. Please log in again.");
        //                     window.location.href = "/login";
        //                 }, error: function(response) {
        //                     console.error(response);
        //                 }
        //             })
        //         }

        //     }, error: function(response) {
        //         console.error(response);
        //     }
        // })

    </script>


    <div class='container'>