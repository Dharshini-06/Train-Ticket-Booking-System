<!DOCTYPE HTML>

<html>
<head>
	<title>Online Railway Reservation System</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
	<link rel="stylesheet" href="includes/css/main.css" />
	<noscript><link rel="stylesheet" href="includes/css/noscript.css" /></noscript>

	<style>
		body {
			background: url("images/landscape-nature-2350-train-wallpaper-preview.jpg") no-repeat center center fixed;
			background-size: cover;
		}
		.role-form {
			background: rgba(255,255,255,0.95);
			max-width: 400px;
			margin: 60px auto;
			padding: 30px;
			border-radius: 12px;
			box-shadow: 0 0 15px rgba(0,0,0,0.4);
			text-align: center;
		}
		.role-form h2 {
			margin-bottom: 20px;
		}
		.role-form select, .role-form button {
			width: 100%;
			padding: 12px;
			margin-top: 15px;
			font-size: 16px;
			border-radius: 5px;
			border: 1px solid #ccc;
		}
	</style>
</head>
<body class="is-preload">



		
		<header id="header" class="alt">
			<h1><a href="index.php">Online Railway Reservation System</a></h1>
			<nav>
				<a href="#menu">Menu</a>
			</nav>
		</header>

	
		<nav id="menu">
			<div class="inner">
				<h2>Menu</h2>
				<ul class="links">
					<li><a href="index.php">Home</a></li>
					<li><a href="pass-login.php">Make Reservation</a></li>
					
					<li><a href="admin/emp-login.php">Admin Login</a></li>
				</ul>
				<a href="#" class="close">Close</a>
			</div>
		</nav>

		
		<section id="banner">
    <div class="inner">
        <div class="logo"><span class="icon solid fa-train"></span></div>
        <h2>Online Railway Reservation System</h2>
        <p>Best, Simple, and User Friendly Way To Reserve Train Tickets Effectively</p>

       
        <form method="post" action="redirect.php" style="margin-top: 2rem;">
            <h3 style="color: white;">SELECT DASHBOARD ROLE</h3>
            <select name="role" required style="padding: 10px; border-radius: 5px; width: 100%; max-width: 300px;">
                <option value="">-- Choose Role --</option>
                <option value="admin">Admin</option>
              
                <option value="passenger">Passenger</option>
            </select>
            <br /><br />
            <input type="submit" value="→ Go" style="padding: 12px 30px; border-radius: 6px; background-color: #007bff; color: white; border: none; cursor: pointer; font-size: 16px; transition: 0.3s;" 
       onmouseover="this.style.backgroundColor='#0056b3'" 
       onmouseout="this.style.backgroundColor='#007bff'" />

        </form>
    </div>
</section>

	<script src="includes/js/jquery.min.js"></script>
	<script src="includes/js/jquery.scrollex.min.js"></script>
	<script src="includes/js/browser.min.js"></script>
	<script src="includes/js/breakpoints.min.js"></script>
	<script src="includes/js/util.js"></script>
	<script src="includes/js/main.js"></script>

</body>
</html>
