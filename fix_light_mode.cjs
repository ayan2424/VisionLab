const fs = require('fs');
let file = fs.readFileSync('resources/views/welcome.blade.php', 'utf8');

// Replace bg-white/xx with responsive variant
file = file.replace(/bg-white\/(5|10|15|20|30|40|50|60|70|80|90)/g, (match, op) => {
    let lightOp = Math.max(1, Math.floor(parseInt(op) / 2));
    return `bg-black/${lightOp} dark:bg-white/${op}`;
});

// Replace bg-white/[0.xx] with responsive variant
file = file.replace(/bg-white\/\[(0\.\d+)\]/g, (match, op) => {
    let val = parseFloat(op);
    let lightVal = (val / 2).toFixed(2);
    return `bg-black/[${lightVal}] dark:bg-white/[${op}]`;
});

// Replace border-white/xx with responsive variant
file = file.replace(/border-white\/(5|10|15|20|30|40|50|60|70|80|90|50%)/g, (match, op) => {
    if(op === '50%') return 'border-black/20 dark:border-white/50%';
    let lightOp = Math.max(1, Math.floor(parseInt(op) / 2));
    return `border-black/${lightOp} dark:border-white/${op}`;
});

// Replace text-white with responsive variant (avoid selection:text-white and bg-rose text-white)
file = file.replace(/(?<!selection:|bg-rose\s|bg-cyan\s|bg-indigo\s|bg-emerald\s)text-white/g, 'text-black dark:text-white');

// Fix metallic-text CSS
file = file.replace(/\.metallic-text\s*\{[^}]+\}/g, `
        .metallic-text {
            -webkit-text-fill-color: transparent;
            color: rgba(0, 0, 0, 0);
            background: linear-gradient(rgb(0, 0, 0) 0%, rgb(80, 80, 80) 40%, rgb(120, 120, 120) 100%) text;
            -webkit-background-clip: text;
            background-clip: text;
        }
        html.dark .metallic-text {
            background: linear-gradient(rgb(255, 255, 255) 0%, rgb(229, 231, 235) 40%, rgb(148, 163, 184) 100%) text;
            -webkit-background-clip: text;
            background-clip: text;
        }
`);

// Same for aurora-text (if needed) but aurora text has color so it's probably fine.

fs.writeFileSync('resources/views/welcome.blade.php', file);
console.log('Fixed welcome.blade.php');
