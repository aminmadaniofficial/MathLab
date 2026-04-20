<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Neon Engineering Cross-Section</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; overflow: hidden; background: #0a0f1a; font-family: 'Inter', sans-serif; color: white; touch-action: none; }
        .glass { background: rgba(15, 20, 35, 0.85); backdrop-filter: blur(12px); border: 1px solid rgba(0, 242, 234, 0.15); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .active-btn { background: rgba(0, 242, 234, 0.9) !important; color: #000 !important; font-weight: 700; box-shadow: 0 0 15px rgba(0, 242, 234, 0.5); }
        input[type="range"] { -webkit-appearance: none; background: rgba(255, 255, 255, 0.15); height: 5px; border-radius: 3px; accent-color: #00f2ea; }
        .shape-btn { font-size: 10px; padding: 6px 14px; border-radius: 10px; flex-shrink: 0; color: #a1a1aa; border: 1px solid transparent; transition: all 0.2s; }
        .shape-btn:hover:not(.active-btn) { background: rgba(255,255,255,0.05); color: white; }
    </style>
</head>
<body>

<div class="fixed top-3 left-0 right-0 z-50 px-3 sm:px-6">
    <div id="shapeContainer" class="flex gap-2 p-2 glass rounded-2xl overflow-x-auto no-scrollbar shadow-xl"></div>
</div>

<div class="fixed bottom-5 left-3 z-50 w-auto max-w-[180px] sm:max-w-[220px] p-4 sm:p-5 glass rounded-[1.5rem] sm:rounded-[2rem]">
    <div class="space-y-4 sm:space-y-5">
        <div>
            <div class="flex justify-between mb-1 text-[9px] sm:text-[10px] font-bold text-cyan-400/90">
                <span>SLICE POSITION</span><span id="pos-val">0</span>
            </div>
            <input type="range" id="slicePos" min="-4.5" max="4.5" step="0.01" value="0" class="w-full cursor-pointer">
        </div>
        <div>
            <div class="flex justify-between mb-1 text-[9px] sm:text-[10px] font-bold text-cyan-400/90">
                <span>SLICE ANGLE</span><span id="rot-val">0°</span>
            </div>
            <input type="range" id="sliceRot" min="0" max="6.28" step="0.01" value="0" class="w-full cursor-pointer">
        </div>
        <button onclick="toggleAction()" id="actionBtn" class="w-full py-2.5 sm:py-3 bg-cyan-500/10 text-cyan-400 rounded-xl text-[10px] sm:text-xs font-bold border border-cyan-500/30 transition hover:bg-cyan-500/20 active:scale-95">SEPARATE PARTS</button>
    </div>
</div>

<script>
    let scene, camera, renderer, controls, group1, group2, laser;
    let isSplit = false, currentGap = 0;
    const clipPlane1 = new THREE.Plane();
    const clipPlane2 = new THREE.Plane();

    const shapeList = [
        {id: 'Box', name: 'Cube'}, {id: 'Cylinder', name: 'Cylinder'}, {id: 'Sphere', name: 'Sphere'},
        {id: 'Cone', name: 'Cone'}, {id: 'Torus', name: 'Torus'}, {id: 'TorusKnot', name: 'Torus Knot'},
        {id: 'Dodecahedron', name: 'Dodecahedron'}, {id: 'Icosahedron', name: 'Icosahedron'}, {id: 'Octahedron', name: 'Octahedron'},
        {id: 'Tetrahedron', name: 'Tetrahedron'},  {id: 'Tube', name: 'Tube'},
        {id: 'Ring', name: 'Flat Ring'}, {id: 'Hexagon', name: 'Hex Prism'}, {id: 'Octagon', name: 'Oct Prism'},
        {id: 'Star', name: 'Star'}, {id: 'ThinBox', name: 'Plate'}, {id: 'Nut', name: 'Nut'},
        {id: 'Prism', name: 'Tri Prism'}
    ];

    init();
    animate();

    function init() {
        scene = new THREE.Scene();
        const gridHelper = new THREE.GridHelper(50, 50, 0x333344, 0x151a25);
        gridHelper.position.y = -5;
        scene.add(gridHelper);

        camera = new THREE.PerspectiveCamera(55, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.set(12, 12, 12);

        renderer = new THREE.WebGLRenderer({ antialias: true, stencil: true, alpha: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        renderer.localClippingEnabled = true;
        document.body.appendChild(renderer.domElement);

        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.maxDistance = 50;

        laser = new THREE.Mesh(
            new THREE.CylinderGeometry(9, 9, 0.015, 64),
            new THREE.MeshBasicMaterial({ color: 0x00f2ea, transparent: true, opacity: 0.3, side: THREE.DoubleSide })
        );
        scene.add(laser);

        group1 = new THREE.Group();
        group2 = new THREE.Group();
        scene.add(group1, group2);

        const container = document.getElementById('shapeContainer');
        shapeList.forEach(s => {
            const btn = document.createElement('button');
            btn.className = 'shape-btn class transition whitespace-nowrap';
            btn.innerText = s.name;
            btn.id = 'btn-' + s.id;
            btn.onclick = () => changeShape(s.id);
            container.appendChild(btn);
        });

        changeShape('Box');
    }

    function createStencilMat(geometry, plane) {
        const g = new THREE.Group();
        const neonColor = 0x00f2ea;

        // 1. Stencil
        const matStencil = new THREE.MeshBasicMaterial({
            depthWrite: false, colorWrite: false, stencilWrite: true,
            stencilFunc: THREE.AlwaysStencilFunc, side: THREE.DoubleSide,
            clippingPlanes: [plane], stencilFail: THREE.ReplaceStencilOp,
            stencilZFail: THREE.ReplaceStencilOp, stencilZPass: THREE.ReplaceStencilOp,
            stencilRef: 1
        });
        g.add(new THREE.Mesh(geometry, matStencil));

        // 2. Main Black Body
        const matMain = new THREE.MeshBasicMaterial({
            color: 0x000000, clippingPlanes: [plane], stencilWrite: true,
            stencilRef: 0, stencilFunc: THREE.EqualStencilFunc, side: THREE.DoubleSide
        });
        g.add(new THREE.Mesh(geometry, matMain));

        // 3. Neon Edges
        const edges = new THREE.EdgesGeometry(geometry);
        const lineMat = new THREE.LineBasicMaterial({ color: neonColor, transparent: true, opacity: 0.7 });
        const line = new THREE.LineSegments(edges, lineMat);
        line.onBeforeRender = () => { line.material.clippingPlanes = [plane]; };
        g.add(line);

        // 4. Invisible Cap Plane
        const capMat = new THREE.MeshBasicMaterial({
            colorWrite: false,
            depthWrite: false,
            clippingPlanes: [plane], stencilWrite: true,
            stencilRef: 1, stencilFunc: THREE.EqualStencilFunc, side: THREE.DoubleSide
        });
        const cap = new THREE.Mesh(new THREE.PlaneGeometry(100, 100), capMat);
        cap.onBeforeRender = function() {
            this.position.copy(plane.normal).multiplyScalar(-plane.constant);
            this.lookAt(this.position.clone().add(plane.normal));
        };
        g.add(cap);

        return g;
    }

    function changeShape(type) {
        group1.clear(); group2.clear();
        let geo;
        switch(type) {
            case 'Box': geo = new THREE.BoxGeometry(6, 6, 6); break;
            case 'Cylinder': geo = new THREE.CylinderGeometry(3, 3, 8, 32); break;
            case 'Sphere': geo = new THREE.SphereGeometry(4, 40, 40); break;
            case 'Cone': geo = new THREE.ConeGeometry(4, 8, 32); break;
            case 'Torus': geo = new THREE.TorusGeometry(4, 1.2, 16, 100); break;
            case 'TorusKnot': geo = new THREE.TorusKnotGeometry(3, 0.8, 100, 16); break;
            case 'Dodecahedron': geo = new THREE.DodecahedronGeometry(4); break;
            case 'Icosahedron': geo = new THREE.IcosahedronGeometry(4); break;
            case 'Octahedron': geo = new THREE.OctahedronGeometry(4); break;
            case 'Tetrahedron': geo = new THREE.TetrahedronGeometry(4.5); break;
            case 'Tube': geo = new THREE.CylinderGeometry(3, 3, 8, 32, 1, true); break;
            case 'Ring': geo = new THREE.TorusGeometry(4, 0.3, 16, 100); break;
            case 'Hexagon': geo = new THREE.CylinderGeometry(4, 4, 7, 6); break;
            case 'Octagon': geo = new THREE.CylinderGeometry(4, 4, 7, 8); break;
            case 'ThinBox': geo = new THREE.BoxGeometry(8, 0.5, 8); break;
            case 'Nut': geo = new THREE.TorusGeometry(3, 1.5, 6, 6); break;
            case 'Prism': geo = new THREE.CylinderGeometry(3, 3, 8, 3); break;
            default: geo = new THREE.OctahedronGeometry(4);
        }

        group1.add(createStencilMat(geo, clipPlane1));
        group2.add(createStencilMat(geo, clipPlane2));

        document.querySelectorAll('.shape-btn').forEach(b => b.classList.remove('active-btn'));
        document.getElementById('btn-' + type).classList.add('active-btn');
        isSplit = false; currentGap = 0;
        updateActionBtn();
    }

    function toggleAction() {
        isSplit = !isSplit;
        updateActionBtn();
    }

    function updateActionBtn() {
        const btn = document.getElementById('actionBtn');
        btn.innerText = isSplit ? "RECONNECT PARTS" : "SEPARATE PARTS";
        btn.classList.toggle('active-btn', isSplit);
        btn.classList.toggle('text-cyan-400', !isSplit);
        btn.classList.toggle('text-black', isSplit);
    }

    function animate() {
        requestAnimationFrame(animate);
        const pos = parseFloat(document.getElementById('slicePos').value);
        const rot = parseFloat(document.getElementById('sliceRot').value);
        const normal = new THREE.Vector3(0, 1, 0).applyAxisAngle(new THREE.Vector3(1, 0, 0), rot);

        document.getElementById('pos-val').innerText = pos.toFixed(1);
        document.getElementById('rot-val').innerText = Math.round(rot * 57.3) + "°";

        laser.rotation.x = rot;
        laser.position.copy(normal).multiplyScalar(pos);
        laser.visible = !isSplit;

        const targetGap = isSplit ? 5 : 0;
        currentGap += (targetGap - currentGap) * 0.1;

        group1.position.copy(normal).multiplyScalar(-currentGap);
        group2.position.copy(normal).multiplyScalar(currentGap);

        clipPlane1.set(normal.clone().negate(), pos - currentGap);
        clipPlane2.set(normal, -(pos + currentGap));

        controls.update();
        renderer.render(scene, camera);
    }

    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });
</script>
</body>
</html>