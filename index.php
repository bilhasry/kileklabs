<?php
$pageTitle = "KilekLabs.com - Low-Profile Software Development";
$pageDescription = "Where ideas strike like lightning. Building tools, writing code, and experimenting with technology — one thunderbolt at a time.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=Roboto+Mono:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main id="main-content">
    <section class="hero" aria-labelledby="hero-heading">
        <div class="hero-lightning" aria-hidden="true">
            <canvas id="lightning-canvas"></canvas>
            <canvas id="particles-canvas"></canvas>
        </div>
        
        <!-- Floating Glass Cards -->
        <div class="glass-float float-1 glass-card" aria-hidden="true"></div>
        <div class="glass-float float-2 glass-card" aria-hidden="true"></div>
        <div class="glass-float float-3 glass-card" aria-hidden="true"></div>
        
        <div class="container hero-content">
            <h1 id="hero-heading">Building tools &amp; experimenting with technology</h1>
            <p>From side projects to deep technical explorations, everything here is driven by curiosity and the desire to create something meaningful.</p>
        </div>
    </section>
    </main>

    <script>
        // Canvas Lightning Effect - Pure 2D
        const canvas = document.getElementById('lightning-canvas');
        const ctx = canvas.getContext('2d');

        // Particle System
        const particlesCanvas = document.getElementById('particles-canvas');
        const particlesCtx = particlesCanvas.getContext('2d');

        function resizeCanvas() {
            canvas.width = canvas.clientWidth;
            canvas.height = canvas.clientHeight;
            particlesCanvas.width = particlesCanvas.clientWidth;
            particlesCanvas.height = particlesCanvas.clientHeight;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        // Particle class
        class Particle {
            constructor() {
                this.reset();
            }

            reset() {
                this.x = Math.random() * particlesCanvas.width;
                this.y = Math.random() * particlesCanvas.height;
                this.size = Math.random() * 2 + 0.5;
                this.speedX = (Math.random() - 0.5) * 0.5;
                this.speedY = (Math.random() - 0.5) * 0.5;
                this.opacity = Math.random() * 0.5 + 0.2;
                this.pulseSpeed = Math.random() * 0.02 + 0.01;
                this.pulsePhase = Math.random() * Math.PI * 2;
            }

            update() {
                this.x += this.speedX;
                this.y += this.speedY;
                this.pulsePhase += this.pulseSpeed;

                // Wrap around edges
                if (this.x < 0) this.x = particlesCanvas.width;
                if (this.x > particlesCanvas.width) this.x = 0;
                if (this.y < 0) this.y = particlesCanvas.height;
                if (this.y > particlesCanvas.height) this.y = 0;
            }

            draw() {
                const pulseOpacity = this.opacity * (0.5 + 0.5 * Math.sin(this.pulsePhase));
                particlesCtx.beginPath();
                particlesCtx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                particlesCtx.fillStyle = `rgba(0, 200, 255, ${pulseOpacity})`;
                particlesCtx.fill();
            }
        }

        // Create particles
        const particles = [];
        const particleCount = 50;
        for (let i = 0; i < particleCount; i++) {
            particles.push(new Particle());
        }

        function animateParticles() {
            particlesCtx.clearRect(0, 0, particlesCanvas.width, particlesCanvas.height);
            
            for (const particle of particles) {
                particle.update();
                particle.draw();
            }

            // Draw connections between nearby particles
            for (let i = 0; i < particles.length; i++) {
                for (let j = i + 1; j < particles.length; j++) {
                    const dx = particles[i].x - particles[j].x;
                    const dy = particles[i].y - particles[j].y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < 100) {
                        const opacity = (1 - distance / 100) * 0.15;
                        particlesCtx.beginPath();
                        particlesCtx.moveTo(particles[i].x, particles[i].y);
                        particlesCtx.lineTo(particles[j].x, particles[j].y);
                        particlesCtx.strokeStyle = `rgba(0, 200, 255, ${opacity})`;
                        particlesCtx.lineWidth = 0.5;
                        particlesCtx.stroke();
                    }
                }
            }
        }

        class LightningBolt {
            constructor() {
                this.segments = [];
                this.branches = [];
                this.life = 0;
                this.maxLife = 0.3;
                this.opacity = 1;
                this.generate();
            }

            generate() {
                const startX = Math.random() * canvas.width;
                const startY = 0;
                const endY = canvas.height;
                const segmentCount = 15;

                let x = startX;
                let y = startY;

                this.segments.push({ x, y });

                for (let i = 0; i < segmentCount; i++) {
                    const progress = i / segmentCount;
                    const jitter = (1 - progress) * 80;
                    x += (Math.random() - 0.5) * jitter;
                    y += (canvas.height / segmentCount);
                    this.segments.push({ x, y });

                    // Add branches
                    if (Math.random() < 0.25) {
                        this.addBranch(x, y, progress);
                    }
                }
            }

            addBranch(x, y, progress) {
                const branchLength = (1 - progress) * 100;
                const branchSegments = 4;
                const angle = (Math.random() - 0.5) * Math.PI * 0.6;

                let bx = x;
                let by = y;
                const branch = [{ x: bx, y: by }];

                for (let i = 0; i < branchSegments; i++) {
                    bx += Math.sin(angle) * (branchLength / branchSegments);
                    by += Math.cos(angle) * (branchLength / branchSegments);
                    branch.push({ x: bx, y: by });
                }

                this.branches.push(branch);
            }

            update(deltaTime) {
                this.life += deltaTime;
                this.opacity = 1 - (this.life / this.maxLife);
                return this.life < this.maxLife;
            }

            draw() {
                if (this.opacity <= 0) return;

                // Main bolt glow
                ctx.beginPath();
                ctx.strokeStyle = `rgba(0, 180, 255, ${this.opacity * 0.5})`;
                ctx.lineWidth = 8;
                ctx.shadowColor = 'rgba(0, 150, 255, 1)';
                ctx.shadowBlur = 30;
                this.drawPath(this.segments);
                ctx.stroke();

                // Main bolt
                ctx.beginPath();
                ctx.strokeStyle = `rgba(0, 200, 255, ${this.opacity})`;
                ctx.lineWidth = 3;
                ctx.shadowBlur = 15;
                this.drawPath(this.segments);
                ctx.stroke();

                // Bright core
                ctx.beginPath();
                ctx.strokeStyle = `rgba(255, 255, 255, ${this.opacity})`;
                ctx.lineWidth = 1.5;
                ctx.shadowBlur = 5;
                this.drawPath(this.segments);
                ctx.stroke();

                // Branches
                for (const branch of this.branches) {
                    ctx.beginPath();
                    ctx.strokeStyle = `rgba(0, 180, 255, ${this.opacity * 0.6})`;
                    ctx.lineWidth = 2;
                    ctx.shadowBlur = 10;
                    this.drawPath(branch);
                    ctx.stroke();
                }

                ctx.shadowBlur = 0;
            }

            drawPath(points) {
                ctx.moveTo(points[0].x, points[0].y);
                for (let i = 1; i < points.length; i++) {
                    ctx.lineTo(points[i].x, points[i].y);
                }
            }
        }

        const bolts = [];
        let lastTime = 0;
        let lastStrike = 0;
        const strikeInterval = 1500;

        function createLightning() {
            bolts.push(new LightningBolt());
            
            // Screen flash
            canvas.style.filter = 'brightness(2)';
            setTimeout(() => {
                canvas.style.filter = 'brightness(1)';
            }, 50);
        }

        function animate(currentTime) {
            requestAnimationFrame(animate);
            
            const deltaTime = (currentTime - lastTime) / 1000;
            lastTime = currentTime;

            // Animate particles
            animateParticles();

            // Clear lightning canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Create new bolts
            if (currentTime - lastStrike > strikeInterval) {
                if (Math.random() < 0.8) {
                    createLightning();
                    lastStrike = currentTime;
                }
            }

            // Update and draw bolts
            for (let i = bolts.length - 1; i >= 0; i--) {
                if (!bolts[i].update(deltaTime)) {
                    bolts.splice(i, 1);
                } else {
                    bolts[i].draw();
                }
            }

            // Ambient glow
            const gradient = ctx.createRadialGradient(
                canvas.width / 2, canvas.height / 2, 0,
                canvas.width / 2, canvas.height / 2, canvas.width / 1.5
            );
            gradient.addColorStop(0, 'rgba(0, 100, 200, 0.08)');
            gradient.addColorStop(1, 'rgba(0, 50, 100, 0)');
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, canvas.width, canvas.height);
        }

        // Start
        setTimeout(createLightning, 300);
        animate(0);
    </script>
</body>
</html>