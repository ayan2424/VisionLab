import React, { useEffect, useMemo, useRef, useState } from 'react';
import * as THREE from 'three';
import { Canvas, useFrame } from '@react-three/fiber';
import { ContactShadows, Environment, Lightformer, OrbitControls, RoundedBox } from '@react-three/drei';
import { AnimatePresence, motion } from 'motion/react';
import {
    ArrowUpRight,
    Bot,
    Send,
    Sparkles,
    Terminal,
    X,
} from 'lucide-react';
import { cn } from '@/lib/cn';

export interface LabSeatTelemetry {
    id: string;
    seatLabel: string;
    studentName: string;
    workspaceSlug: string;
    course: string;
    language: string;
    status: 'running' | 'reviewing_patch' | 'exam_locked';
    cpu: string;
    memory: string;
    humanPct: number;
    position: [number, number, number];
    shirtColor: string;
}

const DEFAULT_SEATS: LabSeatTelemetry[] = [
    {
        id: 'seat-1',
        seatLabel: 'Seat A-01',
        studentName: 'Amina Tariq',
        workspaceSlug: 'cs301-allocator-01',
        course: 'CS-301 Operating Systems',
        language: 'C / Nix GCC 13',
        status: 'running',
        cpu: '2.4%',
        memory: '348 MB',
        humanPct: 91,
        position: [-2.4, 0, 0.35],
        shirtColor: '#0EA5E9',
    },
    {
        id: 'seat-2',
        seatLabel: 'Seat A-02',
        studentName: 'Bilal Ahmed',
        workspaceSlug: 'cs301-allocator-02',
        course: 'CS-301 Operating Systems',
        language: 'C / Valgrind',
        status: 'reviewing_patch',
        cpu: '4.1%',
        memory: '412 MB',
        humanPct: 84,
        position: [-0.8, 0, 0.35],
        shirtColor: '#F59E0B',
    },
    {
        id: 'seat-3',
        seatLabel: 'Seat A-03',
        studentName: 'Zara Siddiqui',
        workspaceSlug: 'cs204-ml-lab-03',
        course: 'CS-204 Machine Learning',
        language: 'Python 3.12',
        status: 'exam_locked',
        cpu: '8.7%',
        memory: '690 MB',
        humanPct: 96,
        position: [0.8, 0, 0.35],
        shirtColor: '#14B8A6',
    },
    {
        id: 'seat-4',
        seatLabel: 'Seat B-01',
        studentName: 'Hamza Khan',
        workspaceSlug: 'se210-web-04',
        course: 'SE-210 Full-Stack Lab',
        language: 'TypeScript / Node 22',
        status: 'running',
        cpu: '1.9%',
        memory: '295 MB',
        humanPct: 88,
        position: [-2.4, 0, 1.95],
        shirtColor: '#2563EB',
    },
    {
        id: 'seat-5',
        seatLabel: 'Seat B-02',
        studentName: 'Sana Farooq',
        workspaceSlug: 'cs301-scheduler-05',
        course: 'CS-301 Operating Systems',
        language: 'Python 3.12',
        status: 'reviewing_patch',
        cpu: '3.2%',
        memory: '380 MB',
        humanPct: 79,
        position: [-0.8, 0, 1.95],
        shirtColor: '#0284C7',
    },
    {
        id: 'seat-6',
        seatLabel: 'Seat B-03',
        studentName: 'Omar Malik',
        workspaceSlug: 'cs410-rust-06',
        course: 'CS-410 Distributed Systems',
        language: 'Rust / Cargo',
        status: 'running',
        cpu: '5.5%',
        memory: '510 MB',
        humanPct: 93,
        position: [0.8, 0, 1.95],
        shirtColor: '#0D9488',
    },
];

/* ── Live Wall Telemetry Screen Texture (1024×512) ───────────────────────── */
function createWallBoardTexture(activeCount: number, patchCount: number): THREE.CanvasTexture {
    const canvas = document.createElement('canvas');
    canvas.width = 1024;
    canvas.height = 512;
    const ctx = canvas.getContext('2d')!;

    ctx.fillStyle = '#070C16';
    ctx.fillRect(0, 0, 1024, 512);

    ctx.fillStyle = '#38BDF8';
    ctx.font = '700 22px "JetBrains Mono", monospace';
    ctx.fillText('VISIONLAB LIVE CLUSTER WALL · HALL 04', 44, 60);

    ctx.fillStyle = '#2DD4BF';
    ctx.font = '700 64px "Satoshi", sans-serif';
    ctx.fillText(`${activeCount} Active Seats`, 44, 152);

    ctx.fillStyle = '#FBBF24';
    ctx.font = '600 28px "JetBrains Mono", monospace';
    ctx.fillText(`${patchCount} AI Patches Staged · 0 Policy Violations`, 44, 206);

    // Capsule bar histogram drawn cleanly on wall monitor
    const bars = [48, 72, 61, 88, 95, 76, 84, 92, 68, 79, 86, 94];
    bars.forEach((h, i) => {
        const x = 48 + i * 78;
        const barH = h * 2.1;
        const y = 456 - barH;
        ctx.beginPath();
        ctx.roundRect(x, y, 42, barH, 21);
        const grad = ctx.createLinearGradient(0, y, 0, 456);
        grad.addColorStop(0, i === 4 || i === 11 ? '#2DD4BF' : '#38BDF8');
        grad.addColorStop(1, '#1D4ED8');
        ctx.fillStyle = grad;
        ctx.fill();
    });

    const tex = new THREE.CanvasTexture(canvas);
    tex.colorSpace = THREE.SRGBColorSpace;
    tex.needsUpdate = true;
    return tex;
}

/* ── Individual Interactive 3D Lab Workstation + Stylized Student Avatar ─── */
function LabDeskUnit({
    seat,
    selected,
    isDark,
    onSelect,
}: {
    seat: LabSeatTelemetry;
    selected: boolean;
    isDark: boolean;
    onSelect: (seat: LabSeatTelemetry) => void;
}) {
    const [hovered, setHovered] = useState(false);
    const beaconRef = useRef<THREE.Mesh>(null);

    const statusColor =
        seat.status === 'reviewing_patch'
            ? '#FBBF24'
            : seat.status === 'exam_locked'
            ? '#2DD4BF'
            : '#38BDF8';

    useFrame((state) => {
        if (beaconRef.current) {
            const scale = selected ? 1.25 + Math.sin(state.clock.elapsedTime * 4) * 0.15 : 1;
            beaconRef.current.scale.setScalar(scale);
        }
    });

    return (
        <group
            position={seat.position}
            onClick={(e) => {
                e.stopPropagation();
                onSelect(seat);
            }}
            onPointerOver={(e) => {
                e.stopPropagation();
                setHovered(true);
                document.body.style.cursor = 'pointer';
            }}
            onPointerOut={() => {
                setHovered(false);
                document.body.style.cursor = 'auto';
            }}
        >
            {/* Selection Floor Halo Ring */}
            {(selected || hovered) && (
                <mesh rotation={[-Math.PI / 2, 0, 0]} position={[0, 0.015, 0.05]}>
                    <ringGeometry args={[0.56, 0.68, 48]} />
                    <meshBasicMaterial color={statusColor} transparent opacity={selected ? 0.85 : 0.45} />
                </mesh>
            )}

            {/* Sculpted Architectural Desk Top */}
            <RoundedBox args={[1.18, 0.07, 0.66]} radius={0.03} smoothness={4} position={[0, 0.56, 0]}>
                <meshStandardMaterial
                    color={isDark ? '#1E293B' : '#FFFFFF'}
                    roughness={0.35}
                    metalness={0.12}
                />
            </RoundedBox>

            {/* Desk Legs */}
            {[-0.5, 0.5].map((lx) => (
                <mesh key={lx} position={[lx, 0.28, 0]}>
                    <boxGeometry args={[0.055, 0.54, 0.52]} />
                    <meshStandardMaterial color={isDark ? '#0F172A' : '#CBD5E1'} metalness={0.6} roughness={0.35} />
                </mesh>
            ))}

            {/* Laptop on Desk */}
            <RoundedBox args={[0.42, 0.025, 0.28]} radius={0.01} position={[0, 0.61, -0.02]}>
                <meshStandardMaterial color="#334155" metalness={0.85} roughness={0.25} />
            </RoundedBox>
            <group position={[0, 0.74, -0.15]} rotation={[-0.22, 0, 0]}>
                <RoundedBox args={[0.42, 0.26, 0.02]} radius={0.01}>
                    <meshStandardMaterial color="#1E293B" metalness={0.8} roughness={0.25} />
                </RoundedBox>
                <mesh position={[0, 0, 0.012]}>
                    <planeGeometry args={[0.37, 0.21]} />
                    <meshStandardMaterial
                        color="#05080F"
                        emissive={statusColor}
                        emissiveIntensity={selected ? 1.5 : 0.85}
                    />
                </mesh>
            </group>

            {/* Stylized Clean Student Figure Seated at Desk */}
            <group position={[0, 0, 0.34]}>
                {/* Chair Base */}
                <RoundedBox args={[0.42, 0.06, 0.4]} radius={0.025} position={[0, 0.34, 0]}>
                    <meshStandardMaterial color="#1E293B" roughness={0.5} />
                </RoundedBox>
                <RoundedBox args={[0.4, 0.38, 0.06]} radius={0.025} position={[0, 0.54, 0.18]}>
                    <meshStandardMaterial color="#1E293B" roughness={0.5} />
                </RoundedBox>

                {/* Torso */}
                <RoundedBox args={[0.34, 0.38, 0.22]} radius={0.09} position={[0, 0.56, -0.02]}>
                    <meshStandardMaterial color={seat.shirtColor} roughness={0.55} />
                </RoundedBox>

                {/* Head */}
                <mesh position={[0, 0.9, -0.02]}>
                    <sphereGeometry args={[0.145, 24, 24]} />
                    <meshStandardMaterial color="#E2B89B" roughness={0.62} />
                </mesh>
                {/* Hair Cap */}
                <mesh position={[0, 0.95, 0.01]} rotation={[-0.25, 0, 0]}>
                    <sphereGeometry args={[0.15, 20, 16, 0, Math.PI * 2, 0, Math.PI * 0.56]} />
                    <meshStandardMaterial color="#1E293B" roughness={0.75} />
                </mesh>
            </group>

            {/* Floating Status Beacon Orb above Seat */}
            <mesh ref={beaconRef} position={[0.46, 0.92, -0.14]}>
                <sphereGeometry args={[0.055, 16, 16]} />
                <meshStandardMaterial
                    color={statusColor}
                    emissive={statusColor}
                    emissiveIntensity={2.0}
                    toneMapped={false}
                />
            </mesh>
        </group>
    );
}

/* ── Complete 3D Isometric University Coding Lab Scene ───────────────────── */
function IsometricLabScene({
    seats,
    selectedSeat,
    isDark,
    onSelectSeat,
}: {
    seats: LabSeatTelemetry[];
    selectedSeat: LabSeatTelemetry;
    isDark: boolean;
    onSelectSeat: (seat: LabSeatTelemetry) => void;
}) {
    const wallTexture = useMemo(
        () =>
            createWallBoardTexture(
                seats.length,
                seats.filter((s) => s.status === 'reviewing_patch').length
            ),
        [seats]
    );

    useEffect(() => {
        return () => wallTexture.dispose();
    }, [wallTexture]);

    const floorColor = isDark ? '#0D1524' : '#E7D5C0';
    const wallColor = isDark ? '#0A111E' : '#F8FAFC';

    return (
        <group position={[0.25, -0.75, 0]}>
            {/* Studio Architectural Floor Slab */}
            <RoundedBox args={[8.6, 0.18, 5.4]} radius={0.08} smoothness={4} position={[0, -0.09, 0.7]}>
                <meshStandardMaterial color={floorColor} roughness={0.62} metalness={0.08} />
            </RoundedBox>

            {/* Back Architectural Wall */}
            <RoundedBox args={[8.6, 2.55, 0.16]} radius={0.06} smoothness={4} position={[0, 1.22, -1.92]}>
                <meshStandardMaterial color={wallColor} roughness={0.55} />
            </RoundedBox>

            {/* Wall-Mounted Live Telemetry Wall Screen */}
            <group position={[-0.75, 1.38, -1.8]}>
                <RoundedBox args={[3.5, 1.65, 0.08]} radius={0.05} smoothness={4}>
                    <meshStandardMaterial color="#05080F" metalness={0.6} roughness={0.25} />
                </RoundedBox>
                <mesh position={[0, 0, 0.046]}>
                    <planeGeometry args={[3.32, 1.5]} />
                    <meshBasicMaterial map={wallTexture} toneMapped={false} />
                </mesh>
            </group>

            {/* Secondary Instructor / Exam Proctor Wall Monitor */}
            <group position={[2.45, 1.42, -1.8]}>
                <RoundedBox args={[1.65, 1.12, 0.08]} radius={0.05} smoothness={4}>
                    <meshStandardMaterial color="#05080F" metalness={0.6} roughness={0.25} />
                </RoundedBox>
                <mesh position={[0, 0, 0.046]}>
                    <planeGeometry args={[1.5, 0.96]} />
                    <meshStandardMaterial color="#091322" emissive="#0EA5E9" emissiveIntensity={0.55} />
                </mesh>
            </group>

            {/* 6 Interactive Student Coding Desks */}
            {seats.map((seat) => (
                <LabDeskUnit
                    key={seat.id}
                    seat={seat}
                    selected={selectedSeat.id === seat.id}
                    isDark={isDark}
                    onSelect={onSelectSeat}
                />
            ))}

            {/* Right-Side Seminar Collaboration Round Table (Like Video 3!) */}
            <group position={[2.75, 0, 1.25]}>
                {/* Round Studio Rug */}
                <mesh rotation={[-Math.PI / 2, 0, 0]} position={[0, 0.01, 0]}>
                    <circleGeometry args={[1.18, 48]} />
                    <meshStandardMaterial color={isDark ? '#142034' : '#F1F5F9'} roughness={0.85} />
                </mesh>
                {/* Round Table */}
                <mesh position={[0, 0.56, 0]}>
                    <cylinderGeometry args={[0.76, 0.76, 0.06, 40]} />
                    <meshStandardMaterial color={isDark ? '#1E293B' : '#FFFFFF'} roughness={0.35} />
                </mesh>
                <mesh position={[0, 0.28, 0]}>
                    <cylinderGeometry args={[0.1, 0.16, 0.54, 24]} />
                    <meshStandardMaterial color="#334155" metalness={0.75} roughness={0.25} />
                </mesh>
                {/* Glowing Center Seminar Hub Ring */}
                <mesh rotation={[-Math.PI / 2, 0, 0]} position={[0, 0.595, 0]}>
                    <ringGeometry args={[0.16, 0.22, 32]} />
                    <meshStandardMaterial color="#2DD4BF" emissive="#2DD4BF" emissiveIntensity={1.6} toneMapped={false} />
                </mesh>
                {/* 6 Perimeter Stools */}
                {[0, 1, 2, 3, 4, 5].map((i) => {
                    const a = (i / 6) * Math.PI * 2;
                    return (
                        <mesh key={i} position={[Math.cos(a) * 0.96, 0.24, Math.sin(a) * 0.96]}>
                            <cylinderGeometry args={[0.16, 0.16, 0.44, 20]} />
                            <meshStandardMaterial color={isDark ? '#0F172A' : '#334155'} roughness={0.5} />
                        </mesh>
                    );
                })}
            </group>
        </group>
    );
}

/* ── Exported Interactive 3D Coding Lab Component with Floating HUD & AI Drawer ── */
export default function LabRoom3DStage({
    isDark,
    seats = DEFAULT_SEATS,
    compact = false,
}: {
    isDark: boolean;
    seats?: LabSeatTelemetry[];
    compact?: boolean;
}) {
    const [selectedSeat, setSelectedSeat] = useState<LabSeatTelemetry>(seats[1] ?? seats[0]);
    const [aiOpen, setAiOpen] = useState<boolean>(false);
    const [aiInput, setAiInput] = useState<string>('');
    const [aiMessages, setAiMessages] = useState<Array<{ from: 'ai' | 'user'; text: string }>>([
        {
            from: 'ai',
            text: `Hall 04 telemetry healthy. ${seats.length} isolated Nix containers active — Seat A-02 and Seat B-02 have AI patches awaiting student review.`,
        },
    ]);

    const handleAskAi = (e: React.FormEvent) => {
        e.preventDefault();
        const q = aiInput.trim();
        if (!q) return;
        setAiInput('');
        setAiMessages((prev) => [
            ...prev,
            { from: 'user', text: q },
            {
                from: 'ai',
                text: `${selectedSeat.studentName} (${selectedSeat.workspaceSlug}) is using ${selectedSeat.cpu} CPU & ${selectedSeat.memory} RAM with ${selectedSeat.humanPct}% verified human authorship. Direct agent writes remain blocked by policy.`,
            },
        ]);
    };

    return (
        <div
            className={cn(
                'relative w-full overflow-hidden rounded-[36px] ring-1 ring-vl-border/15 shadow-[0_32px_90px_-28px_rgba(0,0,0,0.65)]',
                isDark ? 'bg-[#070C16]' : 'bg-[#EEF3F9]',
                compact ? 'h-[500px]' : 'h-[580px] sm:h-[640px]'
            )}
        >
            {/* Interactive 3D Isometric Classroom Canvas */}
            <Canvas
                dpr={[1, 1.75]}
                camera={{ position: [0.4, 4.9, 7.6], fov: 34, near: 0.1, far: 45 }}
                gl={{ antialias: true, powerPreference: 'high-performance' }}
            >
                <color attach="background" args={[isDark ? '#070C16' : '#EEF3F9']} />
                <ambientLight intensity={isDark ? 0.55 : 0.9} />
                <directionalLight position={[6, 9, 6]} intensity={isDark ? 1.65 : 1.9} color="#FFFFFF" />
                <directionalLight position={[-5, 6, 4]} intensity={0.75} color="#38BDF8" />

                <Environment resolution={128} frames={1}>
                    <Lightformer form="rect" intensity={2.4} position={[0, 6, 2]} scale={[10, 4, 1]} color="#FFFFFF" />
                    <Lightformer form="ring" intensity={1.8} position={[5, 3, 3]} scale={4} color="#38BDF8" />
                </Environment>

                <IsometricLabScene
                    seats={seats}
                    selectedSeat={selectedSeat}
                    isDark={isDark}
                    onSelectSeat={(s) => setSelectedSeat(s)}
                />

                <ContactShadows
                    position={[0.25, -0.84, 0.7]}
                    opacity={isDark ? 0.55 : 0.28}
                    scale={12}
                    blur={2.4}
                    far={3}
                />

                <OrbitControls
                    enablePan={false}
                    enableZoom={false}
                    minPolarAngle={Math.PI / 5}
                    maxPolarAngle={Math.PI / 2.4}
                    minAzimuthAngle={-Math.PI / 6}
                    maxAzimuthAngle={Math.PI / 6}
                />
            </Canvas>

            {/* Top Floating Pill HUD Bar (Like Video 3!) */}
            <div className="pointer-events-none absolute inset-x-4 top-4 z-20 flex flex-wrap items-center justify-between gap-3 sm:inset-x-6 sm:top-5">
                <div className="pointer-events-auto flex flex-wrap items-center gap-2 rounded-full bg-white/90 px-3.5 py-1.5 text-xs shadow-lg backdrop-blur-xl ring-1 ring-slate-900/10 dark:bg-[#0B1324]/90 dark:ring-white/15">
                    <span className="inline-flex h-2 w-2 rounded-full bg-teal-400 animate-pulse" />
                    <span className="font-display font-semibold text-slate-900 dark:text-white">
                        Interactive 3D Lab Floor
                    </span>
                    <span className="hidden sm:inline font-mono text-[11px] text-slate-500 dark:text-slate-400">
                        · Click any workstation seat to inspect
                    </span>
                </div>

                <div className="pointer-events-auto flex items-center gap-2">
                    <div className="hidden md:flex items-center gap-1 rounded-full bg-white/90 p-1 backdrop-blur-xl ring-1 ring-slate-900/10 dark:bg-[#0B1324]/90 dark:ring-white/15">
                        {seats.slice(0, 3).map((s) => (
                            <button
                                key={s.id}
                                type="button"
                                onClick={() => setSelectedSeat(s)}
                                className={cn(
                                    'rounded-full px-3 py-1 text-[11px] font-medium transition-colors',
                                    selectedSeat.id === s.id
                                        ? 'bg-sky-500 text-white'
                                        : 'text-slate-600 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white'
                                )}
                            >
                                {s.seatLabel}
                            </button>
                        ))}
                    </div>

                    <button
                        type="button"
                        onClick={() => setAiOpen((v) => !v)}
                        className="inline-flex items-center gap-2 rounded-full bg-gradient-to-b from-sky-500 to-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-lg hover:from-sky-400 hover:to-blue-500 transition-all"
                    >
                        <Sparkles className="h-3.5 w-3.5" />
                        AI Lab Copilot
                    </button>
                </div>
            </div>

            {/* Bottom-Left Floating Frosted Glass Seat Inspector Card (Like Video 3!) */}
            <AnimatePresence mode="wait">
                <motion.div
                    key={selectedSeat.id}
                    initial={{ opacity: 0, y: 12, scale: 0.97 }}
                    animate={{ opacity: 1, y: 0, scale: 1 }}
                    exit={{ opacity: 0, y: 8, scale: 0.97 }}
                    transition={{ duration: 0.2, ease: [0.16, 1, 0.3, 1] }}
                    className="absolute bottom-4 left-4 z-20 w-[calc(100%-2rem)] max-w-[310px] rounded-[28px] bg-white/90 p-5 text-slate-900 shadow-2xl backdrop-blur-2xl ring-1 ring-slate-900/10 dark:bg-[#0B1324]/90 dark:text-slate-100 dark:ring-white/15 sm:bottom-6 sm:left-6"
                >
                    <div className="flex items-start justify-between gap-2">
                        <div>
                            <p className="font-mono text-[10px] text-sky-600 dark:text-sky-400">
                                {selectedSeat.seatLabel} · {selectedSeat.workspaceSlug}
                            </p>
                            <h4 className="mt-0.5 font-display text-base font-semibold">
                                {selectedSeat.studentName}
                            </h4>
                            <p className="text-xs opacity-70">{selectedSeat.course}</p>
                        </div>
                        <span
                            className={cn(
                                'rounded-full px-2.5 py-1 font-mono text-[10px] font-semibold',
                                selectedSeat.status === 'reviewing_patch' && 'bg-amber-500/15 text-amber-600 dark:text-amber-300',
                                selectedSeat.status === 'exam_locked' && 'bg-teal-500/15 text-teal-700 dark:text-teal-300',
                                selectedSeat.status === 'running' && 'bg-sky-500/15 text-sky-700 dark:text-sky-300'
                            )}
                        >
                            {selectedSeat.status === 'reviewing_patch'
                                ? 'AI Patch'
                                : selectedSeat.status === 'exam_locked'
                                ? 'Exam Lock'
                                : 'Running'}
                        </span>
                    </div>

                    <div className="mt-4 grid grid-cols-3 gap-2 rounded-[20px] bg-slate-900/[0.05] p-3 font-mono text-[11px] dark:bg-white/[0.05]">
                        <div>
                            <p className="text-[9px] opacity-60">CPU</p>
                            <p className="mt-0.5 font-semibold tabular-nums">{selectedSeat.cpu}</p>
                        </div>
                        <div>
                            <p className="text-[9px] opacity-60">RAM</p>
                            <p className="mt-0.5 font-semibold tabular-nums">{selectedSeat.memory}</p>
                        </div>
                        <div>
                            <p className="text-[9px] opacity-60">HUMAN</p>
                            <p className="mt-0.5 font-semibold text-teal-600 dark:text-teal-300 tabular-nums">
                                {selectedSeat.humanPct}%
                            </p>
                        </div>
                    </div>

                    <div className="mt-3.5 flex items-center justify-between text-[11px]">
                        <span className="inline-flex items-center gap-1.5 opacity-75">
                            <Terminal className="h-3.5 w-3.5 text-sky-500" />
                            {selectedSeat.language}
                        </span>
                        <span className="inline-flex items-center gap-1 font-medium text-sky-600 dark:text-sky-400">
                            crun isolated <ArrowUpRight className="h-3 w-3" />
                        </span>
                    </div>
                </motion.div>
            </AnimatePresence>

            {/* Slide-Over AI Lab Assistant Glass Panel (Like Video 3!) */}
            <AnimatePresence>
                {aiOpen && (
                    <motion.div
                        initial={{ opacity: 0, x: 24 }}
                        animate={{ opacity: 1, x: 0 }}
                        exit={{ opacity: 0, x: 24 }}
                        transition={{ duration: 0.22, ease: [0.16, 1, 0.3, 1] }}
                        className="absolute right-4 bottom-4 top-16 z-30 flex w-[calc(100%-2rem)] max-w-[330px] flex-col overflow-hidden rounded-[28px] bg-white/95 text-slate-900 shadow-2xl backdrop-blur-2xl ring-1 ring-slate-900/10 dark:bg-[#09101E]/95 dark:text-slate-100 dark:ring-white/15 sm:right-6 sm:bottom-6"
                    >
                        <div className="flex items-center justify-between border-b border-slate-900/10 px-5 py-3.5 dark:border-white/10">
                            <div className="flex items-center gap-2">
                                <span className="flex h-7 w-7 items-center justify-center rounded-full bg-sky-500/15 text-sky-500">
                                    <Bot className="h-4 w-4" />
                                </span>
                                <div>
                                    <p className="font-display text-xs font-semibold">VisionLab AI Telemetry</p>
                                    <p className="font-mono text-[10px] opacity-60">Read-Only Advisory</p>
                                </div>
                            </div>
                            <button
                                type="button"
                                onClick={() => setAiOpen(false)}
                                aria-label="Close AI Copilot"
                                className="rounded-full p-1.5 opacity-60 hover:bg-slate-900/10 hover:opacity-100 dark:hover:bg-white/10"
                            >
                                <X className="h-3.5 w-3.5" />
                            </button>
                        </div>

                        <div className="flex-1 space-y-3 overflow-y-auto p-4 text-xs leading-relaxed">
                            {aiMessages.map((msg, i) => (
                                <div
                                    key={i}
                                    className={cn(
                                        'rounded-[20px] px-4 py-3',
                                        msg.from === 'user'
                                            ? 'ml-6 bg-sky-500 text-white'
                                            : 'mr-4 bg-slate-900/[0.06] dark:bg-white/[0.07]'
                                    )}
                                >
                                    {msg.text}
                                </div>
                            ))}
                        </div>

                        <form
                            onSubmit={handleAskAi}
                            className="flex items-center gap-2 border-t border-slate-900/10 p-3 dark:border-white/10"
                        >
                            <input
                                type="text"
                                value={aiInput}
                                onChange={(e) => setAiInput(e.target.value)}
                                placeholder={`Ask about ${selectedSeat.seatLabel}...`}
                                className="flex-1 rounded-full bg-slate-900/[0.06] px-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-sky-400 dark:bg-white/[0.08]"
                            />
                            <button
                                type="submit"
                                aria-label="Send query"
                                className="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-sky-500 text-white hover:bg-sky-400 transition-colors"
                            >
                                <Send className="h-3.5 w-3.5" />
                            </button>
                        </form>
                    </motion.div>
                )}
            </AnimatePresence>
        </div>
    );
}
