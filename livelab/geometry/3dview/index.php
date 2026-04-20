<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>3D View Studio | MathLab</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/TransformControls.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; overflow: hidden; background: #03070a; font-family: 'Inter', sans-serif; color: white; touch-action: none; }
        .glass { background: rgba(13, 25, 35, 0.85); backdrop-filter: blur(15px); border: 1px solid rgba(0, 242, 234, 0.2); }
        .active-tool { background: #00f2ea !important; color: #000 !important; box-shadow: 0 0 15px #00f2ea; }
        #prop-panel { transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); transform: translateY(120%); }
        #prop-panel.show { transform: translateY(0); }
        .btn-tap:active { transform: scale(0.95); transition: transform 0.1s; }
    </style>
</head>
<body>

<div class="fixed top-4 left-4 z-50 pointer-events-none">
    <h1 class="text-sm font-black text-cyan-400 opacity-50 uppercase tracking-widest">Neon Geometry Engine</h1>
</div>

<div class="fixed right-4 top-1/4 z-50 flex flex-col gap-3 p-2 glass rounded-2xl">
    <button onclick="setMode('translate')" id="t-btn" class="p-4 rounded-xl active-tool btn-tap" title="Move Tool">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m15 15-3 3-3-3M15 9l-3-3-3 3M9 12h12M12 18V6"/></svg>
    </button>
    <button onclick="setMode('scale')" id="s-btn" class="p-4 rounded-xl hover:bg-white/10 btn-tap" title="Scale Tool">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 12V4h8m-8 0 16 16M12 20h8v-8"/></svg>
    </button>
    <div class="h-[1px] bg-white/10 my-1"></div>
    <button onclick="addCube()" class="p-4 rounded-xl bg-cyan-500/20 text-cyan-400 btn-tap" title="Add New Cube">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
    </button>
</div>

<div id="prop-panel" class="fixed bottom-24 left-4 right-4 z-50 p-6 glass rounded-3xl">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold uppercase tracking-wider">Fill Color</span>
            <input type="color" id="cubeColor" class="w-10 h-10 bg-transparent rounded-lg cursor-pointer">
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold uppercase tracking-wider">Show Edges</span>
            <input type="checkbox" id="borderToggle" checked class="w-6 h-6 accent-cyan-400">
        </div>
        <button onclick="deleteCube()" class="px-6 py-2 bg-red-500/20 text-red-400 rounded-xl border border-red-500/20 text-xs font-bold transition hover:bg-red-500/30">DELETE OBJECT</button>
    </div>
</div>

<div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 flex gap-1 p-1.5 glass rounded-full w-[95%] max-w-md overflow-x-auto no-scrollbar">
    <button onclick="changeView('3d')" class="flex-1 py-2 px-3 rounded-full text-[10px] font-bold hover:bg-white/5 whitespace-nowrap uppercase">Perspective</button>
    <button onclick="changeView('top')" class="flex-1 py-2 px-3 rounded-full text-[10px] font-bold hover:bg-white/5 uppercase">Top</button>
    <button onclick="changeView('front')" class="flex-1 py-2 px-3 rounded-full text-[10px] font-bold hover:bg-white/5 uppercase">Front</button>
    <button onclick="changeView('back')" class="flex-1 py-2 px-3 rounded-full text-[10px] font-bold hover:bg-white/5 uppercase">Back</button>
    <button onclick="changeView('left')" class="flex-1 py-2 px-3 rounded-full text-[10px] font-bold hover:bg-white/5 uppercase">Left</button>
    <button onclick="changeView('right')" class="flex-1 py-2 px-3 rounded-full text-[10px] font-bold hover:bg-white/5 uppercase">Right</button>
</div>

<script>
    let scene, camera, renderer, controls, transformControl;
    let cubes = [], selectedObject = null;
    const raycaster = new THREE.Raycaster();
    const mouse = new THREE.Vector2();

    init();
    animate();

    function init() {
        scene = new THREE.Scene();
        camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.set(25, 25, 25);

        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
        document.body.appendChild(renderer.domElement);

        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;

        transformControl = new THREE.TransformControls(camera, renderer.domElement);
        transformControl.addEventListener('dragging-changed', (e) => controls.enabled = !e.value);
        scene.add(transformControl);

        // Neon Grid Helper
        const grid = new THREE.GridHelper(80, 40, 0x00f2ea, 0x111a20);
        grid.material.opacity = 0.1;
        grid.material.transparent = true;
        scene.add(grid);

        addCube();

        window.addEventListener('pointerdown', onSelect);

        document.getElementById('borderToggle').onchange = (e) => {
            if(selectedObject) selectedObject.children[0].visible = e.target.checked;
        };
        document.getElementById('cubeColor').oninput = (e) => {
            if(selectedObject) {
                selectedObject.material.color.set(e.target.value);
                selectedObject.children[0].material.color.set(e.target.value);
            }
        };
    }

    function addCube() {
        const geom = new THREE.BoxGeometry(8, 8, 8);
        const mat = new THREE.MeshBasicMaterial({ color: 0x00f2ea, transparent: true, opacity: 0.2 });
        const mesh = new THREE.Mesh(geom, mat);
        const line = new THREE.LineSegments(new THREE.EdgesGeometry(geom), new THREE.LineBasicMaterial({ color: 0x00f2ea }));
        mesh.add(line);
        mesh.position.y = 4;
        scene.add(mesh);
        cubes.push(mesh);
        selectObject(mesh);
    }

    function onSelect(event) {
        if (transformControl.dragging) return;
        mouse.x = (event.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(event.clientY / window.innerHeight) * 2 + 1;
        raycaster.setFromCamera(mouse, camera);
        const intersects = raycaster.intersectObjects(cubes);
        if (intersects.length > 0) selectObject(intersects[0].object);
    }

    function selectObject(obj) {
        selectedObject = obj;
        transformControl.attach(obj);
        document.getElementById('prop-panel').classList.add('show');
        document.getElementById('borderToggle').checked = obj.children[0].visible;
        document.getElementById('cubeColor').value = '#' + obj.material.color.getHexString();
    }

    function setMode(mode) {
        transformControl.setMode(mode);
        document.getElementById('t-btn').classList.toggle('active-tool', mode === 'translate');
        document.getElementById('s-btn').classList.toggle('active-tool', mode === 'scale');
    }

    function deleteCube() {
        if(selectedObject) {
            scene.remove(selectedObject);
            cubes = cubes.filter(c => c !== selectedObject);
            transformControl.detach();
            document.getElementById('prop-panel').classList.remove('show');
            selectedObject = null;
        }
    }

    function changeView(v) {
        const d = 40;
        controls.reset();
        if(v==='top') camera.position.set(0, d, 0.01);
        else if(v==='front') camera.position.set(0, 0, d);
        else if(v==='back') camera.position.set(0, 0, -d);
        else if(v==='left') camera.position.set(-d, 0, 0);
        else if(v==='right') camera.position.set(d, 0, 0);
        else camera.position.set(25, 25, 25);
        controls.update();
    }

    function animate() {
        requestAnimationFrame(animate);
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