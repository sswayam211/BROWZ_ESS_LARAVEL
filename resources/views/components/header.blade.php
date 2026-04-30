<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BROWZESS</title>


    <!-- additional  -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    

    <!-- bootstrap cdn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>


    <!-- font awsome cdn  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <!-- data table cdn  -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">


    <!-- my css -->
    <link rel="stylesheet" href="{{ url('css/style.css') }}">


    <style>
        @media screen and (max-width:692px) {

            .logo,
            .right-info {
                width: 100%;
            }
        }

        .drop-menu {
            display: block;
            padding-left: 15px;
            height: 0px;
            overflow: hidden;
            transition: all .1s linear;
        }
    </style>

</head>

<body>

    <header class="sticky-top px-sm-2">
        <div class="py-2 px-sm-4 d-flex justify-content-between align-items-center flex-wrap">

            <div class="logo d-flex align-items-center justify-content-between">
                <div class="button d-md-none d-block">
                    <button class="" type="button">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                </div>
                <h1 class="m-0 ms-sm-auto m-auto me-sm-2" style="font-weight: 700;">My ESS</h1>
            </div>


            <div class="right-info">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="fst-info me-md-3">

                        <p class="border border-2 border-white rounded-5 work-list text-center small">My Work List</p>
                        <div class="d-flex justify-content-between align-items-center flex-wrap">

                            <a href="approveLeave2.php?leaves=85474990" class="text-light">
                                <div
                                    class="box border border-2 border-white rounded-3 d-flex justify-content-center align-items-center">
                                    <p class="m-0 mx-sm-2 icon rounded-5 bg-black text-white"><img
                                            src="image/head-icon-leave.png" height="15px" alt=""></p>
                                    <div class="text-center" style="line-height: 1;">
                                        <p class="m-0 fs-5">0</p>
                                        <p class="m-0 small">Leave</p>
                                    </div>
                                </div>
                            </a>

                            <a href="overtime-approval.php" class="text-light">
                                <div
                                    class="box border border-2 border-white rounded-3 d-flex justify-content-center align-items-center">
                                    <p class="m-0 mx-sm-2 icon rounded-5 bg-black text-white"><img
                                            src="image/head-icon-overtime.png" height="15px" alt="">
                                    </p>
                                    <div class="text-center" style="line-height: 1;">
                                        <p class="m-0 fs-5">0</p>
                                        <p class="m-0 small">Overtime</p>
                                    </div>
                                </div>
                            </a>

                            <a href="approveResignation.php" class="text-light">
                                <div
                                    class="box border border-2 border-white rounded-3 d-flex justify-content-center align-items-center">
                                    <p class="m-0 mx-sm-2 icon rounded-5 bg-black text-white">
                                        <img src="image/head-icon-request.png" height="18px" alt="" style="filter:invert(1)">
                                    </p>
                                    <div class="text-center" style="line-height: 1;">
                                        <p class="m-0 fs-5">0</p>
                                        <p class="m-0 small">Resignation</p>
                                    </div>
                                </div>
                            </a>


                            <a href="/approve_appraisal" class="text-light">
                                <div
                                    class="box border border-2 border-white rounded-3 d-flex justify-content-center align-items-center">
                                    <p class="m-0 mx-sm-2 icon rounded-5 bg-black text-white">
                                        <img src="image/head-icon-request.png" height="18px" alt="" style="filter:invert(1)">
                                    </p>
                                    <div class="text-center" style="line-height: 1;">
                                        <p class="m-0 fs-5">{{ count($APRSL_PENDING_NO) }}</p>
                                        <p class="m-0 small">Appraisal Sug.</p>
                                    </div>
                                </div>
                            </a>


                            <a href="approveLoan.php" class="text-light">
                                <div
                                    class="box border border-2 border-white rounded-3 d-flex justify-content-center align-items-center">
                                    <p class="m-0 mx-sm-2 icon rounded-5 bg-black text-white">
                                        <img src="image/head-icon-request.png" height="18px" alt="" style="filter:invert(1)">
                                    </p>
                                    <div class="text-center" style="line-height: 1;">
                                        <p class="m-0 fs-5">0</p>
                                        <p class="m-0 small">Loan Req.</p>
                                    </div>
                                </div>
                            </a>

                            <a href="approveRequest.php" class="text-light">
                                <div
                                    class="box border border-2 border-white rounded-3 d-flex justify-content-center align-items-center">
                                    <p class="m-0 mx-sm-2 icon rounded-5 bg-black text-white">
                                        <img src="image/head-icon-request.png" height="18px" alt="" style="filter:invert(1)">
                                    </p>
                                    <div class="text-center" style="line-height: 1;">
                                        <p class="m-0 fs-5">0</p>
                                        <p class="m-0 small">Requests</p>
                                    </div>
                                </div>
                            </a>


                        </div>
                    </div>
                    <div class="scnd-info d-flex justify-content-end align-items-center ms-auto">
                        <div class="d-flex align-items-center">
                            <div class="rounded-5">
                                <img class="rounded-5 bg-white mx-sm-3 me-1" src="http://browzhr.com/andtradinghrms/public/{{ $employee->EMP_PHOTO }}"
                                    height="50px" width="50px" alt="">
                            </div>
                            <div class="welcome ">
                                <p class="m-0 small-2">Welcome</p>
                                <div class="dropdown">
                                    <a class="text-white dropdown-toggle" style="white-space: normal;" href="#" role="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $employee->EMP_NAME }} ({{ $employee->EMP_CODE }})
                                    </a>

                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="dashboard.php"><i class="fa-solid fa-user me-3"></i>User Profile</a></li>
                                        <li><a class="dropdown-item" href="changePassword.php"><i class="fa-solid fa-key me-3"></i>Change Password</a></li>
                                        <li><a class="dropdown-item" href="logout"><i class="fa-solid fa-arrow-right-from-bracket me-3"></i>Sign Out</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="row">
            <div class="col-xl-2 col-lg-3 col-md-3 p-0">
                <div class="side-nav">
                    <div class="row flex-column justify-content-between h-100 flex-nowrap">
                        <div class="col">
                            <ul class="m-0 p-0">
                                <li class="">
                                    <a href="{{ route('dashboard') }}">
                                        <p><span class="px-2 ps-lg-4 icon"><img src="image/icon-info.png" height="25px"
                                                    alt=""></span>Information</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="./my-Leave.php">
                                        <p><span class="px-2 ps-lg-4 icon"><img src="image/icon-leave.png" height="25px"
                                                    alt=""></span>My Leave
                                        </p>
                                    </a>
                                </li>
                                <li class="">
                                    <a href="my-Overtime.php">
                                        <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-overtime.png" height="25px"
                                                    alt=""></span>My Overtime</p>
                                    </a>
                                </li>

                                <div class="drop-down">
                                    <li class="">
                                        <a href="#" class="show-req-dropdown">
                                            <p class="d-flex justify-content-between flex-nowrap align-items-center " style="white-space:nowrap;">
                                                <span>
                                                    <span class="px-2  ps-lg-4 icon"><img src="image/icon-req.png" height="25px"
                                                            alt=""></span>My Requests
                                                </span>
                                                <span class="drop-icon " style="margin: 6px 15px;transition:all .1s linear">
                                                    <i class="fa-solid fa-chevron-down"></i>
                                                </span>
                                            </p>
                                        </a>
                                    </li>
                                    <div class="drop-menu" style="height:0px">
                                        <li>
                                            <a href="request.php" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-req.png" height="25px"
                                                            alt=""></span>Document
                                                </p>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="loanRequest.php" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-req.png" height="25px"
                                                            alt=""></span>Salary Loan
                                                </p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="resignationRequest.php" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-req.png" height="25px"
                                                            alt=""></span>Resignation
                                                </p>
                                            </a>
                                        </li>
                                        <!-- <li>
                                            <a href="appraisalRequest.php" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-req.png" height="25px"
                                                            alt=""></span>Appraisal Req.
                                                </p>
                                            </a>
                                        </li> -->
                                    </div>
                                </div>

                                <li>
                                    <a href="MyDocuments.php">
                                        <p><span class="px-2 ps-lg-4 icon"><img src="image/icon-doc-2.png" height="35px"
                                                    alt=""></span>My Documents</p>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('appraisal') }}">
                                        <p><span class="px-2 ps-lg-4 icon"><img src="image/icon-appraisal.png" height="35px"
                                                    alt=""></span>Appraisal</p>
                                    </a>
                                </li>


                                <!-- ----------------------------------EXTRA DETAILS ------------------------------ -->
                                <div class="drop-down">
                                    <li class="">
                                        <a href="#" class="show-req-dropdown">
                                            <p class="d-flex justify-content-between flex-nowrap align-items-center " style="white-space:nowrap;">
                                                <span>
                                                    <span class="px-2  ps-lg-4 icon"><img src="image/icon-req.png" height="25px"
                                                            alt=""></span>Master
                                                </span>
                                                <span class="drop-icon " style="margin: 6px 15px;transition:all .1s linear">
                                                    <i class="fa-solid fa-chevron-down"></i>
                                                </span>
                                            </p>
                                        </a>
                                    </li>
                                    <div class="drop-menu" style="height:0px">
                                        <li>
                                            <a href="M_DOC_REQUEST_TYPE.PHP" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-req.png" height="25px"
                                                            alt=""></span>Doc. Req. Type
                                                </p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="M_DOC_REQUEST_PURPOSE.PHP" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-req.png" height="25px"
                                                            alt=""></span>Doc. Req. Purpose
                                                </p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="M_DOC_REQUEST_SUB_CAT.PHP" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-req.png" height="25px"
                                                            alt=""></span>Doc. Req. Sub-Cat.
                                                </p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="M_DOC_REQUEST_LETTER_TEMPLATE.PHP" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-req.png" height="25px"
                                                            alt=""></span>Doc. Template
                                                </p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('appraisal.kpi-master') }}" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-appraisal.png" height="30px"
                                                            alt=""></span>Appraisal KIP
                                                </p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="M_APPRAISAL_KPI_EC.PHP" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-appraisal.png" height="30px"
                                                            alt=""></span>Appraisal KIP EC
                                                </p>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="M_APPRAISAL_KPI_JOB_LINK.PHP" class="">
                                                <p><span class="px-2  ps-lg-4 icon"><img src="image/icon-appraisal.png" height="30px"
                                                            alt=""></span>Appraisal KPI Assingment
                                                </p>
                                            </a>
                                        </li>
                                    </div>
                                </div>

                                <!-- ----------------------------------EXTRA DETAILS ------------------------------ -->





                            </ul>
                        </div>
                        <div class="logo col align-content-end">
                            <div class="side-nav-img text-center">
                                <a href="#">
                                    <img class="" src="image/andtrading_logo.jpg" alt="" height="" width="100%">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-10 col-lg-9 col-md-9 col-sm-12 p-0 main-body">