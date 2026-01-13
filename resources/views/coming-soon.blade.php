<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Hacked - Board Member Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #055498;
            --secondary-blue: #123a60;
            --accent-red: #CE2028;
            --hack-red: #ff0000;
            --hack-dark: #0a0a0a;
            --hack-green: #00ff00;
            --text-dark: #0A0A0A;
            --white: #FFFFFF;
        }

        body {
            font-family: 'Orbitron', 'Montserrat', monospace;
            background: #000000;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
            color: #00ff00;
        }

        /* Matrix-style background */
        .matrix-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.1;
            z-index: 0;
            overflow: hidden;
        }

        .matrix-char {
            position: absolute;
            color: #00ff00;
            font-family: monospace;
            font-size: 14px;
            animation: matrix-fall linear infinite;
            opacity: 0.8;
        }

        @keyframes matrix-fall {
            to {
                transform: translateY(100vh);
            }
        }

        /* Glitch effect */
        .glitch {
            position: relative;
            animation: glitch 0.3s infinite;
        }

        @keyframes glitch {
            0% {
                text-shadow: 2px 0 #ff0000, -2px 0 #00ff00;
            }
            20% {
                text-shadow: -2px 0 #ff0000, 2px 0 #00ff00;
            }
            40% {
                text-shadow: 2px 0 #ff0000, -2px 0 #00ff00;
            }
            60% {
                text-shadow: -2px 0 #ff0000, 2px 0 #00ff00;
            }
            80% {
                text-shadow: 2px 0 #ff0000, -2px 0 #00ff00;
            }
            100% {
                text-shadow: -2px 0 #ff0000, 2px 0 #00ff00;
            }
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 50px 40px;
            max-width: 800px;
            width: 90%;
            animation: fadeInUp 1s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hack-icon {
            width: 180px;
            height: 180px;
            margin: 0 auto 40px;
            position: relative;
            animation: pulse-red 2s ease-in-out infinite;
        }

        @keyframes pulse-red {
            0%, 100% {
                transform: scale(1);
                filter: drop-shadow(0 0 20px rgba(255, 0, 0, 0.8));
            }
            50% {
                transform: scale(1.1);
                filter: drop-shadow(0 0 40px rgba(255, 0, 0, 1));
            }
        }

        .hack-icon::before {
            content: '⚠';
            font-size: 180px;
            display: block;
            line-height: 180px;
            color: #ff0000;
            animation: rotate-warning 3s linear infinite;
        }

        @keyframes rotate-warning {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(-10deg);
            }
            75% {
                transform: rotate(10deg);
            }
        }

        h1 {
            color: #ff0000;
            font-size: 4rem;
            margin-bottom: 20px;
            font-weight: 900;
            text-shadow: 
                0 0 10px #ff0000,
                0 0 20px #ff0000,
                0 0 30px #ff0000,
                0 0 40px #ff0000;
            letter-spacing: 3px;
            animation: glitch 0.3s infinite;
            font-family: 'Orbitron', monospace;
            text-transform: uppercase;
        }

        .subtitle {
            color: #00ff00;
            font-size: 1.6rem;
            margin-bottom: 40px;
            font-weight: 700;
            text-shadow: 0 0 10px #00ff00;
            font-family: 'Orbitron', monospace;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%, 50% {
                opacity: 1;
            }
            51%, 100% {
                opacity: 0.3;
            }
        }

        .message {
            color: #00ff00;
            font-size: 1.2rem;
            line-height: 1.8;
            margin-bottom: 40px;
            background: rgba(0, 0, 0, 0.8);
            padding: 30px;
            border-radius: 10px;
            border: 2px solid #ff0000;
            box-shadow: 
                0 0 20px rgba(255, 0, 0, 0.5),
                inset 0 0 20px rgba(255, 0, 0, 0.1);
            font-family: 'Montserrat', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .message::before {
            content: '>';
            position: absolute;
            left: 15px;
            top: 30px;
            color: #00ff00;
            font-size: 1.5rem;
            animation: blink 1s infinite;
        }

        .message p {
            margin-left: 30px;
        }

        .terminal-box {
            background: rgba(0, 0, 0, 0.9);
            padding: 35px;
            border-radius: 10px;
            margin-bottom: 35px;
            border: 2px solid #00ff00;
            box-shadow: 
                0 0 20px rgba(0, 255, 0, 0.5),
                inset 0 0 20px rgba(0, 255, 0, 0.1);
            font-family: 'Courier New', monospace;
            text-align: left;
            position: relative;
        }

        .terminal-header {
            color: #00ff00;
            font-size: 0.9rem;
            margin-bottom: 20px;
            border-bottom: 1px solid #00ff00;
            padding-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .terminal-line {
            color: #00ff00;
            font-size: 1rem;
            margin: 10px 0;
            font-family: 'Courier New', monospace;
            animation: typewriter 0.5s steps(40) both;
        }

        .terminal-line:nth-child(2) { animation-delay: 0.5s; }
        .terminal-line:nth-child(3) { animation-delay: 1s; }
        .terminal-line:nth-child(4) { animation-delay: 1.5s; }
        .terminal-line:nth-child(5) { animation-delay: 2s; }

        @keyframes typewriter {
            from {
                width: 0;
            }
            to {
                width: 100%;
            }
        }

        .cursor {
            display: inline-block;
            width: 10px;
            height: 20px;
            background: #00ff00;
            animation: blink-cursor 1s infinite;
            margin-left: 5px;
        }

        @keyframes blink-cursor {
            0%, 50% {
                opacity: 1;
            }
            51%, 100% {
                opacity: 0;
            }
        }

        .error-code {
            color: #ff0000;
            font-size: 2rem;
            font-weight: 900;
            margin: 20px 0;
            text-shadow: 0 0 10px #ff0000;
            font-family: 'Orbitron', monospace;
        }

        .contact-info {
            color: #00ff00;
            font-size: 1rem;
            margin-top: 30px;
            font-family: 'Montserrat', sans-serif;
            background: rgba(0, 0, 0, 0.6);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #00ff00;
        }

        .contact-info a {
            color: #ff0000;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            text-shadow: 0 0 10px #ff0000;
        }

        .contact-info a:hover {
            color: #00ff00;
            text-shadow: 0 0 20px #00ff00;
        }

        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: linear-gradient(to bottom, transparent, #00ff00, transparent);
            animation: scan 3s linear infinite;
            z-index: 10;
            pointer-events: none;
        }

        @keyframes scan {
            0% {
                top: 0;
                opacity: 1;
            }
            100% {
                top: 100%;
                opacity: 0;
            }
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2.5rem;
            }

            .subtitle {
                font-size: 1.2rem;
            }

            .message {
                font-size: 1rem;
                padding: 25px;
            }

            .hack-icon {
                width: 120px;
                height: 120px;
            }

            .hack-icon::before {
                font-size: 120px;
                line-height: 120px;
            }

            .terminal-box {
                padding: 25px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 2rem;
                letter-spacing: 1px;
            }

            .terminal-line {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="matrix-bg" id="matrixBg"></div>
    <div class="scan-line"></div>

    <div class="container">
        <div class="hack-icon"></div>
        <h1 class="glitch">SYSTEM HACKED</h1>
        <p class="subtitle">SECURITY BREACH DETECTED</p>
        
        <div class="message">
            <p><strong>WARNING:</strong> Unauthorized access detected. System security has been compromised. All services are temporarily unavailable.</p>
        </div>

        <div class="terminal-box">
            <div class="terminal-header">Security Terminal - Status Report</div>
            <div class="terminal-line">[ERROR] System integrity compromised<span class="cursor"></span></div>
            <div class="terminal-line">[WARNING] Unauthorized access detected<span class="cursor"></span></div>
            <div class="terminal-line">[ALERT] Security protocols activated<span class="cursor"></span></div>
            <div class="terminal-line">[STATUS] All services offline<span class="cursor"></span></div>
            <div class="terminal-line">[ACTION] Contact system administrator immediately<span class="cursor"></span></div>
        </div>

        <div class="error-code">
            ERROR CODE: 0x4B41434B
        </div>

        <div class="contact-info">
            <p>For assistance, contact: <a href="mailto:{{ config('mail.from.address', 'rolan.benavidez@gmail.com') }}">{{ config('mail.from.address', 'rolan.benavidez@gmail.com') }}</a></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Matrix background effect
            const matrixChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*()_+-=[]{}|;:,.<>?';
            const matrixBg = $('#matrixBg');
            
            function createMatrixChar() {
                const char = $('<div class="matrix-char">' + matrixChars[Math.floor(Math.random() * matrixChars.length)] + '</div>');
                char.css({
                    left: Math.random() * 100 + '%',
                    animationDuration: (Math.random() * 3 + 2) + 's',
                    animationDelay: Math.random() * 2 + 's'
                });
                matrixBg.append(char);
                
                setTimeout(() => {
                    char.remove();
                }, 5000);
            }
            
            // Create matrix characters periodically
            setInterval(createMatrixChar, 100);
            
            // Add random glitch effect to title
            setInterval(function() {
                $('h1').addClass('glitch');
                setTimeout(() => {
                    $('h1').removeClass('glitch');
                }, 200);
            }, 3000);
        });
    </script>
</body>
</html>
