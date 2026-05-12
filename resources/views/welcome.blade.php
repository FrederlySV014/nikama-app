<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nikama Delivery</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f9fafb; color: #1f2937; }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
        header { background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        header .container { display: flex; justify-content: space-between; align-items: center; padding: 1rem; }
        .logo { font-size: 1.5rem; font-weight: bold; color: #ea580c; }
        nav { display: flex; gap: 1.5rem; }
        nav a { color: #4b5563; text-decoration: none; font-weight: 500; }
        nav a:hover { color: #ea580c; }
        main { padding: 4rem 0; }
        .hero { text-align: center; margin-bottom: 4rem; }
        .hero h1 { font-size: 2.5rem; font-weight: bold; margin-bottom: 1rem; color: #111827; }
        .hero p { font-size: 1.25rem; color: #6b7280; margin-bottom: 2rem; max-width: 600px; margin-left: auto; margin-right: auto; }
        .btn { display: inline-block; padding: 0.75rem 2rem; border-radius: 0.5rem; font-weight: 600; text-decoration: none; cursor: pointer; border: none; }
        .btn-primary { background: #ea580c; color: white; }
        .btn-primary:hover { background: #c2410c; }
        .btn-secondary { background: white; color: #ea580c; border: 2px solid #ea580c; }
        .btn-secondary:hover { background: #fff7ed; }
        .buttons { display: flex; justify-content: center; gap: 1rem; margin-bottom: 3rem; }
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-top: 4rem; }
        .feature { background: white; padding: 1.5rem; border-radius: 0.75rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); text-align: center; }
        .feature-icon { width: 3rem; height: 3rem; background: #ffedd5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; }
        .feature-icon svg { width: 1.5rem; height: 1.5rem; color: #ea580c; }
        .feature h3 { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; }
        .feature p { color: #6b7280; }
        footer { background: #1f2937; color: #9ca3af; padding: 2rem 0; text-align: center; }
        @media (min-width: 768px) {
            .hero h1 { font-size: 3rem; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">Nikama</h1>
            <nav>
                <a href="#">Menú</a>
                <a href="#">Nosotros</a>
                <a href="#">Contacto</a>
            </nav>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="hero">
                <h1>Tu comida favorita, entregada a domicilio</h1>
                <p>Rápido, seguro y directo a tu puerta. Los mejores restaurantes de la ciudad disponibles en tu teléfono.</p>
                <div class="buttons">
                    <a href="#" class="btn btn-primary">Pedir ahora</a>
                    <a href="#" class="btn btn-secondary">Ver menú</a>
                </div>
            </div>

            <div class="features">
                <div class="feature">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3>Rápido</h3>
                    <p>Entrega en menos de 30 minutos</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h3>Seguro</h3>
                    <p>Pagós seguros y protegidos</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <h3>Soporte 24/7</h3>
                    <p>Atención al cliente siempre disponible</p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>© 2026 Nikama Delivery. Todos los derechos reservados.</p>
        </div>
    </footer>
</body>
</html>