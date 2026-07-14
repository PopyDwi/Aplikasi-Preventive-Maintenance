<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            width: 100%;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-wrap: wrap;
        }

        .left-side {
            flex: 1 1 500px;
            background: linear-gradient(to bottom right, #3b82f6, #93c5fd);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .logo {
            width: 120px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 42px;
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .description {
            font-size: 18px;
            text-align: center;
            line-height: 1.8;
            max-width: 500px;
            margin-bottom: 40px;
        }

        .loginpage-image {
            width: 100%;
            max-width: 420px;
        }

        .right-side {
            flex: 1 1 500px;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .form-container {
            width: 100%;
            max-width: 420px;
        }

        .login-title {
            font-size: 42px;
            color: #005baa;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .login-text {
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .input {
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid #ccc;
            font-size: 15px;
            outline: none;
        }

        .password-box {
            position: relative;
            width: 100%;
        }

        .password-box .input {
            padding-right: 55px;
        }

        .password-box input::-ms-reveal,
        .password-box input::-ms-clear {
            display: none;
        }

        .password-box input::-webkit-credentials-auto-fill-button {
            visibility: hidden;
            display: none !important;
            pointer-events: none;
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password img {
            width: 22px;
            height: 22px;
        }

        .button {
            width: 100%;
            padding: 15px;
            background: #f4c542;
            color: #003b73;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            color: #888;
            font-size: 14px;
            width: 100%;
        }

        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }

            .left-side,
            .right-side {
                width: 100%;
                min-height: auto;
            }

            .left-side,
            .right-side {
                padding: 30px 20px;
            }

            .title,
            .login-title {
                font-size: 32px;
            }

            .description {
                font-size: 16px;
            }

            .loginpage-image {
                max-width: 300px;
                margin-top: 20px;
            }
        }

        @media (max-width: 576px) {
            .title,
            .login-title {
                font-size: 28px;
            }

            .input,
            .button {
                padding: 13px;
                font-size: 14px;
            }

            .password-box .input {
                padding-right: 50px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left-side">
            <img src="{{ asset('assets/logo_perumda.png') }}" class="logo" alt="Logo Perumda Tirta Musi">

            <h1 class="title">Sistem Preventive Maintenance</h1>

            <p class="description">
                Monitoring dan penentuan preventive maintenance aset mesin produksi air
                Perumda Tirta Musi Palembang.
            </p>

            <img src="{{ asset('assets/loginpage.png') }}" class="loginpage-image" alt="Ilustrasi Maintenance">
        </div>

        <div class="right-side">
            <div class="form-container">
                <h2 class="login-title">Login</h2>

                <p class="login-text">
                    Silakan masuk menggunakan akun yang telah diberikan.
                </p>

                @if(session('error'))
                    <div class="error-message">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="/login" method="POST">
                    @csrf

                    <div class="input-group">
                        <label>Username</label>
                        <input
                            type="text"
                            placeholder="Masukkan username"
                            class="input"
                            name="username"
                        >
                    </div>

                    <div class="input-group">
                        <label>Password</label>

                        <div class="password-box">
                            <input
                                type="password"
                                placeholder="Masukkan password"
                                class="input"
                                id="password"
                                name="password"
                            >

                            <span class="toggle-password" onclick="togglePassword()">
                                <img
                                    id="eyeIcon"
                                    src="https://cdn-icons-png.flaticon.com/512/709/709612.png"
                                    alt="Toggle Password"
                                >
                            </span>
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Login Sebagai</label>
                        <select class="input" name="role">
                            <option value="admin">Admin</option>
                            <option value="teknisi">Teknisi</option>
                        </select>
                    </div>

                    <button type="submit" class="button">Masuk</button>
                </form>

                <div class="footer">
                    Perumda Tirta Musi Palembang
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");

            if (password.type === "password") {
                password.type = "text";
                eyeIcon.src = "https://cdn-icons-png.flaticon.com/512/2767/2767146.png";
            } else {
                password.type = "password";
                eyeIcon.src = "https://cdn-icons-png.flaticon.com/512/709/709612.png";
            }
        }
    </script>
</body>
</html>