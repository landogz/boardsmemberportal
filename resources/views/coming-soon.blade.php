<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coming Soon - Board Member Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-blue: #055498;
            --secondary-blue: #123a60;
            --accent-yellow: #FBD116;
            --accent-red: #CE2028;
            --accent-purple: #7C3AED;
            --bg-light: #F9FAFB;
            --text-dark: #0A0A0A;
            --white: #FFFFFF;
        }

        body {
            font-family: 'Montserrat', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #055498 0%, #123a60 50%, #055498 100%);
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            position: relative;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .background-animation {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            animation: float 20s infinite ease-in-out;
            backdrop-filter: blur(2px);
        }

        .circle:nth-child(1) {
            width: 400px;
            height: 400px;
            top: -200px;
            left: -200px;
            animation-delay: 0s;
            background: rgba(251, 209, 22, 0.1);
        }

        .circle:nth-child(2) {
            width: 300px;
            height: 300px;
            bottom: -150px;
            right: -150px;
            animation-delay: 5s;
            background: rgba(124, 58, 237, 0.1);
        }

        .circle:nth-child(3) {
            width: 250px;
            height: 250px;
            top: 50%;
            right: -125px;
            animation-delay: 10s;
            background: rgba(206, 32, 40, 0.1);
        }

        .circle:nth-child(4) {
            width: 200px;
            height: 200px;
            bottom: 20%;
            left: -100px;
            animation-delay: 15s;
            background: rgba(5, 84, 152, 0.15);
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) scale(1) rotate(0deg);
            }
            33% {
                transform: translate(30px, -30px) scale(1.1) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9) rotate(240deg);
            }
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 50px 40px;
            max-width: 700px;
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

        .maintenance-icon {
            width: 140px;
            height: 140px;
            margin: 0 auto 40px;
            position: relative;
            animation: rotatePulse 4s ease-in-out infinite;
        }

        @keyframes rotatePulse {
            0%, 100% {
                transform: rotate(0deg) scale(1);
            }
            25% {
                transform: rotate(90deg) scale(1.1);
            }
            50% {
                transform: rotate(180deg) scale(1);
            }
            75% {
                transform: rotate(270deg) scale(1.1);
            }
        }

        .maintenance-icon::before {
            content: '⚙';
            font-size: 140px;
            display: block;
            line-height: 140px;
            filter: drop-shadow(0 0 20px rgba(251, 209, 22, 0.5));
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from {
                filter: drop-shadow(0 0 20px rgba(251, 209, 22, 0.5));
            }
            to {
                filter: drop-shadow(0 0 30px rgba(251, 209, 22, 0.8));
            }
        }

        h1 {
            color: #ffffff;
            font-size: 3.5rem;
            margin-bottom: 20px;
            font-weight: 700;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            letter-spacing: -0.5px;
            animation: slideInLeft 1s ease-out 0.3s both;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .subtitle {
            color: #ffffff;
            font-size: 1.4rem;
            margin-bottom: 40px;
            font-weight: 400;
            opacity: 0.95;
            animation: slideInRight 1s ease-out 0.5s both;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .message {
            color: #ffffff;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 40px;
            background: rgba(255, 255, 255, 0.12);
            padding: 30px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeIn 1s ease-out 0.7s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .countdown {
            background: rgba(255, 255, 255, 0.15);
            padding: 35px;
            border-radius: 20px;
            margin-bottom: 35px;
            backdrop-filter: blur(15px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: scaleIn 0.8s ease-out 0.9s both;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .countdown-label {
            color: #ffffff;
            font-size: 1.1rem;
            margin-bottom: 20px;
            opacity: 0.95;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .countdown-timer {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .time-unit {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(255, 255, 255, 0.15) 100%);
            padding: 20px 25px;
            border-radius: 15px;
            min-width: 90px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            animation: bounceIn 0.6s ease-out both;
        }

        .time-unit:nth-child(1) { animation-delay: 1s; }
        .time-unit:nth-child(2) { animation-delay: 1.1s; }
        .time-unit:nth-child(3) { animation-delay: 1.2s; }
        .time-unit:nth-child(4) { animation-delay: 1.3s; }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3) translateY(50px);
            }
            50% {
                opacity: 1;
                transform: scale(1.05) translateY(-10px);
            }
            70% {
                transform: scale(0.95) translateY(5px);
            }
            100% {
                transform: scale(1) translateY(0);
            }
        }

        .time-unit:hover {
            transform: translateY(-5px) scale(1.05);
            background: linear-gradient(135deg, rgba(251, 209, 22, 0.3) 0%, rgba(251, 209, 22, 0.2) 100%);
            box-shadow: 0 10px 30px rgba(251, 209, 22, 0.3);
        }

        .time-value {
            color: #ffffff;
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            font-variant-numeric: tabular-nums;
        }

        .time-label {
            color: #ffffff;
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .contact-info {
            color: #ffffff;
            font-size: 1rem;
            opacity: 0.9;
            margin-top: 30px;
            animation: fadeIn 1s ease-out 1.4s both;
        }

        .contact-info a {
            color: #FBD116;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .contact-info a:hover {
            color: #ffffff;
            border-bottom-color: #FBD116;
            text-shadow: 0 0 10px rgba(251, 209, 22, 0.5);
        }

        .pulse-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            background: #FBD116;
            border-radius: 50%;
            margin: 0 8px;
            animation: pulse 2s ease-in-out infinite;
            box-shadow: 0 0 0 0 rgba(251, 209, 22, 0.7);
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(251, 209, 22, 0.7);
            }
            50% {
                transform: scale(1.2);
                box-shadow: 0 0 0 10px rgba(251, 209, 22, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(251, 209, 22, 0);
            }
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2.2rem;
            }

            .subtitle {
                font-size: 1.1rem;
            }

            .message {
                font-size: 1rem;
                padding: 25px;
            }

            .maintenance-icon {
                width: 100px;
                height: 100px;
            }

            .maintenance-icon::before {
                font-size: 100px;
                line-height: 100px;
            }

            .countdown {
                padding: 25px;
            }

            .countdown-timer {
                gap: 10px;
            }

            .time-unit {
                min-width: 70px;
                padding: 15px 20px;
            }

            .time-value {
                font-size: 2rem;
            }

            .time-label {
                font-size: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 1.8rem;
            }

            .time-unit {
                min-width: 60px;
                padding: 12px 15px;
            }

            .time-value {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="background-animation">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="container">
        <div class="maintenance-icon"></div>
        <h1>We're Launching Soon! <span class="pulse-dot"></span></h1>
        <p class="subtitle">Get ready for an amazing experience</p>
        
        <div class="message">
            <p>We're putting the finishing touches on our new Board Member Portal. Stay tuned for an exciting launch that will transform how you manage board operations, meetings, and collaboration.</p>
        </div>

        <div class="countdown">
            <div class="countdown-label">Launch Countdown:</div>
            <div class="countdown-timer">
                <div class="time-unit">
                    <span class="time-value" id="days">00</span>
                    <span class="time-label">Days</span>
                </div>
                <div class="time-unit">
                    <span class="time-value" id="hours">00</span>
                    <span class="time-label">Hours</span>
                </div>
                <div class="time-unit">
                    <span class="time-value" id="minutes">00</span>
                    <span class="time-label">Minutes</span>
                </div>
                <div class="time-unit">
                    <span class="time-value" id="seconds">00</span>
                    <span class="time-label">Seconds</span>
                </div>
            </div>
        </div>

        <div class="contact-info">
            <p>Have questions? Contact us at <a href="mailto:{{ config('mail.from.address', 'rolan.benavidez@gmail.com') }}">{{ config('mail.from.address', 'rolan.benavidez@gmail.com') }}</a></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Get launch date from config or use default
            const launchDate = '{{ config("app.launch_date", "2026-01-20") }}';
            const endTime = new Date(launchDate).getTime();
            
            function updateCountdown() {
                const now = new Date().getTime();
                const distance = endTime - now;

                if (distance < 0) {
                    $('#days').text('00');
                    $('#hours').text('00');
                    $('#minutes').text('00');
                    $('#seconds').text('00');
                    $('.countdown-label').text('We\'re live! Welcome to Board Member Portal!');
                    return;
                }

                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Add animation on value change
                $('.time-value').each(function() {
                    if ($(this).text() !== String($(this).data('prev') || '').padStart(2, '0')) {
                        $(this).css('transform', 'scale(1.2)');
                        setTimeout(() => {
                            $(this).css('transform', 'scale(1)');
                        }, 200);
                    }
                    $(this).data('prev', $(this).text());
                });

                $('#days').text(String(days).padStart(2, '0'));
                $('#hours').text(String(hours).padStart(2, '0'));
                $('#minutes').text(String(minutes).padStart(2, '0'));
                $('#seconds').text(String(seconds).padStart(2, '0'));
            }

            // Update countdown every second
            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    </script>
</body>
</html>

