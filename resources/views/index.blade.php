<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BROWZESS</title>

    <!-- bootstrap cdn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>


    <!-- font awsome cdn  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <!-- my css -->
    <link rel="stylesheet" href="{{ url('css/style.css') }}">

    <style>
        body {
            margin: 0;
            padding: 0;
            font-size: 14px;
        }

        .MAIN_TITLE {
            margin-left: 80px;
            color: white;
            float: left;
            height: 20px;
            transform: skew(10deg);
            background: linear-gradient(135deg, #8B008B 0%, #0a193b 100%);
        }

        .MAIN_TITLE_TEXT {
            color: white;
            font-size: 26px;
            font-weight: bold;
            position: relative;
            top: 0px;

        }


        .fa {
            padding: 13px;
            font-size: 25px;
            width: 50px;
            text-align: center;
            margin: 3px 2px;
            border-radius: 50%;
        }

        .fa:hover {
            opacity: 0.7;
            color: white;
        }

        .fa-facebook {
            background: #3B5998;
            color: white;
        }

        .fa-twitter {
            background: #55ACEE;
            color: white;
        }

        .fa-linkedin {
            background: #007bb5;
            color: white;
        }

        .fa-instagram {
            background: #C13584;
            color: white;
        }

        .fa-skype {
            background: #00aff0;
            color: white;
        }

        .fa-youtube {
            background: red;
            color: white;
        }

        .bg-image {

            background-image: url("img/img_bg.jpg");


            backdrop-filter: blur(8px);
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        .dot {
            height: 50px;
            width: 50px;
            background-color: orange;
            border-radius: 50%;
            display: inline-block;
        }

        form .otp {
            display: inline-block;
            width: 50px;
            height: 50px;
            text-align: center;
        }

        .login-image-border {
            height: 35px;
            width: 100%;
            background-color: #b1d3db;
        }

        .form-label {
            display: inline-block;
            max-width: 100%;
            margin-bottom: 5px;
            font-weight: 700;
            font-size: 14px;
            color: #555;
            text-decoration: none;
        }

        .form-control {
            display: block;
            width: 95%;
            height: 34px;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.42857143;
            color: #555;
            background-color: #FFFFFF;
            background-image: none;
            border: 1px solid #212F3D;
            border-radius: 0px;
        }

        .button {
            padding: 5px 10px;
            background: transparent;
            border: none;
            font-size: 16px;
            color: white;
            min-width: 75px;
            border-radius: 0;
        }

        .button:hover {
            /* filter: blur(1px); */
            opacity: 0.8;
        }
    </style>
</head>

<body style="background-color: #f1f3f6;">
    <div class="col-3 login-box m-auto mt-4 border bg-white shadow">
        <div class="login-logo px-3">
            <img class="" width="100%" src="{{ url('image/andtrading_logo.jpg') }}" alt="">
        </div>
        <div class="login-image-border text-danger fw-bold align-content-center px-2">
            @error('LOGIN_ID')
            {{ $message }}
            @enderror

            @error('LOGIN_PASSWORD')
            {{ $message }}
            @enderror

            @error('login')
            {{ $message }}
            @enderror
        </div>
        <div class="login-form p-3">
            <form action="{{ route('login.verify') }}" method="post">
                @csrf
                <div class="form-group mb-3">
                    <label class="form-label" for="LOGIN_ID">User Id</label>
                    <input class="form-control" type="text" name="LOGIN_ID" id="LOGIN_ID" value="{{ old('LOGIN_ID', $loginId ?? '') }}">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label" for="LOGIN_PASSWORD">Password</label>
                    <input class="form-control" type="password" name="LOGIN_PASSWORD" id="LOGIN_PASSWORD" value="{{ old('LOGIN_PASSWORD', $loginPassword ?? '') }}">
                </div>
                <div class="row justify-content-between mb-3">
                    <div class="col">
                        <input type="checkbox" name="REMEMBER_ME" id="REMEMBER_ME" checked>
                        <label class="form-label" for="REMEMBER_ME">Remember me</label>
                    </div>
                    <div class="col">
                        <a class="form-label" href="">Forget Password?</a>
                    </div>
                </div>
                <div class="form-group text-center">
                    <button class="button me-2" type="submit" style="background:#009688;">Submit</button>
                    <button class="button ms-2" type="reset" style="background:#FFB61E;">Clear</button>
                </div>
            </form>
        </div>
        <hr>
        <div style="text-align:center;" class="p-1">
            <img src="{{ url('image/BROWZESS_Logo.png') }}" class="img-c ircle" width="40%" height="40%" alt="user" style="margin-top:-18px;">
        </div>
    </div>
</body>

</html>