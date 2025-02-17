<!DOCTYPE html>
<html lang="en" style="scroll-behavior: smooth;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EllavelCash - Smart Cashier System</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link href="css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <nav class="navbar navbar-expand-lg navbar-light shadow-sm sticky-top" style="background-color: rgba(188, 224, 255, 0.84);">
        <div class="container-fluid">
            <a class="navbar-brand text-primary font-weight-bold" href="#"><img style="width: 3.5rem;" src="{{asset('asset/img/logo.png')}}" alt=""></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link text-primary" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-primary" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link text-primary" href="#items">Items</a></li>
                    <li class="nav-item"><a class="nav-link text-primary" href="#contact">Contact</a></li>
                </ul>
                @auth
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link text-primary" href="{{route('home')}}">Dashboard</a></li>
                </ul>
                @endauth
                @guest
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link text-primary" href="{{route('login')}}">Login</a></li>
                    <li class="nav-item"><a class="nav-link text-primary" href="{{route('register')}}">Daftar</a></li>
                </ul>
                @endguest
            </div>
        </div>
    </nav>

    <header style="height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
    background: linear-gradient(to right, rgba(80, 120, 240, 0.8), rgba(34, 74, 190, 0.8)), 
                url({{ asset('asset/img/bg-landing.jpg') }});
        background-size: cover;
        background-position: center;">
        <div class="container-fluid">
            <h1 class="font-weight-bold">EllavelCash - Your Smart Cashier Solution</h1>
            <p class="lead">Manage your transactions efficiently with our modern cashier system</p>
            <a href="#features" class="btn btn-light btn-lg">Explore Features</a>
        </div>
    </header>


    <section id="features" class="py-5 bg-white text-center w-100">
        <div class="container">
            <h2 class="text-primary mb-4">Fitur</h2>
            <p class="lead">Empowering businesses with cutting-edge cashier solutions.</p>
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="icon-circle bg-primary text-white mb-3">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h4 class="card-title">Seamless Transactions</h4>
                            <p class="card-text">Fast and efficient transaction processing.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="icon-circle bg-primary text-white mb-3">
                                <i class="fas fa-chart-line fa-3x"></i>
                            </div>
                            <h4 class="card-title">Real-time Analytics</h4>
                            <p class="card-text">Get insights into sales and business performance.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="icon-circle bg-primary text-white mb-3">
                                <i class="fas fa-user-shield fa-3x"></i>
                            </div>
                            <h4 class="card-title">Secure & Reliable</h4>
                            <p class="card-text">Top-notch security for safe transactions.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .icon-circle {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto;
        }

        .card {
            border-radius: 15px;
            transition: transform 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
    </style>

    <section id="items" class="py-5 text-center w-100" style="background-color: rgba(107, 167, 236, 0.8) ;">
        <div class="container-fluid">
            <h2 class="text-light"><strong>Available Items</strong></h2>
            <p class="text-light">Check out our latest products.</p>
            <div class="row">
                @forelse($items as $item)
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm text-light" style="background-color: rgba(10, 128, 238, 0.84)">
                        <img src="{{ asset('storage/'.$item->photo) }}" class="card-img-top" alt="{{ $item->name }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $item->name }}</h5>
                            <p class="card-text">Price: ${{ number_format($item->price, 2) }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <p class="text-muted">No items available at the moment.</p>
                </div>
                @endforelse
            </div>
        </div>
    </section>

    <footer class="bg-white text-center py-3 w-100">
        <div class="container-fluid">
            <p class="text-primary mb-0">&copy; 2025 EllavelCash. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>