<?php
$topics = [
    'fractions' => [
        'id' => 'fractions',
        'title' => 'Fractions & Mixed Numbers',
        'desc' => 'Explore the world of precise divisions and equal shares.',
        'icon' => 'pie-chart',
        'color' => 'text-neon',
        'labs' => []
    ],
    'algebra' => [
        'id' => 'algebra',
        'title' => 'Equations & Algebra',
        'desc' => 'Discover unknowns and the symbolic language of mathematics.',
        'icon' => 'sigma',
        'color' => 'text-purple-500',
        'labs' => []
    ],
    'geometry' => [
        'id' => 'geometry',
        'title' => 'Geometry & Visual Illusions',
        'desc' => 'Playing with dimensions, shapes, and optical illusions.',
        'icon' => 'box',
        'color' => 'text-yellow-500',
        'labs' => [
            ['title' => 'Neon Rectangle: Cylinder Birth', 'desc' => 'Rotate a neon rectangle to see a cylinder formed by motion illusion.', 'img' => './geometry/neon-rectangle/preview.png', 'link' => 'geometry/neon-rectangle'],
            ['title' => 'Neon Semi-Circle: Sphere Birth', 'desc' => 'Rotate a neon semi-circle to create a glowing sphere illusion.', 'img' => './geometry/neon-circle/preview.png', 'link' => 'geometry/neon-circle'],
            ['title' => 'Neon Triangle: Cone Birth', 'desc' => 'Watch a neon cone emerge through the high-speed rotation of a triangle.', 'img' => './geometry/neon-triangle/preview.png', 'link' => 'geometry/neon-triangle'],
            ['title' => 'Offset Circle: Torus Birth', 'desc' => 'Rotate a circle at a distance from the axis to create a glowing 3D Donut.', 'img' => './geometry/neon-torus/preview.png', 'link' => 'geometry/neon-torus'],
            ['title' => 'The Six Cubes Paradox', 'desc' => 'Discover why standard volume formulas can sometimes feel counterintuitive.', 'img' => './geometry/sixcubes/preview.png', 'link' => 'geometry/sixcubes'],
            ['title' => '3D Shape Designer', 'desc' => 'Build, move, and resize cubes to view your creations in various neon perspectives.', 'img' => './geometry/3dview/preview.png', 'link' => 'geometry/3dview'],
            ['title' => '3D Cross-Sections', 'desc' => 'Slice through 20 different 3D shapes to visualize the resulting 2D planes.', 'img' => './geometry/3dsection/preview.png', 'link' => 'geometry/3dsection'],
        ]
    ],
    'numbers' => [
        'id' => 'numbers',
        'title' => 'Number Sense',
        'desc' => 'Interactive number lines and learning positive/negative operations.',
        'icon' => 'plus',
        'color' => 'text-green-500',
        'labs' => [
            ['title' => 'Integer Number Line Operations', 'desc' => 'Master positive and negative numbers using interactive vector movements.', 'img' => './numbers/number-operations/preview.png', 'link' => 'numbers/number-operations'],
        ]
    ],
];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interactive Labs | MathLab</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { dark: '#050505', card: '#0F1014', neon: '#00f2ea' }
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #050505; color: #e2e8f0; scroll-behavior: smooth; }
        .glass-header { background: rgba(5, 5, 5, 0.8); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255,255,255,0.05); }
        .grid-bg { background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.05) 1px, transparent 0); background-size: 40px 40px; }

        .topic-content { max-height: 0; overflow: hidden; transition: max-height 0.5s cubic-bezier(0, 1, 0, 1); }
        .topic-section.active .topic-content { max-height: 2000px; transition: max-height 1s ease-in-out; }
        .topic-section.active .chevron-icon { transform: rotate(180deg); color: #00f2ea; }
        .topic-section.active .topic-trigger { background: rgba(0, 242, 234, 0.05); border-color: rgba(0, 242, 234, 0.3); }
    </style>
</head>
<body class="antialiased">

<div class="fixed inset-0 grid-bg z-[-1]"></div>

<nav class="fixed top-0 w-full z-50 glass-header px-6 py-4">
    <div class="max-w-7xl mx-auto flex justify-between items-center">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-neon rounded-xl flex items-center justify-center text-black shadow-[0_0_20px_rgba(0,242,234,0.4)]">
                <i data-lucide="layout-grid" class="w-6 h-6"></i>
            </div>
            <span class="text-2xl font-black text-white">Math<span class="text-neon">Lab</span></span>
        </div>
        <a href="../index.html" class="flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-neon transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Home
        </a>
    </div>
</nav>

<main class="pt-32 pb-24 px-4 max-w-5xl mx-auto">
    <header class="text-center mb-16" data-aos="fade-down">
        <h1 class="text-4xl md:text-6xl font-black text-white mb-6">Math<span class="text-neon">Lab</span> Explorations</h1>
        <p class="text-slate-400">Select a topic to unlock specialized interactive experiments.</p>
    </header>

    <div class="space-y-6">
        <?php foreach ($topics as $key => $topic): ?>
            <div class="topic-section border border-white/5 rounded-3xl bg-card/50 overflow-hidden transition-all duration-300" id="section-<?php echo $key; ?>">

                <button onclick="toggleTopic('<?php echo $key; ?>')" class="topic-trigger w-full p-6 md:p-8 flex items-center justify-between transition-all hover:bg-white/5 text-left">
                    <div class="flex items-center gap-5">
                        <div class="w-14 h-14 rounded-2xl bg-dark border border-white/10 flex items-center justify-center shadow-inner">
                            <i data-lucide="<?php echo $topic['icon']; ?>" class="w-7 h-7 <?php echo $topic['color']; ?>"></i>
                        </div>
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-white"><?php echo $topic['title']; ?></h2>
                            <p class="text-sm text-slate-500 hidden md:block mt-1"><?php echo $topic['desc']; ?></p>
                        </div>
                    </div>
                    <i data-lucide="chevron-down" class="chevron-icon w-6 h-6 text-slate-500 transition-transform duration-500"></i>
                </button>

                <div class="topic-content">
                    <div class="p-6 md:p-8 pt-0">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 pt-6 border-t border-white/5">
                            <?php if (empty($topic['labs'])): ?>
                                <div class="col-span-full py-10 text-center opacity-30 italic">New labs coming soon...</div>
                            <?php else: ?>
                                <?php foreach ($topic['labs'] as $lab): ?>
                                    <a href="<?php echo $lab['link']; ?>" class="group bg-dark rounded-2xl border border-white/5 overflow-hidden hover:border-neon/40 transition-all duration-500">
                                        <div class="h-40 overflow-hidden relative">
                                            <img src="<?php echo $lab['img']; ?>" class="w-full h-full object-cover opacity-60 group-hover:opacity-100 group-hover:scale-110 transition-all duration-700" alt="Preview">
                                            <div class="absolute inset-0 bg-gradient-to-t from-dark via-transparent to-transparent"></div>
                                        </div>
                                        <div class="p-5">
                                            <h3 class="font-bold text-white group-hover:text-neon transition-colors"><?php echo $lab['title']; ?></h3>
                                            <p class="text-xs text-slate-500 mt-2 leading-relaxed line-clamp-2"><?php echo $lab['desc']; ?></p>
                                            <div class="mt-4 flex items-center text-xs font-bold text-neon opacity-0 group-hover:opacity-100 transition-all translate-x-[-10px] group-hover:translate-x-0">
                                                Launch Experiment <i data-lucide="play" class="w-3 h-3 ml-2 fill-current"></i>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</main>

<footer class="relative mt-10 pb-12 px-6">
    <div class="max-w-5xl mx-auto text-center md:text-left">
        <div class="w-full h-px bg-gradient-to-r from-transparent via-neon/30 to-transparent mb-10"></div>
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-neon/10 border border-neon/20 flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-neon"></i>
                </div>
                <div class="text-left">
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-tighter">Developed by</p>
                    <h4 class="text-sm font-black text-white hover:text-neon transition-colors cursor-default">Amin Madani</h4>
                </div>
            </div>
            <div class="opacity-50 hover:opacity-100 transition-opacity">
                <span class="text-lg font-black tracking-tighter">Math<span class="text-neon">Lab</span></span>
            </div>
            <div class="flex items-center gap-3">
                <a href="#" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 hover:border-neon/50 hover:text-neon transition-all"><i data-lucide="instagram" class="w-4 h-4"></i></a>
                <a href="#" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 hover:border-blue-400/50 hover:text-blue-400 transition-all"><i data-lucide="send" class="w-4 h-4"></i></a>
                <a href="#" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/5 border border-white/10 hover:border-purple-400/50 hover:text-purple-400 transition-all"><i data-lucide="github" class="w-4 h-4"></i></a>
            </div>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    lucide.createIcons();
    AOS.init();

    function toggleTopic(id) {
        const sections = document.querySelectorAll('.topic-section');
        const target = document.getElementById('section-' + id);
        sections.forEach(s => { if (s !== target) s.classList.remove('active'); });
        target.classList.toggle('active');
        if(target.classList.contains('active')) {
            setTimeout(() => { target.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 300);
        }
    }
</script>
</body>
</html>