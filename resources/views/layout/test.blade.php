<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Sidebar</title>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            min-height: 100vh;
            display: flex;
        }

        .sidebar-toggle {
            flex-shrink: 0;
            width: 250px;
            background: #343a40;
            color: #fff;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-link {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: flex;
            align-items: center;
        }

        .sidebar-link:hover {
            background: #495057;
            color: #fff;
        }

        .sidebar-item {
            list-style: none;
        }

        .sidebar-dropdown {
            padding-left: 20px;
        }

        .main {
            flex-grow: 1;
            background: #f8f9fa;
            
        }

        .navbar {
            background: #343a40;
            color: white;
            padding: 10px;
        }

        .toggler-btn {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
        }

        .sidebar-footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            background: #212529;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-toggle">
            <div class="sidebar-logo text-center p-3">
                <a href="#" class="text-white text-decoration-none">Product Management</a>
            </div>
            <!-- Sidebar Navigation -->
            <ul class="sidebar-nav p-0">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#dashboardMenu" aria-expanded="false">
                        <i class="lni lni-dashboard"></i> <span>Dashboard</span>
                    </a>
                    <ul id="dashboardMenu" class="collapse sidebar-dropdown" data-bs-parent=".sidebar-nav">
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#dashboard1Menu" aria-expanded="false">
                                <i class="lni lni-layers"></i> <span>Dashboard1</span>
                            </a>
                            <ul id="dashboard1Menu" class="collapse sidebar-dropdown" data-bs-parent="#dashboardMenu">
                                <li class="sidebar-item">
                                    <a href="http://127.0.0.1:8009/#" class="sidebar-link">
                                        <i class="lni lni-grid-alt"></i> <span>Dashboard2</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed" data-bs-toggle="collapse" data-bs-target="#authMenu" aria-expanded="false">
                        <i class="lni lni-lock"></i> <span>Auth</span>
                    </a>
                    <ul id="authMenu" class="collapse sidebar-dropdown" data-bs-parent=".sidebar-nav">
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">Login</a>
                        </li>
                        <li class="sidebar-item">
                            <a href="#" class="sidebar-link">Register</a>
                        </li>
                    </ul>
                </li>
            </ul>

            <div class="sidebar-footer p-3">
                <a href="#" class="sidebar-link"><i class="lni lni-exit"></i> <span>Logout</span></a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main">
            <nav class="navbar">
                <button class="toggler-btn" onclick="document.getElementById('sidebar').classList.toggle('d-none')">
                    <i class="lni lni-menu"></i>
                </button>
                <span class="navbar-brand ms-3">Responsive Sidebar</span>
            </nav>
            <main class="p-4">
                <div class="container">
                    <h1>Welcome to the Dashboard</h1>
                    <h1>Welcome to the Dashboard</h1>
                    <h1>Welcome to the Dashboard</h1>
                    <h1>Welcome to the Dashboard</h1>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
