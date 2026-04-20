<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Optical Geometry Lab | Neon Sphere</title>

    <script src="../../../js/three.min.js"></script>
    <script src="../../../js/OrbitControls.js"></script>
    <script src="../../../js/tailwind.js"></script>
    <script src="../../../js/lucide.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap" rel="stylesheet">

    <style>
        body { margin: 0; overflow: hidden; background: #050505; font-family: 'Inter', sans-serif; color: white; }
        .glass-panel { background: rgba(15, 15, 15, 0.7); backdrop-filter: blur(15px); border: 1px solid rgba(0, 242, 234, 0.2); }
        .neon-text { color: #00f2ea; text-shadow: 0 0 10px rgba(0, 242, 234, 0.5); }
        input[type="range"] { -webkit-appearance: none; background: rgba(255,255,255,0.1); height: 4px; border-radius: 2px; accent-color: #00f2ea; }
        .scan-line { position: absolute; width: 100%; height: 2px; background: rgba(0, 242, 234, 0.1); animation: scan 3s linear infinite; }
        @keyframes scan { 0% { transform: translateY(-100%); } 100% { transform: translateY(100vh); } }
    </style>
</head>
<body>

<div class="scan-line pointer-events-none"></div>

<header class="fixed top-6 left-6 text-left z-50">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 glass-panel rounded-xl flex items-center justify-center">
            <i data-lucide="circle" class="text-neon w-6 h-6"></i>
        </div>
        <div>
            <h1 class="text-xl md:text-2xl font-black neon-text">Geometric Birth</h1>
            <p class="text-[10px] text-slate-400 uppercase tracking-widest">Sphere Generation Engine</p>
        </div>
    </div>
</header>

<a href="../../" class="back-btn fixed top-6 right-6 z-50 flex items-center gap-2 px-4 py-2 glass-panel rounded-full text-sm font-bold transition-all group">
    <i data-lucide="chevron-left" class="w-4 h-4 group-hover:text-neon"></i>
    BACK
</a>

<div class="fixed bottom-8 left-1/2 -translate-x-1/2 w-[90%] max-w-lg z-50 glass-panel rounded-3xl p-6">
    <div class="grid gap-6">
        <div class="flex items-center gap-4">
            <i data-lucide="zap" class="w-5 h-5 text-yellow-400"></i>
            <div class="flex-1">
                <div class="flex justify-between mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider">Rotation Speed</span>
                    <span id="speedVal" class="text-xs text-neon">0.05</span>
                </div>
                <input type="range" id="speedRange" min="0" max="0.3" step="0.001" value="0.05" class="w-full">
            </div>
        </div>

        <div class="flex items-center gap-4">
            <i data-lucide="sun" class="w-5 h-5 text-neon"></i>
            <div class="flex-1">
                <div class="flex justify-between mb-1">
                    <span class="text-xs font-bold uppercase tracking-wider">Bloom Intensity</span>
                    <span id="bloomVal" class="text-xs text-neon">2.0</span>
                </div>
                <input type="range" id="bloomRange" min="1" max="8" step="0.1" value="2" class="w-full">
            </div>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    camera.position.set(4, 3, 4);
    controls.enableDamping = true;

    // Create semi-circle geometry
    const radius = 2.5;
    const shape = new THREE.Shape();
    shape.absarc(0, 0, radius, -Math.PI / 2, Math.PI / 2, false);
    shape.lineTo(0, -radius);

    const geometry = new THREE.ShapeGeometry(shape);

    const material = new THREE.MeshBasicMaterial({
        color: 0x00f2ea,
        side: THREE.DoubleSide,
        transparent: true,
        opacity: 0.2
    });

    const semiCircle = new THREE.Mesh(geometry, material);
    scene.add(semiCircle);

    // Glowing edges
    const edgePoints = [];
    for(let i = 0; i <= 64; i++) {
        const angle = -Math.PI/2 + (Math.PI * i / 64);
        edgePoints.push(new THREE.Vector3(Math.cos(angle) * radius, Math.sin(angle) * radius, 0));
    }
    const edgeGeom = new THREE.BufferGeometry().setFromPoints(edgePoints);
    const lineMaterial = new THREE.LineBasicMaterial({ color: 0x00f2ea });
    const edgeLine = new THREE.Line(edgeGeom, lineMaterial);
    semiCircle.add(edgeLine);

    // Trails system (Fixed 50 layers for motion smoothness)
    const trailsCount = 50;
    const trails = [];
    for(let i = 0; i < trailsCount; i++) {
        const t = new THREE.Mesh(geometry,
            new THREE.MeshBasicMaterial({
                color: 0x00f2ea,
                transparent: true,
                opacity: 0.02,
                side: THREE.DoubleSide
            })
        );
        scene.add(t);
        trails.push(t);
    }

    const gridHelper = new THREE.GridHelper(10, 20, 0x222222, 0x111111);
    gridHelper.position.y = -3;
    scene.add(gridHelper);

    function animate() {
        requestAnimationFrame(animate);

        const speed = parseFloat(document.getElementById('speedRange').value);
        const bloom = parseFloat(document.getElementById('bloomRange').value);

        document.getElementById('speedVal').innerText = speed.toFixed(3);
        document.getElementById('bloomVal').innerText = bloom.toFixed(1);

        semiCircle.rotation.y += speed;

        trails.forEach((t, index) => {
            const lag = index * 0.015;
            t.rotation.y = semiCircle.rotation.y - (lag * speed * 30);
            t.material.opacity = (0.3 / trailsCount) * bloom;
        });

        // Change edge color based on bloom intensity
        lineMaterial.color.setHSL(0.5, 1, 0.3 * bloom / 2);

        controls.update();
        renderer.render(scene, camera);
    }

    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });

    animate();
</script>
</body>
</html>