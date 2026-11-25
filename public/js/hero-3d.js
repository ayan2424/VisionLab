/**
 * VisionLab — Interactive PBR 3D Hero Stage (Luminous Atelier)
 * Sculptural Autonomous Lab Companion Robot & Sovereign Kernel Core
 *
 * Loaded exclusively on welcome.blade.php via @push('scripts').
 * - Physically-based anodized titanium + optical sapphire glass (RoomEnvironment PMREM)
 * - Smooth damped cursor gaze tracking + mode switching (nix / ai / sha)
 * - Live dual-light studio lighting rig coupled to `vl:theme-changed`
 * - DPR capped (<=2 desktop, <=1.5 mobile), IntersectionObserver pause, prefers-reduced-motion safe
 */

import * as THREE from 'three';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

const MODE_PRESETS = {
    nix: {
        accent: 0x38bdf8,
        secondary: 0x0ea5e9,
        emissiveIntensity: 1.45,
        ringSpeed: 0.42,
        tiltZ: 0.08,
        eyeScaleY: 1.0,
    },
    ai: {
        accent: 0xfbbf24,
        secondary: 0xf59e0b,
        emissiveIntensity: 1.65,
        ringSpeed: 0.68,
        tiltZ: -0.1,
        eyeScaleY: 0.82,
    },
    sha: {
        accent: 0x2dd4bf,
        secondary: 0x0d9488,
        emissiveIntensity: 1.5,
        ringSpeed: 0.34,
        tiltZ: 0.04,
        eyeScaleY: 1.08,
    },
};

function createBeveledVLEmblem(material) {
    const group = new THREE.Group();

    // Sculptural stylized 'V' geometry
    const vShape = new THREE.Shape();
    vShape.moveTo(-0.34, 0.24);
    vShape.lineTo(-0.21, 0.24);
    vShape.lineTo(-0.08, -0.13);
    vShape.lineTo(0.05, 0.24);
    vShape.lineTo(0.17, 0.24);
    vShape.lineTo(-0.02, -0.25);
    vShape.lineTo(-0.14, -0.25);
    vShape.closePath();

    // Sculptural stylized 'L' geometry
    const lShape = new THREE.Shape();
    lShape.moveTo(0.18, 0.24);
    lShape.lineTo(0.29, 0.24);
    lShape.lineTo(0.29, -0.15);
    lShape.lineTo(0.45, -0.15);
    lShape.lineTo(0.45, -0.25);
    lShape.lineTo(0.18, -0.25);
    lShape.closePath();

    const extrudeSettings = {
        depth: 0.08,
        bevelEnabled: true,
        bevelSegments: 3,
        steps: 1,
        bevelSize: 0.015,
        bevelThickness: 0.018,
    };

    const vMesh = new THREE.Mesh(new THREE.ExtrudeGeometry(vShape, extrudeSettings), material);
    const lMesh = new THREE.Mesh(new THREE.ExtrudeGeometry(lShape, extrudeSettings), material);

    group.add(vMesh, lMesh);
    group.position.set(-0.05, 0, -0.04);
    return group;
}

function initVisionLabHero3D() {
    const container = document.getElementById('vl-hero-3d-stage');
    if (!container) return;

    const fallbackEl = document.getElementById('vl-hero-3d-fallback');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const isMobile = window.matchMedia('(max-width: 768px)').matches;

    let renderer;
    try {
        renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true,
            powerPreference: 'high-performance',
        });
    } catch {
        if (fallbackEl) fallbackEl.classList.remove('hidden');
        return;
    }

    const dprCap = isMobile ? 1.5 : 2;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, dprCap));
    renderer.setSize(container.clientWidth, container.clientHeight);
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.15;
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    container.appendChild(renderer.domElement);

    const scene = new THREE.Scene();

    // Photorealistic studio environment map via PMREM + RoomEnvironment
    const pmremGenerator = new THREE.PMREMGenerator(renderer);
    const envTexture = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;
    scene.environment = envTexture;

    const camera = new THREE.PerspectiveCamera(
        34,
        Math.max(container.clientWidth, 1) / Math.max(container.clientHeight, 1),
        0.1,
        50
    );
    camera.position.set(0, 0.25, 6.4);

    // ── Studio Lighting Rig (adapts live between Obsidian Dark & Pearl Mist Light) ──
    const hemiLight = new THREE.HemisphereLight(0xcbe8ff, 0x0b1526, 0.85);
    scene.add(hemiLight);

    const keyLight = new THREE.DirectionalLight(0xf8fafc, 2.2);
    keyLight.position.set(4.5, 5.5, 4.5);
    scene.add(keyLight);

    const rimLight = new THREE.DirectionalLight(0x38bdf8, 1.85);
    rimLight.position.set(-5.2, 2.2, -3.5);
    scene.add(rimLight);

    const tealBounce = new THREE.PointLight(0x2dd4bf, 1.5, 14);
    tealBounce.position.set(2.4, -2.6, 2.2);
    scene.add(tealBounce);

    const coreGlowLight = new THREE.PointLight(0x38bdf8, 2.2, 7.5);
    coreGlowLight.position.set(0, -0.65, 0.85);
    scene.add(coreGlowLight);

    // ── PBR Materials ──
    const shellMaterial = new THREE.MeshPhysicalMaterial({
        color: 0x1e293b,
        metalness: 0.72,
        roughness: 0.22,
        clearcoat: 0.85,
        clearcoatRoughness: 0.12,
        envMapIntensity: 1.35,
    });

    const trimMaterial = new THREE.MeshPhysicalMaterial({
        color: 0x94a3b8,
        metalness: 0.92,
        roughness: 0.18,
        clearcoat: 0.4,
        envMapIntensity: 1.45,
    });

    const darkAlloyMaterial = new THREE.MeshStandardMaterial({
        color: 0x0f172a,
        metalness: 0.82,
        roughness: 0.34,
    });

    const visorGlassMaterial = new THREE.MeshPhysicalMaterial({
        color: 0x060b14,
        metalness: 0.1,
        roughness: 0.05,
        transmission: 0.28,
        ior: 1.52,
        thickness: 0.35,
        clearcoat: 1.0,
        clearcoatRoughness: 0.03,
        envMapIntensity: 1.75,
    });

    const crystalVaultMaterial = new THREE.MeshPhysicalMaterial({
        color: 0xbae6fd,
        metalness: 0.05,
        roughness: 0.06,
        transmission: 0.72,
        ior: 1.48,
        thickness: 0.85,
        clearcoat: 1.0,
        clearcoatRoughness: 0.04,
        transparent: true,
        opacity: 0.92,
    });

    const accentMaterial = new THREE.MeshStandardMaterial({
        color: MODE_PRESETS.nix.accent,
        emissive: MODE_PRESETS.nix.accent,
        emissiveIntensity: MODE_PRESETS.nix.emissiveIntensity,
        metalness: 0.2,
        roughness: 0.15,
    });

    const ringFilamentMaterial = new THREE.MeshStandardMaterial({
        color: 0x64748b,
        metalness: 0.9,
        roughness: 0.2,
    });

    // ── Master Rig Hierarchy ──
    const rootRig = new THREE.Group();
    rootRig.position.y = 0.08;
    scene.add(rootRig);

    // ── 1. Articulated Autonomous Lab Companion Head ──
    const headRig = new THREE.Group();
    headRig.position.set(0, 0.82, 0);
    rootRig.add(headRig);

    // Sculpted rounded-stadium cranial chassis
    const craniumGeo = new THREE.SphereGeometry(0.92, 48, 36);
    craniumGeo.scale(1.14, 0.84, 0.94);
    const cranium = new THREE.Mesh(craniumGeo, shellMaterial);
    headRig.add(cranium);

    // Machined metallic crown brow ridge
    const crownBandGeo = new THREE.TorusGeometry(0.86, 0.032, 16, 64, Math.PI * 1.12);
    const crownBand = new THREE.Mesh(crownBandGeo, trimMaterial);
    crownBand.rotation.set(Math.PI * 0.42, 0, -Math.PI * 0.06);
    crownBand.position.set(0, 0.34, 0.12);
    headRig.add(crownBand);

    // Curved Optical Glass Visor
    const visorGeo = new THREE.SphereGeometry(0.87, 44, 32, -Math.PI * 0.42, Math.PI * 0.84, Math.PI * 0.28, Math.PI * 0.42);
    visorGeo.scale(1.13, 0.84, 0.99);
    const visor = new THREE.Mesh(visorGeo, visorGlassMaterial);
    visor.position.z = 0.06;
    headRig.add(visor);

    // Visor bezel rim
    const bezelGeo = new THREE.TorusGeometry(0.68, 0.024, 14, 56);
    bezelGeo.scale(1.24, 0.62, 0.65);
    const bezel = new THREE.Mesh(bezelGeo, trimMaterial);
    bezel.position.set(0, 0.0, 0.72);
    headRig.add(bezel);

    // Expressive Twin Luminous Optical Eyes inside visor
    const eyeGroup = new THREE.Group();
    eyeGroup.position.set(0, 0.03, 0.85);
    headRig.add(eyeGroup);

    const eyeGeo = new THREE.CapsuleGeometry(0.085, 0.11, 10, 20);
    const leftEye = new THREE.Mesh(eyeGeo, accentMaterial);
    leftEye.position.set(-0.29, 0.02, 0);
    leftEye.rotation.z = Math.PI / 2;

    const rightEye = new THREE.Mesh(eyeGeo, accentMaterial);
    rightEye.position.set(0.29, 0.02, 0);
    rightEye.rotation.z = Math.PI / 2;

    // Subtle acoustic smile / voice-telemetry bar under eyes
    const hudBarGeo = new THREE.CapsuleGeometry(0.018, 0.22, 6, 12);
    const hudBar = new THREE.Mesh(hudBarGeo, accentMaterial);
    hudBar.position.set(0, -0.17, -0.02);
    hudBar.rotation.z = Math.PI / 2;

    eyeGroup.add(leftEye, rightEye, hudBar);

    // Dual Side Acoustic / Sensor Ear Pods
    [-1, 1].forEach((side) => {
        const podGroup = new THREE.Group();
        podGroup.position.set(side * 1.03, 0.02, -0.02);
        podGroup.rotation.z = side * (Math.PI / 2);

        const outerCylinder = new THREE.Mesh(new THREE.CylinderGeometry(0.26, 0.29, 0.16, 32), trimMaterial);
        const innerCap = new THREE.Mesh(new THREE.CylinderGeometry(0.18, 0.18, 0.19, 28), darkAlloyMaterial);
        const ledRing = new THREE.Mesh(new THREE.TorusGeometry(0.21, 0.016, 12, 36), accentMaterial);
        ledRing.rotation.x = Math.PI / 2;
        ledRing.position.y = side > 0 ? -0.085 : 0.085;

        podGroup.add(outerCylinder, innerCap, ledRing);
        headRig.add(podGroup);
    });

    // Subtle Telemetry Fin Antenna on top-right
    const antennaBase = new THREE.Mesh(new THREE.CylinderGeometry(0.045, 0.06, 0.1, 16), trimMaterial);
    antennaBase.position.set(0.46, 0.72, -0.18);
    antennaBase.rotation.z = -0.35;
    const antennaStem = new THREE.Mesh(new THREE.CylinderGeometry(0.015, 0.022, 0.28, 12), trimMaterial);
    antennaStem.position.set(0.52, 0.87, -0.18);
    antennaStem.rotation.z = -0.35;
    const antennaTip = new THREE.Mesh(new THREE.SphereGeometry(0.042, 16, 16), accentMaterial);
    antennaTip.position.set(0.57, 1.0, -0.18);
    headRig.add(antennaBase, antennaStem, antennaTip);

    // ── 2. Articulated Collar & Sovereign Kernel Torso Chamber ──
    const torsoGroup = new THREE.Group();
    torsoGroup.position.set(0, -0.66, 0);
    rootRig.add(torsoGroup);

    // Floating magnetic neck rings
    const neckRingTop = new THREE.Mesh(new THREE.TorusGeometry(0.32, 0.036, 16, 40), darkAlloyMaterial);
    neckRingTop.rotation.x = Math.PI / 2;
    neckRingTop.position.y = 0.68;
    const neckGlow = new THREE.Mesh(new THREE.TorusGeometry(0.26, 0.015, 12, 36), accentMaterial);
    neckGlow.rotation.x = Math.PI / 2;
    neckGlow.position.y = 0.59;
    torsoGroup.add(neckRingTop, neckGlow);

    // Sculpted Shoulder Mantle Arc
    const mantleGeo = new THREE.TorusGeometry(0.86, 0.14, 24, 52, Math.PI);
    mantleGeo.scale(1.18, 0.65, 0.82);
    const mantle = new THREE.Mesh(mantleGeo, shellMaterial);
    mantle.rotation.x = Math.PI * 0.08;
    mantle.position.set(0, 0.18, -0.05);
    torsoGroup.add(mantle);

    // Shoulder Trim Pauldrons
    [-1, 1].forEach((side) => {
        const pauldron = new THREE.Mesh(new THREE.CapsuleGeometry(0.14, 0.24, 12, 20), trimMaterial);
        pauldron.position.set(side * 1.02, 0.12, -0.04);
        pauldron.rotation.z = side * 0.42;
        torsoGroup.add(pauldron);
    });

    // Transmissive Crystal Kernel Vault at chest center
    const vaultOuter = new THREE.Mesh(new THREE.IcosahedronGeometry(0.56, 1), crystalVaultMaterial);
    vaultOuter.position.set(0, -0.08, 0.06);
    torsoGroup.add(vaultOuter);

    // Wireframe precision geodesic cage around the crystal core
    const vaultWire = new THREE.Mesh(
        new THREE.IcosahedronGeometry(0.61, 1),
        new THREE.MeshBasicMaterial({
            color: MODE_PRESETS.nix.accent,
            wireframe: true,
            transparent: true,
            opacity: 0.24,
        })
    );
    vaultWire.position.copy(vaultOuter.position);
    torsoGroup.add(vaultWire);

    // Extruded Beveled VL Emblem inside the crystal chamber
    const emblemGroup = createBeveledVLEmblem(accentMaterial);
    emblemGroup.position.set(-0.04, -0.08, 0.06);
    emblemGroup.scale.setScalar(0.82);
    torsoGroup.add(emblemGroup);

    // Lower tapering pedestal stabilizer
    const lowerCup = new THREE.Mesh(new THREE.CylinderGeometry(0.46, 0.16, 0.36, 36), shellMaterial);
    lowerCup.position.set(0, -0.78, 0);
    const lowerAccentRing = new THREE.Mesh(new THREE.TorusGeometry(0.32, 0.016, 12, 40), accentMaterial);
    lowerAccentRing.rotation.x = Math.PI / 2;
    lowerAccentRing.position.set(0, -0.96, 0);
    torsoGroup.add(lowerCup, lowerAccentRing);

    // ── 3. Three Tilted Orbital Governance Rings & Telemetry Satellites ──
    const orbitConfigs = [
        { radius: 2.02, tube: 0.012, rotX: 1.24, rotY: 0.22, speed: 0.36, nodeCount: 2 },
        { radius: 2.34, tube: 0.009, rotX: 1.72, rotY: -0.45, speed: -0.26, nodeCount: 3 },
        { radius: 1.66, tube: 0.01, rotX: 0.92, rotY: 0.68, speed: 0.44, nodeCount: 1 },
    ];

    const orbitalGroups = orbitConfigs.map((cfg) => {
        const grp = new THREE.Group();
        grp.rotation.set(cfg.rotX, cfg.rotY, 0);

        const ring = new THREE.Mesh(new THREE.TorusGeometry(cfg.radius, cfg.tube, 14, 96), ringFilamentMaterial);
        grp.add(ring);

        for (let i = 0; i < cfg.nodeCount; i++) {
            const angle = (i / cfg.nodeCount) * Math.PI * 2;
            const node = new THREE.Mesh(new THREE.OctahedronGeometry(0.068, 0), accentMaterial);
            node.position.set(Math.cos(angle) * cfg.radius, Math.sin(angle) * cfg.radius, 0);
            grp.add(node);
        }

        rootRig.add(grp);
        return { grp, baseSpeed: cfg.speed };
    });

    // Subtle floating data-shard particles
    const shardCount = isMobile ? 22 : 42;
    const shardPositions = new Float32Array(shardCount * 3);
    for (let i = 0; i < shardCount; i++) {
        const r = 2.1 + Math.random() * 1.4;
        const theta = Math.random() * Math.PI * 2;
        const phi = (Math.random() - 0.5) * Math.PI * 0.85;
        shardPositions[i * 3] = r * Math.cos(theta) * Math.cos(phi);
        shardPositions[i * 3 + 1] = r * Math.sin(phi) * 0.95;
        shardPositions[i * 3 + 2] = r * Math.sin(theta) * Math.cos(phi) - 0.3;
    }
    const shardGeo = new THREE.BufferGeometry();
    shardGeo.setAttribute('position', new THREE.BufferAttribute(shardPositions, 3));
    const shardMaterial = new THREE.PointsMaterial({
        color: MODE_PRESETS.nix.accent,
        size: 0.045,
        transparent: true,
        opacity: 0.68,
    });
    const shards = new THREE.Points(shardGeo, shardMaterial);
    rootRig.add(shards);

    // ── Theme-Reactive Studio Lighting ──
    function applyThemeLighting(isDark) {
        if (isDark) {
            shellMaterial.color.setHex(0x1b2638);
            trimMaterial.color.setHex(0x94a3b8);
            visorGlassMaterial.color.setHex(0x050a14);
            ringFilamentMaterial.color.setHex(0x475569);
            hemiLight.intensity = 0.88;
            keyLight.intensity = 2.25;
            rimLight.intensity = 1.9;
            renderer.toneMappingExposure = 1.18;
        } else {
            // Pearl Mist Light: Alabaster ceramic titanium + deep obsidian visor contrast
            shellMaterial.color.setHex(0xe2e8f0);
            trimMaterial.color.setHex(0x64748b);
            visorGlassMaterial.color.setHex(0x0b1220);
            ringFilamentMaterial.color.setHex(0x94a3b8);
            hemiLight.intensity = 1.28;
            keyLight.intensity = 2.55;
            rimLight.intensity = 1.35;
            renderer.toneMappingExposure = 1.06;
        }
    }

    applyThemeLighting(document.documentElement.classList.contains('dark'));

    window.addEventListener('vl:theme-changed', (ev) => {
        const isDark = ev?.detail?.dark ?? document.documentElement.classList.contains('dark');
        applyThemeLighting(isDark);
        if (prefersReducedMotion) renderer.render(scene, camera);
    });

    // ── Live Subsystem State Coupling (`nix` / `ai` / `sha`) ──
    let currentPreset = MODE_PRESETS.nix;
    const targetAccent = new THREE.Color(currentPreset.accent);
    const currentAccent = new THREE.Color(currentPreset.accent);
    let targetTiltZ = currentPreset.tiltZ;
    let targetEyeScaleY = currentPreset.eyeScaleY;

    window.addEventListener('vl:hero-mode', (ev) => {
        const mode = ev?.detail?.mode;
        if (!mode || !MODE_PRESETS[mode]) return;
        currentPreset = MODE_PRESETS[mode];
        targetAccent.setHex(currentPreset.accent);
        targetTiltZ = currentPreset.tiltZ;
        targetEyeScaleY = currentPreset.eyeScaleY;

        if (prefersReducedMotion) {
            accentMaterial.color.copy(targetAccent);
            accentMaterial.emissive.copy(targetAccent);
            vaultWire.material.color.copy(targetAccent);
            shardMaterial.color.copy(targetAccent);
            coreGlowLight.color.copy(targetAccent);
            renderer.render(scene, camera);
        }
    });

    // ── Pointer Gaze Tracking ──
    const pointer = { x: 0, y: 0, targetX: 0, targetY: 0 };
    const onPointerMove = (e) => {
        const rect = container.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) return;
        const nx = ((e.clientX - rect.left) / rect.width) * 2 - 1;
        const ny = -(((e.clientY - rect.top) / rect.height) * 2 - 1);
        pointer.targetX = THREE.MathUtils.clamp(nx, -1, 1);
        pointer.targetY = THREE.MathUtils.clamp(ny, -1, 1);
    };
    const onPointerLeave = () => {
        pointer.targetX = 0;
        pointer.targetY = 0;
    };

    window.addEventListener('pointermove', onPointerMove, { passive: true });
    container.addEventListener('pointerleave', onPointerLeave, { passive: true });

    // ── Responsive Resize Handling ──
    const handleResize = () => {
        const width = Math.max(container.clientWidth, 1);
        const height = Math.max(container.clientHeight, 1);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height);
        if (prefersReducedMotion) renderer.render(scene, camera);
    };
    const resizeObserver = new ResizeObserver(handleResize);
    resizeObserver.observe(container);

    // ── Animation Loop with IntersectionObserver Auto-Pause ──
    let isVisible = true;
    let rafId = null;
    const clock = new THREE.Clock();

    const renderFrame = () => {
        if (!isVisible) return;
        rafId = requestAnimationFrame(renderFrame);

        const elapsed = clock.getElapsedTime();

        // Smooth pointer damping
        pointer.x += (pointer.targetX - pointer.x) * 0.06;
        pointer.y += (pointer.targetY - pointer.y) * 0.06;

        // Smooth accent color interpolation across subsystem modes
        currentAccent.lerp(targetAccent, 0.07);
        accentMaterial.color.copy(currentAccent);
        accentMaterial.emissive.copy(currentAccent);
        accentMaterial.emissiveIntensity += (currentPreset.emissiveIntensity - accentMaterial.emissiveIntensity) * 0.06;
        vaultWire.material.color.copy(currentAccent);
        shardMaterial.color.copy(currentAccent);
        coreGlowLight.color.copy(currentAccent);
        rimLight.color.copy(currentAccent);

        // Gentle hover levitation
        rootRig.position.y = 0.08 + Math.sin(elapsed * 1.45) * 0.09;
        rootRig.rotation.y = pointer.x * 0.24 + Math.sin(elapsed * 0.45) * 0.08;
        rootRig.rotation.z += (targetTiltZ - rootRig.rotation.z) * 0.05;

        // Expressive head gaze tracking + periodic micro-blink
        headRig.rotation.y = pointer.x * 0.34;
        headRig.rotation.x = -pointer.y * 0.22 + Math.sin(elapsed * 0.9) * 0.03;

        const blinkCycle = elapsed % 4.6;
        const blinkFactor = blinkCycle > 4.44 ? 0.12 : 1.0;
        const desiredEyeScale = targetEyeScaleY * blinkFactor;
        leftEye.scale.x += (desiredEyeScale - leftEye.scale.x) * 0.22;
        rightEye.scale.x += (desiredEyeScale - rightEye.scale.x) * 0.22;

        // Sovereign Kernel chamber rotation
        vaultOuter.rotation.y = elapsed * 0.42;
        vaultOuter.rotation.x = Math.sin(elapsed * 0.3) * 0.18;
        vaultWire.rotation.y = -elapsed * 0.28;
        emblemGroup.rotation.y = Math.sin(elapsed * 0.85) * 0.32;

        // Orbital governance rings
        orbitalGroups.forEach(({ grp, baseSpeed }) => {
            grp.rotation.z += baseSpeed * currentPreset.ringSpeed * 0.025;
        });

        shards.rotation.y = elapsed * 0.08;

        renderer.render(scene, camera);
    };

    if (prefersReducedMotion) {
        // Static high-craft studio hero pose for reduced-motion users
        rootRig.rotation.y = -0.18;
        headRig.rotation.y = 0.14;
        renderer.render(scene, camera);
    } else {
        const intersectionObserver = new IntersectionObserver(
            (entries) => {
                const entry = entries[0];
                isVisible = Boolean(entry?.isIntersecting);
                if (isVisible && rafId === null) {
                    clock.getDelta();
                    rafId = requestAnimationFrame(renderFrame);
                } else if (!isVisible && rafId !== null) {
                    cancelAnimationFrame(rafId);
                    rafId = null;
                }
            },
            { threshold: 0.05 }
        );
        intersectionObserver.observe(container);
        rafId = requestAnimationFrame(renderFrame);
    }

    // Cleanup on pagehide
    window.addEventListener('pagehide', () => {
        if (rafId !== null) cancelAnimationFrame(rafId);
        resizeObserver.disconnect();
        pmremGenerator.dispose();
        envTexture.dispose();
        renderer.dispose();
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initVisionLabHero3D);
} else {
    initVisionLabHero3D();
}
