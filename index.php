<?php
$dbFile = "database.json";
$data = json_decode(file_get_contents($dbFile), true);
$message = "";

/* BOOK CONSULTATION */
if (isset($_POST['book'])) {
    $data['consultations'][] = [
        "name" => $_POST['name'],
        "email" => $_POST['email'],
        "service" => $_POST['service'],
        "issue" => $_POST['issue'],
        "time" => date("Y-m-d H:i:s")
    ];
    file_put_contents($dbFile, json_encode($data, JSON_PRETTY_PRINT));
    $message = "✅ Consultation booked successfully!";
}

/* UPLOAD DOCUMENT */
if (isset($_POST['upload'])) {
    $fileName = time() . "_" . basename($_FILES['document']['name']);
    move_uploaded_file($_FILES['document']['tmp_name'], "uploads/" . $fileName);

    $data['documents'][] = [
        "name" => $_POST['dname'],
        "email" => $_POST['demail'],
        "file" => $fileName,
        "time" => date("Y-m-d H:i:s")
    ];
    file_put_contents($dbFile, json_encode($data, JSON_PRETTY_PRINT));
    $message = "📄 Document uploaded securely!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Kanooni Sahayeta</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body{margin:0;font-family:Segoe UI;background:#f4f6f9}
header{background:linear-gradient(135deg,#1e3c72,#2a5298);color:#fff;padding:60px;text-align:center}
nav a{color:white;margin:0 10px;text-decoration:none;font-weight:600}
section{padding:60px 10%}
h2{color:#1e3c72}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px}
.card{background:#fff;border-radius:15px;padding:25px;box-shadow:0 10px 25px rgba(0,0,0,.1);transition:.3s}
.card:hover{transform:translateY(-8px)}
form input,form textarea,form select{width:100%;padding:12px;margin:10px 0;border-radius:8px;border:1px solid #ccc}
button{background:#1e3c72;color:white;border:none;padding:12px 30px;border-radius:25px;font-size:16px;cursor:pointer}
button:hover{background:#16325c}
footer{background:#1e3c72;color:white;text-align:center;padding:20px}
.msg{text-align:center;color:green;font-weight:bold}
img{width:100%;border-radius:15px}
</style>
</head>

<body>

<header>
<h1>Kanooni Sahayeta</h1>
<p>Your Trusted Partner in Justice</p>
<nav>
<a href="#services">Services</a>
<a href="#consult">Consultation</a>
<a href="#upload">Upload</a>
<a href="#payment">Payment</a>
</nav>
</header>

<?php if($message) echo "<p class='msg'>$message</p>"; ?>

<section id="services">
<h2>Our Legal Services</h2>
<div class="cards">
<div class="card">💼 <b>Financial Law</b><br>Bank fraud, loan disputes, financial recovery</div>
<div class="card">⚖️ <b>Civil Matters</b><br>Property disputes, agreements, notices</div>
<div class="card">💻 <b>Cyber Crime</b><br>Online fraud, hacking, digital abuse</div>
<div class="card">👨‍👩‍👧 <b>Family Law</b><br>Divorce, custody, maintenance</div>
<div class="card">🛡️ <b>POCSO</b><br>Child protection & sensitive legal handling</div>
</div>
</section>

<section>
<img src="https://images.unsplash.com/photo-1528740561666-dc2479dc08ab" alt="Law Firm">
</section>

<section id="consult">
<h2>Book a Consultation</h2>
<form method="post">
<input name="name" placeholder="Your Name" required>
<input type="email" name="email" placeholder="Email" required>
<select name="service">
<option>Financial Law</option>
<option>Civil Matter</option>
<option>Cyber Crime</option>
<option>Family Matter</option>
<option>POCSO</option>
</select>
<textarea name="issue" placeholder="Describe your legal issue"></textarea>
<button name="book">Book Consultation</button>
</form>
</section>

<section id="upload">
<h2>Secure Document Upload</h2>
<form method="post" enctype="multipart/form-data">
<input name="dname" placeholder="Your Name" required>
<input type="email" name="demail" placeholder="Email" required>
<input type="file" name="document" required>
<button name="upload">Upload Document</button>
</form>
</section>

<section id="payment">
<h2>Online Payment</h2>
<p>Consultation Fee: <b>₹500</b></p>
<button onclick="alert('Payment Gateway Integration Placeholder')">Pay Now</button>
</section>

<footer>
<p>© 2026 Kanooni Sahayeta | Justice with Compassion</p>
</footer>

</body>
</html>
