import React from 'react';
import { useForm, usePage } from '@inertiajs/react';
import { motion } from 'motion/react';
import { Building2, CheckCircle2, Mail, Send, ShieldCheck } from 'lucide-react';
import { PublicLayout } from '@/layouts/PublicLayout';
import { MagneticButton, SpotlightSurface } from '@/components/ui/Primitives';
import type { SharedProps } from '@/types';

export default function Contact() {
    const { props } = usePage<SharedProps>();
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        institution: '',
        role: 'faculty',
        cohort_size: '100_500',
        message: '',
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post('/contact', {
            preserveScroll: true,
            onSuccess: () => reset('message'),
        });
    };

    return (
        <PublicLayout title="Request Campus Pilot">
            <section className="relative pt-36 pb-24">
                <div className="mx-auto max-w-[1380px] px-4 sm:px-6 lg:px-10">
                    <div className="grid grid-cols-1 gap-12 lg:grid-cols-12">
                        <motion.div
                            initial={{ opacity: 0, y: 18 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, ease: [0.16, 1, 0.3, 1] }}
                            className="space-y-8 lg:col-span-5"
                        >
                            <div className="space-y-4">
                                <h1 className="font-display text-[clamp(2.4rem,4.6vw,4.1rem)] font-semibold leading-[1.03] tracking-[-0.035em] text-vl-ink text-balance">
                                    Launch a VisionLab pilot on your campus.
                                </h1>
                                <p className="text-base leading-relaxed text-vl-ink-secondary">
                                    Share your department profile, cohort volume, and syllabus stack. Our academic infrastructure team configures dedicated Nix templates and container quotas for evaluation.
                                </p>
                            </div>

                            <div className="space-y-4">
                                {[
                                    {
                                        icon: Building2,
                                        title: 'Self-Hosted or Dedicated Cluster',
                                        desc: 'Deploy on-premise behind your university firewall or inside an isolated regional VPC.',
                                    },
                                    {
                                        icon: ShieldCheck,
                                        title: 'Institutional Provisioning',
                                        desc: 'No public self-signup. Admin-managed student IDs, CSV roster import, and role-scoped RBAC.',
                                    },
                                    {
                                        icon: Mail,
                                        title: 'Rate-Limited Verified Intake',
                                        desc: 'Requests route directly to our campus onboarding queue with audit confirmation.',
                                    },
                                ].map((item) => {
                                    const Icon = item.icon;
                                    return (
                                        <div key={item.title} className="flex items-start gap-3.5 rounded-2xl bg-vl-surface/75 p-4">
                                            <Icon className="mt-0.5 h-5 w-5 shrink-0 text-sky-400" />
                                            <div>
                                                <p className="text-sm font-semibold text-vl-ink">{item.title}</p>
                                                <p className="mt-0.5 text-xs leading-relaxed text-vl-ink-secondary">{item.desc}</p>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </motion.div>

                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.5, delay: 0.08, ease: [0.16, 1, 0.3, 1] }}
                            className="lg:col-span-7"
                        >
                            <SpotlightSurface accent="sky" className="p-7 sm:p-10">
                                {props.flash?.status && (
                                    <div className="mb-6 flex items-start gap-3 rounded-xl bg-teal-500/12 p-4 text-sm text-teal-700 dark:text-teal-200">
                                        <CheckCircle2 className="mt-0.5 h-4 w-4 shrink-0 text-teal-400" />
                                        <p>{props.flash.status}</p>
                                    </div>
                                )}

                                <form onSubmit={handleSubmit} className="space-y-5">
                                    <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                        <div>
                                            <label htmlFor="name" className="mb-2 block text-xs font-medium text-vl-ink-secondary">
                                                Full Name
                                            </label>
                                            <input
                                                id="name"
                                                type="text"
                                                value={data.name}
                                                onChange={(e) => setData('name', e.target.value)}
                                                placeholder="Dr. Nadia Rahman"
                                                required
                                                className="vl-input w-full"
                                            />
                                            {errors.name && <p className="mt-1.5 text-xs text-rose-400">{errors.name}</p>}
                                        </div>

                                        <div>
                                            <label htmlFor="email" className="mb-2 block text-xs font-medium text-vl-ink-secondary">
                                                Institutional Email
                                            </label>
                                            <input
                                                id="email"
                                                type="email"
                                                value={data.email}
                                                onChange={(e) => setData('email', e.target.value)}
                                                placeholder="n.rahman@university.edu"
                                                required
                                                className="vl-input w-full"
                                            />
                                            {errors.email && <p className="mt-1.5 text-xs text-rose-400">{errors.email}</p>}
                                        </div>
                                    </div>

                                    <div>
                                        <label htmlFor="institution" className="mb-2 block text-xs font-medium text-vl-ink-secondary">
                                            University or Institute Name
                                        </label>
                                        <input
                                            id="institution"
                                            type="text"
                                            value={data.institution}
                                            onChange={(e) => setData('institution', e.target.value)}
                                            placeholder="National Institute of Technology"
                                            required
                                            className="vl-input w-full"
                                        />
                                        {errors.institution && <p className="mt-1.5 text-xs text-rose-400">{errors.institution}</p>}
                                    </div>

                                    <div className="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                        <div>
                                            <label htmlFor="role" className="mb-2 block text-xs font-medium text-vl-ink-secondary">
                                                Your Role
                                            </label>
                                            <select
                                                id="role"
                                                value={data.role}
                                                onChange={(e) => setData('role', e.target.value)}
                                                className="vl-input w-full"
                                            >
                                                <option value="faculty">Faculty / Instructor</option>
                                                <option value="department_head">Department Chair / Dean</option>
                                                <option value="it_admin">Campus IT / Lab Administrator</option>
                                                <option value="other">Academic Coordinator</option>
                                            </select>
                                            {errors.role && <p className="mt-1.5 text-xs text-rose-400">{errors.role}</p>}
                                        </div>

                                        <div>
                                            <label htmlFor="cohort_size" className="mb-2 block text-xs font-medium text-vl-ink-secondary">
                                                Estimated Active Seats
                                            </label>
                                            <select
                                                id="cohort_size"
                                                value={data.cohort_size}
                                                onChange={(e) => setData('cohort_size', e.target.value)}
                                                className="vl-input w-full"
                                            >
                                                <option value="under_100">Under 100 students</option>
                                                <option value="100_500">100 – 500 students</option>
                                                <option value="500_2000">500 – 2,000 students</option>
                                                <option value="over_2000">2,000+ campus-wide</option>
                                            </select>
                                            {errors.cohort_size && <p className="mt-1.5 text-xs text-rose-400">{errors.cohort_size}</p>}
                                        </div>
                                    </div>

                                    <div>
                                        <label htmlFor="message" className="mb-2 block text-xs font-medium text-vl-ink-secondary">
                                            Course Stack &amp; Pilot Goals
                                        </label>
                                        <textarea
                                            id="message"
                                            rows={5}
                                            value={data.message}
                                            onChange={(e) => setData('message', e.target.value)}
                                            placeholder="Tell us about the courses, toolchains (Python, C/C++, Web, Rust), and exam integrity goals for your upcoming term..."
                                            required
                                            className="vl-input w-full resize-y"
                                        />
                                        {errors.message && <p className="mt-1.5 text-xs text-rose-400">{errors.message}</p>}
                                    </div>

                                    <div className="pt-2">
                                        <MagneticButton
                                            type="submit"
                                            size="lg"
                                            variant="primary"
                                            disabled={processing}
                                            className="w-full sm:w-auto"
                                        >
                                            <Send className="h-4 w-4" />
                                            {processing ? 'Submitting Request...' : 'Submit Pilot Request'}
                                        </MagneticButton>
                                    </div>
                                </form>
                            </SpotlightSurface>
                        </motion.div>
                    </div>
                </div>
            </section>
        </PublicLayout>
    );
}
