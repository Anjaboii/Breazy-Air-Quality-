<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Contact Us | BreazyAQI</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"/>
  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(to right, #a8edea, #fed6e3);
      overflow-x: hidden;
    }

    .contact-wrapper {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 60px 20px;
    }

    .contact-title {
      font-size: 40px;
      font-weight: 600;
      margin-bottom: 10px;
      color: #1b3a4b;
      text-align: center;
    }

    .contact-subtitle {
      font-size: 18px;
      color: #333;
      max-width: 600px;
      text-align: center;
      margin-bottom: 40px;
    }

    .contact-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 40px;
      width: 100%;
      max-width: 1100px;
    }

    .contact-card, .contact-form {
      background: rgba(255, 255, 255, 0.8);
      border-radius: 20px;
      padding: 30px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(10px);
      transition: transform 0.3s ease;
    }

    .contact-card:hover, .contact-form:hover {
      transform: translateY(-5px);
    }

    .contact-card h3 {
      margin-bottom: 10px;
      color: #00796b;
    }

    .contact-card p {
      margin: 0;
      font-size: 15px;
    }

    .contact-form form {
      display: flex;
      flex-direction: column;
    }

    .contact-form input,
    .contact-form textarea {
      font-family: inherit;
      padding: 12px 16px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 16px;
    }

    .contact-form button {
      background: #1b5e20;
      color: #fff;
      padding: 14px;
      border: none;
      border-radius: 10px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .contact-form button:hover {
      background: #2e7d32;
    }

    .map-embed {
      margin-top: 40px;
      width: 100%;
      max-width: 1100px;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    iframe {
      width: 100%;
      height: 300px;
      border: 0;
    }

    @media (max-width: 768px) {
      .contact-title {
        font-size: 32px;
      }
    }
  </style>
</head>
<body>

  <section class="contact-wrapper">
    <h1 class="contact-title">Contact Us</h1>
    <p class="contact-subtitle">Let us know how we can help you stay informed and safe with accurate air quality updates.</p>

    <div class="contact-container">
      <div class="contact-card">
        <h3>📍 Address</h3>
        <p>EcoTech Park, Block C<br>Greenfield City, Earthville</p>
        <h3>📞 Phone</h3>
        <p>+1 (800) 123-4567</p>
        <h3>📧 Email</h3>
        <p>support@breazyaqi.com</p>
      </div>

      <div class="contact-form">
        <form action="#" method="post">
          <input type="text" name="name" placeholder="Your Name" required />
          <input type="email" name="email" placeholder="Your Email" required />
          <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
          <button type="submit">Send Message</button>
        </form>
      </div>
    </div>

    <div class="map-embed">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!..." allowfullscreen="" loading="lazy"></iframe>
    </div>
  </section>

</body>
</html>
