<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Admin Dashboard</title>
        <style>
            * { box-sizing: border-box; }
            html, body {
                margin: 0;
                height: 100%;
                font-family: "Segoe UI", Arial, sans-serif;
                background: #edf2f7;
                color: #1f2937;
            }

            body {
                min-height: 100vh;
            }

            .dashboard {
                display: flex;
                min-height: 100vh;
                width: 100%;
                background: #edf2f7;
            }

            .sidebar {
                width: 300px;
                background: #f8fafc;
                border-right: 1px solid #d7e0eb;
                padding: 18px 16px 20px;
                display: flex;
                flex-direction: column;
            }

            .avatar-wrap {
                display: flex;
                justify-content: center;
                padding: 8px 0 18px;
            }

            .avatar {
                width: 118px;
                height: 118px;
                border-radius: 50%;
                border: 3px solid #2c3e50;
                background: linear-gradient(135deg, #ffffff 0%, #e5eef9 100%);
                box-shadow: inset 0 0 0 10px #f8fafc;
            }

            .nav {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-top: 12px;
            }

            .nav-item {
                display: block;
                width: 100%;
                padding: 12px 14px;
                border: 2px solid #3b82f6;
                border-radius: 8px;
                background: #eef4ff;
                color: #1f2937;
                font-size: 22px;
                font-weight: 500;
                text-decoration: none;
                transition: all 0.2s ease;
            }

            .nav-item:hover {
                background: #dfeeff;
            }

            .nav-item.active {
                background: #dfeeff;
                box-shadow: inset 0 0 0 1px rgba(59,130,246,0.2);
            }

            .sidebar-footer {
                margin-top: auto;
                padding-top: 18px;
            }

            .logout-btn {
                width: 100%;
                border: none;
                border-radius: 8px;
                background: #1f2937;
                color: #fff;
                padding: 12px 16px;
                font-size: 18px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.2s ease;
            }

            .logout-btn:hover {
                background: #111827;
            }

            .content {
                flex: 1;
                padding: 28px 30px;
            }

            .content-card {
                background: #ffffff;
                border: 1px solid #dfe8f3;
                border-radius: 16px;
                min-height: 420px;
                box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
            }
        </style>
    </head>
    <body>
        <div class="dashboard">
            <aside class="sidebar">
                <div class="avatar-wrap">
                    <div class="avatar" aria-label="Profile avatar"></div>
                </div>

                <nav class="nav" aria-label="Sidebar navigation">
                    <a href="#" class="nav-item active">Dashboard</a>
                    <a href="#" class="nav-item">Censuses</a>
                    <a href="#" class="nav-item">Home_hole</a>
                    <a href="#" class="nav-item">Family</a>
                    <a href="#" class="nav-item">Year</a>
                    <a href="#" class="nav-item">School</a>
                    <a href="{{ route('group-school.create') }}" class="nav-item">Group_school</a>
                    <a href="#" class="nav-item">Address</a>
                </nav>

                <div class="sidebar-footer">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">Log out user</button>
                    </form>
                </div>
            </aside>

            <main class="content">
                <div class="content-card"></div>
            </main>
        </div>
    </body>
</html>
