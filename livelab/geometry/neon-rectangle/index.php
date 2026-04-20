<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optical Geometry Lab | Cylinder Generation</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            overflow: hidden;
            background: radial-gradient(circle at center, #111 0%, #050505 100%);
            font-family: 'Inter', sans-serif;
            color: white;
        }

        /* Glass Panel Styling */
        .glass-panel {
            background: rgba(15, 15, 15, 0.7);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(0, 242, 234, 0.2);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.5), inset 0 0 20px rgba(0, 242, 234, 0.05);
        }

        .neon-text { color: #00f2ea; text-shadow: 0 0 10px rgba(0, 242, 234, 0.5); }

        /* Neon Slider Styling */
        input[type="range"] {
            -webkit-appearance: none;
            background: rgba(255,255,255,0.1);
            height: 4px;
            border-radius: 2px;
            accent-color: #00f2ea;
        }

        .back-btn:hover {
            transform: translateX(-5px);
            box-shadow: 0 0 15px rgba(0, 242, 234, 0.3);
        }

        /* Scanline Animation */
        @keyframes scan {
            0% { transform: translateY(-100%); opacity: 0; }
            50% { opacity: 0.5; }
            100% { transform: translateY(100%); opacity: 0; }
        }
        .scan-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: rgba(0, 242, 234, 0.5);
            animation: scan 3s linear infinite;
        }
    </style>
</head>
<body>

<div class="scan-line pointer-events-none"></div>

<header class="fixed top-6 left-6 text-left z-50">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 glass-panel rounded-xl flex items-center justify-center">
            <i data-lucide="box" class="text-neon w-6 h-6"></i>
        </div>
        <div>
            <h1 class="text-xl md:text-2xl font-black neon-text">Light Kinematics: Cylinder Birth</h1>
            <p class="text-[10px] text-slate-400 uppercase tracking-widest">Geometry Interaction Module V1.0</p>
        </div>
    </div>
</header>

<a href="../../" class="back-btn fixed top-6 right-6 z-50 flex items-center gap-2 px-4 py-2 glass-panel rounded-full text-sm font-bold transition-all group">
    BACK
    <i data-lucide="chevron-left" class="w-4 h-4 group-hover:text-neon"></i>
</a>

<div class="fixed bottom-8 left-1/2 -translate-x-1/2 w-[90%] max-w-lg z-50 glass-panel rounded-3xl p-6 transition-transform hover:scale-[1.02]">
    <div class="grid gap-6">
        <div class="flex items-center gap-4">
            <i data-lucide="zap" class="w-5 h-5 text-yellow-400"></i>
            <div class="flex-1">
                <div class="flex justify-between mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider">Rotation Speed</span>
                    <span id="speedVal" class="text-xs text-neon">0.05</span>
                </div>
                <input type="range" id="speedRange" min="0" max="0.3" step="0.001" value="0.05" class="w-full cursor-pointer">
            </div>
        </div>

        <div class="flex items-center gap-4">
            <i data-lucide="sun" class="w-5 h-5 text-neon"></i>
            <div class="flex-1">
                <div class="flex justify-between mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider">Bloom Intensity</span>
                    <span id="bloomVal" class="text-xs text-neon">2.0</span>
                </div>
                <input type="range" id="bloomRange" min="1" max="8" step="0.1" value="2" class="w-full cursor-pointer">
            </div>
        </div>
    </div>

    <div class="mt-4 pt-4 border-t border-white/5 flex justify-center gap-6">
        <div class="flex items-center gap-2 text-[10px] text-slate-500 italic">
            <i data-lucide="move-3d" class="w-3 h-3"></i> Drag to rotate
        </div>
        <div class="flex items-center gap-2 text-[10px] text-slate-500 italic">
            <i data-lucide="zoom-in" class="w-3 h-3"></i> Scroll to zoom
        </div>
    </div>
</div>

<script>
    // Initialize Icons
    lucide.createIcons();

    // 3D Scene Setup
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    // Orbit Controls
    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    camera.position.set(4, 4, 4);
    controls.enableDamping = true;

    // Plane Geometry (Modified for edge rotation)
    const width = 1.5;
    const height = 4;
    const geometry = new THREE.PlaneGeometry(width, height);
    // Translate geometry center to the edge for precise mathematical rotation around one side
    geometry.translate(width / 2, 0, 0);

    const material = new THREE.MeshBasicMaterial({
        color: 0x00f2ea,
        side: THREE.DoubleSide,
        transparent: true,
        opacity: 0.2
    });

    const rect = new THREE.Mesh(geometry, material);
    scene.add(rect);

    // Glowing Edges
    const edges = new THREE.EdgesGeometry(geometry);
    const lineMaterial = new THREE.LineBasicMaterial({ color: 0x00f2ea });
    const wireframe = new THREE.LineSegments(edges, lineMaterial);
    rect.add(wireframe);

    // Trails System (Volume Simulator)
    const trailsCount = 40;
    const trails = [];
    for(let i = 0; i < trailsCount; i++) {
        const trailRect = new THREE.Mesh(geometry,
            new THREE.MeshBasicMaterial({
                color: 0x00f2ea,
                transparent: true,
                opacity: 0.03,
                side: THREE.DoubleSide
            })
        );
        scene.add(trailRect);
        trails.push(trailRect);
    }

    // Grid Helper for spatial awareness
    const gridHelper = new THREE.GridHelper(10, 20, 0x333333, 0x111111);
    gridHelper.position.y = -2;
    scene.add(gridHelper);

    // Animation Loop
    function animate() {
        requestAnimationFrame(animate);

        const speed = parseFloat(document.getElementById('speedRange').value);
        const bloom = parseFloat(document.getElementById('bloomRange').value);

        // Update UI Panel Values
        document.getElementById('speedVal').innerText = speed.toFixed(3);
        document.getElementById('bloomVal').innerText = bloom.toFixed(1);

        rect.rotation.y += speed;

        trails.forEach((t, index) => {
            const lag = index * 0.015;
            t.rotation.y = rect.rotation.y - (lag * speed * 30);
            t.material.opacity = (0.2 / trailsCount) * bloom;
        });

        // Dynamic edge color based on bloom intensity
        lineMaterial.color.setHSL(0.5, 1, 0.4 * bloom / 2);

        controls.update();
        renderer.render(scene, camera);
    }

    // Handle Window Resize
    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });

    animate();
</script>
</body>
</html>