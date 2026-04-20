<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Precision Engineering Fit Model</title>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">

    <style>
        body { 
            margin: 0; 
            overflow: hidden; 
            background: #050505; 
            font-family: 'Inter', sans-serif; 
        }
        .info { 
            position: fixed; 
            top: 20px; 
            left: 20px; /* Switched to left for English layout */
            color: #00f2ea; 
            background: rgba(0,0,0,0.8); 
            padding: 15px; 
            border: 1px solid #00f2ea; 
            border-radius: 8px; 
            pointer-events: none; 
            box-shadow: 0 0 15px rgba(0, 242, 234, 0.2);
        }
        .label { font-weight: 700; color: #fff; margin-bottom: 5px; display: block; }
    </style>
</head>
<body>

<div class="info">
    <span class="label">Precision Fit Configuration</span>
    Container: 35 x 18 x 20<br>
    Bottom: 5 Units (25x14x4)<br>
    Top: 1 Unit (25x4x20)
</div>

<script>
    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.body.appendChild(renderer.domElement);

    const controls = new THREE.OrbitControls(camera, renderer.domElement);
    camera.position.set(45, 30, 45);
    controls.enableDamping = true;

    // --- Main Container (35x18x20) ---
    const containerGeom = new THREE.BoxGeometry(35, 18, 20);
    const containerEdges = new THREE.EdgesGeometry(containerGeom);
    const container = new THREE.LineSegments(
        containerEdges, 
        new THREE.LineBasicMaterial({ color: 0x00f2ea, transparent: true, opacity: 0.4 })
    );
    scene.add(container);

    // Function to create internal components
    function createSubCube(w, h, d, color) {
        const group = new THREE.Group();
        const geom = new THREE.BoxGeometry(w, h, d);
        const mesh = new THREE.Mesh(
            geom, 
            new THREE.MeshBasicMaterial({ color: color, transparent: true, opacity: 0.2 })
        );
        const edges = new THREE.EdgesGeometry(geom);
        const line = new THREE.LineSegments(
            edges, 
            new THREE.LineBasicMaterial({ color: color, linewidth: 2 })
        );
        group.add(mesh, line);
        return group;
    }

    // Creating the 5 base components
    for(let i = 0; i < 5; i++) {
        const cube = createSubCube(25, 14, 4, 0x00ffcc);
        cube.position.z = -8 + (i * 4);
        cube.position.y = -2;
        scene.add(cube);
    }

    // Creating the top component
    const topCube = createSubCube(25, 4, 20, 0xff0066);
    topCube.position.y = 7;
    scene.add(topCube);

    // Environment Grid
    scene.add(new THREE.GridHelper(100, 40, 0x222222, 0x111111));

    function animate() {
        requestAnimationFrame(animate);
        controls.update();
        renderer.render(scene, camera);
    }
    animate();

    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });
</script>
</body>
</html>