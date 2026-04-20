<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Number Line Interactive Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=JetBrains+Mono:wght@700&display=swap');
        body { background: #050505; font-family: 'Inter', sans-serif; color: white; overflow: hidden; touch-action: none; -webkit-user-select: none; user-select: none; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1); }
        .view-section { display: none; height: 100%; width: 100%; }
        .view-section.active { display: flex; flex-direction: column; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .draggable { cursor: grab; transition: transform 0.2s; }
        .draggable:active { cursor: grabbing; transform: scale(0.95); }
        .drop-bag { border: 2px dashed rgba(0, 242, 234, 0.2); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .drop-bag.active { background: rgba(0, 242, 234, 0.15); border-color: #00f2ea; transform: scale(1.1); box-shadow: 0 0 30px rgba(0, 242, 234, 0.2); }
        .vector-path { transition: all 0.5s cubic-bezier(0.18, 0.89, 0.32, 1.28); }
        .nav-btn { transition: 0.3s; opacity: 0.4; }
        .nav-btn.active { opacity: 1; background: rgba(0, 242, 234, 0.1); border: 1px solid #00f2ea; color: #00f2ea; }
        input[type="range"] { -webkit-appearance: none; background: #1a1a1a; height: 8px; border-radius: 10px; width: 100%; cursor: pointer; }
        input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; width: 24px; height: 24px; background: #00f2ea; border-radius: 50%; border: 4px solid #000; box-shadow: 0 0 10px rgba(0, 242, 234, 0.5); }
    </style>
</head>
<body class="h-screen flex flex-col">

<header class="h-16 md:h-20 glass flex justify-between items-center px-4 md:px-8 z-50">
    <div class="flex items-center gap-2 md:gap-4">
        <div class="w-10 h-10 md:w-12 md:h-12 bg-cyan-500/10 rounded-xl flex items-center justify-center border border-cyan-500/30">
            <i data-lucide="binary" class="text-cyan-400 w-5 h-5 md:w-6 md:h-6"></i>
        </div>
        <div>
            <h1 class="text-sm md:text-xl font-black tracking-tight">NUMBER <span class="text-cyan-400" id="header-title">LINE LAB</span></h1>
        </div>
    </div>
    <div id="net-worth-display" class="hidden glass px-6 py-2 rounded-full border-white/10 shadow-2xl">
        <span id="worth-value" class="font-['JetBrains_Mono'] font-bold text-yellow-400 text-xl md:text-3xl">$0</span>
    </div>
    <button onclick="resetView()" class="group p-2 hover:bg-white/10 rounded-full transition-all">
        <i data-lucide="rotate-ccw" class="w-5 h-5 text-slate-400 group-hover:text-white group-hover:rotate-[-90deg] transition-all"></i>
    </button>
</header>

<div class="flex-1 relative overflow-hidden">

    <section id="view-explore" class="view-section active p-4 md:p-10 items-center justify-center">
        <div class="mb-8 md:mb-12 flex gap-3 md:gap-6 items-center text-3xl md:text-6xl font-black">
            <span id="ex-val-a" class="text-cyan-400">0</span>
            <span class="text-slate-600">+</span>
            <span id="ex-val-b" class="text-purple-400">0</span>
            <span class="text-white">=</span>
            <div class="px-6 md:px-10 py-2 bg-white text-black rounded-xl md:rounded-2xl shadow-[0_0_30px_rgba(255,255,255,0.3)]" id="ex-val-res">0</div>
        </div>

        <svg viewBox="0 0 1000 250" class="w-full max-w-5xl overflow-visible mb-12">
            <defs>
                <marker id="arrow-cyan" viewBox="0 0 10 10" refX="10" refY="5" markerWidth="4" markerHeight="4" orient="auto-start-reverse">
                  <path d="M 0 0 L 10 5 L 0 10 z" fill="#00f2ea" />
                </marker>
                <marker id="arrow-purple" viewBox="0 0 10 10" refX="10" refY="5" markerWidth="4" markerHeight="4" orient="auto-start-reverse">
                  <path d="M 0 0 L 10 5 L 0 10 z" fill="#a855f7" />
                </marker>
            </defs>
            <line x1="50" y1="150" x2="950" y2="150" stroke="rgba(255,255,255,0.1)" stroke-width="2" />
            <g class="ticks-group"></g>
            <line id="ex-vec-res" x1="500" y1="150" x2="500" y2="150" stroke="rgba(255,255,255,0.15)" stroke-width="12" stroke-linecap="round" />
            <line id="ex-vec-a" x1="500" y1="110" x2="500" y2="110" stroke="#00f2ea" stroke-width="8" stroke-linecap="round" marker-end="url(#arrow-cyan)" class="vector-path" />
            <line id="ex-vec-b" x1="500" y1="70" x2="500" y2="70" stroke="#a855f7" stroke-width="8" stroke-linecap="round" marker-end="url(#arrow-purple)" class="vector-path" />
        </svg>

        <div class="w-full max-w-2xl space-y-8 md:space-y-12">
            <div class="space-y-3">
                <div class="flex justify-between text-xs font-black text-cyan-400 uppercase tracking-widest"><span>Initial Value (A)</span><span id="label-in-a" class="bg-cyan-400/10 px-2 py-0.5 rounded">0</span></div>
                <input type="range" id="ex-in-a" min="-10" max="10" value="0">
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-xs font-black text-purple-400 uppercase tracking-widest"><span>Added Value (B)</span><span id="label-in-b" class="bg-purple-400/10 px-2 py-0.5 rounded">0</span></div>
                <input type="range" id="ex-in-b" min="-10" max="10" value="0">
            </div>
        </div>
    </section>

    <section id="view-networth" class="view-section p-4 flex-col lg:flex-row gap-6">
        <aside class="w-full lg:w-80 glass rounded-3xl p-6 flex flex-row lg:flex-col gap-6 overflow-x-auto">
            <div class="min-w-[160px] flex-1">
                <h3 class="text-xs font-black text-green-400 mb-4 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-4 h-4"></i> Assets
                </h3>
                <div class="flex lg:grid lg:grid-cols-2 gap-3">
                    <div class="draggable glass p-4 rounded-2xl border-green-500/20 text-center flex-1 hover:bg-green-500/5" data-val="5">
                        <i data-lucide="gem" class="w-8 h-8 mx-auto text-green-500 mb-2"></i>
                        <span class="text-sm font-bold">$5</span>
                    </div>
                    <div class="draggable glass p-4 rounded-2xl border-green-500/20 text-center flex-1 hover:bg-green-500/5" data-val="10">
                        <i data-lucide="banknote" class="w-8 h-8 mx-auto text-green-500 mb-2"></i>
                        <span class="text-sm font-bold">$10</span>
                    </div>
                </div>
            </div>
            <div class="min-w-[160px] flex-1">
                <h3 class="text-xs font-black text-red-400 mb-4 uppercase tracking-widest flex items-center gap-2">
                    <i data-lucide="trending-down" class="w-4 h-4"></i> Debts
                </h3>
                <div class="flex lg:grid lg:grid-cols-2 gap-3">
                    <div class="draggable glass p-4 rounded-2xl border-red-500/20 text-center flex-1 hover:bg-red-500/5" data-val="-5">
                        <i data-lucide="receipt" class="w-8 h-8 mx-auto text-red-500 mb-2"></i>
                        <span class="text-sm font-bold">-$5</span>
                    </div>
                    <div class="draggable glass p-4 rounded-2xl border-red-500/20 text-center flex-1 hover:bg-red-500/5" data-val="-10">
                        <i data-lucide="credit-card" class="w-8 h-8 mx-auto text-red-500 mb-2"></i>
                        <span class="text-sm font-bold">-$10</span>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col items-center justify-center relative min-h-[400px]">
            <svg viewBox="0 0 1000 200" class="w-full max-w-4xl overflow-visible mb-12">
                <line x1="50" y1="150" x2="950" y2="150" stroke="rgba(255,255,255,0.1)" stroke-width="2" />
                <g class="ticks-group"></g>
                <g id="nw-vectors"></g>
            </svg>
            <div id="nw-dropzone" class="drop-bag w-48 h-48 md:w-64 md:h-64 rounded-full flex flex-col items-center justify-center gap-2 relative">
                <i data-lucide="shopping-cart" class="w-12 h-12 md:w-16 md:h-16 text-slate-700"></i>
                <span class="text-[10px] text-slate-500 font-black tracking-widest uppercase">Calculation Bag</span>
                <div id="items-count" class="bg-cyan-400/20 text-cyan-400 px-4 py-1 rounded-full text-xs font-bold">0 Items</div>
            </div>
            <p class="mt-6 text-slate-500 text-xs italic">Drag items into the bag to calculate total net worth</p>
        </div>
    </section>
</div>

<nav class="h-20 md:h-24 glass flex justify-center items-center gap-4 md:gap-12 px-4 z-50">
    <button onclick="switchView('explore')" id="nav-explore" class="nav-btn active flex flex-col items-center gap-2 p-3 rounded-2xl w-28 md:w-40">
        <i data-lucide="move-horizontal" class="w-5 h-5"></i>
        <span class="text-[10px] font-black uppercase tracking-widest">Operations</span>
    </button>
    <button onclick="switchView('networth')" id="nav-networth" class="nav-btn flex flex-col items-center gap-2 p-3 rounded-2xl w-28 md:w-40">
        <i data-lucide="landmark" class="w-5 h-5"></i>
        <span class="text-[10px] font-black uppercase tracking-widest">Financials</span>
    </button>
</nav>

<script>
    lucide.createIcons();
    const origin = 500, step = 40;
    let bagItems = [];

    // Initialize Number Lines
    document.querySelectorAll('.ticks-group').forEach(g => {
        for(let i=-10; i<=10; i++) {
            const x = origin + (i * step);
            g.innerHTML += `
                <line x1="${x}" y1="145" x2="${x}" y2="155" stroke="rgba(255,255,255,${i===0?0.5:0.1})" />
                <text x="${x}" y="185" text-anchor="middle" fill="white" fill-opacity="${i===0?0.8:0.2}" font-family="JetBrains Mono" font-size="12">${i}</text>
            `;
        }
    });

    function switchView(view) {
        document.querySelectorAll('.view-section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(`view-${view}`).classList.add('active');
        document.getElementById(`nav-${view}`).classList.add('active');
        document.getElementById('header-title').innerText = view === 'explore' ? 'OPERATIONS' : 'FINANCIALS';
        document.getElementById('net-worth-display').classList.toggle('hidden', view !== 'networth');
    }

    function updateExplore() {
        const a = parseInt(document.getElementById('ex-in-a').value);
        const b = parseInt(document.getElementById('ex-in-b').value);
        const sum = a + b;
        
        document.getElementById('ex-val-a').innerText = a;
        document.getElementById('ex-val-b').innerText = b < 0 ? `(${b})` : b;
        document.getElementById('ex-val-res').innerText = sum;
        document.getElementById('label-in-a').innerText = a;
        document.getElementById('label-in-b').innerText = b;

        // Vector A starts at 0
        document.getElementById('ex-vec-a').setAttribute('x2', origin + (a * step));
        
        // Vector B starts at end of A
        const vB = document.getElementById('ex-vec-b');
        vB.setAttribute('x1', origin + (a * step));
        vB.setAttribute('x2', origin + (a * step) + (b * step));

        // Result Vector (shadow line)
        const vRes = document.getElementById('ex-vec-res');
        vRes.setAttribute('x2', origin + (sum * step));
    }

    // Drag and Drop Logic
    const draggables = document.querySelectorAll('.draggable');
    const dropzone = document.getElementById('nw-dropzone');

    draggables.forEach(el => {
        el.addEventListener('mousedown', e => startDrag(e, false));
        el.addEventListener('touchstart', e => { e.preventDefault(); startDrag(e, true); }, {passive: false});
    });

    function startDrag(e, isTouch) {
        const el = e.currentTarget;
        const val = parseInt(el.dataset.val);
        const clone = el.cloneNode(true);
        const startEvent = isTouch ? e.touches[0] : e;

        clone.style.position = 'fixed';
        clone.style.width = el.offsetWidth + 'px';
        clone.style.left = startEvent.clientX - el.offsetWidth/2 + 'px';
        clone.style.top = startEvent.clientY - el.offsetHeight/2 + 'px';
        clone.style.zIndex = '1000';
        clone.style.pointerEvents = 'none';
        clone.classList.add('glass');
        document.body.appendChild(clone);

        const move = (ev) => {
            const moveEvent = isTouch ? ev.touches[0] : ev;
            clone.style.left = moveEvent.clientX - el.offsetWidth/2 + 'px';
            clone.style.top = moveEvent.clientY - el.offsetHeight/2 + 'px';
            
            const r = dropzone.getBoundingClientRect();
            if(moveEvent.clientX > r.left && moveEvent.clientX < r.right && 
               moveEvent.clientY > r.top && moveEvent.clientY < r.bottom) {
                dropzone.classList.add('active');
            } else {
                dropzone.classList.remove('active');
            }
        };

        const end = (ev) => {
            const endEvent = isTouch ? ev.changedTouches[0] : ev;
            const r = dropzone.getBoundingClientRect();
            
            if(endEvent.clientX > r.left && endEvent.clientX < r.right && 
               endEvent.clientY > r.top && endEvent.clientY < r.bottom) {
                bagItems.push(val);
                updateNetWorth();
            }
            
            clone.remove();
            dropzone.classList.remove('active');
            window.removeEventListener(isTouch?'touchmove':'mousemove', move);
            window.removeEventListener(isTouch?'touchend':'mouseup', end);
        };

        window.addEventListener(isTouch?'touchmove':'mousemove', move, {passive: false});
        window.addEventListener(isTouch?'touchend':'mouseup', end);
    }

    function updateNetWorth() {
        const container = document.getElementById('nw-vectors');
        container.innerHTML = '';
        let currentX = origin, total = 0;
        
        bagItems.forEach((v, i) => {
            const targetX = currentX + (v * step);
            const color = v > 0 ? '#22c55e' : '#ef4444';
            const y = 150 - (i * 4) - 15; // Vertical stacking to avoid overlap
            
            // Marker ID for the tip
            const marker = v > 0 ? 'arrow-cyan' : 'arrow-purple';
            
            container.innerHTML += `
                <line x1="${currentX}" y1="${y}" x2="${targetX}" y2="${y}" 
                      stroke="${color}" stroke-width="4" stroke-linecap="round" 
                      class="vector-path" />`;
            currentX = targetX;
            total += v;
        });

        document.getElementById('worth-value').innerText = total >= 0 ? `$${total}` : `-$${Math.abs(total)}`;
        document.getElementById('items-count').innerText = `${bagItems.length} Items`;
        
        // Final Result Ghost Vector
        if(bagItems.length > 0) {
            container.innerHTML += `
                <line x1="${origin}" y1="150" x2="${currentX}" y2="150" 
                      stroke="#eab308" stroke-width="6" opacity="0.3" stroke-linecap="round" />`;
        }
    }

    function resetView() {
        if(document.getElementById('view-explore').classList.contains('active')) {
            document.getElementById('ex-in-a').value = 0;
            document.getElementById('ex-in-b').value = 0;
            updateExplore();
        } else {
            bagItems = [];
            updateNetWorth();
        }
    }

    document.getElementById('ex-in-a').oninput = updateExplore;
    document.getElementById('ex-in-b').oninput = updateExplore;
    updateExplore();
</script>
</body>
</html>