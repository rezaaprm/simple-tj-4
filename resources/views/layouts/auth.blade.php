<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'TransJakarta - Login')</title>

        <!-- Bootstrap 5 -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome 6 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

        <style>
            :root {
                --primary-500: #0D6EFD;
                --primary-600: #0A58CA;
                --primary-default: #0D6EFD;
            }

            body {
                min-height: 100vh;
            }

            #auth-left {
                min-height: 100vh;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                color: #ffffff;
            }

            #auth-left h1 {
                font-weight: 700;
                color: #ffffff;
            }

            #auth-left p {
                font-weight: 400;
                color: #ffffff;
            }

            #auth-right {
                min-height: 100vh;
            }

            #auth-card {
                width: 95%;
                border-radius: .5rem;
                box-shadow: 0 5px 50px rgba(0, 0, 0, 0.2);
            }

            .form-label {
                font-weight: 600;
            }

            .form-control,
            .form-select {
                border-radius: .5rem;
                padding: .55rem;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: var(--primary-default);
                box-shadow: 0 0 0 .1rem rgba(13, 110, 253, .25);
            }

            .btn-login {
                font-weight: 600;
                border-radius: .5rem;
                padding: .55rem;
            }

            .btn-warning:hover {
                background-color: #e0a800;
                border-color: #d39e00;
            }

            .alert-danger {
                border-radius: .5rem;
            }

            @media (max-width: 768px) {
                #auth-left {
                    min-height: 200px;
                }

                #auth-right {
                    min-height: auto;
                }
            }
        </style>

        @stack('styles')
    </head>

    <body>
        <main class="container-fluid">
            <div class="row min-vh-100">
                <!-- Left Side - Gambar dengan dynamic background -->
                <div class="col-md-8 d-flex flex-column align-items-center justify-content-center p-0" id="auth-left"
                    style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('@yield('banner', asset('image/hero-yamaha-1.jpeg'))');">
                    <div class="text-center">
                        <h1 class="mb-3">TransJakarta</h1>
                        <p class="lead mb-3">
                            Sistem Transportasi Publik Terintegrasi<br>
                            untuk Jakarta yang Lebih Baik
                        </p>
                    </div>
                </div>

                <!-- Right Side - Form -->
                <div class="col-md-4 d-flex flex-column align-items-center justify-content-center p-0" id="auth-right">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
        <!-- Font Awesome JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js"></script>

        @if (session('success'))
            <script>
                alert("{{ session('success') }}");
            </script>
        @endif

        @if (session('error'))
            <script>
                alert("{{ session('error') }}");
            </script>
        @endif

        @stack('scripts')
    </body>

</html>
