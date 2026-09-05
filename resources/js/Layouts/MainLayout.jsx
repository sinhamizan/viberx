import ApplicationLogo from '@/Components/ApplicationLogo';
import { Link } from '@inertiajs/react';

export default function MarketingLayout({ children }) {
  return (
    <div className="flex min-h-screen flex-col bg-neutral-950 text-white">
      <header className="border-b border-neutral-800">
        <div className="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
          <Link href="/" className="flex items-center gap-2">
            <ApplicationLogo className="h-6 w-6 fill-current text-emerald-400" />
            <span className="text-lg font-bold">VibeRX</span>
          </Link>

          <nav className="flex items-center gap-6 text-sm">
            <Link
              href={route('pricing.index')}
              className="text-neutral-300 hover:text-white"
            >
              Pricing
            </Link>
            <Link
              href={route('login')}
              className="text-neutral-300 hover:text-white"
            >
              Sign in
            </Link>
            <Link
              href={route('pricing.index')}
              className="rounded-md bg-emerald-300 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-neutral-900 hover:bg-emerald-200"
            >
              Get Started
            </Link>
          </nav>
        </div>
      </header>

      <main className="flex-1">{children}</main>

      <footer className="border-t border-neutral-800">
        <div className="mx-auto flex max-w-5xl flex-col items-center justify-between gap-3 px-4 py-6 text-xs text-neutral-500 sm:flex-row">
          <p>&copy; {new Date().getFullYear()} VibeRX. All rights reserved.</p>

          <div className="flex items-center gap-4">
            <a href="#" className="hover:text-neutral-300">
              Terms of Service
            </a>
            <a href="#" className="hover:text-neutral-300">
              Privacy Policy
            </a>
          </div>
        </div>
      </footer>
    </div>
  );
}
