<?php
// Initialize session for form handling
session_start();

// JSON Database file paths
$consultationsFile = 'data/consultations.json';
$paymentsFile = 'data/payments.json';
$documentsFile = 'data/documents.json';

// Create data directory if it doesn't exist
if (!file_exists('data')) {
    mkdir('data', 0755, true);
}

// Initialize JSON files if they don't exist
if (!file_exists($consultationsFile)) {
    file_put_contents($consultationsFile, json_encode([]));
}
if (!file_exists($paymentsFile)) {
    file_put_contents($paymentsFile, json_encode([]));
}
if (!file_exists($documentsFile)) {
    file_put_contents($documentsFile, json_encode([]));
}

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Handle Consultation Booking
    if (isset($_POST['action']) && $_POST['action'] === 'book_consultation') {
        $consultation = [
            'id' => uniqid(),
            'name' => htmlspecialchars($_POST['name']),
            'email' => htmlspecialchars($_POST['email']),
            'phone' => htmlspecialchars($_POST['phone']),
            'service' => htmlspecialchars($_POST['service']),
            'date' => htmlspecialchars($_POST['consultation_date']),
            'time' => htmlspecialchars($_POST['consultation_time']),
            'message' => htmlspecialchars($_POST['consultation_message']),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $consultations = json_decode(file_get_contents($consultationsFile), true);
        $consultations[] = $consultation;
        file_put_contents($consultationsFile, json_encode($consultations, JSON_PRETTY_PRINT));
        
        $message = "परामर्श सफलतापूर्वक बुक किया गया! Consultation booked successfully! Booking ID: " . $consultation['id'];
        $messageType = 'success';
    }
    
    // Handle Payment
    if (isset($_POST['action']) && $_POST['action'] === 'process_payment') {
        $payment = [
            'id' => uniqid(),
            'name' => htmlspecialchars($_POST['payment_name']),
            'email' => htmlspecialchars($_POST['payment_email']),
            'amount' => htmlspecialchars($_POST['amount']),
            'service' => htmlspecialchars($_POST['payment_service']),
            'transaction_id' => 'TXN' . strtoupper(uniqid()),
            'status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $payments = json_decode(file_get_contents($paymentsFile), true);
        $payments[] = $payment;
        file_put_contents($paymentsFile, json_encode($payments, JSON_PRETTY_PRINT));
        
        $message = "भुगतान सफल! Payment successful! Transaction ID: " . $payment['transaction_id'];
        $messageType = 'success';
    }
    
    // Handle Document Upload
    if (isset($_POST['action']) && $_POST['action'] === 'upload_document') {
        $uploadDir = 'uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadedFiles = [];
        if (isset($_FILES['documents'])) {
            foreach ($_FILES['documents']['name'] as $key => $filename) {
                if ($_FILES['documents']['error'][$key] === UPLOAD_ERR_OK) {
                    $uniqueName = uniqid() . '_' . basename($filename);
                    $uploadPath = $uploadDir . $uniqueName;
                    
                    if (move_uploaded_file($_FILES['documents']['tmp_name'][$key], $uploadPath)) {
                        $uploadedFiles[] = $uniqueName;
                    }
                }
            }
        }
        
        $document = [
            'id' => uniqid(),
            'name' => htmlspecialchars($_POST['doc_name']),
            'email' => htmlspecialchars($_POST['doc_email']),
            'case_type' => htmlspecialchars($_POST['case_type']),
            'description' => htmlspecialchars($_POST['doc_description']),
            'files' => $uploadedFiles,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $documents = json_decode(file_get_contents($documentsFile), true);
        $documents[] = $document;
        file_put_contents($documentsFile, json_encode($documents, JSON_PRETTY_PRINT));
        
        $message = "दस्तावेज़ सफलतापूर्वक जमा किए गए! Documents submitted successfully! Submission ID: " . $document['id'];
        $messageType = 'success';
    }
}
?>

<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>कानूनी सहायता | Kanooni Sahayeta - Legal Services</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        :root {
            --primary: #1e40af;
            --secondary: #7c3aed;
            --accent: #f59e0b;
            --dark: #1f2937;
            --light: #f3f4f6;
            --success: #10b981;
            --error: #ef4444;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            overflow-x: hidden;
        }
        
        /* Smooth Scroll */
        html {
            scroll-behavior: smooth;
        }
        
        /* Navigation */
        nav {
            position: fixed;
            width: 100%;
            background: rgba(30, 64, 175, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 5%;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        nav.scrolled {
            padding: 0.5rem 5%;
            background: rgba(30, 64, 175, 1);
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-icon {
            font-size: 2rem;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .nav-links a:hover {
            color: var(--accent);
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent);
            transition: width 0.3s ease;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(135deg, #1e40af 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,165.3C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
            background-size: cover;
            animation: wave 10s ease-in-out infinite;
        }
        
        @keyframes wave {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .hero-content {
            text-align: center;
            color: white;
            z-index: 1;
            padding: 2rem;
            animation: fadeInUp 1s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .hero h1 {
            font-size: 4rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .hero .subtitle {
            font-size: 1.5rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .cta-button {
            background: var(--accent);
            color: white;
            padding: 1rem 3rem;
            border: none;
            border-radius: 50px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4);
        }
        
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.6);
        }
        
        /* Section Styles */
        section {
            padding: 5rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: var(--primary);
            position: relative;
            padding-bottom: 1rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 2px;
        }
        
        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .service-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }
        
        .service-card:hover::before {
            transform: scaleX(1);
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .service-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary);
        }
        
        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .service-card p {
            color: #666;
            line-height: 1.8;
        }
        
        /* Forms */
        .form-container {
            max-width: 700px;
            margin: 2rem auto;
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(30, 64, 175, 0.1);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            width: 100%;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 64, 175, 0.3);
        }
        
        /* Alert Messages */
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            animation: slideInDown 0.5s ease-out;
        }
        
        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #ef4444;
        }
        
        /* Stats Section */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        
        .stat-card {
            text-align: center;
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 15px;
            color: white;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: scale(1.05);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 5%;
            text-align: center;
        }
        
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .footer-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-links a:hover {
            color: var(--accent);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero .subtitle {
                font-size: 1.2rem;
            }
            
            .nav-links {
                display: none;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .form-container {
                padding: 1.5rem;
            }
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* File Upload Styling */
        .file-upload {
            border: 2px dashed var(--primary);
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-upload:hover {
            background: #f3f4f6;
            border-color: var(--secondary);
        }
        
        .file-upload input[type="file"] {
            display: none;
        }
        
        /* Tabs */
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            border: none;
            background: none;
            font-size: 1rem;
            font-weight: 600;
            color: #666;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .tab.active {
            color: var(--primary);
        }
        
        .tab.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav id="navbar">
        <div class="nav-container">
            <div class="logo">
                <span class="logo-icon">⚖️</span>
                <span>कानूनी सहायता</span>
            </div>
            <ul class="nav-links">
                <li><a href="#home">होम</a></li>
                <li><a href="#services">सेवाएं</a></li>
                <li><a href="#booking">बुकिंग</a></li>
                <li><a href="#payment">भुगतान</a></li>
                <li><a href="#documents">दस्तावेज़</a></li>
                <li><a href="#contact">संपर्क</a></li>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>कानूनी सहायता</h1>
            <p class="subtitle">आपके न्याय के लिए समर्पित | Dedicated to Your Justice</p>
            <p style="font-size: 1.2rem; margin-bottom: 2rem;">विशेषज्ञ कानूनी सलाह और प्रतिनिधित्व</p>
            <a href="#services" class="cta-button">हमारी सेवाएं देखें</a>
        </div>
    </section>

    <!-- Stats Section -->
    <section style="background: var(--light);">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number">500+</div>
                <div class="stat-label">खुश ग्राहक</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">95%</div>
                <div class="stat-label">सफलता दर</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">15+</div>
                <div class="stat-label">वर्षों का अनुभव</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24/7</div>
                <div class="stat-label">उपलब्ध</div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services">
        <h2 class="section-title">हमारी कानूनी सेवाएं | Our Legal Services</h2>
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">💰</div>
                <h3>वित्तीय कानून</h3>
                <h4>Financial Law</h4>
                <p>बैंकिंग विवाद, ऋण वसूली, वित्तीय धोखाधड़ी और निवेश संबंधी मामलों में विशेषज्ञ परामर्श। Banking disputes, loan recovery, financial fraud, and investment matters.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">🏛️</div>
                <h3>सिविल मामले</h3>
                <h4>Civil Matters</h4>
                <p>संपत्ति विवाद, अनुबंध मामले, उपभोक्ता अधिकार और नागरिक दायित्व। Property disputes, contract cases, consumer rights, and civil liabilities.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">🔒</div>
                <h3>साइबर अपराध</h3>
                <h4>Cyber Crime</h4>
                <p>ऑनलाइन धोखाधड़ी, डेटा चोरी, साइबर धमकी और डिजिटल अपराधों से सुरक्षा। Online fraud, data theft, cyber bullying, and digital crime protection.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">👨‍👩‍👧‍👦</div>
                <h3>पारिवारिक कानून</h3>
                <h4>Family Law</h4>
                <p>तलाक, गोद लेना, संरक्षकता, विरासत और पारिवारिक विवाद। Divorce, adoption, guardianship, inheritance, and family disputes.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">🛡️</div>
                <h3>POCSO संबंधी मामले</h3>
                <h4>POCSO Related</h4>
                <p>बच्चों के यौन अपराधों से संरक्षण अधिनियम के तहत संवेदनशील और गोपनीय कानूनी सहायता। Sensitive and confidential legal assistance under Protection of Children from Sexual Offences Act.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">📋</div>
                <h3>कानूनी परामर्श</h3>
                <h4>Legal Consultation</h4>
                <p>सभी कानूनी मामलों पर व्यक्तिगत परामर्श और मार्गदर्शन। Personal consultation and guidance on all legal matters.</p>
            </div>
        </div>
    </section>

    <!-- Booking Section -->
    <section id="booking" style="background: var(--light);">
        <h2 class="section-title">परामर्श बुक करें | Book Consultation</h2>
        
        <?php if ($message && $messageType): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="form-container">
            <form method="POST" action="#booking">
                <input type="hidden" name="action" value="book_consultation">
                
                <div class="form-group">
                    <label for="name">पूरा नाम | Full Name *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">ईमेल | Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">फ़ोन नंबर | Phone Number *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="service">सेवा चुनें | Select Service *</label>
                    <select id="service" name="service" required>
                        <option value="">चुनें...</option>
                        <option value="financial">वित्तीय कानून | Financial Law</option>
                        <option value="civil">सिविल मामले | Civil Matters</option>
                        <option value="cyber">साइबर अपराध | Cyber Crime</option>
                        <option value="family">पारिवारिक कानून | Family Law</option>
                        <option value="pocso">POCSO संबंधित | POCSO Related</option>
                        <option value="consultation">सामान्य परामर्श | General Consultation</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="consultation_date">परामर्श तिथि | Consultation Date *</label>
                    <input type="date" id="consultation_date" name="consultation_date" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="consultation_time">समय | Time *</label>
                    <input type="time" id="consultation_time" name="consultation_time" required>
                </div>
                
                <div class="form-group">
                    <label for="consultation_message">संदेश | Message</label>
                    <textarea id="consultation_message" name="consultation_message" rows="4" placeholder="कृपया अपने मामले का संक्षिप्त विवरण दें..."></textarea>
                </div>
                
                <button type="submit" class="submit-btn">परामर्श बुक करें | Book Consultation</button>
            </form>
        </div>
    </section>

    <!-- Payment Section -->
    <section id="payment">
        <h2 class="section-title">ऑनलाइन भुगतान | Online Payment</h2>
        
        <div class="form-container">
            <form method="POST" action="#payment">
                <input type="hidden" name="action" value="process_payment">
                
                <div class="form-group">
                    <label for="payment_name">नाम | Name *</label>
                    <input type="text" id="payment_name" name="payment_name" required>
                </div>
                
                <div class="form-group">
                    <label for="payment_email">ईमेल | Email *</label>
                    <input type="email" id="payment_email" name="payment_email" required>
                </div>
                
                <div class="form-group">
                    <label for="payment_service">सेवा | Service *</label>
                    <select id="payment_service" name="payment_service" required>
                        <option value="">चुनें...</option>
                        <option value="consultation">परामर्श शुल्क | Consultation Fee - ₹1,000</option>
                        <option value="financial">वित्तीय मामला | Financial Case - ₹5,000</option>
                        <option value="civil">सिविल मामला | Civil Case - ₹7,000</option>
                        <option value="cyber">साइबर अपराध | Cyber Crime - ₹8,000</option>
                        <option value="family">पारिवारिक कानून | Family Law - ₹6,000</option>
                        <option value="pocso">POCSO मामला | POCSO Case - ₹10,000</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="amount">राशि (₹) | Amount (₹) *</label>
                    <input type="number" id="amount" name="amount" required min="100">
                </div>
                
                <div class="form-group">
                    <label for="card_number">कार्ड नंबर | Card Number *</label>
                    <input type="text" id="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="expiry">समाप्ति तिथि | Expiry *</label>
                        <input type="text" id="expiry" placeholder="MM/YY" maxlength="5">
                    </div>
                    
                    <div class="form-group">
                        <label for="cvv">CVV *</label>
                        <input type="text" id="cvv" placeholder="123" maxlength="3">
                    </div>
                </div>
                
                <button type="submit" class="submit-btn">भुगतान करें | Pay Now</button>
                
                <p style="text-align: center; margin-top: 1rem; color: #666; font-size: 0.9rem;">
                    🔒 सुरक्षित भुगतान गेटवे | Secure Payment Gateway
                </p>
            </form>
        </div>
    </section>

    <!-- Document Upload Section -->
    <section id="documents" style="background: var(--light);">
        <h2 class="section-title">दस्तावेज़ जमा करें | Submit Documents</h2>
        
        <div class="form-container">
            <form method="POST" action="#documents" enctype="multipart/form-data">
                <input type="hidden" name="action" value="upload_document">
                
                <div class="form-group">
                    <label for="doc_name">नाम | Name *</label>
                    <input type="text" id="doc_name" name="doc_name" required>
                </div>
                
                <div class="form-group">
                    <label for="doc_email">ईमेल | Email *</label>
                    <input type="email" id="doc_email" name="doc_email" required>
                </div>
                
                <div class="form-group">
                    <label for="case_type">मामले का प्रकार | Case Type *</label>
                    <select id="case_type" name="case_type" required>
                        <option value="">चुनें...</option>
                        <option value="financial">वित्तीय | Financial</option>
                        <option value="civil">सिविल | Civil</option>
                        <option value="cyber">साइबर अपराध | Cyber Crime</option>
                        <option value="family">पारिवारिक | Family</option>
                        <option value="pocso">POCSO</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="doc_description">विवरण | Description *</label>
                    <textarea id="doc_description" name="doc_description" rows="4" required placeholder="अपने मामले और दस्तावेज़ों का विवरण दें..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>दस्तावेज़ अपलोड करें | Upload Documents *</label>
                    <div class="file-upload" onclick="document.getElementById('file-input').click()">
                        <p style="font-size: 2rem; margin-bottom: 0.5rem;">📄</p>
                        <p style="color: var(--primary); font-weight: 600;">क्लिक करके फ़ाइलें चुनें</p>
                        <p style="color: #666; font-size: 0.9rem;">PDF, DOC, DOCX, JPG, PNG (Max 10MB each)</p>
                        <input type="file" id="file-input" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;">
                    </div>
                    <p id="file-names" style="margin-top: 1rem; color: #666;"></p>
                </div>
                
                <button type="submit" class="submit-btn">दस्तावेज़ जमा करें | Submit Documents</button>
                
                <p style="text-align: center; margin-top: 1rem; color: #666; font-size: 0.9rem;">
                    🔒 सभी दस्तावेज़ गोपनीय रखे जाएंगे | All documents will be kept confidential
                </p>
            </form>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact">
        <h2 class="section-title">संपर्क करें | Contact Us</h2>
        
        <div style="max-width: 800px; margin: 0 auto; text-align: center;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 3rem;">
                <div style="padding: 2rem; background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">📞</div>
                    <h3>फ़ोन | Phone</h3>
                    <p style="color: var(--primary); font-weight: 600;">+91 1234567890</p>
                    <p>सोम-शुक्र: 9:00 AM - 6:00 PM</p>
                </div>
                
                <div style="padding: 2rem; background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">✉️</div>
                    <h3>ईमेल | Email</h3>
                    <p style="color: var(--primary); font-weight: 600;">info@kanoonisahayeta.com</p>
                    <p>24 घंटे में जवाब</p>
                </div>
                
                <div style="padding: 2rem; background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <div style="font-size: 2.5rem; margin-bottom: 1rem;">📍</div>
                    <h3>पता | Address</h3>
                    <p style="color: var(--primary); font-weight: 600;">123 Legal Street</p>
                    <p>Bhopal, Madhya Pradesh</p>
                </div>
            </div>
            
            <div style="margin-top: 3rem; padding: 2rem; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 15px; color: white;">
                <h3 style="font-size: 1.8rem; margin-bottom: 1rem;">आपातकालीन सहायता | Emergency Support</h3>
                <p style="font-size: 1.2rem; margin-bottom: 1rem;">24/7 उपलब्ध | Available 24/7</p>
                <p style="font-size: 1.5rem; font-weight: bold;">🚨 Helpline: +91 9876543210</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div style="margin-bottom: 2rem;">
                <h3 style="font-size: 2rem; margin-bottom: 1rem;">⚖️ कानूनी सहायता</h3>
                <p style="opacity: 0.8;">न्याय के लिए समर्पित | Dedicated to Justice</p>
            </div>
            
            <div class="footer-links">
                <a href="#home">होम</a>
                <a href="#services">सेवाएं</a>
                <a href="#booking">बुकिंग</a>
                <a href="#payment">भुगतान</a>
                <a href="#documents">दस्तावेज़</a>
                <a href="#contact">संपर्क</a>
            </div>
            
            <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 2rem; margin-top: 2rem;">
                <p>&copy; 2026 Kanooni Sahayeta. All Rights Reserved.</p>
                <p style="margin-top: 0.5rem; opacity: 0.7;">Developed with ❤️ for Justice</p>
            </div>
        </div>
    </footer>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // File upload display
        document.getElementById('file-input').addEventListener('change', function(e) {
            const fileNames = Array.from(e.target.files).map(file => file.name).join(', ');
            document.getElementById('file-names').textContent = fileNames ? `चयनित फ़ाइलें: ${fileNames}` : '';
        });
        
        // Form validation and enhancement
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const button = this.querySelector('.submit-btn');
                button.innerHTML = '<span class="loading"></span> जमा किया जा रहा है...';
                button.disabled = true;
            });
        });
        
        // Card number formatting
        const cardInput = document.getElementById('card_number');
        if (cardInput) {
            cardInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\s+/g, '');
                let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
                e.target.value = formattedValue;
            });
        }
        
        // Expiry date formatting
        const expiryInput = document.getElementById('expiry');
        if (expiryInput) {
            expiryInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });
        }
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
        
        // Service card click effect
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('click', function() {
                this.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
            });
        });
        
        // Animate stats on scroll
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.stat-card, .service-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>
