import React, { useEffect, useMemo, useRef } from 'react';
import * as THREE from 'three';
import { Canvas, useFrame } from '@react-three/fiber';
import { ContactShadows, Environment, Float, Lightformer, RoundedBox } from '@react-three/drei';

export type HeroCoreMode = 'nix' | 'ai' | 'sha';

const MODE_META: Record<
    HeroCoreMode,
    {
        accent: string;
        badge: string;
        cardTitle: string;
        cardLine1: string;
        cardLine2: string;
        activeFile: string;
        terminal: string;
    }
> = {
    nix: {
        accent: '#38BDF8',
        badge: 'NIX ISOLATION · CRUN',
        cardTitle: 'CONTAINER ISOLATED',
        cardLine1: 'dev.nix · gcc13 + python312',
        cardLine2: 'CPU 1.4% · RAM 312 MB · 0 Drift',
        activeFile: '.vision/dev.nix',
        terminal: 'nix-shell .vision/dev.nix -> Toolchain locked (142ms) · 18/18 tests passed',
    },
    ai: {
        accent: '#FBBF24',
        badge: 'AI PATCH GATE · HITL',
        cardTitle: 'PATCH AWAITING REVIEW',
        cardLine1: 'src/allocator.c  (+4  -1)',
        cardLine2: 'Blocked direct write · Student gate',
        activeFile: 'src/allocator.c',
        terminal: 'ai_pending_patches #408 staged -> Waiting for explicit student approval',
    },
    sha: {
        accent: '#2DD4BF',
        badge: 'VSIX SHA-256 · SIGNED',
        cardTitle: 'CHECKSUM VERIFIED',
        cardLine1: 'visionlab-python-2.4.1.vsix',
        cardLine2: 'sha256:e3b0c442...98fc  MATCH',
        activeFile: 'extensions/policy.json',
        terminal: 'SyncWorkspaceExtensions -> 3 verified VSIX extensions mounted cleanly',
    },
};

/* ── High-DPI Live VisionLab IDE Screen Canvas Texture (1536×960) ────────── */
function createIdeScreenTexture(mode: HeroCoreMode): THREE.CanvasTexture {
    const canvas = document.createElement('canvas');
    canvas.width = 1536;
    canvas.height = 960;
    const ctx = canvas.getContext('2d')!;
    const meta = MODE_META[mode];

    const roundRect = (x: number, y: number, w: number, h: number, r: number) => {
        ctx.beginPath();
        ctx.roundRect(x, y, w, h, r);
    };

    // Deep IDE Obsidian Ground
    ctx.fillStyle = '#070B14';
    ctx.fillRect(0, 0, 1536, 960);

    // Subtle top ambient gradient wash
    const topGlow = ctx.createRadialGradient(1100, 120, 40, 1100, 120, 720);
    topGlow.addColorStop(0, `${meta.accent}24`);
    topGlow.addColorStop(1, 'rgba(7,11,20,0)');
    ctx.fillStyle = topGlow;
    ctx.fillRect(0, 0, 1536, 960);

    // Top 56px Slim VisionLab IDE Chrome Bar
    ctx.fillStyle = '#0B1220';
    ctx.fillRect(0, 0, 1536, 56);
    ctx.fillStyle = 'rgba(255,255,255,0.07)';
    ctx.fillRect(0, 55, 1536, 1);

    // Traffic lights
    const dots = ['#FB7185', '#FBBF24', '#2DD4BF'];
    dots.forEach((c, idx) => {
        ctx.fillStyle = c;
        ctx.beginPath();
        ctx.arc(34 + idx * 24, 28, 6.5, 0, Math.PI * 2);
        ctx.fill();
    });

    // Top workspace slug pill
    roundRect(128, 12, 340, 32, 16);
    ctx.fillStyle = 'rgba(255,255,255,0.06)';
    ctx.fill();
    ctx.fillStyle = '#E2E8F0';
    ctx.font = '600 17px "JetBrains Mono", monospace';
    ctx.fillText('cs301-systems-lab  ·  VS Code', 148, 34);

    // Top right live mode pill
    roundRect(1140, 11, 368, 34, 17);
    ctx.fillStyle = `${meta.accent}22`;
    ctx.fill();
    ctx.strokeStyle = `${meta.accent}66`;
    ctx.lineWidth = 1.5;
    ctx.stroke();
    ctx.fillStyle = meta.accent;
    ctx.beginPath();
    ctx.arc(1164, 28, 5, 0, Math.PI * 2);
    ctx.fill();
    ctx.font = '600 16px "JetBrains Mono", monospace';
    ctx.fillText(meta.badge, 1182, 34);

    // Left Explorer Sidebar (290px)
    ctx.fillStyle = '#090F1C';
    ctx.fillRect(0, 56, 290, 904);
    ctx.fillStyle = 'rgba(255,255,255,0.06)';
    ctx.fillRect(290, 56, 1, 904);

    ctx.fillStyle = '#64748B';
    ctx.font = '600 14px "JetBrains Mono", monospace';
    ctx.fillText('WORKSPACE EXPLORER', 28, 98);

    const files = [
        { name: '.vision/dev.nix', active: mode === 'nix' },
        { name: 'src/allocator.c', active: mode === 'ai' },
        { name: 'lab/scheduler.py', active: false },
        { name: 'extensions/policy.json', active: mode === 'sha' },
        { name: 'tests/slab_test.c', active: false },
        { name: 'README.md', active: false },
    ];

    files.forEach((f, idx) => {
        const y = 126 + idx * 46;
        if (f.active) {
            roundRect(16, y - 24, 258, 38, 19);
            ctx.fillStyle = `${meta.accent}22`;
            ctx.fill();
        }
        ctx.fillStyle = f.active ? '#F8FAFC' : '#94A3B8';
        ctx.font = f.active ? '600 17px "JetBrains Mono", monospace' : '400 16px "JetBrains Mono", monospace';
        ctx.fillText(f.name, 34, y);
    });

    // Bottom Explorer Quota Card
    roundRect(18, 760, 254, 168, 24);
    ctx.fillStyle = 'rgba(255,255,255,0.04)';
    ctx.fill();
    ctx.fillStyle = '#94A3B8';
    ctx.font = '600 14px "JetBrains Mono", monospace';
    ctx.fillText('CONTAINER TELEMETRY', 38, 796);
    ctx.fillStyle = '#F8FAFC';
    ctx.font = '600 18px "JetBrains Mono", monospace';
    ctx.fillText('CPU  1.4%   RAM 312MB', 38, 834);
    // Progress pill bar
    roundRect(38, 856, 214, 10, 5);
    ctx.fillStyle = 'rgba(255,255,255,0.08)';
    ctx.fill();
    roundRect(38, 856, 152, 10, 5);
    ctx.fillStyle = meta.accent;
    ctx.fill();
    ctx.fillStyle = '#2DD4BF';
    ctx.font = '500 14px "JetBrains Mono", monospace';
    ctx.fillText('● VisionGuard Active', 38, 902);

    // Editor Tab Bar
    ctx.fillStyle = '#0A101D';
    ctx.fillRect(291, 56, 1245, 50);
    ctx.fillStyle = 'rgba(255,255,255,0.06)';
    ctx.fillRect(291, 105, 1245, 1);

    roundRect(312, 65, 290, 34, 17);
    ctx.fillStyle = '#131E33';
    ctx.fill();
    ctx.fillStyle = '#F8FAFC';
    ctx.font = '600 16px "JetBrains Mono", monospace';
    ctx.fillText(meta.activeFile, 336, 88);

    // Editor Code Area
    const drawCodeLine = (
        lineNo: number,
        y: number,
        text: string,
        color: string,
        bg?: string
    ) => {
        if (bg) {
            roundRect(314, y - 27, 1180, 38, 12);
            ctx.fillStyle = bg;
            ctx.fill();
        }
        ctx.fillStyle = '#475569';
        ctx.font = '400 17px "JetBrains Mono", monospace';
        ctx.fillText(String(lineNo).padStart(2, ' '), 328, y);
        ctx.fillStyle = color;
        ctx.font = '500 19px "JetBrains Mono", monospace';
        ctx.fillText(text, 378, y);
    };

    if (mode === 'nix') {
        const lines: Array<[string, string, string?]> = [
            ['# VisionLab Deterministic Course Profile · CS-301', '#64748B'],
            ['{ pkgs ? import <nixpkgs> {} }:', '#38BDF8'],
            ['', '#E2E8F0'],
            ['pkgs.mkShell {', '#E2E8F0'],
            ['  name = "visionlab-cs301-systems";', '#2DD4BF'],
            ['  packages = with pkgs; [', '#E2E8F0'],
            ['    gcc13            # Deterministic C/C++ compiler', '#93C5FD', 'rgba(56,189,248,0.10)'],
            ['    python312        # Grading harness runtime', '#93C5FD', 'rgba(56,189,248,0.10)'],
            ['    valgrind gdb     # Memory-safety & debuggers', '#93C5FD'],
            ['  ];', '#E2E8F0'],
            ['  shellHook = "echo VisionLab Nix Shell Locked";', '#FBBF24'],
            ['}', '#E2E8F0'],
        ];
        lines.forEach(([txt, col, bg], i) => drawCodeLine(i + 1, 158 + i * 42, txt, col, bg));
    } else if (mode === 'ai') {
        // Amber HITL Banner inside IDE
        roundRect(322, 126, 1168, 52, 26);
        ctx.fillStyle = 'rgba(251,191,36,0.13)';
        ctx.fill();
        ctx.strokeStyle = 'rgba(251,191,36,0.4)';
        ctx.lineWidth = 1.5;
        ctx.stroke();
        ctx.fillStyle = '#FDE68A';
        ctx.font = '600 17px "JetBrains Mono", monospace';
        ctx.fillText('AI PATCH #408 STAGED · Review diff before writing to workspace', 350, 158);

        const lines: Array<[string, string, string?]> = [
            ['void *slab_alloc(Arena *arena, size_t size) {', '#94A3B8'],
            ['    BlockHeader *hdr = arena_alloc_header(size);', '#CBD5E1'],
            ['-   return commit_slot(hdr->next, hdr);', '#FDA4AF', 'rgba(244,63,94,0.18)'],
            ['+   if (hdr == NULL || hdr->offset + size > arena->limit) {', '#5EEAD4', 'rgba(45,212,191,0.16)'],
            ['+       return ARENA_ERR_BOUNDS;', '#5EEAD4', 'rgba(45,212,191,0.16)'],
            ['+   }', '#5EEAD4', 'rgba(45,212,191,0.16)'],
            ['+   return commit_slot(hdr->next, hdr);', '#5EEAD4', 'rgba(45,212,191,0.16)'],
            ['}', '#94A3B8'],
        ];
        lines.forEach(([txt, col, bg], i) => drawCodeLine(40 + i, 232 + i * 44, txt, col, bg));

        // Pill action hints inside screen
        roundRect(1040, 610, 210, 44, 22);
        ctx.fillStyle = 'rgba(244,63,94,0.22)';
        ctx.fill();
        ctx.fillStyle = '#FDA4AF';
        ctx.font = '600 16px "JetBrains Mono", monospace';
        ctx.fillText('✕ Reject Patch', 1072, 638);

        roundRect(1268, 610, 220, 44, 22);
        ctx.fillStyle = '#0284C7';
        ctx.fill();
        ctx.fillStyle = '#FFFFFF';
        ctx.fillText('✓ Approve Patch', 1296, 638);
    } else {
        const lines: Array<[string, string, string?]> = [
            ['{', '#94A3B8'],
            ['  "registry": "institutional-signed-vsix",', '#2DD4BF'],
            ['  "artifact": "visionlab-python-2.4.1.vsix",', '#F8FAFC'],
            ['  "sha256": "e3b0c44298fc1c149afbf4c8996fb924",', '#5EEAD4', 'rgba(45,212,191,0.14)'],
            ['  "verification": "MATCH_CONFIRMED",', '#2DD4BF', 'rgba(45,212,191,0.14)'],
            ['  "policy_scope": "course:CS-301",', '#93C5FD'],
            ['  "copilot_blocked": true,', '#FBBF24'],
            ['  "synced_containers": 42', '#E2E8F0'],
            ['}', '#94A3B8'],
        ];
        lines.forEach(([txt, col, bg], i) => drawCodeLine(i + 1, 165 + i * 46, txt, col, bg));
    }

    // Integrated Bottom Terminal Dock
    roundRect(316, 706, 1188, 224, 28);
    ctx.fillStyle = '#05080F';
    ctx.fill();
    ctx.strokeStyle = 'rgba(255,255,255,0.08)';
    ctx.lineWidth = 1.5;
    ctx.stroke();

    ctx.fillStyle = '#64748B';
    ctx.font = '600 14px "JetBrains Mono", monospace';
    ctx.fillText('TERMINAL  ·  crun://vl_ws_408', 346, 746);

    ctx.fillStyle = meta.accent;
    ctx.font = '600 18px "JetBrains Mono", monospace';
    ctx.fillText('coder@visionlab ~/cs301 $', 346, 796);
    ctx.fillStyle = '#E2E8F0';
    ctx.font = '500 18px "JetBrains Mono", monospace';
    ctx.fillText(meta.terminal, 346, 836);
    ctx.fillStyle = '#2DD4BF';
    ctx.fillText('✔ Ready in 142ms  ·  Zero host bleed  ·  Audit stream connected', 346, 882);

    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;
    texture.anisotropy = 8;
    texture.needsUpdate = true;
    return texture;
}

/* ── Floating Holographic Telemetry Card Texture (768×280) ───────────────── */
function createFloatingCardTexture(mode: HeroCoreMode, isDark: boolean): THREE.CanvasTexture {
    const canvas = document.createElement('canvas');
    canvas.width = 768;
    canvas.height = 280;
    const ctx = canvas.getContext('2d')!;
    const meta = MODE_META[mode];

    ctx.clearRect(0, 0, 768, 280);
    ctx.beginPath();
    ctx.roundRect(8, 8, 752, 264, 56);
    ctx.fillStyle = isDark ? 'rgba(9, 15, 28, 0.94)' : 'rgba(255, 255, 255, 0.96)';
    ctx.fill();
    ctx.strokeStyle = `${meta.accent}88`;
    ctx.lineWidth = 3;
    ctx.stroke();

    // Accent status dot
    ctx.fillStyle = meta.accent;
    ctx.beginPath();
    ctx.arc(62, 76, 10, 0, Math.PI * 2);
    ctx.fill();

    ctx.fillStyle = meta.accent;
    ctx.font = '700 22px "JetBrains Mono", monospace';
    ctx.fillText(meta.cardTitle, 90, 84);

    ctx.fillStyle = isDark ? '#F8FAFC' : '#0B1220';
    ctx.font = '700 32px "Satoshi", sans-serif';
    ctx.fillText(meta.cardLine1, 52, 152);

    ctx.fillStyle = isDark ? '#94A3B8' : '#475569';
    ctx.font = '500 23px "JetBrains Mono", monospace';
    ctx.fillText(meta.cardLine2, 52, 206);

    const texture = new THREE.CanvasTexture(canvas);
    texture.colorSpace = THREE.SRGBColorSpace;
    texture.anisotropy = 4;
    texture.needsUpdate = true;
    return texture;
}

/* ── Subtle Seamless Studio Backdrop Shader (Exact Match to --vl-bg) ─────── */
const STUDIO_VERT = /* glsl */ `
varying vec2 vUv;
void main() {
    vUv = uv;
    gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
}
`;

const STUDIO_FRAG = /* glsl */ `
uniform float uTime;
uniform vec2 uPointer;
uniform vec3 uBg;
uniform vec3 uAccent;
uniform float uDark;
varying vec2 vUv;

void main() {
    vec2 uv = vUv;
    vec2 center = vec2(0.72, 0.50) + uPointer * 0.03;
    float d = length((uv - center) * vec2(1.25, 1.0));
    float glow = exp(-d * 2.55);
    float secondary = exp(-length((uv - vec2(0.55, 0.78)) * vec2(1.4, 1.1)) * 3.4);
    float pulse = 0.92 + 0.08 * sin(uTime * 0.7);

    float intensity = mix(0.14, 0.34, uDark) * pulse;
    vec3 col = mix(uBg, uAccent, glow * intensity);
    col = mix(col, vec3(0.17, 0.83, 0.75), secondary * intensity * 0.35);

    // Seamless fade to exact page background near top, left, and bottom edges
    float bottomFade = smoothstep(0.0, 0.24, uv.y);
    float leftFade = smoothstep(0.18, 0.55, uv.x);
    col = mix(uBg, col, bottomFade * leftFade);

    gl_FragColor = vec4(col, 1.0);
}
`;

function SeamlessStudioBackdrop({ mode, isDark }: { mode: HeroCoreMode; isDark: boolean }) {
    const matRef = useRef<THREE.ShaderMaterial>(null);
    const targetBg = useMemo(() => new THREE.Color(), []);
    const targetAccent = useMemo(() => new THREE.Color(), []);

    const uniforms = useMemo(
        () => ({
            uTime: { value: 0 },
            uPointer: { value: new THREE.Vector2(0, 0) },
            uBg: { value: new THREE.Color(isDark ? '#05080F' : '#F4F7FB') },
            uAccent: { value: new THREE.Color(MODE_META[mode].accent) },
            uDark: { value: isDark ? 1.0 : 0.0 },
        }),
        []
    );

    useFrame((state, delta) => {
        if (!matRef.current) return;
        matRef.current.uniforms.uTime.value += delta;
        matRef.current.uniforms.uPointer.value.lerp(state.pointer, 0.04);
        targetBg.set(isDark ? '#05080F' : '#F4F7FB');
        targetAccent.set(MODE_META[mode].accent);
        matRef.current.uniforms.uBg.value.lerp(targetBg, 0.3);
        matRef.current.uniforms.uAccent.value.lerp(targetAccent, 0.14);
        matRef.current.uniforms.uDark.value = THREE.MathUtils.lerp(
            matRef.current.uniforms.uDark.value,
            isDark ? 1.0 : 0.0,
            0.3
        );
    });

    return (
        <mesh position={[0, 0, -6.5]} scale={[28, 16, 1]}>
            <planeGeometry args={[1, 1]} />
            <shaderMaterial
                ref={matRef}
                vertexShader={STUDIO_VERT}
                fragmentShader={STUDIO_FRAG}
                uniforms={uniforms}
                depthWrite={false}
                toneMapped={false}
            />
        </mesh>
    );
}

/* ── Studio-Lit CNC Titanium Laptop + Floating Depth Architecture ────────── */
function VisionBookWorkstation({ mode, isDark }: { mode: HeroCoreMode; isDark: boolean }) {
    const groupRef = useRef<THREE.Group>(null);
    const lidRef = useRef<THREE.Group>(null);
    const keysRef = useRef<THREE.InstancedMesh>(null);
    const stackRef = useRef<THREE.Group>(null);

    const screenTexture = useMemo(() => createIdeScreenTexture(mode), [mode]);
    const cardTexture = useMemo(() => createFloatingCardTexture(mode, isDark), [mode, isDark]);

    useEffect(() => {
        return () => {
            screenTexture.dispose();
        };
    }, [screenTexture]);

    useEffect(() => {
        return () => {
            cardTexture.dispose();
        };
    }, [cardTexture]);

    // Populate 65 sculpted matte laptop keycaps in a single instanced draw call
    useEffect(() => {
        if (!keysRef.current) return;
        const dummy = new THREE.Object3D();
        let idx = 0;
        const rows = 5;
        const cols = 13;
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const x = -1.24 + c * 0.207;
                const z = -0.68 + r * 0.19;
                dummy.position.set(x, 0.064, z);
                dummy.scale.set(1, 1, 1);
                dummy.updateMatrix();
                keysRef.current.setMatrixAt(idx++, dummy.matrix);
            }
        }
        keysRef.current.instanceMatrix.needsUpdate = true;
    }, []);

    useFrame((state, delta) => {
        const t = state.clock.elapsedTime;
        if (groupRef.current) {
            const targetY = -0.36 + state.pointer.x * 0.16;
            const targetX = 0.12 - state.pointer.y * 0.09;
            groupRef.current.rotation.y = THREE.MathUtils.lerp(groupRef.current.rotation.y, targetY, 0.05);
            groupRef.current.rotation.x = THREE.MathUtils.lerp(groupRef.current.rotation.x, targetX, 0.05);
        }

        if (lidRef.current) {
            const lidAngle = mode === 'ai' ? -0.22 : mode === 'sha' ? -0.16 : -0.19;
            lidRef.current.rotation.x = THREE.MathUtils.lerp(lidRef.current.rotation.x, lidAngle, 0.06);
        }

        if (stackRef.current) {
            stackRef.current.rotation.y += delta * 0.35;
            stackRef.current.position.y = 0.22 + Math.sin(t * 1.4) * 0.06;
        }
    });

    const chassisColor = isDark ? '#2B3548' : '#D4DDE9';
    const trimColor = isDark ? '#141B29' : '#94A3B8';
    const accent = MODE_META[mode].accent;

    return (
        <Float speed={1.1} rotationIntensity={0.05} floatIntensity={0.14}>
            <group ref={groupRef} position={[1.64, -0.54, 0.1]} rotation={[0.11, -0.34, 0]} scale={0.84}>
                {/* ── 1. CNC Unibody Laptop Base Deck ── */}
                <RoundedBox args={[3.36, 0.11, 2.28]} radius={0.045} smoothness={6} position={[0, 0, 0]}>
                    <meshPhysicalMaterial
                        color={chassisColor}
                        metalness={0.9}
                        roughness={0.24}
                        clearcoat={0.4}
                        clearcoatRoughness={0.18}
                    />
                </RoundedBox>

                {/* Subtle glowing edge accent line on front chamfer */}
                <mesh position={[0, 0.02, 1.142]}>
                    <boxGeometry args={[0.52, 0.012, 0.008]} />
                    <meshStandardMaterial color="#05080F" emissive={accent} emissiveIntensity={2.0} toneMapped={false} />
                </mesh>

                {/* Matte Recessed Keyboard Deck Well */}
                <RoundedBox args={[2.86, 0.02, 1.06]} radius={0.015} smoothness={4} position={[0, 0.052, -0.28]}>
                    <meshStandardMaterial color="#0A0F1A" metalness={0.25} roughness={0.82} />
                </RoundedBox>

                {/* 65 Instanced Sculpted Matte Keycaps */}
                <instancedMesh ref={keysRef} args={[undefined, undefined, 65]}>
                    <boxGeometry args={[0.175, 0.024, 0.155]} />
                    <meshStandardMaterial color="#171E2C" metalness={0.2} roughness={0.68} />
                </instancedMesh>

                {/* Frosted Glass Haptic Trackpad */}
                <RoundedBox args={[1.15, 0.012, 0.68]} radius={0.025} smoothness={4} position={[0, 0.055, 0.66]}>
                    <meshPhysicalMaterial
                        color={isDark ? '#1B2435' : '#C5D1E0'}
                        metalness={0.5}
                        roughness={0.28}
                        clearcoat={0.6}
                    />
                </RoundedBox>

                {/* Precision Machined Hinge Cylinder */}
                <mesh position={[0, 0.075, -1.06]} rotation={[0, 0, Math.PI / 2]}>
                    <cylinderGeometry args={[0.042, 0.042, 2.55, 24]} />
                    <meshStandardMaterial color={trimColor} metalness={0.94} roughness={0.2} />
                </mesh>

                {/* ── 2. Ultra-Thin Retina Display Lid with Live VisionLab IDE ── */}
                <group ref={lidRef} position={[0, 0.08, -1.08]} rotation={[-0.19, 0, 0]}>
                    {/* Rear Anodized Aluminum Lid Shell */}
                    <RoundedBox args={[3.36, 2.16, 0.062]} radius={0.036} smoothness={6} position={[0, 1.08, 0]}>
                        <meshPhysicalMaterial
                            color={chassisColor}
                            metalness={0.92}
                            roughness={0.22}
                            clearcoat={0.45}
                            clearcoatRoughness={0.14}
                        />
                    </RoundedBox>

                    {/* Glossy Jet-Black Screen Bezel */}
                    <RoundedBox args={[3.26, 2.06, 0.012]} radius={0.028} smoothness={4} position={[0, 1.08, 0.03]}>
                        <meshPhysicalMaterial
                            color="#05080F"
                            metalness={0.1}
                            roughness={0.08}
                            clearcoat={1.0}
                            clearcoatRoughness={0.05}
                        />
                    </RoundedBox>

                    {/* Live 2K VisionLab VS Code Display Plane */}
                    <mesh position={[0, 1.08, 0.038]}>
                        <planeGeometry args={[3.14, 1.96]} />
                        <meshBasicMaterial map={screenTexture} toneMapped={false} />
                    </mesh>

                    {/* Subtle Screen Ambient Bounce Light onto Keyboard */}
                    <pointLight position={[0, 1.0, 0.8]} intensity={isDark ? 1.4 : 0.4} distance={4.5} color={accent} />

                    {/* ── 3. Floating Purposeful Telemetry Pill Card (Upper-Right Depth Layer) ── */}
                    <Float speed={1.8} rotationIntensity={0.04} floatIntensity={0.12}>
                        <group position={[0.78, 1.68, 0.44]} rotation={[0, -0.14, 0]}>
                            <mesh>
                                <planeGeometry args={[1.52, 0.56]} />
                                <meshBasicMaterial map={cardTexture} transparent toneMapped={false} />
                            </mesh>
                        </group>
                    </Float>
                </group>

                {/* ── 4. Floating Isolated Container Stack (Lower-Left Depth Layer) ── */}
                <group ref={stackRef} position={[-1.74, 0.14, 0.52]} scale={0.9}>
                    {[0, 0.24, 0.48].map((yPos, i) => (
                        <group key={i} position={[0, yPos, 0]}>
                            <RoundedBox args={[0.64, 0.14, 0.64]} radius={0.045} smoothness={5}>
                                <meshPhysicalMaterial
                                    color={isDark ? '#132036' : '#E2E8F0'}
                                    metalness={0.65}
                                    roughness={0.18}
                                    clearcoat={0.9}
                                    transparent
                                    opacity={0.9}
                                />
                            </RoundedBox>
                            <mesh position={[0, 0, 0.324]}>
                                <boxGeometry args={[0.38, 0.022, 0.01]} />
                                <meshStandardMaterial
                                    color="#05080F"
                                    emissive={i === 2 ? accent : '#38BDF8'}
                                    emissiveIntensity={i === 2 ? 2.4 : 1.1}
                                    toneMapped={false}
                                />
                            </mesh>
                        </group>
                    ))}
                </group>
            </group>
        </Float>
    );
}

function StudioCameraRig() {
    useFrame((state) => {
        const tx = state.pointer.x * 0.28;
        const ty = 0.22 + state.pointer.y * 0.18;
        state.camera.position.x = THREE.MathUtils.lerp(state.camera.position.x, tx, 0.04);
        state.camera.position.y = THREE.MathUtils.lerp(state.camera.position.y, ty, 0.04);
        state.camera.lookAt(0.32, 0.18, 0);
    });
    return null;
}

export default function HeroStudioStage({
    mode,
    isDark,
}: {
    mode: HeroCoreMode;
    isDark: boolean;
}) {
    return (
        <div
            id="vl-hero-3d-stage"
            className="pointer-events-none absolute inset-0 h-full w-full overflow-hidden"
            aria-hidden="true"
        >
            <Canvas
                dpr={[1, 1.75]}
                camera={{ position: [0, 0.22, 6.2], fov: 36, near: 0.1, far: 40 }}
                gl={{
                    antialias: true,
                    alpha: false,
                    powerPreference: 'high-performance',
                    toneMapping: THREE.ACESFilmicToneMapping,
                    toneMappingExposure: isDark ? 1.1 : 1.0,
                }}
            >
                <SeamlessStudioBackdrop mode={mode} isDark={isDark} />

                <ambientLight intensity={isDark ? 0.5 : 0.85} />
                <directionalLight position={[4.5, 6.5, 5]} intensity={isDark ? 2.1 : 2.2} color="#F8FAFC" />
                <directionalLight position={[-4, 3, 3]} intensity={isDark ? 1.1 : 0.9} color="#38BDF8" />

                {/* Studio Softbox IBL Reflections across CNC Titanium & Glass */}
                <Environment resolution={256} frames={1}>
                    <group rotation={[-Math.PI / 3, 0, 1]}>
                        <Lightformer
                            form="rect"
                            intensity={isDark ? 3.2 : 2.8}
                            position={[0, 5, 1]}
                            scale={[11, 2.8, 1]}
                            color="#FFFFFF"
                        />
                        <Lightformer
                            form="ring"
                            intensity={2.8}
                            position={[5, 2, 3]}
                            scale={4.5}
                            color="#38BDF8"
                        />
                        <Lightformer
                            form="rect"
                            intensity={2.0}
                            position={[-5, 0, 2]}
                            scale={[7, 2, 1]}
                            color="#2DD4BF"
                        />
                    </group>
                </Environment>

                <VisionBookWorkstation mode={mode} isDark={isDark} />

                <ContactShadows
                    position={[1.72, -0.92, 0.1]}
                    opacity={isDark ? 0.65 : 0.28}
                    scale={8.5}
                    blur={2.4}
                    far={2.6}
                    color={isDark ? '#000000' : '#0F172A'}
                />

                <StudioCameraRig />
            </Canvas>
        </div>
    );
}
