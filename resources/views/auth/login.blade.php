<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/@webpixels/css/dist/index.css" rel="stylesheet">
    <title>Login to Handiworks Chat</title>

    <style>
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }

        .loading-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border-top: 3px solid #007bff;
            border-right: 3px solid transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="px-5 py-5 p-lg-0">
        <div class="d-flex justify-content-center">
            <div class="col-12 col-md-9 col-lg-7 min-h-lg-screen d-flex flex-column justify-content-center py-lg-16 px-lg-20 position-relative">
                <div class="row">
                    <div class="col-lg-10 col-md-9 col-xl-7 mx-auto">
                        <div class="text-center mb-12">
                            <span class="d-inline-block d-lg-block h1 mb-lg-6 me-3">👋</span>
                            <h1 class="ls-tight font-bolder h2">
                                Welcome back!
                            </h1>
                            <p class="mt-2">Start sending messages to anyone to build a contract.</p>
                        </div>
                        <form method="POST" action="{{ route('login') }}" id="login-form">
                            @csrf
                            <div class="mb-5">
                                <label class="form-label" for="email">Email address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Your email address" required>
                            </div>
                            <div class="mb-5">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" autocomplete="current-password" required>
                            </div>
                            <div class="mb-5">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="check_example" id="check_example">
                                    <label class="form-check-label" for="check_example">
                                        Keep me logged in
                                    </label>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary w-full" id="login-button">
                                    Sign in
                                </button>
                            </div>
                            @if ($errors->any())
                            <div class="mt-4">
                                <div class="text-danger">{{ $errors->first() }}</div>
                            </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="loading-overlay" id="loading-overlay">
        <div class="loading-spinner"></div>
    </div>

    <script>
        const loginForm = document.getElementById('login-form');
        const loginButton = document.getElementById('login-button');
        const loadingOverlay = document.getElementById('loading-overlay');

        loginForm.addEventListener('submit', function(event) {
            event.preventDefault();
            loadingOverlay.classList.add('active');
            loginButton.disabled = true;
            setTimeout(function() {
                loginForm.submit();
            }, 2000); // Simulate a delay before submitting the form (replace with your actual form submission logic)
        });
    </script>
</body>

</html>