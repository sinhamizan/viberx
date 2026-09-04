import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';

export default function GuestLayout({ children }) {
    return (
        <div className="flex min-h-screen items-center justify-center bg-neutral-950 px-4 py-10">
            <div className="w-full max-w-md overflow-hidden rounded-lg border border-neutral-800 bg-neutral-900 shadow-xl">
                <div className="flex">
                    <div className="w-1.5 shrink-0 bg-emerald-400/80" />

                    <div className="w-full px-8 py-10">
                        <div className="mb-8 flex justify-center">
                            <Link href="/">
                                <ApplicationLogo className="h-12 w-12 fill-current text-emerald-400" />
                            </Link>
                        </div>

                        {children}
                    </div>
                </div>
            </div>
        </div>
    );
}
