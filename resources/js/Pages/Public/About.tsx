import React from 'react';
import { motion } from 'motion/react';
import { ArrowRight, BookOpen, Building2, Cpu, ShieldCheck } from 'lucide-react';
import { PublicLayout } from '@/layouts/PublicLayout';
import { MagneticButton, SpotlightSurface } from '@/components/ui/Primitives';

export default function About() {
    return (
        <PublicLayout title="About VisionLab">
            <section className="relative pt-36 pb-24">
                <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10 space-y-20">
                    <div className="grid grid-cols-1 items-center gap-12 lg:grid-cols-12">
                        <motion.div
                            initial={{ opacity: 0, y: 18 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, ease: [0.16, 1, 0.3, 1] }}
                            className="space-y-6 lg:col-span-6"
                        >
                            <h1 className="font-display text-[clamp(2.5rem,5vw,4.5rem)] font-semibold leading-[1.03] tracking-[-0.035em] text-vl-ink text-balance">
                                Built to return engineering sovereignty to academic labs.
                            </h1>
                            <p className="max-w-[58ch] text-base leading-relaxed text-vl-ink-secondary sm:text-lg">
                                Computer science departments shouldn't have to choose between unmanaged local laptops and opaque third-party SaaS sandboxes. VisionLab runs under institutional governance — unifying isolated VS Code containers, Nix reproducibility, auditable AI assistance, and campus ERP operations.
                            </p>
                            <div className="flex flex-wrap gap-3 pt-2">
                                <MagneticButton href="/contact" variant="primary" size="lg">
                                    Request Campus Pilot
                                    <ArrowRight className="h-4 w-4" />
                                </MagneticButton>
                                <MagneticButton href="/features" variant="secondary" size="lg">
                                    Inspect Architecture
                                </MagneticButton>
                            </div>
                        </motion.div>

                        <motion.div
                            initial={{ opacity: 0, scale: 0.97 }}
                            animate={{ opacity: 1, scale: 1 }}
                            transition={{ duration: 0.55, ease: [0.16, 1, 0.3, 1] }}
                            className="lg:col-span-6"
                        >
                            <div className="relative overflow-hidden rounded-[32px] aspect-[16/11] shadow-[0_32px_72px_-28px_rgba(0,0,0,0.65)]">
                                <img
                                    src="/images/redesign/university-atrium-study.jpg"
                                    alt="University atrium and collaborative engineering space"
                                    className="h-full w-full object-cover"
                                />
                                <div
                                    aria-hidden="true"
                                    className="absolute inset-0 bg-gradient-to-t from-[#050911]/80 via-transparent to-transparent"
                                />
                                <div className="absolute inset-x-6 bottom-5 flex items-center justify-between text-xs text-slate-200">
                                    <span>Academic Cloud Infrastructure</span>
                                    <span className="font-mono text-sky-300">Sovereign Deployment</span>
                                </div>
                            </div>
                        </motion.div>
                    </div>

                    <div className="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                        {[
                            {
                                icon: Cpu,
                                title: 'Deterministic Runtime',
                                desc: 'Declarative Nix configurations ensure lab assignments build cleanly on day one.',
                            },
                            {
                                icon: ShieldCheck,
                                title: 'Auditable AI Policy',
                                desc: 'Every proposed code diff passes through explicit student inspection before writing.',
                            },
                            {
                                icon: BookOpen,
                                title: 'Faculty Grading Speed',
                                desc: 'Rubrics, live workspace inspection, and VisionGuard forensics live side-by-side.',
                            },
                            {
                                icon: Building2,
                                title: 'Campus ERP Integration',
                                desc: 'Campuses, departments, batches, challans, and library records stay synchronized.',
                            },
                        ].map((item) => {
                            const Icon = item.icon;
                            return (
                                <SpotlightSurface key={item.title} className="p-6">
                                    <Icon className="h-5 w-5 text-sky-400" />
                                    <h2 className="mt-4 font-display text-lg font-semibold text-vl-ink">{item.title}</h2>
                                    <p className="mt-2 text-sm leading-relaxed text-vl-ink-secondary">{item.desc}</p>
                                </SpotlightSurface>
                            );
                        })}
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
