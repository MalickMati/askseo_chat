<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 | ASKSEO</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        :root {
            --primary: #8E84FF;
            --secondary: #6526DE;
            --dark: #121212;
            --light: #f2f2f2;
            --glass: rgba(255, 255, 255, 0.05);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--dark);
            color: var(--light);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: 
                radial-gradient(circle at 25% 25%, rgba(142, 132, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(0, 184, 255, 0.1) 0%, transparent 50%);
            overflow: hidden;
        }
        
        .container {
            background: var(--glass);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                transparent,
                transparent,
                transparent,
                var(--primary),
                transparent,
                transparent,
                transparent
            );
            transform: rotate(30deg);
            z-index: -1;
            animation: shine 6s infinite;
        }
        
        @keyframes shine {
            0% { transform: rotate(30deg) translate(-10%, -10%); }
            50% { transform: rotate(30deg) translate(10%, 10%); }
            100% { transform: rotate(30deg) translate(-10%, -10%); }
        }
        
        h1 {
            font-size: 5rem;
            margin-bottom: 20px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        h2 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        p {
            margin-bottom: 30px;
            opacity: 0.8;
            line-height: 1.6;
        }
        
        .back-button {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border: none;
            color: var(--dark);
            padding: 12px 30px;
            font-size: 1rem;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(142, 132, 255, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .back-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 255, 157, 0.4);
        }
        
        .back-button:active {
            transform: translateY(0);
        }
        
        .back-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: 0.5s;
        }
        
        .back-button:hover::before {
            left: 100%;
        }
        
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
        }
        
        .particle {
            position: absolute;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 50%;
            filter: blur(2px);
            opacity: 0.3;
        }
    </style>
</head>
<body>
    <div class="particles" id="particles"></div>
    <div class="container">
        <h1>500</h1>
        <h2>Internal Server Error!</h2>
        <p>Looks like the server is not capable of fulfilling your request at the moment. You can contact the <strong style="color:black;">admin</strong> for feedback and we will look into the problem.</p>
        <button class="back-button" onclick="window.history.back()">Take Me Back</button>
    </div>

    <script>
        // Create particles
        const particlesContainer = document.getElementById('particles');
        const particleCount = 30;
        
        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');
            
            // Random size between 2px and 6px
            const size = Math.random() * 4 + 2;
            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            
            // Random position
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.top = `${Math.random() * 100}%`;
            
            // Random animation
            const duration = Math.random() * 20 + 10;
            const delay = Math.random() * 5;
            particle.style.animation = `float ${duration}s ease-in-out ${delay}s infinite alternate`;
            
            particlesContainer.appendChild(particle);
        }
        
        // Add floating animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0% { transform: translate(0, 0); }
                25% { transform: translate(${Math.random() * 50 - 25}px, ${Math.random() * 50 - 25}px); }
                50% { transform: translate(${Math.random() * 50 - 25}px, ${Math.random() * 50 - 25}px); }
                75% { transform: translate(${Math.random() * 50 - 25}px, ${Math.random() * 50 - 25}px); }
                100% { transform: translate(0, 0); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>