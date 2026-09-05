import { Link } from '@inertiajs/react';
import SiteLogo from '../../images/site-logo.png';

export default function MainLayout({ children }) {
  return (
    <div>
      <div className="bg-black text-white py-3">
        <header className="flex items-center justify-between container mx-auto">
          <div className="">
            <Link href="/">
              <img src={SiteLogo} alt="Viberx" className="h-12 w-auto" />
            </Link>
          </div>
          <div className="">
            <ul className="flex items-center gap-4 font-semibold capitalize">
              <li>
                <Link href="">Products</Link>
              </li>
              <li>
                <Link href={route('pricing.index')}>Pricing</Link>
              </li>
              <li>
                <Link href="">How It Works</Link>
              </li>
            </ul>
          </div>
          <div className="flex items-center gap-4">
            <Link
              href="/login"
              className="font-semibold border border-white py-2 px-5 rounded-sm"
            >
              SIGN IN
            </Link>
            <Link
              href="/login"
              className="bg-white text-black hover:bg-gray-200 font-semibold py-2 px-4 rounded"
            >
              GET STARTED
            </Link>
          </div>
        </header>
      </div>

      <main>{children}</main>

      <div className="bg-black text-white py-3">
        <footer className="mt-12 container mx-auto py-8 text-center text-sm text-neutral-400">
          <div className="flex items-center justify-between gap-3">
            <div className="">
              <h3 className="">PRODUCTS</h3>
              <ul>
                <li>
                  <Link href="">Product 1</Link>
                </li>
                <li>
                  <Link href="">Product 2</Link>
                </li>
                <li>
                  <Link href="">Product 3</Link>
                </li>
              </ul>
            </div>
            <div className="">
              <h3 className="">PRODUCTS</h3>
              <ul>
                <li>
                  <Link href="">Product 1</Link>
                </li>
                <li>
                  <Link href="">Product 2</Link>
                </li>
                <li>
                  <Link href="">Product 3</Link>
                </li>
              </ul>
            </div>
            <div className="">
              <h3 className="">PRODUCTS</h3>
              <ul>
                <li>
                  <Link href="">Product 1</Link>
                </li>
                <li>
                  <Link href="">Product 2</Link>
                </li>
                <li>
                  <Link href="">Product 3</Link>
                </li>
              </ul>
            </div>
            <div className="">
              <h3 className="">PRODUCTS</h3>
              <ul>
                <li>
                  <Link href="">Product 1</Link>
                </li>
                <li>
                  <Link href="">Product 2</Link>
                </li>
                <li>
                  <Link href="">Product 3</Link>
                </li>
              </ul>
            </div>
            <div className="">
              <h3 className="">PRODUCTS</h3>
              <ul>
                <li>
                  <Link href="">Product 1</Link>
                </li>
                <li>
                  <Link href="">Product 2</Link>
                </li>
                <li>
                  <Link href="">Product 3</Link>
                </li>
              </ul>
            </div>
          </div>
        </footer>
      </div>
    </div>
  );
}
