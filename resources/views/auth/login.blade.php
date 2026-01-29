<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Feeder Stainas</title>
    <!-- Bootstrap Css -->
    <link href="{{ asset('templates/assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('templates/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        :root {
            --warm-bg: #fdfcf0;
            --warm-accent: #8d6e63;
            --warm-accent-hover: #6d4c41;
            --warm-text: #3e2723;
            --warm-muted: #a1887f;
            --soft-shadow: 0 20px 40px rgba(141, 110, 99, 0.1);
        }

        body {
            background-color: var(--warm-bg);
            color: var(--warm-text);
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }

        .auth-full-page-bg {
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .login-card {
            border: none;
            border-radius: 30px;
            box-shadow: var(--soft-shadow);
            overflow: hidden;
            background: white;
            transition: transform 0.3s ease;
        }

        .login-right-content {
            background: linear-gradient(135deg, #fdfcf0 0%, #f5e6d3 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            min-height: 500px;
        }

        .svg-container {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .login-image {
            position: relative;
            z-index: 2;
            max-width: 85%;
            filter: drop-shadow(20px 20px 50px rgba(141, 110, 99, 0.3));
            transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .login-image:hover {
            transform: scale(1.05) translateY(-10px);
        }

        .form-content {
            padding: 60px;
        }

        .btn-primary {
            background-color: var(--warm-accent);
            border-color: var(--warm-accent);
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--warm-accent-hover);
            border-color: var(--warm-accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(109, 76, 65, 0.3);
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #efebe9;
            background-color: #fafafa;
        }

        .form-control:focus {
            border-color: var(--warm-accent);
            box-shadow: 0 0 0 0.25rem rgba(141, 110, 99, 0.1);
            background-color: white;
        }

        .form-label {
            font-weight: 500;
            color: var(--warm-accent);
        }

        .text-primary {
            color: var(--warm-accent) !important;
        }

        .welcome-text {
            color: var(--warm-text);
            font-weight: 800;
            letter-spacing: -0.5px;
        }
    </style>
</head>

<body>
    <div class="auth-full-page-bg">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-xl-11">
                    <div class="card login-card">
                        <div class="row g-0">
                            <!-- Left: Login Form -->
                            <div class="col-lg-6 order-2 order-lg-1">
                                <div class="form-content">
                                    <div class="mb-5 text-center text-lg-start">
                                        <h2 class="welcome-text mb-4">Selamat datang di Feeder Importer!</h2>
                                        <p class="text-muted">Aplikasi ini hadir sebagai wujud kontribusi <span
                                                class="fw-bold">PTKIS</span> melalui proyek <span
                                                class="fst-italic text-primary">Open Source</span> yang sepenuhnya
                                            gratis untuk digunakan. Kami berkomitmen untuk terus mengembangkan
                                            fitur-fitur terbaik guna membantu dan mempermudah tugas Anda dalam mengelola
                                            pelaporan data PDDIKTI.</p>
                                    </div>
                                    <form method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="form-label">Email Address</label>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}" required placeholder="yourname@domain.com">
                                            @error('email')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4">
                                            <div class="d-flex align-items-start">
                                                <div class="flex-grow-1">
                                                    <label class="form-label">Password</label>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <a href="#" class="text-muted small">Forgot password?</a>
                                                </div>
                                            </div>
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" required
                                                placeholder="••••••••">
                                            @error('password')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-4 form-check">
                                            <input class="form-check-input" type="checkbox" name="remember"
                                                id="remember" {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label text-muted" for="remember">Keep me logged
                                                in</label>
                                        </div>
                                        <div class="mt-4 pt-2">
                                            <button class="btn btn-primary w-100 waves-effect waves-light"
                                                type="submit">Sign In</button>
                                        </div>
                                    </form>

                                    <div class="mt-5 text-center text-lg-start">
                                        <p class="text-muted mb-0">Need access? <a href="#"
                                                class="text-primary fw-bold text-decoration-none">Contact Admin</a></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Right: 3D Image & SVG Backdrop -->
                            <div class="col-lg-6 order-1 order-lg-2 d-none d-lg-block">
                                <div class="login-right-content h-100">
                                    <div class="svg-container">
                                        <!-- Animated/3D Backdrop SVG -->
                                        <svg viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg"
                                            style="width: 110%; height: 110%; opacity: 0.4; margin: -5%;">
                                            <defs>
                                                <linearGradient id="warmGrad" x1="0%" y1="0%"
                                                    x2="100%" y2="100%">
                                                    <stop offset="0%" style="stop-color:#f5e6d3;stop-opacity:1" />
                                                    <stop offset="100%" style="stop-color:#dfccb8;stop-opacity:1" />
                                                </linearGradient>
                                            </defs>
                                            <path fill="url(#warmGrad)"
                                                d="M410.5,334.5Q371,419,279,431.5Q187,444,115.5,372Q44,300,75.5,204.5Q107,109,211.5,70.5Q316,32,383,141Q450,250,410.5,334.5Z">
                                                <animate attributeName="d" dur="15s" repeatCount="indefinite"
                                                    values="
                                                    M410.5,334.5Q371,419,279,431.5Q187,444,115.5,372Q44,300,75.5,204.5Q107,109,211.5,70.5Q316,32,383,141Q450,250,410.5,334.5Z;
                                                    M433.5,321Q402,392,328,427.5Q254,463,172.5,431.5Q91,400,60,285.5Q29,171,114.5,91Q200,11,310,61Q420,111,442.5,230.5Q465,350,433.5,321Z;
                                                    M385.5,340Q353,430,265,422.5Q177,415,116,346.5Q55,278,92,192.5Q129,107,226.5,74Q324,41,371,145.5Q418,250,385.5,340Z;
                                                    M410.5,334.5Q371,419,279,431.5Q187,444,115.5,372Q44,300,75.5,204.5Q107,109,211.5,70.5Q316,32,383,141Q450,250,410.5,334.5Z
                                                " />
                                            </path>
                                        </svg>
                                    </div>
                                    <img src="{{ asset('image/login_image.png') }}" alt="Login Image"
                                        class="login-image">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 text-center">
                <p class="text-muted small">©
                    <script>
                        document.write(new Date().getFullYear())
                    </script> Feeder Stainas. Crafted with <i class="mdi mdi-heart text-danger"></i>
                </p>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{ asset('templates/assets/libs/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('templates/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('templates/assets/libs/metismenu/metisMenu.min.js') }}"></script>
    <script src="{{ asset('templates/assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('templates/assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('templates/assets/libs/feather-icons/feather.min.js') }}"></script>
</body>

</html>
