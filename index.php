<?php
$db = "database.json";
$data = json_decode(file_get_contents($db), true);
$alert = "";

/* BOOK CONSULTATION */
if (isset($_POST['book'])) {
    $data['consultations'][] = [
        "name" => $_POST['name'],
        "email" => $_POST['email'],
        "service" => $_POST['service'],
        "message" => $_POST['message'],
        "time" => date("Y-m-d H:i:s")
    ];
    file_put_contents($db, json_encode($data, JSON_PRETTY_PRINT));
    $alert = "Consultation booked successfully!";
}

/* DOCUMENT UPLOAD */
if (isset($_POST['upload'])) {
    $fileName = time() . "_" . $_FILES['doc']['name'];
    move_uploaded_file($_FILES['doc']['tmp_name'], "uploads/" . $fileName);

    $data['documents'][] = [
        "name" => $_POST['dname'],
        "email" => $_POST['demail'],
        "file" => $fileName,
        "time" => date("Y-m-d H:i:s")
    ];
    file_put_contents($db, json_encode($data, JSON_PRETTY_PRINT));
    $alert = "Document uploaded successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Kanooni Sahayeta</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{margin:0;font-family:'Segoe UI',sans-serif;background:#f5f7fa}
header{
  background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
  color:#fff;text-align:center;padding:70px 20px
}
nav a{color:#fff;margin:0 12px;text-decoration:none;font-weight:600}
section{padding:60px 10%}
h2{color:#203a43}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:25px}
.card{
  background:#fff;border-radius:18px;padding:25px;
  box-shadow:0 12px 30px rgba(0,0,0,.1);
  transition:.3s
}
.card:hover{transform:translateY(-8px)}
form input,form textarea,form select{
  width:100%;padding:12px;margin:10px 0;
  border-radius:8px;border:1px solid #ccc
}
button{
  background:#203a43;color:white;border:none;
  padding:12px 28px;border-radius:25px;
  font-size:16px;cursor:pointer
}
button:hover{background:#0f2027}
.hero-img{width:100%;border-radius:20px;margin-top:30px}
.notice{text-align:center;color:green;font-weight:bold}
footer{
  background:#0f2027;color:white;
  text-align:center;padding:20px
}
</style>
</head>

<body>

<header>
<h1>Kanooni Sahayeta</h1>
<p>Justice | Trust | Legal Support</p>
<nav>
<a href="#services">Services</a>
<a href="#consult">Consultation</a>
<a href="#upload">Documents</a>
<a href="#payment">Payment</a>
</nav>
</header>

<?php if($alert) echo "<p class='notice'>$alert</p>"; ?>

<section>
<img class="hero-img"
src="https://images.unsplash.com/photo-1528740561666-dc2479dc08ab"
alt="Law Firm">
</section>

<section id="services">
<h2>Our Legal Services</h2>
<div class="cards">
<div class="card">💼 <b>Financial Law</b><br>Banking disputes, financial fraud, recovery cases</div>
<div class="card">⚖️ <b>Civil Matters</b><br>Property disputes, agreements, civil litigation</div>
<div class="card">💻 <b>Cyber Crime</b><br>Online fraud, identity theft, cyber harassment</div>
<div class="card">👨‍👩‍👧 <b>Family Matters</b><br>Divorce, custody, maintenance & mediation</div>
<div class="card">🛡️ <b>POCSO</b><br>Child protection cases handled sensitively</div>
</div>
</section>

<section id="consult">
<h2>Book a Legal Consultation</h2>
<form method="post">
<input name="name" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email Address" required>
<select name="service">
<option>Financial Law</option>
<option>Civil Matter</option>
<option>Cyber Crime</option>
<option>Family Matter</option>
<option>POCSO</option>
</select>
<textarea name="message" placeholder="Briefly explain your legal issue"></textarea>
<button name="book">Book Consultation</button>
</form>
</section>

<section id="upload">
<h2>Submit Legal Documents</h2>
<form method="post" enctype="multipart/form-data">
<input name="dname" placeholder="Your Name" required>
<input type="email" name="demail" placeholder="Email" required>
<input type="file" name="doc" required>
<button name="upload">Submit Document</button>
</form>
</section>

<section id="payment">
<h2>Online Payment</h2>
<p>Consultation Fee: <strong>₹500</strong></p>
<button onclick="alert('Payment gateway integration placeholder')">
Pay Securely
</button>
</section>

<footer>
<p>© 2026 Kanooni Sahayeta | Legal Help Made Accessible</p>
</footer>

</body>
</html>
